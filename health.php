<?php
/**
 * Quick health check — https://www.nectradigital.com/health.php
 * Delete or protect this file after debugging.
 */
header('Content-Type: text/plain; charset=utf-8');
echo "OK PHP " . PHP_VERSION . "\n";

try {
    require_once __DIR__ . '/includes/config.php';
    echo "OK config.php\n";
    require_once __DIR__ . '/includes/db.php';
    echo "OK db.php — connected\n";
    require_once __DIR__ . '/includes/seo-data.php';
    echo "OK seo-data.php\n";
    require_once __DIR__ . '/includes/seo-components.php';
    echo "OK seo-components.php\n";
    $res = $conn->query("SHOW TABLES LIKE 'ge_%'");
    echo "OK ge_* tables: " . ($res ? $res->num_rows : 0) . "\n";
    echo "\nAll checks passed. Homepage should work.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "FAIL: " . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
}
