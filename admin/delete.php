<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
include '../includes/db.php';

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: dashboard.php");
    exit;
}

$id = intval($_GET['id']);
$type = $_GET['type'];

if ($type == 'blog') {
    $conn->query("DELETE FROM blog_posts WHERE id=$id");
    header("Location: dashboard.php?page=blog&msg=deleted");
} elseif ($type == 'career') {
    $conn->query("DELETE FROM careers WHERE id=$id");
    header("Location: dashboard.php?page=careers&msg=job_deleted");
} else {
    header("Location: dashboard.php");
}
exit;
?>
