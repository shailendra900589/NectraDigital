<?php
/**
 * Admin Shared Table Toolbar Partial
 * Parameters:
 * - $toolbarTotalCount (int|null)
 * - $toolbarTotalLabel (string)
 * - $toolbarBulkActions (array of ['action' => '', 'label' => '', 'class' => ''])
 * - $toolbarExports (array of ['url' => '', 'label' => '', 'icon' => ''])
 * - $toolbarCustomHtml (string|null)
 */
$toolbarTotalCount = $toolbarTotalCount ?? null;
$toolbarTotalLabel = $toolbarTotalLabel ?? 'Records';
$toolbarBulkActions = $toolbarBulkActions ?? [];
$toolbarExports = $toolbarExports ?? [];
$toolbarCustomHtml = $toolbarCustomHtml ?? null;
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-2 px-3 border-bottom bg-light rounded-top-3">
    <div class="d-flex align-items-center gap-2">
        <?php if (!empty($toolbarBulkActions)): ?>
        <div class="dropdown" id="bulkActionsDropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-check-square me-1"></i> Bulk Actions
            </button>
            <ul class="dropdown-menu shadow-sm">
                <?php foreach ($toolbarBulkActions as $ba): ?>
                <li>
                    <button type="submit" name="bulk_action" value="<?php echo htmlspecialchars($ba['action']); ?>" 
                            class="dropdown-item <?php echo htmlspecialchars($ba['class'] ?? ''); ?>"
                            <?php if (!empty($ba['confirm'])): ?>onclick="return confirm('<?php echo htmlspecialchars(addslashes($ba['confirm'])); ?>')"<?php endif; ?>>
                        <?php echo htmlspecialchars($ba['label']); ?>
                    </button>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if ($toolbarTotalCount !== null): ?>
        <span class="text-muted small">
            Total: <strong class="text-dark"><?php echo number_format($toolbarTotalCount); ?></strong> <?php echo htmlspecialchars($toolbarTotalLabel); ?>
        </span>
        <?php endif; ?>

        <?php if ($toolbarCustomHtml): ?>
        <?php echo $toolbarCustomHtml; ?>
        <?php endif; ?>
    </div>

    <?php if (!empty($toolbarExports)): ?>
    <div class="d-flex align-items-center gap-2">
        <?php foreach ($toolbarExports as $exp): ?>
        <a href="<?php echo htmlspecialchars($exp['url']); ?>" class="<?php echo htmlspecialchars($exp['class'] ?? 'btn btn-sm btn-outline-secondary'); ?>">
            <i class="<?php echo htmlspecialchars($exp['icon'] ?? 'fas fa-download'); ?> me-1"></i>
            <?php echo htmlspecialchars($exp['label']); ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
