<?php
require_once 'includes/auth.php';
require_once __DIR__ . '/../../includes/growth/bootstrap.php';

use Growth\Models\CaseStudy;
use Growth\Models\Service;

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'slug' => ge_slugify($_POST['slug'] ?? $_POST['title'] ?? ''),
        'service_id' => !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null,
        'category' => trim($_POST['category'] ?? ''),
        'client_name' => trim($_POST['client_name'] ?? ''),
        'client_industry' => trim($_POST['client_industry'] ?? ''),
        'results_summary' => trim($_POST['results_summary'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'meta_title' => trim($_POST['meta_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'status' => $_POST['status'] ?? 'draft',
    ];
    if (!empty($_FILES['image']['name'])) {
        $up = ge_upload_image($_FILES['image'], 'cs_');
        if (!empty($up['success'])) $data['image'] = $up['success'];
    }
    if ($action === 'edit' && $id) {
        $ex = CaseStudy::find($id);
        if ($ex && empty($data['image'])) $data['image'] = $ex['image'];
        CaseStudy::update($id, $data);
        ge_admin_flash('success', 'Case study updated.');
    } else {
        CaseStudy::create($data);
        ge_admin_flash('success', 'Case study created.');
    }
    header('Location: case-studies.php');
    exit;
}

if ($action === 'delete' && $id) {
    CaseStudy::delete($id);
    ge_admin_flash('success', 'Deleted.');
    header('Location: case-studies.php');
    exit;
}

$item = $id ? CaseStudy::find($id) : null;
$studies = ge_is_ready() ? CaseStudy::all() : [];
$services = ge_is_ready() ? Service::all(true) : [];

require_once 'includes/layout.php';
ge_admin_layout_start('Case Studies', 'case-studies');

if ($action === 'add' || ($action === 'edit' && $item)):
?>
<div class="ge-card"><form method="POST" enctype="multipart/form-data"><div class="row g-3">
    <div class="col-md-8"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($item['title'] ?? ''); ?>"></div>
    <div class="col-md-4"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($item['slug'] ?? ''); ?>"></div>
    <div class="col-md-4"><label class="form-label">Category</label><input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($item['category'] ?? ''); ?>" placeholder="SEO, Software, Design"></div>
    <div class="col-md-4"><label class="form-label">Service</label><select name="service_id" class="form-select"><option value="">—</option><?php foreach ($services as $s): ?><option value="<?php echo $s['id']; ?>" <?php echo ($item['service_id']??'')==$s['id']?'selected':''; ?>><?php echo htmlspecialchars($s['name']); ?></option><?php endforeach; ?></select></div>
    <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><option value="draft">Draft</option><option value="published" <?php echo ($item['status']??'')==='published'?'selected':''; ?>>Published</option></select></div>
    <div class="col-md-6"><label class="form-label">Client Name</label><input type="text" name="client_name" class="form-control" value="<?php echo htmlspecialchars($item['client_name'] ?? ''); ?>"></div>
    <div class="col-md-6"><label class="form-label">Industry</label><input type="text" name="client_industry" class="form-control" value="<?php echo htmlspecialchars($item['client_industry'] ?? ''); ?>"></div>
    <div class="col-12"><label class="form-label">Results Summary</label><textarea name="results_summary" class="form-control" rows="2"><?php echo htmlspecialchars($item['results_summary'] ?? ''); ?></textarea></div>
    <div class="col-12"><label class="form-label">Content (HTML)</label><textarea name="content" class="form-control" rows="8"><?php echo htmlspecialchars($item['content'] ?? ''); ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-control" value="<?php echo htmlspecialchars($item['meta_title'] ?? ''); ?>"></div>
    <div class="col-md-6"><label class="form-label">Featured Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
    <div class="col-12"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="2"><?php echo htmlspecialchars($item['meta_description'] ?? ''); ?></textarea></div>
    <div class="col-12"><button type="submit" class="btn btn-ge-primary">Save</button><a href="case-studies.php" class="btn btn-outline-secondary ms-2">Cancel</a></div>
</div></form></div>
<?php else: ?>
<div class="d-flex justify-content-between mb-4"><span></span><a href="?action=add" class="btn btn-ge-primary"><i class="fas fa-plus"></i> New Case Study</a></div>
<div class="ge-card"><table class="table ge-table"><thead><tr><th>Title</th><th>Category</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($studies as $cs): ?><tr><td><?php echo htmlspecialchars($cs['title']); ?></td><td><?php echo htmlspecialchars($cs['category'] ?? ''); ?></td><td><?php echo $cs['status']; ?></td><td><a href="?action=edit&id=<?php echo $cs['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a> <a href="?action=delete&id=<?php echo $cs['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php endif;
ge_admin_layout_end();
