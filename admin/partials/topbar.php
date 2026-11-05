<?php
/**
 * Admin Shared Topbar Partial
 */
$pageContext = $pageContext ?? 'Dashboard';
?>
<header class="ge-topbar d-flex align-items-center justify-content-between px-4 py-2 bg-white border-bottom shadow-sm">
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <div class="d-flex align-items-center gap-2 small text-muted">
            <i class="fas fa-layer-group text-primary"></i>
            <span>Nectra Console</span>
            <span>/</span>
            <span class="text-dark fw-semibold"><?php echo htmlspecialchars($pageContext); ?></span>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Quick Add / Action Shortcut -->
        <a href="/admin/create_post.php" class="btn btn-sm btn-outline-primary d-none d-sm-inline-flex align-items-center gap-1">
            <i class="fas fa-plus"></i> New Post
        </a>

        <!-- Live Website Preview -->
        <a href="/" target="_blank" rel="noopener" class="btn btn-sm btn-light border text-secondary" title="View Public Site">
            <i class="fas fa-globe"></i>
        </a>

        <!-- Admin Profile Indicator -->
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem; font-weight: 700;">
                A
            </div>
            <span class="small fw-semibold text-dark d-none d-md-inline">Administrator</span>
        </div>
    </div>
</header>
