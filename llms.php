<?php
require_once 'includes/db.php';
require_once 'includes/seo-data.php';
require_once 'includes/blog_orphan.php';

blog_orphan_ensure_schema($conn);
$listWhere = blog_listable_sql();

header('Content-Type: text/plain; charset=utf-8');

if(!defined('SITE_URL')) {
    define('SITE_URL', 'https://www.nectradigital.com');
}

echo "# Nectra Digital\n\n";
echo "> Best SEO company in India. Search engine optimization services, AI automation, digital marketing, web development, and software development.\n\n";
echo "## Founder\n";
echo "- " . FOUNDER_NAME . " — " . FOUNDER_TITLE . " (" . FOUNDER_EXPERIENCE . " experience)\n";
echo "- Expertise: " . implode(', ', FOUNDER_EXPERTISE) . "\n\n";

echo "## Main Pages\n";
echo "- [Home](" . SITE_URL . "/)\n";
echo "- [About & Founder](" . SITE_URL . "/about)\n";
echo "- [All Services](" . SITE_URL . "/services)\n";
echo "- [AEO Answers](" . SITE_URL . "/aeo)\n";
echo "- [Content Strategy](" . SITE_URL . "/content-strategy)\n";
echo "- [Portfolio](" . SITE_URL . "/portfolio)\n";
echo "- [Intel / Blog](" . SITE_URL . "/insights)\n";
echo "- [Contact](" . SITE_URL . "/contact)\n\n";

echo "## SEO Services\n";
foreach (get_services_data() as $slug => $svc) {
    echo "- [" . $svc['h1'] . "](" . SITE_URL . "/" . $slug . ")\n";
}
echo "\n";

echo "## City Locations\n";
foreach (get_cities_data() as $slug => $city) {
    echo "- [Digital Agency " . $city['name'] . "](" . SITE_URL . "/digital-agency-" . $slug . ")\n";
}
echo "\n";

echo "## Legal\n";
echo "- [Privacy Policy](" . SITE_URL . "/privacy)\n";
echo "- [Terms](" . SITE_URL . "/terms)\n";
echo "- [Disclaimer](" . SITE_URL . "/disclaimer)\n";
echo "- [Editorial Guidelines](" . SITE_URL . "/editorial-guidelines)\n\n";

echo "## Blog Posts\n";
$sql = "SELECT title, slug FROM blog_posts WHERE {$listWhere} ORDER BY created_at DESC";
$result = $conn->query($sql);
if($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "- [" . nectra_decode_entities($row['title']) . "](" . SITE_URL . "/" . $row['slug'] . ")\n";
    }
} else {
    echo "- No posts yet.\n";
}
echo "\n";

echo "## Technical\n";
echo "- [Sitemap](" . SITE_URL . "/sitemap.xml)\n";
echo "- [Robots](" . SITE_URL . "/robots.txt)\n";
?>
