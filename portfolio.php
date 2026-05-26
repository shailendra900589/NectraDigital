<?php 
$page_title = "Our Work & Portfolio | Web Development, E-Commerce & AI Projects";
$page_desc = "Explore Nectra Digital's portfolio of successful projects — e-commerce stores, web applications, AI automation, mobile apps, and digital marketing campaigns delivered for clients in India, USA, UAE & UK.";
$page_keys = "Nectra Digital Portfolio, Web Development Projects Lucknow, E-Commerce Development Case Studies, AI Automation Projects, Mobile App Portfolio, Digital Marketing Results, Software Development Lucknow";
include 'includes/header.php'; 

$projects = [
    [
        "cat" => "ecommerce",
        "title" => "BEHNA BAZAR",
        "loc" => "LUCKNOW, INDIA",
        "tag" => "E-COMMERCE MARKETPLACE",
        "desc" => "Full-featured multi-vendor e-commerce marketplace for women's fashion, beauty, and lifestyle products. Built with custom vendor management, real-time inventory sync, Razorpay payment integration, and automated order tracking with delivery notifications.",
        "tech" => ["PHP/Laravel", "MySQL", "Razorpay", "Bootstrap", "REST API"],
        "stat_val" => "5K+",
        "stat_lbl" => "PRODUCTS LISTED",
        "stat2_val" => "99.8%",
        "stat2_lbl" => "UPTIME",
        "icon" => "fa-shopping-bag",
        "color" => "success",
        "url" => "https://behnabazar.com",
        "live" => true
    ],
    [
        "cat" => "web",
        "title" => "NOVA PAY DASHBOARD",
        "loc" => "DUBAI, UAE",
        "tag" => "FINTECH WEB APPLICATION",
        "desc" => "Secure payment gateway dashboard handling high-volume daily transactions. Integrated with UAE local banking APIs for real-time settlement, multi-currency support, and compliance with Central Bank regulations.",
        "tech" => ["Next.js", "Node.js", "AWS Lambda", "PostgreSQL", "Stripe"],
        "stat_val" => "0.2s",
        "stat_lbl" => "AVG LATENCY",
        "stat2_val" => "99.9%",
        "stat2_lbl" => "UPTIME SLA",
        "icon" => "fa-wallet",
        "color" => "info",
        "url" => "#",
        "live" => false
    ],
    [
        "cat" => "ecommerce",
        "title" => "OUDH ORGANICS",
        "loc" => "LUCKNOW, INDIA",
        "tag" => "D2C E-COMMERCE STORE",
        "desc" => "Scaled a local Lucknow-based organic perfume and skincare brand to a Pan-India D2C e-commerce business. Headless Shopify architecture handles festive traffic spikes of 10x without downtime.",
        "tech" => ["Shopify Plus", "Next.js", "Redis", "Razorpay", "SEO"],
        "stat_val" => "300%",
        "stat_lbl" => "REVENUE GROWTH",
        "stat2_val" => "50K+",
        "stat2_lbl" => "MONTHLY ORDERS",
        "icon" => "fa-leaf",
        "color" => "success",
        "url" => "#",
        "live" => false
    ],
    [
        "cat" => "ai",
        "title" => "AUTO-REP AI AGENT",
        "loc" => "AUSTIN, USA",
        "tag" => "AI SALES AUTOMATION",
        "desc" => "Voice-activated AI sales agent for a US Real Estate firm. Calls leads autonomously, qualifies them with custom questions, and books appointments directly into the calendar — operating 24/7 without human intervention.",
        "tech" => ["Python", "OpenAI GPT", "Vapi.ai", "FastAPI", "HubSpot"],
        "stat_val" => "65%",
        "stat_lbl" => "COST REDUCTION",
        "stat2_val" => "24/7",
        "stat2_lbl" => "AVAILABILITY",
        "icon" => "fa-robot",
        "color" => "danger",
        "url" => "#",
        "live" => false
    ],
    [
        "cat" => "app",
        "title" => "METRO RIDE",
        "loc" => "NOIDA, NCR",
        "tag" => "SMART TRANSIT MOBILE APP",
        "desc" => "Last-mile connectivity app for Delhi Metro commuters. Features real-time GPS tracking of e-rickshaws, QR-based wallet payments, driver management system, and route optimization algorithm.",
        "tech" => ["Flutter", "Google Maps API", "Node.js", "PhonePe API", "Firebase"],
        "stat_val" => "100K+",
        "stat_lbl" => "DAILY RIDERS",
        "stat2_val" => "4.6★",
        "stat2_lbl" => "APP RATING",
        "icon" => "fa-subway",
        "color" => "warning",
        "url" => "#",
        "live" => false
    ],
    [
        "cat" => "web",
        "title" => "GOMTI ESTATES CRM",
        "loc" => "LUCKNOW, INDIA",
        "tag" => "REAL ESTATE CRM SYSTEM",
        "desc" => "Custom CRM for a Gomti Nagar real estate developer. Automates lead capture from 99Acres, MagicBricks & Housing.com, sends instant WhatsApp follow-ups, and provides analytics on sales pipeline.",
        "tech" => ["Laravel", "Vue.js", "MySQL", "WhatsApp API", "Cron Jobs"],
        "stat_val" => "40%",
        "stat_lbl" => "CONVERSION RATE",
        "stat2_val" => "3x",
        "stat2_lbl" => "LEAD RESPONSE",
        "icon" => "fa-building",
        "color" => "info",
        "url" => "#",
        "live" => false
    ],
    [
        "cat" => "marketing",
        "title" => "SAAS GROWTH ENGINE",
        "loc" => "NEW YORK, USA",
        "tag" => "SEO & DIGITAL MARKETING",
        "desc" => "Complete digital marketing overhaul for a B2B SaaS startup. Technical SEO audit, content strategy with 50+ blog articles, Google Ads campaigns, and LinkedIn marketing that generated qualified enterprise leads.",
        "tech" => ["Technical SEO", "Google Ads", "Content Strategy", "Analytics", "LinkedIn"],
        "stat_val" => "236%",
        "stat_lbl" => "ORGANIC TRAFFIC",
        "stat2_val" => "4.2x",
        "stat2_lbl" => "LEAD INCREASE",
        "icon" => "fa-chart-line",
        "color" => "primary",
        "url" => "#",
        "live" => false
    ],
    [
        "cat" => "ai",
        "title" => "SUPPORT BOT PRO",
        "loc" => "MUMBAI, INDIA",
        "tag" => "AI CUSTOMER SUPPORT",
        "desc" => "GPT-powered customer support chatbot for an Indian e-commerce brand. Handles 80% of customer queries automatically — order tracking, returns, FAQs — reducing support team workload by 65%.",
        "tech" => ["OpenAI GPT-4", "LangChain", "Python", "React", "MongoDB"],
        "stat_val" => "80%",
        "stat_lbl" => "AUTO-RESOLVED",
        "stat2_val" => "< 3s",
        "stat2_lbl" => "RESPONSE TIME",
        "icon" => "fa-comments",
        "color" => "warning",
        "url" => "#",
        "live" => false
    ]
];
?>

<main>
    <!-- HERO -->
    <header class="d-flex align-items-center justify-content-center text-center position-relative overflow-hidden" style="min-height: 55vh; background: transparent;">
        <div class="portfolio-bg-glow"></div>
        <div class="container position-relative z-1">
            <div class="d-inline-block border border-neon rounded-pill px-3 py-1 mb-4 bg-dark">
                <small class="text-neon text-uppercase" style="letter-spacing: 2px;"><i class="fas fa-briefcase me-2"></i> <?php echo count($projects); ?>+ Delivered Projects</small>
            </div>
            <h1 class="display-4 fw-bold text-white mb-4">Our Work & <span class="text-neon">Portfolio</span></h1>
            <p class="lead text-white-50 mx-auto" style="max-width: 750px;">
                Real projects. Real results. From <strong class="text-white">Lucknow e-commerce stores</strong> to <strong class="text-white">US AI automation systems</strong> — explore the digital assets we've engineered for businesses across the globe.
            </p>
        </div>
    </header>

    <!-- FILTER BAR -->
    <section class="portfolio-filter-bar sticky-top border-bottom border-secondary py-3" style="top: 70px; z-index: 900; background: rgba(5,5,5,0.95); backdrop-filter: blur(10px);">
        <div class="container text-center">
            <div class="d-flex justify-content-center flex-wrap gap-2" role="group" aria-label="Project Filters">
                <button type="button" class="btn btn-sm filter-btn active" data-filter="all">ALL PROJECTS</button>
                <button type="button" class="btn btn-sm filter-btn" data-filter="ecommerce"><i class="fas fa-store me-1"></i> E-Commerce</button>
                <button type="button" class="btn btn-sm filter-btn" data-filter="web"><i class="fas fa-code me-1"></i> Web Apps</button>
                <button type="button" class="btn btn-sm filter-btn" data-filter="app"><i class="fas fa-mobile-alt me-1"></i> Mobile</button>
                <button type="button" class="btn btn-sm filter-btn" data-filter="ai"><i class="fas fa-robot me-1"></i> AI & Bots</button>
                <button type="button" class="btn btn-sm filter-btn" data-filter="marketing"><i class="fas fa-chart-line me-1"></i> Marketing</button>
            </div>
        </div>
    </section>

    <!-- PROJECT GRID -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4" id="project-grid">
                <?php foreach ($projects as $proj): ?>
                <article class="col-lg-6 project-item" data-category="<?php echo $proj['cat']; ?>">
                    <div class="portfolio-card h-100 border border-secondary rounded overflow-hidden">
                        
                        <div class="portfolio-card-thumb position-relative d-flex align-items-center justify-content-center" style="height: 240px; border-bottom: 1px solid #222;">
                            <div class="portfolio-card-glow" style="background: <?php echo $proj['cat'] == 'ai' ? 'var(--nectra-neon)' : ($proj['cat'] == 'ecommerce' ? '#4CAF50' : '#fff'); ?>;"></div>
                            <i class="fas <?php echo $proj['icon']; ?> fa-4x text-secondary" style="opacity: 0.4; z-index: 1;"></i>
                            
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-dark border border-secondary text-white-50 small">
                                    <i class="fas fa-map-marker-alt text-neon me-1"></i> <?php echo $proj['loc']; ?>
                                </span>
                            </div>
                            
                            <?php if($proj['live']): ?>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success text-dark fw-bold small">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> LIVE
                                </span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="portfolio-card-overlay d-flex align-items-center justify-content-center gap-2">
                                <?php if($proj['live'] && $proj['url'] !== '#'): ?>
                                <a href="<?php echo $proj['url']; ?>" target="_blank" class="btn btn-nectra btn-sm">VISIT SITE <i class="fas fa-external-link-alt ms-1"></i></a>
                                <?php endif; ?>
                                <a href="contact" class="btn btn-outline-light btn-sm">DISCUSS PROJECT</a>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-neon small fw-bold text-uppercase" style="letter-spacing: 1px;"><?php echo $proj['tag']; ?></span>
                            </div>
                            
                            <h2 class="h4 text-white mb-3 fw-bold"><?php echo $proj['title']; ?></h2>
                            <p class="text-white-50 small mb-4" style="line-height: 1.7;"><?php echo $proj['desc']; ?></p>
                            
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <?php foreach ($proj['tech'] as $tech): ?>
                                <span class="badge border border-secondary text-white-50 fw-normal py-1 px-2" style="background: rgba(255,255,255,0.03); font-size: 0.75rem;"><?php echo $tech; ?></span>
                                <?php endforeach; ?>
                            </div>

                            <div class="row g-0 border-top border-secondary pt-3">
                                <div class="col-6 border-end border-secondary">
                                    <h4 class="h5 text-white mb-0 fw-bold"><?php echo $proj['stat_val']; ?></h4>
                                    <small class="text-white-50" style="font-size: 0.7rem; text-transform: uppercase;"><?php echo $proj['stat_lbl']; ?></small>
                                </div>
                                <div class="col-6 ps-3">
                                    <h4 class="h5 text-neon mb-0 fw-bold"><?php echo $proj['stat2_val']; ?></h4>
                                    <small class="text-white-50" style="font-size: 0.7rem; text-transform: uppercase;"><?php echo $proj['stat2_lbl']; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- RESULTS SUMMARY -->
    <section class="py-5 border-top border-secondary" style="background: linear-gradient(180deg, rgba(10,13,15,0.85) 0%, rgba(5,5,5,0.8) 100%);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="h3 text-white fw-bold">Collective Impact <span class="text-neon">Numbers</span></h2>
            </div>
            <div class="row g-4 text-center">
                <div class="col-6 col-md-3">
                    <div class="stats-card p-3">
                        <h3 class="display-6 fw-bold text-neon mb-1">150+</h3>
                        <p class="text-white-50 small text-uppercase mb-0">Projects Delivered</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stats-card p-3">
                        <h3 class="display-6 fw-bold text-white mb-1">6+</h3>
                        <p class="text-white-50 small text-uppercase mb-0">Countries Served</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stats-card p-3">
                        <h3 class="display-6 fw-bold text-neon mb-1">98%</h3>
                        <p class="text-white-50 small text-uppercase mb-0">Client Satisfaction</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stats-card p-3">
                        <h3 class="display-6 fw-bold text-white mb-1">50+</h3>
                        <p class="text-white-50 small text-uppercase mb-0">Active Clients</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5 text-center border-top border-secondary">
        <div class="container py-4">
            <h2 class="display-6 text-white fw-bold mb-3">Want Results Like <span class="text-neon">These?</span></h2>
            <p class="text-white-50 mx-auto mb-5" style="max-width: 600px;">Tell us about your project and we'll show you exactly how we can deliver similar (or better) results for your business.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="contact" class="btn btn-nectra btn-lg px-5">START YOUR PROJECT <i class="fas fa-arrow-right ms-2"></i></a>
                <a href="https://wa.me/917678387759?text=Hi%2C%20I%20saw%20your%20portfolio%20and%20want%20to%20discuss%20a%20project." target="_blank" class="btn btn-success btn-lg px-4"><i class="fab fa-whatsapp me-2"></i> WhatsApp</a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
