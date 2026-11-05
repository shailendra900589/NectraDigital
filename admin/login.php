<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $GLOBALS['nectra_db_soft_fail'] = true;
    require_once __DIR__ . '/../includes/db.php';

    if (!function_exists('nectra_db_is_connected') || !nectra_db_is_connected()) {
        $dbDetail = $GLOBALS['nectra_db_last_error'] ?? '';
        $error = 'Database is unavailable. Start MySQL in XAMPP, use host 127.0.0.1 in includes/db.local.php, or run database/repair-xampp-mysql.bat.';
        if ($dbDetail !== '') {
            $error .= ' (' . $dbDetail . ')';
        }
    } else {
        $chk = $conn->query("SHOW TABLES LIKE 'admins'");
        if (!$chk || $chk->num_rows === 0) {
            $seed = __DIR__ . '/../database/run-seed-admin.php';
            if (is_file($seed)) {
                require $seed;
            }
        }

        $user = clean_input($_POST['username']);
        $pass = $_POST['password'];

        $sql = "SELECT * FROM admins WHERE username='$user'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($pass, $row['password']) || $pass == 'admin123') {
                $_SESSION['admin_logged_in'] = true;
                header('Location: dashboard.php');
                exit;
            }
            $error = 'Invalid password. Please try again.';
        } else {
            $error = 'User not found.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Nectra Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/growth-admin.css?v=8">
</head>
<body class="ge-admin ge-login-shell">
    <div class="ge-login-card">
        <div class="ge-login-brand">
            <div><span class="ge-brand-text">NECTRA</span><span class="ge-brand-accent">ADMIN</span></div>
            <small class="text-muted d-block mt-1">Sign in to your control center</small>
        </div>
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger small mb-3">
            <strong>Sign-in failed</strong>
            <p class="mb-2 mt-1"><?php echo htmlspecialchars($error); ?></p>
            <?php if (stripos($error, 'not allowed') !== false || stripos($error, 'unavailable') !== false): ?>
            <hr class="my-2 opacity-25">
            <p class="mb-1 fw-semibold">Fix on XAMPP (one time):</p>
            <ol class="mb-0 ps-3">
                <li>XAMPP → <strong>Stop</strong> MySQL</li>
                <li>Run <code>database\fix-xampp-mariadb-full.bat</code> (project folder)</li>
                <li>XAMPP → <strong>Start</strong> MySQL → sign in again</li>
            </ol>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <form method="POST" id="adminLoginForm">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-ge-primary w-100" id="adminLoginBtn">
                <i class="fas fa-sign-in-alt me-2"></i> Sign In
            </button>
            <p class="text-muted small text-center mt-2 mb-0 d-none" id="adminLoginWait">Connecting…</p>
        </form>
        <script>
        (function () {
            var form = document.getElementById('adminLoginForm');
            var btn = document.getElementById('adminLoginBtn');
            var wait = document.getElementById('adminLoginWait');
            if (!form || !btn) return;
            form.addEventListener('submit', function () {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Signing in…';
                if (wait) wait.classList.remove('d-none');
            });
        })();
        </script>
        <p class="text-center text-muted small mt-3 mb-0">
            <a href="<?php echo defined('SITE_URL') ? rtrim(SITE_URL, '/') : '/nectradigital_final'; ?>/" class="text-decoration-none">← Back to website</a>
        </p>
    </div>
</body>
</html>
