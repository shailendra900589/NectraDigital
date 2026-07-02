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

    /** @return array{pending:int,completed:int,failed:int,total:int} */
    public static function stats(): array
    {
        $defaults = ['pending' => 0, 'completed' => 0, 'failed' => 0, 'total' => 0];
        if (!function_exists('ge_table_exists') || !ge_table_exists('ge_indexing_queue')) {
            return $defaults;
        }
        $row = self::fetchOne(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) AS failed
             FROM ge_indexing_queue"
        ) ?? $defaults;
        foreach (array_keys($defaults) as $k) {
            $defaults[$k] = (int)($row[$k] ?? 0);
        }
        return $defaults;
    }

    /** Recent queue rows for live activity feed. */
    public static function recentActivity(int $limit = 25): array
    {
        if (!function_exists('ge_table_exists') || !ge_table_exists('ge_indexing_queue')) {
            return [];
        }
        $limit = max(1, min(100, $limit));
        return self::fetchAll(
            "SELECT id, url, status, created_at, processed_at
             FROM ge_indexing_queue
             ORDER BY COALESCE(processed_at, created_at) DESC, id DESC
             LIMIT ?",
            'i',
            [$limit]
        );
    }

    public static function lastProcessedAt(): ?string
    {
        if (!function_exists('ge_table_exists') || !ge_table_exists('ge_indexing_queue')) {
            return null;
        }
        $row = self::fetchOne(
            "SELECT processed_at FROM ge_indexing_queue
             WHERE processed_at IS NOT NULL
             ORDER BY processed_at DESC LIMIT 1"
        );
        return $row['processed_at'] ?? null;
    }
}
