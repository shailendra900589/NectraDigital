<?php
/**
 * Refresh high-intent SEO keywords + meta on all landing pages.
 * CLI: php database/refresh-seo-keywords.php
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);
set_time_limit(0);

if (php_sapi_name() !== 'cli') {
    die("CLI only\n");
}

require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\Engines\SeoRefreshEngine;
use Growth\Engines\DiscoveryEngine;

if (!ge_is_ready()) {
    die("ERROR: Run database/migrate.php first.\n");
}

echo "Refreshing SEO keywords and meta on all landing pages...\n";
$result = SeoRefreshEngine::refreshAll(true);
echo "Updated: {$result['updated']} pages\n";
echo "URLs signaled to search engines: {$result['urls_signaled']}\n";

echo "Pinging all sitemaps and feeds...\n";
$pub = DiscoveryEngine::publishAll(0, 50);
echo "Indexed batch processed: {$pub['processed']}\n";
echo "Done.\n";
