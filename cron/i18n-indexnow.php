<?php
/**
 * Cron: submit all published URLs in every language via IndexNow.
 * CLI: php cron/i18n-indexnow.php
 * Web: /cron/i18n-indexnow.php?token=YOUR_CRON_TOKEN
 */
require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\Engines\IndexingEngine;

ge_cron_auth_or_exit();

$result = IndexingEngine::submitAllPublishedUrls(true, true);

if (php_sapi_name() === 'cli') {
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
} else {
    header('Content-Type: application/json');
    echo json_encode($result);
}
