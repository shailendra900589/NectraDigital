<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) exit;
include '../includes/db.php';

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM blog_posts WHERE id=$id");
$post = $result->fetch_assoc();

// --- SAME UPLOAD FUNCTION ---
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
    $title = clean_input($_POST['title']);
    $cat = clean_input($_POST['category']);
    $content = $_POST['content'];
    $input_slug = clean_input($_POST['slug']); // NEW: Get slug from form
    $meta_desc = clean_input($_POST['meta_description']); // NEW: Get Meta Description
    $img_path = $post['image']; // Keep old image by default

    // NEW SLUG LOGIC
    // If the user left the custom slug empty, generate it from the title
    $raw_slug = !empty($input_slug) ? $input_slug : $title;
    // Clean the slug: lowercase, replace spaces/special chars with hyphens
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $raw_slug), '-'));

    // Handle New Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload = upload_image($_FILES['image']);
        if (isset($upload['error'])) {
            $error = $upload['error'];
        } else {
            $img_path = $upload['success'];
        }
    }

    // UPDATE DATABASE WITH NEW SLUG & META DESCRIPTION
    if (!isset($error)) {
        $stmt = $conn->prepare("UPDATE blog_posts SET title=?, category=?, image=?, content=?, slug=?, meta_description=? WHERE id=?");
        $stmt->bind_param("ssssssi", $title, $cat, $img_path, $content, $slug, $meta_desc, $id);
        if ($stmt->execute()) header("Location: dashboard.php?page=blog");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Protocol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>body{background:#111; color:#fff;} .note-editable{background:#222; color:#fff;}</style>
</head>
<body class="p-5">
    <div class="container">
        <h3>Edit Intelligence: <span class="text-info"><?php echo htmlspecialchars($post['title']); ?></span></h3>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        
        <form method="POST" class="mt-4" enctype="multipart/form-data">
            <div class="row">
                
                <div class="col-md-6 mb-3">
                    <label>Title</label>
                    <input type="text" name="title" id="post_title" value="<?php echo htmlspecialchars($post['title']); ?>" class="form-control bg-dark text-white" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Custom URL Slug</label>
                    <input type="text" name="slug" id="post_slug" value="<?php echo isset($post['slug']) ? htmlspecialchars($post['slug']) : ''; ?>" class="form-control bg-dark text-white" placeholder="Leave blank to auto-generate">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Category</label>
                    <input type="text" name="category" value="<?php echo htmlspecialchars($post['category']); ?>" class="form-control bg-dark text-white" required>
                </div>

                <div class="col-md-8 mb-3">
                    <label>Current Image</label><br>
                    <?php if(!empty($post['image'])): ?>
                        <img src="../<?php echo htmlspecialchars($post['image']); ?>" height="50" class="mb-2 border border-secondary" style="object-fit: cover;">
                    <?php endif; ?>
                    <label class="d-block text-white-50 small">Upload New to Replace (Max 500KB - WebP/SVG)</label>
                    <input type="file" name="image" class="form-control bg-dark text-white" accept=".webp, .svg">
                </div>

                <div class="col-12 mb-3">
                    <label>SEO Meta Description (Max 160 chars)</label>
                    <textarea name="meta_description" class="form-control bg-dark text-white" rows="2" maxlength="160" placeholder="Enter a compelling summary for search engines..."><?php echo isset($post['meta_description']) ? htmlspecialchars($post['meta_description']) : ''; ?></textarea>
                </div>

                <div class="col-12 mb-3">
                    <label>Content Protocol</label>
                    <textarea id="summernote" name="content"><?php echo $post['content']; ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-warning">UPDATE</button>
            <a href="dashboard.php?page=blog" class="btn btn-outline-secondary ms-2">CANCEL</a>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        // Initialize Summernote
        $('#summernote').summernote({ height: 400 });

        // Auto-fill slug as user types title ONLY if slug field is currently empty 
        // (so we don't accidentally overwrite an existing slug while editing the title)
        $('#post_title').on('input', function() {
            if(!$('#post_slug').data('manually-edited') && $('#post_slug').val() === '') {
                var val = $(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
                $('#post_slug').val(val);
            }
        });
        
        // Track if user manually edits the slug
        $('#post_slug').on('input', function() {
            $(this).data('manually-edited', true);
        });
    </script>
</body>
</html>