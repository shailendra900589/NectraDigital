<?php
require_once __DIR__ . '/init.php';

$events = [];
if (ge_table_exists('ge_analytics_events')) {
    $db = ge_conn();
    $res = $db->query("SELECT event_type, COUNT(*) AS cnt FROM ge_analytics_events GROUP BY event_type ORDER BY cnt DESC LIMIT 20");
    while ($row = $res->fetch_assoc()) $events[] = $row;
    $recent = $db->query("SELECT * FROM ge_analytics_events ORDER BY id DESC LIMIT 50");
    $recentRows = [];
    while ($row = $recent->fetch_assoc()) $recentRows[] = $row;
} else {
    $recentRows = [];
}

ge_admin_layout();
ge_admin_layout_start('Analytics', 'analytics');
?>

<?php if (!ge_table_exists('ge_analytics_events')): ?>
<div class="alert alert-warning">Run migration to enable analytics events tracking.</div>
<?php else: ?>
<div class="row g-4 mb-4">
<?php foreach ($events as $e): ?>
<div class="col-md-3"><div class="ge-stat-card"><div class="ge-stat-value"><?php echo number_format($e['cnt']); ?></div><div class="ge-stat-label"><?php echo htmlspecialchars($e['event_type']); ?></div></div></div>
<?php endforeach; ?>
</div>
<div class="ge-card"><h2 class="h6 mb-3">Recent Events</h2><table class="table ge-table table-sm"><thead><tr><th>Type</th><th>URL</th><th>Date</th></tr></thead><tbody>
<?php foreach ($recentRows as $r): ?>
<tr><td><?php echo htmlspecialchars($r['event_type']); ?></td><td class="small"><?php echo htmlspecialchars(mb_substr($r['page_url'] ?? '', 0, 60)); ?></td><td class="small text-muted"><?php echo $r['created_at']; ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif;

ge_admin_layout_end();
