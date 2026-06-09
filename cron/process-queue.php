<?php
/**
 * Process queued landing page generation (cron: every 5 min)
 * php cron/process-queue.php
 */
require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\LandingPageGenerator;
use Growth\Engines\IndexingEngine;

header('Content-Type: text/plain');
$limit = (int)($_GET['limit'] ?? 100);
$result = LandingPageGenerator::processQueue($limit);
echo "Processed: {$result['processed']} queue items\n";

if (ge_setting('auto_index_queue', '1') === '1') {
    $idx = IndexingEngine::processQueue((int)ge_setting('index_batch_size', 50));
    echo "Indexing: submitted {$idx['processed']}, failed {$idx['failed']}\n";
}
