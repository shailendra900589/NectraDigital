<?php
require_once 'includes/db.php';
if(!defined('SITE_URL')) require_once 'includes/config.php';

header('Content-Type: application/rss+xml; charset=utf-8');
header('Cache-Control: public, max-age=3600');

$sql = "SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 30";
$result = $conn->query($sql);

$lastBuild = date('D, d M Y H:i:s O');
if($result && $result->num_rows > 0) {
    $first = $result->fetch_assoc();
    $lastBuild = date('D, d M Y H:i:s O', strtotime($first['created_at']));
    mysqli_data_seek($result, 0);
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" 
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:media="http://search.yahoo.com/mrss/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
    <title>Nectra Digital - Web Development, SEO &amp; Digital Marketing Insights</title>
    <link><?php echo SITE_URL; ?></link>
    <description>Expert insights on web development, SEO strategies, digital marketing, AI automation, and custom software development from Nectra Digital — Lucknow's top software development company.</description>
    <language>en-in</language>
    <lastBuildDate><?php echo $lastBuild; ?></lastBuildDate>
    <generator>Nectra Digital CMS</generator>
    <managingEditor>contact@nectradigital.com (Nectra Digital)</managingEditor>
    <webMaster>contact@nectradigital.com (Nectra Digital)</webMaster>
    <copyright>Copyright <?php echo date('Y'); ?> Nectra Digital. All rights reserved.</copyright>
    <atom:link href="<?php echo SITE_URL; ?>/rss.xml" rel="self" type="application/rss+xml" />
    <image>
        <url><?php echo SITE_URL; ?>/assets/images/logo.png</url>
        <title>Nectra Digital</title>
        <link><?php echo SITE_URL; ?></link>
        <width>144</width>
        <height>144</height>
    </image>
    <category>Technology</category>
    <category>Web Development</category>
    <category>Digital Marketing</category>
    <category>SEO</category>
<?php
if($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $post_url = SITE_URL . '/' . htmlspecialchars($row['slug']);
        
        // Decode title properly for CDATA
        $title = $row['title'];
        while ($title !== html_entity_decode($title, ENT_QUOTES, 'UTF-8')) {
            $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        }
        
        // Clean excerpt
        $raw = preg_replace('/<\/(p|h[1-6]|div|li|br)>/i', ' ', $row['content']);
        $raw = strip_tags($raw);
        $raw = preg_replace('/\s+/', ' ', trim($raw));
        $raw = html_entity_decode($raw, ENT_QUOTES, 'UTF-8');
        $excerpt = mb_substr($raw, 0, 300) . '...';
        
        // Category
        $cat = html_entity_decode($row['category'], ENT_QUOTES, 'UTF-8');
        
        // Image URL
        $img_url = '';
        if(!empty($row['image'])) {
            if(strpos($row['image'], 'http') === 0) {
                $img_url = $row['image'];
            } else {
                $img_url = SITE_URL . '/' . ltrim($row['image'], '/');
            }
        }
        
        $pubDate = date('D, d M Y H:i:s O', strtotime($row['created_at']));
?>
    <item>
        <title><![CDATA[<?php echo $title; ?>]]></title>
        <link><?php echo $post_url; ?></link>
        <guid isPermaLink="true"><?php echo $post_url; ?></guid>
        <description><![CDATA[<?php echo $excerpt; ?>]]></description>
        <dc:creator><![CDATA[Nectra Digital]]></dc:creator>
        <category><![CDATA[<?php echo $cat; ?>]]></category>
        <pubDate><?php echo $pubDate; ?></pubDate>
<?php if(!empty($img_url)): ?>
        <enclosure url="<?php echo htmlspecialchars($img_url); ?>" type="image/webp" length="0" />
        <media:content url="<?php echo htmlspecialchars($img_url); ?>" medium="image">
            <media:title type="plain"><?php echo htmlspecialchars($title); ?></media:title>
        </media:content>
        <media:thumbnail url="<?php echo htmlspecialchars($img_url); ?>" />
<?php endif; ?>
    </item>
<?php
    }
}
?>
</channel>
</rss>
