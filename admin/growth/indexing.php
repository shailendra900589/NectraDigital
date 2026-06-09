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
        ge_admin_flash('success', "Submitted {$r['processed']} URLs via IndexNow. Failed: {$r['failed']}. ({$r['batches']} batches)");
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

    header('Location: indexing.php');
    exit;
}

$stats = ge_is_ready() ? LandingPage::indexStats() : ['total'=>0,'indexed'=>0,'pending'=>0,'submitted'=>0,'failed'=>0];
$queue = ge_is_ready() ? IndexingQueue::all(30) : [];
$recentPages = ge_is_ready() ? LandingPage::paginated(1, 20, ['index_status' => 'pending']) : ['data'=>[]];
$idxInfo = ['key' => IndexingEngine::apiKey(), 'key_url' => IndexingEngine::keyFileUrl()];

ge_admin_layout();
ge_admin_layout_start('Indexing Manager', 'indexing');
?>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value text-success"><?php echo number_format($stats['indexed'] ?? 0); ?></div><div class="ge-stat-label">Indexed</div></div></div>
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value text-warning"><?php echo number_format($stats['pending'] ?? 0); ?></div><div class="ge-stat-label">Pending</div></div></div>
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value"><?php echo number_format($stats['submitted'] ?? 0); ?></div><div class="ge-stat-label">Submitted</div></div></div>
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value text-danger"><?php echo number_format($stats['failed'] ?? 0); ?></div><div class="ge-stat-label">Failed</div></div></div>
</div>

<div class="alert alert-info small">
    <strong>Auto engines:</strong> IndexNow · Bing · Yandex · DuckDuckGo · Google/Bing sitemap ping · RSS · Discover feed · News sitemap · Atom feed
    <br>Feeds: <a href="<?php echo SITE_URL; ?>/rss.xml" target="_blank">RSS</a> ·
    <a href="<?php echo SITE_URL; ?>/discover-feed.xml" target="_blank">Discover</a> ·
    <a href="<?php echo SITE_URL; ?>/news-sitemap.xml" target="_blank">News</a> ·
    <a href="<?php echo SITE_URL; ?>/atom.xml" target="_blank">Atom</a>
    <br>IndexNow key: <a href="<?php echo htmlspecialchars($idxInfo['key_url']); ?>" target="_blank"><?php echo htmlspecialchars($idxInfo['key_url']); ?></a>
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
                <button type="submit" class="btn btn-success">Process Queue → IndexNow + Bing + Yandex</button>
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
            <p class="small text-muted mb-1">Cron jobs (Hostinger):</p>
            <code class="small d-block mb-1">php cron/process-indexing.php</code>
            <code class="small d-block mb-2">php cron/publish-discovery.php</code>
            <p class="small text-muted mb-0">Also submit in <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a>:<br><code><?php echo SITE_URL; ?>/sitemap.xml</code></p>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="ge-card mb-4">
            <h2 class="h6 mb-3">Pending Pages</h2>
            <div class="table-responsive"><table class="table ge-table table-sm"><thead><tr><th>URL</th><th>Action</th></tr></thead><tbody>
            <?php foreach ($recentPages['data'] as $p): ?>
            <tr><td><code><?php echo htmlspecialchars($p['slug']); ?></code></td><td>
                <form method="POST" class="d-inline"><input type="hidden" name="action" value="mark_indexed"><input type="hidden" name="page_id" value="<?php echo $p['id']; ?>"><button type="submit" class="btn btn-sm btn-outline-success">Mark Indexed</button></form>
            </td></tr>
            <?php endforeach; ?>
            </tbody></table></div>
        </div>
        <div class="ge-card">
            <h2 class="h6 mb-3">Index Queue Log</h2>
            <div class="table-responsive"><table class="table ge-table table-sm"><thead><tr><th>URL</th><th>Status</th><th>Date</th></tr></thead><tbody>
            <?php foreach ($queue as $q): ?>
            <tr><td class="small"><code><?php echo htmlspecialchars(parse_url($q['url'], PHP_URL_PATH) ?? $q['url']); ?></code></td><td><?php echo $q['status']; ?></td><td class="small text-muted"><?php echo $q['created_at']; ?></td></tr>
            <?php endforeach; ?>
            </tbody></table></div>
        </div>
    </div>
</div>

<?php ge_admin_layout_end(); ?>
