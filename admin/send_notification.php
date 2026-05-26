<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
include '../includes/db.php';

$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $url = trim($_POST['url']);
    $image = trim($_POST['image']);
    
    if (empty($title) || empty($body)) {
        $error = "Title and body are required.";
    } else {
        $result = $conn->query("SELECT * FROM push_subscriptions");
        $sent = 0;
        $failed = 0;
        
        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url ?: '/nectradigital_final/',
            'image' => $image,
            'icon' => '/nectradigital_final/assets/images/logo.png'
        ]);
        
        while ($sub = $result->fetch_assoc()) {
            $ch = curl_init($sub['endpoint']);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'TTL: 86400'
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                $sent++;
            } else {
                $failed++;
                if ($httpCode == 410 || $httpCode == 404) {
                    $conn->query("DELETE FROM push_subscriptions WHERE id=" . intval($sub['id']));
                }
            }
        }
        
        $msg = "Notification sent to $sent subscribers. ($failed failed)";
    }
}

$sub_count = $conn->query("SELECT COUNT(*) as c FROM push_subscriptions")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notification | Nectra Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #0d0d0d; color: #fff; font-family: 'Inter', sans-serif; }
        .form-control { background-color: #1a1a1a; border-color: #333; color: #fff; border-radius: 8px; }
        .form-control:focus { background-color: #222; color: #fff; border-color: #00E5FF; box-shadow: 0 0 0 2px rgba(0,229,255,0.1); }
        .btn-cyan { background: #00E5FF; color: #000; font-weight: 600; border: none; }
        .btn-cyan:hover { background: #00bcd4; color: #000; }
        label { font-size: 0.85rem; color: #999; margin-bottom: 4px; }
    </style>
</head>
<body>
    <div class="container py-4" style="max-width: 600px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-bell text-info me-2"></i>Push Notifications</h4>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>

        <div class="alert alert-dark border-secondary mb-4">
            <i class="fas fa-users me-2 text-info"></i> <strong><?php echo $sub_count; ?></strong> active subscribers
        </div>

        <?php if($msg): ?><div class="alert alert-success py-2"><?php echo $msg; ?></div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-danger py-2"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Notification Title *</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. New Blog Post Published!" required>
            </div>
            <div class="mb-3">
                <label>Body Text *</label>
                <textarea name="body" class="form-control" rows="3" placeholder="Short description of the update..." required></textarea>
            </div>
            <div class="mb-3">
                <label>Click URL (optional)</label>
                <input type="text" name="url" class="form-control" placeholder="/nectradigital_final/insights">
            </div>
            <div class="mb-3">
                <label>Image URL (optional)</label>
                <input type="text" name="image" class="form-control" placeholder="Full image URL for rich notification">
            </div>
            <button type="submit" class="btn btn-cyan w-100 py-2"><i class="fas fa-paper-plane me-2"></i>Send to All Subscribers</button>
        </form>
    </div>
</body>
</html>
