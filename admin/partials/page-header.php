<?php
/**
 * Admin Shared Page Header Partial
 * Parameters:
 * - $headerTitle (string)
 * - $headerSubtitle (string|null)
 * - $headerActions (array of buttons: ['label', 'url', 'icon', 'class'])
 */
$headerTitle = $headerTitle ?? 'Dashboard';
$headerSubtitle = $headerSubtitle ?? null;
$headerActions = $headerActions ?? [];
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1" style="letter-spacing: -0.02em;"><?php echo htmlspecialchars($headerTitle); ?></h1>
        <?php if (!empty($headerSubtitle)): ?>
        <p class="text-muted small mb-0"><?php echo htmlspecialchars($headerSubtitle); ?></p>
        <?php endif; ?>
    </div>
    <?php if (!empty($headerActions)): ?>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($headerActions as $act): ?>
        <a href="<?php echo htmlspecialchars($act['url']); ?>" class="<?php echo htmlspecialchars($act['class'] ?? 'btn btn-primary btn-sm'); ?>">
            <?php if (!empty($act['icon'])): ?><i class="<?php echo htmlspecialchars($act['icon']); ?> me-1"></i><?php endif; ?>
            <?php echo htmlspecialchars($act['label']); ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
