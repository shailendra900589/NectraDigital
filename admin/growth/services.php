<?php
require_once __DIR__ . '/init.php';

use Growth\Models\Service;
use Growth\Models\City;
use Growth\Models\LandingPage;
use Growth\Engines\CatalogSyncEngine;
use Growth\LandingPageGenerator;

require_once __DIR__ . '/../../includes/seo-data.php';

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pageError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ge_admin_require_ready()) {
        ge_admin_flash('error', 'Database not migrated. Run database/migrate.php first.');
        header('Location: services.php');
        exit;
    }

    if (($action === 'sync_catalog') || !empty($_POST['sync_catalog'])) {
        set_time_limit(600);
        $sync = CatalogSyncEngine::syncAll(true);
        $created = (int)($sync['pages_created'] ?? 0);
        $msg = "Synced {$sync['services']} services and {$sync['cities']} cities from website catalog.";
        if ($created > 0) {
            $msg .= " Auto-generated {$created} new landing pages.";
        }
        ge_admin_flash('success', $msg);
        header('Location: services.php');
        exit;
    }

    if ($action === 'generate_all_pages') {
        set_time_limit(600);
        CatalogSyncEngine::syncAll();
        $result = LandingPageGenerator::generateFullMatrix(false, !empty($_POST['regenerate']));
        ge_admin_flash('success', "Generated {$result['processed']} landing pages" . ($result['failed'] ? " ({$result['failed']} failed)" : '') . '.');
        header('Location: landing-pages.php');
        exit;
    }

    if ($action === 'generate_cities' && $id) {
        set_time_limit(300);
        CatalogSyncEngine::syncAll();
        $cityIds = array_column(City::all(true), 'id');
        $result = LandingPageGenerator::generateBulk([$id], $cityIds, [0], !empty($_POST['regenerate']));
        ge_admin_flash('success', "Generated {$result['processed']} city pages for this service.");
        header('Location: landing-pages.php?service_id=' . $id);
        exit;
    }

    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => ge_slugify($_POST['slug'] ?? $_POST['name'] ?? ''),
        'url_prefix' => ge_slugify($_POST['url_prefix'] ?? $_POST['name'] ?? ''),
        'meta_title_template' => trim($_POST['meta_title_template'] ?? ''),
        'meta_description_template' => trim($_POST['meta_description_template'] ?? ''),
        'h1_template' => trim($_POST['h1_template'] ?? ''),
        'h2_template' => trim($_POST['h2_template'] ?? ''),
        'content_template' => $_POST['content_template'] ?? '',
        'keywords_template' => trim($_POST['keywords_template'] ?? ''),
        'schema_type' => $_POST['schema_type'] ?? 'Service',
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'status' => $_POST['status'] ?? 'active',
    ];

    if (!empty($_FILES['service_image']['name'])) {
        $up = ge_upload_image($_FILES['service_image'], 'svc_');
        if (!empty($up['success'])) $data['service_image'] = $up['success'];
    }

    $faqItems = [];
    if (!empty($_POST['faq_q']) && is_array($_POST['faq_q'])) {
        foreach ($_POST['faq_q'] as $i => $q) {
            $a = $_POST['faq_a'][$i] ?? '';
            if (trim($q) && trim($a)) $faqItems[] = ['q' => trim($q), 'a' => trim($a)];
        }
    }
    $data['faq_template'] = ge_json_encode($faqItems);

    if ($action === 'edit' && $id) {
        $existing = Service::find($id);
        if ($existing && empty($data['service_image'])) $data['service_image'] = $existing['service_image'];
        Service::update($id, $data);
        ge_admin_flash('success', 'Service updated.');
    } else {
        $newId = Service::create($data);
        if (($data['status'] ?? 'active') === 'active') {
            set_time_limit(600);
            $gen = LandingPageGenerator::generateForService($newId, false);
            $created = max(0, ($gen['processed'] ?? 0) - ($gen['skipped'] ?? 0));
            ge_admin_flash('success', "Service created. Auto-generated {$created} complete city landing pages.");
        } else {
            ge_admin_flash('success', 'Service created.');
        }
    }
    header('Location: services.php');
    exit;
}

if ($action === 'delete' && $id) {
    if (ge_admin_require_ready()) {
        Service::delete($id);
        ge_admin_flash('success', 'Service deleted.');
    } else {
        ge_admin_flash('error', 'Database not migrated.');
    }
    header('Location: services.php');
    exit;
}

$item = ($id && ge_admin_require_ready()) ? Service::find($id) : null;
$services = ge_admin_require_ready() ? ge_admin_safe(fn() => Service::withPageCounts(), []) : [];
$cityTotal = ge_admin_require_ready() ? (int)City::count(true) : count(get_cities_data());
$coverage = ge_admin_require_ready() ? ge_admin_safe(fn() => LandingPage::coverageSummary(), []) : [];

if (ge_admin_require_ready() && Service::count() === 0) {
    ge_admin_safe(fn() => CatalogSyncEngine::syncAll());
    $services = Service::withPageCounts();
    $coverage = LandingPage::coverageSummary();
}

ge_admin_layout();
ge_admin_layout_start($action === 'add' || $action === 'edit' ? 'Service Manager' : 'Services', 'services');

if ($action === 'add' || ($action === 'edit' && $item)):
    $faqs = ge_json_decode($item['faq_template'] ?? '[]');
?>
<div class="ge-card">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Service Name *</label><input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($item['name'] ?? ''); ?>"></div>
            <div class="col-md-3"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($item['slug'] ?? ''); ?>" placeholder="auto-generated"></div>
            <div class="col-md-3"><label class="form-label">URL Prefix *</label><input type="text" name="url_prefix" class="form-control" required value="<?php echo htmlspecialchars($item['url_prefix'] ?? ''); ?>" placeholder="seo"><small class="text-muted">Used in: seo-company-lucknow</small></div>
            <div class="col-md-8"><label class="form-label">Meta Title Template</label><input type="text" name="meta_title_template" class="form-control" value="<?php echo htmlspecialchars($item['meta_title_template'] ?? 'Best {service_name} Company in {city_name} | Nectra Digital'); ?>"></div>
            <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" <?php echo ($item['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option><option value="inactive">Inactive</option></select></div>
            <div class="col-12"><label class="form-label">Meta Description Template</label><textarea name="meta_description_template" class="form-control ckeditor-basic" rows="2"><?php echo htmlspecialchars($item['meta_description_template'] ?? 'Top {service_name} company in {city_name}, {state}. Expert services, free consultation. Contact Nectra Digital.'); ?></textarea></div>
            <div class="col-md-6"><label class="form-label">H1 Template</label><input type="text" name="h1_template" class="form-control" value="<?php echo htmlspecialchars($item['h1_template'] ?? 'Best {service_name} Company in {city_name}'); ?>"></div>
            <div class="col-md-6"><label class="form-label">H2 Template</label><input type="text" name="h2_template" class="form-control" value="<?php echo htmlspecialchars($item['h2_template'] ?? 'Expert {service_name} in {state} · Nectra Digital'); ?>"></div>
            <div class="col-12"><label class="form-label">Content Template (HTML, optional)</label><textarea name="content_template" class="form-control ckeditor-full" rows="4" placeholder="Leave empty for auto-generated unique content"><?php echo $item['content_template'] ?? ''; ?></textarea></div>
            <div class="col-12"><label class="form-label">Extra Keywords (comma-separated)</label><input type="text" name="keywords_template" class="form-control" value="<?php echo htmlspecialchars($item['keywords_template'] ?? ''); ?>"></div>
            <div class="col-md-6"><label class="form-label">Service Image</label><input type="file" name="service_image" class="form-control" accept="image/*"></div>
            <div class="col-md-3"><label class="form-label">Schema Type</label><select name="schema_type" class="form-select"><option value="Service">Service</option><option value="ProfessionalService">ProfessionalService</option></select></div>
            <div class="col-md-3"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="<?php echo (int)($item['sort_order'] ?? 0); ?>"></div>
            <div class="col-12"><label class="form-label">FAQ Templates</label>
                <div id="faqRepeater">
                    <?php if (empty($faqs)) $faqs = [['q'=>'','a'=>'']]; foreach ($faqs as $faq): ?>
                    <div class="row g-2 mb-2 faq-row"><div class="col-md-5"><input type="text" name="faq_q[]" class="form-control" placeholder="Question" value="<?php echo htmlspecialchars($faq['q']); ?>"></div><div class="col-md-6"><input type="text" name="faq_a[]" class="form-control" placeholder="Answer" value="<?php echo htmlspecialchars($faq['a']); ?>"></div></div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addFaqRow()">+ Add FAQ</button>
            </div>
            <div class="col-12"><button type="submit" class="btn btn-ge-primary">Save Service</button><a href="services.php" class="btn btn-outline-secondary ms-2">Cancel</a><?php if ($action === 'add'): ?><p class="text-muted small mt-2 mb-0"><i class="fas fa-magic me-1"></i> Saving an active service auto-generates complete SEO landing pages for all cities.</p><?php endif; ?></div>
        </div>
    </form>
</div>
<script>function addFaqRow(){document.getElementById('faqRepeater').insertAdjacentHTML('beforeend','<div class="row g-2 mb-2 faq-row"><div class="col-md-5"><input type="text" name="faq_q[]" class="form-control" placeholder="Question"></div><div class="col-md-6"><input type="text" name="faq_a[]" class="form-control" placeholder="Answer"></div></div>');}</script>
<?php else: ?>
<?php if (!empty($coverage)): ?>
<div class="ge-card mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h2 class="h6 mb-1">Service × City Coverage</h2>
            <p class="text-muted small mb-0"><?php echo number_format($coverage['pages']); ?> of <?php echo number_format($coverage['expected']); ?> pages (<?php echo $coverage['coverage_pct']; ?>%) · <?php echo $coverage['services']; ?> services × <?php echo $coverage['cities']; ?> cities</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="POST" action="?action=sync_catalog" class="d-inline">
                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync me-1"></i>Sync from Website</button>
            </form>
            <form method="POST" action="?action=generate_all_pages" class="d-inline" onsubmit="return confirm('Generate all service × city pages? This may take a few minutes.')">
                <button type="submit" class="btn btn-sm btn-ge-primary"><i class="fas fa-magic me-1"></i>Generate All City Pages</button>
            </form>
            <a href="landing-pages.php" class="btn btn-sm btn-outline-info">View Landing Pages</a>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="d-flex justify-content-between mb-4"><span class="text-muted"><?php echo count($services); ?> services · <?php echo $cityTotal; ?> cities each</span><a href="?action=add" class="btn btn-ge-primary"><i class="fas fa-plus me-2"></i>Create Service</a></div>
<div class="ge-card"><div class="table-responsive"><table class="table ge-table"><thead><tr><th>Name</th><th>URL Prefix</th><th>City Pages</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($services as $s):
    $pages = (int)($s['page_count'] ?? 0);
    $total = (int)($s['city_total'] ?? $cityTotal);
    $pct = $total > 0 ? (int)round(($pages / $total) * 100) : 0;
?>
<tr>
    <td><strong><?php echo htmlspecialchars($s['name']); ?></strong><br><small class="text-muted"><a href="/<?php echo htmlspecialchars($s['slug']); ?>" target="_blank"><?php echo htmlspecialchars($s['slug']); ?></a></small></td>
    <td><code><?php echo htmlspecialchars($s['url_prefix']); ?>-company-in-{city}</code></td>
    <td>
        <span class="ge-badge ge-badge-<?php echo $pct >= 100 ? 'indexed' : ($pages > 0 ? 'pending' : 'failed'); ?>"><?php echo $pages; ?>/<?php echo $total; ?></span>
        <?php if ($pages > 0): ?><br><a href="landing-pages.php?service_id=<?php echo $s['id']; ?>" class="small">View pages</a><?php endif; ?>
    </td>
    <td><span class="ge-badge ge-badge-<?php echo $s['status']==='active'?'indexed':'pending'; ?>"><?php echo $s['status']; ?></span></td>
    <td class="text-nowrap">
        <a href="?action=edit&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></a>
        <?php if ($pages < $total): ?>
        <form method="POST" action="?action=generate_cities&id=<?php echo $s['id']; ?>" class="d-inline" onsubmit="return confirm('Generate city pages for <?php echo htmlspecialchars($s['name'], ENT_QUOTES); ?>?')">
            <button type="submit" class="btn btn-sm btn-ge-primary" title="Generate all cities"><i class="fas fa-map-marker-alt"></i></button>
        </form>
        <?php endif; ?>
        <a href="generate.php?service_id=<?php echo $s['id']; ?>&mode=service_all_cities" class="btn btn-sm btn-outline-info" title="Generator"><i class="fas fa-magic"></i></a>
        <a href="?action=delete&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete? Cascades landing pages.')"><i class="fas fa-trash"></i></a>
    </td>
</tr>
<?php endforeach; ?>
<?php if (empty($services)): ?><tr><td colspan="5" class="text-muted text-center py-4">No services yet. <form method="POST" action="?action=sync_catalog" class="d-inline"><button type="submit" class="btn btn-sm btn-ge-primary">Sync from Website</button></form> to import all 15 services.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php endif;
ge_admin_layout_end();
