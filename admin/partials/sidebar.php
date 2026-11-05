<?php
/**
 * Admin Shared Sidebar Partial
 * Navy #081426, modern active links, grouped sections
 */
require_once dirname(__DIR__) . '/includes/admin-nav.php';
$adminCtx = admin_layout_context();
$activePage = $activePage ?? '';
$navItems = admin_nav_items();
?>
<aside class="ge-sidebar" id="adminSidebar">
    <!-- Brand / Console Header -->
    <div class="ge-brand ge-sidebar-brand p-3 d-flex align-items-center justify-content-between border-bottom border-secondary border-opacity-25" style="background: rgba(0,0,0,0.2);">
        <div>
            <div class="fw-bold text-white fs-5" style="letter-spacing: -0.02em;">
                <span class="text-white">NECTRA</span><span class="text-primary">ADMIN</span>
            </div>
            <small class="text-white-50" style="font-size: 0.7rem;">Control Center v2.5</small>
        </div>
        <button type="button" class="btn btn-sm text-white-50 d-md-none" id="sidebarCloseBtn" aria-label="Close sidebar">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Navigation Scroll List -->
    <nav class="ge-nav ge-sidebar-scroll py-2" aria-label="Admin Navigation">
        <?php foreach ($navItems as $item): ?>
            <?php if (!empty($item['section'])): ?>
            <div class="ge-nav-section text-uppercase px-3 pt-3 pb-1 text-white-50 small fw-bold" style="font-size: 0.65rem; letter-spacing: 0.8px;">
                <?php echo htmlspecialchars($item['section']); ?>
            </div>
            <?php else: ?>
            <a href="<?php echo htmlspecialchars(admin_nav_resolve_url($item['url'])); ?>"
               class="ge-nav-link d-flex align-items-center gap-2 px-3 py-2 text-decoration-none text-white-50 <?php echo ($activePage === ($item['id'] ?? '')) ? 'active text-white' : ''; ?>">
                <i class="fas <?php echo htmlspecialchars($item['icon']); ?>" style="width: 20px;"></i>
                <span class="small fw-medium"><?php echo htmlspecialchars($item['label']); ?></span>
            </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>

    <!-- Footer Links -->
    <div class="ge-sidebar-footer p-3 border-top border-secondary border-opacity-25" style="background: rgba(0,0,0,0.2);">
        <a href="<?php echo htmlspecialchars(admin_site_url()); ?>" target="_blank" rel="noopener" class="d-flex align-items-center gap-2 text-white-50 text-decoration-none small mb-2">
            <i class="fas fa-external-link-alt" style="width: 20px;"></i> View Website
        </a>
        <a href="<?php echo htmlspecialchars($adminCtx['root']); ?>logout.php" class="d-flex align-items-center gap-2 text-danger text-decoration-none small fw-semibold">
            <i class="fas fa-power-off" style="width: 20px;"></i> Sign Out
        </a>
    </div>
</aside>
