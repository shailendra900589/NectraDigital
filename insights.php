<?php 
require_once 'includes/db.php';
require_once 'includes/blog_orphan.php';
require_once 'includes/text-utils.php';
require_once 'includes/text-utils.php';
blog_orphan_ensure_schema($conn);
$page_title = "Digital Intelligence & Insights";
$page_desc = "Nectra Digital Insights. Deep dives into AI, Tech, and SEO protocols.";
include 'includes/header.php'; 

// ==========================================
// PAGINATION ENGINE
// ==========================================
$posts_per_page = 8; // Number of posts to show per page
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$offset = ($current_page - 1) * $posts_per_page;

// Count total posts (listed only)
$listWhere = blog_listable_sql();
$total_sql = "SELECT COUNT(*) as total FROM blog_posts WHERE {$listWhere}";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $posts_per_page);

// FETCH POSTS — exclude orphan (direct-link-only) posts
$sql = "SELECT * FROM blog_posts WHERE {$listWhere} ORDER BY created_at DESC LIMIT $posts_per_page OFFSET $offset";
$result = $conn->query($sql);
?>

<main>
    <header class="py-5 text-center position-relative" style="background: linear-gradient(to bottom, #050505 0%, #0a1518 100%);">
        <div class="container py-5 position-relative z-1">
            <h6 class="text-neon text-uppercase mb-3" style="letter-spacing: 3px;">Knowledge Base</h6>
            <h1 class="display-4 fw-bold text-white mb-4">DIGITAL <span class="text-neon">INTELLIGENCE</span></h1>
        </div>
        <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(#00e5ff 1px, transparent 1px); background-size: 40px 40px; opacity: 0.05; pointer-events: none;"></div>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                
                <?php 
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $excerpt = nectra_fix_mojibake(strip_tags($row['content'] ?? ''));
                        $excerpt = mb_substr($excerpt, 0, 120) . '...';
                        
                        // Image Handling: Show Uploaded Image or Default Icon
                        $thumb_html = '';
                        if(!empty($row['image']) && file_exists($row['image'])) {
                            $thumb_html = '<div class="mb-4 rounded overflow-hidden border border-secondary" style="height: 200px;">
                                <img src="'.$row['image'].'" class="w-100 h-100 object-fit-cover" alt="'.nectra_display_text($row['title']).'">
                            </div>';
                        } else {
                            $thumb_html = '<div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="bg-dark border border-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-bolt text-neon"></i>
                                </div>
                                <span class="text-white-50 x-small fw-bold text-uppercase" style="letter-spacing: 1px;">'.nectra_display_text($row['category']).'</span>
                            </div>';
                        }

                        // ==========================================
                        // FIX: Added 'post/' so the link matches .htaccess
                        // ==========================================
                        $post_title = nectra_display_text($row['title']);
                        $post_category = nectra_display_text($row['category']);
                        $post_link = $row['slug'];

                        echo '
                        <div class="col-md-6">
                            <article class="card h-100 bg-glass border border-secondary service-card p-0 overflow-hidden">
                                <div class="card-body p-4 d-flex flex-column">
                                    
                                    '.$thumb_html.'
                                    
                                    '.((!empty($row['image'])) ? '<div class="mb-3"><span class="badge border border-secondary text-white-50">'.$post_category.'</span></div>' : '').'

                                    <h3 class="h4 text-white mb-3">
                                        <a href="'.$post_link.'" class="text-white text-decoration-none stretched-link hover-neon">
                                            '.$post_title.'
                                        </a>
                                    </h3>
                                    <p class="text-white-50 small mb-4">
                                        '.$excerpt.'
                                    </p>
                                    
                                    <div class="d-flex align-items-center justify-content-between border-top border-secondary pt-3 mt-auto flex-wrap gap-2">
                                        <span class="text-white-50 x-small"><i class="far fa-user me-1"></i> <?php echo FOUNDER_NAME; ?> · <i class="far fa-calendar ms-2 me-1"></i> <?php echo date('M j, Y', strtotime($row['created_at'])); ?></span>
                                        <span class="text-neon x-small fw-bold">Read article <i class="fas fa-chevron-right ms-1"></i></span>
                                    </div>
                                </div>
                            </article>
                        </div>';
                    }
                } else {
                    echo "<div class='col-12 text-center py-5'><p class='text-white-50'>No intelligence reports found in the mainframe.</p></div>";
                }
                ?>

            </div>

            <?php 
            // ==========================================
            // DYNAMIC NEON PAGINATION BUTTONS
            // ==========================================
            if ($total_pages > 1) {
                echo '<div class="row mt-5 pt-4">';
                echo '<div class="col-12 d-flex justify-content-center">';
                echo '<nav aria-label="Intelligence Pagination">';
                echo '<ul class="pagination pagination-neon mb-0">';
                
                // PREV Button
                if ($current_page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page='.($current_page - 1).'"><i class="fas fa-chevron-left me-1"></i> PREV</a></li>';
                } else {
                    echo '<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left me-1"></i> PREV</span></li>';
                }

                // Page Numbers
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        echo '<li class="page-item active" aria-current="page"><span class="page-link">'.$i.'</span></li>';
                    } else {
                        echo '<li class="page-item"><a class="page-link" href="?page='.$i.'">'.$i.'</a></li>';
                    }
                }

                // NEXT Button
                if ($current_page < $total_pages) {
                    echo '<li class="page-item"><a class="page-link" href="?page='.($current_page + 1).'">NEXT <i class="fas fa-chevron-right ms-1"></i></a></li>';
                } else {
                    echo '<li class="page-item disabled"><span class="page-link">NEXT <i class="fas fa-chevron-right ms-1"></i></span></li>';
                }

                echo '</ul>';
                echo '</nav>';
                echo '</div>';
                echo '</div>';
            }
            ?>

        </div>
    </section>
</main>

<style>
/* Utilities */
.object-fit-cover { object-fit: cover; }
.hover-neon:hover { color: var(--nectra-neon) !important; text-shadow: 0 0 10px rgba(0,229,255,0.4); transition: 0.3s; }

/* Custom Neon Pagination Styling */
.pagination-neon {
    gap: 8px;
}
.pagination-neon .page-link {
    background-color: transparent;
    border: 1px solid #333;
    color: #a0a0a0;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 1px;
    padding: 10px 18px;
    border-radius: 4px;
    transition: all 0.3s ease;
}
.pagination-neon .page-link:hover {
    background-color: rgba(0, 229, 255, 0.05);
    border-color: #00e5ff;
    color: #00e5ff;
    box-shadow: 0 0 15px rgba(0, 229, 255, 0.15);
}
.pagination-neon .page-item.active .page-link {
    background-color: rgba(0, 229, 255, 0.15);
    border-color: #00e5ff;
    color: #00e5ff;
    box-shadow: 0 0 20px rgba(0, 229, 255, 0.3);
}
.pagination-neon .page-item.disabled .page-link {
    background-color: transparent;
    border-color: #1a1a1a;
    color: #444;
    cursor: not-allowed;
}
</style>

<?php include 'includes/footer.php'; ?>