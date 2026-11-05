<?php 
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';
require_once __DIR__ . '/includes/i18n.php';

$page_title = "Legal Disclaimer & Results Notice — Nectra Digital";
$page_desc = "Nectra Digital legal notice covering performance indicators, SEO rankings expectations, professional advisory limits, and third-party platform dependencies.";
$page_keys = "Disclaimer, Legal Notice, Nectra Digital Disclaimer, Service Disclaimers";
$canonical_url = nectra_page_canonical('/disclaimer');

include 'includes/header.php';
?>

<main class="bg-light">
    <!-- Hero Header -->
    <header class="nd-page-hero nd-legal-hero text-center">
        <div class="container py-2 py-md-3">
            <h1 class="fw-bold text-white mb-2 nd-hero-title">Legal Disclaimer</h1>
            <p class="text-white-50 small mb-0">Last Updated: <?php echo date("F Y"); ?> &middot; Regulatory Information</p>
        </div>
    </header>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="mb-3">
                    <?php render_breadcrumbs([['name' => 'Home', 'url' => '/'], ['name' => 'Disclaimer', 'url' => '/disclaimer']], 'light'); ?>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white text-secondary nd-legal-body">
                    <h3 class="h5 fw-bold text-dark mb-3">1. Informational & Educational Purposes</h3>
                    <p class="mb-4">
                        The content published across nectradigital.com, including technical insights, guides, benchmark statistics, and service descriptions, is provided for general informational and educational purposes. While we maintain rigorous engineering standards, Nectra Digital makes no express or implied warranties regarding universal suitability for every business infrastructure.
                    </p>

                    <h3 class="h5 fw-bold text-dark mb-3">2. SEO & Digital Marketing Growth Notice</h3>
                    <p class="mb-4">
                        Search engine optimization (SEO), answer engine optimization (AEO), and digital marketing performance fluctuate based on numerous factors outside any single entity's direct control, including industry saturation, existing domain authority, algorithm updates, and market competition. Published client case studies illustrate verified historical results achieved under specific parameters. In adherence to Google's Webmaster Guidelines, no agency can legitimately guarantee an absolute #1 search position.
                    </p>

                    <h3 class="h5 fw-bold text-dark mb-3">3. Professional Consultation & Advisory</h3>
                    <p class="mb-4">
                        The information provided on this platform does not constitute certified legal, tax, or investment advice. Readers are advised to consult qualified specialists before making critical capital allocations based on software architectural trends or digital marketing recommendations.
                    </p>

                    <h3 class="h5 fw-bold text-dark mb-3">4. Third-Party Integrations & External Links</h3>
                    <p class="mb-4">
                        Our website and client deliverables may integrate with or link to third-party services (such as Google APIs, OpenAI, Meta Ads, WhatsApp Business, or payment gateways). Nectra Digital does not manage, endorse, or accept liability for third-party terms of service, uptime levels, or privacy behaviors.
                    </p>

                    <div class="border-top pt-4 mt-5 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Corporate Legal Office</h6>
                            <p class="small text-muted mb-0">For formal legal correspondence or regulatory inquiries, contact our counsel.</p>
                        </div>
                        <a href="mailto:legal@nectradigital.com" class="btn btn-outline-primary btn-sm px-3 rounded-pill flex-shrink-0">
                            <i class="fas fa-envelope me-1"></i> legal@nectradigital.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
