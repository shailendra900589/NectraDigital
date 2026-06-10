<?php
/**
 * Nectra Digital Growth Engine — Bootstrap & Autoload
 */
if (defined('GE_BOOTSTRAP_LOADED')) {
    return;
}
define('GE_BOOTSTRAP_LOADED', true);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/helpers.php';

spl_autoload_register(function ($class) {
    $prefix = 'Growth\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $parts = explode('\\', $relative);
    $type = $parts[0] ?? '';
    $name = $parts[1] ?? $parts[0] ?? '';

    $map = [
        'Models' => __DIR__ . '/models/' . $name . '.php',
        'Engines' => __DIR__ . '/engines/' . $name . '.php',
    ];

    if (isset($map[$type]) && file_exists($map[$type])) {
        require_once $map[$type];
    } elseif ($relative === 'LandingPageGenerator') {
        require_once __DIR__ . '/LandingPageGenerator.php';
    } elseif ($class === 'Growth\\LandingPageGenerator') {
        require_once __DIR__ . '/LandingPageGenerator.php';
    }
});

function ge_conn(): mysqli {
    global $conn;
    return $conn;
}

function ge_setting(string $key, $default = null) {
    static $cache = [];
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    if (!ge_table_exists('ge_settings')) {
        return $default;
    }
    $db = ge_conn();
    $stmt = $db->prepare("SELECT setting_value FROM ge_settings WHERE setting_key = ? LIMIT 1");
    if (!$stmt) {
        return $default;
    }
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $cache[$key] = $row['setting_value'];
        return $cache[$key];
    }
    return $default;
}

function ge_table_exists(string $table): bool {
    static $cache = [];
    if (isset($cache[$table])) {
        return $cache[$table];
    }
    $db = ge_conn();
    $table = $db->real_escape_string($table);
    $r = $db->query("SHOW TABLES LIKE '$table'");
    $cache[$table] = $r && $r->num_rows > 0;
    return $cache[$table];
}

function ge_is_ready(): bool {
    return ge_table_exists('ge_services') && ge_table_exists('ge_landing_pages');
}
