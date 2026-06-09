<?php 
// SEO CONFIGURATION
$page_title = "Global & Local Case Studies";
$page_desc = "From Lucknow startups to Dubai FinTechs. Explore Nectra Digital's portfolio of high-performance digital assets.";
$page_keys = "Web Development Lucknow, International Client Portfolio, Next.js Projects, AI Automation Case Studies";
include 'includes/header.php'; 

// --- SCALABLE DATA ENGINE (Mixed Global & Local) ---
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

<main>
    <header class="d-flex align-items-center justify-content-center text-center position-relative overflow-hidden" style="min-height: 60vh; background: #050505;">
        <div style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(0,229,255,0.05) 0%, rgba(5,5,5,0) 60%); animation: rotate 20s linear infinite;"></div>
        
        <div class="container position-relative z-1">
            <h1 class="h6 text-neon text-uppercase mb-3" style="letter-spacing: 4px; text-shadow: 0 0 10px rgba(0,229,255,0.5);">Deployed Infrastructure</h1>
            <p class="display-3 fw-bold text-white mb-4" style="text-shadow: 0 0 20px rgba(0,0,0,0.8);">BORDERLESS <span class="text-neon">ASSETS</span></p>
            <p class="lead text-white-50 mx-auto" style="max-width: 700px; font-weight: 300;">
                From Lucknow to London. We engineer high-performance digital engines for visionary brands across the globe.
            </p>
        </div>
    </header>

    <section class="sticky-top border-bottom border-secondary py-3" style="top: 70px; z-index: 900; background: rgba(5,5,5,0.95); backdrop-filter: blur(10px);">
        <div class="container text-center">
            <div class="btn-group" role="group" aria-label="Project Filters">
                <button type="button" class="btn btn-sm btn-outline-dark text-white active filter-btn" data-filter="all" style="border-color: #333;">ALL</button>
                <button type="button" class="btn btn-sm btn-outline-dark text-white filter-btn" data-filter="web" style="border-color: #333;">WEB</button>
                <button type="button" class="btn btn-sm btn-outline-dark text-white filter-btn" data-filter="app" style="border-color: #333;">APPS</button>
                <button type="button" class="btn btn-sm btn-outline-dark text-white filter-btn" data-filter="ai" style="border-color: #333;">AI & BOTS</button>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4" id="project-grid">

                <?php foreach ($projects as $proj): ?>
                <article class="col-lg-6 project-item fade-in" data-category="<?php echo $proj['cat']; ?>">
                    <div class="card h-100 border border-secondary rounded overflow-hidden service-card p-0" 
                         style="background: #0a0a0a; transition: all 0.4s ease;">
                        
                        <div class="project-thumb position-relative d-flex align-items-center justify-content-center bg-dark" 
                             style="height: 280px; border-bottom: 1px solid #222; overflow: hidden;">
                            
                            <div style="position:absolute; width:100px; height:100px; background:<?php echo $proj['cat'] == 'ai' ? 'var(--nectra-neon)' : '#fff'; ?>; opacity:0.1; filter:blur(50px); border-radius:50%;"></div>
                            
                            <i class="fas <?php echo $proj['icon']; ?> fa-4x text-secondary opacity-50" style="z-index: 1;"></i>
                            
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-<?php echo $proj['color']; ?> text-dark fw-bold" style="box-shadow: 0 0 10px rgba(255,255,255,0.2);">
                                    <i class="fas fa-map-marker-alt me-1"></i> <?php echo $proj['loc']; ?>
                                </span>
                            </div>
                            
                            <div class="project-overlay d-flex align-items-center justify-content-center">
                                <a href="contact" class="btn btn-nectra btn-sm">REQUEST BLUEPRINT</a>
                            </div>
                        </div>

                        <div class="card-body p-4 position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-neon x-small fw-bold text-uppercase" style="letter-spacing: 1px;"><?php echo $proj['tag']; ?></span>
                            </div>
                            
                            <h2 class="h3 text-white mb-3" style="font-family: 'Orbitron', sans-serif;"><?php echo $proj['title']; ?></h2>
                            <p class="text-white-50 small mb-4" style="line-height: 1.6;"><?php echo $proj['desc']; ?></p>
                            
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <?php foreach ($proj['tech'] as $tech): ?>
                                    <span class="badge border border-secondary text-white-50 fw-normal py-2 px-3" 
                                          style="background: rgba(255,255,255,0.03);"><?php echo $tech; ?></span>
                                <?php endforeach; ?>
                            </div>

                            <div class="row g-0 border-top border-secondary pt-3 mt-auto">
                                <div class="col-6 border-end border-secondary">
                                    <h4 class="h4 text-white mb-0 fw-bold"><?php echo $proj['stat_val']; ?></h4>
                                    <small class="text-white-50 x-small text-uppercase"><?php echo $proj['stat_lbl']; ?></small>
                                </div>
                                <div class="col-6 ps-4">
                                    <h4 class="h4 text-neon mb-0 fw-bold"><i class="fas fa-check-circle small"></i></h4>
                                    <small class="text-white-50 x-small text-uppercase">SUCCESSFUL</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>

            </div>

            <div class="text-center mt-5">
                <button class="btn btn-outline-secondary text-white-50 btn-sm px-4 py-2" style="letter-spacing: 2px;">
                    <i class="fas fa-plus me-2"></i> LOAD ARCHIVES
                </button>
            </div>
        </div>
    </section>

    <section class="py-5 text-center mt-4 bg-glass">
        <div class="container">
            <h2 class="text-white mb-4">GLOBAL STANDARDS. LOCAL ROOTS.</h2>
            <p class="lead text-white-50 mb-4">Whether you are in Hazratganj or Manhattan, we build for scale.</p>
            <a href="contact" class="btn btn-nectra btn-lg">INITIALIZE PROJECT</a>
        </div>
    </section>
</main>

<style>
    @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .project-item .card:hover { border-color: var(--nectra-neon) !important; transform: translateY(-5px); box-shadow: 0 10px 40px rgba(0, 229, 255, 0.1); }
    .project-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(5,5,5,0.8); backdrop-filter: blur(5px); opacity: 0; transition: opacity 0.3s ease; }
    .project-item .card:hover .project-overlay { opacity: 1; }
    .filter-btn.active { background-color: var(--nectra-neon) !important; color: #000 !important; border-color: var(--nectra-neon) !important; box-shadow: 0 0 15px var(--nectra-neon); }
    .fade-in { animation: fadeIn 0.5s ease-in; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const projectItems = document.querySelectorAll('.project-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const filterValue = btn.getAttribute('data-filter');
            projectItems.forEach(item => {
                item.classList.remove('fade-in');
                void item.offsetWidth; 
                if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                    item.style.display = 'block';
                    item.classList.add('fade-in');
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>