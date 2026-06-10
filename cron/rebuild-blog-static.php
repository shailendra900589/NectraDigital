<?php
/**
 * Cron: rebuild static HTML snapshots for all blog posts (Bing/Google crawler bypass).
 */
require_once __DIR__ . '/../includes/growth/bootstrap.php';
require_once __DIR__ . '/../includes/blog_static.php';

ge_cron_auth_or_exit();

header('Content-Type: application/json; charset=utf-8');
$built = blog_static_rebuild_all($conn);
echo json_encode(['ok' => true, 'built' => $built], JSON_UNESCAPED_UNICODE);
