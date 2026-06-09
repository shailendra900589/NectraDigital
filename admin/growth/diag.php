<?php
/**
 * Growth admin diagnostics — delete after fixing production issues.
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

echo "Growth Admin Diagnostics\n";
echo "PHP: " . PHP_VERSION . "\n\n";

try {
    require_once __DIR__ . '/../../includes/growth/bootstrap.php';
    echo "OK bootstrap.php\n";

    require_once __DIR__ . '/includes/auth.php';
    echo "OK auth.php (session active)\n";

    echo "DB host: " . ge_conn()->host_info . "\n";
    echo "ge_is_ready: " . (ge_is_ready() ? 'yes' : 'no') . "\n";

    $tables = ['ge_services', 'ge_cities', 'ge_landing_pages', 'ge_settings', 'ge_generation_jobs', 'ge_industries', 'ge_crm_leads'];
    foreach ($tables as $t) {
        echo $t . ': ' . (ge_table_exists($t) ? 'yes' : 'no') . "\n";
    }

    // Simulate second bootstrap load (was causing HTTP 500)
    require_once __DIR__ . '/../../includes/growth/bootstrap.php';
    echo "OK second bootstrap load\n";

    require_once __DIR__ . '/includes/layout.php';
    echo "OK layout.php\n";

    echo "\nAll checks passed. index.php should work.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "FAIL: " . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
}
