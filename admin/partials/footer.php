<?php
/**
 * Admin Shared Footer Partial
 * Parameters:
 * - $adminVersion (string|null)
 * - $adminScripts (array of script paths)
 * - $includeCkeditor (bool)
 */
$adminVersion = $adminVersion ?? (function_exists('admin_platform_version') ? admin_platform_version() : '2.0');
$adminScripts = $adminScripts ?? [];
$includeCkeditor = !empty($includeCkeditor);
$assetBase = defined('SITE_URL') ? rtrim(SITE_URL, '/') . '/assets/' : '../assets/';
?>
        </div><!-- /.ge-content -->
    </main><!-- /.ge-main -->
</div><!-- /.ge-layout -->

<footer class="py-3 px-4 border-top bg-white text-muted small d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
    <div>
        &copy; <?php echo date('Y'); ?> <strong class="text-dark">Nectra Digital</strong>. Enterprise Control Center.
    </div>
    <div class="d-flex align-items-center gap-3">
        <span>Version <code class="text-dark">v<?php echo htmlspecialchars($adminVersion); ?></code></span>
        <span>&middot;</span>
        <a href="<?php echo defined('SITE_URL') ? htmlspecialchars(SITE_URL) : '../'; ?>" target="_blank" rel="noopener" class="text-muted text-decoration-none hover-primary">
            <i class="fas fa-external-link-alt fa-xs me-1"></i> Public Site
        </a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo htmlspecialchars($assetBase); ?>js/admin.js?v=2"></script>
<script src="<?php echo htmlspecialchars($assetBase); ?>js/growth-admin.js?v=2"></script>
<?php foreach ($adminScripts as $s): ?>
<script src="<?php echo htmlspecialchars($s); ?>"></script>
<?php endforeach; ?>
<?php if ($includeCkeditor && function_exists('nectra_ckeditor_scripts')): ?>
    <?php nectra_ckeditor_scripts($assetBase); ?>
<?php endif; ?>
</body>
</html>
