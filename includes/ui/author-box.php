<?php
/**
 * Nectra Digital — E-E-A-T Verified Author Box
 * Google Search Quality Rater Guidelines compliant author credibility block
 */
if (!defined('FOUNDER_NAME')) {
    require_once dirname(__DIR__) . '/seo-data.php';
}
?>
<div class="nd-author-box my-4" itemscope itemtype="https://schema.org/Person">
    <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-4">
        <div class="flex-shrink-0 text-center">
            <div class="nd-author-avatar bg-white d-flex align-items-center justify-content-center shadow-sm mx-auto" style="width: 84px; height: 84px;">
                <i class="fas fa-user-tie fa-2x text-primary-blue"></i>
            </div>
            <span class="badge bg-success-subtle text-success border border-success-subtle mt-2 d-inline-block small" style="font-size: 0.7rem;">
                <i class="fas fa-check-circle"></i> Verified Expert
            </span>
        </div>
        <div class="flex-grow-1 text-center text-sm-start">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-between gap-2 mb-1">
                <div>
                    <h4 class="h5 fw-bold mb-0 text-dark" itemprop="name"><?php echo htmlspecialchars(FOUNDER_NAME); ?></h4>
                    <span class="text-primary-blue fw-semibold small" itemprop="jobTitle"><?php echo htmlspecialchars(FOUNDER_TITLE); ?> · <?php echo htmlspecialchars(FOUNDER_EXPERIENCE); ?> Experience</span>
                </div>
                <div>
                    <a href="<?php echo htmlspecialchars(FOUNDER_LINKEDIN); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-pill" itemprop="sameAs" aria-label="LinkedIn Profile">
                        <i class="fab fa-linkedin me-1"></i> LinkedIn
                    </a>
                </div>
            </div>
            <p class="text-secondary small mt-2 mb-3" itemprop="description" style="line-height: 1.6;">
                <?php echo htmlspecialchars(FOUNDER_NAME); ?> specializes in technical SEO, organic growth architecture, AI workflow automation, and scalable software engineering. Every strategy and technical recommendation published on Nectra Digital is reviewed to meet rigorous industry standards.
            </p>
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-between gap-2 pt-2 border-top border-light-subtle">
                <div class="d-flex flex-wrap gap-1">
                    <?php foreach (array_slice(FOUNDER_EXPERTISE, 0, 5) as $skill): ?>
                    <span class="badge bg-light text-secondary border small"><?php echo htmlspecialchars($skill); ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="small text-muted" style="font-size: 0.775rem;">
                    <i class="fas fa-calendar-check me-1"></i> Reviewed for Accuracy · <?php echo date('F Y'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
