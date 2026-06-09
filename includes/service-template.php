<?php
/**
 * Shared template for all service pages
 * Usage: $service_slug = 'seo-services'; require 'includes/service-template.php';
 */
require_once __DIR__ . '/seo-data.php';
require_once __DIR__ . '/seo-components.php';

if (!isset($service_slug)) {
    header('Location: /services');
    exit;
}

$services = get_services_data();
if (!isset($services[$service_slug])) {
    header('Location: /404.php');
    exit;
}

$service = $services[$service_slug];
$page_title = $service['title'];
$page_desc = $service['meta_desc'];
$page_keys = $service['keywords'];

$breadcrumbs = [
    ['name' => 'Home', 'url' => SITE_URL . '/'],
    ['name' => 'Services', 'url' => SITE_URL . '/services'],
    ['name' => $service['h1'], 'url' => SITE_URL . '/' . $service_slug]
];
$page_schema = [get_breadcrumb_schema($breadcrumbs)];

include __DIR__ . '/header.php';
render_service_schema($service_slug, $service);
?>

<main>
    <header class="py-5 d-flex align-items-center" style="min-height: 45vh; background: linear-gradient(to bottom, #050505 0%, #0a1518 100%);">
        <div class="container">
            <?php render_breadcrumbs($breadcrumbs); ?>
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h6 class="text-neon text-uppercase mb-2" style="letter-spacing: 2px;"><?php echo $service['silo']; ?> Services</h6>
                    <h1 class="display-5 fw-bold text-white mb-3"><?php echo htmlspecialchars($service['h1']); ?></h1>
                    <p class="lead text-white-50 mb-4"><?php echo htmlspecialchars($service['intro']); ?></p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="/contact?service=<?php echo urlencode($service['h1']); ?>" class="btn btn-nectra">Get Free Audit</a>
                        <a href="/contact?service=Consultation" class="btn btn-outline-light">Book Consultation</a>
                    </div>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="fas <?php echo $service['icon']; ?> fa-6x text-neon opacity-50"></i>
                </div>
            </div>
        </div>
    </header>

    <section class="py-5 border-top border-secondary">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <h2 class="text-white h4 mb-4">What's <span class="text-neon">Included</span></h2>
                    <div class="row g-3">
                        <?php foreach ($service['features'] as $feature): ?>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start p-3 border border-secondary rounded bg-glass">
                                <i class="fas fa-check-circle text-neon me-3 mt-1"></i>
                                <span class="text-white small"><?php echo htmlspecialchars($feature); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-5 p-4 border border-neon rounded bg-glass">
                        <h2 class="text-neon h6 text-uppercase mb-2"><i class="fas fa-bolt me-2"></i>Quick Answer</h2>
                        <p class="text-white mb-0"><?php echo htmlspecialchars($service['intro']); ?></p>
                    </div>

                    <div class="mt-4 p-4 border border-secondary rounded bg-dark">
                        <h2 class="text-white h6 mb-3"><i class="fas fa-lightbulb text-neon me-2"></i>Expert Insight</h2>
                        <p class="text-white-50 small fst-italic">"<?php echo FOUNDER_NAME; ?>, <?php echo FOUNDER_TITLE; ?>: Our <?php echo strtolower($service['h1']); ?> approach is built on data, not guesswork. Every strategy is customized to your market, competition, and growth goals."</p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="p-4 border border-secondary rounded bg-glass sticky-top" style="top: 100px;">
                        <h3 class="text-white h6 mb-3">Start Your Project</h3>
                        <ul class="list-unstyled text-white-50 small mb-4">
                            <li class="mb-2"><i class="fas fa-check text-neon me-2"></i>Free initial consultation</li>
                            <li class="mb-2"><i class="fas fa-check text-neon me-2"></i>Custom strategy proposal</li>
                            <li class="mb-2"><i class="fas fa-check text-neon me-2"></i>Transparent monthly reporting</li>
                            <li class="mb-2"><i class="fas fa-check text-neon me-2"></i>No long-term lock-in</li>
                        </ul>
                        <a href="/contact?service=<?php echo urlencode($service['h1']); ?>" class="btn btn-nectra w-100 mb-2">Request Proposal</a>
                        <a href="/contact?service=Strategy+Call" class="btn btn-outline-light w-100 btn-sm">Schedule Strategy Call</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php render_faq_section($service['faqs'], $service['h1'] . ' — FAQ'); ?>
    <?php render_internal_links_service($service['related']); ?>
    <?php render_cta_blocks('compact'); ?>
    <?php render_founder_section(true); ?>
</main>

<?php include __DIR__ . '/footer.php'; ?>
