<?php 
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';

$page_title = "Best SEO Company India | SEO Services, AI Automation & Digital Marketing Agency";
$page_desc = "Nectra Digital is the best SEO company in India offering search engine optimization services, local SEO, technical SEO, AI automation, digital marketing, web development & lead generation. 5+ years expertise, 200+ projects, 4.9★ rating.";
$page_keys = "SEO Company India, Best SEO Company India, SEO Services India, Local SEO Services, Technical SEO Services, Enterprise SEO Agency, Digital Marketing Agency India, Performance Marketing Agency, Google Ads Agency, Meta Ads Agency, AI Automation Services, AI Chatbot Development, WhatsApp AI Bot, Website Development Company, Web Development Agency, Software Development Company, Mobile App Development Company, Lead Generation Agency, Marketing Automation Agency, Search Engine Optimization Services, SEO Expert India, AI Agency India";

$page_schema = [get_breadcrumb_schema([['name' => 'Home', 'url' => SITE_URL . '/']]), get_review_schema()];

include 'includes/header.php';
output_faq_schema(get_homepage_faqs());
?>

<header class="d-flex align-items-center justify-content-center text-center position-relative" style="min-height: 100vh; background: #050505; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, rgba(0, 229, 255, 0.1) 0%, rgba(5,5,5,1) 70%); pointer-events: none;"></div>
    
    <div class="container position-relative z-1">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-inline-block border border-neon rounded-pill px-3 py-1 mb-4 bg-dark">
                    <small class="text-neon text-uppercase" style="letter-spacing: 2px;"><i class="fas fa-award me-2"></i> Best SEO Company India · 4.9★ Rating · 200+ Projects</small>
                </div>
                
                <h1 class="display-3 fw-bold text-white mb-4" style="text-shadow: 0 0 20px rgba(0,0,0,0.8);">
                    India's Leading <span class="text-neon" style="text-shadow: 0 0 15px var(--nectra-neon);">SEO & Digital Marketing Agency</span>
                </h1>
                
                <p class="lead text-white-50 mb-4 mx-auto" style="max-width: 800px; line-height: 1.7;">
                    Nectra Digital delivers search engine optimization services, AI automation, performance marketing, and custom software development that generate qualified leads and measurable ROI for businesses across India and globally.
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-2 mb-5">
                    <a href="/contact?service=SEO+Audit" class="btn btn-nectra btn-lg">Get Free SEO Audit</a>
                    <a href="/contact?service=Consultation" class="btn btn-outline-light btn-lg">Book Free Consultation</a>
                </div>

                <div class="d-flex flex-wrap justify-content-center gap-4 text-white-50 small">
                    <span><i class="fas fa-check text-neon me-1"></i> SEO Services India</span>
                    <span><i class="fas fa-check text-neon me-1"></i> AI Automation</span>
                    <span><i class="fas fa-check text-neon me-1"></i> Web Development</span>
                    <span><i class="fas fa-check text-neon me-1"></i> Lead Generation</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-4 text-white-50 animate-bounce">
        <small class="text-uppercase" style="letter-spacing: 2px;">Scroll</small><br>
        <i class="fas fa-chevron-down mt-2"></i>
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

<section id="services" class="py-5 border-top border-secondary">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h6 class="text-neon text-uppercase mb-2">Full-Service Digital Agency</h6>
            <h2 class="text-white">SEO, Marketing & <span class="text-neon">Development Services</span></h2>
            <p class="text-white-50 mx-auto" style="max-width: 700px;">End-to-end digital solutions from India's top SEO company — search engine optimization, AI automation, performance marketing, and custom software development.</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-4">
                <a href="/seo-services" class="text-decoration-none">
                    <div class="service-card text-center h-100 p-4 border border-secondary rounded bg-glass hover-effect">
                        <i class="fas fa-search fa-2x text-neon mb-3"></i>
                        <h3 class="h5 text-white">SEO Services India</h3>
                        <p class="text-white-50 small">Search engine optimization, technical SEO, and content authority building by certified SEO experts.</p>
                        <span class="text-neon small fw-bold">Explore SEO &rarr;</span>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="/ai-automation-services" class="text-decoration-none">
                    <div class="service-card text-center h-100 p-4 border border-secondary rounded bg-glass hover-effect">
                        <i class="fas fa-robot fa-2x text-neon mb-3"></i>
                        <h3 class="h5 text-white">AI Automation Services</h3>
                        <p class="text-white-50 small">AI chatbots, WhatsApp bots, workflow automation, and business process optimization.</p>
                        <span class="text-neon small fw-bold">Explore AI &rarr;</span>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="/digital-marketing-services" class="text-decoration-none">
                    <div class="service-card text-center h-100 p-4 border border-secondary rounded bg-glass hover-effect">
                        <i class="fas fa-chart-line fa-2x text-neon mb-3"></i>
                        <h3 class="h5 text-white">Digital Marketing Agency</h3>
                        <p class="text-white-50 small">Performance marketing, Google Ads, Meta Ads, and ROI-driven campaigns that convert.</p>
                        <span class="text-neon small fw-bold">Explore Marketing &rarr;</span>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="/web-development-services" class="text-decoration-none">
                    <div class="service-card text-center h-100 p-4 border border-secondary rounded bg-glass hover-effect">
                        <i class="fas fa-code fa-2x text-neon mb-3"></i>
                        <h3 class="h5 text-white">Web Development Agency</h3>
                        <p class="text-white-50 small">React, Next.js, WordPress, Laravel — SEO-ready websites built for speed and conversions.</p>
                        <span class="text-neon small fw-bold">Explore Web Dev &rarr;</span>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="/software-development-services" class="text-decoration-none">
                    <div class="service-card text-center h-100 p-4 border border-secondary rounded bg-glass hover-effect">
                        <i class="fas fa-laptop-code fa-2x text-neon mb-3"></i>
                        <h3 class="h5 text-white">Software Development</h3>
                        <p class="text-white-50 small">Custom SaaS, enterprise software, APIs, and mobile apps by experienced engineers.</p>
                        <span class="text-neon small fw-bold">Explore Software &rarr;</span>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="/local-seo-services" class="text-decoration-none">
                    <div class="service-card text-center h-100 p-4 border border-secondary rounded bg-glass hover-effect">
                        <i class="fas fa-map-marker-alt fa-2x text-neon mb-3"></i>
                        <h3 class="h5 text-white">Local SEO Services</h3>
                        <p class="text-white-50 small">Google Business Profile optimization, local citations, and map pack domination.</p>
                        <span class="text-neon small fw-bold">Explore Local SEO &rarr;</span>
                    </div>
                </a>
            </div>
        </div>

        <div class="text-center">
            <a href="/services" class="btn btn-outline-light">View All 15+ Services</a>
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
                    <p class="text-white-50">Get a free SEO audit and customized growth strategy from India's top digital marketing agency.</p>
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
            fetch('process.php', { method: 'POST', body: new FormData(this) })
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
