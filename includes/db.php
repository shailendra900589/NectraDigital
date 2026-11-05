<?php
if (array_key_exists('nectra_db_conn', $GLOBALS)) {
    $conn = $GLOBALS['nectra_db_conn'];
    return;
}

require_once __DIR__ . '/text-utils.php';

if (!function_exists('nectra_db_fail_hard')) {
    function nectra_db_fail_hard(): bool
    {
        if (PHP_SAPI === 'cli') {
            $argv0 = $_SERVER['argv'][0] ?? '';
            if (preg_match('#/(admin|cron|database)/#', str_replace('\\', '/', $argv0))) {
                return true;
            }
        }
        if (!empty($GLOBALS['nectra_db_soft_fail'])) {
            return false;
        }
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
        $base = basename($script);
        if ($base === 'login.php') {
            return false;
        }
        if (preg_match('#/(admin|cron|database)/#', $script)) {
            return true;
        }
        $name = basename($script);
        return in_array($name, ['process.php', 'health.php'], true);
    }
}

if (!function_exists('nectra_db_is_connected')) {
    function nectra_db_is_connected(): bool
    {
        return !empty($GLOBALS['nectra_db_connected'])
            && isset($GLOBALS['nectra_db_conn'])
            && $GLOBALS['nectra_db_conn'] instanceof mysqli;
    }
}

if (!function_exists('nectra_db_connect_timeout')) {
    function nectra_db_connect_timeout(): int
    {
        return nectra_is_local_environment() ? 3 : 8;
    }
}

if (!function_exists('nectra_db_attempt_connect')) {
    function nectra_db_attempt_connect(string $host, string $user, string $pass, string $dbname = ''): ?mysqli
    {
        if (function_exists('mysqli_report')) {
            mysqli_report(MYSQLI_REPORT_OFF);
        }
        if (!function_exists('mysqli_init')) {
            $GLOBALS['nectra_db_last_error'] = 'PHP mysqli extension is not enabled';
            return null;
        }

        $mysqli = mysqli_init();
        if (!$mysqli) {
            $GLOBALS['nectra_db_last_error'] = 'Could not initialize mysqli';
            return null;
        }

        $timeout = nectra_db_connect_timeout();
        if (defined('MYSQLI_OPT_CONNECT_TIMEOUT')) {
            @mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, $timeout);
        }
        if (defined('MYSQLI_OPT_READ_TIMEOUT')) {
            @mysqli_options($mysqli, MYSQLI_OPT_READ_TIMEOUT, $timeout);
        }

        $port = 3306;
        $connected = @mysqli_real_connect($mysqli, $host, $user, $pass, $dbname !== '' ? $dbname : null, $port);
        if ($connected && !$mysqli->connect_error) {
            if (method_exists($mysqli, 'set_charset')) {
                $mysqli->set_charset('utf8mb4');
            }
            return $mysqli;
        }
        $GLOBALS['nectra_db_last_error'] = $mysqli->connect_error ?: 'Connection timed out or refused';
        return null;
    }
}

if (!function_exists('nectra_db_pick_database')) {
    function nectra_db_pick_database(mysqli $tempConn, array $candidates): string
    {
        foreach ($candidates as $cand) {
            if ($cand !== '' && @$tempConn->select_db($cand)) {
                return $cand;
            }
        }
        $fallback = $candidates[0] ?? 'nectradigital';
        @$tempConn->query(
            "CREATE DATABASE IF NOT EXISTS `{$fallback}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
        return $fallback;
    }
}

$host = nectra_is_local_environment() ? '127.0.0.1' : 'localhost';
$user = '';
$pass = '';
$dbname = '';

if (is_file(__DIR__ . '/db.local.php')) {
    require __DIR__ . '/db.local.php';
}

$conn = null;
$GLOBALS['nectra_db_connected'] = false;
$GLOBALS['nectra_db_last_error'] = '';

if ($user === '' || $dbname === '') {
    if (nectra_db_fail_hard()) {
        die('Database not configured. Create includes/db.local.php with $host, $user, $pass, $dbname.');
    }
    $GLOBALS['nectra_db_conn'] = null;
    return;
}

$dbCandidates = array_values(array_unique(array_filter([
    $dbname,
    'nectradigital',
    'nectradigital_final',
    'nectra',
    'nectra_final',
    'nectra_db',
    'test',
])));

if (nectra_is_local_environment()) {
    $hostCandidates = array_values(array_unique(array_filter([
        $host === 'localhost' ? '127.0.0.1' : $host,
        '127.0.0.1',
    ])));
} else {
    $hostCandidates = array_values(array_unique(array_filter([
        $host,
        'localhost',
    ])));
}

$lastError = '';

foreach ($hostCandidates as $tryHost) {
    $conn = nectra_db_attempt_connect($tryHost, $user, $pass, $dbname);
    if ($conn) {
        $host = $tryHost;
        break;
    }
    $lastError = $GLOBALS['nectra_db_last_error'] ?? $lastError;

    $bootstrap = nectra_db_attempt_connect($tryHost, $user, $pass, '');
    if ($bootstrap instanceof mysqli) {
        $dbname = nectra_db_pick_database($bootstrap, $dbCandidates);
        $bootstrap->close();
        $conn = nectra_db_attempt_connect($tryHost, $user, $pass, $dbname);
        if ($conn) {
            $host = $tryHost;
            break;
        }
        $lastError = $GLOBALS['nectra_db_last_error'] ?? $lastError;
    }
}

$GLOBALS['nectra_db_conn'] = $conn;
$GLOBALS['nectra_db_connected'] = $conn instanceof mysqli;

if (!$conn) {
    if (nectra_db_fail_hard()) {
        die(
            'Database Connection Failed: '
            . ($lastError ?: 'Unknown error')
            . '. Please verify database credentials in includes/db.local.php.'
        );
    }
    error_log('[Nectra] Database unavailable: ' . $lastError);
    return;
}

if (!function_exists('clean_input')) {
    function clean_input($data)
    {
        global $conn;
        $data = sanitize_db_text($data);
        if (!$conn instanceof mysqli) {
            return addslashes($data);
        }
        return $conn->real_escape_string($data);
    }
}

if (!function_exists('is_spam')) {
    function is_spam($text)
    {
        $bad_words = ['<a href', 'http', 'https', 'www', '.com', '.ru', 'cryto', 'forex'];
        foreach ($bad_words as $word) {
            if (stripos($text, $word) !== false) {
                return true;
            }
        }
        return false;
    }
}
