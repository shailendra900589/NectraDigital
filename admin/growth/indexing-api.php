<?php
/**
 * Live JSON API for Indexing Manager (stats polling + AJAX batch process).
 */
require_once __DIR__ . '/init.php';

use Growth\Models\IndexingQueue;
use Growth\Engines\IndexingEngine;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

function ge_indexing_clear_stats_cache(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        unset($_SESSION['ge_landing_index_stats'], $_SESSION['ge_landing_index_stats_at']);
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = (string)($_POST['action'] ?? $_GET['action'] ?? '');

        if ($action === 'process_batch') {
            @set_time_limit(120);
            ge_indexing_clear_stats_cache();
            $result = IndexingEngine::processWebBatch();
            echo json_encode([
                'ok' => true,
                'message' => 'Submitted ' . (int)($result['processed'] ?? 0) . ' URLs. Failed: ' . (int)($result['failed'] ?? 0),
                'result' => $result,
                'stats' => IndexingEngine::dashboardStats(true),
                'activity' => IndexingQueue::recentActivity(25),
            ], JSON_UNESCAPED_SLASHES);
            exit;
        }

        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Unknown action']);
        exit;
    }

    ge_indexing_clear_stats_cache();
    echo json_encode([
        'ok' => true,
        'stats' => IndexingEngine::dashboardStats(true),
        'activity' => IndexingQueue::recentActivity(25),
    ], JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
