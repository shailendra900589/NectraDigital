<?php 
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';
require_once __DIR__ . '/includes/i18n.php';

$page_title = "Editorial Guidelines & E-E-A-T Content Standards — Nectra Digital";
$page_desc = "Nectra Digital editorial guidelines ensuring EEAT-compliant, expert-verified technical content. Learn about our author verification, fact-checking, and editorial integrity.";
$page_keys = "Editorial Guidelines, EEAT Content Standards, Nectra Digital Editorial Policy, Content Accuracy";
$canonical_url = nectra_page_canonical('/editorial-guidelines');

$page_schema = [get_breadcrumb_schema([
    ['name' => 'Home', 'url' => SITE_URL . '/'],
    ['name' => 'Editorial Guidelines', 'url' => SITE_URL . '/editorial-guidelines']
])];

include 'includes/header.php';
?>

<main class="bg-light">
    <!-- Hero Header -->
    <header class="nd-page-hero nd-legal-hero text-center">
        <div class="container py-2 py-md-3">
            <h1 class="fw-bold text-white mb-2 nd-hero-title">Editorial Guidelines</h1>
            <p class="text-white-50 small mb-0">Reviewed by <?php echo FOUNDER_NAME; ?> &middot; Last Updated: <?php echo date("F Y"); ?></p>
        </div>
    </header>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="mb-3">
                    <?php render_breadcrumbs([['name' => 'Home', 'url' => '/'], ['name' => 'Editorial Guidelines', 'url' => '/editorial-guidelines']], 'light'); ?>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white text-secondary nd-legal-body">
                    <h3 class="h5 fw-bold text-dark mb-3">1. Content Mission & Standards</h3>
                    <p class="mb-4">
                        Nectra Digital publishes technical guides, research breakdowns, and engineering perspectives designed to inform practitioners and business leaders about modern web systems, search algorithms, AI automation, and technical marketing. We prioritize clarity, empirical evidence, and actionable value over superficial content volume.
                    </p>

                    <h3 class="h5 fw-bold text-dark mb-3">2. Author Verification & E-E-A-T Framework</h3>
                    <p class="mb-4">
                        All technical content is either authored or rigorously vetted by <strong><?php echo FOUNDER_NAME; ?></strong> (<?php echo FOUNDER_TITLE; ?>) or qualified senior engineering and SEO practitioners. Every published article contains an author byline linking to full professional credentials, verified LinkedIn profiles, and verified field experience.
                    </p>

                    <h3 class="h5 fw-bold text-dark mb-3">3. Technical Review & Fact-Checking Protocol</h3>
                    <p class="mb-4">
                        Before public dissemination, content is verified against three criteria:
                        <br>&bull; <strong>Empirical Validation:</strong> Code snippets, architectural diagrams, and schema specifications are verified on active production or staging environments.
                        <br>&bull; <strong>Source Citation:</strong> Case studies and industry statistics cite reputable, primary documentation (W3C, Google Search Central, MDN Web Docs, RFC standards).
                        <br>&bull; <strong>Readability & Machine Parseability:</strong> Headings, schema, tables, and definitions are formatted to optimize comprehension for human readers and AI search models (ChatGPT, Gemini, Claude, Perplexity).
                    </p>

                    <h3 class="h5 fw-bold text-dark mb-3">4. AI Tooling & Human Editorial Responsibility</h3>
                    <p class="mb-4">
                        While our engineers leverage state-of-the-art LLMs for data synthesis, query pattern clustering, and drafting assistance, automated text is never published without comprehensive human review, fact-checking, and editorial sign-off.
                    </p>

                    <h3 class="h5 fw-bold text-dark mb-3">5. Corrections & Transparent Errata</h3>
                    <p class="mb-4">
                        We actively invite corrections from our community and readers. If you discover a technical discrepancy or outdated API reference, notify our editorial board at <a href="mailto:editorial@nectradigital.com" class="text-primary text-decoration-none fw-semibold">editorial@nectradigital.com</a>. Corrections are investigated and updated within 48 hours.
                    </p>

                    <div class="border-top pt-4 mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Editorial Correspondence</h6>
                            <p class="small text-muted mb-0">Direct contributions and technical review submissions to our team.</p>
                        </div>
                        <a href="mailto:editorial@nectradigital.com" class="btn btn-outline-primary btn-sm px-3 rounded-pill flex-shrink-0">
                            <i class="fas fa-envelope me-1"></i> editorial@nectradigital.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
