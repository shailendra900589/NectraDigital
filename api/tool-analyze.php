<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/growth/bootstrap.php';

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$url = trim($input['url'] ?? '');
$type = $input['type'] ?? 'seo_audit';

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid URL']);
    exit;
}

$result = \Growth\Engines\CompetitorEngine::analyze($url);
if (!($result['success'] ?? false)) {
    echo json_encode($result);
    exit;
}

$d = $result['data'];
echo json_encode([
    'success' => true,
    'type' => $type,
    'url' => $url,
    'title' => $d['meta_title'],
    'description' => mb_substr($d['meta_description'] ?? '', 0, 300),
    'h1' => ge_json_decode($d['h1_tags'] ?? '[]'),
    'schemas' => ge_json_decode($d['schemas_found'] ?? '[]'),
    'gaps' => ge_json_decode($d['content_gaps'] ?? '[]'),
    'keywords' => ge_json_decode($d['keywords_detected'] ?? '[]'),
]);
