<?php
require_once 'includes/db.php';
if(!defined('SITE_URL')) require_once 'includes/config.php';

header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=1800');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<?php
$sql = "SELECT title, slug, category, image, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 50";
$result = $conn->query($sql);

if($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $post_url = SITE_URL . '/' . htmlspecialchars($row['slug']);
        $pub_date = date('c', strtotime($row['created_at']));
        
        $title = $row['title'];
        while ($title !== html_entity_decode($title, ENT_QUOTES, 'UTF-8')) {
            $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        }
        
        $cat = html_entity_decode($row['category'], ENT_QUOTES, 'UTF-8');
        
        $img_url = '';
        if(!empty($row['image'])) {
            $img_url = (strpos($row['image'], 'http') === 0) ? $row['image'] : SITE_URL . '/' . ltrim($row['image'], '/');
        }
?>
  <url>
    <loc><?php echo $post_url; ?></loc>
    <news:news>
      <news:publication>
        <news:name>Nectra Digital</news:name>
        <news:language>en</news:language>
      </news:publication>
      <news:publication_date><?php echo $pub_date; ?></news:publication_date>
      <news:title><![CDATA[<?php echo $title; ?>]]></news:title>
      <news:keywords><![CDATA[<?php echo $cat; ?>, web development, digital marketing, SEO, Lucknow]]></news:keywords>
    </news:news>
<?php if(!empty($img_url)): ?>
    <image:image>
      <image:loc><?php echo htmlspecialchars($img_url); ?></image:loc>
      <image:title><![CDATA[<?php echo $title; ?>]]></image:title>
    </image:image>
<?php endif; ?>
  </url>
<?php
    }
}
?>
</urlset>
