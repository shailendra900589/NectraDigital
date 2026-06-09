<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) exit;
include '../includes/db.php';
require_once '../includes/ckeditor.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = clean_input($_POST['position']);
    $stack = clean_input($_POST['stack']);
    $loc = clean_input($_POST['location']);
    $desc = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO careers (position, stack, location, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $stack, $loc, $desc);
    if ($stmt->execute()) {
        header("Location: dashboard.php?page=careers");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Open Recruitment Channel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php nectra_ckeditor_styles(); ?>
    <style>body{background:#111; color:#fff;} body.admin-editor { padding: 2rem; }</style>
</head>
<body class="admin-editor p-5">
    <div class="container">
        <h3 class="mb-4">Initialize Recruitment Protocol</h3>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Position Title</label>
                    <input type="text" name="position" class="form-control bg-dark text-white" required>
                </div>
                <div class="col-md-3">
                    <label>Tech Stack</label>
                    <input type="text" name="stack" class="form-control bg-dark text-white" required>
                </div>
                <div class="col-md-3">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control bg-dark text-white" value="Remote / Global" required>
                </div>
                <div class="col-12">
                    <label>Mission Briefing (Description)</label>
                    <textarea name="description" class="ckeditor-full" required></textarea>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-info w-100">DEPLOY JOB POST</button>
                    <a href="dashboard.php?page=careers" class="btn btn-outline-secondary w-100 mt-2">CANCEL</a>
                </div>
            </div>
        </form>
    </div>
    <?php nectra_ckeditor_scripts('../assets'); ?>
</body>
</html>
