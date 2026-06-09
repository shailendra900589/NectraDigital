<?php
require_once __DIR__ . '/init.php';

use Growth\Engines\IndexingEngine;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = [
        'url_pattern', 'url_pattern_city', 'url_pattern_industry',
        'founder_name', 'founder_title', 'founder_experience', 'founder_linkedin',
        'batch_size', 'index_batch_size', 'auto_sitemap', 'auto_index_queue',
        'indexnow_api_key', 'cron_token',
        'index_engine_indexnow', 'index_engine_bing', 'index_engine_yandex',
        'index_engine_duckduckgo', 'index_engine_google_sitemap', 'index_engine_bing_sitemap',
    ];
    $db = ge_conn();
    $stmt = $db->prepare("INSERT INTO ge_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            $val = $_POST[$key];
            $stmt->bind_param('ss', $key, $val);
            $stmt->execute();
        }
    }
    if (!empty($_POST['indexnow_api_key'])) {
        IndexingEngine::ensureKeyFile(trim($_POST['indexnow_api_key']));
    }
    ge_admin_flash('success', 'Settings saved.');
    header('Location: settings.php');
    exit;
}

$idxKey = ge_setting('indexnow_api_key', '');
if ($idxKey === '' && class_exists(\Growth\Engines\IndexingEngine::class)) {
    $idxKey = \Growth\Engines\IndexingEngine::apiKey();
}

ge_admin_layout();
ge_admin_layout_start('Settings', 'settings');
?>

<div class="ge-card">
    <form method="POST">
        <div class="row g-3">
            <div class="col-12"><h2 class="h6">URL Patterns</h2></div>
            <div class="col-md-6"><label class="form-label">City Landing Pattern</label><input type="text" name="url_pattern_city" class="form-control" value="<?php echo htmlspecialchars(ge_setting('url_pattern_city', ge_setting('url_pattern', '{url_prefix}-company-in-{city_slug}'))); ?>"><small class="text-muted">{url_prefix}, {city_slug}</small></div>
            <div class="col-md-6"><label class="form-label">Industry Landing Pattern</label><input type="text" name="url_pattern_industry" class="form-control" value="<?php echo htmlspecialchars(ge_setting('url_pattern_industry', '{url_prefix}-company-in-{city_slug}-for-{industry_slug}')); ?>"><small class="text-muted">+ {industry_slug}</small></div>
            <div class="col-md-4"><label class="form-label">Generation Batch Size</label><input type="number" name="batch_size" class="form-control" value="<?php echo htmlspecialchars(ge_setting('batch_size', '50')); ?>"></div>
            <div class="col-md-4"><label class="form-label">Index Batch Size</label><input type="number" name="index_batch_size" class="form-control" value="<?php echo htmlspecialchars(ge_setting('index_batch_size', '50')); ?>"></div>
            <input type="hidden" name="url_pattern" value="<?php echo htmlspecialchars(ge_setting('url_pattern_city', ge_setting('url_pattern', '{url_prefix}-company-in-{city_slug}'))); ?>">

            <div class="col-12"><hr><h2 class="h6">Auto Indexing — Search Engines</h2></div>
            <div class="col-md-6"><label class="form-label">IndexNow API Key</label><input type="text" name="indexnow_api_key" class="form-control" value="<?php echo htmlspecialchars($idxKey); ?>"><small class="text-muted">Key file auto-created at /<?php echo htmlspecialchars($idxKey); ?>.txt</small></div>
            <div class="col-md-6"><label class="form-label">Cron Token (for web cron)</label><input type="text" name="cron_token" class="form-control" value="<?php echo htmlspecialchars(ge_setting('cron_token', '')); ?>" placeholder="random-secret-token"></div>

            <?php
            $engines = [
                'indexnow' => 'IndexNow API (multi-engine)',
                'bing' => 'Bing IndexNow',
                'yandex' => 'Yandex IndexNow',
                'duckduckgo' => 'DuckDuckGo (via IndexNow)',
                'google_sitemap' => 'Google Sitemap Ping',
                'bing_sitemap' => 'Bing Sitemap Ping',
            ];
            foreach ($engines as $ek => $label):
                $val = ge_setting('index_engine_' . $ek, '1');
            ?>
            <div class="col-md-4"><label class="form-label"><?php echo $label; ?></label><select name="index_engine_<?php echo $ek; ?>" class="form-select"><option value="1" <?php echo $val==='1'?'selected':''; ?>>Enabled</option><option value="0" <?php echo $val==='0'?'selected':''; ?>>Disabled</option></select></div>
            <?php endforeach; ?>

            <div class="col-12"><hr><h2 class="h6">Founder / EEAT</h2></div>
            <div class="col-md-6"><label class="form-label">Founder Name</label><input type="text" name="founder_name" class="form-control" value="<?php echo htmlspecialchars(ge_setting('founder_name')); ?>"></div>
            <div class="col-md-6"><label class="form-label">Founder Title</label><input type="text" name="founder_title" class="form-control" value="<?php echo htmlspecialchars(ge_setting('founder_title')); ?>"></div>
            <div class="col-md-6"><label class="form-label">Experience</label><input type="text" name="founder_experience" class="form-control" value="<?php echo htmlspecialchars(ge_setting('founder_experience')); ?>"></div>
            <div class="col-md-6"><label class="form-label">LinkedIn URL</label><input type="url" name="founder_linkedin" class="form-control" value="<?php echo htmlspecialchars(ge_setting('founder_linkedin')); ?>"></div>

            <div class="col-12"><hr><h2 class="h6">Automation</h2></div>
            <div class="col-md-6"><label class="form-label">Auto-update Sitemap</label><select name="auto_sitemap" class="form-select"><option value="1" <?php echo ge_setting('auto_sitemap')==='1'?'selected':''; ?>>Enabled</option><option value="0">Disabled</option></select></div>
            <div class="col-md-6"><label class="form-label">Auto Index Queue on Generate</label><select name="auto_index_queue" class="form-select"><option value="1" <?php echo ge_setting('auto_index_queue')==='1'?'selected':''; ?>>Enabled</option><option value="0">Disabled</option></select></div>
            <div class="col-12"><button type="submit" class="btn btn-ge-primary">Save Settings</button> <a href="../dashboard.php?page=seo" class="btn btn-outline-secondary ms-2">Open Indexing Panel</a></div>
        </div>
    </form>
</div>

<div class="ge-card mt-4">
    <h2 class="h6 mb-3">Database Migration</h2>
    <p class="text-muted small">Run once to create Growth Engine tables.</p>
    <a href="../../database/migrate.php" target="_blank" class="btn btn-outline-secondary">Run Migration</a>
</div>

<?php ge_admin_layout_end(); ?>
