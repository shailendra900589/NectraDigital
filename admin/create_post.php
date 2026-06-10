<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) exit;
require_once '../includes/db.php';
require_once '../includes/ckeditor.php';
require_once '../includes/blog_orphan.php';
require_once '../includes/blog_faq.php';
require_once '../includes/blog_schema.php';

blog_schema_ensure($conn);

date_default_timezone_set('Asia/Kolkata');

function upload_image($file) {
    $target_dir = "../assets/uploads/";
    if ($file["size"] > 512000) return ["error" => "Error: File too large (Max 500KB)."];
    $allowed_types = ['image/webp', 'image/svg+xml'];
    if (!in_array(mime_content_type($file["tmp_name"]), $allowed_types)) return ["error" => "Error: Only WEBP or SVG."];
    $new_name = uniqid("blog_", true) . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
    if (move_uploaded_file($file["tmp_name"], $target_dir . $new_name)) return ["success" => "assets/uploads/" . $new_name];
    return ["error" => "Upload failed."];
}

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = sanitize_db_text($_POST['title']);
    $cat = sanitize_db_text($_POST['category']);
    $content = $_POST['content'];
    $input_slug = sanitize_db_text($_POST['slug']);
    $meta_desc = sanitize_db_text($_POST['meta_description']);
    $is_orphan = !empty($_POST['is_orphan']) ? 1 : 0;
    $faq_json = blog_faq_encode(blog_faq_parse_request());
    $img_path = "";
    $scheduled_time = !empty($_POST['scheduled_time']) ? clean_input($_POST['scheduled_time']) : date('Y-m-d H:i:s');
    $raw_slug = !empty($input_slug) ? $input_slug : $title;
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $raw_slug), '-'));

    if ($slug === '') {
        $error = 'Invalid slug — please enter a title or slug.';
    }

    if ($error === null && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload = upload_image($_FILES['image']);
        if (isset($upload['error'])) {
            $error = $upload['error'];
        } else {
            $img_path = $upload['success'];
        }
    } elseif ($error === null) {
        $error = "Featured Image is required.";
    }

    if ($error === null) {
        $result = blog_insert_post($conn, [
            'title' => $title,
            'category' => $cat,
            'image' => $img_path,
            'content' => $content,
            'slug' => $slug,
            'meta_description' => $meta_desc,
            'faq_json' => $faq_json,
            'created_at' => $scheduled_time,
            'is_orphan' => $is_orphan,
        ]);

        if (!empty($result['ok'])) {
            blog_signal_post_indexed($slug, $scheduled_time);
            header("Location: dashboard.php?page=blog");
            exit;
        }
        $error = 'Could not save post: ' . ($result['error'] ?? 'Unknown database error');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Protocol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php nectra_ckeditor_styles(); ?>
    <style>body{background:#111; color:#fff;} body.admin-editor { padding: 2rem; }</style>
</head>
<body class="admin-editor p-5">
    <div class="container">
        <h3>Create New Intelligence</h3>
        <?php if($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST" class="mt-4" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Title</label>
                    <input type="text" name="title" id="post_title" class="form-control bg-dark text-white" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Custom URL Slug (Optional)</label>
                    <input type="text" name="slug" id="post_slug" class="form-control bg-dark text-white" placeholder="Leave blank to auto-generate">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control bg-dark text-white" placeholder="e.g. AI, SEO" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Featured Image (Max 500KB - WebP/SVG)</label>
                    <input type="file" name="image" class="form-control bg-dark text-white" accept=".webp, .svg" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Schedule (Optional)</label>
                    <input type="datetime-local" name="scheduled_time" class="form-control bg-dark text-white text-secondary">
                </div>
                <div class="col-12 mb-3">
                    <label>SEO Meta Description (Max 160 chars)</label>
                    <textarea name="meta_description" class="form-control bg-dark text-white" rows="2" maxlength="160" placeholder="Plain text summary for search engines..."></textarea>
                </div>
                <div class="col-12 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_orphan" id="is_orphan" value="1">
                        <label class="form-check-label" for="is_orphan">
                            <strong>Orphan post</strong> — direct URL only, hidden from Insights &amp; site listings, still auto-indexed (sitemap + IndexNow)
                        </label>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label>Content Protocol</label>
                    <textarea id="post_content" name="content" class="ckeditor-full"></textarea>
                </div>
                <?php $blog_faqs = []; include __DIR__ . '/includes/blog-faq-form.php'; ?>
            </div>
            <button type="submit" class="btn btn-info">PUBLISH / SCHEDULE</button>
            <a href="dashboard.php?page=blog" class="btn btn-outline-secondary ms-2">CANCEL</a>
        </form>
    </div>
    <?php nectra_ckeditor_scripts('../assets'); ?>
    <script>
        document.getElementById('post_title').addEventListener('input', function() {
            var slug = document.getElementById('post_slug');
            if (!slug.dataset.manual) {
                slug.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
            }
        });
        document.getElementById('post_slug').addEventListener('input', function() {
            this.dataset.manual = '1';
        });
    </script>
</body>
</html>
