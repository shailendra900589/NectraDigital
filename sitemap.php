<?php
require_once 'includes/db.php';

// 1. SET HEADER (To tell browser this is XML, not HTML)
header("Content-Type: application/xml; charset=utf-8");

// 2. DEFINE BASE URL (Must match your forced WWW domain)
require_once 'includes/config.php';
$base_url = SITE_URL;

// 3. START XML STRUCTURE
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// --- PART A: STATIC PAGES (Manually List Main Pages) ---
$static_pages = [
    ""          => "1.0", // Homepage (High Priority)
    "services"  => "0.8",
    "portfolio" => "0.8",
    "insights"  => "0.9", // Blog Feed
    "careers"   => "0.7",
    "contact"   => "0.6",
    "hire-experts"   => "0.6"
];

foreach ($static_pages as $slug => $priority) {
    $url = $base_url . ($slug ? "/" . $slug : "");
    $date = date("c"); // Current ISO 8601 date
    
    echo "
    <url>
        <loc>{$url}</loc>
        <lastmod>{$date}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>{$priority}</priority>
    </url>";
}

// --- PART B: DYNAMIC BLOG POSTS (From Database) ---
$sql = "SELECT slug, created_at FROM blog_posts ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Clean URL Logic (No .php, No ?slug=)
        $url = $base_url . "/" . htmlspecialchars($row['slug']);
        $date = date("c", strtotime($row['created_at']));
        
        echo "
    <url>
        <loc>{$url}</loc>
        <lastmod>{$date}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>";
    }
}

// --- PART C: DYNAMIC CAREER JOBS (Optional) ---

$job_sql = "SELECT id, position, created_at FROM careers WHERE status='Open'";
$job_res = $conn->query($job_sql);
if ($job_res->num_rows > 0) {
    while($job = $job_res->fetch_assoc()) {
        // Assuming you might have single job pages later like /job/123
        $url = $base_url . "/careers"; 
        // leaving commented as currently careers are on one page
    }
}


echo '</urlset>';
?>