<?php
/**
 * Dynamic city landing page template
 * Accessed via /digital-agency-{city}
 */
require_once __DIR__ . '/includes/seo-data.php';
require_once __DIR__ . '/includes/seo-components.php';

$city_slug = isset($_GET['city']) ? preg_replace('/[^a-z]/', '', strtolower($_GET['city'])) : '';
$cities = get_cities_data();

if (!$city_slug || !isset($cities[$city_slug])) {
    header('Location: /404.php');
    exit;
}

$city = $cities[$city_slug];
$city_name = $city['name'];
$city_state = $city['state'];
$is_hq = !empty($city['is_hq']);

$page_title = "Best Digital Marketing & SEO Company in {$city_name} | Nectra Digital";
$page_desc = "Top SEO company and digital marketing agency in {$city_name}, {$city_state}. Expert SEO services, AI automation, web development & lead generation. Free consultation.";
$page_keys = "SEO company {$city_name}, best SEO company in {$city_name}, digital marketing agency {$city_name}, web development company {$city_name}, website development {$city_name}, PPC agency {$city_name}, local SEO {$city_name}, AI automation {$city_name}, hire SEO {$city_name}, SEO services {$city_name}";

$breadcrumbs = [
    ['name' => 'Home', 'url' => SITE_URL . '/'],
    ['name' => 'Locations', 'url' => SITE_URL . '/services'],
    ['name' => $city_name, 'url' => SITE_URL . '/digital-agency-' . $city_slug]
];

$city_faqs = [
    ['q' => "Why choose Nectra Digital for SEO in {$city_name}?", 'a' => "Nectra Digital delivers proven SEO results for {$city_name} businesses with local keyword targeting, Google Business Profile optimization, and city-specific content strategies. Our team understands the {$city_name} market dynamics and competition landscape."],
    ['q' => "What digital marketing services do you offer in {$city_name}?", 'a' => "We offer complete digital marketing services in {$city_name} including SEO, local SEO, Google Ads, Meta Ads, social media marketing, AI automation, web development, and lead generation — all customized for the {$city_name} market."],
    ['q' => "Do you have a physical office in {$city_name}?", 'a' => $is_hq ? "Yes! {$city_name} is our global headquarters. We operate from Lucknow, Uttar Pradesh, serving clients across {$city_name} and nationwide." : "We serve {$city_name} clients remotely with dedicated account managers, video consultations, and on-site meetings when required. Our HQ is in Lucknow with pan-India operations."],
    ['q' => "How much do SEO services cost in {$city_name}?", 'a' => "SEO packages for {$city_name} businesses start from ₹15,000/month for local businesses. Enterprise and competitive industries may require ₹50,000-1,00,000/month. We provide free SEO audits with customized pricing."],
    ['q' => "How quickly can you start a project in {$city_name}?", 'a' => "We can initiate projects within 48 hours of agreement. Our onboarding process includes a discovery call, audit, strategy presentation, and kickoff — typically completed within one week."]
];

include __DIR__ . '/includes/header.php';
render_local_business_schema($city, $city_slug);
output_faq_schema($city_faqs);
?>

<main>
    <header class="py-5 d-flex align-items-center" style="min-height: 45vh; background: linear-gradient(to bottom, #050505 0%, #0a1518 100%);">
        <div class="container">
            <?php render_breadcrumbs($breadcrumbs); ?>
            <h6 class="text-neon text-uppercase mb-2"><?php echo $is_hq ? 'Global Headquarters' : 'Local Operations'; ?></h6>
            <h1 class="display-5 fw-bold text-white mb-3">Best SEO & Digital Marketing Company in <span class="text-neon"><?php echo htmlspecialchars($city_name); ?></span></h1>
            <p class="lead text-white-50 mb-4 mx-auto" style="max-width: 800px;">
                Nectra Digital is a leading SEO company and digital marketing agency serving businesses in <?php echo htmlspecialchars($city_name); ?>, <?php echo htmlspecialchars($city_state); ?>. 
                We deliver search engine optimization, AI automation, web development, and performance marketing that drives measurable ROI.
            </p>
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="/contact?city=<?php echo urlencode($city_name); ?>" class="btn btn-nectra">Get Free SEO Audit in <?php echo htmlspecialchars($city_name); ?></a>
                <a href="/contact?service=Consultation" class="btn btn-outline-light">Book Free Consultation</a>
            </div>
        </div>
    </header>

    <?php render_trust_signals(); ?>

    <section class="py-5">
        <div class="container">
            <h2 class="text-white h3 mb-4 text-center">Digital Services in <span class="text-neon"><?php echo htmlspecialchars($city_name); ?></span></h2>
            <div class="row g-4">
                <?php
                $featured = ['seo-services', 'local-seo-services', 'digital-marketing-services', 'ai-automation-services', 'web-development-services', 'ppc-management'];
                $all_services = get_services_data();
                foreach ($featured as $slug):
                    if (!isset($all_services[$slug])) continue;
                    $s = $all_services[$slug];
                    $serviceHref = ge_service_city_landing_url($slug, $city_slug);
                ?>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo htmlspecialchars($serviceHref); ?>" class="text-decoration-none">
                        <div class="p-4 border border-secondary rounded bg-glass h-100 hover-effect">
                            <i class="fas <?php echo $s['icon']; ?> text-neon fa-2x mb-3"></i>
                            <h3 class="text-white h6"><?php echo htmlspecialchars($s['h1']); ?></h3>
                            <p class="text-white-50 small mb-0"><?php echo htmlspecialchars(mb_substr($s['intro'], 0, 120)); ?>...</p>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 bg-darker border-top border-secondary">
        <div class="container">
            <div class="row align-items-center">
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
                        <p class="text-white small mb-0">Nectra Digital is among the best SEO companies serving <?php echo htmlspecialchars($city_name); ?>, offering comprehensive digital marketing, AI automation, and web development services with proven 340%+ traffic growth results.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 border-top border-secondary">
        <div class="container">
            <h2 class="text-white h5 mb-4">We Also Serve</h2>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($cities as $slug => $c): if ($slug === $city_slug) continue; ?>
                <a href="/digital-agency-<?php echo $slug; ?>" class="badge bg-dark border border-secondary text-white-50 p-2 text-decoration-none hover-effect"><?php echo htmlspecialchars($c['name']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php render_faq_section($city_faqs, "SEO & Digital Marketing FAQ — {$city_name}"); ?>
    <?php render_cta_blocks('compact'); ?>
    <?php render_founder_section(true); ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
