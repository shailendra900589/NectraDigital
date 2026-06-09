<?php
namespace Growth\Models;

class LandingPage extends BaseModel
{
    public static function findBySlug(string $slug): ?array
    {
        return self::fetchOne(
            "SELECT lp.*, s.name AS service_name, s.url_prefix, s.service_image, s.schema_type,
                    c.name AS city_name, c.state, c.country, c.population, c.latitude, c.longitude, c.city_description
             FROM ge_landing_pages lp
             INNER JOIN ge_services s ON s.id = lp.service_id
             INNER JOIN ge_cities c ON c.id = lp.city_id
             WHERE lp.slug = ? AND lp.status = 'published' LIMIT 1",
            's', [$slug]
        );
    }

    public static function find(int $id): ?array
    {
        return self::fetchOne(
            "SELECT lp.*, s.name AS service_name, s.url_prefix, c.name AS city_name, c.state
             FROM ge_landing_pages lp
             INNER JOIN ge_services s ON s.id = lp.service_id
             INNER JOIN ge_cities c ON c.id = lp.city_id
             WHERE lp.id = ? LIMIT 1",
            'i', [$id]
        );
    }

    public static function exists(int $serviceId, int $cityId): bool
    {
        $row = self::fetchOne(
            "SELECT id FROM ge_landing_pages WHERE service_id = ? AND city_id = ? LIMIT 1",
            'ii', [$serviceId, $cityId]
        );
        return $row !== null;
    }

    public static function count(string $status = ''): int
    {
        $sql = "SELECT COUNT(*) AS c FROM ge_landing_pages";
        if ($status !== '') {
            $sql .= " WHERE status = '" . self::db()->real_escape_string($status) . "'";
        }
        $row = self::fetchOne($sql);
        return (int)($row['c'] ?? 0);
    }

    public static function paginated(int $page = 1, int $perPage = 25, array $filters = []): array
    {
        $where = ['1=1'];
        $types = '';
        $params = [];

        if (!empty($filters['service_id'])) {
            $where[] = 'lp.service_id = ?';
            $types .= 'i';
            $params[] = (int)$filters['service_id'];
        }
        if (!empty($filters['city_id'])) {
            $where[] = 'lp.city_id = ?';
            $types .= 'i';
            $params[] = (int)$filters['city_id'];
        }
        if (!empty($filters['index_status'])) {
            $where[] = 'lp.index_status = ?';
            $types .= 's';
            $params[] = $filters['index_status'];
        }

        $whereSql = implode(' AND ', $where);
        $countRow = self::fetchOne("SELECT COUNT(*) AS c FROM ge_landing_pages lp WHERE $whereSql", $types, $params);
        $total = (int)($countRow['c'] ?? 0);
        $pg = ge_paginate($total, $page, $perPage);

        $types .= 'ii';
        $params[] = $pg['per_page'];
        $params[] = $pg['offset'];

        $rows = self::fetchAll(
            "SELECT lp.id, lp.slug, lp.meta_title, lp.index_status, lp.is_indexed, lp.status, lp.generated_at,
                    s.name AS service_name, c.name AS city_name
             FROM ge_landing_pages lp
             INNER JOIN ge_services s ON s.id = lp.service_id
             INNER JOIN ge_cities c ON c.id = lp.city_id
             WHERE $whereSql
             ORDER BY lp.id DESC LIMIT ? OFFSET ?",
            $types, $params
        );

        return ['data' => $rows, 'pagination' => $pg];
    }

    public static function upsert(array $data): int
    {
        $existing = self::fetchOne(
            "SELECT id FROM ge_landing_pages WHERE service_id = ? AND city_id = ? LIMIT 1",
            'ii', [(int)$data['service_id'], (int)$data['city_id']]
        );

        if ($existing) {
            self::update((int)$existing['id'], $data);
            return (int)$existing['id'];
        }

        $sql = "INSERT INTO ge_landing_pages (service_id, city_id, slug, url_path, meta_title, meta_description, h1, h2, content, quick_answer, key_takeaways, summary, expert_insight, faq_json, schema_json, keywords_json, paa_json, voice_answer, internal_links_json, content_hash, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        self::execute($sql, 'iissssssssssssssssssss', [
            (int)$data['service_id'], (int)$data['city_id'], $data['slug'], $data['url_path'],
            $data['meta_title'], $data['meta_description'], $data['h1'], $data['h2'],
            $data['content'], $data['quick_answer'], $data['key_takeaways'], $data['summary'],
            $data['expert_insight'], $data['faq_json'], $data['schema_json'], $data['keywords_json'],
            $data['paa_json'], $data['voice_answer'], $data['internal_links_json'], $data['content_hash'],
            $data['status'] ?? 'published',
        ]);
        return self::lastId();
    }

    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE ge_landing_pages SET slug=?, url_path=?, meta_title=?, meta_description=?, h1=?, h2=?, content=?, quick_answer=?, key_takeaways=?, summary=?, expert_insight=?, faq_json=?, schema_json=?, keywords_json=?, paa_json=?, voice_answer=?, internal_links_json=?, content_hash=?, status=? WHERE id=?";
        return self::execute($sql, 'ssssssssssssssssssi', [
            $data['slug'], $data['url_path'], $data['meta_title'], $data['meta_description'],
            $data['h1'], $data['h2'], $data['content'], $data['quick_answer'], $data['key_takeaways'],
            $data['summary'], $data['expert_insight'], $data['faq_json'], $data['schema_json'],
            $data['keywords_json'], $data['paa_json'], $data['voice_answer'], $data['internal_links_json'],
            $data['content_hash'], $data['status'] ?? 'published', $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        return self::execute("DELETE FROM ge_landing_pages WHERE id = ?", 'i', [$id]);
    }

    public static function forSitemap(int $limit = 50000, int $offset = 0): array
    {
        return self::fetchAll(
            "SELECT slug, updated_at, generated_at FROM ge_landing_pages WHERE status = 'published' ORDER BY id ASC LIMIT ? OFFSET ?",
            'ii', [$limit, $offset]
        );
    }

    public static function indexStats(): array
    {
        return self::fetchOne(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN index_status = 'indexed' OR is_indexed = 1 THEN 1 ELSE 0 END) AS indexed,
                SUM(CASE WHEN index_status = 'pending' THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN index_status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                SUM(CASE WHEN index_status = 'failed' THEN 1 ELSE 0 END) AS failed
             FROM ge_landing_pages WHERE status = 'published'"
        ) ?? ['total' => 0, 'indexed' => 0, 'pending' => 0, 'submitted' => 0, 'failed' => 0];
    }
}
