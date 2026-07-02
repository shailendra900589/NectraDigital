<?php
/**
 * Dynamic programmatic landing page renderer
 * Expects $page from page-router.php
 */
if (empty($page)) {
    header('Location: /404.php');
    exit;
}

require_once __DIR__ . '/includes/growth/bootstrap.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/seo-components.php';

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

include __DIR__ . '/includes/header.php';
$schemaData = ge_json_decode($page['schema_json'] ?? '{}', []);
if (!empty($schemaData)) {
    SchemaEngine::output($schemaData);
}
?>

<main class="growth-landing">
    <section class="gl-hero py-5">
        <div class="container py-4">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb bg-transparent p-0 m-0 small">
                    <li class="breadcrumb-item"><a href="/" class="text-white-50 text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="/services" class="text-white-50 text-decoration-none">Services</a></li>
                    <li class="breadcrumb-item active text-neon"><?php echo htmlspecialchars($page['city_name']); ?><?php if (!empty($page['industry_name'])): ?> · <?php echo htmlspecialchars($page['industry_name']); ?><?php endif; ?></li>
                </ol>
            </nav>
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge border border-neon text-neon mb-3"><?php echo htmlspecialchars($page['service_name']); ?> · <?php echo htmlspecialchars($page['city_name']); ?><?php if (!empty($page['industry_name'])): ?> · <?php echo htmlspecialchars($page['industry_name']); ?><?php endif; ?></span>
                    <h1 class="display-5 fw-bold text-white mb-3"><?php echo htmlspecialchars($page['h1']); ?></h1>
                    <?php if (!empty($page['h2'])): ?>
                    <h2 class="h5 text-white-50 mb-3"><?php echo htmlspecialchars($page['h2']); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($page['h3'])): ?>
                    <h3 class="h6 text-neon mb-4"><?php echo htmlspecialchars($page['h3']); ?></h3>
                    <?php endif; ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach (array_slice($ctas, 0, 2) as $cta): ?>
                        <a href="<?php echo htmlspecialchars($cta['url']); ?>" class="btn <?php echo ($cta['icon'] ?? '') === 'fa-search' ? 'btn-nectra' : 'btn-outline-light'; ?>"><?php echo htmlspecialchars($cta['label']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-center">
                    <div class="gl-stat-ring mx-auto">
                        <div class="gl-stat-value">4.9★</div>
                        <div class="gl-stat-label">Client Rating</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-3 border-top border-bottom border-secondary bg-dark">
        <div class="container">
            <div class="row text-center g-3">
                <div class="col-3 col-md"><div class="gl-mini-stat"><strong class="text-neon">200+</strong><br><small class="text-white-50">Projects</small></div></div>
                <div class="col-3 col-md"><div class="gl-mini-stat"><strong class="text-neon">340%</strong><br><small class="text-white-50">Avg Growth</small></div></div>
                <div class="col-3 col-md"><div class="gl-mini-stat"><strong class="text-neon">5+</strong><br><small class="text-white-50">Years Exp</small></div></div>
                <div class="col-3 col-md"><div class="gl-mini-stat"><strong class="text-neon">24/7</strong><br><small class="text-white-50">Support</small></div></div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <?php if (!empty($page['quick_answer'])): ?>
                    <div class="p-4 border border-neon rounded bg-glass mb-4">
                        <h2 class="text-neon h6 text-uppercase mb-2"><i class="fas fa-bolt me-2"></i>Quick Answer</h2>
                        <p class="text-white mb-0"><?php echo htmlspecialchars($page['quick_answer']); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($page['voice_answer'])): ?>
                    <div class="p-3 border border-secondary rounded bg-dark mb-4 d-none" itemscope itemtype="https://schema.org/SpeakableSpecification">
                        <meta itemprop="cssSelector" content=".voice-answer">
                        <p class="voice-answer text-white-50 small mb-0"><?php echo htmlspecialchars($page['voice_answer']); ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="gl-content text-white-50 mb-4">
                        <?php echo $page['content']; ?>
                    </div>

                    <?php if (!empty($takeaways)): ?>
                    <div class="p-4 border border-secondary rounded bg-dark mb-4">
                        <h2 class="text-white h6 mb-3"><i class="fas fa-list-check text-neon me-2"></i>Key Takeaways</h2>
                        <ul class="text-white-50 small mb-0">
                            <?php foreach ($takeaways as $t): ?>
                            <li><?php echo htmlspecialchars($t); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($page['expert_insight'])): ?>
                    <div class="p-4 border border-secondary rounded bg-glass mb-4">
                        <h2 class="text-white h6 mb-2"><i class="fas fa-lightbulb text-neon me-2"></i>Expert Insight</h2>
                        <p class="text-white-50 small fst-italic mb-0"><?php echo htmlspecialchars($page['expert_insight']); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($faqs)): ?>
                    <div class="mb-4">
                        <h2 class="text-white h5 mb-3">Frequently Asked Questions</h2>
                        <div class="accordion accordion-flush" id="lpFaq">
                            <?php foreach ($faqs as $i => $faq): ?>
                            <div class="accordion-item bg-transparent border-secondary">
                                <h3 class="accordion-header">
                                    <button class="accordion-button <?php echo $i > 0 ? 'collapsed' : ''; ?> bg-dark text-white" type="button" data-bs-toggle="collapse" data-bs-target="#lpfaq<?php echo $i; ?>">
                                        <?php echo htmlspecialchars($faq['q']); ?>
                                    </button>
                                </h3>
                                <div id="lpfaq<?php echo $i; ?>" class="accordion-collapse collapse <?php echo $i === 0 ? 'show' : ''; ?>" data-bs-parent="#lpFaq">
                                    <div class="accordion-body text-white-50"><?php echo htmlspecialchars($faq['a']); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($paa)): ?>
                    <div class="p-4 border border-secondary rounded mb-4">
                        <h2 class="text-white h6 mb-3">People Also Ask</h2>
                        <?php foreach ($paa as $item): ?>
                        <div class="mb-3 pb-3 border-bottom border-secondary">
                            <strong class="text-white small d-block mb-1"><?php echo htmlspecialchars($item['question']); ?></strong>
                            <span class="text-white-50 small"><?php echo htmlspecialchars($item['answer']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($page['summary'])): ?>
                    <div class="p-4 border-start border-neon border-3 bg-glass">
                        <h2 class="text-white h6 mb-2">Summary</h2>
                        <p class="text-white-50 small mb-0"><?php echo htmlspecialchars($page['summary']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <div class="p-4 border border-secondary rounded bg-glass sticky-top gl-sidebar-cta" style="top:100px;">
                        <h3 class="text-white h6 mb-3">Start Your Project in <?php echo htmlspecialchars($page['city_name']); ?></h3>
                        <div class="d-grid gap-2">
                            <?php foreach ($ctas as $cta): ?>
                            <a href="<?php echo htmlspecialchars($cta['url']); ?>" class="btn btn-sm <?php echo strpos($cta['label'], 'Audit') !== false ? 'btn-nectra' : 'btn-outline-light'; ?>"><?php echo htmlspecialchars($cta['label']); ?></a>
                            <?php endforeach; ?>
                        </div>
                        <hr class="border-secondary my-3">
                        <p class="text-white-50 small mb-0"><i class="fas fa-map-marker-alt text-neon me-2"></i><?php echo htmlspecialchars($page['city_name'] . ', ' . ($page['state'] ?? 'India')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($links)): ?>
    <section class="py-4 border-top border-secondary">
        <div class="container">
            <h2 class="text-white h6 mb-3">Related Resources</h2>
            <div class="row g-2">
                <?php foreach ($links as $link): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" class="d-block p-2 border border-secondary rounded text-decoration-none hover-effect">
                        <span class="text-neon small"><?php echo htmlspecialchars($link['title']); ?></span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php else:
        render_internal_links_service(['seo-services', 'local-seo-services', 'performance-marketing-services', 'web-development-services', 'ai-automation-services']);
    endif; ?>
</main>

<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/growth-landing.css">

<?php include __DIR__ . '/includes/footer.php'; ?>
