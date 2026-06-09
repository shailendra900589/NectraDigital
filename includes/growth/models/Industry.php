<?php
namespace Growth\Models;

class Industry extends BaseModel
{
    public static function all(bool $activeOnly = false): array
    {
        $sql = "SELECT * FROM ge_industries" . ($activeOnly ? " WHERE status = 'active'" : "") . " ORDER BY name ASC";
        return self::fetchAll($sql);
    }

    public static function find(int $id): ?array
    {
        return self::fetchOne("SELECT * FROM ge_industries WHERE id = ? LIMIT 1", 'i', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::fetchOne("SELECT * FROM ge_industries WHERE slug = ? LIMIT 1", 's', [$slug]);
    }

    public static function count(bool $activeOnly = false): int
    {
        $sql = "SELECT COUNT(*) AS c FROM ge_industries" . ($activeOnly ? " WHERE status = 'active'" : "");
        return (int)(self::fetchOne($sql)['c'] ?? 0);
    }

    public static function create(array $data): int
    {
        self::execute(
            "INSERT INTO ge_industries (name, slug, description, icon, meta_title_template, meta_description_template, status) VALUES (?, ?, ?, ?, ?, ?, ?)",
            'sssssss',
            [$data['name'], $data['slug'], $data['description'] ?? null, $data['icon'] ?? 'fa-industry',
             $data['meta_title_template'] ?? null, $data['meta_description_template'] ?? null, $data['status'] ?? 'active']
        );
        return self::lastId();
    }

    public static function update(int $id, array $data): bool
    {
        return self::execute(
            "UPDATE ge_industries SET name=?, slug=?, description=?, icon=?, meta_title_template=?, meta_description_template=?, status=? WHERE id=?",
            'sssssssi',
            [$data['name'], $data['slug'], $data['description'] ?? null, $data['icon'] ?? 'fa-industry',
             $data['meta_title_template'] ?? null, $data['meta_description_template'] ?? null, $data['status'] ?? 'active', $id]
        );
    }

    public static function delete(int $id): bool
    {
        return self::execute("DELETE FROM ge_industries WHERE id = ?", 'i', [$id]);
    }
}
