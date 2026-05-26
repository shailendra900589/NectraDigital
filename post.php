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
    require_once 'includes/config.php';
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
// Fully decode double/triple-encoded HTML entities
$decoded_title = $post['title'];
while ($decoded_title !== htmlspecialchars_decode($decoded_title, ENT_QUOTES)) {
    $decoded_title = htmlspecialchars_decode($decoded_title, ENT_QUOTES);
}
$decoded_category = $post['category'];
while ($decoded_category !== htmlspecialchars_decode($decoded_category, ENT_QUOTES)) {
    $decoded_category = htmlspecialchars_decode($decoded_category, ENT_QUOTES);
}

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

// 5. INJECT HEADER & BASE TAG
include 'includes/header.php'; 
$header_html = ob_get_clean();
$base_tag = '<base href="' . SITE_URL . '/">';
$meta_charset = '<meta charset="UTF-8">'; 

// Article JSON-LD for Google Discover & Rich Results
$article_json = [
    "@context" => "https://schema.org",
    "@type" => "Article",
    "headline" => $decoded_title,
    "description" => $page_desc,
    "image" => !empty($display_img) ? $display_img : SITE_URL . '/assets/images/logo.png',
    "author" => [
        "@type" => "Organization",
        "name" => "Nectra Digital",
        "url" => SITE_URL
    ],
    "publisher" => [
        "@type" => "Organization",
        "name" => "Nectra Digital",
        "logo" => [
            "@type" => "ImageObject",
            "url" => SITE_URL . "/assets/images/logo.png"
        ]
    ],
    "datePublished" => date('c', strtotime($post['created_at'])),
    "dateModified" => date('c', strtotime($post['created_at'])),
    "mainEntityOfPage" => [
        "@type" => "WebPage",
        "@id" => SITE_URL . '/' . $post['slug']
    ],
    "articleSection" => $decoded_category,
    "keywords" => $page_keys
];
$article_ld = '<script type="application/ld+json">' . json_encode($article_json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

$header_html = str_replace('<head>', "<head>\n" . $meta_charset . "\n" . $base_tag, $header_html);
$header_html = str_replace('</head>', $article_ld . "\n</head>", $header_html);
echo $header_html;

// 6. AD ENGINE
function get_ad($placement, $conn) {
    $check = $conn->query("SHOW TABLES LIKE 'ads'");
    if($check && $check->num_rows > 0) {
        $stmt = $conn->prepare("SELECT * FROM ads WHERE placement=? AND status='active' ORDER BY RAND() LIMIT 1");
        $stmt->bind_param("s", $placement);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res && $res->num_rows > 0) {
            $ad = $res->fetch_assoc();
            echo '<div class="ad-container my-5 position-relative p-3 border border-secondary rounded" 
                       style="background: rgba(10, 10, 10, 0.85); backdrop-filter: blur(5px); z-index: 5; box-shadow: 0 4px 20px rgba(0,0,0,0.5);">';
            echo '<span class="position-absolute top-0 start-0 badge bg-secondary" style="font-size:10px; opacity:0.8;">SPONSORED</span>';
            
            if($ad['type'] == 'image' && !empty($ad['image_path'])) {
                $ad_img = $ad['image_path'];
                if(strpos($ad_img, 'http') === false) $ad_img = SITE_URL . '/' . ltrim($ad_img, '/');
                echo '<a href="'.htmlspecialchars($ad['link']).'" target="_blank" rel="noopener" style="display:block; margin-top:15px;">
                        <img src="'.htmlspecialchars($ad_img).'" class="img-fluid rounded" alt="'.htmlspecialchars($ad['title']).'">
                      </a>';
            } elseif($ad['type'] == 'code' && !empty($ad['ad_code'])) {
                echo '<div class="ad-code-wrap" style="margin-top:15px;">' . $ad['ad_code'] . '</div>'; 
            }
            echo '</div>';
            return true;
        }
    }
    return false;
}

$check_tables = $conn->query("SHOW TABLES LIKE 'ads'");
if($check_tables && $check_tables->num_rows > 0) {
    $check_sidebar = $conn->query("SELECT id FROM ads WHERE placement='sidebar' AND status='active'");
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


<main>
    <header class="post-hero-header text-center">
        <div class="container position-relative">
            <span class="badge border border-neon text-neon mb-4 text-uppercase tracking-widest px-3 py-2">
                <?php echo $decoded_category; ?>
            </span>
            <h1 class="display-4 fw-bold text-white mb-4 mx-auto" style="max-width: 900px; text-shadow: 0 0 20px rgba(0,0,0,0.8);">
                <?php echo $decoded_title; ?>
            </h1>
            
            <div class="text-white-50 small d-flex justify-content-center gap-4">
                <span><i class="far fa-user me-2 text-neon"></i> Nectra Intel</span>
            </div>

        </div>
        <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 1px; background: radial-gradient(circle, #00f2ff 0%, transparent 100%); opacity: 0.5;"></div>
    </header>

    <article class="py-5">
        <div class="container">
            <div class="row justify-content-center"><div class="col-12"><?php get_ad('header', $conn); ?></div></div>

            <div class="row justify-content-center mt-4">
                <div class="<?php echo $col_class; ?>">
                    
                    <?php if(!empty($display_img)): ?>
                    <div class="mb-5 rounded overflow-hidden border border-secondary shadow-lg position-relative" style="z-index: 3;">
                        <img src="<?php echo $display_img; ?>" alt="<?php echo $decoded_title; ?>" class="img-fluid w-100 d-block">
                    </div>
                    <?php endif; ?>

                    <div class="blog-content">
                        <?php 
                            $content = $post['content'];
                            
                            // 8. ENCODING REPAIR (Mojibake fix via hex byte sequences)
                            $mojibake = [
                                "\xC3\xA2\xE2\x82\xAC\xE2\x84\xA2" => "\xE2\x80\x99",
                                "\xC3\xA2\xE2\x82\xAC\xC5\x93"     => "\xE2\x80\x9C",
                                "\xC3\xA2\xE2\x82\xAC\xC2\x9D"     => "\xE2\x80\x9D",
                                "\xC3\xA2\xE2\x82\xAC\xE2\x80\x9C" => "\xE2\x80\x94",
                                "\xC3\xA2\xE2\x82\xAC\xE2\x80\x9D" => "\xE2\x80\x93",
                                "\xC3\xA2\xE2\x82\xAC\xCB\x9C"     => "\xE2\x80\x98",
                                "\xC3\xA2\xE2\x82\xAC\xC2\xA6"     => "\xE2\x80\xA6",
                                "\xC3\x82\xC2\xA0"                   => " ",
                            ];
                            $content = str_replace(array_keys($mojibake), array_values($mojibake), $content);
                            
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
                    
                    <div class="mt-5 pt-5 border-top border-secondary d-flex justify-content-between">
                        <a href="<?php echo SITE_URL; ?>/insights" class="btn btn-outline-secondary text-white btn-sm px-4">
                            <i class="fas fa-arrow-left me-2"></i> BACK TO INTEL
                        </a>
                    </div>
                    
                    <div class="mt-5 pt-5">
                        <h3 class="h4 mb-4 border-start border-4 border-info ps-3 text-white">Recent Intel</h3>
                        <div class="row g-4">
                            <?php
                            $pid = $post['id'];
                            
                            // NEW: Fetches the 8 most recent posts, entirely ignoring categories
                            $sql = "SELECT * FROM blog_posts WHERE id != ? ORDER BY created_at DESC LIMIT 8";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $pid);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $rel_title = $row['title'];
                                    while ($rel_title !== htmlspecialchars_decode($rel_title, ENT_QUOTES)) {
                                        $rel_title = htmlspecialchars_decode($rel_title, ENT_QUOTES);
                                    }
                                    $img = $row['image'];
                                    if(!empty($img) && strpos($img, 'http') === false) $img = SITE_URL . '/' . ltrim($img, '/');
                                    if(empty($img)) $img = SITE_URL . '/assets/images/logo.png';

                                    echo '
                                    <div class="col-md-6">
                                        <div class="card h-100 border-secondary bg-transparent overflow-hidden hover-neon-border" style="transition:0.3s;">
                                            <div class="card-img-top-wrapper">
                                                <img src="'.$img.'" class="w-100 h-100" alt="'.htmlspecialchars($rel_title).'">
                                            </div>
                                            
                                            <div class="card-body d-flex flex-column justify-content-center" style="background: rgba(20,20,20,0.6);">
                                                <h5 class="card-title text-white mb-0 h6" style="line-height: 1.5; font-weight: 600;">
                                                    <a href="'.SITE_URL.'/'.$row['slug'].'" class="text-white text-decoration-none stretched-link">'.$rel_title.'</a>
                                                </h5>
                                            </div>
                                            
                                        </div>
                                    </div>';
                                }
                            } else {
                                echo '<p class="text-white-50 small col-12">No related posts found.</p>';
                            }
                            ?>
                        </div>
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
                    <div class="sticky-top" style="top: 120px;">
                        <div class="mb-4 text-center">
                            <h6 class="text-white-50 text-uppercase small mb-3 border-bottom border-secondary pb-2">Sponsored Intel</h6>
                            <?php get_ad('sidebar', $conn); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </article>
</main>


<?php include 'includes/footer.php'; ?>