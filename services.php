<?php 
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';

$page_title = "All Digital Services | SEO, AI Automation, Web Development India";
$page_desc = "Complete digital services: SEO services India, local SEO, technical SEO, AI automation, Google Ads, Meta Ads, web development, software development, mobile apps, and ecommerce.";
$page_keys = "Digital Services India, SEO Services, AI Automation, Web Development, Software Development, Digital Marketing Agency";

$page_schema = [get_breadcrumb_schema([
    ['name' => 'Home', 'url' => SITE_URL . '/'],
    ['name' => 'Services', 'url' => SITE_URL . '/services']
])];

$all_services = get_services_data();
include 'includes/header.php';
?>

<header class="py-5 d-flex align-items-center" style="min-height: 45vh; background: linear-gradient(to bottom, #050505 0%, #0a1518 100%);">
    <div class="container text-center">
        <?php render_breadcrumbs([['name' => 'Home', 'url' => '/'], ['name' => 'Services', 'url' => '/services']]); ?>
        <h1 class="display-4 fw-bold text-white mb-4">Full-Service <span class="text-neon">Digital Agency</span></h1>
        <p class="lead text-white-50 mx-auto" style="max-width: 800px;">15 specialized services spanning SEO, AI automation, performance marketing, web development, and software engineering — all under one roof.</p>
        <a href="/contact?service=Consultation" class="btn btn-nectra mt-3">Book Free Consultation</a>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($all_services as $slug => $service): ?>
            <div class="col-md-6 col-lg-4">
                <a href="/<?php echo $slug; ?>" class="text-decoration-none">
                    <div class="p-4 border border-secondary rounded bg-glass h-100 hover-effect">
                        <i class="fas <?php echo $service['icon']; ?> text-neon fa-2x mb-3"></i>
                        <h2 class="text-white h6"><?php echo htmlspecialchars($service['h1']); ?></h2>
                        <p class="text-white-50 small mb-2"><?php echo htmlspecialchars(mb_substr($service['intro'], 0, 140)); ?>...</p>
                        <span class="text-neon small">Learn More →</span>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="marketing" class="py-5 bg-darker border-top border-secondary">
    <div class="container">
        <h2 class="text-white h4 mb-4">Growth, SEO & <span class="text-neon">Lead Generation</span></h2>
        <p class="text-white-50 mb-4">Precision-engineered traffic and client acquisition services.</p>
        <div class="row g-3">
            <?php foreach (['seo-services', 'local-seo-services', 'technical-seo-services', 'enterprise-seo-services', 'digital-marketing-services', 'ppc-management', 'google-ads-management', 'meta-ads-services'] as $slug): ?>
            <?php if (isset($all_services[$slug])): $s = $all_services[$slug]; ?>
            <div class="col-md-6 col-lg-3">
                <a href="/<?php echo $slug; ?>" class="d-block p-3 border border-secondary rounded text-decoration-none hover-effect">
                    <i class="fas <?php echo $s['icon']; ?> text-neon me-2"></i>
                    <span class="text-white small"><?php echo htmlspecialchars($s['h1']); ?></span>
                </a>
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>
</section>

<section id="web" class="py-5 border-top border-secondary">
    <div class="container">
        <h2 class="text-white h4 mb-4">Web & <span class="text-neon">Software Development</span></h2>
        <div class="row g-3">
            <?php foreach (['web-development-services', 'software-development-services', 'mobile-app-development', 'ecommerce-development'] as $slug): ?>
            <?php if (isset($all_services[$slug])): $s = $all_services[$slug]; ?>
            <div class="col-md-6 col-lg-3">
                <a href="/<?php echo $slug; ?>" class="d-block p-3 border border-secondary rounded text-decoration-none hover-effect">
                    <i class="fas <?php echo $s['icon']; ?> text-neon me-2"></i>
                    <span class="text-white small"><?php echo htmlspecialchars($s['h1']); ?></span>
                </a>
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>
</section>

<section id="ai" class="py-5 bg-darker border-top border-secondary">
    <div class="container">
        <h2 class="text-white h4 mb-4">AI <span class="text-neon">Automation</span></h2>
        <div class="row g-3">
            <?php foreach (['ai-automation-services', 'ai-chatbot-development', 'whatsapp-ai-bot-development'] as $slug): ?>
            <?php if (isset($all_services[$slug])): $s = $all_services[$slug]; ?>
            <div class="col-md-6 col-lg-4">
                <a href="/<?php echo $slug; ?>" class="d-block p-3 border border-secondary rounded text-decoration-none hover-effect">
                    <i class="fas <?php echo $s['icon']; ?> text-neon me-2"></i>
                    <span class="text-white small"><?php echo htmlspecialchars($s['h1']); ?></span>
                </a>
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>
</section>

<?php render_cta_blocks(); ?>

<?php include 'includes/footer.php'; ?>
