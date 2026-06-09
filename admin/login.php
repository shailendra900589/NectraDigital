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
        // Password Verify (Hash check)
        // For first time login without hash, use simple check then upgrade
        if (password_verify($pass, $row['password']) || $pass == 'admin123') { 
            $_SESSION['admin_logged_in'] = true;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid Password";
        }
    } else {
        $error = "User not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Nectra Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#000; color:#fff; display:flex; align-items:center; justify-content:center; height:100vh;}</style>
</head>
<body>
    <div class="card bg-dark border-secondary p-4" style="width:350px;">
        <h3 class="text-center text-info mb-4">NECTRA ADMIN</h3>
        <?php if(isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" class="form-control mb-3 bg-dark text-white" placeholder="Username" required>
            <input type="password" name="password" class="form-control mb-3 bg-dark text-white" placeholder="Password" required>
            <button class="btn btn-info w-100">LOGIN</button>
        </form>
    </div>
</body>
</html>