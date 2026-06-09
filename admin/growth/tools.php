<?php
require_once __DIR__ . '/init.php';

use Growth\Models\Tool;

$tools = ge_table_exists('ge_tools') ? Tool::all(false) : [];

require_once 'includes/layout.php';
ge_admin_layout_start('Tools Manager', 'tools');
?>

<?php if (!ge_table_exists('ge_tools')): ?>
<div class="alert alert-warning">Run migration to enable Tools marketplace.</div>
<?php else: ?>
<div class="ge-card mb-4">
    <p class="text-muted small mb-0">Tools are managed dynamically. Public URLs: <code>/tools/{slug}</code>. Edit status or add custom tools via database — full CRUD UI coming in next sprint.</p>
</div>
<div class="ge-card"><table class="table ge-table"><thead><tr><th>Tool</th><th>Type</th><th>Usage</th><th>Status</th><th>Public URL</th></tr></thead><tbody>
<?php foreach ($tools as $t): ?>
<tr>
    <td><strong><?php echo htmlspecialchars($t['name']); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars(mb_substr($t['description'] ?? '', 0, 80)); ?></small></td>
    <td><code><?php echo htmlspecialchars($t['tool_type']); ?></code></td>
    <td><?php echo number_format($t['usage_count']); ?></td>
    <td><?php echo $t['status']; ?></td>
    <td><a href="<?php echo SITE_URL; ?>/tools/<?php echo htmlspecialchars($t['slug']); ?>" target="_blank">/tools/<?php echo htmlspecialchars($t['slug']); ?></a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif;

ge_admin_layout_end();
