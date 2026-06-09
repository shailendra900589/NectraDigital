<?php
/**
 * Generate all Service × City landing pages (CLI)
 * php database/generate-all-pages.php
 * php database/generate-all-pages.php --service=web-development-services
 * php database/generate-all-pages.php --regenerate
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);
set_time_limit(0);

if (php_sapi_name() !== 'cli') {
    die("CLI only\n");
}

// Show progress immediately (no output buffering hang)
while (ob_get_level() > 0) {
    ob_end_flush();
}
ob_implicit_flush(true);

require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\Engines\CatalogSyncEngine;
use Growth\Models\Service;
use Growth\Models\City;
use Growth\Models\LandingPage;
use Growth\LandingPageGenerator;

if (!ge_is_ready()) {
    die("ERROR: Run database/migrate.php first.\n");
}

$regenerate = in_array('--regenerate', $argv, true);
$serviceSlug = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--service=')) {
        $serviceSlug = substr($arg, 10);
    }
}

$existing = LandingPage::count('published');
echo "Existing published pages: {$existing}\n";

echo "Syncing catalog from website...\n";
$sync = CatalogSyncEngine::syncAll();
echo "Synced {$sync['services']} services, {$sync['cities']} cities\n";
echo "Expected matrix: " . ($sync['services'] * $sync['cities']) . " pages\n\n";

$started = microtime(true);

if ($serviceSlug) {
    $service = Service::findBySlug($serviceSlug);
    if (!$service) {
        die("Service not found: $serviceSlug\n");
    }
    echo "Generating city pages for: {$service['name']}...\n";
    $result = LandingPageGenerator::generateBulk(
        [(int)$service['id']],
        array_column(City::all(true), 'id'),
        [0],
        $regenerate
    );
} else {
    echo "Generating full service × city matrix (no live IndexNow per page — fast mode)...\n";
    $result = LandingPageGenerator::generateFullMatrix(false, $regenerate);
}

$elapsed = round(microtime(true) - $started, 1);
$totalNow = LandingPage::count('published');

echo "\nDone in {$elapsed}s: {$result['processed']} processed, {$result['failed']} failed";
if (!empty($result['skipped'])) {
    echo ", {$result['skipped']} skipped (already existed)";
}
echo "\nPublished pages now: {$totalNow}\n";

if (!empty($result['slugs'])) {
    echo "Sample URLs:\n";
    foreach ($result['slugs'] as $slug) {
        echo "  /$slug\n";
    }
}

if (class_exists(\Growth\Engines\DiscoveryEngine::class)) {
    echo "\nSubmitting ALL URLs to search engines (IndexNow batch)...\n";
    $pub = \Growth\Engines\DiscoveryEngine::publishAll(10000, 100);
    echo "Queued: {$pub['queued']}, IndexNow submitted: {$pub['processed']}\n";
}

echo "Complete.\n";
