<?php
require_once 'includes/auth.php';
require_once __DIR__ . '/../../includes/growth/bootstrap.php';

use Growth\Models\KnowledgeBase;
use Growth\Models\Author;

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ge_table_exists('ge_knowledge_base')) {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'slug' => ge_slugify($_POST['slug'] ?? $_POST['title'] ?? ''),
        'category' => trim($_POST['category'] ?? ''),
        'silo' => trim($_POST['silo'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'quick_answer' => trim($_POST['quick_answer'] ?? ''),
        'author_id' => (int)($_POST['author_id'] ?? 0),
        'meta_title' => trim($_POST['meta_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'status' => $_POST['status'] ?? 'draft',
    ];
    if ($action === 'edit' && $id) {
        KnowledgeBase::update($id, $data);
        ge_admin_flash('success', 'Article updated.');
    } else {
        KnowledgeBase::create($data);
        ge_admin_flash('success', 'Article created.');
    }
    header('Location: knowledge-base.php');
    exit;
}

if ($action === 'delete' && $id) {
    KnowledgeBase::delete($id);
    ge_admin_flash('success', 'Deleted.');
    header('Location: knowledge-base.php');
    exit;
}

$item = ($id && ge_table_exists('ge_knowledge_base')) ? KnowledgeBase::all() : [];
$articles = ge_table_exists('ge_knowledge_base') ? KnowledgeBase::all() : [];
$authors = ge_table_exists('ge_authors') ? Author::all(true) : [];
$editItem = null;
if ($id && ge_table_exists('ge_knowledge_base')) {
    foreach ($articles as $a) { if ((int)$a['id'] === $id) { $editItem = $a; break; } }
}

require_once 'includes/layout.php';
ge_admin_layout_start('Knowledge Base', 'knowledge');
?>

<?php if (!ge_table_exists('ge_knowledge_base')): ?>
<div class="alert alert-warning">Run migration first.</div>
<?php elseif ($action === 'add' || ($action === 'edit' && $editItem)): ?>
<div class="ge-card"><form method="POST"><div class="row g-3">
<div class="col-md-8"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($editItem['title'] ?? ''); ?>"></div>
<div class="col-md-4"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($editItem['slug'] ?? ''); ?>"></div>
<div class="col-md-4"><label class="form-label">Category</label><input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($editItem['category'] ?? ''); ?>"></div>
<div class="col-md-4"><label class="form-label">Topic Silo</label><input type="text" name="silo" class="form-control" placeholder="SEO, AI Automation..." value="<?php echo htmlspecialchars($editItem['silo'] ?? ''); ?>"></div>
<div class="col-md-4"><label class="form-label">Author</label><select name="author_id" class="form-select"><option value="0">—</option><?php foreach ($authors as $au): ?><option value="<?php echo $au['id']; ?>" <?php echo (int)($editItem['author_id'] ?? 0) === (int)$au['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($au['name']); ?></option><?php endforeach; ?></select></div>
<div class="col-12"><label class="form-label">Quick Answer (AEO)</label><textarea name="quick_answer" class="form-control" rows="2"><?php echo htmlspecialchars($editItem['quick_answer'] ?? ''); ?></textarea></div>
<div class="col-12"><label class="form-label">Content</label><textarea name="content" class="form-control" rows="10"><?php echo htmlspecialchars($editItem['content'] ?? ''); ?></textarea></div>
<div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="draft">Draft</option><option value="published" <?php echo ($editItem['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option></select></div>
<div class="col-12"><button type="submit" class="btn btn-ge-primary">Save</button><a href="knowledge-base.php" class="btn btn-outline-secondary ms-2">Cancel</a></div>
</div></form></div>
<?php else: ?>
<div class="d-flex justify-content-between mb-4"><span class="text-muted"><?php echo count($articles); ?> articles</span><a href="?action=add" class="btn btn-ge-primary"><i class="fas fa-plus"></i> Add Article</a></div>
<div class="ge-card"><table class="table ge-table table-sm"><thead><tr><th>Title</th><th>Silo</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($articles as $a): ?>
<tr><td><?php echo htmlspecialchars($a['title']); ?></td><td><?php echo htmlspecialchars($a['silo'] ?? '-'); ?></td><td><?php echo $a['status']; ?></td>
<td><a href="?action=edit&id=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
<a href="?action=delete&id=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif;

ge_admin_layout_end();
