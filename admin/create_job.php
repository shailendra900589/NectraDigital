<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $position = clean_input($_POST['position']);
    $stack = clean_input($_POST['stack']);
    $loc = clean_input($_POST['location']);
    $type = clean_input($_POST['type']);
    $experience = clean_input($_POST['experience']);
    $salary = clean_input($_POST['salary_range']);
    $openings = intval($_POST['openings']);
    $qualification = clean_input($_POST['qualification']);
    $desc = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO careers (position, stack, location, type, experience, salary_range, openings, qualification, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssis", $position, $stack, $loc, $type, $experience, $salary, $openings, $qualification, $desc);
    
    if ($stmt->execute()) {
        // Auto-send push notification
        $notif_payload = json_encode([
            'title' => 'New Job Opening: ' . $position,
            'body' => $type . ' | ' . $stack . ' | ' . ($salary ?: 'Competitive Salary'),
            'url' => '/nectradigital_final/careers',
            'icon' => '/nectradigital_final/assets/images/logo.png'
        ]);
        $subs = $conn->query("SELECT * FROM push_subscriptions");
        while($s = $subs->fetch_assoc()) {
            $ch = curl_init($s['endpoint']);
            curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $notif_payload, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'TTL: 86400'], CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5]);
            $hc = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_exec($ch); curl_close($ch);
            if($hc == 410 || $hc == 404) $conn->query("DELETE FROM push_subscriptions WHERE id=".intval($s['id']));
        }
        header("Location: dashboard.php?page=careers&msg=job_created");
        exit;
    } else {
        $error = "Failed to create job: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job | Nectra Admin</title>
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
            <h4 class="mb-0"><i class="fas fa-briefcase text-info me-2"></i>Create New Job Posting</h4>
            <a href="dashboard.php?page=careers" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>

        <?php if(isset($error)) echo "<div class='alert alert-danger py-2'>$error</div>"; ?>

        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Position Title *</label>
                    <input type="text" name="position" class="form-control" placeholder="e.g. Senior Full Stack Developer" required>
                </div>
                <div class="col-md-6">
                    <label>Tech Stack *</label>
                    <input type="text" name="stack" class="form-control" placeholder="e.g. React, Node.js, AWS" required>
                </div>
                <div class="col-md-4">
                    <label>Location *</label>
                    <input type="text" name="location" class="form-control" value="Remote / Lucknow" required>
                </div>
                <div class="col-md-4">
                    <label>Job Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="Remote">Remote</option>
                        <option value="On-site">On-site (Lucknow)</option>
                        <option value="Hybrid">Hybrid</option>
                        <option value="Freelance">Freelance / Contract</option>
                        <option value="Internship">Internship</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Experience Required</label>
                    <select name="experience" class="form-select">
                        <option value="Fresher">Fresher (0-1 year)</option>
                        <option value="1-2 years">1-2 years</option>
                        <option value="2-4 years" selected>2-4 years</option>
                        <option value="4-6 years">4-6 years</option>
                        <option value="6+ years">6+ years</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Salary Range (Monthly)</label>
                    <select name="salary_range" class="form-select">
                        <option value="">Not Disclosed</option>
                        <option value="10,000 - 20,000">₹10,000 - ₹20,000</option>
                        <option value="20,000 - 40,000">₹20,000 - ₹40,000</option>
                        <option value="40,000 - 70,000">₹40,000 - ₹70,000</option>
                        <option value="70,000 - 1,00,000">₹70,000 - ₹1,00,000</option>
                        <option value="1,00,000 - 1,50,000">₹1,00,000 - ₹1,50,000</option>
                        <option value="1,50,000+">₹1,50,000+</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Number of Openings</label>
                    <input type="number" name="openings" class="form-control" value="1" min="1" max="50">
                </div>
                <div class="col-md-4">
                    <label>Qualification</label>
                    <input type="text" name="qualification" class="form-control" placeholder="e.g. B.Tech, BCA, MCA, Any Graduate">
                </div>
                <div class="col-12">
                    <label>Job Description *</label>
                    <textarea id="summernote" name="description" required></textarea>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-cyan px-4 py-2"><i class="fas fa-plus-circle me-2"></i>Publish Job</button>
                    <a href="dashboard.php?page=careers" class="btn btn-outline-secondary ms-2 px-4 py-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $('#summernote').summernote({
            height: 300,
            placeholder: 'Describe responsibilities, requirements, perks...',
            toolbar: [
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ]
        });
    </script>
</body>
</html>
