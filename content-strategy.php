<?php 
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';
require_once __DIR__ . '/includes/i18n.php';

$page_title = "Content Authority Strategy & Silo Blueprint — Nectra Digital";
$page_desc = "Complete content authority blueprint featuring semantic topic clusters, pillar-cluster hierarchies, 100+ strategic keyword angles, and internal linking maps.";
$page_keys = "Content Strategy, Blog Topics SEO, Content Silo, Pillar Pages, Internal Linking Strategy, E-E-A-T Architecture";
$canonical_url = nectra_page_canonical('/content-strategy');
$noindex = false;

include 'includes/header.php';
$silos = get_content_silos();
$topics = get_blog_topic_ideas();
?>

<main class="bg-light">
    <!-- Hero Header -->
    <header class="nd-page-hero nd-legal-hero text-center">
        <div class="container py-2 py-md-3">
            <h1 class="fw-bold text-white mb-3 nd-hero-title">Content Authority Strategy</h1>
            <p class="lead text-white-50 mx-auto nd-prose-width">
                Pillar-cluster hierarchies, semantic topic models, and internal PageRank distribution designed for search dominance and AI engine citation.
            </p>
        </div>
    </header>

    <div class="container py-5">
        <div class="mb-4">
            <?php render_breadcrumbs([['name' => 'Home', 'url' => '/'], ['name' => 'Content Strategy', 'url' => '/content-strategy']], 'light'); ?>
        </div>

        <!-- Section 1: Content Silo Structure -->
        <section class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-0 nd-section-title">Semantic Silo Architecture</h2>
                </div>
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill small fw-semibold">
                    <?php echo count($silos); ?> Core Hubs Active
                </span>
            </div>

            <div class="row g-4">
                <?php foreach ($silos as $silo => $data): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                                <i class="fas fa-sitemap"></i>
                            </span>
                            <h3 class="h5 fw-bold text-dark mb-0"><?php echo htmlspecialchars($silo); ?></h3>
                        </div>
                        <div class="p-2.5 bg-light rounded-3 mb-3 small">
                            <span class="text-muted">Pillar:</span> 
                            <strong class="text-dark d-block"><?php echo htmlspecialchars($data['pillar']); ?></strong>
                        </div>
                        <h4 class="small fw-bold text-uppercase text-muted mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">Cluster Sub-Topics</h4>
                        <ul class="list-unstyled text-secondary small mb-0 d-flex flex-column gap-1.5">
                            <?php foreach ($data['clusters'] as $cluster): ?>
                            <li class="d-flex align-items-start gap-2">
                                <i class="fas fa-arrow-right text-primary fa-xs mt-1.5 flex-shrink-0"></i>
                                <span><?php echo htmlspecialchars($cluster); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Section 2: 100 Strategic Topics -->
        <section class="mb-5">
            <div class="mb-4">
                <span class="text-primary fw-semibold small text-uppercase" style="letter-spacing: 0.05em;">Editorial Calendar</span>
                <h2 class="h3 fw-bold text-dark mb-1">Strategic Topic Catalog</h2>
                <p class="text-muted small mb-0">Systematically planned search intents targeting top, middle, and bottom of the enterprise conversion funnel.</p>
            </div>

            <div class="row g-4">
                <?php foreach ($topics as $category => $ideas): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <h3 class="h5 fw-bold text-dark mb-0">
                                <i class="fas fa-folder-open text-primary me-2"></i><?php echo htmlspecialchars($category); ?>
                            </h3>
                            <span class="badge bg-light text-secondary border small">
                                <?php echo count($ideas); ?> Topics
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($ideas as $idea): ?>
                            <span class="badge bg-light text-dark border p-2 fw-normal small rounded-2">
                                <?php echo htmlspecialchars($idea); ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Section 3: Internal Linking Map -->
        <section class="mb-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <span class="text-primary fw-semibold small text-uppercase" style="letter-spacing: 0.05em;">PageRank Flow</span>
                <h2 class="h3 fw-bold text-dark mb-3">Internal Linking Architecture</h2>
                <p class="text-muted small mb-4">Strict bidirectional equity distribution between authority hubs and programmatic conversion endpoints.</p>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <h4 class="h6 fw-bold text-dark mb-2"><i class="fas fa-home text-primary me-1"></i> Homepage Flow</h4>
                            <p class="text-muted small mb-0">Links directly to all 17 flagship services, top national city hubs, authoritative company pillars (/about, /portfolio, /aeo), and featured editorial insights.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <h4 class="h6 fw-bold text-dark mb-2"><i class="fas fa-layer-group text-info me-1"></i> Core Service Pages</h4>
                            <p class="text-muted small mb-0">Cross-links to 4 sibling services within the same silo, parent topical pillar, case studies, and dynamic local city landing pages.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <h4 class="h6 fw-bold text-dark mb-2"><i class="fas fa-newspaper text-success me-1"></i> Editorial Insights</h4>
                            <p class="text-muted small mb-0">Every post features 8–12 contextually anchored internal links to high-intent service pages, verified author bio, and related silo articles.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <h4 class="h6 fw-bold text-dark mb-2"><i class="fas fa-map-marked-alt text-warning me-1"></i> City Hubs & Matrices</h4>
                            <p class="text-muted small mb-0">Bidirectional links connecting geographic landing pages back to corresponding service templates, related tier-1/tier-2 cities, and lead capture forms.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <div class="text-center py-4">
            <h3 class="h4 fw-bold text-dark mb-2">Want This Authority Architecture For Your Brand?</h3>
            <p class="text-muted small mb-4">We engineer custom semantic search strategies and content silos for growth-stage enterprises.</p>
            <a href="/contact" class="btn btn-primary px-5 py-3 rounded-pill fw-semibold shadow-xs">
                Schedule Technical Consultation <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
