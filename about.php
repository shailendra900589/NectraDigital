<?php 
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';

$page_title = "About Nectra Digital | Founder Ravindra Kumar Chauhan — SEO Expert India";
$page_desc = "Meet Ravindra Kumar Chauhan, Founder & CEO of Nectra Digital. 5+ years expertise in SEO, digital marketing, AI automation, and software development. India's trusted digital transformation company.";
$page_keys = "About Nectra Digital, Ravindra Kumar Chauhan, SEO Expert India, Digital Marketing Founder, AI Agency India Founder";
require_once 'includes/i18n.php';
$canonical_url = nectra_page_canonical('/');

require_once __DIR__ . '/includes/eeat-copy.php';

$page_schema = [
    get_breadcrumb_schema([
        ['name' => 'Home', 'url' => SITE_URL . '/'],
        ['name' => 'About', 'url' => SITE_URL . '/about']
    ]),
    get_founder_schema(),
];

include 'includes/header.php';
?>

<main>
    <header class="d-flex align-items-center justify-content-center text-center position-relative overflow-hidden" style="min-height: 55vh; background: #050505;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at 50% 50%, rgba(0, 229, 255, 0.05) 0%, rgba(5,5,5,1) 70%);"></div>
        <div class="container position-relative z-1">
            <?php render_breadcrumbs([['name' => 'Home', 'url' => '/'], ['name' => 'About', 'url' => '/about']]); ?>
            <span class="badge border border-neon text-neon px-3 py-2 mb-3 rounded-pill">Founder-Led Agency · Editorial Standards</span>
            <h1 class="display-4 fw-bold text-white mb-3">About Nectra Digital — <span class="text-neon">SEO Expert India</span></h1>
            <p class="lead text-white-50 mx-auto" style="max-width: 700px;">A founder-led digital marketing and software development company engineering measurable growth through SEO, AI automation, and performance marketing.</p>
        </div>
    </header>

    <section class="py-5 border-top border-secondary">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-4 mb-4 mb-lg-0 text-center">
                    <div class="mx-auto rounded-circle border border-neon d-flex align-items-center justify-content-center bg-dark" style="width:220px;height:220px;" itemscope itemtype="https://schema.org/Person">
                        <meta itemprop="name" content="<?php echo FOUNDER_NAME; ?>">
                        <meta itemprop="jobTitle" content="<?php echo FOUNDER_TITLE; ?>">
                        <i class="fas fa-user-tie fa-5x text-neon opacity-75"></i>
                    </div>
                </div>
                <div class="col-lg-8">
                    <h2 class="text-white h3 mb-2"><?php echo FOUNDER_NAME; ?></h2>
                    <p class="text-neon text-uppercase small mb-3"><?php echo FOUNDER_TITLE; ?> · <?php echo FOUNDER_EXPERIENCE; ?> Experience</p>
                    <p class="text-white-50 mb-4">Ravindra Kumar Chauhan founded Nectra Digital with a clear mission: bridge the gap between complex technology and measurable business profit. With <?php echo FOUNDER_EXPERIENCE; ?> of hands-on experience in SEO, digital marketing, AI automation, web development, and software development, he has built Nectra Digital into one of India's most trusted digital agencies — serving 200+ clients with an average 340% organic traffic growth.</p>
                    <p class="text-white-50 mb-4">"We don't sell templates. We build digital assets engineered for speed, security, and scale. Every SEO strategy, every line of code, every marketing campaign is designed to generate ROI — not vanity metrics."</p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <?php foreach (FOUNDER_EXPERTISE as $skill): ?>
                        <span class="badge bg-dark border border-secondary text-white-50"><?php echo $skill; ?></span>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?php echo FOUNDER_LINKEDIN; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-nectra btn-sm"><i class="fab fa-linkedin me-2"></i>Connect on LinkedIn</a>
                    <a href="/contact" class="btn btn-outline-light btn-sm ms-2">Schedule Strategy Call</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-darker border-top border-secondary">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="text-white">Our <span class="text-neon">Code</span></h2>
                <p class="text-white-50">Three principles governing every project we deliver.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-4 border border-secondary rounded bg-glass h-100 text-center">
                        <i class="fas fa-tachometer-alt fa-2x text-neon mb-3"></i>
                        <h3 class="text-white h5">Speed Is Priority</h3>
                        <p class="text-white-50 small mb-0">Millisecond performance in websites, SEO results within 90 days, and rapid project delivery without compromising quality.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border border-secondary rounded bg-glass h-100 text-center">
                        <i class="fas fa-shield-alt fa-2x text-neon mb-3"></i>
                        <h3 class="text-white h5">Transparency & Editorial Standards</h3>
                        <p class="text-white-50 small mb-0">Expert-authored content, verified credentials, published editorial guidelines, and transparent reporting on KPIs that matter.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border border-secondary rounded bg-glass h-100 text-center">
                        <i class="fas fa-chart-line fa-2x text-neon mb-3"></i>
                        <h3 class="text-white h5">ROI or Nothing</h3>
                        <p class="text-white-50 small mb-0">We measure success in revenue, leads, and conversions. If it doesn't generate business value, we don't do it.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 border-top border-secondary">
        <div class="container py-4">
            <h2 class="text-white h4 mb-4">Contact Authority</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-4 border border-secondary rounded bg-glass">
                        <i class="fas fa-map-marker-alt text-neon mb-2"></i>
                        <h3 class="text-white h6">Headquarters</h3>
                        <p class="text-white-50 small mb-0">Lucknow, Uttar Pradesh, India<br>Serving 20+ cities nationwide</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border border-secondary rounded bg-glass">
                        <i class="fas fa-envelope text-neon mb-2"></i>
                        <h3 class="text-white h6">Direct Contact</h3>
                        <p class="text-white-50 small mb-0"><?php require_once __DIR__ . '/includes/site-contact.php'; echo nectra_email_html_link('text-neon'); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border border-secondary rounded bg-glass">
                        <i class="fas fa-file-alt text-neon mb-2"></i>
                        <h3 class="text-white h6">Legal & Editorial</h3>
                        <p class="text-white-50 small mb-0">
                            <a href="/editorial-guidelines" class="text-neon d-block">Editorial Guidelines</a>
                            <a href="/privacy" class="text-white-50 d-block">Privacy Policy</a>
                            <a href="/terms" class="text-white-50 d-block">Terms & Conditions</a>
                            <a href="/disclaimer" class="text-white-50 d-block">Disclaimer</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php render_money_services_strip(['seo-services', 'performance-marketing-services', 'web-development-services', 'ai-automation-services'], 'Services We Deliver'); ?>
    <?php render_cta_blocks('compact'); ?>
</main>

<?php include 'includes/footer.php'; ?>
