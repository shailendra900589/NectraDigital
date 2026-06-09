<?php
if (!defined('GE_BOOTSTRAP_LOADED')) {
    require_once __DIR__ . '/../../includes/growth/bootstrap.php';
}

function ge_admin_page_title(string $title): string {
    return htmlspecialchars($title) . ' — Nectra Growth Engine';
}

function ge_admin_nav(): array {
    return [
        ['section' => 'Main Admin'],
        ['url' => '../dashboard.php?page=home', 'icon' => 'fa-home', 'label' => 'NECTRAOS Dashboard', 'page' => 'nectraos'],
        ['section' => 'Overview'],
        ['url' => '../dashboard.php?page=home', 'icon' => 'fa-chart-line', 'label' => 'Growth Overview', 'page' => 'dashboard'],
        ['section' => 'Programmatic SEO'],
        ['url' => 'services.php', 'icon' => 'fa-cogs', 'label' => 'Services', 'page' => 'services'],
        ['url' => 'cities.php', 'icon' => 'fa-map-marker-alt', 'label' => 'Cities', 'page' => 'cities'],
        ['url' => 'industries.php', 'icon' => 'fa-industry', 'label' => 'Industries', 'page' => 'industries'],
        ['url' => 'keywords.php', 'icon' => 'fa-key', 'label' => 'Keywords', 'page' => 'keywords'],
        ['url' => 'landing-pages.php', 'icon' => 'fa-file-alt', 'label' => 'Landing Pages', 'page' => 'landing'],
        ['url' => 'generate.php', 'icon' => 'fa-magic', 'label' => 'Generate', 'page' => 'generate'],
        ['section' => 'Content & EEAT'],
        ['url' => 'case-studies.php', 'icon' => 'fa-trophy', 'label' => 'Case Studies', 'page' => 'case-studies'],
        ['url' => 'authors.php', 'icon' => 'fa-user-edit', 'label' => 'Authors', 'page' => 'authors'],
        ['url' => 'knowledge-base.php', 'icon' => 'fa-book', 'label' => 'Knowledge Base', 'page' => 'knowledge'],
        ['section' => 'SEO & Indexing'],
        ['url' => 'indexing.php', 'icon' => 'fa-search-plus', 'label' => 'Indexing', 'page' => 'indexing'],
        ['url' => 'competitor.php', 'icon' => 'fa-crosshairs', 'label' => 'Competitor Intel', 'page' => 'competitor'],
        ['section' => 'CRM & Sales'],
        ['url' => 'leads.php', 'icon' => 'fa-user-plus', 'label' => 'Leads', 'page' => 'leads'],
        ['section' => 'Platform'],
        ['url' => 'tools.php', 'icon' => 'fa-toolbox', 'label' => 'Tools', 'page' => 'tools'],
        ['url' => 'analytics.php', 'icon' => 'fa-chart-bar', 'label' => 'Analytics', 'page' => 'analytics'],
        ['url' => 'settings.php', 'icon' => 'fa-sliders-h', 'label' => 'Settings', 'page' => 'settings'],
    ];
}

function ge_admin_layout_start(string $title, string $activePage = ''): void {
    $flash = ge_admin_get_flash();
    ?><!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ge_admin_page_title($title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/growth-admin.css?v=3">
    <?php require_once __DIR__ . '/../../../includes/ckeditor.php'; nectra_ckeditor_styles(); ?>
</head>
<body class="ge-admin">
<div class="ge-layout">
    <aside class="ge-sidebar">
        <div class="ge-brand">
            <span class="ge-brand-text">NECTRA</span><span class="ge-brand-accent">GROWTH</span>
            <small class="d-block text-muted mt-1">Enterprise Growth Platform v<?php echo htmlspecialchars(ge_setting('platform_version', '2.0')); ?></small>
        </div>
        <nav class="ge-nav">
            <?php foreach (ge_admin_nav() as $item): ?>
            <?php if (!empty($item['section'])): ?>
            <div class="ge-nav-section"><?php echo htmlspecialchars($item['section']); ?></div>
            <?php else: ?>
            <a href="<?php echo $item['url']; ?>" class="ge-nav-link <?php echo $activePage === $item['page'] ? 'active' : ''; ?>">
                <i class="fas <?php echo $item['icon']; ?>"></i> <?php echo $item['label']; ?>
            </a>
            <?php endif; ?>
            <?php endforeach; ?>
            <hr class="border-secondary my-2">
            <a href="../dashboard.php?page=home" class="ge-nav-link"><i class="fas fa-arrow-left"></i> NECTRAOS Admin</a>
            <a href="../logout.php" class="ge-nav-link text-danger"><i class="fas fa-power-off"></i> Logout</a>
        </nav>
    </aside>
    <main class="ge-main">
        <header class="ge-topbar">
            <h1 class="ge-page-title"><?php echo htmlspecialchars($title); ?></h1>
            <div class="ge-topbar-actions">
                <button type="button" class="btn btn-sm ge-theme-toggle" id="themeToggle" title="Toggle theme"><i class="fas fa-moon"></i></button>
                <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-external-link-alt"></i> View Site</a>
            </div>
        </header>
        <div class="ge-content">
            <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            <?php if (!ge_is_ready()): ?>
            <div class="alert alert-warning">
                <strong>Database not migrated.</strong> Run <a href="../../database/migrate.php" target="_blank">database/migrate.php</a> to create Growth Engine tables.
            </div>
            <?php endif; ?>
<?php
}

/** Page numbers with ellipsis for large result sets. */
function ge_pagination_range(int $current, int $totalPages, int $adjacent = 2): array
{
    if ($totalPages <= 1) {
        return [];
    }
    if ($totalPages <= 7) {
        return range(1, $totalPages);
    }

    $pages = [1];
    $left = max(2, $current - $adjacent);
    $right = min($totalPages - 1, $current + $adjacent);

    if ($left > 2) {
        $pages[] = 0;
    }
    for ($i = $left; $i <= $right; $i++) {
        $pages[] = $i;
    }
    if ($right < $totalPages - 1) {
        $pages[] = 0;
    }
    $pages[] = $totalPages;

    return $pages;
}

/**
 * Render compact admin pagination (prev / 1 … 5 6 7 … 39 / next).
 *
 * @param array{page:int,pages:int,total:int,per_page:int} $pg from ge_paginate()
 * @param array<string,mixed> $queryParams preserved in links (page key excluded)
 */
function ge_admin_pagination(array $pg, array $queryParams = []): void
{
    $current = (int)($pg['page'] ?? 1);
    $totalPages = (int)($pg['pages'] ?? 1);
    $total = (int)($pg['total'] ?? 0);

    if ($totalPages <= 1) {
        return;
    }

    unset($queryParams['page']);
    $buildUrl = static function (int $p) use ($queryParams): string {
        $params = array_merge($queryParams, ['page' => $p]);
        return '?' . http_build_query($params);
    };

    $from = ($current - 1) * (int)($pg['per_page'] ?? 50) + 1;
    $to = min($total, $current * (int)($pg['per_page'] ?? 50));
    ?>
    <div class="ge-pagination-wrap">
        <div class="ge-pagination-meta text-muted small">
            Showing <?php echo number_format($from); ?>–<?php echo number_format($to); ?> of <?php echo number_format($total); ?>
            · Page <?php echo $current; ?> / <?php echo $totalPages; ?>
        </div>
        <nav aria-label="Pagination">
            <ul class="pagination pagination-sm ge-pagination mb-0">
                <li class="page-item <?php echo $current <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo $current <= 1 ? '#' : htmlspecialchars($buildUrl($current - 1)); ?>" aria-label="Previous">&laquo;</a>
                </li>
                <?php foreach (ge_pagination_range($current, $totalPages) as $p): ?>
                    <?php if ($p === 0): ?>
                <li class="page-item disabled"><span class="page-link ge-page-ellipsis">…</span></li>
                    <?php else: ?>
                <li class="page-item <?php echo $p === $current ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo htmlspecialchars($buildUrl($p)); ?>"><?php echo $p; ?></a>
                </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                <li class="page-item <?php echo $current >= $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo $current >= $totalPages ? '#' : htmlspecialchars($buildUrl($current + 1)); ?>" aria-label="Next">&raquo;</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php
}

function ge_admin_layout_end(): void {
    ?>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/growth-admin.js"></script>
<?php nectra_ckeditor_scripts('../../assets'); ?>
</body>
</html>
    <?php
}
