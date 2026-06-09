<?php
require_once 'includes/db.php';

if(!defined('SITE_URL')) {
    define('SITE_URL', 'https://www.nectradigital.com');
}

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

// Google News only accepts articles from the last 48 hours, but we will fetch the latest 20 to keep the feed populated.
$sql = "SELECT title, slug, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 20";
$result = $conn->query($sql);

if($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $post_url = SITE_URL . '/' . htmlspecialchars($row['slug']);
        // Google News requires ISO 8601 format (e.g., 2026-03-08T10:13:16+05:30)
        $pub_date = date('c', strtotime($row['created_at'])); 
        
        echo "  <url>\n";
        echo "    <loc>" . $post_url . "</loc>\n";
        echo "    <news:news>\n";
        echo "      <news:publication>\n";
        echo "        <news:name>Nectra Digital</news:name>\n";
        echo "        <news:language>en</news:language>\n";
        echo "      </news:publication>\n";
        echo "      <news:publication_date>" . $pub_date . "</news:publication_date>\n";
        echo "      <news:title><![CDATA[" . $row['title'] . "]]></news:title>\n";
        echo "    </news:news>\n";
        echo "  </url>\n";
    }
}

echo '</urlset>';
?>