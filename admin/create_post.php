<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
include '../includes/db.php';

date_default_timezone_set('Asia/Kolkata'); 

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = clean_input($_POST['title']);
    $cat = clean_input($_POST['category']);
    $content = $_POST['content'];
    $input_slug = clean_input($_POST['slug']);
    $meta_desc = clean_input($_POST['meta_description']);
    $img_path = "";

    $scheduled_time = !empty($_POST['scheduled_time']) ? clean_input($_POST['scheduled_time']) : date('Y-m-d H:i:s');

    $raw_slug = !empty($input_slug) ? $input_slug : $title;
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $raw_slug), '-'));

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload = upload_image($_FILES['image']);
        if (isset($upload['error'])) {
            $error = $upload['error'];
        } else {
            $img_path = $upload['success'];
        }
    } else {
        $error = "Featured image is required.";
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO blog_posts (title, category, image, content, slug, meta_description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $title, $cat, $img_path, $content, $slug, $meta_desc, $scheduled_time);
        if ($stmt->execute()) {
            // Auto-send push notification for new blog post
            $notif_payload = json_encode([
                'title' => 'New Post: ' . $title,
                'body' => !empty($meta_desc) ? substr($meta_desc, 0, 100) : substr(strip_tags($content), 0, 100),
                'url' => '/nectradigital_final/' . $slug,
                'icon' => '/nectradigital_final/assets/images/logo.png'
            ]);
            $subs = $conn->query("SELECT * FROM push_subscriptions");
            while($s = $subs->fetch_assoc()) {
                $ch = curl_init($s['endpoint']);
                curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $notif_payload, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'TTL: 86400'], CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5]);
                curl_exec($ch); curl_close($ch);
            }
            header("Location: dashboard.php?page=blog");
            exit;
        } else {
            $error = "Database error: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post | Nectra Admin</title>
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
    </style>
</head>
<body>
    <div class="container py-4" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-plus-circle text-info me-2"></i>Create New Blog Post</h4>
            <a href="dashboard.php?page=blog" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>

        <?php if(isset($error)) echo "<div class='alert alert-danger py-2'>$error</div>"; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-8">
                    <label>Post Title *</label>
                    <input type="text" name="title" id="post_title" class="form-control" placeholder="Enter blog post title" required>
                </div>
                <div class="col-md-4">
                    <label>Category *</label>
                    <input type="text" name="category" class="form-control" placeholder="e.g. Digital Marketing" required>
                </div>
                
                <div class="col-md-6">
                    <label>URL Slug (auto-generated if empty)</label>
                    <input type="text" name="slug" id="post_slug" class="form-control" placeholder="custom-url-slug">
                </div>
                <div class="col-md-6">
                    <label>Schedule Date (optional)</label>
                    <input type="datetime-local" name="scheduled_time" class="form-control">
                </div>

                <div class="col-md-6">
                    <label>Featured Image * (Max 2MB - PNG, JPG, WebP, SVG, GIF)</label>
                    <input type="file" name="image" class="form-control" accept=".webp,.svg,.png,.jpg,.jpeg,.gif" required>
                </div>
                <div class="col-md-6">
                    <label>Meta Description (SEO - Max 160 chars)</label>
                    <textarea name="meta_description" class="form-control" rows="2" maxlength="160" placeholder="Compelling summary for search engines..."></textarea>
                </div>
                
                <div class="col-12">
                    <label>Content *</label>
                    <textarea id="summernote" name="content"></textarea>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-cyan px-4 py-2"><i class="fas fa-paper-plane me-2"></i>Publish Post</button>
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
            placeholder: 'Write your blog post content here...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video', 'table', 'hr']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });

        $('#post_title').on('input', function() {
            if(!$('#post_slug').data('manually-edited')) {
                var val = $(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
                $('#post_slug').val(val);
            }
        });
        $('#post_slug').on('input', function() { $(this).data('manually-edited', true); });
    </script>
</body>
</html>
