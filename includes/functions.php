<?php
require_once 'includes/config.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // CSRF Check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(["status" => "error", "message" => "Security Alert: Invalid Token."]);
        exit;
    }

    // Sanitize Input
    $name = strip_tags(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $service = strip_tags(trim($_POST['service']));

    // Validation
    if (empty($name) || empty($email) || empty($service)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    // (Optional) Send Email here using mail() function
    // mail("contact@nectradigital.com", "New Lead: $name", "Service: $service");

    echo json_encode(["status" => "success", "message" => "Request Initialized. We will contact you shortly."]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid Request."]);
}
?>