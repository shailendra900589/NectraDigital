<?php 
// 1. SYSTEM FIXES: BUFFERING & CHARSET
ob_start();
header('Content-Type: text/html; charset=utf-8');

require_once 'includes/db.php';

// FORCE DB CHARSET
if(isset($conn)) {
    $conn->set_charset("utf8mb4");
}

// 2. DEFINE FUNCTIONS FIRST
if (!function_exists('clean_input')) {
    function clean_input($data) {
        global $conn;
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return ($conn) ? $conn->real_escape_string($data) : $data;
    }
}

if(!defined('SITE_URL')) {
    define('SITE_URL', 'https://www.nectradigital.com');
}

// 3. FETCH POST DATA
if(isset($_GET['slug'])) {
    $slug = clean_input($_GET['slug']);
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        header("Location: " . SITE_URL . "/404.php"); exit;
    }
} else {
    header("Location: " . SITE_URL . "/insights.php"); exit;
}

// 4. *** ADVANCED MULTI-PHRASE SEO KEYWORD GENERATOR ***
// FIX: Decode HTML entities so "&amp;" turns back into "&" and stops generating "amp" as a keyword!
$decoded_title = htmlspecialchars_decode($post['title'], ENT_QUOTES);
$decoded_category = htmlspecialchars_decode($post['category'], ENT_QUOTES);

$page_title = $decoded_title;

// NEW: Check if manual meta description exists, else auto-generate
if (!empty($post['meta_description'])) {
    $page_desc = htmlspecialchars_decode(strip_tags($post['meta_description']), ENT_QUOTES);
} else {
    $page_desc = htmlspecialchars_decode(mb_substr(strip_tags($post['content']), 0, 160), ENT_QUOTES) . "...";
}

// Clean the title of special characters
$clean_title = strtolower(preg_replace('/[^a-zA-Z0-9 ]/', '', $decoded_title));
$words_array = array_values(array_filter(explode(' ', $clean_title)));

// Expanded stop-word dictionary (Removes useless words)
$stop_words = ['the', 'a', 'an', 'and', 'or', 'but', 'is', 'are', 'was', 'were', 'to', 'in', 'on', 'at', 'by', 'for', 'with', 'about', 'as', 'of', 'how', 'why', 'what', 'when', 'where', 'who', 'will', 'it', 'that', 'this', 'you', 'your', 'need', 'both', 'vs', 'can', 'do', 'does', 'from', 'get', 'we', 'us', 'amp', 'nbsp', 'quot'];

// Filter to get strong single words
$filtered_words = [];
foreach ($words_array as $word) {
    if (strlen($word) > 2 && !in_array($word, $stop_words)) {
        $filtered_words[] = $word;
    }
}

$seo_keywords_array = [];

// A. Add the full exact match phrase (Highly SEO attractive)
$seo_keywords_array[] = $clean_title;

// B. Generate 2-word phrases (Long-tail keywords)
$count = count($filtered_words);
for ($i = 0; $i < $count - 1; $i++) {
    $seo_keywords_array[] = $filtered_words[$i] . ' ' . $filtered_words[$i+1];
}

// C. Generate 3-word phrases (Very specific long-tail)
for ($i = 0; $i < $count - 2; $i++) {
    $seo_keywords_array[] = $filtered_words[$i] . ' ' . $filtered_words[$i+1] . ' ' . $filtered_words[$i+2];
}

// D. Add the strong single words back in
$seo_keywords_array = array_merge($seo_keywords_array, $filtered_words);

// E. Remove duplicates and slice the top 15 most relevant
$seo_keywords_array = array_unique($seo_keywords_array);
$auto_generated_keys = implode(', ', array_slice($seo_keywords_array, 0, 15));

// Final Balanced Keywords: Category + Defaults + Auto-Generated Phrases
$page_keys  = $decoded_category . ", Nectra Digital, Tech Insights, " . $auto_generated_keys;

// Smart Image Path Fix
$display_img = $post['image'];
if (!empty($display_img) && strpos($display_img, 'http') === false) {
    $display_img = SITE_URL . '/' . ltrim($display_img, '/');
}
$page_img = $display_img;
$og_type = 'article';

$post_date = date('c', strtotime($post['created_at']));
$post_modified = !empty($post['updated_at']) ? date('c', strtotime($post['updated_at'])) : $post_date;

require_once 'includes/seo-data.php';

$page_schema = [
    get_breadcrumb_schema([
        ['name' => 'Home', 'url' => SITE_URL . '/'],
        ['name' => 'Intel', 'url' => SITE_URL . '/insights'],
        ['name' => $decoded_title, 'url' => SITE_URL . '/' . $post['slug']]
    ]),
    [
        '@type' => 'BlogPosting',
        '@id' => SITE_URL . '/' . $post['slug'] . '#article',
        'headline' => $decoded_title,
        'description' => $page_desc,
        'image' => $display_img ?: SITE_URL . '/assets/images/logo.png',
        'datePublished' => $post_date,
        'dateModified' => $post_modified,
        'author' => get_founder_schema(),
        'publisher' => ['@id' => SITE_URL . '/#organization'],
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => SITE_URL . '/' . $post['slug']],
        'articleSection' => $decoded_category,
        'inLanguage' => 'en-IN'
    ]
];

// 5. INJECT HEADER & BASE TAG
include 'includes/header.php';
require_once 'includes/seo-components.php'; 
$header_html = ob_get_clean();
$base_tag = '<base href="' . SITE_URL . '/">';
$meta_charset = '<meta charset="UTF-8">'; 

$header_html = str_replace('<head>', "<head>\n" . $meta_charset . "\n" . $base_tag, $header_html);
echo $header_html;

// 6. AD ENGINE
function render_ad_unit(array $ad, string $variant = 'banner'): bool
{
    $isSidebar = ($variant === 'sidebar');
    $html = '';

    if ($ad['type'] === 'image') {
        $ad_img = $ad['image_path'] ?? '';
        if ($ad_img && strpos($ad_img, 'http') === false) {
            $ad_img = SITE_URL . '/' . ltrim($ad_img, '/');
        }
        $link = htmlspecialchars($ad['link'] ?? '#', ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars(html_entity_decode($ad['title'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES, 'UTF-8');

        if ($ad_img) {
            if ($isSidebar) {
                $html .= '<a href="' . $link . '" target="_blank" rel="noopener sponsored" class="sidebar-ad-link d-block text-decoration-none">';
                $html .= '<div class="sidebar-ad-img-wrap"><img src="' . htmlspecialchars($ad_img) . '" alt="' . $title . '" class="sidebar-ad-img" loading="lazy"></div>';
                if ($title !== '') {
                    $html .= '<div class="sidebar-ad-caption px-3 py-2 border-top border-secondary"><span class="text-white small fw-semibold d-block">' . $title . '</span></div>';
                }
                $html .= '</a>';
            } else {
                $html .= '<a href="' . $link . '" target="_blank" rel="noopener sponsored" style="display:block; margin-top:15px;">';
                $html .= '<img src="' . htmlspecialchars($ad_img) . '" class="img-fluid rounded" alt="' . $title . '">';
                $html .= '</a>';
            }
        }
    } elseif (trim($ad['ad_code'] ?? '') !== '') {
        if ($isSidebar) {
            $html .= '<div class="sidebar-ad-code p-3">' . ($ad['ad_code'] ?? '') . '</div>';
        } else {
            $html .= '<div class="mt-3" style="margin-top:15px; color:#fff;">' . ($ad['ad_code'] ?? '') . '</div>';
        }
    }

    if ($html === '') {
        return false;
    }

    if ($isSidebar) {
        echo '<div class="sidebar-ad-card border border-secondary rounded overflow-hidden bg-dark hover-neon-border" style="transition:0.3s;">';
        echo '<span class="badge bg-secondary sidebar-ad-badge">SPONSORED</span>';
        echo $html;
        echo '</div>';
    } else {
        echo '<div class="ad-container my-5 position-relative p-3 border border-secondary rounded" style="background: rgba(10, 10, 10, 0.85); backdrop-filter: blur(5px); z-index: 5; box-shadow: 0 4px 20px rgba(0,0,0,0.5);">';
        echo '<span class="position-absolute top-0 start-0 badge bg-secondary" style="font-size:10px; opacity:0.8;">SPONSORED</span>';
        echo $html;
        echo '</div>';
    }

    return true;
}

function get_ad($placement, $conn)
{
    $check = $conn->query("SHOW TABLES LIKE 'ads'");
    if (!$check || $check->num_rows === 0) {
        return false;
    }

    $placement = $conn->real_escape_string($placement);
    $sql = "SELECT * FROM ads WHERE placement='$placement' AND (status='active' OR status IS NULL OR status='') ORDER BY RAND() LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        render_ad_unit($res->fetch_assoc(), 'banner');
        return true;
    }
    return false;
}

function get_active_ads(mysqli $conn, array $placements, int $limit, array $excludeIds = []): array
{
    if ($limit <= 0) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($placements), '?'));
    $types = str_repeat('s', count($placements));
    $params = $placements;

    $excludeSql = '';
    if (!empty($excludeIds)) {
        $excludeIds = array_map('intval', $excludeIds);
        $excludeSql = ' AND id NOT IN (' . implode(',', $excludeIds) . ')';
    }

    $sql = "SELECT * FROM ads
            WHERE (status = 'active' OR status IS NULL OR status = '')
            AND placement IN ($placeholders)
            $excludeSql
            ORDER BY id DESC
            LIMIT " . (int)$limit;

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();

    $ads = [];
    while ($row = $res->fetch_assoc()) {
        $ads[] = $row;
    }
    return $ads;
}

function ad_has_displayable_content(array $ad): bool
{
    if (($ad['type'] ?? '') === 'image') {
        return trim($ad['image_path'] ?? '') !== '';
    }
    return trim($ad['ad_code'] ?? '') !== '';
}

function build_sidebar_ad_slots(mysqli $conn, int $target = 20, int $minimum = 15): array
{
    $target = max($minimum, min(25, $target));
    $minimum = max(15, min($minimum, $target));

    $ads = array_values(array_filter(
        get_active_ads($conn, ['sidebar'], $target),
        'ad_has_displayable_content'
    ));
    $usedIds = array_column($ads, 'id');

    if (count($ads) < $target) {
        $more = array_values(array_filter(
            get_active_ads($conn, ['header', 'content'], $target - count($ads), $usedIds),
            'ad_has_displayable_content'
        ));
        foreach ($more as $ad) {
            $ads[] = $ad;
            $usedIds[] = $ad['id'];
        }
    }

    if (count($ads) < $target) {
        $more = array_values(array_filter(
            get_active_ads($conn, ['sidebar', 'header', 'content'], $target - count($ads), $usedIds),
            'ad_has_displayable_content'
        ));
        foreach ($more as $ad) {
            $ads[] = $ad;
            $usedIds[] = $ad['id'];
        }
    }

    if (count($ads) > 0 && count($ads) < $minimum) {
        $pool = $ads;
        $i = 0;
        while (count($ads) < $minimum) {
            $ads[] = $pool[$i % count($pool)];
            $i++;
        }
    }

    return array_slice($ads, 0, $target);
}

function render_sidebar_ads($conn, int $target = 20): int
{
    $check = $conn->query("SHOW TABLES LIKE 'ads'");
    if (!$check || $check->num_rows === 0) {
        return 0;
    }

    $ads = build_sidebar_ad_slots($conn, $target, 15);
    if (empty($ads)) {
        return 0;
    }

    echo '<div class="sidebar-ad-stack">';
    $rendered = 0;
    foreach ($ads as $ad) {
        if (render_ad_unit($ad, 'sidebar')) {
            $rendered++;
        }
    }
    echo '</div>';

    return $rendered;
}

$check_tables = $conn->query("SHOW TABLES LIKE 'ads'");
if($check_tables && $check_tables->num_rows > 0) {
    $check_sidebar = $conn->query("SELECT id FROM ads WHERE (status='active' OR status IS NULL OR status='')");
    $has_sidebar = ($check_sidebar && $check_sidebar->num_rows > 0);
} else {
    $has_sidebar = false;
}
$col_class = $has_sidebar ? "col-lg-8" : "col-lg-10 mx-auto";

// 7. COMMENT LOGIC
$msg = "";
$error_msg = "";
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $c_name = clean_input($_POST['c_name']);
    $c_email = clean_input($_POST['c_email']);
    $c_msg = clean_input($_POST['c_msg']);
    $post_id = $post['id'];
    
    $contains_link = false;
    $patterns = ["http:", "https:", "www.", ".com", "href="];
    foreach ($patterns as $p) { if (stripos($c_msg, $p) !== false) $contains_link = true; }

    if ($contains_link) {
        $error_msg = "Security: Links are prohibited.";
    } elseif (!empty($_POST['website_url'])) {
        // Bot
    } else {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, name, email, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $post_id, $c_name, $c_email, $c_msg);
        if($stmt->execute()) $msg = "Comment submitted.";
        else $error_msg = "System error.";
    }
}
?>

<canvas id="nectra-canvas"></canvas>

<style>
    html, body { margin: 0; padding: 0; width: 100%; min-height: 100vh; background-color: #050505 !important; color: #e0e0e0 !important; overflow-x: hidden; }
    #nectra-canvas { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 0; pointer-events: none; }
    .post-hero-header { position: relative; z-index: 2; padding-top: 100px; padding-bottom: 50px; background: transparent; border-bottom: 1px solid rgba(255,255,255,0.1); margin-top: 0; }
    .post-hero-header .blog-post-title {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: clamp(1.85rem, 3.2vw, 2.65rem);
        font-weight: 700;
        line-height: 1.28;
        letter-spacing: -0.025em;
        max-width: 820px;
        margin: 0 auto 1.25rem;
        text-shadow: none;
        color: #fff !important;
        text-wrap: balance;
    }
    .post-hero-header .blog-post-category {
        font-family: 'Inter', sans-serif;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.08em;
    }
    .post-hero-header .blog-post-meta {
        font-family: 'Inter', sans-serif;
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.65);
    }
    .post-hero-header .blog-post-meta i { color: #00e5ff; opacity: 0.9; }
    main { position: relative; z-index: 2; }
    .blog-content { font-family: 'Inter', sans-serif; font-size: 1.15rem; line-height: 1.8; color: #d4d4d4; }
    h1, h2, h3, h4, h5 { color: #fff !important; font-weight: 700; margin-top: 2rem; }
    .text-neon { color: #00f2ff !important; text-shadow: 0 0 10px rgba(0,242,255,0.5); }
    img.img-fluid { border: 1px solid #333; border-radius: 8px; background: #000; max-width: 100%; height: auto; }
    a { text-decoration: none; color: #00f2ff; }
    a:hover { color: #fff; text-shadow: 0 0 8px #00f2ff; }
    .card-img-top-wrapper { height: 200px; overflow: hidden; position: relative; }
    .card-img-top-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
    .hover-neon-border:hover img { transform: scale(1.05); }
    .recent-intel-section { width: 100%; background: rgba(0, 0, 0, 0.35); }
    .recent-intel-section .card-img-top-wrapper {
        height: auto;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        position: relative;
        background: #0a0a0a;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .recent-intel-section .card-img-top-wrapper img {
        width: 100%;
        height: 100%;
        max-height: none;
        object-fit: contain;
        object-position: center center;
        border: none !important;
        border-radius: 0 !important;
        background: #0a0a0a;
    }
    .recent-intel-section .hover-neon-border:hover img {
        transform: scale(1.02);
    }
    .sidebar-ad-stack { display: flex; flex-direction: column; gap: 1.25rem; width: 100%; }
    .sidebar-ad-column { width: 100%; height: auto; overflow: visible; }
    .sidebar-ad-card { position: relative; background: rgba(12, 12, 12, 0.95) !important; }
    .sidebar-ad-badge {
        position: absolute; top: 8px; left: 8px; z-index: 2;
        font-size: 10px; opacity: 0.9; letter-spacing: 0.05em;
    }
    .sidebar-ad-img-wrap {
        aspect-ratio: 4 / 3;
        background: #0a0a0a;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-top: 0;
    }
    .sidebar-ad-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        display: block;
        border: none !important;
        border-radius: 0 !important;
        background: #0a0a0a;
    }
    .sidebar-ad-caption { background: rgba(20, 20, 20, 0.9); }
    .sidebar-ad-code { color: #fff; font-size: 0.875rem; overflow: hidden; }
    .sidebar-ad-card.hover-neon-border:hover { border-color: #00f2ff !important; box-shadow: 0 0 15px rgba(0, 242, 255, 0.15); }
</style>

<main>
    <header class="post-hero-header text-center">
        <div class="container position-relative">
            <span class="badge border border-secondary text-white-50 mb-4 blog-post-category text-uppercase px-3 py-2">
                <?php echo htmlspecialchars($decoded_category, ENT_QUOTES, 'UTF-8'); ?>
            </span>
            <h1 class="blog-post-title">
                <?php echo htmlspecialchars($decoded_title, ENT_QUOTES, 'UTF-8'); ?>
            </h1>
            
            <div class="blog-post-meta d-flex justify-content-center gap-4 flex-wrap">
                <span><i class="far fa-user me-2 text-neon"></i> <?php echo FOUNDER_NAME; ?></span>
                <span><i class="far fa-calendar me-2 text-neon"></i> <?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                <span><i class="far fa-clock me-2 text-neon"></i> <?php echo max(1, round(str_word_count(strip_tags($post['content'])) / 200)); ?> min read</span>
            </div>

        </div>
        <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 1px; background: radial-gradient(circle, #00f2ff 0%, transparent 100%); opacity: 0.5;"></div>
    </header>

    <article class="py-5">
        <div class="container">
            <div class="row justify-content-center"><div class="col-12"><?php get_ad('header', $conn); ?></div></div>

            <div class="row justify-content-center mt-4 align-items-start">
                <div class="<?php echo $col_class; ?>">
                    
                    <?php if(!empty($display_img)): ?>
                    <div class="mb-5 rounded overflow-hidden border border-secondary shadow-lg position-relative" style="z-index: 3;">
                        <img src="<?php echo $display_img; ?>" alt="<?php echo $decoded_title; ?>" class="img-fluid w-100 d-block">
                    </div>
                    <?php endif; ?>

                    <?php 
                        $content_plain = strip_tags($post['content']);
                        render_geo_blocks($decoded_title, $content_plain);
                    ?>

                    <div class="blog-content">
                        <?php 
                            $content = $post['content'];
                            
                            // 8. ENCODING REPAIR PATCH (Now with Emojis Restored)
                            $bad_chars = [
                                'â€™' => "'",  
                                'â€œ' => '"',  
                                'â€'  => '"',  
                                'â€“' => '-',  
                                'â€”' => '--', 
                                'â€˜' => "'",
                                'ðŸŸ¢' => '🟢', 
                                'ðŸ‘‰' => '👉', 
                                'ðŸ“ž' => '📞', 
                                'ðŸš€' => '🚀'
                            ];
                            $content = str_replace(array_keys($bad_chars), array_values($bad_chars), $content);
                            
                            ob_start(); get_ad('content', $conn); $ad_html = ob_get_clean();
                            
                            if($ad_html) {
                                $paragraphs = explode('</p>', $content);
                                if(count($paragraphs) > 2) {
                                    array_splice($paragraphs, 2, 0, $ad_html);
                                    echo implode('</p>', $paragraphs);
                                } else { echo $content . $ad_html; }
                            } else { echo $content; }
                        ?>
                    </div>
                    
                    <?php 
                        render_geo_summary($decoded_title);
                        render_author_bio();
                        
                        $post_faqs = [
                            ['q' => 'Who wrote this article?', 'a' => 'This article was authored and verified by ' . FOUNDER_NAME . ', ' . FOUNDER_TITLE . ' at Nectra Digital, with ' . FOUNDER_EXPERIENCE . ' of industry experience.'],
                            ['q' => 'Is this information up to date?', 'a' => 'We review and update our content regularly to reflect current SEO best practices, algorithm changes, and industry standards. Published: ' . date('F j, Y', strtotime($post['created_at'])) . '.'],
                            ['q' => 'Can Nectra Digital help implement these strategies?', 'a' => 'Yes. Nectra Digital offers full-service SEO, digital marketing, AI automation, and web development. Book a free consultation at nectradigital.com/contact.']
                        ];
                        render_faq_section($post_faqs, 'Article FAQ');
                        
                        render_post_internal_links($conn, $post, $decoded_category);
                    ?>

                    <div class="mt-4 pt-4 border-top border-secondary d-flex justify-content-between flex-wrap gap-2">
                        <a href="/insights" class="btn btn-outline-secondary text-white btn-sm px-4">
                            <i class="fas fa-arrow-left me-2"></i> BACK TO INTEL
                        </a>
                        <a href="/contact" class="btn btn-nectra btn-sm">Get Free SEO Audit</a>
                    </div>

                    <div class="mt-5 pt-5">
                        <h3 class="h4 mb-4 border-start border-4 border-info ps-3 text-white">Discussions</h3>
                        <?php if($msg) echo "<div class='alert alert-success border-success bg-dark text-success mb-4'>$msg</div>"; ?>
                        <?php if($error_msg) echo "<div class='alert alert-danger border-danger bg-dark text-danger mb-4'>$error_msg</div>"; ?>

                        <div class="p-4 border border-secondary rounded mb-5" style="background: rgba(20,20,20,0.4);">
                            <form method="POST">
                                <input type="text" name="website_url" style="display:none;" autocomplete="off">
                                <div class="row g-3">
                                    <div class="col-md-6"><input type="text" name="c_name" class="form-control bg-dark text-white border-secondary" placeholder="Name" required></div>
                                    <div class="col-md-6"><input type="email" name="c_email" class="form-control bg-dark text-white border-secondary" placeholder="Email (Hidden)" required></div>
                                    <div class="col-12"><textarea name="c_msg" class="form-control bg-dark text-white border-secondary" rows="3" placeholder="Comment..." required></textarea></div>
                                    <div class="col-12"><button type="submit" name="submit_comment" class="btn btn-outline-info w-100">POST</button></div>
                                </div>
                            </form>
                        </div>

                        <?php
                        $c_sql = "SELECT * FROM comments WHERE post_id={$post['id']} AND status='approved' ORDER BY created_at DESC";
                        $c_res = $conn->query($c_sql);
                        if($c_res->num_rows > 0) {
                            while($com = $c_res->fetch_assoc()) {
                                echo '<div class="d-flex mb-4 border-bottom border-secondary pb-3">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-dark fw-bold border border-info" 
                                             style="width:40px; height:40px; background: #00f2ff;">'.substr($com['name'], 0, 1).'</div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-white mb-1">'.$com['name'].'</h6>
                                        <p class="text-white-50 small mb-0">'.$com['comment'].'</p>
                                    </div>
                                </div>';
                            }
                        } else {
                            echo "<p class='text-white-50 small'>No discussions yet.</p>";
                        }
                        ?>
                    </div>
                </div>

                <?php if($has_sidebar): ?>
                <div class="col-lg-4 mt-5 mt-lg-0 ps-lg-5">
                    <aside class="sidebar-ad-column" aria-label="Sponsored content">
                        <h6 class="text-white-50 text-uppercase small mb-3 border-bottom border-secondary pb-2 text-center">Sponsored Intel</h6>
                        <?php render_sidebar_ads($conn, 20); ?>
                    </aside>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <section class="recent-intel-section mt-5 py-5 border-top border-secondary">
            <div class="container-fluid px-3 px-lg-4 px-xl-5">
                <h3 class="h4 mb-4 border-start border-4 border-info ps-3 text-white">Recent Intel</h3>
                <div class="row g-4">
                    <?php
                    $pid = $post['id'];
                    $sql = "SELECT * FROM blog_posts WHERE id != ? ORDER BY created_at DESC LIMIT 9";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $pid);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $rel_title = $row['title'];
                            for ($d = 0; $d < 4; $d++) {
                                $rel_title = html_entity_decode($rel_title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            }
                            $rel_title = htmlspecialchars($rel_title, ENT_QUOTES, 'UTF-8');
                            $img = $row['image'];
                            if (strpos($img, 'http') === false) {
                                $img = SITE_URL . '/' . ltrim($img, '/');
                            }
                            if (empty($img)) {
                                $img = SITE_URL . '/assets/images/logo.png';
                            }
                            echo '
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 border-secondary bg-transparent overflow-hidden hover-neon-border" style="transition:0.3s;">
                                    <div class="card-img-top-wrapper">
                                        <img src="' . htmlspecialchars($img) . '" class="recent-intel-img" alt="' . $rel_title . '">
                                    </div>
                                    <div class="card-body d-flex flex-column justify-content-center" style="background: rgba(20,20,20,0.6);">
                                        <h5 class="card-title text-white mb-0 h6" style="line-height: 1.5; font-weight: 600;">
                                            <a href="/' . htmlspecialchars($row['slug']) . '" class="text-white text-decoration-none stretched-link">' . $rel_title . '</a>
                                        </h5>
                                    </div>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<p class="text-white-50 small col-12">System Update: Additional intel not yet declassified.</p>';
                    }
                    ?>
                </div>
            </div>
        </section>
    </article>
</main>

<script>
const canvas = document.getElementById('nectra-canvas');
const ctx = canvas.getContext('2d');
let particles = [];

function resize() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}
window.addEventListener('resize', resize);
resize();

class Particle {
    constructor() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.vx = (Math.random() - 0.5) * 0.5;
        this.vy = (Math.random() - 0.5) * 0.5;
        this.size = Math.random() * 2;
    }
    update() {
        this.x += this.vx;
        this.y += this.vy;
        if(this.x < 0 || this.x > canvas.width) this.vx *= -1;
        if(this.y < 0 || this.y > canvas.height) this.vy *= -1;
    }
    draw() {
        ctx.fillStyle = 'rgba(0, 242, 255, 0.5)';
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
    }
}

for(let i=0; i<100; i++) particles.push(new Particle());

function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    for(let i=0; i<particles.length; i++) {
        particles[i].update();
        particles[i].draw();
        for(let j=i; j<particles.length; j++) {
            const dx = particles[i].x - particles[j].x;
            const dy = particles[i].y - particles[j].y;
            const distance = Math.sqrt(dx*dx + dy*dy);
            if(distance < 100) {
                ctx.strokeStyle = `rgba(0, 242, 255, ${0.1 - distance/1000})`;
                ctx.lineWidth = 0.5;
                ctx.beginPath();
                ctx.moveTo(particles[i].x, particles[i].y);
                ctx.lineTo(particles[j].x, particles[j].y);
                ctx.stroke();
            }
        }
    }
    requestAnimationFrame(animate);
}
animate();
</script>

<?php include 'includes/footer.php'; ?>