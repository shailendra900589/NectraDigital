<?php
/**
 * Dynamic city landing page template
 * Accessed via /digital-agency-{city}
 */
require_once __DIR__ . '/includes/seo-data.php';
require_once __DIR__ . '/includes/seo-components.php';
require_once __DIR__ . '/includes/local-page-seo.php';
require_once __DIR__ . '/includes/i18n.php';

$city_slug = isset($_GET['city']) ? preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['city'])) : '';
$cities = get_cities_data();

if (!$city_slug) {
    header('Location: /404.php');
    exit;
}

$city = $cities[$city_slug] ?? null;
if (!$city && function_exists('ge_city_from_slug')) {
    require_once __DIR__ . '/includes/service-city-resolver.php';
    $city = ge_city_from_slug($city_slug);
}

if (!$city) {
    header('Location: /404.php');
    exit;
}

$city_name = $city['name'];
$city_state = $city['state'] ?? '';
$is_hq = !empty($city['is_hq']);

$hubSeo = ge_city_hub_seo($city, $city_slug);
$page_title = $hubSeo['page_title'];
$page_desc = $hubSeo['page_desc'];
$page_keys = $hubSeo['page_keys'];
$canonical_url = nectra_normalize_canonical_url($hubSeo['canonical_url']);
$og_type = $hubSeo['og_type'];
$breadcrumbs = $hubSeo['breadcrumbs'];
$city_faqs = ge_city_hub_faqs($city, $city_slug);

$page_schema = [
    get_breadcrumb_schema($breadcrumbs),
    [
        '@type' => 'WebPage',
        '@id' => $canonical_url . '#webpage',
        'url' => $canonical_url,
        'name' => $hubSeo['h1'],
        'description' => $page_desc,
        'isPartOf' => ['@id' => SITE_URL . '/#website'],
        'about' => ['@id' => SITE_URL . '/#organization'],
    ],
];

include __DIR__ . '/includes/header.php';
render_local_business_schema($city, $city_slug);
output_faq_schema($city_faqs);
?>

<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/service-pages.css">

<main class="svc-page">
    <header class="svc-hero" style="min-height: 48vh;">
        <div class="svc-hero-glow"></div>
        <div class="container position-relative z-1 py-5">
            <?php render_breadcrumbs($breadcrumbs); ?>
            <span class="svc-badge"><?php echo $is_hq ? 'Global Headquarters' : 'Local Operations'; ?></span>
            <h1 class="display-5 fw-bold text-white mb-3"><?php echo htmlspecialchars($hubSeo['h1']); ?></h1>
            <p class="lead text-white-50 mb-4" style="max-width: 820px;">
                <?php echo htmlspecialchars($hubSeo['hero_intro']); ?>
            </p>
            <div class="d-flex flex-wrap gap-3">
                <a href="/contact?city=<?php echo urlencode($city_name); ?>" class="btn btn-nectra btn-lg">Get Free SEO Audit in <?php echo htmlspecialchars($city_name); ?></a>
                <a href="/contact?service=Consultation&amp;city=<?php echo urlencode($city_name); ?>" class="btn btn-outline-light btn-lg">Book Free Consultation</a>
            </div>
        </div>
    </header>

    <?php render_trust_signals(); ?>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-white h3 mb-2">Digital Services in <span class="text-neon"><?php echo htmlspecialchars($city_name); ?></span></h2>
                <p class="text-white-50 small mx-auto mb-0" style="max-width:640px;">Browse every service we deliver in <?php echo htmlspecialchars($city_name); ?> — each page includes process, deliverables, FAQs, and a free proposal form.</p>
            </div>
            <div class="row g-4">
                <?php
                $all_services = get_services_data();
                if (is_file(__DIR__ . '/includes/db.local.php') && file_exists(__DIR__ . '/includes/growth/bootstrap.php')) {
                    try {
                        require_once __DIR__ . '/includes/growth/bootstrap.php';
                        if (function_exists('ge_is_ready') && ge_is_ready()) {
                            require_once __DIR__ . '/includes/service-content.php';
                            foreach (\Growth\Models\Service::all(true) as $dbSvc) {
                                if (!isset($all_services[$dbSvc['slug']])) {
                                    $all_services[$dbSvc['slug']] = ge_minimal_service_from_record($dbSvc);
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        // static catalog only
                    }
                }
                foreach ($all_services as $slug => $s):
                    $serviceHref = ge_service_city_landing_url($slug, $city_slug);
                    $silo = $s['silo'] ?? ($s['h1'] ?? 'Service');
                ?>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo htmlspecialchars($serviceHref); ?>" class="text-decoration-none">
                        <div class="svc-benefit-card p-4 h-100">
                            <div class="d-flex align-items-start gap-3 mb-2">
                                <div class="svc-benefit-icon flex-shrink-0"><i class="fas <?php echo htmlspecialchars($s['icon'] ?? 'fa-rocket'); ?>"></i></div>
                                <div>
                                    <span class="svc-badge" style="font-size:0.65rem;padding:0.2rem 0.6rem;"><?php echo htmlspecialchars($silo); ?></span>
                                    <h3 class="text-white h6 mb-1 mt-1"><?php echo htmlspecialchars($silo . ' in ' . $city_name); ?></h3>
                                </div>
                            </div>
                            <p class="text-white-50 small mb-2"><?php echo htmlspecialchars(mb_substr($s['intro'] ?? '', 0, 120)); ?>…</p>
                            <span class="text-neon small fw-semibold">View Full Page →</span>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 bg-darker border-top border-secondary">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="text-white h3 mb-4">Why <?php echo htmlspecialchars($city_name); ?> Businesses Choose Nectra Digital</h2>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-3"><i class="fas fa-check-circle text-neon me-2"></i> Local SEO expertise targeting <?php echo htmlspecialchars($city_name); ?> keywords and map pack rankings</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-neon me-2"></i> 5+ years experience with 200+ successful projects across India</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-neon me-2"></i> Dedicated account manager with <?php echo htmlspecialchars($city_name); ?> market knowledge</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-neon me-2"></i> Transparent monthly reporting with clear ROI metrics</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-neon me-2"></i> Full-stack capabilities: SEO + Ads + Development + AI under one roof</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="p-4 border border-neon rounded bg-glass">
                        <h3 class="text-neon h6 mb-3"><i class="fas fa-bolt me-2"></i>Quick Answer</h3>
                        <p class="text-white small mb-0">Nectra Digital is among the best SEO and digital marketing companies serving <?php echo htmlspecialchars($city_name); ?>, <?php echo htmlspecialchars($city_state); ?> — with proven 340%+ traffic growth, AI automation, and web development under one roof.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 border-top border-secondary">
        <div class="container">
            <h2 class="text-white h5 mb-4">We Also Serve</h2>
            <div class="d-flex flex-wrap gap-2">
                <?php
                $allCities = get_cities_data();
                foreach ($allCities as $slug => $c):
                    if ($slug === $city_slug) continue;
                ?>
                <a href="<?php echo ge_city_hub_path($slug); ?>" class="badge bg-dark border border-secondary text-white-50 p-2 text-decoration-none hover-effect"><?php echo htmlspecialchars($c['name']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 border-top border-secondary">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-6">
                    <h2 class="text-white h3 mb-3">Get a Free Audit in <span class="text-neon"><?php echo htmlspecialchars($city_name); ?></span></h2>
                    <p class="text-white-50 mb-0">Tell us about your business in <?php echo htmlspecialchars($city_name); ?> — we will share a tailored SEO and digital marketing plan with clear next steps.</p>
                </div>
                <div class="col-lg-6">
                    <div class="p-4 border border-secondary rounded bg-glass">
                        <?php
                        $form_service = 'Digital Marketing';
                        $form_city = $city_name;
                        $form_instance = 'city-hub';
                        include __DIR__ . '/includes/partials/contact-form-inline.php';
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php render_faq_section($city_faqs, "SEO & Digital Marketing FAQ — {$city_name}"); ?>
    <?php render_cta_blocks('compact'); ?>
    <?php render_founder_section(true); ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
