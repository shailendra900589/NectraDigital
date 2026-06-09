<?php
require_once 'includes/db.php';
require_once 'includes/growth/bootstrap.php';

header('Content-Type: application/xml; charset=utf-8');

if (ge_is_ready()) {
    echo \Growth\Engines\SitemapEngine::generateXml();
} else {
    require_once 'includes/seo-data.php';
    header('Content-Type: application/xml; charset=utf-8');
    $base_url = SITE_URL;
    echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach (['','services','about','contact','insights'] as $p) {
        $loc = $base_url . ($p ? "/$p" : '');
        echo "<url><loc>$loc</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>";
    }
    $result = $conn->query("SELECT slug, created_at FROM blog_posts ORDER BY created_at DESC");
    if ($result) while ($row = $result->fetch_assoc()) {
        echo "<url><loc>{$base_url}/{$row['slug']}</loc><priority>0.7</priority></url>";
    }
    echo '</urlset>';
}
