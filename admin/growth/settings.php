<?php
require_once 'includes/auth.php';
require_once __DIR__ . '/../../includes/growth/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = ['url_pattern', 'founder_name', 'founder_title', 'founder_experience', 'founder_linkedin', 'batch_size', 'auto_sitemap', 'auto_index_queue'];
    $db = ge_conn();
    $stmt = $db->prepare("INSERT INTO ge_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            $val = $_POST[$key];
            $stmt->bind_param('ss', $key, $val);
            $stmt->execute();
        }
    }
    ge_admin_flash('success', 'Settings saved.');
    header('Location: settings.php');
    exit;
}

require_once 'includes/layout.php';
ge_admin_layout_start('Settings', 'settings');
?>

<div class="ge-card">
    <form method="POST">
        <div class="row g-3">
            <div class="col-12"><h2 class="h6">URL Pattern</h2></div>
            <div class="col-md-8"><label class="form-label">Landing Page URL Pattern</label><input type="text" name="url_pattern" class="form-control" value="<?php echo htmlspecialchars(ge_setting('url_pattern', '{url_prefix}-company-{city_slug}')); ?>"><small class="text-muted">Tokens: {url_prefix}, {city_slug}, {service_slug}</small></div>
            <div class="col-md-4"><label class="form-label">Batch Size</label><input type="number" name="batch_size" class="form-control" value="<?php echo htmlspecialchars(ge_setting('batch_size', '50')); ?>"></div>
            <div class="col-12"><hr><h2 class="h6">Founder / EEAT</h2></div>
            <div class="col-md-6"><label class="form-label">Founder Name</label><input type="text" name="founder_name" class="form-control" value="<?php echo htmlspecialchars(ge_setting('founder_name')); ?>"></div>
            <div class="col-md-6"><label class="form-label">Founder Title</label><input type="text" name="founder_title" class="form-control" value="<?php echo htmlspecialchars(ge_setting('founder_title')); ?>"></div>
            <div class="col-md-6"><label class="form-label">Experience</label><input type="text" name="founder_experience" class="form-control" value="<?php echo htmlspecialchars(ge_setting('founder_experience')); ?>"></div>
            <div class="col-md-6"><label class="form-label">LinkedIn URL</label><input type="url" name="founder_linkedin" class="form-control" value="<?php echo htmlspecialchars(ge_setting('founder_linkedin')); ?>"></div>
            <div class="col-12"><hr><h2 class="h6">Automation</h2></div>
            <div class="col-md-6"><label class="form-label">Auto-update Sitemap</label><select name="auto_sitemap" class="form-select"><option value="1" <?php echo ge_setting('auto_sitemap')==='1'?'selected':''; ?>>Enabled</option><option value="0">Disabled</option></select></div>
            <div class="col-md-6"><label class="form-label">Auto Index Queue</label><select name="auto_index_queue" class="form-select"><option value="1" <?php echo ge_setting('auto_index_queue')==='1'?'selected':''; ?>>Enabled</option><option value="0">Disabled</option></select></div>
            <div class="col-12"><button type="submit" class="btn btn-ge-primary">Save Settings</button></div>
        </div>
    </form>
</div>

<div class="ge-card mt-4">
    <h2 class="h6 mb-3">Database Migration</h2>
    <p class="text-muted small">Run once to create Growth Engine tables.</p>
    <a href="../../database/migrate.php" target="_blank" class="btn btn-outline-secondary">Run Migration</a>
</div>

<?php ge_admin_layout_end(); ?>
