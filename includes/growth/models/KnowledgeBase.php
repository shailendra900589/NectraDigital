<?php
namespace Growth\Models;

class KnowledgeBase extends BaseModel
{
    public static function all(string $status = ''): array
    {
        if (!ge_table_exists('ge_knowledge_base')) return [];
        $sql = "SELECT kb.*, a.name AS author_name FROM ge_knowledge_base kb LEFT JOIN ge_authors a ON a.id = kb.author_id";
        if ($status) {
            $sql .= " WHERE kb.status = '" . self::db()->real_escape_string($status) . "'";
        }
        $sql .= " ORDER BY kb.id DESC";
        return self::fetchAll($sql);
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::fetchOne(
            "SELECT kb.*, a.name AS author_name, a.slug AS author_slug FROM ge_knowledge_base kb
             LEFT JOIN ge_authors a ON a.id = kb.author_id
             WHERE kb.slug = ? AND kb.status = 'published' LIMIT 1",
            's', [$slug]
        );
    }

    public static function create(array $data): int
    {
        self::execute(
            "INSERT INTO ge_knowledge_base (title, slug, category, silo, content, quick_answer, author_id, pillar_id, meta_title, meta_description, faq_json, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            'sssssssiisss',
            [$data['title'], $data['slug'], $data['category'] ?? null, $data['silo'] ?? null,
             $data['content'] ?? null, $data['quick_answer'] ?? null,
             $data['author_id'] ?: null, $data['pillar_id'] ?: null,
             $data['meta_title'] ?? null, $data['meta_description'] ?? null,
             $data['faq_json'] ?? null, $data['status'] ?? 'draft']
        );
        return self::lastId();
    }

    public static function update(int $id, array $data): bool
    {
        return self::execute(
            "UPDATE ge_knowledge_base SET title=?, slug=?, category=?, silo=?, content=?, quick_answer=?, author_id=?, pillar_id=?, meta_title=?, meta_description=?, faq_json=?, status=? WHERE id=?",
            'ssssssiissssi',
            [$data['title'], $data['slug'], $data['category'] ?? null, $data['silo'] ?? null,
             $data['content'] ?? null, $data['quick_answer'] ?? null,
             $data['author_id'] ?: null, $data['pillar_id'] ?: null,
             $data['meta_title'] ?? null, $data['meta_description'] ?? null,
             $data['faq_json'] ?? null, $data['status'] ?? 'draft', $id]
        );
    }

    public static function delete(int $id): bool
    {
        return self::execute("DELETE FROM ge_knowledge_base WHERE id = ?", 'i', [$id]);
    }
}
