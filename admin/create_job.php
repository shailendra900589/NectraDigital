<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) exit;
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = clean_input($_POST['position']);
    $stack = clean_input($_POST['stack']);
    $loc = clean_input($_POST['location']);
    $desc = $_POST['description']; // HTML allowed

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
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>body{background:#111; color:#fff;} .note-editable{background:#222; color:#fff;}</style>
</head>
<body class="p-5">
    <div class="container">
        <h3 class="mb-4">Initialize Recruitment Protocol</h3>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Position Title</label>
                    <input type="text" name="position" class="form-control bg-dark text-white" placeholder="e.g. Senior Python Architect" required>
                </div>
                <div class="col-md-3">
                    <label>Tech Stack</label>
                    <input type="text" name="stack" class="form-control bg-dark text-white" placeholder="e.g. Django / AWS" required>
                </div>
                <div class="col-md-3">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control bg-dark text-white" value="Remote / Global" required>
                </div>
                <div class="col-12">
                    <label>Mission Briefing (Description)</label>
                    <textarea id="summernote" name="description" required></textarea>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-info w-100">DEPLOY JOB POST</button>
                    <a href="dashboard.php?page=careers" class="btn btn-outline-secondary w-100 mt-2">CANCEL</a>
                </div>
            </div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $('#summernote').summernote({
            placeholder: 'Detail the requirements and perks...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ]
        });
    </script>
</body>
</html>