<?php 
$page_title = "Terms of Engagement & Service Agreements — Nectra Digital";
$page_desc = "Terms and conditions for engineering, design, and digital marketing services provided by Nectra Digital. Intellectual property, payment terms, and warranties.";
require_once __DIR__ . '/includes/i18n.php';
$canonical_url = nectra_page_canonical('/terms');
include 'includes/header.php'; 
?>

<main class="bg-light">
    <!-- Hero Header -->
    <header class="nd-page-hero nd-legal-hero text-center">
        <div class="container py-2 py-md-3">
            <span class="badge bg-primary bg-opacity-25 text-info border border-primary border-opacity-25 px-3 py-1.5 rounded-pill mb-3 small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">
                Legal & Governance
            </span>
            <h1 class="fw-bold text-white mb-2 nd-hero-title">Terms of Engagement</h1>
            <p class="text-white-50 small mb-0">Effective Date: <?php echo date("F Y"); ?> &middot; Master Services Agreement</p>
        </div>
    </header>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white text-secondary" style="font-size: 0.95rem; line-height: 1.8;">
                    
                    <h3 class="h5 fw-bold text-dark mb-3">1. Scope of Engagement</h3>
                    <p class="mb-4">
                        By commissioning a project or signing a Statement of Work (SOW) with Nectra Digital, the Client agrees to the terms outlined herein. We engineer custom web applications, AI automation pipelines, and technical SEO architecture. Project timelines and milestone schedules are outlined in individual service specifications.
                    </p>

                    <h3 class="h5 fw-bold text-dark mb-3">2. Invoicing, Payments & Intellectual Property Rights</h3>
                    <p class="mb-3">
                        Unless an alternative written schedule is established in the formal Statement of Work:
                    </p>
                    <ul class="mb-4 ps-3">
                        <li class="mb-2"><strong>Sprint Milestones:</strong> Custom development is initiated upon receipt of the initial project commitment deposit. Subsequent milestone invoices are tied to accepted deliverables.</li>
                        <li class="mb-2"><strong>Full IP Transfer:</strong> Upon complete settlement of all agreed fees, 100% of custom source code, design assets, and database schemas become the exclusive intellectual property of the Client.</li>
                        <li class="mb-2"><strong>Portfolio Display:</strong> Nectra Digital reserves the right to reference the non-confidential technical engagement in case studies unless an explicit Mutual Non-Disclosure Agreement (NDA) is executed.</li>
                    </ul>

                    <h3 class="h5 fw-bold text-dark mb-3">3. Revisions, Acceptance & Quality Guarantees</h3>
                    <p class="mb-4">
                        We prioritize client satisfaction through structured feedback sprint gates. Design and architectural prototypes are reviewed collaboratively prior to production coding. Each sprint includes dedicated QA testing for cross-browser stability, mobile responsiveness, and Core Web Vitals optimization.
                    </p>

                    <h3 class="h5 fw-bold text-dark mb-3">4. Limitation of Liability & Third-Party Platforms</h3>
                    <p class="mb-4">
                        Nectra Digital exercises professional diligence in all technical implementations. However, we cannot accept liability for service disruptions originating from external third-party platforms (including AWS, Google Cloud, OpenAI, hosting providers, or search engine algorithm updates beyond reasonable engineering control).
                    </p>

                    <div class="border-top pt-4 mt-5 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Questions Concerning Service Terms?</h6>
                            <p class="small text-muted mb-0">Our legal counsel and business operations team are available to discuss custom enterprise MSAs.</p>
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