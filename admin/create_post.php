<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) exit;
include '../includes/db.php';

// Set timezone to match your local time for accurate scheduling
date_default_timezone_set('Asia/Kolkata'); 

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
    $input_slug = clean_input($_POST['slug']);
    $meta_desc = clean_input($_POST['meta_description']);
    $img_path = "";

    // NEW: Capture Scheduled Time. If left blank, use current time.
    $scheduled_time = !empty($_POST['scheduled_time']) ? clean_input($_POST['scheduled_time']) : date('Y-m-d H:i:s');

    // If the user left the custom slug empty, generate it from the title
    $raw_slug = !empty($input_slug) ? $input_slug : $title;
    
    // Clean the slug: lowercase, replace spaces/special chars with hyphens, and remove trailing hyphens
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $raw_slug), '-'));

    // Handle Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload = upload_image($_FILES['image']);
        if (isset($upload['error'])) {
            $error = $upload['error'];
        } else {
            $img_path = $upload['success'];
        }
    } else {
        $error = "Featured Image is required.";
    }

    // NEW: Updated SQL to insert the scheduled_time into your created_at column
    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO blog_posts (title, category, image, content, slug, meta_description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $title, $cat, $img_path, $content, $slug, $meta_desc, $scheduled_time);
        if ($stmt->execute()) header("Location: dashboard.php?page=blog");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Protocol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>body{background:#111; color:#fff;} .note-editable{background:#222; color:#fff;}</style>
</head>
<body class="p-5">
    <div class="container">
        <h3>Create New Intelligence</h3>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
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
                    <textarea name="meta_description" class="form-control bg-dark text-white" rows="2" maxlength="160" placeholder="Enter a compelling summary for search engines..."></textarea>
                </div>
                
                <div class="col-12 mb-3">
                    <label>Content Protocol</label>
                    <textarea id="summernote" name="content"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-info">PUBLISH / SCHEDULE</button>
            <a href="dashboard.php?page=blog" class="btn btn-outline-secondary ms-2">CANCEL</a>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        // Initialize Summernote
        $('#summernote').summernote({ height: 400 });

        // Auto-fill slug as user types title (but allow manual editing)
        $('#post_title').on('input', function() {
            if(!$('#post_slug').data('manually-edited')) {
                var val = $(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
                $('#post_slug').val(val);
            }
        });
        
        // Stop auto-fill if user decides to manually edit the slug
        $('#post_slug').on('input', function() {
            $(this).data('manually-edited', true);
        });
    </script>
</body>
</html>