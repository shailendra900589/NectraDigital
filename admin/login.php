<?php
session_start();
include '../includes/db.php';

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $row['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "Account not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Nectra Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            background: #0a0a0a;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            margin: 0;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 40px 30px;
            background: rgba(20, 20, 25, 0.95);
            border: 1px solid rgba(0, 229, 255, 0.15);
            border-radius: 16px;
            box-shadow: 0 0 40px rgba(0, 229, 255, 0.05);
        }
        .login-card h3 {
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
        }
        .form-control {
            background: rgba(30, 35, 40, 0.9);
            border: 1px solid #333;
            color: #fff;
            padding: 12px 16px;
            border-radius: 8px;
        }
        .form-control:focus {
            background: rgba(25, 30, 35, 1);
            border-color: #00E5FF;
            box-shadow: 0 0 0 3px rgba(0, 229, 255, 0.1);
            color: #fff;
        }
        .btn-login {
            background: #00E5FF;
            color: #000;
            font-weight: 700;
            padding: 12px;
            border-radius: 8px;
            border: none;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: #00bcd4;
            color: #000;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 229, 255, 0.3);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h3><span class="text-white">NECTRA</span><span style="color: #00E5FF;">DIGITAL</span></h3>
            <p class="text-white-50 small mb-0">Admin Control Panel</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger py-2 text-center small"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-white-50 small">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-white-50"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label text-white-50 small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-white-50"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-login w-100">
                <i class="fas fa-sign-in-alt me-2"></i> LOGIN
            </button>
        </form>
        
        <p class="text-center text-white-50 mt-4 mb-0" style="font-size: 11px;">
            <i class="fas fa-shield-alt me-1"></i> Secured Access Only
        </p>
    </div>
</body>
</html>
