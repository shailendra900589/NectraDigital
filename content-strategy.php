<?php 
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';

$page_title = "Content Strategy & Blog Topics | Nectra Digital SEO Authority Plan";
$page_desc = "Complete content authority strategy with 100+ blog topic ideas, pillar pages, content silos, and internal linking map for SEO dominance.";
$page_keys = "Content Strategy, Blog Topics SEO, Content Silo, Pillar Pages, Internal Linking Strategy";
$noindex = false;

include 'includes/header.php';
$silos = get_content_silos();
$topics = get_blog_topic_ideas();
?>

<main class="py-5">
    <div class="container py-5">
        <?php render_breadcrumbs([['name' => 'Home', 'url' => '/'], ['name' => 'Content Strategy', 'url' => '/content-strategy']]); ?>
        <h1 class="text-white display-6 mb-3">Content Authority <span class="text-neon">Strategy</span></h1>
        <p class="text-white-50 mb-5">Pillar pages, content silos, 100+ blog topics, and internal linking architecture for organic dominance.</p>

        <section class="mb-5">
            <h2 class="text-white h4 mb-4">Content Silo Structure</h2>
            <div class="row g-4">
                <?php foreach ($silos as $silo => $data): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="p-4 border border-secondary rounded bg-glass h-100">
                        <h3 class="text-neon h6"><?php echo htmlspecialchars($silo); ?></h3>
                        <p class="text-white small fw-bold mb-2">Pillar: <?php echo htmlspecialchars($data['pillar']); ?></p>
                        <ul class="text-white-50 small mb-0">
                            <?php foreach ($data['clusters'] as $cluster): ?>
                            <li><?php echo htmlspecialchars($cluster); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="mb-5">
            <h2 class="text-white h4 mb-4">100 Blog Topic Ideas</h2>
            <?php foreach ($topics as $category => $ideas): ?>
            <div class="mb-4">
                <h3 class="text-neon h6 mb-2"><?php echo htmlspecialchars($category); ?> (<?php echo count($ideas); ?> topics)</h3>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($ideas as $idea): ?>
                    <span class="badge bg-dark border border-secondary text-white-50 p-2"><?php echo htmlspecialchars($idea); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </section>

        <section class="mb-5 p-4 border border-neon rounded bg-glass">
            <h2 class="text-white h5 mb-3">Internal Linking Map</h2>
            <ul class="text-white-50 small">
                <li><strong class="text-white">Homepage →</strong> All 15 service pages, top 5 city pages, /about, /aeo, /insights</li>
                <li><strong class="text-white">Service Pages →</strong> Related services (4 each), /insights, /contact, parent silo pages</li>
                <li><strong class="text-white">Blog Posts →</strong> 10+ internal links: related services, category posts, city pages, /contact, /about</li>
                <li><strong class="text-white">City Pages →</strong> 6 featured services, all other cities, /contact, /about</li>
                <li><strong class="text-white">AEO Page →</strong> Relevant service pages, /contact, homepage</li>
            </ul>
        </section>

        <div class="text-center">
            <a href="/contact" class="btn btn-nectra">Implement This Strategy</a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
