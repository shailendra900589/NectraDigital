<?php
require_once __DIR__ . '/init.php';

use Growth\Models\LandingPage;
use Growth\Models\Service;
use Growth\Models\City;

if (isset($_GET['delete'])) {
    LandingPage::delete((int)$_GET['delete']);
    ge_admin_flash('success', 'Landing page deleted.');
    header('Location: landing-pages.php');
    exit;
}

$page = max(1, (int)($_GET['page'] ?? 1));
$filters = [];
if (!empty($_GET['service_id'])) $filters['service_id'] = (int)$_GET['service_id'];
if (!empty($_GET['city_id'])) $filters['city_id'] = (int)$_GET['city_id'];
if (!empty($_GET['index_status'])) $filters['index_status'] = $_GET['index_status'];

$result = ge_is_ready() ? LandingPage::paginated($page, 25, $filters) : ['data' => [], 'pagination' => ge_paginate(0, 1)];
$services = ge_is_ready() ? Service::all(true) : [];
$cities = ge_is_ready() ? City::all(true) : [];

require_once 'includes/layout.php';
ge_admin_layout_start('Landing Pages', 'landing');
?>

<div class="ge-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label small">Service</label><select name="service_id" class="form-select form-select-sm"><option value="">All</option><?php foreach ($services as $s): ?><option value="<?php echo $s['id']; ?>" <?php echo ($filters['service_id']??0)==$s['id']?'selected':''; ?>><?php echo htmlspecialchars($s['name']); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label small">City</label><select name="city_id" class="form-select form-select-sm"><option value="">All</option><?php foreach ($cities as $c): ?><option value="<?php echo $c['id']; ?>" <?php echo ($filters['city_id']??0)==$c['id']?'selected':''; ?>><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label small">Index Status</label><select name="index_status" class="form-select form-select-sm"><option value="">All</option><?php foreach (['pending','submitted','indexed','failed'] as $st): ?><option value="<?php echo $st; ?>" <?php echo ($filters['index_status']??'')===$st?'selected':''; ?>><?php echo ucfirst($st); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><button type="submit" class="btn btn-sm btn-ge-primary">Filter</button> <a href="generate.php" class="btn btn-sm btn-outline-secondary">Generate New</a></div>
    </form>
</div>

<div class="ge-card">
    <div class="table-responsive"><table class="table ge-table table-sm"><thead><tr><th>URL</th><th>Title</th><th>Index</th><th>Generated</th><th></th></tr></thead><tbody>
    <?php foreach ($result['data'] as $lp): ?>
    <tr>
        <td><a href="<?php echo SITE_URL . '/' . $lp['slug']; ?>" target="_blank" class="text-info"><code><?php echo htmlspecialchars($lp['slug']); ?></code></a></td>
        <td class="small"><?php echo htmlspecialchars(mb_substr($lp['meta_title'] ?? '', 0, 60)); ?></td>
        <td><span class="ge-badge ge-badge-<?php echo $lp['index_status']==='indexed'?'indexed':'pending'; ?>"><?php echo $lp['index_status']; ?></span></td>
        <td class="small text-muted"><?php echo date('M j, Y', strtotime($lp['generated_at'])); ?></td>
        <td><a href="?delete=<?php echo $lp['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete page?')"><i class="fas fa-trash"></i></a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($result['data'])): ?><tr><td colspan="5" class="text-center text-muted py-4">No landing pages yet. <a href="generate.php">Generate pages</a></td></tr><?php endif; ?>
    </tbody></table></div>
    <?php $pg = $result['pagination']; if ($pg['pages'] > 1): ?><nav class="mt-3"><ul class="pagination pagination-sm justify-content-center"><?php for ($i=1;$i<=$pg['pages'];$i++): ?><li class="page-item <?php echo $i===$pg['page']?'active':''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($filters); ?>"><?php echo $i; ?></a></li><?php endfor; ?></ul></nav><?php endif; ?>
</div>

<?php ge_admin_layout_end(); ?>
