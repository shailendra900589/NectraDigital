<?php
/**
 * Admin Shared Filter Bar Partial
 * Parameters:
 * - $filterAction (string)
 * - $filterSearchQuery (string|null)
 * - $filterSearchPlaceholder (string)
 * - $filterDropdowns (array of ['name' => '', 'label' => '', 'value' => '', 'options' => ['key' => 'Label']])
 * - $filterResetUrl (string|null)
 * - $filterHidden (array of key => val)
 */
$filterAction = $filterAction ?? '';
$filterSearchQuery = $filterSearchQuery ?? '';
$filterSearchPlaceholder = $filterSearchPlaceholder ?? 'Search records…';
$filterDropdowns = $filterDropdowns ?? [];
$filterResetUrl = $filterResetUrl ?? null;
$filterHidden = $filterHidden ?? [];
?>
<div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
    <form method="GET" action="<?php echo htmlspecialchars($filterAction); ?>" class="row g-2 align-items-end">
        <?php foreach ($filterHidden as $hKey => $hVal): ?>
        <input type="hidden" name="<?php echo htmlspecialchars($hKey); ?>" value="<?php echo htmlspecialchars((string)$hVal); ?>">
        <?php endforeach; ?>

        <div class="col-12 col-md-4">
            <label class="form-label small text-muted fw-semibold mb-1" style="font-size: 0.75rem;">SEARCH</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                <input type="search" name="q" value="<?php echo htmlspecialchars($filterSearchQuery); ?>" 
                       class="form-control form-control-sm border-start-0" 
                       placeholder="<?php echo htmlspecialchars($filterSearchPlaceholder); ?>">
            </div>
        </div>

        <?php foreach ($filterDropdowns as $dd): ?>
        <div class="col-6 col-md-3">
            <label class="form-label small text-muted fw-semibold mb-1" style="font-size: 0.75rem;"><?php echo htmlspecialchars(strtoupper($dd['label'] ?? 'FILTER')); ?></label>
            <select name="<?php echo htmlspecialchars($dd['name']); ?>" class="form-select form-select-sm">
                <?php foreach (($dd['options'] ?? []) as $optVal => $optLabel): ?>
                <option value="<?php echo htmlspecialchars((string)$optVal); ?>" <?php echo ((string)($dd['value'] ?? '') === (string)$optVal) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($optLabel); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endforeach; ?>

        <div class="col-12 col-md d-flex gap-2 justify-content-md-end mt-2 mt-md-0">
            <button type="submit" class="btn btn-sm btn-primary px-3">
                <i class="fas fa-filter me-1"></i> Apply
            </button>
            <?php if ($filterResetUrl): ?>
            <a href="<?php echo htmlspecialchars($filterResetUrl); ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-rotate-left me-1"></i> Reset
            </a>
            <?php endif; ?>
        </div>
    </form>
</div>
