<?php
$tools = $service['tools'] ?? [];
$toolList = !empty($tools) ? implode(', ', array_slice($tools, 0, 5)) : 'industry-standard platforms';
$silo = strtolower($service['silo'] ?? 'your project');
$contactPath = function_exists('nectra_web_path') ? nectra_web_path('/contact?service=' . urlencode($service['h1'])) : '/contact';
?>
<div class="svc-tech-panel">
    <div class="row g-4 align-items-center">
        <div class="col-lg-8">
            <h2 class="h4 fw-bold text-dark mb-3 nd-section-title">Tools &amp; Infrastructure</h2>
            <?php if (!empty($tools)): ?>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <?php foreach ($tools as $tool): ?>
                <span class="svc-tool-badge"><?php echo htmlspecialchars($tool); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="p-4 rounded-3 bg-light border svc-insight-card mb-0">
                <h3 class="h6 fw-bold text-dark mb-2"><i class="fas fa-lightbulb text-primary me-2"></i>Expert insight</h3>
                <p class="text-muted small fst-italic mb-2">"We focus on measurable commercial outcomes — clear search intent, transparent reporting, and continuous optimization tailored to your unit economics." — <?php echo FOUNDER_NAME; ?>, <?php echo FOUNDER_TITLE; ?></p>
                <p class="text-secondary small mb-0">We use <?php echo htmlspecialchars($toolList); ?> across design, development, QA, and launch — so your <?php echo htmlspecialchars($silo); ?> delivery stays fast, traceable, and easy to scale.</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="svc-scope-cta text-center p-4 p-lg-5 h-100 d-flex flex-column justify-content-center">
                <p class="small text-white-50 mb-3">Need a scoped proposal with timelines and investment?</p>
                <a href="<?php echo htmlspecialchars($contactPath); ?>" class="btn btn-light fw-semibold px-4 nd-btn-motion"><i class="fas fa-file-signature me-2"></i>Get Free Proposal</a>
            </div>
        </div>
    </div>
</div>
