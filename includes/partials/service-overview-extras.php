<?php
/** Fills overview column so it balances the sidebar (all service pages). */
$features = array_slice($service['features'] ?? [], 0, 6);
$stats = $service['stats'] ?? [];
?>
<?php if (!empty($stats)): ?>
<div class="row g-3 svc-at-a-glance mb-4" data-nd-stagger>
    <?php foreach ($stats as $stat): ?>
    <div class="col-6 col-md-3 col-lg-6 col-xl-3">
        <div class="svc-glance-stat text-center p-3 h-100">
            <div class="svc-glance-val fw-bold text-primary"><?php echo htmlspecialchars($stat['value']); ?></div>
            <div class="svc-glance-lbl small text-muted"><?php echo htmlspecialchars($stat['label']); ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($features)): ?>
<div class="svc-capabilities">
    <h3 class="h6 fw-bold text-dark mb-3">Core capabilities included</h3>
    <div class="row g-2" data-nd-stagger>
        <?php foreach ($features as $feat): ?>
        <div class="col-md-6">
            <div class="svc-capability-item d-flex align-items-start gap-2 p-3 h-100">
                <i class="fas fa-check-circle text-primary mt-1 flex-shrink-0"></i>
                <span class="small text-secondary fw-medium"><?php echo htmlspecialchars($feat); ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
