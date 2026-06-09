<?php
/**
 * Growth admin diagnostics — visit while logged in.
 * https://www.nectradigital.com/admin/growth/diag.php
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

echo "Growth Admin Diagnostics\nPHP: " . PHP_VERSION . "\n\n";

try {
    require_once __DIR__ . '/init.php';
    echo "OK init.php\n";
    echo "DB: " . ge_conn()->host_info . "\n";
    echo "ge_is_ready: " . (ge_is_ready() ? 'yes' : 'no') . "\n";

    foreach (['ge_services', 'ge_cities', 'ge_landing_pages', 'ge_settings'] as $t) {
        echo "$t: " . (ge_table_exists($t) ? 'yes' : 'NO — run migrate') . "\n";
    }

    if (ge_table_exists('ge_services')) {
        $n = ge_conn()->query("SELECT COUNT(*) AS c FROM ge_services")->fetch_assoc()['c'] ?? 0;
        echo "ge_services rows: $n\n";
    }

    require_once __DIR__ . '/includes/layout.php';
    echo "OK layout.php\n";

    echo "\nAll checks passed.\n";
    echo "Services: /admin/growth/services.php\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "FAIL: " . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
}
