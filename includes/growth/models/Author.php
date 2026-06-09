<?php
namespace Growth\Models;

class Author extends BaseModel
{
    public static function all(bool $activeOnly = false): array
    {
        $sql = "SELECT * FROM ge_authors" . ($activeOnly ? " WHERE status = 'active'" : "") . " ORDER BY is_founder DESC, name ASC";
        return ge_table_exists('ge_authors') ? self::fetchAll($sql) : [];
    }

    public static function find(int $id): ?array
    {
        return self::fetchOne("SELECT * FROM ge_authors WHERE id = ? LIMIT 1", 'i', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::fetchOne("SELECT * FROM ge_authors WHERE slug = ? LIMIT 1", 's', [$slug]);
    }

    public static function founder(): ?array
    {
        return self::fetchOne("SELECT * FROM ge_authors WHERE is_founder = 1 AND status = 'active' LIMIT 1");
    }

    public static function create(array $data): int
    {
        self::execute(
            "INSERT INTO ge_authors (name, slug, title, bio, expertise, avatar, linkedin, email, is_founder, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            'ssssssssis',
            [$data['name'], $data['slug'], $data['title'] ?? null, $data['bio'] ?? null,
             $data['expertise'] ?? null, $data['avatar'] ?? null, $data['linkedin'] ?? null,
             $data['email'] ?? null, (int)($data['is_founder'] ?? 0), $data['status'] ?? 'active']
        );
        return self::lastId();
    }

    public static function update(int $id, array $data): bool
    {
        return self::execute(
            "UPDATE ge_authors SET name=?, slug=?, title=?, bio=?, expertise=?, avatar=?, linkedin=?, email=?, is_founder=?, status=? WHERE id=?",
            'ssssssssisi',
            [$data['name'], $data['slug'], $data['title'] ?? null, $data['bio'] ?? null,
             $data['expertise'] ?? null, $data['avatar'] ?? null, $data['linkedin'] ?? null,
             $data['email'] ?? null, (int)($data['is_founder'] ?? 0), $data['status'] ?? 'active', $id]
        );
    }

    public static function delete(int $id): bool
    {
        return self::execute("DELETE FROM ge_authors WHERE id = ?", 'i', [$id]);
    }
}
