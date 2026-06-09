<?php
session_start();
include '../includes/db.php';
if (!isset($_SESSION['admin_logged_in'])) exit;

$id = intval($_GET['id']);
$type = $_GET['type'];

if($type == 'blog') {
    $conn->query("DELETE FROM blog_posts WHERE id=$id");
} elseif ($type == 'career') {
    $conn->query("DELETE FROM careers WHERE id=$id");
}
header("Location: dashboard.php?page=$type");
?>