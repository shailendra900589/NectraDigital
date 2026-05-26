<?php 
require_once 'includes/db.php'; 
$page_title = "Digital Marketing Blog | SEO Tips, Web Development & AI Insights 2026";
$page_desc = "Read high-value insights on SEO ranking strategies, digital marketing trends 2026, custom web development, AI automation, and software solutions. Expert articles from Nectra Digital, Lucknow's top tech agency.";
$page_keys = "digital marketing blog, SEO tips 2026, web development insights, best digital marketing strategies, how to rank on Google, AI automation blog, custom software development guide, Lucknow tech blog, website development tips, SEO for startups, digital marketing for small business, web development trends";
include 'includes/header.php'; 

// ==========================================
// PAGINATION ENGINE
// ==========================================
$posts_per_page = 9;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$offset = ($current_page - 1) * $posts_per_page;

$total_sql = "SELECT COUNT(*) as total FROM blog_posts";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $posts_per_page);

$sql = "SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT $posts_per_page OFFSET $offset";
$result = $conn->query($sql);

// JSON-LD ItemList for SEO
$items_json = [];
$item_pos = 1;
$result_copy = $conn->query($sql);
while($item = $result_copy->fetch_assoc()) {
    $item_title = html_entity_decode($item['title'], ENT_QUOTES, 'UTF-8');
    $items_json[] = [
        "@type" => "ListItem",
        "position" => $item_pos++,
        "url" => SITE_URL . '/' . $item['slug'],
        "name" => $item_title
    ];
}
?>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Blog",
    "name": "Nectra Digital Insights",
    "description": "Expert blog covering SEO, digital marketing, web development, AI automation, and tech trends for businesses in India and globally.",
    "url": "<?php echo SITE_URL; ?>/insights",
    "publisher": {
        "@type": "Organization",
        "name": "Nectra Digital",
        "logo": {
            "@type": "ImageObject",
            "url": "<?php echo SITE_URL; ?>/assets/images/logo.png"
        }
    },
    "blogPost": <?php echo json_encode($items_json, JSON_UNESCAPED_SLASHES); ?>
}
</script>

<meta name="robots" content="max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<main>
    <header class="py-5 text-center position-relative" style="min-height: 40vh; display: flex; align-items: center; background: linear-gradient(to bottom, rgba(5,5,5,0.7) 0%, rgba(10,21,24,0.8) 100%);">
        <div class="container py-5 position-relative z-1">
            <div class="d-inline-block border border-neon rounded-pill px-3 py-1 mb-4 bg-dark">
                <small class="text-neon text-uppercase" style="letter-spacing: 2px;"><i class="fas fa-newspaper me-2"></i> Expert Knowledge Base</small>
            </div>
            <h1 class="display-4 fw-bold text-white mb-4">Digital Marketing &amp; <span class="text-neon">Web Development</span> Insights</h1>
            <p class="lead text-white-50 mx-auto" style="max-width: 750px;">Actionable guides on SEO ranking strategies, digital marketing trends, custom software development, and AI automation — helping businesses grow online in 2026 and beyond.</p>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            
            <div class="text-center mb-5">
                <h2 class="h4 text-white mb-2">Latest Articles on SEO, Marketing &amp; Development</h2>
                <p class="text-white-50 small">Practical tips and strategies from our team of developers, SEO experts, and digital marketers</p>
            </div>

            <div class="row g-4">
                
                <?php 
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $clean_title = $row['title'];
                        while ($clean_title !== htmlspecialchars_decode($clean_title, ENT_QUOTES)) {
                            $clean_title = htmlspecialchars_decode($clean_title, ENT_QUOTES);
                        }
                        $clean_category = $row['category'];
                        while ($clean_category !== htmlspecialchars_decode($clean_category, ENT_QUOTES)) {
                            $clean_category = htmlspecialchars_decode($clean_category, ENT_QUOTES);
                        }
                        
                        $raw_content = $row['content'];
                        $raw_content = preg_replace('/<\/(p|h[1-6]|div|li|br|blockquote)>/i', ' ', $raw_content);
                        $raw_content = strip_tags($raw_content);
                        $raw_content = preg_replace('/\s+/', ' ', trim($raw_content));
                        while ($raw_content !== htmlspecialchars_decode($raw_content, ENT_QUOTES)) {
                            $raw_content = htmlspecialchars_decode($raw_content, ENT_QUOTES);
                        }
                        $mojibake = [
                            "\xC3\xA2\xE2\x82\xAC\xE2\x84\xA2" => "'",
                            "\xC3\xA2\xE2\x82\xAC\xC5\x93"     => '"',
                            "\xC3\xA2\xE2\x82\xAC\xC2\x9D"     => '"',
                            "\xC3\xA2\xE2\x82\xAC\xE2\x80\x9C" => '—',
                            "\xC3\xA2\xE2\x82\xAC\xE2\x80\x9D" => '–',
                            "\xC3\xA2\xE2\x82\xAC\xCB\x9C"     => "'",
                            "\xC3\xA2\xE2\x82\xAC\xC2\xA6"     => '...',
                            "\xC3\x82\xC2\xA0"                   => ' ',
                        ];
                        $raw_content = str_replace(array_keys($mojibake), array_values($mojibake), $raw_content);
                        $excerpt = mb_substr($raw_content, 0, 150) . "...";
                        
                        // Image
                        $thumb_html = '';
                        if(!empty($row['image']) && file_exists($row['image'])) {
                            $thumb_html = '<div class="blog-card-img rounded-top overflow-hidden border-bottom border-secondary">
                                <img src="'.$row['image'].'" class="w-100" style="height:200px; object-fit:cover;" alt="'.htmlspecialchars($clean_title).'">
                            </div>';
                        } else {
                            $thumb_html = '<div class="blog-card-img rounded-top overflow-hidden border-bottom border-secondary d-flex align-items-center justify-content-center" style="height:200px; background:rgba(0,229,255,0.03);">
                                <i class="fas fa-bolt text-neon fa-2x"></i>
                            </div>';
                        }

                        $post_link = $row['slug'];
                        $date_str = date('M d, Y', strtotime($row['created_at']));

                        echo '
                        <div class="col-md-6 col-lg-4">
                            <article class="card h-100 bg-glass border border-secondary service-card p-0 overflow-hidden">
                                '.$thumb_html.'
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge border border-secondary text-white-50 small">'.$clean_category.'</span>
                                        <span class="text-white-50 x-small">'.$date_str.'</span>
                                    </div>

                                    <h3 class="h6 text-white mb-3" style="line-height:1.5;">
                                        <a href="'.$post_link.'" class="text-white text-decoration-none stretched-link hover-neon">
                                            '.$clean_title.'
                                        </a>
                                    </h3>
                                    <p class="text-white-50 small mb-3" style="line-height: 1.6; flex-grow:1;">
                                        '.$excerpt.'
                                    </p>
                                    
                                    <div class="d-flex align-items-center border-top border-secondary pt-3 mt-auto">
                                        <small class="text-white-50"><i class="far fa-clock me-1"></i> '.ceil(str_word_count($raw_content)/200).' min read</small>
                                        <span class="ms-auto text-neon small fw-bold">READ <i class="fas fa-arrow-right ms-1"></i></span>
                                    </div>
                                </div>
                            </article>
                        </div>';
                    }
                } else {
                    echo "<div class='col-12 text-center py-5'><p class='text-white-50'>No blog posts found yet.</p></div>";
                }
                ?>

            </div>

            <?php 
            if ($total_pages > 1) {
                echo '<div class="row mt-5 pt-4">';
                echo '<div class="col-12 d-flex justify-content-center">';
                echo '<nav aria-label="Blog Pagination">';
                echo '<ul class="pagination pagination-neon mb-0">';
                
                if ($current_page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page='.($current_page - 1).'"><i class="fas fa-chevron-left me-1"></i> Prev</a></li>';
                } else {
                    echo '<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left me-1"></i> Prev</span></li>';
                }

                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        echo '<li class="page-item active" aria-current="page"><span class="page-link">'.$i.'</span></li>';
                    } else {
                        echo '<li class="page-item"><a class="page-link" href="?page='.$i.'">'.$i.'</a></li>';
                    }
                }

                if ($current_page < $total_pages) {
                    echo '<li class="page-item"><a class="page-link" href="?page='.($current_page + 1).'">Next <i class="fas fa-chevron-right ms-1"></i></a></li>';
                } else {
                    echo '<li class="page-item disabled"><span class="page-link">Next <i class="fas fa-chevron-right ms-1"></i></span></li>';
                }

                echo '</ul>';
                echo '</nav>';
                echo '</div>';
                echo '</div>';
            }
            ?>

        </div>
    </section>

    <section class="py-5" style="background: rgba(0,229,255,0.02); border-top: 1px solid rgba(0,229,255,0.1);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="h4 text-white mb-3">Looking for Expert Digital Solutions?</h2>
                    <p class="text-white-50 mb-4">From custom web development and SEO optimization to AI-powered automation — our team delivers results-driven solutions for businesses across India.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="<?php echo SITE_URL; ?>/services" class="btn btn-outline-info px-4">Our Services</a>
                        <a href="<?php echo SITE_URL; ?>/contact" class="btn btn-nectra px-4">Get Free Consultation</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


<?php include 'includes/footer.php'; ?>
