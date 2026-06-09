<?php
require_once __DIR__ . '/../../includes/growth/bootstrap.php';

function ge_admin_page_title(string $title): string {
    return htmlspecialchars($title) . ' — Nectra Growth Engine';
}

function ge_admin_nav(): array {
    return [
        ['url' => 'index.php', 'icon' => 'fa-chart-line', 'label' => 'Dashboard', 'page' => 'dashboard'],
        ['url' => 'services.php', 'icon' => 'fa-cogs', 'label' => 'Services', 'page' => 'services'],
        ['url' => 'cities.php', 'icon' => 'fa-map-marker-alt', 'label' => 'Cities', 'page' => 'cities'],
        ['url' => 'keywords.php', 'icon' => 'fa-key', 'label' => 'Keywords', 'page' => 'keywords'],
        ['url' => 'landing-pages.php', 'icon' => 'fa-file-alt', 'label' => 'Landing Pages', 'page' => 'landing'],
        ['url' => 'generate.php', 'icon' => 'fa-magic', 'label' => 'Generate', 'page' => 'generate'],
        ['url' => 'indexing.php', 'icon' => 'fa-search-plus', 'label' => 'Indexing', 'page' => 'indexing'],
        ['url' => 'case-studies.php', 'icon' => 'fa-trophy', 'label' => 'Case Studies', 'page' => 'case-studies'],
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
    <link rel="stylesheet" href="../../assets/css/growth-admin.css">
</head>
<body class="ge-admin">
<div class="ge-layout">
    <aside class="ge-sidebar">
        <div class="ge-brand">
            <span class="ge-brand-text">NECTRA</span><span class="ge-brand-accent">GROWTH</span>
            <small class="d-block text-muted mt-1">SEO Growth Engine</small>
        </div>
        <nav class="ge-nav">
            <?php foreach (ge_admin_nav() as $item): ?>
            <a href="<?php echo $item['url']; ?>" class="ge-nav-link <?php echo $activePage === $item['page'] ? 'active' : ''; ?>">
                <i class="fas <?php echo $item['icon']; ?>"></i> <?php echo $item['label']; ?>
            </a>
            <?php endforeach; ?>
            <hr class="border-secondary my-2">
            <a href="../dashboard.php" class="ge-nav-link"><i class="fas fa-arrow-left"></i> Legacy Admin</a>
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

function ge_admin_layout_end(): void {
    ?>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/growth-admin.js"></script>
</body>
</html>
    <?php
}
