<?php
require_once __DIR__ . '/init.php';

use Growth\Models\Author;

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ge_table_exists('ge_authors')) {
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => ge_slugify($_POST['slug'] ?? $_POST['name'] ?? ''),
        'title' => trim($_POST['title'] ?? ''),
        'bio' => trim($_POST['bio'] ?? ''),
        'linkedin' => trim($_POST['linkedin'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'is_founder' => !empty($_POST['is_founder']) ? 1 : 0,
        'status' => $_POST['status'] ?? 'active',
    ];
    if ($action === 'edit' && $id) {
        Author::update($id, $data);
        ge_admin_flash('success', 'Author updated.');
    } else {
        Author::create($data);
        ge_admin_flash('success', 'Author created.');
    }
    header('Location: authors.php');
    exit;
}

$item = ($id && ge_table_exists('ge_authors')) ? Author::find($id) : null;
$authors = ge_table_exists('ge_authors') ? Author::all() : [];

ge_admin_layout();
ge_admin_layout_start('Authors Manager (EEAT)', 'authors');

if (!ge_table_exists('ge_authors')): ?>
<div class="alert alert-warning">Run migration to enable Authors.</div>
<?php elseif ($action === 'add' || ($action === 'edit' && $item)): ?>
<div class="ge-card"><form method="POST"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($item['name'] ?? ''); ?>"></div>
<div class="col-md-6"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($item['slug'] ?? ''); ?>"></div>
<div class="col-md-6"><label class="form-label">Title</label><input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title'] ?? ''); ?>"></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($item['email'] ?? ''); ?>"></div>
<div class="col-12"><label class="form-label">Bio</label><textarea name="bio" class="form-control ckeditor-basic" rows="4"><?php echo $item['bio'] ?? ''; ?></textarea></div>
<div class="col-md-8"><label class="form-label">LinkedIn</label><input type="url" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($item['linkedin'] ?? ''); ?>"></div>
<div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
<div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_founder" id="is_founder" <?php echo !empty($item['is_founder']) ? 'checked' : ''; ?>><label class="form-check-label" for="is_founder">Founder (Person Schema)</label></div></div>
<div class="col-12"><button type="submit" class="btn btn-ge-primary">Save</button><a href="authors.php" class="btn btn-outline-secondary ms-2">Cancel</a></div>
</div></form></div>
<?php else: ?>
<div class="d-flex justify-content-between mb-4"><span class="text-muted"><?php echo count($authors); ?> authors</span><a href="?action=add" class="btn btn-ge-primary"><i class="fas fa-plus"></i> Add Author</a></div>
<div class="ge-card"><table class="table ge-table table-sm"><thead><tr><th>Name</th><th>Title</th><th>Founder</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($authors as $a): ?>
<tr><td><strong><?php echo htmlspecialchars($a['name']); ?></strong></td><td><?php echo htmlspecialchars($a['title'] ?? ''); ?></td><td><?php echo $a['is_founder'] ? 'Yes' : '-'; ?></td><td><?php echo $a['status']; ?></td><td><a href="?action=edit&id=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif;
ge_admin_layout_end();
