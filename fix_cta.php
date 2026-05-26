<?php
// ONE-TIME FIX: Remove garbled WhatsApp CTA text from all blog posts
// DELETE THIS FILE AFTER RUNNING
include 'includes/db.php';

echo "<h2>Fixing CTA/WhatsApp garbled text...</h2>";

$result = $conn->query("SELECT id, title, content FROM blog_posts");
$fixed = 0;

while($row = $result->fetch_assoc()) {
    $content = $row['content'];
    $original = $content;
    
    // Remove heading blocks containing "Click Here to" + WhatsApp/phone
    $content = preg_replace('/<(h[1-6])[^>]*>\s*<b[^>]*>.*?Click Here to.*?<\/b>\s*<\/\1>/si', '', $content);
    $content = preg_replace('/<(h[1-6])[^>]*>\s*<b[^>]*>\s*<a[^>]*>.*?Click Here to.*?<\/a>\s*<\/b>\s*<\/\1>/si', '', $content);
    $content = preg_replace('/<(h[1-6])[^>]*>.*?Click Here to (Talk|WhatsApp|Explode|Start).*?<\/\1>/si', '', $content);
    
    // Remove any standalone garbled emoji-like sequences before text
    $content = preg_replace('/[\x{1F300}-\x{1F9FF}]/u', '', $content);
    
    if($content !== $original) {
        $stmt = $conn->prepare("UPDATE blog_posts SET content=? WHERE id=?");
        $stmt->bind_param("si", $content, $row['id']);
        $stmt->execute();
        $fixed++;
        echo "<p>Fixed: Post #" . $row['id'] . " - " . htmlspecialchars($row['title']) . "</p>";
    }
}

echo "<h3>Done! Fixed $fixed posts.</h3>";
echo "<p style='color:red;'><strong>DELETE THIS FILE NOW: rm fix_cta.php</strong></p>";
?>
