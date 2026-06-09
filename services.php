<?php 
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';
require_once 'includes/service-content.php';

$page_title = "All Digital Services | SEO, AI Automation, Web Development India";
$page_desc = "Complete digital services: SEO services India, local SEO, technical SEO, AI automation, Google Ads, Meta Ads, web development, software development, mobile apps, and ecommerce.";
$page_keys = "Digital Services India, SEO Services, AI Automation, Web Development, Software Development, Digital Marketing Agency";

$page_schema = [get_breadcrumb_schema([
    ['name' => 'Home', 'url' => SITE_URL . '/'],
    ['name' => 'Services', 'url' => SITE_URL . '/services']
])];

$all_services = get_services_data();
$silo_groups = [
    'SEO & Growth' => ['seo-services', 'local-seo-services', 'technical-seo-services', 'enterprise-seo-services'],
    'Performance Marketing' => ['digital-marketing-services', 'ppc-management', 'google-ads-management', 'meta-ads-services'],
    'AI & Automation' => ['ai-automation-services', 'ai-chatbot-development', 'whatsapp-ai-bot-development'],
    'Development' => ['web-development-services', 'software-development-services', 'mobile-app-development', 'ecommerce-development'],
];

include 'includes/header.php';
?>

<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/service-pages.css">

<header class="svc-hero" style="min-height:55vh">
    <div class="svc-hero-glow"></div>
    <div class="container text-center position-relative z-1 py-5">
        <?php render_breadcrumbs([['name' => 'Home', 'url' => '/'], ['name' => 'Services', 'url' => '/services']]); ?>
        <span class="svc-badge">15 Specialized Services</span>
        <h1 class="display-4 fw-bold text-white mb-4">Full-Service <span class="text-neon">Digital Agency</span></h1>
        <p class="lead text-white-50 mx-auto mb-4" style="max-width:800px">SEO, AI automation, performance marketing, web development, and software engineering — one strategic partner accountable for your entire growth engine.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="/contact?service=Consultation" class="btn btn-nectra btn-lg">Book Free Consultation</a>
            <a href="/contact?service=Free+Audit" class="btn btn-outline-light btn-lg">Get Free Audit</a>
        </div>
    </div>
</header>

<section class="svc-trust py-4 border-top border-bottom border-secondary">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-6 col-md-3"><div class="svc-stat-val text-neon">15</div><div class="svc-stat-lbl text-white-50 small">Services</div></div>
            <div class="col-6 col-md-3"><div class="svc-stat-val text-neon">200+</div><div class="svc-stat-lbl text-white-50 small">Projects</div></div>
            <div class="col-6 col-md-3"><div class="svc-stat-val text-neon">340%</div><div class="svc-stat-lbl text-white-50 small">Avg. Growth</div></div>
            <div class="col-6 col-md-3"><div class="svc-stat-val text-neon">4.9★</div><div class="svc-stat-lbl text-white-50 small">Client Rating</div></div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="text-white h3">Explore Our <span class="text-neon">Services</span></h2>
            <p class="text-white-50 mx-auto" style="max-width:650px">Every service page includes detailed process, deliverables, FAQs, and industry expertise — so you know exactly what you're getting.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($all_services as $slug => $service):
                $ext = get_service_extended($slug, $service);
                $tagline = $ext['tagline'] ?? $service['silo'];
            ?>
            <div class="col-md-6 col-lg-4">
                <a href="/<?php echo $slug; ?>" class="text-decoration-none d-block h-100">
                    <div class="svc-benefit-card p-4 h-100">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="svc-benefit-icon flex-shrink-0"><i class="fas <?php echo $service['icon']; ?>"></i></div>
                            <div>
                                <span class="svc-badge" style="font-size:0.65rem;padding:0.2rem 0.6rem;margin-bottom:0.5rem"><?php echo htmlspecialchars($service['silo']); ?></span>
                                <h2 class="text-white h6 mb-1"><?php echo htmlspecialchars($service['h1']); ?></h2>
                                <p class="text-neon small mb-0" style="font-size:0.75rem"><?php echo htmlspecialchars($tagline); ?></p>
                            </div>
                        </div>
                        <p class="text-white-50 small mb-3"><?php echo htmlspecialchars(mb_substr($service['intro'], 0, 130)); ?>...</p>
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <?php foreach (array_slice($service['features'] ?? [], 0, 3) as $feat): ?>
                            <span class="svc-tool-badge" style="font-size:0.7rem;padding:0.25rem 0.6rem"><?php echo htmlspecialchars($feat); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <span class="text-neon small fw-semibold">View Full Service →</span>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php foreach ($silo_groups as $group_name => $slugs): ?>
<section class="py-5 <?php echo ($group_name === 'Performance Marketing' || $group_name === 'Development') ? 'bg-darker border-top border-secondary' : ''; ?>">
    <div class="container">
        <h2 class="text-white h4 mb-2"><?php
            if (strpos($group_name, ' & ') !== false) {
                [$a, $b] = explode(' & ', $group_name, 2);
                echo htmlspecialchars($a) . ' <span class="text-neon">' . htmlspecialchars($b) . '</span>';
            } else {
                echo '<span class="text-neon">' . htmlspecialchars($group_name) . '</span>';
            }
        ?></h2>
        <p class="text-white-50 small mb-4">Specialized <?php echo strtolower($group_name); ?> solutions for Indian and global businesses.</p>
        <div class="row g-3">
            <?php foreach ($slugs as $slug):
                if (!isset($all_services[$slug])) continue;
                $s = $all_services[$slug];
            ?>
            <div class="col-md-6 col-lg-3">
                <a href="/<?php echo $slug; ?>" class="d-flex align-items-center p-3 border border-secondary rounded text-decoration-none hover-effect h-100 svc-deliverable">
                    <i class="fas <?php echo $s['icon']; ?> text-neon me-3 fa-lg"></i>
                    <span class="text-white small"><?php echo htmlspecialchars($s['h1']); ?></span>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endforeach; ?>

<section id="locations" class="py-5 bg-darker border-top border-secondary">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="text-white h3 mb-2">Service Locations Across <span class="text-neon">India</span></h2>
            <p class="text-white-50 small mx-auto mb-0" style="max-width:620px;">Local SEO, digital marketing, and development services in major cities — each location hub links to every service page for that city.</p>
        </div>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <?php foreach (get_cities_data() as $slug => $c): ?>
            <a href="/digital-agency-<?php echo $slug; ?>" class="badge bg-dark border border-secondary text-white-50 p-2 text-decoration-none hover-effect"><?php echo htmlspecialchars($c['name']); ?><?php if (!empty($c['state'])): ?> <span class="opacity-75">· <?php echo htmlspecialchars($c['state']); ?></span><?php endif; ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5 border-top border-secondary">
    <div class="container text-center">
        <h2 class="text-white h4 mb-3">Not Sure Which Service You Need?</h2>
        <p class="text-white-50 mb-4 mx-auto" style="max-width:550px">Book a free strategy call. We'll audit your current performance and recommend the right mix of SEO, ads, AI, and development.</p>
        <a href="/contact?service=Strategy+Call" class="btn btn-nectra btn-lg">Book Free Strategy Call</a>
    </div>
</section>

<?php render_cta_blocks(); ?>
<?php render_founder_section(true); ?>
<?php render_trust_signals(); ?>

<?php include 'includes/footer.php'; ?>
