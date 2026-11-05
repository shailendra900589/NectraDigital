<?php
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';
require_once 'includes/i18n.php';
require_once 'includes/nav-data.php';
require_once 'includes/brand-config.php';

$page_title = 'Technology Solutions for Startups & Enterprises | Nectra Digital';
$page_desc = 'Software, web and mobile solutions for startups, SMEs and enterprises — product development, automation, e-commerce and digital transformation.';
$canonical_url = nectra_page_canonical('/solutions');
$page_schema = [get_breadcrumb_schema([
    ['name' => 'Home', 'url' => SITE_URL . '/'],
    ['name' => 'Solutions', 'url' => SITE_URL . '/solutions'],
])];
$isDarkHeader = true;

include 'includes/header.php';
$solutions = nectra_solutions_nav_items();
?>
<main>
<header class="nd-page-hero nd-section-dark text-center">
    <div class="container">
        <?php render_breadcrumbs([['name' => 'Home', 'url' => '/'], ['name' => 'Solutions', 'url' => '/solutions']]); ?>
        <h1 class="fw-bold text-white mb-3 nd-hero-title">Solutions Built Around Your Business</h1>
        <p class="lead text-white-50 mx-auto" style="max-width: 720px;">Positioning paths for how we deliver software, websites and mobile products — each links to established services and consultation.</p>
    </div>
</header>
<section class="nd-section">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($solutions as $s): ?>
            <div class="col-md-6 col-lg-4" id="<?php echo htmlspecialchars($s['anchor']); ?>">
                <article class="nd-card p-4 h-100">
                    <h2 class="h5 fw-bold"><?php echo htmlspecialchars($s['title']); ?></h2>
                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($s['desc']); ?></p>
                    <a href="<?php echo nectra_nav_path(NECTRA_CTA_PRIMARY_URL); ?>" class="fw-semibold text-primary-blue text-decoration-none small">Discuss this solution →</a>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php
$ctaHeading = 'Not sure which solution fits?';
$ctaText = 'Share your goals — we will recommend the right mix of software, web, mobile and growth services.';
include 'includes/ui/cta.php';
?>
</main>
<?php
include 'includes/footer.php';
