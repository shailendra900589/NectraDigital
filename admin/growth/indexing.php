<?php
require_once 'includes/auth.php';
require_once __DIR__ . '/../../includes/growth/bootstrap.php';

use Growth\Models\LandingPage;
use Growth\Models\IndexingQueue;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'mark_indexed' && !empty($_POST['page_id'])) {
        $db = ge_conn();
        $id = (int)$_POST['page_id'];
        $db->query("UPDATE ge_landing_pages SET index_status='indexed', is_indexed=1, index_verified_at=NOW() WHERE id=$id");
        ge_admin_flash('success', 'Marked as indexed.');
    }

    if ($action === 'queue_all_pending') {
        $db = ge_conn();
        $res = $db->query("SELECT id, slug FROM ge_landing_pages WHERE status='published' AND index_status IN ('pending','failed') LIMIT 500");
        $count = 0;
        while ($row = $res->fetch_assoc()) {
            IndexingQueue::enqueue(SITE_URL . '/' . $row['slug'], (int)$row['id']);
            $db->query("UPDATE ge_landing_pages SET index_status='submitted', index_submitted_at=NOW() WHERE id=" . (int)$row['id']);
            $count++;
        }
        ge_admin_flash('success', "Queued {$count} pages for indexing.");
    }

    if ($action === 'process_queue') {
        $pending = IndexingQueue::pending(50);
        foreach ($pending as $item) {
            IndexingQueue::markProcessed((int)$item['id'], 'completed', 'Manual submission recorded. Submit URLs via Google Search Console.');
        }
        ge_admin_flash('success', 'Processed ' . count($pending) . ' queue items.');
    }

    header('Location: indexing.php');
    exit;
}

$stats = ge_is_ready() ? LandingPage::indexStats() : ['total'=>0,'indexed'=>0,'pending'=>0,'submitted'=>0,'failed'=>0];
$queue = ge_is_ready() ? IndexingQueue::all(30) : [];
$recentPages = ge_is_ready() ? LandingPage::paginated(1, 20, ['index_status' => 'pending']) : ['data'=>[]];

require_once 'includes/layout.php';
ge_admin_layout_start('Indexing Manager', 'indexing');
?>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value text-success"><?php echo number_format($stats['indexed'] ?? 0); ?></div><div class="ge-stat-label">Indexed</div></div></div>
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value text-warning"><?php echo number_format($stats['pending'] ?? 0); ?></div><div class="ge-stat-label">Pending</div></div></div>
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value"><?php echo number_format($stats['submitted'] ?? 0); ?></div><div class="ge-stat-label">Submitted</div></div></div>
    <div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value text-danger"><?php echo number_format($stats['failed'] ?? 0); ?></div><div class="ge-stat-label">Failed</div></div></div>
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
                <button type="submit" class="btn btn-outline-secondary">Process Index Queue</button>
            </form>
            <hr>
            <p class="small text-muted mb-0">Submit sitemap in <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a>:<br><code><?php echo SITE_URL; ?>/sitemap.xml</code></p>
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
