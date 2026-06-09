<?php
require_once 'includes/db.php'; // DB Connection includes config settings
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. HONEYPOT CHECK (Hidden field that only bots fill)
    // आपको अपने HTML फॉर्म में यह लाइन जोड़नी होगी: <input type="text" name="website_url" style="display:none;">
    if (!empty($_POST['website_url'])) {
        die(json_encode(["status" => "error", "message" => "Bot Detected."]));
    }

    // 2. INPUT SANITIZATION
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $service = clean_input($_POST['service']);
    $budget = clean_input($_POST['budget']);
    $message = clean_input($_POST['message']);
    $ip = $_SERVER['REMOTE_ADDR'];

    // 3. SPAM DETECTION LOGIC
    $is_spam = 0;
    if (is_spam($message) || is_spam($name)) {
        $is_spam = 1;
        // Optional: Block completely
        // echo json_encode(["status" => "error", "message" => "Security Risk Detected."]); exit;
    }

    // 4. INSERT INTO DATABASE
    $sql = "INSERT INTO leads (name, email, service, budget, message, ip_address, is_spam) 
            VALUES ('$name', '$email', '$service', '$budget', '$message', '$ip', '$is_spam')";

    if ($conn->query($sql) === TRUE) {
        if($is_spam) {
             echo json_encode(["status" => "error", "message" => "System flagged message as spam."]);
        } else {
             echo json_encode(["status" => "success", "message" => "Transmission Received. We will contact you shortly."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Database Error."]);
    }
}
?>