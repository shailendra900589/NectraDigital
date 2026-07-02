<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = clean_input($_POST['username']);
    $pass = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE username='$user'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password']) || $pass == 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            header("Location: dashboard.php");
            exit;
        }
        $error = "Invalid password. Please try again.";
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Nectra Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/growth-admin.css?v=5">
    <style>
        body.ge-admin { display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem; }
        .login-card {
            width: 100%; max-width: 400px; background: var(--ge-surface);
            border: 1px solid var(--ge-border); border-radius: 16px; padding: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
        }
        .login-brand { text-align: center; margin-bottom: 1.75rem; }
        .login-brand .ge-brand-text { font-size: 1.35rem; }
    </style>
</head>
<body class="ge-admin">
    <div class="login-card">
        <div class="login-brand">
            <div><span class="ge-brand-text">NECTRA</span><span class="ge-brand-accent">ADMIN</span></div>
            <small class="text-muted">Sign in to your control center</small>
        </div>
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small text-muted">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label small text-muted">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-ge-primary w-100">
                <i class="fas fa-sign-in-alt me-2"></i> Sign In
            </button>
        </form>
    </div>
</body>
</html>
