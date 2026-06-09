<?php
/**
 * Export city / landing page URLs for Google Search Console listing.
 * ?type=cities|landing|all
 */
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    exit('Forbidden');
}

require_once __DIR__ . '/includes/admin-growth.php';

$type = $_GET['type'] ?? 'all';
$allowed = ['cities', 'landing', 'all'];
if (!in_array($type, $allowed, true)) {
    $type = 'all';
}

$lines = [];
$filename = 'nectra-urls-' . $type . '-' . date('Y-m-d') . '.txt';

if ($type === 'cities' || $type === 'all') {
    foreach (admin_city_hub_urls() as $hub) {
        $lines[] = $hub['hub_url'];
    }
}

if ($type === 'landing' || $type === 'all') {
    foreach (admin_landing_page_urls(50000) as $row) {
        $lines[] = $row['url'];
    }
}

$lines = array_values(array_unique($lines));
sort($lines);

header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-store');

echo "# Nectra Digital — URL export ({$type})\n";
echo '# Generated: ' . date('c') . "\n";
echo '# Total: ' . count($lines) . "\n\n";
echo implode("\n", $lines);
