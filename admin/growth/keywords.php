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
    header('Location: keywords.php?' . http_build_query(array_filter([
        'service_id' => $_GET['service_id'] ?? '',
        'city_id' => $_GET['city_id'] ?? '',
        'keyword_type' => $_GET['keyword_type'] ?? '',
        'q' => $_GET['q'] ?? '',
        'auto' => $_GET['auto'] ?? '',
        'page' => $_GET['page'] ?? '',
    ])));
    exit;
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 40;
$filters = [];
if (!empty($_GET['service_id'])) {
    $filters['service_id'] = (int)$_GET['service_id'];
}
if (!empty($_GET['city_id'])) {
    $filters['city_id'] = (int)$_GET['city_id'];
}
if (!empty($_GET['keyword_type'])) {
    $filters['keyword_type'] = trim($_GET['keyword_type']);
}
if (!empty($_GET['q'])) {
    $filters['q'] = trim($_GET['q']);
}
if (isset($_GET['auto']) && $_GET['auto'] !== '') {
    $filters['auto'] = (int)$_GET['auto'];
}

$result = ge_is_ready() ? Keyword::paginated($page, $perPage, $filters) : ['data' => [], 'pagination' => ge_paginate(0, 1, $perPage)];
$keywords = $result['data'];
$pg = $result['pagination'];
$total = (int)($pg['total'] ?? 0);
$services = ge_is_ready() ? Service::all(true) : [];
$cities = ge_is_ready() ? City::all(true) : [];
$keywordTypes = ['primary', 'secondary', 'lsi', 'commercial', 'transactional', 'informational', 'local'];

ge_admin_layout();
ge_admin_layout_start('Keyword Manager', 'keywords');
?>

<div class="row g-4">
    <div class="col-lg-4 col-xl-3">
        <div class="ge-card">
            <h2 class="h6 mb-3"><i class="fas fa-plus-circle text-info me-1"></i> Add Keyword</h2>
            <form method="POST">
                <div class="mb-3"><label class="form-label">Keyword *</label><input type="text" name="keyword" class="form-control form-control-sm" required placeholder="e.g. SEO company Lucknow"></div>
                <div class="mb-3"><label class="form-label">Type</label>
                    <select name="keyword_type" class="form-select form-select-sm">
                        <?php foreach ($keywordTypes as $t): ?>
                        <option value="<?php echo $t; ?>"><?php echo ucfirst($t); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Service</label><select name="service_id" class="form-select form-select-sm"><option value="">— Any —</option><?php foreach ($services as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option><?php endforeach; ?></select></div>
                <div class="mb-3"><label class="form-label">City</label><select name="city_id" class="form-select form-select-sm"><option value="">— Any —</option><?php foreach ($cities as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>
                <button type="submit" class="btn btn-ge-primary w-100 btn-sm">Add Keyword</button>
            </form>
            <p class="text-muted small mt-3 mb-0">Keywords auto-generate when landing pages are created (marked <span class="ge-badge ge-badge-pending">auto</span>).</p>
        </div>
    </div>

    <div class="col-lg-8 col-xl-9">
        <div class="ge-card mb-3 ge-filter-bar">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3 col-sm-6"><label class="form-label">Search</label><input type="text" name="q" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filters['q'] ?? ''); ?>" placeholder="Keyword contains…"></div>
                <div class="col-md-2 col-sm-6"><label class="form-label">Service</label><select name="service_id" class="form-select form-select-sm"><option value="">All</option><?php foreach ($services as $s): ?><option value="<?php echo $s['id']; ?>" <?php echo ($filters['service_id'] ?? 0) == $s['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['name']); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-2 col-sm-6"><label class="form-label">City</label><select name="city_id" class="form-select form-select-sm"><option value="">All</option><?php foreach ($cities as $c): ?><option value="<?php echo $c['id']; ?>" <?php echo ($filters['city_id'] ?? 0) == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-2 col-sm-6"><label class="form-label">Type</label><select name="keyword_type" class="form-select form-select-sm"><option value="">All</option><?php foreach ($keywordTypes as $t): ?><option value="<?php echo $t; ?>" <?php echo ($filters['keyword_type'] ?? '') === $t ? 'selected' : ''; ?>><?php echo ucfirst($t); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-2 col-sm-6"><label class="form-label">Source</label><select name="auto" class="form-select form-select-sm"><option value="">All</option><option value="1" <?php echo isset($filters['auto']) && (int)$filters['auto'] === 1 ? 'selected' : ''; ?>>Auto only</option><option value="0" <?php echo isset($filters['auto']) && (int)$filters['auto'] === 0 ? 'selected' : ''; ?>>Manual only</option></select></div>
                <div class="col-md-1 col-sm-6 d-grid"><button type="submit" class="btn btn-ge-primary btn-sm">Filter</button></div>
            </form>
        </div>

        <div class="ge-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="h6 mb-0"><i class="fas fa-key text-info me-1"></i> Keywords</h2>
                <span class="text-muted small"><?php echo number_format($total); ?> total<?php if (!empty($filters)): ?> · filtered<?php endif; ?></span>
            </div>
            <div class="table-responsive">
                <table class="table ge-table table-sm table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            <th>Type</th>
                            <th>Service</th>
                            <th>City</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($keywords as $k): ?>
                        <tr>
                            <td class="ge-keyword-cell">
                                <?php echo htmlspecialchars($k['keyword']); ?>
                                <?php if (!empty($k['is_auto_generated'])): ?><span class="ge-badge ge-badge-pending ms-1">auto</span><?php endif; ?>
                            </td>
                            <td><span class="badge bg-dark border border-secondary ge-type-badge text-white-50"><?php echo htmlspecialchars($k['keyword_type']); ?></span></td>
                            <td class="ge-cell-muted small"><?php echo htmlspecialchars($k['service_name'] ?? '—'); ?></td>
                            <td class="ge-cell-muted small"><?php echo htmlspecialchars($k['city_name'] ?? '—'); ?></td>
                            <td class="text-end">
                                <?php
                                $delQuery = array_merge($filters, ['page' => $page, 'delete' => $k['id']]);
                                ?>
                                <a href="?<?php echo htmlspecialchars(http_build_query($delQuery)); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this keyword?')" title="Delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($keywords)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No keywords found. <?php echo empty($filters) ? 'Add one or generate landing pages.' : 'Try clearing filters.'; ?></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php ge_admin_pagination($pg, $filters); ?>
        </div>
    </div>
</div>

<?php ge_admin_layout_end(); ?>
