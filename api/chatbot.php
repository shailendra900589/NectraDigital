<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/chatbot-engine.php';

if (file_exists(__DIR__ . '/../includes/growth/bootstrap.php')) {
    require_once __DIR__ . '/../includes/growth/bootstrap.php';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$message = trim($input['message'] ?? '');
$action = trim($input['action'] ?? 'chat');

if ($action === 'reset') {
    $_SESSION['nectra_chatbot'] = ['mode' => 'idle', 'lead' => []];
    echo json_encode([
        'success' => true,
        'reply' => nectra_chat_welcome(),
        'quick_replies' => ['Services', 'Pricing', 'Contact', 'Free Audit'],
        'state' => $_SESSION['nectra_chatbot'],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'welcome') {
    echo json_encode([
        'success' => true,
        'reply' => nectra_chat_welcome(),
        'quick_replies' => ['Services', 'Pricing', 'Contact', 'Free Audit'],
        'state' => $_SESSION['nectra_chatbot'] ?? ['mode' => 'idle', 'lead' => []],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION['nectra_chatbot'])) {
    $_SESSION['nectra_chatbot'] = ['mode' => 'idle', 'lead' => []];
}

$state = &$_SESSION['nectra_chatbot'];
$result = nectra_chat_process($message, $state);
$_SESSION['nectra_chatbot'] = $state;

echo json_encode(array_merge(['success' => true], $result), JSON_UNESCAPED_UNICODE);
