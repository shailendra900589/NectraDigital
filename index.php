<?php 
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';
require_once 'includes/i18n.php';
require_once 'includes/eeat-copy.php';

$page_title = "SEO & Digital Marketing Agency India | Nectra Digital";
$page_desc = "Nectra Digital — SEO, performance marketing, AI automation, web & software development. 200+ projects, 340% avg. growth. Free audit & strategy call.";
$page_keys = "SEO Company India, search engine optimization India, SEO services India, digital marketing agency India, AI automation, software development company India";
$canonical_url = nectra_page_canonical('/');

$page_schema = [
    get_breadcrumb_schema([['name' => 'Home', 'url' => SITE_URL . '/']]),
];
require_once __DIR__ . '/includes/site-contact.php';
$page_schema[] = get_home_local_business_schema();

include 'includes/header.php';
output_faq_schema(get_homepage_faqs());
?>

<header class="d-flex align-items-center justify-content-center text-center position-relative" style="min-height: 75vh; background: #050505; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, rgba(0, 229, 255, 0.1) 0%, rgba(5,5,5,1) 70%); pointer-events: none;"></div>
    
    <div class="container position-relative z-1">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-inline-block border border-neon rounded-pill px-3 py-1 mb-4 bg-dark">
                    <small class="text-neon text-uppercase" style="letter-spacing: 2px;"><i class="fas fa-chart-line me-2"></i> 340% Avg. Organic Growth · 200+ Projects · <?php echo nectra_market_city_count(); ?>+ Cities</small>
                </div>
                
                <h1 class="display-3 fw-bold text-white mb-3" style="text-shadow: 0 0 20px rgba(0,0,0,0.8);">
                    SEO &amp; <span class="text-neon" style="text-shadow: 0 0 15px var(--nectra-neon);">Digital Marketing Agency</span> in India
                </h1>
                <h2 class="h4 text-white-50 fw-normal mb-4 mx-auto" style="max-width: 820px;">
                    SEO, paid media, AI automation, and custom software — one team focused on leads, revenue, and measurable ROI.
                </h2>
                
                <p class="lead text-white-50 mb-4 mx-auto" style="max-width: 800px; line-height: 1.7;">
                    We help Indian and global brands rank on Google, convert traffic into qualified leads, and scale with automation and engineering support.
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                    <a href="/contact?service=SEO+Audit" class="btn btn-nectra btn-lg">Get Free SEO Audit</a>
                    <a href="/seo-services" class="btn btn-outline-light btn-lg">Explore SEO Services</a>
                </div>

                <div class="d-flex flex-wrap justify-content-center gap-2 mb-2">
                    <?php
                    $hero_links = [
                        'seo-services' => 'SEO',
                        'performance-marketing-services' => 'Paid Media',
                        'social-media-marketing-services' => 'Social Media',
                        'ai-automation-services' => 'AI Automation',
                        'web-development-services' => 'Web Dev',
                        'software-development-services' => 'Software',
                    ];
                    foreach ($hero_links as $slug => $label): ?>
                    <a href="/<?php echo htmlspecialchars($slug); ?>" class="badge bg-dark border border-secondary text-white-50 p-2 text-decoration-none hover-effect"><?php echo htmlspecialchars($label); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</header>

<?php render_trust_signals(); ?>

<section class="py-4 border-bottom border-secondary bg-dark">
    <div class="container">
        <div class="row align-items-center text-center text-lg-start">
            <div class="col-lg-2 mb-2 mb-lg-0">
                <span class="text-white-50 small text-uppercase" style="letter-spacing: 1px;">Trusted Tech:</span>
            </div>
            <div class="col-lg-10">
                <div class="d-flex justify-content-around justify-content-lg-start gap-5 opacity-50 grayscale-hover">
                    <i class="fab fa-react fa-2x text-white" title="React Development"></i>
                    <i class="fab fa-aws fa-2x text-white" title="AWS Cloud"></i>
                    <i class="fab fa-python fa-2x text-white" title="Python Development"></i>
                    <i class="fab fa-google fa-2x text-white" title="Google Ads & SEO"></i>
                    <i class="fab fa-shopify fa-2x text-white" title="Ecommerce Development"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<?php render_usp_section(); ?>

<section class="py-5 border-top border-secondary">
    <div class="container py-4">
        <div class="row g-5 align-items-start">
            <div class="col-lg-7">
                <h2 class="text-white h3 mb-4">Grow Organic Traffic &amp; <span class="text-neon">Qualified Leads</span></h2>
                <p class="text-white-50">Nectra Digital is a full-service agency helping brands in India and worldwide improve Google visibility, run profitable ad campaigns, and build websites and apps that convert. Our <a href="/seo-services" class="text-neon text-decoration-none">SEO services</a> cover technical audits, content strategy, local SEO, and link building — with monthly reporting you can act on.</p>
                <p class="text-white-50">Beyond organic search, we manage <a href="/performance-marketing-services" class="text-neon text-decoration-none">performance marketing</a> on Google and Meta, <a href="/social-media-marketing-services" class="text-neon text-decoration-none">social media marketing</a>, and <a href="/software-development-services" class="text-neon text-decoration-none">custom software</a> when your growth plan needs engineering. Whether you target Lucknow, Mumbai, Delhi, or national rankings — we build a roadmap tied to leads and revenue.</p>
                <h3 class="text-white h5 mt-4 mb-3">Why businesses choose Nectra Digital</h3>
                <ul class="text-white-50">
                    <li class="mb-2"><strong class="text-white">Proven SEO results</strong> — 5+ years, 200+ projects, 340% average traffic growth</li>
                    <li class="mb-2"><strong class="text-white">Integrated marketing &amp; tech</strong> — SEO, ads, social, AI, and development under one roof</li>
                    <li class="mb-2"><strong class="text-white">Dedicated account teams</strong> — strategists and specialists, not rotating juniors</li>
                    <li class="mb-2"><strong class="text-white">Local SEO in 20+ cities</strong> — <a href="/local-seo-services" class="text-neon text-decoration-none">map pack rankings</a> and city landing pages</li>
                </ul>
            </div>
            <div class="col-lg-5">
                <?php render_nap_block('home'); ?>
                <div class="mt-4 p-4 border border-neon rounded bg-glass">
                    <h3 class="text-neon h6 mb-2">Free SEO Audit</h3>
                    <p class="text-white-50 small mb-3">Technical SEO review, competitor snapshot, and priority fixes — free, no obligation.</p>
                    <a href="/contact?service=SEO+Audit" class="btn btn-nectra btn-sm w-100">Request Free Audit</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="services" class="py-5 border-top border-secondary">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h6 class="text-neon text-uppercase mb-2">Full-Service Digital Agency</h6>
            <h2 class="text-white">SEO, Marketing & <span class="text-neon">Development Services</span></h2>
            <p class="text-white-50 mx-auto" style="max-width: 700px;">SEO, paid media, AI automation, web development, and software engineering — one partner for traffic and leads.</p>
        </div>

        <div class="row g-4 mb-5">
            <?php
            $home_services = get_services_data();
            foreach (get_primary_services() as $home_slug):
                if (!isset($home_services[$home_slug])) continue;
                $hs = $home_services[$home_slug];
            ?>
            <div class="col-md-6 col-lg-4">
                <a href="/<?php echo htmlspecialchars($home_slug); ?>" class="text-decoration-none">
                    <div class="service-card text-center h-100 p-4 border border-secondary rounded bg-glass hover-effect">
                        <i class="fas <?php echo htmlspecialchars($hs['icon']); ?> fa-2x text-neon mb-3"></i>
                        <h3 class="h5 text-white"><?php echo htmlspecialchars($hs['h1']); ?></h3>
                        <p class="text-white-50 small"><?php echo htmlspecialchars(mb_substr($hs['intro'], 0, 120)); ?>…</p>
                        <span class="text-neon small fw-bold">Explore <?php echo htmlspecialchars($hs['silo']); ?> &rarr;</span>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center">
            <a href="/services" class="btn btn-outline-light">View All 17 Services</a>
        </div>
    </div>
</section>

<section class="py-5 bg-darker border-top border-secondary">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="text-white h3">Answer Engine <span class="text-neon">Optimized</span> Insights</h2>
            <p class="text-white-50 small">Direct answers to the questions businesses ask most — optimized for Google AI Overviews, ChatGPT, and Perplexity.</p>
        </div>
        <div class="row g-4">
            <?php foreach (array_slice(get_aeo_answers(), 0, 3) as $key => $aeo): ?>
            <div class="col-md-4">
                <div class="p-4 border border-secondary rounded bg-glass h-100">
                    <h3 class="text-neon h6 mb-2"><?php echo htmlspecialchars($aeo['question']); ?></h3>
                    <p class="text-white-50 small mb-3"><?php echo htmlspecialchars($aeo['quick_answer']); ?></p>
                    <a href="/aeo#<?php echo $key; ?>" class="text-neon small">Read full answer &rarr;</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/aeo" class="btn btn-outline-light btn-sm">View All AEO Answers</a>
        </div>
    </div>
</section>

<section class="py-5 border-top border-secondary">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="text-white h3">Serving Businesses Across <span class="text-neon">India</span></h2>
            <p class="text-white-50 small">Local SEO and digital marketing services in 20+ cities</p>
        </div>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <?php foreach (get_cities_data() as $slug => $c): ?>
            <a href="/digital-agency-<?php echo $slug; ?>" class="badge bg-dark border border-secondary text-white-50 p-2 text-decoration-none hover-effect"><?php echo htmlspecialchars($c['name']); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php render_founder_section(); ?>

<section id="contact" class="py-5 border-top border-secondary">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center mb-5">
                    <h2 class="text-white">Ready to <span class="text-neon">Dominate Search?</span></h2>
                    <p class="text-white-50">Request a free SEO audit and custom growth plan — we respond within 24 hours.</p>
                </div>
                
                <div class="p-4 border border-secondary rounded bg-glass">
                    <form id="contactForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div style="display:none;"><input type="text" name="website_url" autocomplete="off"></div>
                        <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="Full Name" required></div>
                        <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Business Email" required></div>
                        <div class="mb-3">
                            <select name="service" class="form-select" required>
                                <option value="" disabled selected>Select Requirement</option>
                                <option value="Free SEO Audit">Get Free SEO Audit</option>
                                <option value="Book Consultation">Book Free Consultation</option>
                                <option value="Request Proposal">Request Proposal</option>
                                <option value="SEO Services">SEO Services</option>
                                <option value="AI Automation">AI Automation</option>
                                <option value="Web Development">Web Development</option>
                                <option value="Digital Marketing">Digital Marketing</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-nectra w-100 mt-2">Talk To Expert</button>
                        <div id="responseMsg" class="mt-3 text-center fw-bold" aria-live="polite"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php render_faq_section(get_homepage_faqs(), 'SEO & Digital Marketing FAQ'); ?>
<?php render_cta_blocks(); ?>

<style>
.grayscale-hover { filter: grayscale(100%); transition: 0.3s; }
.grayscale-hover:hover { filter: grayscale(0%); opacity: 1; }
.hover-effect:hover { transform: translateY(-5px); border-color: var(--nectra-neon) !important; transition: 0.3s; }
.animate-bounce { animation: bounce 2s infinite; }
@keyframes bounce { 0%, 20%, 50%, 80%, 100% {transform: translateX(-50%) translateY(0);} 40% {transform: translateX(-50%) translateY(-10px);} 60% {transform: translateX(-50%) translateY(-5px);} }
.accordion-button:not(.collapsed) { background: rgba(0,229,255,0.1); color: var(--nectra-neon); }
.accordion-button::after { filter: invert(1); }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('contactForm');
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = form.querySelector('button');
            const responseDiv = document.getElementById('responseMsg');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'CONNECTING...';
            btn.disabled = true;
            fetch('/process', { method: 'POST', body: new FormData(this) })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') window.location.href = "/thank-you";
                else { responseDiv.innerHTML = '<span class="text-danger">' + data.message + '</span>'; btn.innerHTML = originalText; btn.disabled = false; }
            })
            .catch(() => { responseDiv.innerHTML = '<span class="text-danger">System Error.</span>'; btn.innerHTML = originalText; btn.disabled = false; });
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
