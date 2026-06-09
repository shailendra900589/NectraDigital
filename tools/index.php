<?php
require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\Models\Tool;

$tools = ge_table_exists('ge_tools') ? Tool::all(true) : [];

$page_title = 'Free SEO & Marketing Tools | ' . SITE_NAME;
$page_desc = 'Free SEO audit, keyword research, meta tag generator, schema markup, ROI calculator and more — by Nectra Digital.';
$page_keys = 'SEO tools, free SEO audit, keyword tool, meta tag generator';

include __DIR__ . '/../includes/header.php';
?>

<main class="py-5">
    <div class="container py-4">
        <h1 class="display-6 fw-bold text-white mb-2">Tools Marketplace</h1>
        <p class="text-white-50 mb-5">Free enterprise-grade SEO, marketing, and growth tools — fully managed from admin panel.</p>
        <div class="row g-4">
            <?php if (empty($tools)): ?>
            <div class="col-12"><div class="alert alert-secondary">Tools will appear after database migration. Run <code>database/migrate.php</code>.</div></div>
            <?php else: foreach ($tools as $tool): ?>
            <div class="col-md-6 col-lg-4">
                <a href="/tools/<?php echo htmlspecialchars($tool['slug']); ?>" class="d-block p-4 border border-secondary rounded text-decoration-none hover-effect h-100">
                    <h2 class="h5 text-neon mb-2"><?php echo htmlspecialchars($tool['name']); ?></h2>
                    <p class="text-white-50 small mb-0"><?php echo htmlspecialchars($tool['description'] ?? ''); ?></p>
                </a>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
