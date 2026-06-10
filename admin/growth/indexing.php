<?php
/**
 * Indexing Manager — lightweight GET (no gateway timeout), batched POST.
 */
require_once __DIR__ . '/init.php';

use Growth\Models\LandingPage;
use Growth\Models\IndexingQueue;
use Growth\Engines\IndexingEngine;
use Growth\Engines\DiscoveryEngine;
use Growth\Engines\SeoRefreshEngine;

/** Heavy work belongs in cron — web UI runs one safe batch per click. */
function ge_indexing_invalidate_stats_cache(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        unset($_SESSION['ge_landing_index_stats'], $_SESSION['ge_landing_index_stats_at']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    @set_time_limit(120);
    $action = $_POST['action'] ?? '';

    if ($action === 'mark_indexed' && !empty($_POST['page_id'])) {
        $db = ge_conn();
        $id = (int)$_POST['page_id'];
        $db->query("UPDATE ge_landing_pages SET index_status='indexed', is_indexed=1, index_verified_at=NOW() WHERE id=$id");
        ge_admin_flash('success', 'Marked as indexed.');
        ge_indexing_invalidate_stats_cache();
    }

    if ($action === 'queue_all_pending') {
        $r = IndexingEngine::queueAllPending(2000, false);
        ge_admin_flash('success', "Queued {$r['queued']} URLs. Use Process One Batch or set up cron to submit.");
        ge_indexing_invalidate_stats_cache();
    }

    if ($action === 'process_queue') {
        $r = IndexingEngine::processWebBatch();
        $n = (int)($r['processed'] ?? 0);
        ge_admin_flash('success', "Submitted {$n} URLs (one batch). Run cron or click again for more. Failed: " . (int)($r['failed'] ?? 0));
        ge_indexing_invalidate_stats_cache();
    }

    if ($action === 'ping_sitemap') {
        $r = IndexingEngine::pingSitemap();
        ge_admin_flash($r['ok'] ? 'success' : 'error', $r['ok'] ? 'Sitemap ping sent.' : 'Sitemap ping failed.');
    }

    if ($action === 'submit_all_indexnow') {
        $r = IndexingEngine::submitAllPublishedUrls(true, false);
        $n = (int)($r['urls_submitted'] ?? 0);
        $total = (int)($r['urls_total'] ?? 0);
        ge_admin_flash($r['ok'] ? 'success' : 'warning', $r['ok']
            ? "IndexNow: first {$n} URL batches submitted (of ~{$total} with languages). Run cron for full queue."
            : 'IndexNow batch failed — check IndexNow key in Settings.');
        ge_indexing_invalidate_stats_cache();
    }

    if ($action === 'queue_and_process') {
        $q = IndexingEngine::queueAllPending(2000, false);
        $r = IndexingEngine::processWebBatch();
        ge_admin_flash('success', "Queued {$q['queued']} URLs + submitted " . (int)($r['processed'] ?? 0) . " in one batch. Cron handles the rest.");
        ge_indexing_invalidate_stats_cache();
    }

    if ($action === 'publish_all') {
        $r = DiscoveryEngine::publishAll(500, 2);
        ge_admin_flash('success', "Discovery push started: {$r['processed']} URLs in cron-sized batches. Queued: {$r['queued']}.");
        ge_indexing_invalidate_stats_cache();
    }

    if ($action === 'refresh_seo') {
        @set_time_limit(300);
        $r = SeoRefreshEngine::refreshAll(true);
        ge_admin_flash('success', "Refreshed SEO on {$r['updated']} pages.");
        ge_indexing_invalidate_stats_cache();
    }

    if ($action === 'bing_submit_sitemap') {
        $urls = [rtrim(SITE_URL, '/') . '/sitemap.xml', rtrim(SITE_URL, '/') . '/news-sitemap.xml'];
        $r = IndexingEngine::submitBingWebmasterUrls($urls, false);
        ge_admin_flash(
            !empty($r['ok']) ? 'success' : 'error',
            !empty($r['ok']) ? 'Bing Webmaster API: sitemap URLs submitted.' : ('Bing API failed — ' . ($r['message'] ?? 'check API key'))
        );
    }

    if ($action === 'bing_test') {
        $siteUrl = rtrim(trim((string)ge_setting('bing_webmaster_site_url', SITE_URL)), '/');
        $r = IndexingEngine::submitBingWebmasterUrls([$siteUrl . '/'], false);
        ge_admin_flash(
            !empty($r['ok']) ? 'success' : 'error',
            !empty($r['ok']) ? 'Bing Webmaster API connected — homepage submitted.' : ('Bing test failed — ' . ($r['message'] ?? 'verify key + site URL'))
        );
    }

    if ($action === 'refresh_stats') {
        ge_indexing_invalidate_stats_cache();
        ge_admin_flash('success', 'Stats refreshed.');
    }

    header('Location: indexing.php');
    exit;
}

// --- Fast GET load (must finish before nginx 504) ---
$stats = ['total' => 0, 'indexed' => 0, 'pending' => 0, 'submitted' => 0, 'failed' => 0];
$queue = [];
$pendingPages = [];
$queuePending = 0;
$idxInfo = ['key' => '', 'key_url' => '', 'host' => parse_url(SITE_URL, PHP_URL_HOST) ?: ''];
$loadError = null;

try {
    if (ge_is_ready()) {
        $stats = LandingPage::indexStatsFast(true);
        $pendingPages = LandingPage::pendingSample(15);
    }
    if (ge_table_exists('ge_indexing_queue')) {
        $queue = IndexingQueue::all(20);
        $row = ge_conn()->query("SELECT COUNT(*) AS c FROM ge_indexing_queue WHERE status='pending'");
        if ($row) {
            $queuePending = (int)($row->fetch_assoc()['c'] ?? 0);
        }
    }

    $key = IndexingEngine::readApiKey();
    if ($key === '') {
        $key = IndexingEngine::apiKey();
    }
    $idxInfo = [
        'key' => $key,
        'key_url' => IndexingEngine::keyFileUrl($key),
        'host' => IndexingEngine::host(),
    ];
} catch (Throwable $e) {
    $loadError = $e->getMessage();
    error_log('growth/indexing load: ' . $e->getMessage());
}

$bingKey = trim((string)ge_setting('bing_webmaster_api_key', ''));
$bingSite = rtrim(trim((string)ge_setting('bing_webmaster_site_url', SITE_URL)), '/');
$bingApiOn = ge_setting('index_engine_bing_api', '1') === '1';
$cronToken = trim((string)ge_setting('cron_token', ''));
if ($cronToken === '' && function_exists('ge_ensure_cron_token')) {
    $cronToken = ge_ensure_cron_token();
}
$cronBase = rtrim(SITE_URL, '/');
$cronUrls = [
    'indexing' => $cronBase . '/cron/process-indexing.php' . ($cronToken ? '?token=' . urlencode($cronToken) : ''),
    'i18n_index' => $cronBase . '/cron/i18n-indexnow.php' . ($cronToken ? '?token=' . urlencode($cronToken) : ''),
    'discovery' => $cronBase . '/cron/publish-discovery.php' . ($cronToken ? '?token=' . urlencode($cronToken) : ''),
];

ge_admin_layout();
ge_admin_layout_start('Indexing Manager', 'indexing');
?>

<?php if ($loadError): ?>
<div class="alert alert-warning">Partial load: <?php echo htmlspecialchars($loadError); ?></div>
<?php endif; ?>

<div class="alert alert-secondary small mb-4">
    <strong>Hostinger tip:</strong> Bulk indexing runs via <strong>cron</strong> (below). Buttons here submit <strong>one safe batch</strong> so the page never times out (504).
    <?php if ($queuePending > 0): ?>
    <span class="text-warning ms-1"><?php echo number_format($queuePending); ?> URLs waiting in queue.</span>
    <?php endif; ?>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value text-success"><?php echo number_format((int)($stats['indexed'] ?? 0)); ?></div><div class="ge-stat-label">Indexed</div></div></div>
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value text-warning"><?php echo number_format((int)($stats['pending'] ?? 0)); ?></div><div class="ge-stat-label">Pending</div></div></div>
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value"><?php echo number_format((int)($stats['submitted'] ?? 0)); ?></div><div class="ge-stat-label">Submitted</div></div></div>
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value text-danger"><?php echo number_format((int)($stats['failed'] ?? 0)); ?></div><div class="ge-stat-label">Failed</div></div></div>
</div>
<form method="POST" class="mb-4"><input type="hidden" name="action" value="refresh_stats"><button type="submit" class="btn btn-sm btn-outline-secondary">Refresh stats</button></form>

<div class="alert alert-info small">
    <strong>Engines:</strong> IndexNow · Bing IndexNow · Bing Webmaster API · Yandex · DuckDuckGo
    <br>IndexNow key: <a href="<?php echo htmlspecialchars($idxInfo['key_url']); ?>" target="_blank"><?php echo htmlspecialchars($idxInfo['key_url']); ?></a>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="ge-card h-100">
            <h2 class="h6 mb-3"><i class="fab fa-microsoft me-1"></i> Bing Webmaster API</h2>
            <?php if ($bingKey !== '' && $bingApiOn): ?>
                <p class="small text-success mb-2"><i class="fas fa-check-circle"></i> API key saved · Site: <code><?php echo htmlspecialchars($bingSite); ?></code></p>
                <form method="POST" class="d-inline"><input type="hidden" name="action" value="bing_test"><button type="submit" class="btn btn-sm btn-outline-success me-2">Test Bing API</button></form>
                <form method="POST" class="d-inline"><input type="hidden" name="action" value="bing_submit_sitemap"><button type="submit" class="btn btn-sm btn-outline-info">Submit Sitemaps to Bing</button></form>
            <?php elseif ($bingKey !== '' && !$bingApiOn): ?>
                <p class="small text-warning mb-0">Key saved but disabled — <a href="settings.php">enable Bing URL API</a>.</p>
            <?php else: ?>
                <p class="small text-muted mb-2">Add Bing API key in <a href="settings.php">Growth Settings</a>.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="ge-card h-100">
            <h2 class="h6 mb-3"><i class="fas fa-clock me-1"></i> Cron (full auto indexing)</h2>
            <p class="small text-muted">Add in Hostinger → Cron Jobs (every 5–15 min):</p>
            <code class="small d-block mb-1 text-break"><?php echo htmlspecialchars($cronUrls['indexing']); ?></code>
            <code class="small d-block mb-1 text-break"><?php echo htmlspecialchars($cronUrls['i18n_index']); ?></code>
            <code class="small d-block text-break"><?php echo htmlspecialchars($cronUrls['discovery']); ?></code>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="ge-card">
            <h2 class="h6 mb-3">Indexing Actions</h2>
            <form method="POST" class="d-grid gap-2"><input type="hidden" name="action" value="queue_all_pending"><button type="submit" class="btn btn-ge-primary">Queue Pending URLs</button></form>
            <form method="POST" class="d-grid gap-2 mt-2"><input type="hidden" name="action" value="process_queue"><button type="submit" class="btn btn-success">Process One Batch → IndexNow + Bing</button></form>
            <form method="POST" class="d-grid gap-2 mt-2"><input type="hidden" name="action" value="ping_sitemap"><button type="submit" class="btn btn-outline-secondary">Ping Sitemap</button></form>
            <form method="POST" class="d-grid gap-2 mt-2"><input type="hidden" name="action" value="submit_all_indexnow"><button type="submit" class="btn btn-warning">Submit First Batches → IndexNow</button></form>
            <form method="POST" class="d-grid gap-2 mt-2"><input type="hidden" name="action" value="queue_and_process"><button type="submit" class="btn btn-outline-success">Queue + One Batch</button></form>
            <p class="small text-muted mt-3 mb-0"><a href="../export-urls.php?type=all">Download URLs for Google Search Console</a></p>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="ge-card mb-4">
            <h2 class="h6 mb-3">Pending Pages (sample)</h2>
            <div class="table-responsive"><table class="table ge-table table-sm"><thead><tr><th>Slug</th><th></th></tr></thead><tbody>
            <?php foreach ($pendingPages as $p): ?>
            <tr><td><code><?php echo htmlspecialchars($p['slug'] ?? ''); ?></code></td><td>
                <form method="POST" class="d-inline"><input type="hidden" name="action" value="mark_indexed"><input type="hidden" name="page_id" value="<?php echo (int)($p['id'] ?? 0); ?>"><button type="submit" class="btn btn-sm btn-outline-success">Mark Indexed</button></form>
            </td></tr>
            <?php endforeach; ?>
            <?php if (empty($pendingPages)): ?>
            <tr><td colspan="2" class="text-muted text-center small">No pending sample (or stats cached).</td></tr>
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
                    if (is_string($parsed) && $parsed !== '') {
                        $pathLabel = $parsed;
                    }
                } else {
                    $pathLabel = '—';
                }
            ?>
            <tr><td class="small"><code><?php echo htmlspecialchars($pathLabel); ?></code></td><td><?php echo htmlspecialchars((string)($q['status'] ?? '')); ?></td><td class="small text-muted"><?php echo htmlspecialchars((string)($q['created_at'] ?? '')); ?></td></tr>
            <?php endforeach; ?>
            <?php if (empty($queue)): ?>
            <tr><td colspan="3" class="text-muted text-center small">Queue empty.</td></tr>
            <?php endif; ?>
            </tbody></table></div>
        </div>
    </div>
</div>

<?php ge_admin_layout_end(); ?>
