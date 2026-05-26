<?php
header('Content-Type: application/json');
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'POST required']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['endpoint']) || empty($input['keys']['p256dh']) || empty($input['keys']['auth'])) {
    echo json_encode(['error' => 'Invalid subscription data']);
    exit;
}

$endpoint = $input['endpoint'];
$p256dh = $input['keys']['p256dh'];
$auth = $input['keys']['auth'];

$stmt = $conn->prepare("INSERT INTO push_subscriptions (endpoint, p256dh, auth) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE p256dh=VALUES(p256dh), auth=VALUES(auth)");
$stmt->bind_param("sss", $endpoint, $p256dh, $auth);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Subscribed']);
} else {
    echo json_encode(['error' => 'Database error']);
}
?>
