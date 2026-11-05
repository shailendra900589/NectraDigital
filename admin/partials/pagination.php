<?php
/**
 * Admin Shared Pagination Partial
 * Parameters:
 * - $currentPage (int)
 * - $totalPages (int)
 * - $totalRecords (int)
 * - $perPage (int)
 * - $queryParams (array)
 */
$currentPage = max(1, (int)($currentPage ?? 1));
$totalPages = max(1, (int)($totalPages ?? 1));
$totalRecords = (int)($totalRecords ?? 0);
$perPage = max(1, (int)($perPage ?? 20));
$queryParams = $queryParams ?? [];

if ($totalPages <= 1 && $totalRecords <= $perPage) {
    return;
}

unset($queryParams['p'], $queryParams['page_num']);
$buildUrl = static function (int $p) use ($queryParams): string {
    return '?' . http_build_query(array_merge($queryParams, ['p' => $p]));
};

$from = ($currentPage - 1) * $perPage + 1;
$to = min($totalRecords, $currentPage * $perPage);

// Smart page window
$adjacent = 2;
$pageNumbers = [1];
$left = max(2, $currentPage - $adjacent);
$right = min($totalPages - 1, $currentPage + $adjacent);
if ($left > 2) {
    $pageNumbers[] = 0; // ellipsis
}
for ($i = $left; $i <= $right; $i++) {
    $pageNumbers[] = $i;
}
if ($right < $totalPages - 1) {
    $pageNumbers[] = 0; // ellipsis
}
if ($totalPages > 1) {
    $pageNumbers[] = $totalPages;
}
?>
<div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 p-3 border-top bg-white rounded-bottom-3">
    <div class="text-muted small">
        Showing <strong><?php echo number_format($from); ?></strong> to <strong><?php echo number_format($to); ?></strong> of <strong><?php echo number_format($totalRecords); ?></strong> entries
        · Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>
    </div>
    <nav aria-label="Table pagination">
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo $currentPage <= 1 ? '#' : htmlspecialchars($buildUrl($currentPage - 1)); ?>" aria-label="Previous">
                    <i class="fas fa-chevron-left fa-xs"></i>
                </a>
            </li>
            <?php foreach ($pageNumbers as $pNum): ?>
                <?php if ($pNum === 0): ?>
                <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php else: ?>
                <li class="page-item <?php echo $pNum === $currentPage ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo htmlspecialchars($buildUrl($pNum)); ?>"><?php echo $pNum; ?></a>
                </li>
                <?php endif; ?>
            <?php endforeach; ?>
            <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo $currentPage >= $totalPages ? '#' : htmlspecialchars($buildUrl($currentPage + 1)); ?>" aria-label="Next">
                    <i class="fas fa-chevron-right fa-xs"></i>
                </a>
            </li>
        </ul>
    </nav>
</div>
