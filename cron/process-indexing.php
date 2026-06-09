<?php
/**
 * Cron: process indexing queue via IndexNow + sitemap pings.
 * CLI: php cron/process-indexing.php
 * Web (protect in production): /cron/process-indexing.php?token=YOUR_CRON_TOKEN
 */
require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\Engines\IndexingEngine;

$token = ge_setting('cron_token', '');
if (php_sapi_name() !== 'cli') {
    $provided = $_GET['token'] ?? '';
    if ($token === '' || !hash_equals($token, $provided)) {
        http_response_code(403);
        die('Forbidden');
    }
}

$result = IndexingEngine::processQueue((int)ge_setting('index_batch_size', 50));

if (php_sapi_name() === 'cli') {
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
} else {
    header('Content-Type: application/json');
    echo json_encode($result);
}
