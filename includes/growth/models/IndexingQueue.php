<?php
namespace Growth\Models;

class IndexingQueue extends BaseModel
{
    public static function enqueue(string $url, ?int $landingPageId = null, string $action = 'submit'): int
    {
        if (self::enqueueUnique($url, $landingPageId, $action)) {
            return self::lastId();
        }
        return 0;
    }

    public static function enqueueUnique(string $url, ?int $landingPageId = null, string $action = 'submit'): bool
    {
        $existing = self::fetchOne(
            "SELECT id FROM ge_indexing_queue WHERE url = ? AND status = 'pending' LIMIT 1",
            's',
            [$url]
        );
        if ($existing) {
            return false;
        }

        self::execute(
            "INSERT INTO ge_indexing_queue (landing_page_id, url, action_type, status) VALUES (?, ?, ?, 'pending')",
            'iss',
            [$landingPageId ?? 0, $url, $action]
        );
        return true;
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
        if (!function_exists('ge_table_exists') || !ge_table_exists('ge_indexing_queue')) {
            return [];
        }

        if (function_exists('ge_table_exists') && ge_table_exists('ge_landing_pages')) {
            return self::fetchAll(
                "SELECT q.*, lp.slug FROM ge_indexing_queue q
                 LEFT JOIN ge_landing_pages lp ON lp.id = q.landing_page_id
                 ORDER BY q.id DESC LIMIT ?",
                'i', [$limit]
            );
        }

        return self::fetchAll(
            "SELECT * FROM ge_indexing_queue ORDER BY id DESC LIMIT ?",
            'i', [$limit]
        );
    }
}
