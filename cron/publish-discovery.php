<?php
/**
 * Cron: publish to all search engines + ping feeds/sitemaps.
 * CLI: php cron/publish-discovery.php
 * Web: /cron/publish-discovery.php?token=YOUR_CRON_TOKEN
 */
require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\Engines\DiscoveryEngine;
use Growth\Engines\SeoRefreshEngine;

ge_cron_auth_or_exit();

$refresh = in_array('--refresh-seo', $argv ?? [], true) || !empty($_GET['refresh_seo']);
$result = ['discovery' => DiscoveryEngine::publishAll(500, 100)];

if ($refresh && ge_is_ready()) {
    $result['seo_refresh'] = SeoRefreshEngine::refreshAll(true);
}

if (php_sapi_name() === 'cli') {
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
} else {
    header('Content-Type: application/json');
    echo json_encode($result);
}
