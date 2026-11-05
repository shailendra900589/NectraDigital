<?php 
// SEO CONFIGURATION
$page_title = "Global & Local Case Studies | Nectra Digital Portfolio";
$page_desc = "Explore Nectra Digital's portfolio of high-performance digital assets: FinTech dashboards, Headless E-commerce, AI Voice Agents, and Transit Apps.";
$page_keys = "Web Development Lucknow, International Client Portfolio, Next.js Projects, AI Automation Case Studies, Digital Marketing Results";
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';
require_once 'includes/i18n.php';
$canonical_url = nectra_page_canonical('/portfolio');

$page_schema = [
    get_breadcrumb_schema([
        ['name' => 'Home', 'url' => SITE_URL . '/'],
        ['name' => 'Case Studies', 'url' => SITE_URL . '/portfolio']
    ])
];

include 'includes/header.php'; 

// --- SCALABLE DATA ENGINE (Verified Projects) ---
$projects = [
    // 1. GLOBAL PROJECT (High Authority)
    [
        "cat" => "web",
        "title" => "NOVA PAY",
        "loc" => "DUBAI, UAE",
        "tag" => "FINTECH DASHBOARD",
        "desc" => "Military-grade secure payment gateway dashboard handling $50k+ daily transactions. Integrated with local UAE banking APIs for real-time settlement.",
        "tech" => ["Next.js", "Node.js", "AWS", "PostgreSQL"],
        "stat_val" => "0.2s",
        "stat_lbl" => "LATENCY",
        "icon" => "fa-wallet",
        "color" => "info" 
    ],
    // 2. LOCAL PROJECT (Local Trust - Lucknow)
    [
        "cat" => "web",
        "title" => "OUDH ORGANICS",
        "loc" => "LUCKNOW, UP",
        "tag" => "D2C E-COMMERCE",
        "desc" => "Scaled a local Lucknow-based organic perfume brand to a Pan-India D2C giant. Implemented Headless Shopify to handle festive traffic spikes.",
        "tech" => ["Shopify Plus", "Next.js", "Redis", "Razorpay"],
        "stat_val" => "300%",
        "stat_lbl" => "SALES GROWTH",
        "icon" => "fa-leaf",
        "color" => "success"
    ],
    // 3. GLOBAL PROJECT (AI Tech)
    [
        "cat" => "ai",
        "title" => "AUTO-REP",
        "loc" => "AUSTIN, USA",
        "tag" => "AI SALES AGENT",
        "desc" => "Voice-activated AI sales agent for a US Real Estate firm. It calls leads, qualifies them, and books appointments directly into the calendar.",
        "tech" => ["Python", "Vapi.ai", "FastAPI", "HubSpot"],
        "stat_val" => "24/7",
        "stat_lbl" => "UPTIME",
        "icon" => "fa-robot",
        "color" => "danger"
    ],
    // 4. LOCAL PROJECT (NCR/India Tech)
    [
        "cat" => "app",
        "title" => "METRO RIDE",
        "loc" => "NOIDA, NCR",
        "tag" => "SMART TRANSIT APP",
        "desc" => "Last-mile connectivity app for Metro commuters. Features real-time GPS tracking of e-rickshaws and QR-based wallet payments.",
        "tech" => ["Flutter", "Google Maps", "Node.js", "PhonePe API"],
        "stat_val" => "100k+",
        "stat_lbl" => "DAILY RIDERS",
        "icon" => "fa-subway",
        "color" => "warning"
    ],
    // 5. GLOBAL PROJECT (E-commerce)
    [
        "cat" => "web",
        "title" => "LUXE STREET",
        "loc" => "NEW YORK, USA",
        "tag" => "HEADLESS FASHION",
        "desc" => "High-performance fashion store for a NY streetwear brand. Implemented AI-driven 3D product try-ons to boost engagement.",
        "tech" => ["React", "Three.js", "Stripe", "Sanity CMS"],
        "stat_val" => "$2M+",
        "stat_lbl" => "REVENUE",
        "icon" => "fa-shopping-bag",
        "color" => "primary"
    ],
    // 6. LOCAL PROJECT (Lucknow Real Estate)
    [
        "cat" => "web",
        "title" => "GOMTI ESTATES",
        "loc" => "LUCKNOW, UP",
        "tag" => "REAL ESTATE CRM",
        "desc" => "Custom CRM for a Gomti Nagar developer. Automates lead capture from 99Acres/MagicBricks and sends WhatsApp follow-ups instantly.",
        "tech" => ["Laravel", "Vue.js", "MySQL", "WhatsApp API"],
        "stat_val" => "40%",
        "stat_lbl" => "LEAD CONVERSION",
        "icon" => "fa-building",
        "color" => "info"
    ]
];
?>

<main style="background: #F8FAFC; min-height: 100vh;">
    <!-- HERO (Dark Navy) -->
    <header class="nd-page-hero nd-section-dark text-center position-relative overflow-hidden">
        <div class="nd-page-hero-glow" aria-hidden="true"></div>
        <div class="container position-relative z-1 py-2 py-md-3">
            <?php render_breadcrumbs([['name' => 'Home', 'url' => '/'], ['name' => 'Case Studies', 'url' => '/portfolio']]); ?>
            <h1 class="fw-bold text-white mb-3 nd-hero-title">Engineered Products &amp; <span class="nd-text-gradient">Measurable Growth</span></h1>
            <p class="lead text-white-50 mx-auto" style="max-width: 680px; line-height: 1.6;">
                From local businesses in India to international FinTech startups in Dubai and New York — discover how we build scalable digital assets.
            </p>
        </div>
    </header>

    <!-- TRUST STRIP -->
    <?php include __DIR__ . '/includes/ui/trust-bar.php'; ?>

    <!-- FILTER BAR -->
    <section class="sticky-top bg-white border-bottom py-3 shadow-sm" style="top: 70px; z-index: 900;">
        <div class="container text-center">
            <div class="btn-group" role="group" aria-label="Project Filters">
                <button type="button" class="btn btn-sm btn-primary filter-btn px-4 fw-semibold" data-filter="all">ALL PROJECTS</button>
                <button type="button" class="btn btn-sm btn-outline-secondary filter-btn px-3 fw-medium" data-filter="web">WEB &amp; SAAS</button>
                <button type="button" class="btn btn-sm btn-outline-secondary filter-btn px-3 fw-medium" data-filter="app">MOBILE APPS</button>
                <button type="button" class="btn btn-sm btn-outline-secondary filter-btn px-3 fw-medium" data-filter="ai">AI &amp; AUTOMATION</button>
            </div>
        </div>
    </section>

    <!-- CASE STUDY GRID -->
    <section class="py-5">
        <div class="container py-3">
            <div class="row g-4" id="project-grid">
                <?php foreach ($projects as $proj): ?>
                <article class="col-lg-6 project-item" data-category="<?php echo htmlspecialchars($proj['cat']); ?>">
                    <div class="nd-card p-4 h-100 d-flex flex-column bg-white shadow-sm">
                        <!-- Top Bar with Tag and Location -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-light text-primary border fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                <?php echo htmlspecialchars($proj['tag']); ?>
                            </span>
                            <span class="badge bg-light text-secondary border small">
                                <i class="fas fa-map-marker-alt text-primary me-1"></i> <?php echo htmlspecialchars($proj['loc']); ?>
                            </span>
                        </div>

                        <!-- Title and Icon -->
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 text-primary flex-shrink-0" style="width: 48px; height: 48px;">
                                <i class="fas <?php echo htmlspecialchars($proj['icon']); ?> fa-lg"></i>
                            </div>
                            <h2 class="h5 fw-bold text-dark mb-0"><?php echo htmlspecialchars($proj['title']); ?></h2>
                        </div>

                        <!-- Description -->
                        <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                            <?php echo htmlspecialchars($proj['desc']); ?>
                        </p>

                        <!-- Tech Stack Tags -->
                        <div class="d-flex flex-wrap gap-1 mb-4">
                            <?php foreach ($proj['tech'] as $tech): ?>
                            <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.72rem;"><?php echo htmlspecialchars($tech); ?></span>
                            <?php endforeach; ?>
                        </div>

                        <!-- Outcome Bar -->
                        <div class="p-3 rounded-3 d-flex align-items-center justify-content-between" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <div>
                                <div class="h5 fw-bold text-dark mb-0"><?php echo htmlspecialchars($proj['stat_val']); ?></div>
                                <small class="text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;"><?php echo htmlspecialchars($proj['stat_lbl']); ?></small>
                            </div>
                            <a href="/contact?service=Case+Study+Inquiry" class="btn btn-sm btn-outline-primary fw-semibold">
                                Request Blueprint →
                            </a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FINAL CTA -->
    <?php include __DIR__ . '/includes/ui/cta.php'; ?>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const projectItems = document.querySelectorAll('.project-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-primary');

            const filterValue = btn.getAttribute('data-filter');
            projectItems.forEach(item => {
                if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>