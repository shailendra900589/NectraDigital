<?php
/**
 * Dynamic programmatic landing page renderer
 * Expects $page from page-router.php
 */
require_once __DIR__ . '/includes/config.php';
if (empty($page)) {
    header('Location: ' . SITE_URL . '/404.php');
    exit;
}

require_once __DIR__ . '/includes/growth/bootstrap.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/seo-components.php';
require_once __DIR__ . '/includes/eeat-copy.php';

use Growth\Engines\SchemaEngine;
use Growth\Engines\SeoEngine;

$faqs = ge_json_decode($page['faq_json'] ?? '[]');
$takeaways = ge_json_decode($page['key_takeaways'] ?? '[]');
$paa = ge_json_decode($page['paa_json'] ?? '[]');
$links = ge_json_decode($page['internal_links_json'] ?? '[]');
$ctas = ge_json_decode($page['cta_json'] ?? '[]');
if (empty($ctas)) {
    $ctas = ge_default_ctas();
}
$seo = SeoEngine::metaTags($page);

$page_title = $page['meta_title'];
$page_desc = $page['meta_description'];
$page_keys = implode(', ', ge_json_decode($page['keywords_json'] ?? '[]'));
$canonical_url = $seo['canonical'];
$og_type = 'website';
$isDarkHeader = true;

include __DIR__ . '/includes/header.php';
$schemaData = ge_json_decode($page['schema_json'] ?? '{}', []);
if (!empty($schemaData)) {
    SchemaEngine::output($schemaData);
}
?>

<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/growth-landing.css">

<main class="growth-landing">
    <!-- HERO (Dark Navy) -->
    <section class="gl-hero py-5">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h1 class="fw-bold text-white mb-3 nd-hero-title"><?php echo htmlspecialchars($page['h1']); ?></h1>
                    <?php if (!empty($page['h2'])): ?>
                    <h2 class="h5 text-white-50 mb-3 fw-normal"><?php echo htmlspecialchars($page['h2']); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($page['h3'])): ?>
                    <h3 class="h6 mb-4" style="color: #38BDF8;"><?php echo htmlspecialchars($page['h3']); ?></h3>
                    <?php endif; ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach (array_slice($ctas, 0, 2) as $cta): ?>
                        <a href="<?php echo htmlspecialchars($cta['url']); ?>" class="btn <?php echo ($cta['icon'] ?? '') === 'fa-search' ? 'btn-primary' : 'btn-outline-light'; ?> px-4"><?php echo htmlspecialchars($cta['label']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-center">
                    <div class="gl-stat-ring mx-auto">
                        <div class="gl-stat-value"><?php echo htmlspecialchars(nectra_trust_rating_label()); ?></div>
                        <div class="gl-stat-label">Client Feedback</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TRUST STRIP -->
    <?php include __DIR__ . '/includes/ui/trust-bar.php'; ?>

    <!-- MAIN BODY CONTENT & SIDEBAR -->
    <section class="py-5" style="background: #F8FAFC;">
        <div class="container py-3">
            <div class="row g-4">
                <div class="col-lg-8">
                    <?php if (!empty($page['quick_answer'])): ?>
                    <div class="p-4 rounded-3 bg-white border mb-4 shadow-sm" style="border-left: 4px solid var(--nd-primary) !important;">
                        <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="fas fa-bolt"></i> Quick Answer
                        </div>
                        <p class="text-dark mb-0 fw-medium" style="font-size: 1.05rem; line-height: 1.6;"><?php echo htmlspecialchars($page['quick_answer']); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($page['voice_answer'])): ?>
                    <div class="p-3 border rounded bg-white mb-4 d-none" itemscope itemtype="https://schema.org/SpeakableSpecification">
                        <meta itemprop="cssSelector" content=".voice-answer">
                        <p class="voice-answer text-muted small mb-0"><?php echo htmlspecialchars($page['voice_answer']); ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- Dynamic HTML Content -->
                    <div class="gl-content p-4 rounded-4 bg-white border shadow-sm mb-4">
                        <?php echo $page['content']; ?>
                    </div>

                    <?php if (!empty($takeaways)): ?>
                    <div class="p-4 rounded-3 bg-white border mb-4 shadow-sm">
                        <h2 class="h6 fw-bold text-dark mb-3"><i class="fas fa-list-check text-primary me-2"></i>Key Takeaways</h2>
                        <ul class="text-muted small mb-0">
                            <?php foreach ($takeaways as $t): ?>
                            <li class="mb-2"><?php echo htmlspecialchars($t); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($page['expert_insight'])): ?>
                    <div class="p-4 rounded-3 bg-white border mb-4 shadow-sm" style="border-left: 4px solid var(--nd-primary) !important;">
                        <h2 class="h6 fw-bold text-dark mb-2"><i class="fas fa-lightbulb text-primary me-2"></i>Expert Insight</h2>
                        <p class="text-muted small fst-italic mb-0"><?php echo htmlspecialchars($page['expert_insight']); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($faqs)): ?>
                    <div class="p-4 rounded-4 bg-white border shadow-sm mb-4">
                        <h2 class="h5 fw-bold text-dark mb-3">Frequently Asked Questions</h2>
                        <div class="accordion accordion-flush" id="lpFaq">
                            <?php foreach ($faqs as $i => $faq): ?>
                            <div class="accordion-item border-bottom">
                                <h3 class="accordion-header">
                                    <button class="accordion-button <?php echo $i > 0 ? 'collapsed' : ''; ?> fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#lpfaq<?php echo $i; ?>">
                                        <?php echo htmlspecialchars($faq['q']); ?>
                                    </button>
                                </h3>
                                <div id="lpfaq<?php echo $i; ?>" class="accordion-collapse collapse <?php echo $i === 0 ? 'show' : ''; ?>" data-bs-parent="#lpFaq">
                                    <div class="accordion-body text-muted small"><?php echo htmlspecialchars($faq['a']); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($paa)): ?>
                    <div class="p-4 rounded-3 bg-white border mb-4 shadow-sm">
                        <h2 class="h6 fw-bold text-dark mb-3">People Also Ask</h2>
                        <?php foreach ($paa as $item): ?>
                        <div class="mb-3 pb-3 border-bottom">
                            <strong class="text-dark small d-block mb-1"><?php echo htmlspecialchars($item['question']); ?></strong>
                            <span class="text-muted small"><?php echo htmlspecialchars($item['answer']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($page['summary'])): ?>
                    <div class="p-4 rounded-3 bg-white border shadow-sm" style="border-left: 4px solid var(--nd-primary) !important;">
                        <h2 class="h6 fw-bold text-dark mb-2">Summary</h2>
                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($page['summary']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <div class="p-4 border rounded-4 bg-white shadow-sm sticky-top gl-sidebar-cta" style="top:100px;">
                        <h3 class="h6 fw-bold text-dark mb-3">Start Your Project in <?php echo htmlspecialchars($page['city_name']); ?></h3>
                        <p class="text-muted small mb-3">Custom growth roadmap prepared within 24 hours.</p>
                        <div class="d-grid gap-2">
                            <?php foreach ($ctas as $cta): ?>
                            <a href="<?php echo htmlspecialchars($cta['url']); ?>" class="btn btn-sm <?php echo strpos($cta['label'], 'Audit') !== false ? 'btn-primary' : 'btn-outline-primary'; ?> fw-semibold py-2"><?php echo htmlspecialchars($cta['label']); ?></a>
                            <?php endforeach; ?>
                        </div>
                        <hr class="my-3" style="border-color: #E2E8F0;">
                        <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt text-primary me-2"></i><?php echo htmlspecialchars($page['city_name'] . ', ' . ($page['state'] ?? 'India')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($links)): ?>
    <section class="py-4 bg-white border-top border-bottom">
        <div class="container">
            <h2 class="h6 fw-bold text-dark mb-3">Related Resources</h2>
            <div class="row g-2">
                <?php foreach ($links as $link): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" class="d-block p-2 border rounded text-decoration-none text-dark bg-light hover-lift">
                        <span class="text-primary small fw-medium"><?php echo htmlspecialchars($link['title']); ?></span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php else:
        render_internal_links_service(['seo-services', 'local-seo-services', 'performance-marketing-services', 'web-development-services', 'ai-automation-services']);
    endif; ?>

    <!-- FINAL CTA & AUTHOR -->
    <?php include __DIR__ . '/includes/ui/cta.php'; ?>
    <?php include __DIR__ . '/includes/ui/author-box.php'; ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
