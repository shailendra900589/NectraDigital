<?php
/**
 * Premium service page template — broad content, conversion-focused layout
 */
require_once __DIR__ . '/seo-data.php';
require_once __DIR__ . '/seo-components.php';
require_once __DIR__ . '/service-content.php';
require_once __DIR__ . '/local-page-seo.php';

if (!isset($service_slug)) {
    header('Location: /services');
    exit;
}

$is_city_page = !empty($is_city_page);

if (empty($service) || !is_array($service) || !isset($service['process'])) {
    $services = get_services_data();
    $serviceBase = $services[$service_slug] ?? null;

    if ($serviceBase === null) {
        if (is_file(__DIR__ . '/db.local.php') && file_exists(__DIR__ . '/growth/bootstrap.php')) {
            require_once __DIR__ . '/growth/bootstrap.php';
            if (function_exists('ge_is_ready') && ge_is_ready()) {
                $dbService = \Growth\Models\Service::findBySlug($service_slug);
                if ($dbService) {
                    $serviceBase = ge_minimal_service_from_record($dbService);
                }
            }
        }
    }

    if ($serviceBase === null) {
        header('Location: /404.php');
        exit;
    }

    $service = get_service_extended($service_slug, $serviceBase);
}

if ($is_city_page) {
    $service['h1'] = $localized_h1 ?? $service['h1'];
    $service['intro'] = $localized_intro ?? $service['intro'];
    $page_title = $page_title ?? $service['title'];
    $page_desc = $page_desc ?? $service['meta_desc'];
    $page_keys = $page_keys ?? ($service['keywords'] ?? '');
    $canonical_url = $canonical_url ?? null;
    $og_type = $og_type ?? 'website';
    $overview = ($service['overview'] ?? '<p>' . htmlspecialchars($service['intro']) . '</p>');
    if (!empty($localized_overview_extra)) {
        $overview .= $localized_overview_extra;
    }
    $service['faqs'] = $localized_faqs ?? $service['faqs'];
    $city_quick_answer = $quick_answer ?? $service['intro'];
    $form_service = ($service['silo'] ?? $service['h1']) . ' — ' . ($city_name ?? '');
    $form_city = $city_name ?? '';
    if (empty($breadcrumbs)) {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => SITE_URL . '/'],
            ['name' => 'Locations', 'url' => ge_locations_url()],
            ['name' => $city_name ?? 'Location', 'url' => ge_city_hub_url($city_slug ?? '')],
            ['name' => $service['silo'] ?? $service['h1'], 'url' => SITE_URL . ge_service_city_landing_url($service_slug, $city_slug ?? '')],
        ];
    }
} else {
    $page_title = $service['title'];
    $page_desc = $service['meta_desc'];
    $overview = $service['overview'] ?? '<p>' . htmlspecialchars($service['intro']) . '</p>';
    $city_quick_answer = $service['intro'];
    $form_service = $service['h1'];
    $form_city = '';
    $breadcrumbs = [
        ['name' => 'Home', 'url' => SITE_URL . '/'],
        ['name' => 'Services', 'url' => SITE_URL . '/services'],
        ['name' => $service['h1'], 'url' => SITE_URL . '/' . $service_slug],
    ];
}
$page_schema = [get_breadcrumb_schema($breadcrumbs)];
if ($is_city_page && !empty($canonical_url)) {
    $page_schema[] = [
        '@type' => 'WebPage',
        '@id' => rtrim($canonical_url, '/') . '#webpage',
        'url' => rtrim($canonical_url, '/'),
        'name' => $service['h1'] ?? $page_title,
        'description' => $page_desc ?? '',
        'isPartOf' => ['@id' => SITE_URL . '/#website'],
        'about' => ['@id' => SITE_URL . '/#organization'],
    ];
}

require_once __DIR__ . '/growth/engines/IntentKeywordEngine.php';
if ($is_city_page) {
    // page_keys set by resolver via IntentKeywordEngine
} else {
    $page_keys = \Growth\Engines\IntentKeywordEngine::forStaticPage($service_slug, $service['keywords'] ?? '');
}
$tagline = $is_city_page && !empty($localized_h2)
    ? $localized_h2
    : ($service['tagline'] ?? 'Results-Driven ' . $service['silo'] . ' Solutions');
$benefits = $service['benefits'] ?? [];
$paa = array_merge($service['paa'] ?? [], array_slice($service['faqs'] ?? [], 0, 2));

include __DIR__ . '/header.php';
render_service_schema($service_slug, $service);
if ($is_city_page && !empty($city)) {
    render_local_business_schema($city, $city_slug ?? '');
}
?>

<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/service-pages.css">

<main class="svc-page">
    <!-- HERO -->
    <header class="svc-hero">
        <div class="svc-hero-glow"></div>
        <div class="container position-relative z-1 py-5">
            <?php render_breadcrumbs($breadcrumbs); ?>
            <div class="row align-items-center g-5 py-4">
                <div class="col-lg-7">
                    <span class="svc-badge"><?php echo htmlspecialchars($service['silo']); ?><?php if ($is_city_page && !empty($city_name)): ?> · <?php echo htmlspecialchars($city_name); ?><?php else: ?> · Nectra Digital<?php endif; ?></span>
                    <p class="svc-tagline text-neon mb-2"><?php echo htmlspecialchars($tagline); ?></p>
                    <?php if ($is_city_page && !empty($city_name)): ?>
                    <h1 class="display-4 fw-bold text-white mb-4">Best <?php echo htmlspecialchars($service['silo']); ?> Company in <span class="text-neon"><?php echo htmlspecialchars($city_name); ?></span></h1>
                    <?php else: ?>
                    <h1 class="display-4 fw-bold text-white mb-4"><?php echo htmlspecialchars($service['h1']); ?></h1>
                    <?php endif; ?>
                    <p class="lead text-white-50 mb-4"><?php echo htmlspecialchars($service['intro']); ?></p>
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="#serviceCityForm" class="btn btn-nectra btn-lg"><?php echo $is_city_page ? 'Get Free Proposal in ' . htmlspecialchars($city_name) : 'Get Free Audit'; ?></a>
                        <a href="/contact?service=Strategy+Call<?php echo $is_city_page ? '&city=' . urlencode($city_name) : ''; ?>" class="btn btn-outline-light btn-lg">Book Strategy Call</a>
                    </div>
                    <div class="d-flex flex-wrap gap-4 text-white-50 small">
                        <span><i class="fas fa-check-circle text-neon me-1"></i> Free Consultation</span>
                        <span><i class="fas fa-check-circle text-neon me-1"></i> No Long-Term Contract</span>
                        <span><i class="fas fa-check-circle text-neon me-1"></i> ROI-Focused</span>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="svc-hero-card p-4 p-lg-5">
                        <div class="svc-hero-icon mb-4"><i class="fas <?php echo $service['icon']; ?>"></i></div>
                        <div class="row g-3">
                            <?php foreach ($service['stats'] as $stat): ?>
                            <div class="col-6">
                                <div class="svc-stat-box text-center p-3">
                                    <div class="svc-stat-val text-neon"><?php echo htmlspecialchars($stat['value']); ?></div>
                                    <div class="svc-stat-lbl text-white-50 small"><?php echo htmlspecialchars($stat['label']); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- TRUST STRIP -->
    <section class="svc-trust py-4 border-top border-bottom border-secondary">
        <div class="container">
            <div class="row text-center g-3">
                <div class="col-6 col-md-3"><i class="fas fa-award text-neon me-2"></i><span class="text-white-50 small">5+ Years Experience</span></div>
                <div class="col-6 col-md-3"><i class="fas fa-users text-neon me-2"></i><span class="text-white-50 small">200+ Projects</span></div>
                <div class="col-6 col-md-3"><i class="fas fa-star text-neon me-2"></i><span class="text-white-50 small">4.9★ Client Rating</span></div>
                <div class="col-6 col-md-3"><i class="fas fa-headset text-neon me-2"></i><span class="text-white-50 small">24/7 Support</span></div>
            </div>
        </div>
    </section>

    <!-- OVERVIEW -->
    <section class="py-5">
        <div class="container">
            <div class="row g-5 align-items-start">
                <div class="col-lg-8">
                    <h2 class="text-white h3 mb-4">Why Choose Our <span class="text-neon"><?php echo htmlspecialchars($service['silo']); ?></span><?php if ($is_city_page && !empty($city_name)): ?> in <span class="text-neon"><?php echo htmlspecialchars($city_name); ?></span><?php endif; ?>?</h2>
                    <div class="svc-overview text-white-50"><?php echo $overview; ?></div>

                    <div class="mt-5 p-4 border border-neon rounded svc-glass">
                        <h3 class="text-neon h6 text-uppercase mb-2"><i class="fas fa-bolt me-2"></i>Quick Answer</h3>
                        <p class="text-white mb-0"><?php echo htmlspecialchars($city_quick_answer); ?></p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="svc-sidebar-card p-4 sticky-top" style="top:100px" id="serviceCityForm">
                        <h3 class="text-white h6 mb-3"><?php echo $is_city_page ? 'Start Your Project in ' . htmlspecialchars($city_name) : 'Start Your Project'; ?></h3>
                        <?php $form_instance = 'sidebar'; include __DIR__ . '/partials/contact-form-inline.php'; ?>
                        <hr class="border-secondary my-3">
                        <ul class="list-unstyled text-white-50 small mb-0 svc-checklist">
                            <li><i class="fas fa-check text-neon"></i> Free initial consultation</li>
                            <li><i class="fas fa-check text-neon"></i> Custom strategy proposal</li>
                            <li><i class="fas fa-check text-neon"></i> Transparent monthly reporting</li>
                            <li><i class="fas fa-check text-neon"></i> Dedicated account manager</li>
                            <li><i class="fas fa-check text-neon"></i> No long-term lock-in</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CHALLENGES -->
    <section class="py-5 bg-darker border-top border-secondary">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-white h3">Problems We <span class="text-neon">Solve</span></h2>
                <p class="text-white-50 mx-auto" style="max-width:600px">Most businesses struggle with these challenges before working with us.</p>
            </div>
            <div class="row g-4">
                <?php foreach ($service['challenges'] as $ch): ?>
                <div class="col-md-4">
                    <div class="svc-challenge-card p-4 h-100">
                        <i class="fas <?php echo $ch['icon']; ?> text-neon fa-2x mb-3"></i>
                        <h3 class="text-white h6"><?php echo htmlspecialchars($ch['title']); ?></h3>
                        <p class="text-white-50 small mb-0"><?php echo htmlspecialchars($ch['desc']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- BENEFITS -->
    <?php if (!empty($benefits)): ?>
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-white h3">Key <span class="text-neon">Benefits</span></h2>
            </div>
            <div class="row g-4">
                <?php foreach ($benefits as $b): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="svc-benefit-card p-4 h-100">
                        <div class="svc-benefit-icon mb-3"><i class="fas <?php echo $b['icon']; ?>"></i></div>
                        <h3 class="text-white h6"><?php echo htmlspecialchars($b['title']); ?></h3>
                        <p class="text-white-50 small mb-0"><?php echo htmlspecialchars($b['desc']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- PROCESS -->
    <section class="py-5 bg-darker border-top border-secondary">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-white h3">Our <span class="text-neon">Process</span></h2>
                <p class="text-white-50">A proven 5-step framework for delivering measurable results.</p>
            </div>
            <div class="row g-4">
                <?php foreach ($service['process'] as $step): ?>
                <div class="col-md-6 col-lg">
                    <div class="svc-process-step p-4 h-100 text-center">
                        <div class="svc-step-num text-neon mb-2"><?php echo $step['step']; ?></div>
                        <h3 class="text-white h6"><?php echo htmlspecialchars($step['title']); ?></h3>
                        <p class="text-white-50 small mb-0"><?php echo htmlspecialchars($step['desc']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- DELIVERABLES + TOOLS -->
    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <h2 class="text-white h4 mb-4">What's <span class="text-neon">Included</span></h2>
                    <div class="row g-2">
                        <?php foreach ($service['deliverables'] as $d): ?>
                        <div class="col-md-6">
                            <div class="svc-deliverable d-flex align-items-center p-3">
                                <i class="fas fa-check-circle text-neon me-2 flex-shrink-0"></i>
                                <span class="text-white small"><?php echo htmlspecialchars($d); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 class="text-white h4 mb-4">Tools & <span class="text-neon">Technology</span></h2>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($service['tools'] as $tool): ?>
                        <span class="svc-tool-badge"><?php echo htmlspecialchars($tool); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 p-4 border border-secondary rounded svc-glass">
                        <h3 class="text-white h6 mb-2"><i class="fas fa-lightbulb text-neon me-2"></i>Expert Insight</h3>
                        <p class="text-white-50 small fst-italic mb-0">"Our <?php echo strtolower(htmlspecialchars($service['h1'])); ?> approach is built on data, transparency, and relentless optimization — not guesswork. Every strategy is customized to your market, competition, and growth goals." — <?php echo FOUNDER_NAME; ?>, <?php echo FOUNDER_TITLE; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- INDUSTRIES -->
    <section class="py-5 bg-darker border-top border-secondary">
        <div class="container">
            <h2 class="text-white h4 mb-4 text-center">Industries We <span class="text-neon">Serve</span></h2>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <?php foreach ($service['industries'] as $ind): ?>
                <span class="svc-industry-pill"><?php echo htmlspecialchars($ind); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- COMPARISON -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-white h4 mb-4 text-center">Nectra Digital vs <span class="text-neon">Typical Agencies</span></h2>
            <div class="row g-4 justify-content-center">
                <div class="col-md-5">
                    <div class="svc-compare-card svc-compare-us p-4 h-100">
                        <h3 class="text-neon h6 mb-3"><i class="fas fa-check-double me-2"></i>Nectra Digital</h3>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($service['comparison']['us'] as $item): ?>
                            <li class="text-white small mb-2"><i class="fas fa-check text-neon me-2"></i><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="svc-compare-card p-4 h-100">
                        <h3 class="text-white-50 h6 mb-3"><i class="fas fa-times me-2"></i>Typical Agencies</h3>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($service['comparison']['others'] as $item): ?>
                            <li class="text-white-50 small mb-2"><i class="fas fa-times text-danger me-2"></i><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php render_faq_section($service['faqs'], $service['h1'] . ' — FAQ'); ?>

    <?php
    try {
        render_service_city_links($service_slug, $service, $is_city_page ? ($city_slug ?? null) : null);
    } catch (\Throwable $e) {
        // Never block footer / lower sections if city links fail
    }
    ?>

    <?php if ($is_city_page): ?>
    <section class="py-5 border-top border-secondary">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="svc-sidebar-card p-4 p-lg-5">
                        <h2 class="text-white h4 mb-2 text-center">Get a Free <?php echo htmlspecialchars($service['silo']); ?> Proposal in <span class="text-neon"><?php echo htmlspecialchars($city_name); ?></span></h2>
                        <p class="text-white-50 small text-center mb-4">Tell us about your goals — we respond within 24 hours with a custom strategy for <?php echo htmlspecialchars($city_name); ?> businesses.</p>
                        <?php $form_instance = 'bottom'; include __DIR__ . '/partials/contact-form-inline.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($paa)): ?>
    <section class="py-5 bg-darker border-top border-secondary">
        <div class="container py-2">
            <h2 class="text-white h5 mb-4">People Also Ask</h2>
            <?php foreach ($paa as $item): ?>
            <div class="mb-4 pb-3 border-bottom border-secondary">
                <h3 class="text-white h6 mb-2"><?php echo htmlspecialchars($item['q']); ?></h3>
                <p class="text-white-50 small mb-0"><?php echo htmlspecialchars($item['a']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php render_internal_links_service($service['related']); ?>
    <?php render_cta_blocks('full'); ?>
    <?php render_founder_section(true); ?>
    <?php render_trust_signals(); ?>
</main>

<?php include __DIR__ . '/footer.php'; ?>
