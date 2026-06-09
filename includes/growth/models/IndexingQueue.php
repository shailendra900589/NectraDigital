<?php
namespace Growth\Models;

class IndexingQueue extends BaseModel
{
    public static function enqueue(string $url, ?int $landingPageId = null, string $action = 'submit'): int
    {
        self::execute(
            "INSERT INTO ge_indexing_queue (landing_page_id, url, action_type, status) VALUES (?, ?, ?, 'pending')",
            'iss', [$landingPageId, $url, $action]
        );
        return self::lastId();
    }

    public static function pending(int $limit = 100): array
    {
        return self::fetchAll(
            "SELECT * FROM ge_indexing_queue WHERE status = 'pending' ORDER BY id ASC LIMIT ?",
            'i', [$limit]
        );
    }

    public static function markProcessed(int $id, string $status, ?string $response = null): bool
    {
        return self::execute(
            "UPDATE ge_indexing_queue SET status = ?, response = ?, processed_at = NOW() WHERE id = ?",
            'ssi', [$status, $response, $id]
        );
    }

    public static function all(int $limit = 50): array
    {
        return self::fetchAll(
            "SELECT q.*, lp.slug FROM ge_indexing_queue q
             LEFT JOIN ge_landing_pages lp ON lp.id = q.landing_page_id
             ORDER BY q.id DESC LIMIT ?",
            'i', [$limit]
        );
    }
}
