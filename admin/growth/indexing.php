<?php
require_once __DIR__ . '/init.php';

use Growth\Models\LandingPage;
use Growth\Models\IndexingQueue;
use Growth\Engines\IndexingEngine;
use Growth\Engines\DiscoveryEngine;
use Growth\Engines\SeoRefreshEngine;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'mark_indexed' && !empty($_POST['page_id'])) {
        $db = ge_conn();
        $id = (int)$_POST['page_id'];
        $db->query("UPDATE ge_landing_pages SET index_status='indexed', is_indexed=1, index_verified_at=NOW() WHERE id=$id");
        ge_admin_flash('success', 'Marked as indexed.');
    }

    if ($action === 'queue_all_pending') {
        $r = IndexingEngine::queueAllPending(5000, false);
        ge_admin_flash('success', "Queued {$r['queued']} pages for indexing.");
    }

    if ($action === 'process_queue') {
        $r = IndexingEngine::processAllQueue((int)ge_setting('index_batch_size', 100));
        ge_admin_flash('success', "Submitted {$r['processed']} URLs via IndexNow + Bing API. Failed: {$r['failed']}. ({$r['batches']} batches)");
    }

    if ($action === 'ping_sitemap') {
        $r = IndexingEngine::pingSitemap();
        ge_admin_flash($r['ok'] ? 'success' : 'error', $r['ok'] ? 'All sitemaps & feeds pinged (Google, Bing, Yandex).' : 'Sitemap ping failed.');
    }

    if ($action === 'submit_all_indexnow') {
        set_time_limit(300);
        $r = IndexingEngine::submitAllPublishedUrls(true, true);
        $n = (int)($r['urls_submitted'] ?? 0);
        $total = (int)($r['urls_total'] ?? 0);
        ge_admin_flash($r['ok'] ? 'success' : 'error', $r['ok']
            ? "IndexNow: submitted {$n} of {$total} URLs."
            : 'IndexNow submission failed.');
    }

    if ($action === 'queue_and_process') {
        $r = IndexingEngine::queueAndSubmitAll(10000);
        $submitted = (int)($r['direct']['urls_submitted'] ?? 0);
        ge_admin_flash('success', "Submitted {$submitted} URLs via IndexNow. Queued: {$r['queued']}.");
    }

    if ($action === 'publish_all') {
        set_time_limit(300);
        $r = DiscoveryEngine::publishAll(10000, 100);
        ge_admin_flash('success', "IndexNow: submitted {$r['processed']} URLs. Queued: {$r['queued']}.");
    }

    if ($action === 'refresh_seo') {
        set_time_limit(300);
        $r = SeoRefreshEngine::refreshAll(true);
        ge_admin_flash('success', "Refreshed SEO on {$r['updated']} pages. Signaled {$r['urls_signaled']} URLs.");
    }

    if ($action === 'bing_submit_sitemap') {
        $siteUrl = rtrim(trim((string)ge_setting('bing_webmaster_site_url', SITE_URL)), '/');
        $urls = [rtrim(SITE_URL, '/') . '/sitemap.xml', rtrim(SITE_URL, '/') . '/news-sitemap.xml'];
        $r = IndexingEngine::submitBingWebmasterUrls($urls);
        ge_admin_flash(
            !empty($r['ok']) ? 'success' : 'error',
            !empty($r['ok'])
                ? 'Bing Webmaster API: sitemap URLs submitted.'
                : ('Bing API failed — ' . ($r['message'] ?? 'check API key in Settings'))
        );
    }

    if ($action === 'bing_test') {
        $siteUrl = rtrim(trim((string)ge_setting('bing_webmaster_site_url', SITE_URL)), '/');
        $r = IndexingEngine::submitBingWebmasterUrls([$siteUrl . '/']);
        ge_admin_flash(
            !empty($r['ok']) ? 'success' : 'error',
            !empty($r['ok'])
                ? 'Bing Webmaster API connected — homepage submitted successfully.'
                : ('Bing API test failed — ' . ($r['message'] ?? 'verify key + site URL in Settings'))
        );
    }

    header('Location: indexing.php');
    exit;
}

$stats = ['total' => 0, 'indexed' => 0, 'pending' => 0, 'submitted' => 0, 'failed' => 0];
$queue = [];
$recentPages = ['data' => []];
$idxInfo = ['key' => '', 'key_url' => '', 'host' => parse_url(SITE_URL, PHP_URL_HOST) ?: ''];
$loadError = null;

try {
    if (ge_is_ready()) {
        $stats = LandingPage::indexStats();
        try {
            $recentPages = LandingPage::paginated(1, 20, ['index_status' => 'pending']);
        } catch (Throwable $e) {
            $recentPages = LandingPage::paginated(1, 20, []);
        }
    }
    if (ge_table_exists('ge_indexing_queue')) {
        $queue = IndexingQueue::all(30);
    }
    $key = IndexingEngine::apiKey();
    $idxInfo = ['key' => $key, 'key_url' => IndexingEngine::keyFileUrl($key), 'host' => IndexingEngine::host()];
} catch (Throwable $e) {
    $loadError = $e->getMessage();
    error_log('growth/indexing load: ' . $e->getMessage());
}

$bingKey = trim((string)ge_setting('bing_webmaster_api_key', ''));
$bingSite = rtrim(trim((string)ge_setting('bing_webmaster_site_url', SITE_URL)), '/');
$bingApiOn = ge_setting('index_engine_bing_api', '1') === '1';
$cronUrls = function_exists('ge_cron_urls') ? ge_cron_urls() : ['indexing' => '', 'i18n_index' => '', 'discovery' => ''];

ge_admin_layout();
ge_admin_layout_start('Indexing Manager', 'indexing');
?>

<?php if ($loadError): ?>
<div class="alert alert-warning">Partial load error: <?php echo htmlspecialchars($loadError); ?>. Core actions may still work.</div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value text-success"><?php echo number_format((int)($stats['indexed'] ?? 0)); ?></div><div class="ge-stat-label">Indexed</div></div></div>
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value text-warning"><?php echo number_format((int)($stats['pending'] ?? 0)); ?></div><div class="ge-stat-label">Pending</div></div></div>
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value"><?php echo number_format((int)($stats['submitted'] ?? 0)); ?></div><div class="ge-stat-label">Submitted</div></div></div>
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value text-danger"><?php echo number_format((int)($stats['failed'] ?? 0)); ?></div><div class="ge-stat-label">Failed</div></div></div>
</div>

<div class="alert alert-info small">
    <strong>Auto engines:</strong> IndexNow · Bing IndexNow · Bing Webmaster URL API · Yandex · DuckDuckGo · Sitemap ping
    <br>Feeds: <a href="<?php echo SITE_URL; ?>/rss.xml" target="_blank">RSS</a> ·
    <a href="<?php echo SITE_URL; ?>/discover-feed.xml" target="_blank">Discover</a> ·
    <a href="<?php echo SITE_URL; ?>/news-sitemap.xml" target="_blank">News</a> ·
    <a href="<?php echo SITE_URL; ?>/atom.xml" target="_blank">Atom</a>
    <br>IndexNow key file: <a href="<?php echo htmlspecialchars($idxInfo['key_url']); ?>" target="_blank"><?php echo htmlspecialchars($idxInfo['key_url']); ?></a>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="ge-card h-100">
            <h2 class="h6 mb-3"><i class="fab fa-microsoft me-1"></i> Bing Webmaster API</h2>
            <?php if ($bingKey !== '' && $bingApiOn): ?>
                <p class="small text-success mb-2"><i class="fas fa-check-circle"></i> API key saved · Site: <code><?php echo htmlspecialchars($bingSite); ?></code></p>
                <form method="POST" class="d-inline"><input type="hidden" name="action" value="bing_test"><button type="submit" class="btn btn-sm btn-outline-success me-2">Test Bing API (submit homepage)</button></form>
                <form method="POST" class="d-inline"><input type="hidden" name="action" value="bing_submit_sitemap"><button type="submit" class="btn btn-sm btn-outline-info">Submit Sitemaps to Bing</button></form>
                <p class="small text-muted mt-3 mb-0">New blogs (listed + orphan) auto-submit via Bing API on publish. Queue cron also sends batches.</p>
            <?php elseif ($bingKey !== '' && !$bingApiOn): ?>
                <p class="small text-warning mb-0">API key saved but disabled — enable in <a href="settings.php">Settings → Bing URL API</a>.</p>
            <?php else: ?>
                <p class="small text-muted mb-2">Paste your Bing Webmaster API key in <a href="settings.php">Growth Settings</a> → Bing Webmaster URL Submission API.</p>
                <a href="settings.php" class="btn btn-sm btn-ge-primary">Open Settings</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="ge-card h-100">
            <h2 class="h6 mb-3"><i class="fas fa-clock me-1"></i> Cron Jobs (Hostinger)</h2>
            <?php if (!empty($cronUrls['indexing'])): ?>
            <code class="small d-block mb-1"><?php echo htmlspecialchars($cronUrls['i18n_index']); ?></code>
            <code class="small d-block mb-1"><?php echo htmlspecialchars($cronUrls['indexing']); ?></code>
            <code class="small d-block mb-0"><?php echo htmlspecialchars($cronUrls['discovery']); ?></code>
            <?php else: ?>
            <p class="small text-muted mb-0">Open <a href="settings.php">Settings</a> to generate cron URLs.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="ge-card">
            <h2 class="h6 mb-3">Indexing Actions</h2>
            <form method="POST" class="d-grid gap-2">
                <input type="hidden" name="action" value="queue_all_pending">
                <button type="submit" class="btn btn-ge-primary">Queue All Pending (500 batch)</button>
            </form>
            <form method="POST" class="d-grid gap-2 mt-2">
                <input type="hidden" name="action" value="process_queue">
                <button type="submit" class="btn btn-success">Process Queue → IndexNow + Bing API</button>
            </form>
            <form method="POST" class="d-grid gap-2 mt-2">
                <input type="hidden" name="action" value="ping_sitemap">
                <button type="submit" class="btn btn-outline-secondary">Ping Sitemap (Google + Bing)</button>
            </form>
            <form method="POST" class="d-grid gap-2 mt-2">
                <input type="hidden" name="action" value="submit_all_indexnow">
                <button type="submit" class="btn btn-warning">Submit ALL URLs → IndexNow</button>
            </form>
            <form method="POST" class="d-grid gap-2 mt-2">
                <input type="hidden" name="action" value="queue_and_process">
                <button type="submit" class="btn btn-outline-success">Queue + Submit All (One Click)</button>
            </form>
            <form method="POST" class="d-grid gap-2 mt-2">
                <input type="hidden" name="action" value="publish_all">
                <button type="submit" class="btn btn-success">Publish All → IndexNow + Sitemaps</button>
            </form>
            <p class="small text-muted mt-2 mb-0"><a href="../export-urls.php?type=all">Download all URLs for Google Search Console</a></p>
            <form method="POST" class="d-grid gap-2 mt-2">
                <input type="hidden" name="action" value="refresh_seo">
                <button type="submit" class="btn btn-outline-info">Refresh High-Intent SEO Keywords (All Pages)</button>
            </form>
            <hr>
            <p class="small text-muted mb-0">Google Search Console: submit <code><?php echo SITE_URL; ?>/sitemap.xml</code></p>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="ge-card mb-4">
            <h2 class="h6 mb-3">Pending Pages</h2>
            <div class="table-responsive"><table class="table ge-table table-sm"><thead><tr><th>URL</th><th>Action</th></tr></thead><tbody>
            <?php foreach (($recentPages['data'] ?? []) as $p): ?>
            <tr><td><code><?php echo htmlspecialchars($p['slug'] ?? ''); ?></code></td><td>
                <form method="POST" class="d-inline"><input type="hidden" name="action" value="mark_indexed"><input type="hidden" name="page_id" value="<?php echo (int)($p['id'] ?? 0); ?>"><button type="submit" class="btn btn-sm btn-outline-success">Mark Indexed</button></form>
            </td></tr>
            <?php endforeach; ?>
            <?php if (empty($recentPages['data'])): ?>
            <tr><td colspan="2" class="text-muted text-center small">No pending pages or Growth DB not ready.</td></tr>
            <?php endif; ?>
            </tbody></table></div>
        </div>
        <div class="ge-card">
            <h2 class="h6 mb-3">Index Queue Log</h2>
            <div class="table-responsive"><table class="table ge-table table-sm"><thead><tr><th>URL</th><th>Status</th><th>Date</th></tr></thead><tbody>
            <?php foreach ($queue as $q):
                $pathLabel = trim((string)($q['url'] ?? ''));
                if ($pathLabel !== '') {
                    $parsed = parse_url($pathLabel, PHP_URL_PATH);
                    if ($parsed !== null && $parsed !== false && $parsed !== '') {
                        $pathLabel = $parsed;
                    }
                } else {
                    $pathLabel = '—';
                }
            ?>
            <tr><td class="small"><code><?php echo htmlspecialchars($pathLabel); ?></code></td><td><?php echo htmlspecialchars((string)($q['status'] ?? '')); ?></td><td class="small text-muted"><?php echo htmlspecialchars((string)($q['created_at'] ?? '')); ?></td></tr>
            <?php endforeach; ?>
            <?php if (empty($queue)): ?>
            <tr><td colspan="3" class="text-muted text-center small">No queue items yet.</td></tr>
            <?php endif; ?>
            </tbody></table></div>
        </div>
    </div>
</div>

<?php ge_admin_layout_end(); ?>
