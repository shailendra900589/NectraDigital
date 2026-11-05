<?php
require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\Models\Tool;

$tools = ge_table_exists('ge_tools') ? Tool::all(true) : [];

$page_title = 'Free Enterprise SEO & Growth Tools — Nectra Digital';
$page_desc = 'Free production tools for SEO audits, meta tag generation, ROI forecasting, and website development estimation by Nectra Digital.';
$page_keys = 'SEO tools, free SEO audit, keyword tool, meta tag generator, ROI calculator';

include __DIR__ . '/../includes/header.php';
?>

<main class="bg-light">
    <!-- Hero Header -->
    <header class="nd-page-hero nd-legal-hero text-center">
        <div class="container py-2 py-md-3">
            <span class="badge bg-primary bg-opacity-25 text-info border border-primary border-opacity-25 px-3 py-1.5 rounded-pill mb-3 small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">
                Engineering Utilities
            </span>
            <h1 class="fw-bold text-white mb-3 nd-hero-title">Free Growth & SEO Tools</h1>
            <p class="lead text-white-50 mx-auto nd-prose-width">
                Production-grade marketing and architectural utilities engineered to audit technical search metrics, generate clean schema, and calculate ROI.
            </p>
        </div>
    </header>

    <div class="container py-5">
        <div class="row g-4">
            <?php if (empty($tools)): ?>
            <div class="col-12">
                <div class="alert alert-info rounded-3 p-4">
                    <h5 class="fw-bold mb-1">Tools Library Synchronizing</h5>
                    <p class="small mb-0">Tools are configured via the database. If this is a fresh setup, initialize growth tables via <code>database/migrate.php</code>.</p>
                </div>
            </div>
            <?php else: foreach ($tools as $tool): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white hover-shadow transition">
                    <div class="rounded-3 p-2.5 d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary mb-3" style="width: 44px; height: 44px;">
                        <i class="fas fa-toolbox fa-sm"></i>
                    </div>
                    <h2 class="h5 fw-bold text-dark mb-2"><?php echo htmlspecialchars($tool['name']); ?></h2>
                    <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;"><?php echo htmlspecialchars($tool['description'] ?? ''); ?></p>
                    <a href="/tools/<?php echo htmlspecialchars($tool['slug']); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold align-self-start">
                        Launch Tool <i class="fas fa-arrow-right fa-xs ms-1"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
