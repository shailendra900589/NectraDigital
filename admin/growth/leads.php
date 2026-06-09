<?php
require_once 'includes/auth.php';
require_once __DIR__ . '/../../includes/growth/bootstrap.php';

use Growth\Models\CrmLead;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['lead_id']) && !empty($_POST['status'])) {
    CrmLead::updateStatus((int)$_POST['lead_id'], $_POST['status']);
    ge_admin_flash('success', 'Lead status updated.');
    header('Location: leads.php');
    exit;
}

$stats = ge_table_exists('ge_crm_leads') ? CrmLead::stats() : ['total' => 0, 'new' => 0, 'won' => 0];
$leads = ge_table_exists('ge_crm_leads') ? CrmLead::all(200) : [];

require_once 'includes/layout.php';
ge_admin_layout_start('Leads Manager', 'leads');
?>

<div class="row g-4 mb-4">
    <div class="col-4"><div class="ge-stat-card"><div class="ge-stat-value"><?php echo $stats['total']; ?></div><div class="ge-stat-label">Total Leads</div></div></div>
    <div class="col-4"><div class="ge-stat-card"><div class="ge-stat-value"><?php echo $stats['new']; ?></div><div class="ge-stat-label">New</div></div></div>
    <div class="col-4"><div class="ge-stat-card"><div class="ge-stat-value"><?php echo $stats['won']; ?></div><div class="ge-stat-label">Won</div></div></div>
</div>

<?php if (!ge_table_exists('ge_crm_leads')): ?>
<div class="alert alert-warning">Run migration to enable CRM leads.</div>
<?php else: ?>
<div class="ge-card"><div class="table-responsive"><table class="table ge-table table-sm"><thead><tr><th>Contact</th><th>Interest</th><th>Source</th><th>Status</th><th>Date</th><th>Action</th></tr></thead><tbody>
<?php foreach ($leads as $lead): ?>
<tr>
    <td><strong><?php echo htmlspecialchars($lead['name']); ?></strong><br><small><?php echo htmlspecialchars($lead['email']); ?></small><?php if ($lead['phone']): ?><br><small><?php echo htmlspecialchars($lead['phone']); ?></small><?php endif; ?></td>
    <td><?php echo htmlspecialchars($lead['service_interest'] ?? '-'); ?><?php if ($lead['city']): ?><br><small><?php echo htmlspecialchars($lead['city']); ?></small><?php endif; ?></td>
    <td><?php echo htmlspecialchars($lead['source']); ?></td>
    <td><span class="ge-badge"><?php echo htmlspecialchars($lead['status']); ?></span></td>
    <td class="small text-muted"><?php echo $lead['created_at']; ?></td>
    <td>
        <form method="POST" class="d-flex gap-1">
            <input type="hidden" name="lead_id" value="<?php echo $lead['id']; ?>">
            <select name="status" class="form-select form-select-sm" style="width:120px">
                <?php foreach (['new','contacted','qualified','proposal','won','lost'] as $st): ?>
                <option value="<?php echo $st; ?>" <?php echo $lead['status'] === $st ? 'selected' : ''; ?>><?php echo $st; ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-outline-secondary">Save</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?php endif;

ge_admin_layout_end();
