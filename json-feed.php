<?php
header('Content-Type: application/feed+json; charset=utf-8');
include 'includes/db.php';
include 'includes/config.php';

$result = $conn->query("SELECT * FROM blog_posts WHERE created_at <= NOW() ORDER BY created_at DESC LIMIT 20");

$items = [];
while($post = $result->fetch_assoc()) {
    $title = html_entity_decode(htmlspecialchars_decode($post['title']), ENT_QUOTES, 'UTF-8');
    $desc = !empty($post['meta_description']) ? $post['meta_description'] : substr(strip_tags($post['content']), 0, 250);
    $img = !empty($post['image']) ? SITE_URL . '/' . $post['image'] : '';
    
    $items[] = [
        "id" => SITE_URL . '/' . $post['slug'],
        "url" => SITE_URL . '/' . $post['slug'],
        "title" => $title,
        "content_text" => strip_tags(html_entity_decode($desc, ENT_QUOTES, 'UTF-8')),
        "date_published" => date('c', strtotime($post['created_at'])),
        "image" => $img,
        "authors" => [["name" => "Nectra Digital"]],
        "tags" => [html_entity_decode($post['category'], ENT_QUOTES, 'UTF-8')]
    ];
}

echo json_encode([
    "version" => "https://jsonfeed.org/version/1.1",
    "title" => "Nectra Digital - Tech & Digital Marketing Insights",
    "home_page_url" => SITE_URL,
    "feed_url" => SITE_URL . "/feed.json",
    "description" => "Latest insights on web development, SEO, AI automation, and digital marketing from Nectra Digital, Lucknow.",
    "icon" => SITE_URL . "/assets/images/logo.png",
    "favicon" => SITE_URL . "/assets/favicon_io/favicon-32x32.png",
    "language" => "en-IN",
    "authors" => [["name" => "Nectra Digital", "url" => SITE_URL]],
    "items" => $items
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
