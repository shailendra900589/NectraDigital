<?php
/**
 * Admin Shared Stat Card Partial
 * Parameters:
 * - $statLabel (string)
 * - $statValue (string|int)
 * - $statIcon (string|null)
 * - $statTrend (string|null) e.g. "+14.2%"
 * - $statTrendType (string) 'up'|'down'|'neutral'
 * - $statSubtitle (string|null)
 * - $statColor (string) 'primary'|'success'|'warning'|'info'|'danger'
 * - $statUrl (string|null)
 */
$statLabel = $statLabel ?? 'Metric';
$statValue = $statValue ?? '0';
$statIcon = $statIcon ?? null;
$statTrend = $statTrend ?? null;
$statTrendType = $statTrendType ?? 'up';
$statSubtitle = $statSubtitle ?? null;
$statColor = $statColor ?? 'primary';
$statUrl = $statUrl ?? null;
?>
<div class="ge-stat-card card border-0 shadow-sm rounded-3 p-3 h-100 position-relative bg-white">
    <div class="d-flex align-items-start justify-content-between mb-2">
        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.04em; font-size: 0.72rem;"><?php echo htmlspecialchars($statLabel); ?></span>
        <?php if ($statIcon): ?>
        <div class="rounded-2 p-2 d-inline-flex align-items-center justify-content-center bg-<?php echo htmlspecialchars($statColor); ?>-subtle text-<?php echo htmlspecialchars($statColor); ?>" style="width: 34px; height: 34px;">
            <i class="<?php echo htmlspecialchars($statIcon); ?> fa-sm"></i>
        </div>
        <?php endif; ?>
    </div>
    <div class="d-flex align-items-baseline gap-2 mb-1">
        <h3 class="h3 fw-bold text-dark mb-0"><?php echo htmlspecialchars((string)$statValue); ?></h3>
        <?php if ($statTrend): ?>
        <span class="badge <?php echo $statTrendType === 'up' ? 'bg-success-subtle text-success' : ($statTrendType === 'down' ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary'); ?> small px-1.5 py-0.5 rounded-pill" style="font-size: 0.72rem;">
            <i class="fas fa-arrow-<?php echo $statTrendType === 'up' ? 'up' : ($statTrendType === 'down' ? 'down' : 'right'); ?> me-0.5" style="font-size: 0.65rem;"></i>
            <?php echo htmlspecialchars($statTrend); ?>
        </span>
        <?php endif; ?>
    </div>
    <?php if ($statSubtitle): ?>
    <div class="text-muted small" style="font-size: 0.78rem;"><?php echo htmlspecialchars($statSubtitle); ?></div>
    <?php endif; ?>
    <?php if ($statUrl): ?>
    <a href="<?php echo htmlspecialchars($statUrl); ?>" class="stretched-link" aria-label="<?php echo htmlspecialchars($statLabel); ?>"></a>
    <?php endif; ?>
</div>
