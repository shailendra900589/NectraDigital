<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) exit;
include '../includes/db.php';
require_once '../includes/ckeditor.php';
require_once '../includes/blog_orphan.php';
require_once '../includes/blog_faq.php';
blog_orphan_ensure_schema($conn);
blog_faq_ensure_schema($conn);

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM blog_posts WHERE id=$id");
$post = $result->fetch_assoc();

function upload_image($file) {
    $target_dir = "../assets/uploads/";
    if ($file["size"] > 512000) return ["error" => "Error: File too large (Max 500KB)."];
    $allowed_types = ['image/webp', 'image/svg+xml'];
    if (!in_array(mime_content_type($file["tmp_name"]), $allowed_types)) return ["error" => "Error: Only WEBP or SVG."];
    $new_name = uniqid("blog_", true) . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
    if (move_uploaded_file($file["tmp_name"], $target_dir . $new_name)) return ["success" => "assets/uploads/" . $new_name];
    return ["error" => "Upload failed."];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = sanitize_db_text($_POST['title']);
    $cat = sanitize_db_text($_POST['category']);
    $content = $_POST['content'];
    $input_slug = sanitize_db_text($_POST['slug']);
    $meta_desc = sanitize_db_text($_POST['meta_description']);
    $is_orphan = !empty($_POST['is_orphan']) ? 1 : 0;
    $faq_json = blog_faq_encode(blog_faq_parse_request());
    $img_path = $post['image'];
    $raw_slug = !empty($input_slug) ? $input_slug : $title;
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $raw_slug), '-'));

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload = upload_image($_FILES['image']);
        if (isset($upload['error'])) {
            $error = $upload['error'];
        } else {
            $img_path = $upload['success'];
        }
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("UPDATE blog_posts SET title=?, category=?, image=?, content=?, slug=?, meta_description=?, faq_json=?, is_orphan=? WHERE id=?");
        $stmt->bind_param("sssssssii", $title, $cat, $img_path, $content, $slug, $meta_desc, $faq_json, $is_orphan, $id);
        if ($stmt->execute()) {
            blog_signal_post_indexed($slug, $post['created_at'] ?? null);
            header("Location: dashboard.php?page=blog");
        }
    }
}
$post_is_orphan = blog_is_orphan($post);
$blog_faqs = blog_faq_decode($post['faq_json'] ?? null);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Protocol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php nectra_ckeditor_styles(); ?>
    <style>body{background:#111; color:#fff;} body.admin-editor { padding: 2rem; }</style>
</head>
<body class="admin-editor p-5">
    <div class="container">
        <h3>Edit Intelligence: <span class="text-info"><?php echo nectra_display_text($post['title']); ?></span></h3>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST" class="mt-4" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Title</label>
                    <input type="text" name="title" id="post_title" value="<?php echo nectra_display_text($post['title']); ?>" class="form-control bg-dark text-white" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Custom URL Slug</label>
                    <input type="text" name="slug" id="post_slug" value="<?php echo isset($post['slug']) ? htmlspecialchars($post['slug']) : ''; ?>" class="form-control bg-dark text-white">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Category</label>
                    <input type="text" name="category" value="<?php echo nectra_display_text($post['category']); ?>" class="form-control bg-dark text-white" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label>Current Image</label><br>
                    <?php if(!empty($post['image'])): ?>
                        <img src="../<?php echo htmlspecialchars($post['image']); ?>" height="50" class="mb-2 border border-secondary" style="object-fit: cover;">
                    <?php endif; ?>
                    <label class="d-block text-white-50 small">Upload New to Replace</label>
                    <input type="file" name="image" class="form-control bg-dark text-white" accept=".webp, .svg">
                </div>
                <div class="col-12 mb-3">
                    <label>SEO Meta Description (Max 160 chars)</label>
                    <textarea name="meta_description" class="form-control bg-dark text-white" rows="2" maxlength="160"><?php echo isset($post['meta_description']) ? nectra_display_text($post['meta_description']) : ''; ?></textarea>
                </div>
                <div class="col-12 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_orphan" id="is_orphan" value="1" <?php echo !empty($post_is_orphan) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_orphan">
                            <strong>Orphan post</strong> — direct URL only, hidden from Insights &amp; site listings, still auto-indexed
                        </label>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label>Content Protocol</label>
                    <textarea id="post_content" name="content" class="ckeditor-full"><?php echo $post['content']; ?></textarea>
                </div>
                <?php include __DIR__ . '/includes/blog-faq-form.php'; ?>
            </div>
            <button type="submit" class="btn btn-warning">UPDATE</button>
            <a href="dashboard.php?page=blog" class="btn btn-outline-secondary ms-2">CANCEL</a>
        </form>
    </div>
    <?php nectra_ckeditor_scripts('../assets'); ?>
    <script>
        document.getElementById('post_slug').addEventListener('input', function() { this.dataset.manual = '1'; });
    </script>
</body>
</html>
