<?php
/**
 * Admin Shared Empty State Partial
 * Parameters:
 * - $emptyIcon (string)
 * - $emptyTitle (string)
 * - $emptyText (string)
 * - $emptyActionUrl (string|null)
 * - $emptyActionLabel (string|null)
 * - $emptyActionIcon (string|null)
 */
$emptyIcon = $emptyIcon ?? 'fas fa-inbox';
$emptyTitle = $emptyTitle ?? 'No Records Found';
$emptyText = $emptyText ?? 'There are currently no items matching your criteria.';
$emptyActionUrl = $emptyActionUrl ?? null;
$emptyActionLabel = $emptyActionLabel ?? null;
$emptyActionIcon = $emptyActionIcon ?? 'fas fa-plus';
?>
<div class="text-center py-5 px-3">
    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle text-muted mb-3" style="width: 64px; height: 64px;">
        <i class="<?php echo htmlspecialchars($emptyIcon); ?> fa-2x"></i>
    </div>
    <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($emptyTitle); ?></h5>
    <p class="text-muted small mx-auto mb-3" style="max-width: 420px;"><?php echo htmlspecialchars($emptyText); ?></p>
    <?php if ($emptyActionUrl && $emptyActionLabel): ?>
    <a href="<?php echo htmlspecialchars($emptyActionUrl); ?>" class="btn btn-sm btn-primary">
        <?php if ($emptyActionIcon): ?><i class="<?php echo htmlspecialchars($emptyActionIcon); ?> me-1"></i><?php endif; ?>
        <?php echo htmlspecialchars($emptyActionLabel); ?>
    </a>
    <?php endif; ?>
</div>
