<?php
namespace Growth\Models;

class GenerationJob extends BaseModel
{
    public static function create(string $type, int $total, ?int $serviceId = null, ?int $cityId = null): int
    {
        self::execute(
            "INSERT INTO ge_generation_jobs (job_type, service_id, city_id, total_pages, status) VALUES (?, ?, ?, ?, 'queued')",
            'siii', [$type, $serviceId, $cityId, $total]
        );
        return self::lastId();
    }

    public static function start(int $id): bool
    {
        return self::execute("UPDATE ge_generation_jobs SET status = 'running', started_at = NOW() WHERE id = ?", 'i', [$id]);
    }

    public static function progress(int $id, int $processed, int $failed = 0): bool
    {
        return self::execute("UPDATE ge_generation_jobs SET processed = ?, failed = ? WHERE id = ?", 'iii', [$processed, $failed, $id]);
    }

    public static function complete(int $id, string $status = 'completed'): bool
    {
        return self::execute("UPDATE ge_generation_jobs SET status = ?, completed_at = NOW() WHERE id = ?", 'si', [$status, $id]);
    }

    public static function recent(int $limit = 10): array
    {
        return self::fetchAll("SELECT * FROM ge_generation_jobs ORDER BY id DESC LIMIT ?", 'i', [$limit]);
    }

    public static function find(int $id): ?array
    {
        return self::fetchOne("SELECT * FROM ge_generation_jobs WHERE id = ? LIMIT 1", 'i', [$id]);
    }
}
