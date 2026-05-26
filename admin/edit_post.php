<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
include '../includes/db.php';

if (!isset($_GET['id'])) { header("Location: dashboard.php?page=blog"); exit; }

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM blog_posts WHERE id=$id");
if ($result->num_rows == 0) { header("Location: dashboard.php?page=blog"); exit; }
$post = $result->fetch_assoc();

function upload_image($file) {
    $target_dir = "../assets/uploads/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    if ($file["size"] > 2097152) return ["error" => "File too large. Max 2MB allowed."];
    $allowed_types = ['image/webp', 'image/svg+xml', 'image/png', 'image/jpeg', 'image/gif'];
    if (!in_array(mime_content_type($file["tmp_name"]), $allowed_types)) return ["error" => "Only WEBP, SVG, PNG, JPG, GIF allowed."];
    $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
    $new_name = uniqid("blog_", true) . "." . $ext;
    if (move_uploaded_file($file["tmp_name"], $target_dir . $new_name)) return ["success" => "assets/uploads/" . $new_name];
    return ["error" => "Upload failed."];
}

$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = clean_input($_POST['title']);
    $cat = clean_input($_POST['category']);
    $content = $_POST['content'];
    $input_slug = clean_input($_POST['slug']);
    $meta_desc = clean_input($_POST['meta_description']);
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
        $stmt = $conn->prepare("UPDATE blog_posts SET title=?, category=?, image=?, content=?, slug=?, meta_description=? WHERE id=?");
        $stmt->bind_param("ssssssi", $title, $cat, $img_path, $content, $slug, $meta_desc, $id);
        if ($stmt->execute()) {
            $success = "Post updated successfully!";
            $result = $conn->query("SELECT * FROM blog_posts WHERE id=$id");
            $post = $result->fetch_assoc();
        } else {
            $error = "Update failed: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post | Nectra Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #0d0d0d; color: #fff; font-family: 'Inter', sans-serif; }
        .form-control, .form-select { background-color: #1a1a1a; border-color: #333; color: #fff; border-radius: 8px; }
        .form-control:focus, .form-select:focus { background-color: #222; color: #fff; border-color: #00E5FF; box-shadow: 0 0 0 2px rgba(0,229,255,0.1); }
        .note-editable { background: #1a1a1a; color: #fff; }
        .note-toolbar { background: #222; }
        .btn-cyan { background: #00E5FF; color: #000; font-weight: 600; border: none; }
        .btn-cyan:hover { background: #00bcd4; color: #000; }
        label { font-size: 0.85rem; color: #999; margin-bottom: 4px; }
        .current-img { max-height: 80px; border: 1px solid #333; border-radius: 6px; object-fit: cover; }
    </style>
</head>
<body>
    <div class="container py-4" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-pen text-warning me-2"></i>Edit Post</h4>
            <a href="dashboard.php?page=blog" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>

        <?php if(isset($error)) echo "<div class='alert alert-danger py-2'>$error</div>"; ?>
        <?php if($success) echo "<div class='alert alert-success py-2'>$success</div>"; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-8">
                    <label>Post Title *</label>
                    <input type="text" name="title" id="post_title" value="<?php echo htmlspecialchars($post['title']); ?>" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Category *</label>
                    <input type="text" name="category" value="<?php echo htmlspecialchars($post['category']); ?>" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label>URL Slug</label>
                    <input type="text" name="slug" id="post_slug" value="<?php echo htmlspecialchars($post['slug'] ?? ''); ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Current Image</label>
                    <div class="d-flex align-items-center gap-3">
                        <?php if(!empty($post['image'])): ?>
                            <img src="../<?php echo htmlspecialchars($post['image']); ?>" class="current-img">
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <input type="file" name="image" class="form-control" accept=".webp,.svg,.png,.jpg,.jpeg,.gif">
                            <small class="text-white-50">Upload new to replace (Max 2MB)</small>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label>Meta Description (SEO - Max 160 chars)</label>
                    <textarea name="meta_description" class="form-control" rows="2" maxlength="160"><?php echo htmlspecialchars($post['meta_description'] ?? ''); ?></textarea>
                </div>

                <div class="col-12">
                    <label>Content *</label>
                    <textarea id="summernote" name="content"><?php echo $post['content']; ?></textarea>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-warning px-4 py-2 fw-bold"><i class="fas fa-save me-2"></i>Update Post</button>
                    <a href="dashboard.php?page=blog" class="btn btn-outline-secondary ms-2 px-4 py-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $('#summernote').summernote({
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video', 'table', 'hr']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });

        $('#post_title').on('input', function() {
            if(!$('#post_slug').data('manually-edited') && $('#post_slug').val() === '') {
                var val = $(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
                $('#post_slug').val(val);
            }
        });
        $('#post_slug').on('input', function() { $(this).data('manually-edited', true); });
    </script>
</body>
</html>
