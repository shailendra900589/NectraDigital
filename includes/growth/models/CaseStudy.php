<?php
namespace Growth\Models;

class CaseStudy extends BaseModel
{
    public static function all(bool $publishedOnly = false): array
    {
        $sql = "SELECT cs.*, s.name AS service_name FROM ge_case_studies cs
                LEFT JOIN ge_services s ON s.id = cs.service_id";
        if ($publishedOnly) $sql .= " WHERE cs.status = 'published'";
        $sql .= " ORDER BY cs.created_at DESC";
        return self::fetchAll($sql);
    }

    public static function find(int $id): ?array
    {
        return self::fetchOne("SELECT * FROM ge_case_studies WHERE id = ? LIMIT 1", 'i', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::fetchOne(
            "SELECT cs.*, s.name AS service_name FROM ge_case_studies cs
             LEFT JOIN ge_services s ON s.id = cs.service_id
             WHERE cs.slug = ? AND cs.status = 'published' LIMIT 1",
            's', [$slug]
        );
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO ge_case_studies (title, slug, service_id, category, client_name, client_industry, results_summary, content, image, meta_title, meta_description, faq_json, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        self::execute($sql, 'ssissssssssss', [
            $data['title'], $data['slug'],
            !empty($data['service_id']) ? (int)$data['service_id'] : null,
            $data['category'] ?? null, $data['client_name'] ?? null, $data['client_industry'] ?? null,
            $data['results_summary'] ?? null, $data['content'] ?? null, $data['image'] ?? null,
            $data['meta_title'] ?? null, $data['meta_description'] ?? null,
            $data['faq_json'] ?? null, $data['status'] ?? 'draft',
        ]);
        return self::lastId();
    }

    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE ge_case_studies SET title=?, slug=?, service_id=?, category=?, client_name=?, client_industry=?, results_summary=?, content=?, image=?, meta_title=?, meta_description=?, faq_json=?, status=? WHERE id=?";
        return self::execute($sql, 'ssissssssssssi', [
            $data['title'], $data['slug'],
            !empty($data['service_id']) ? (int)$data['service_id'] : null,
            $data['category'] ?? null, $data['client_name'] ?? null, $data['client_industry'] ?? null,
            $data['results_summary'] ?? null, $data['content'] ?? null, $data['image'] ?? null,
            $data['meta_title'] ?? null, $data['meta_description'] ?? null,
            $data['faq_json'] ?? null, $data['status'] ?? 'draft', $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        return self::execute("DELETE FROM ge_case_studies WHERE id = ?", 'i', [$id]);
    }
}
