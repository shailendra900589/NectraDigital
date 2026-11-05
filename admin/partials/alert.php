<?php
/**
 * Admin Shared Alert Partial
 * Parameters:
 * - $alertType (string) 'success'|'danger'|'warning'|'info'
 * - $alertMessage (string|null)
 * - $alertHtml (string|null)
 * - $alertDismiss (bool)
 * - $alertIcon (string|null)
 */
$alertType = $alertType ?? 'info';
$alertMessage = $alertMessage ?? '';
$alertHtml = $alertHtml ?? null;
$alertDismiss = $alertDismiss ?? true;

$iconMap = [
    'success' => 'fas fa-circle-check',
    'danger'  => 'fas fa-triangle-exclamation',
    'warning' => 'fas fa-circle-exclamation',
    'info'    => 'fas fa-circle-info',
];
$icon = $alertIcon ?? ($iconMap[$alertType] ?? 'fas fa-info-circle');
?>
<div class="alert alert-<?php echo htmlspecialchars($alertType); ?> d-flex align-items-center gap-2 <?php echo $alertDismiss ? 'alert-dismissible fade show' : ''; ?> rounded-3 shadow-xs mb-3" role="alert">
    <i class="<?php echo htmlspecialchars($icon); ?> flex-shrink-0"></i>
    <div class="flex-grow-1 small">
        <?php if ($alertHtml !== null): ?>
            <?php echo $alertHtml; ?>
        <?php else: ?>
            <?php echo htmlspecialchars($alertMessage); ?>
        <?php endif; ?>
    </div>
    <?php if ($alertDismiss): ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    <?php endif; ?>
</div>
