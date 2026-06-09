<?php
// 1. DATABASE CONNECTION
require_once 'includes/db.php';

if(!defined('SITE_URL')) {
    define('SITE_URL', 'https://www.nectradigital.com');
}

// 2. SET XML HEADER
header('Content-Type: application/rss+xml; charset=utf-8');

// 3. BUILD RSS STRUCTURE
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
echo '<channel>' . "\n";
echo '<title>Nectra Digital Intel</title>' . "\n";
echo '<link>' . SITE_URL . '</link>' . "\n";
echo '<description>High-performance Next.js Web Development, AI Automation, and ROI-driven Marketing for global brands.</description>' . "\n";
echo '<language>en-us</language>' . "\n";
echo '<atom:link href="' . SITE_URL . '/rss.xml" rel="self" type="application/rss+xml" />' . "\n";

// 4. FETCH LATEST BLOG POSTS
$sql = "SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 20";
$result = $conn->query($sql);

if($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $post_url = SITE_URL . '/' . htmlspecialchars($row['slug']);
        
        // Create a clean excerpt for the description
        $clean_desc = mb_substr(strip_tags($row['content']), 0, 250) . '...';
        
        // Format the date properly for RSS (RFC 822)
        $pubDate = date('D, d M Y H:i:s O', strtotime($row['created_at']));

        echo '<item>' . "\n";
        echo '<title><![CDATA[' . $row['title'] . ']]></title>' . "\n";
        echo '<link>' . $post_url . '</link>' . "\n";
        echo '<guid isPermaLink="true">' . $post_url . '</guid>' . "\n";
        echo '<description><![CDATA[' . $clean_desc . ']]></description>' . "\n";
        echo '<category><![CDATA[' . $row['category'] . ']]></category>' . "\n";
        echo '<pubDate>' . $pubDate . '</pubDate>' . "\n";
        echo '</item>' . "\n";
    }
}

echo '</channel>' . "\n";
echo '</rss>';
?>