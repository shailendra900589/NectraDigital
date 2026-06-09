<?php
namespace Growth\Models;

class Tool extends BaseModel
{
    public static function all(bool $activeOnly = true): array
    {
        if (!ge_table_exists('ge_tools')) return [];
        $sql = "SELECT * FROM ge_tools" . ($activeOnly ? " WHERE status = 'active'" : "") . " ORDER BY name ASC";
        return self::fetchAll($sql);
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::fetchOne("SELECT * FROM ge_tools WHERE slug = ? LIMIT 1", 's', [$slug]);
    }

    public static function incrementUsage(int $id): void
    {
        self::execute("UPDATE ge_tools SET usage_count = usage_count + 1 WHERE id = ?", 'i', [$id]);
    }
}
