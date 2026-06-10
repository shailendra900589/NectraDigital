<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/growth/bootstrap.php';

header('Content-Type: application/xml; charset=utf-8');

$base = SITE_URL;
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

// All blog posts (including orphan) — Bing/Google news discovery
if (isset($conn) && $conn instanceof mysqli) {
    $res = @$conn->query("SELECT title, slug, created_at, category FROM blog_posts ORDER BY created_at DESC LIMIT 30");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $post_url = $base . '/' . htmlspecialchars($row['slug']);
            $pub_date = date('c', strtotime($row['created_at']));
            echo "  <url>\n";
            echo "    <loc>{$post_url}</loc>\n";
            echo "    <news:news>\n";
            echo "      <news:publication>\n";
            echo "        <news:name>Nectra Digital</news:name>\n";
            echo "        <news:language>en</news:language>\n";
            echo "      </news:publication>\n";
            echo "      <news:publication_date>{$pub_date}</news:publication_date>\n";
            echo "      <news:title><![CDATA[" . nectra_decode_entities($row['title']) . "]]></news:title>\n";
            if (!empty($row['category'])) {
                echo "      <news:keywords><![CDATA[" . nectra_decode_entities($row['category']) . ", SEO, digital marketing, India]]></news:keywords>\n";
            }
            echo "    </news:news>\n";
            echo "  </url>\n";
        }
    }
}

// Fresh landing pages (Discover / fresh content signals)
if (ge_is_ready()) {
    $pages = \Growth\Models\LandingPage::forFeed(50);
    foreach ($pages as $p) {
        $loc = $base . '/' . htmlspecialchars($p['slug']);
        $pub = date('c', strtotime($p['generated_at'] ?? 'now'));
        $title = htmlspecialchars($p['meta_title'] ?? $p['h1'], ENT_XML1);
        echo "  <url>\n";
        echo "    <loc>{$loc}</loc>\n";
        echo "    <news:news>\n";
        echo "      <news:publication>\n";
        echo "        <news:name>Nectra Digital</news:name>\n";
        echo "        <news:language>en</news:language>\n";
        echo "      </news:publication>\n";
        echo "      <news:publication_date>{$pub}</news:publication_date>\n";
        echo "      <news:title><![CDATA[{$title}]]></news:title>\n";
        $kw = implode(', ', ge_json_decode($p['keywords_json'] ?? '[]'));
        if ($kw) {
            echo "      <news:keywords><![CDATA[{$kw}]]></news:keywords>\n";
        }
        echo "    </news:news>\n";
        echo "  </url>\n";
    }
}

echo '</urlset>';
