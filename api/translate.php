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
require_once __DIR__ . '/../includes/i18n.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$target = trim($input['target'] ?? '');
$source = trim($input['source'] ?? 'en');
$supported = nectra_supported_languages();

if (!isset($supported[$target])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Unsupported target language']);
    exit;
}

if (!nectra_translate_api_enabled()) {
    http_response_code(503);
    echo json_encode([
        'success' => false,
        'error'   => 'Translation API not configured. Using on-page Google Translate widget.',
        'fallback'=> true,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$texts = [];
if (!empty($input['texts']) && is_array($input['texts'])) {
    $texts = array_slice(array_map('strval', $input['texts']), 0, 50);
} elseif (!empty($input['text'])) {
    $texts = [substr((string)$input['text'], 0, 5000)];
}

if (empty($texts)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No text provided']);
    exit;
}

$translated = nectra_translate_text($texts, $target, $source);
if ($translated === null) {
    http_response_code(502);
    echo json_encode(['success' => false, 'error' => 'Translation service unavailable']);
    exit;
}

echo json_encode([
    'success'     => true,
    'target'      => $target,
    'source'      => $source,
    'translations'=> is_array($translated) ? $translated : [$translated],
], JSON_UNESCAPED_UNICODE);
