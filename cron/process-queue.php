<?php
/**
 * Process queued landing page generation (cron: every 5 min)
 * php cron/process-queue.php
 */
require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\LandingPageGenerator;

header('Content-Type: text/plain');
$limit = (int)($_GET['limit'] ?? 100);
$result = LandingPageGenerator::processQueue($limit);
echo "Processed: {$result['processed']} queue items\n";
