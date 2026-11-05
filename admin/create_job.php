<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) exit;
require_once '../includes/db.php';
require_once '../includes/ckeditor.php';

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = clean_input($_POST['position']);
    $stack = clean_input($_POST['stack']);
    $loc = clean_input($_POST['location']);
    $desc = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO careers (position, stack, location, description) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        $error = 'Database error: ' . ($conn->error ?: 'Could not prepare statement. Check careers table exists.');
    } else {
        $stmt->bind_param("ssss", $title, $stack, $loc, $desc);
        if ($stmt->execute()) {
            header("Location: dashboard.php?page=careers");
            exit;
        }
        $error = 'Could not save job: ' . ($stmt->error ?: 'Insert failed');
    }
}

require_once __DIR__ . '/includes/admin-layout.php';
admin_layout_start('Post New Job', 'careers', ['ckeditor' => true]);
?>
        <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <div class="ge-card ge-editor-form">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Position title</label>
                    <input type="text" name="position" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tech stack</label>
                    <input type="text" name="stack" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="Remote / Global" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Job description</label>
                    <textarea name="description" class="ckeditor-full" required></textarea>
                </div>
            </div>
            <div class="ge-form-actions">
                <button type="submit" class="btn btn-ge-primary"><i class="fas fa-briefcase me-2"></i>Publish job</button>
                <a href="dashboard.php?page=careers" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
        </div>
<?php
admin_layout_end(['ckeditor' => true]);
