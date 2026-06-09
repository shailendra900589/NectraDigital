<?php
require_once __DIR__ . '/init.php';

use Growth\Models\Service;

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pageError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ge_admin_require_ready()) {
        ge_admin_flash('error', 'Database not migrated. Run database/migrate.php first.');
        header('Location: services.php');
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
        Service::create($data);
        ge_admin_flash('success', 'Service created.');
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
$services = ge_admin_require_ready() ? ge_admin_safe(fn() => Service::all(), []) : [];

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
            <div class="col-md-8"><label class="form-label">Meta Title Template</label><input type="text" name="meta_title_template" class="form-control" value="<?php echo htmlspecialchars($item['meta_title_template'] ?? 'Best {service_name} in {city_name} | Nectra Digital'); ?>"></div>
            <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" <?php echo ($item['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option><option value="inactive">Inactive</option></select></div>
            <div class="col-12"><label class="form-label">Meta Description Template</label><textarea name="meta_description_template" class="form-control ckeditor-basic" rows="2"><?php echo htmlspecialchars($item['meta_description_template'] ?? 'Top {service_name} company in {city_name}, {state}. Expert services, free consultation. Contact Nectra Digital.'); ?></textarea></div>
            <div class="col-md-6"><label class="form-label">H1 Template</label><input type="text" name="h1_template" class="form-control" value="<?php echo htmlspecialchars($item['h1_template'] ?? 'Best {service_name} in {city_name}'); ?>"></div>
            <div class="col-md-6"><label class="form-label">H2 Template</label><input type="text" name="h2_template" class="form-control" value="<?php echo htmlspecialchars($item['h2_template'] ?? 'Professional {service_name} in {state}'); ?>"></div>
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
            <div class="col-12"><button type="submit" class="btn btn-ge-primary">Save Service</button><a href="services.php" class="btn btn-outline-secondary ms-2">Cancel</a></div>
        </div>
    </form>
</div>
<script>function addFaqRow(){document.getElementById('faqRepeater').insertAdjacentHTML('beforeend','<div class="row g-2 mb-2 faq-row"><div class="col-md-5"><input type="text" name="faq_q[]" class="form-control" placeholder="Question"></div><div class="col-md-6"><input type="text" name="faq_a[]" class="form-control" placeholder="Answer"></div></div>');}</script>
<?php else: ?>
<div class="d-flex justify-content-between mb-4"><span class="text-muted"><?php echo count($services); ?> services</span><a href="?action=add" class="btn btn-ge-primary"><i class="fas fa-plus me-2"></i>Create Service</a></div>
<div class="ge-card"><div class="table-responsive"><table class="table ge-table"><thead><tr><th>Name</th><th>URL Prefix</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($services as $s): ?>
<tr><td><strong><?php echo htmlspecialchars($s['name']); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($s['slug']); ?></small></td><td><code><?php echo htmlspecialchars($s['url_prefix']); ?>-company-{city}</code></td><td><span class="ge-badge ge-badge-<?php echo $s['status']==='active'?'indexed':'pending'; ?>"><?php echo $s['status']; ?></span></td><td><a href="?action=edit&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a> <a href="?action=delete&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete? Cascades landing pages.')"><i class="fas fa-trash"></i></a></td></tr>
<?php endforeach; ?>
<?php if (empty($services)): ?><tr><td colspan="4" class="text-muted text-center py-4">No services yet. Create your first service to start generating pages.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php endif;
ge_admin_layout_end();
