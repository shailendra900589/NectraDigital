<?php
/**
 * Nectra Digital — Reusable Enterprise Call to Action (CTA)
 * High-conversion dual button layout with dark navy background
 */
$ctaHeading = $ctaHeading ?? 'Have a Product Idea or Development Requirement?';
$ctaText = $ctaText ?? 'Tell us what you are building. We will help you plan the right design, technology and development approach.';
$ctaPrimaryLabel = $ctaPrimaryLabel ?? (defined('NECTRA_CTA_PRIMARY_LABEL') ? NECTRA_CTA_PRIMARY_LABEL : 'Start a Project');
$ctaPrimaryUrl = $ctaPrimaryUrl ?? (defined('NECTRA_CTA_PRIMARY_URL') ? NECTRA_CTA_PRIMARY_URL : '/contact?service=Start+Project');
$ctaSecondaryLabel = $ctaSecondaryLabel ?? (defined('NECTRA_CTA_SECONDARY_LABEL') ? NECTRA_CTA_SECONDARY_LABEL : 'Talk to an Expert');
$ctaSecondaryUrl = $ctaSecondaryUrl ?? (defined('NECTRA_CTA_SECONDARY_URL') ? NECTRA_CTA_SECONDARY_URL : '/contact?service=Consultation');
if (!function_exists('nectra_web_path')) {
    require_once dirname(__DIR__) . '/text-utils.php';
}
$ctaPrimaryUrl = nectra_web_path($ctaPrimaryUrl);
$ctaSecondaryUrl = nectra_web_path($ctaSecondaryUrl);
?>
<div class="nd-cta-card text-center position-relative">
    <div class="nd-cta-glow"></div>
    <div class="position-relative z-1" style="max-width: 760px; margin: 0 auto;">
        <h2 class="fw-bold text-white mb-3"><?php echo htmlspecialchars($ctaHeading); ?></h2>
        <p class="lead mb-4 text-white-50"><?php echo htmlspecialchars($ctaText); ?></p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="<?php echo htmlspecialchars($ctaPrimaryUrl); ?>" class="btn btn-nectra-primary btn-nectra-lg nd-btn-motion">
                <?php echo htmlspecialchars($ctaPrimaryLabel); ?> <i class="fas fa-arrow-right ms-1"></i>
            </a>
            <a href="<?php echo htmlspecialchars($ctaSecondaryUrl); ?>" class="btn btn-nectra-outline-light btn-nectra-lg nd-btn-motion">
                <i class="fas fa-headset me-1"></i> <?php echo htmlspecialchars($ctaSecondaryLabel); ?>
            </a>
        </div>
        <div class="d-flex flex-wrap justify-content-center gap-4 mt-4 pt-2 text-white-50 small">
            <span><i class="fas fa-check-circle text-tech-blue me-1"></i> Free Strategy Session</span>
            <span><i class="fas fa-check-circle text-tech-blue me-1"></i> Transparent Deliverables</span>
            <span><i class="fas fa-check-circle text-tech-blue me-1"></i> No Lock-in Contracts</span>
        </div>
    </div>
</div>
