<?php
require_once __DIR__ . '/init.php';

use Growth\Models\Keyword;
use Growth\Models\Service;
use Growth\Models\City;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Keyword::create([
        'keyword' => trim($_POST['keyword'] ?? ''),
        'keyword_type' => $_POST['keyword_type'] ?? 'primary',
        'service_id' => !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null,
        'city_id' => !empty($_POST['city_id']) ? (int)$_POST['city_id'] : null,
        'is_auto_generated' => 0,
    ]);
    ge_admin_flash('success', 'Keyword added.');
    header('Location: keywords.php');
    exit;
}

if (isset($_GET['delete'])) {
    Keyword::delete((int)$_GET['delete']);
    ge_admin_flash('success', 'Keyword deleted.');
    header('Location: keywords.php');
    exit;
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 50;
$total = ge_is_ready() ? Keyword::count() : 0;
$pg = ge_paginate($total, $page, $perPage);
$keywords = ge_is_ready() ? Keyword::all($perPage, $pg['offset']) : [];
$services = ge_is_ready() ? Service::all(true) : [];
$cities = ge_is_ready() ? City::all(true) : [];

ge_admin_layout();
ge_admin_layout_start('Keyword Manager', 'keywords');
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="ge-card">
            <h2 class="h6 mb-3">Add Keyword</h2>
            <form method="POST">
                <div class="mb-3"><label class="form-label">Keyword *</label><input type="text" name="keyword" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Type</label>
                    <select name="keyword_type" class="form-select">
                        <?php foreach (['primary','secondary','lsi','commercial','transactional','informational','local'] as $t): ?>
                        <option value="<?php echo $t; ?>"><?php echo ucfirst($t); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Service (optional)</label><select name="service_id" class="form-select"><option value="">— Any —</option><?php foreach ($services as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option><?php endforeach; ?></select></div>
                <div class="mb-3"><label class="form-label">City (optional)</label><select name="city_id" class="form-select"><option value="">— Any —</option><?php foreach ($cities as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>
                <button type="submit" class="btn btn-ge-primary w-100">Add Keyword</button>
            </form>
            <p class="text-muted small mt-3 mb-0">Keywords are also auto-generated when landing pages are created.</p>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="ge-card">
            <div class="d-flex justify-content-between mb-3"><span class="text-muted"><?php echo number_format($total); ?> keywords</span></div>
            <div class="table-responsive"><table class="table ge-table table-sm"><thead><tr><th>Keyword</th><th>Type</th><th>Service</th><th>City</th><th></th></tr></thead><tbody>
            <?php foreach ($keywords as $k): ?>
            <tr><td><?php echo htmlspecialchars($k['keyword']); ?><?php if ($k['is_auto_generated']): ?><span class="ge-badge ge-badge-pending ms-1">auto</span><?php endif; ?></td><td><?php echo $k['keyword_type']; ?></td><td><?php echo htmlspecialchars($k['service_name'] ?? '—'); ?></td><td><?php echo htmlspecialchars($k['city_name'] ?? '—'); ?></td><td><a href="?delete=<?php echo $k['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a></td></tr>
            <?php endforeach; ?>
            </tbody></table></div>
            <?php if ($pg['pages'] > 1): ?><nav class="mt-3"><ul class="pagination pagination-sm"><?php for ($i=1;$i<=$pg['pages'];$i++): ?><li class="page-item <?php echo $i===$pg['page']?'active':''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li><?php endfor; ?></ul></nav><?php endif; ?>
        </div>
    </div>
</div>

<?php ge_admin_layout_end(); ?>
