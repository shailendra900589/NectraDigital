<?php
/**
 * Run database migrations (v1 + v2)
 * CLI: php database/migrate.php
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "Nectra Digital — Migration starting...\n";

if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

try {
    require_once __DIR__ . '/../includes/db.php';
} catch (Throwable $e) {
    die("DB load failed: " . $e->getMessage() . "\n");
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    die("ERROR: \$conn not set. Create includes/db.local.php with Hostinger MySQL credentials.\n");
}

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error . "\n");
}

echo "DB connected OK\n\n";

function run_sql_file(mysqli $conn, string $path): array {
    if (!file_exists($path)) {
        return ['ok' => 0, 'fail' => 0, 'msg' => "Missing: $path\n"];
    }
    $sql = file_get_contents($path);
    $sql = preg_replace('/^--.*$/m', '', $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $ok = 0;
    $fail = 0;
    $out = "=== " . basename($path) . " ===\n";
    foreach ($statements as $statement) {
        if (strlen($statement) < 5) continue;
        if ($conn->query($statement)) {
            $ok++;
            if (preg_match('/CREATE TABLE.*?(\w+)/i', $statement, $m)) {
                $out .= "OK: Table {$m[1]}\n";
            }
        } else {
            $err = $conn->error;
            if (stripos($err, 'Duplicate') !== false || stripos($err, 'already exists') !== false) {
                $ok++;
                $out .= "SKIP: " . substr($statement, 0, 50) . "... ($err)\n";
            } else {
                $fail++;
                $out .= "FAIL: $err\n  " . substr($statement, 0, 80) . "...\n";
            }
        }
    }
    $out .= "Done: $ok ok, $fail failed\n\n";
    return ['ok' => $ok, 'fail' => $fail, 'msg' => $out];
}

$r1 = run_sql_file($conn, __DIR__ . '/schema.sql');
$r2 = run_sql_file($conn, __DIR__ . '/schema-v2.sql');

echo $r1['msg'] . $r2['msg'];
echo "Total: " . ($r1['ok'] + $r2['ok']) . " ok, " . ($r1['fail'] + $r2['fail']) . " failed\n";

if (file_exists(__DIR__ . '/../includes/growth/bootstrap.php')) {
    require_once __DIR__ . '/../includes/growth/bootstrap.php';
    if (function_exists('ge_is_ready') && ge_is_ready()) {
        require_once __DIR__ . '/../includes/growth/engines/CatalogSyncEngine.php';
        $sync = \Growth\Engines\CatalogSyncEngine::syncAll();
        echo "\nCatalog sync: {$sync['services']} services, {$sync['cities']} cities\n";
        echo "Run: php database/generate-all-pages.php — to create all service × city landing pages\n";
    }
}

echo "Admin: https://www.nectradigital.com/admin/growth/\n";
