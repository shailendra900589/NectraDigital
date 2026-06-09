<?php
namespace Growth\Models;

class Keyword extends BaseModel
{
    public static function all(int $limit = 500, int $offset = 0): array
    {
        return self::fetchAll(
            "SELECT k.*, s.name AS service_name, c.name AS city_name FROM ge_keywords k
             LEFT JOIN ge_services s ON s.id = k.service_id
             LEFT JOIN ge_cities c ON c.id = k.city_id
             ORDER BY k.id DESC LIMIT ? OFFSET ?",
            'ii', [$limit, $offset]
        );
    }

    public static function count(): int
    {
        $row = self::fetchOne("SELECT COUNT(*) AS c FROM ge_keywords");
        return (int)($row['c'] ?? 0);
    }

    public static function find(int $id): ?array
    {
        return self::fetchOne("SELECT * FROM ge_keywords WHERE id = ? LIMIT 1", 'i', [$id]);
    }

    public static function create(array $data): int
    {
        $sid = !empty($data['service_id']) ? (int)$data['service_id'] : 0;
        $cid = !empty($data['city_id']) ? (int)$data['city_id'] : 0;
        $sql = "INSERT INTO ge_keywords (keyword, keyword_type, service_id, city_id, is_auto_generated, status) VALUES (?, ?, NULLIF(?, 0), NULLIF(?, 0), ?, ?)";
        self::execute($sql, 'ssiiis', [
            $data['keyword'],
            $data['keyword_type'] ?? 'primary',
            $sid, $cid,
            (int)($data['is_auto_generated'] ?? 0),
            $data['status'] ?? 'active',
        ]);
        return self::lastId();
    }

    public static function delete(int $id): bool
    {
        return self::execute("DELETE FROM ge_keywords WHERE id = ?", 'i', [$id]);
    }

    public static function forLandingPage(int $landingPageId): array
    {
        return self::fetchAll(
            "SELECT k.* FROM ge_keywords k
             INNER JOIN ge_keyword_mappings m ON m.keyword_id = k.id
             WHERE m.landing_page_id = ? ORDER BY m.is_primary DESC, m.position ASC",
            'i', [$landingPageId]
        );
    }
}
