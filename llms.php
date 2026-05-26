<?php
// 1. CONNECT TO DATABASE
require_once 'includes/db.php';

// 2. SET PROPER HEADERS FOR LLM BOTS
header('Content-Type: text/plain; charset=utf-8');

if(!defined('SITE_URL')) {
    require_once 'includes/config.php';
}

// 3. GENERATE STATIC CONTENT
echo "# Nectra Digital\n\n";
echo "> We engineer digital dominance. High-performance Next.js Web Development, AI Automation, and ROI-driven Marketing for global brands.\n\n";

echo "## Main Links\n";
echo "- [Home](" . SITE_URL . "/)\n";
echo "- [Services](" . SITE_URL . "/services)\n";
echo "- [Portfolio](" . SITE_URL . "/portfolio)\n";
echo "- [Intel / Blog](" . SITE_URL . "/insights)\n";
echo "- [Contact](" . SITE_URL . "/contact)\n\n";

echo "## Opportunities\n";
echo "- [Hire Experts](" . SITE_URL . "/hire-experts)\n";
echo "- [Careers](" . SITE_URL . "/careers)\n\n";

// 4. DYNAMICALLY FETCH BLOG POSTS
echo "## Nectra Intel (Blog Posts)\n";
$sql = "SELECT title, slug FROM blog_posts ORDER BY created_at DESC";
$result = $conn->query($sql);

if($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Automatically outputs: - [Post Title](https://www.nectradigital.com/post-slug)
        echo "- [" . $row['title'] . "](" . SITE_URL . "/" . $row['slug'] . ")\n";
    }
} else {
    echo "- No intel published yet.\n";
}
echo "\n";

// 5. TECHNICAL LINKS
echo "## Technical Info\n";
echo "- [Sitemap](" . SITE_URL . "/sitemap.xml)\n";
echo "- [Robots](" . SITE_URL . "/robots.txt)\n";
?>