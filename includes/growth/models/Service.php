<?php
namespace Growth\Models;

class Service extends BaseModel
{
    public static function all(bool $activeOnly = false): array
    {
        $sql = "SELECT * FROM ge_services" . ($activeOnly ? " WHERE status = 'active'" : "") . " ORDER BY sort_order ASC, name ASC";
        return self::fetchAll($sql);
    }

    public static function find(int $id): ?array
    {
        return self::fetchOne("SELECT * FROM ge_services WHERE id = ? LIMIT 1", 'i', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::fetchOne("SELECT * FROM ge_services WHERE slug = ? LIMIT 1", 's', [$slug]);
    }

    public static function count(bool $activeOnly = false): int
    {
        $sql = "SELECT COUNT(*) AS c FROM ge_services" . ($activeOnly ? " WHERE status = 'active'" : "");
        $row = self::fetchOne($sql);
        return (int)($row['c'] ?? 0);
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO ge_services (name, slug, url_prefix, meta_title_template, meta_description_template, h1_template, h2_template, content_template, service_image, faq_template, keywords_template, schema_type, sort_order, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        self::execute($sql, 'ssssssssssssis', [
            $data['name'], $data['slug'], $data['url_prefix'],
            $data['meta_title_template'] ?? null,
            $data['meta_description_template'] ?? null,
            $data['h1_template'] ?? null,
            $data['h2_template'] ?? null,
            $data['content_template'] ?? null,
            $data['service_image'] ?? null,
            $data['faq_template'] ?? null,
            $data['keywords_template'] ?? null,
            $data['schema_type'] ?? 'Service',
            (int)($data['sort_order'] ?? 0),
            $data['status'] ?? 'active',
        ]);
        return self::lastId();
    }

    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE ge_services SET name=?, slug=?, url_prefix=?, meta_title_template=?, meta_description_template=?, h1_template=?, h2_template=?, content_template=?, service_image=?, faq_template=?, keywords_template=?, schema_type=?, sort_order=?, status=? WHERE id=?";
        return self::execute($sql, 'ssssssssssssisi', [
            $data['name'], $data['slug'], $data['url_prefix'],
            $data['meta_title_template'] ?? null,
            $data['meta_description_template'] ?? null,
            $data['h1_template'] ?? null,
            $data['h2_template'] ?? null,
            $data['content_template'] ?? null,
            $data['service_image'] ?? null,
            $data['faq_template'] ?? null,
            $data['keywords_template'] ?? null,
            $data['schema_type'] ?? 'Service',
            (int)($data['sort_order'] ?? 0),
            $data['status'] ?? 'active',
            $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        return self::execute("DELETE FROM ge_services WHERE id = ?", 'i', [$id]);
    }
}
