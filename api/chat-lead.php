<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\Models\CrmLead;

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$message = trim($input['message'] ?? '');
$service = trim($input['service'] ?? 'Chatbot Inquiry');
$phone = trim($input['phone'] ?? '');

if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Name and valid email required']);
    exit;
}

if (ge_table_exists('ge_crm_leads')) {
    $id = CrmLead::create([
        'name' => $name,
        'email' => $email,
        'phone' => $phone ?: null,
        'service_interest' => $service,
        'message' => $message,
        'source' => 'chatbot',
        'meta_json' => ge_json_encode(['page' => $_SERVER['HTTP_REFERER'] ?? '']),
    ]);
    if (ge_table_exists('ge_analytics_events')) {
        $db = ge_conn();
        $evt = 'lead_chatbot';
        $pg = $_SERVER['HTTP_REFERER'] ?? '';
        $stmt = $db->prepare("INSERT INTO ge_analytics_events (event_type, page_url, metadata) VALUES (?, ?, ?)");
        $meta = ge_json_encode(['lead_id' => $id]);
        $stmt->bind_param('sss', $evt, $pg, $meta);
        $stmt->execute();
    }
    echo json_encode(['success' => true, 'lead_id' => $id, 'reply' => 'Thank you! Our team will contact you within 24 hours.']);
    exit;
}

echo json_encode(['success' => true, 'reply' => 'Thank you! We received your message.']);
