<?php
/**
 * Cron: process indexing queue via IndexNow + sitemap pings.
 * CLI: php cron/process-indexing.php
 * Web (protect in production): /cron/process-indexing.php?token=YOUR_CRON_TOKEN
 */
require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\Engines\IndexingEngine;

ge_cron_auth_or_exit();

$result = IndexingEngine::processAllQueue((int)ge_setting('index_batch_size', 100), 50);
IndexingEngine::logRun('cron_process_indexing', $result);

if (php_sapi_name() === 'cli') {
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
} else {
    header('Content-Type: application/json');
    echo json_encode($result);
}
