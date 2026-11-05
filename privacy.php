<?php 
$page_title = "Privacy Policy — Nectra Digital";
$page_desc = "Nectra Digital Privacy Policy. Transparent data handling practices, GDPR compliance, CCPA adherence, and data security standards.";
require_once __DIR__ . '/includes/i18n.php';
$canonical_url = nectra_page_canonical('/privacy');
include 'includes/header.php'; 
?>

<main class="bg-light">
    <!-- Hero Header -->
    <header class="nd-page-hero nd-legal-hero text-center">
        <div class="container py-2 py-md-3">
            <span class="badge bg-primary bg-opacity-25 text-info border border-primary border-opacity-25 px-3 py-1.5 rounded-pill mb-3 small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">
                Legal & Governance
            </span>
            <h1 class="fw-bold text-white mb-2 nd-hero-title">Privacy Policy</h1>
            <p class="text-white-50 small mb-0">Last Updated: <?php echo date("F Y"); ?> &middot; Version 2.4</p>
        </div>
    </header>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white text-secondary" style="font-size: 0.95rem; line-height: 1.8;">
                    
                    <h3 class="h5 fw-bold text-dark mb-3">1. Overview & Commitment</h3>
                    <p class="mb-4">
                        At Nectra Digital ("we", "our", or "the Company"), protecting your privacy and treating personal information with integrity is foundational to how we build software and deliver services. This Privacy Policy details our data collection, processing, and storage practices across our public website (<a href="https://www.nectradigital.com" class="text-primary text-decoration-none">nectradigital.com</a>) and client engagement portals.
                    </p>

                    <h3 class="h5 fw-bold text-dark mb-3">2. Information We Collect</h3>
                    <p class="mb-3">
                        We adhere to the principle of data minimization and only collect information essential to servicing your project requirements:
                    </p>
                    <ul class="mb-4 ps-3">
                        <li class="mb-2"><strong>Contact Data:</strong> Full name, professional email address, phone/WhatsApp number, company name, and location provided voluntarily through inquiry forms.</li>
                        <li class="mb-2"><strong>Project Briefs:</strong> Technical scopes, budget estimations, and business goals shared during discovery.</li>
                        <li class="mb-2"><strong>Telemetry & Performance Data:</strong> Anonymized browsing metrics (pages viewed, referrer, operating system, and Core Web Vitals) collected strictly for service optimization via first-party analytics.</li>
                    </ul>

                    <h3 class="h5 fw-bold text-dark mb-3">3. Global Compliance (GDPR & CCPA)</h3>
                    <p class="mb-4">
                        We actively comply with global data governance frameworks:
                        <br>&bull; <strong>European Union (GDPR):</strong> You maintain the legal right to access, rectify, restrict processing of, or request the permanent erasure of your personal data ("Right to be Forgotten").
                        <br>&bull; <strong>California Residents (CCPA):</strong> We do not sell personal data, monetize user lists, or exchange visitor identities with data brokers.
                    </p>

                    <h3 class="h5 fw-bold text-dark mb-3">4. Security Architecture & Encryption</h3>
                    <p class="mb-4">
                        All communications across nectradigital.com are transmitted over 256-bit TLS/SSL encryption. Production databases reside behind multi-layered firewalls with strict role-based access control (RBAC). No unauthenticated third-party services have access to client CRM data.
                    </p>

                    <h3 class="h5 fw-bold text-dark mb-3">5. Cookies & Tracking Technologies</h3>
                    <p class="mb-4">
                        We utilize minimal, privacy-conscious cookies solely for session maintenance, security rate-limiting, and aggregate performance telemetry. You may disable cookies in your browser settings at any time without impacting fundamental site navigation.
                    </p>

                    <div class="border-top pt-4 mt-5 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Data Governance Officer</h6>
                            <p class="small text-muted mb-0">For data deletion, compliance queries, or subject access requests, reach our legal team directly.</p>
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