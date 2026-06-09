<?php
namespace Growth\Models;

class LandingPage extends BaseModel
{
    private static function industryJoin(): string
    {
        return ge_table_exists('ge_industries')
            ? " LEFT JOIN ge_industries ind ON ind.id = lp.industry_id AND lp.industry_id > 0"
            : "";
    }

    public static function findBySlug(string $slug): ?array
    {
        $join = self::industryJoin();
        $cols = ge_table_exists('ge_industries') ? ", ind.name AS industry_name, ind.slug AS industry_slug" : ", NULL AS industry_name, NULL AS industry_slug";
        return self::fetchOne(
            "             SELECT lp.*, s.name AS service_name, s.slug AS service_slug_ge, s.url_prefix, s.service_image, s.schema_type,
                    c.name AS city_name, c.slug AS city_slug, c.state, c.country, c.population, c.latitude, c.longitude, c.city_description
                    {$cols}
             FROM ge_landing_pages lp
             INNER JOIN ge_services s ON s.id = lp.service_id
             INNER JOIN ge_cities c ON c.id = lp.city_id
             {$join}
             WHERE lp.slug = ? AND lp.status = 'published' LIMIT 1",
            's', [$slug]
        );
    }

    public static function find(int $id): ?array
    {
        $join = self::industryJoin();
        $cols = ge_table_exists('ge_industries') ? ", ind.name AS industry_name" : ", NULL AS industry_name";
        return self::fetchOne(
            "SELECT lp.*, s.name AS service_name, s.url_prefix, c.name AS city_name, c.state{$cols}
             FROM ge_landing_pages lp
             INNER JOIN ge_services s ON s.id = lp.service_id
             INNER JOIN ge_cities c ON c.id = lp.city_id
             {$join}
             WHERE lp.id = ? LIMIT 1",
            'i', [$id]
        );
    }

    public static function exists(int $serviceId, int $cityId, int $industryId = 0): bool
    {
        $row = self::fetchOne(
            "SELECT id FROM ge_landing_pages WHERE service_id = ? AND city_id = ? AND industry_id = ? LIMIT 1",
            'iii', [$serviceId, $cityId, $industryId]
        );
        return $row !== null;
    }

    public static function count(string $status = ''): int
    {
        $sql = "SELECT COUNT(*) AS c FROM ge_landing_pages";
        if ($status !== '') {
            $sql .= " WHERE status = '" . self::db()->real_escape_string($status) . "'";
        }
        return (int)(self::fetchOne($sql)['c'] ?? 0);
    }

    public static function paginated(int $page = 1, int $perPage = 25, array $filters = []): array
    {
        $where = ['1=1'];
        $types = '';
        $params = [];

        foreach (['service_id', 'city_id', 'industry_id'] as $f) {
            if (!empty($filters[$f])) {
                $where[] = "lp.{$f} = ?";
                $types .= 'i';
                $params[] = (int)$filters[$f];
            }
        }
        if (!empty($filters['index_status'])) {
            $where[] = 'lp.index_status = ?';
            $types .= 's';
            $params[] = $filters['index_status'];
        }

        $whereSql = implode(' AND ', $where);
        $join = self::industryJoin();
        $indCol = ge_table_exists('ge_industries') ? ", ind.name AS industry_name" : ", NULL AS industry_name";

        $total = (int)(self::fetchOne("SELECT COUNT(*) AS c FROM ge_landing_pages lp WHERE $whereSql", $types, $params)['c'] ?? 0);
        $pg = ge_paginate($total, $page, $perPage);

        $types .= 'ii';
        $params[] = $pg['per_page'];
        $params[] = $pg['offset'];

        $rows = self::fetchAll(
            "SELECT lp.id, lp.slug, lp.service_id, lp.meta_title, lp.index_status, lp.is_indexed, lp.status, lp.page_type, lp.generated_at,
                    s.name AS service_name, c.name AS city_name{$indCol}
             FROM ge_landing_pages lp
             INNER JOIN ge_services s ON s.id = lp.service_id
             INNER JOIN ge_cities c ON c.id = lp.city_id
             {$join}
             WHERE $whereSql
             ORDER BY lp.id DESC LIMIT ? OFFSET ?",
            $types, $params
        );

        return ['data' => $rows, 'pagination' => $pg];
    }

    public static function upsert(array $data): int
    {
        $industryId = (int)($data['industry_id'] ?? 0);
        $existing = self::fetchOne(
            "SELECT id FROM ge_landing_pages WHERE service_id = ? AND city_id = ? AND industry_id = ? LIMIT 1",
            'iii', [(int)$data['service_id'], (int)$data['city_id'], $industryId]
        );

        if ($existing) {
            self::update((int)$existing['id'], $data);
            return (int)$existing['id'];
        }

        $hasV2 = self::columnExists('industry_id');
        if ($hasV2) {
            $sql = "INSERT INTO ge_landing_pages (service_id, city_id, industry_id, page_type, slug, url_path, meta_title, meta_description, h1, h2, h3, content, quick_answer, key_takeaways, summary, expert_insight, faq_json, schema_json, keywords_json, paa_json, voice_answer, internal_links_json, cta_json, content_hash, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            self::execute($sql, 'iiissssssssssssssssssssss', [
                (int)$data['service_id'], (int)$data['city_id'], $industryId,
                $data['page_type'] ?? ($industryId > 0 ? 'service_city_industry' : 'service_city'),
                $data['slug'], $data['url_path'], $data['meta_title'], $data['meta_description'],
                $data['h1'], $data['h2'], $data['h3'] ?? null, $data['content'],
                $data['quick_answer'], $data['key_takeaways'], $data['summary'], $data['expert_insight'],
                $data['faq_json'], $data['schema_json'], $data['keywords_json'], $data['paa_json'],
                $data['voice_answer'], $data['internal_links_json'], $data['cta_json'] ?? null,
                $data['content_hash'], $data['status'] ?? 'published',
            ]);
        } else {
            $sql = "INSERT INTO ge_landing_pages (service_id, city_id, slug, url_path, meta_title, meta_description, h1, h2, content, quick_answer, key_takeaways, summary, expert_insight, faq_json, schema_json, keywords_json, paa_json, voice_answer, internal_links_json, content_hash, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            self::execute($sql, 'iisssssssssssssssssss', [
                (int)$data['service_id'], (int)$data['city_id'], $data['slug'], $data['url_path'],
                $data['meta_title'], $data['meta_description'], $data['h1'], $data['h2'],
                $data['content'], $data['quick_answer'], $data['key_takeaways'], $data['summary'],
                $data['expert_insight'], $data['faq_json'], $data['schema_json'], $data['keywords_json'],
                $data['paa_json'], $data['voice_answer'], $data['internal_links_json'], $data['content_hash'],
                $data['status'] ?? 'published',
            ]);
        }
        return self::lastId();
    }

    public static function update(int $id, array $data): bool
    {
        if (self::columnExists('industry_id')) {
            $sql = "UPDATE ge_landing_pages SET slug=?, url_path=?, meta_title=?, meta_description=?, h1=?, h2=?, h3=?, content=?, quick_answer=?, key_takeaways=?, summary=?, expert_insight=?, faq_json=?, schema_json=?, keywords_json=?, paa_json=?, voice_answer=?, internal_links_json=?, cta_json=?, content_hash=?, page_type=?, status=? WHERE id=?";
            return self::execute($sql, 'ssssssssssssssssssssssi', [
                $data['slug'], $data['url_path'], $data['meta_title'], $data['meta_description'],
                $data['h1'], $data['h2'], $data['h3'] ?? null, $data['content'], $data['quick_answer'],
                $data['key_takeaways'], $data['summary'], $data['expert_insight'], $data['faq_json'],
                $data['schema_json'], $data['keywords_json'], $data['paa_json'], $data['voice_answer'],
                $data['internal_links_json'], $data['cta_json'] ?? null, $data['content_hash'],
                $data['page_type'] ?? 'service_city', $data['status'] ?? 'published', $id,
            ]);
        }

        $sql = "UPDATE ge_landing_pages SET slug=?, url_path=?, meta_title=?, meta_description=?, h1=?, h2=?, content=?, quick_answer=?, key_takeaways=?, summary=?, expert_insight=?, faq_json=?, schema_json=?, keywords_json=?, paa_json=?, voice_answer=?, internal_links_json=?, content_hash=?, status=? WHERE id=?";
        return self::execute($sql, 'sssssssssssssssssssi', [
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

    public static function countsGroupedByService(): array
    {
        if (!self::db()) {
            return [];
        }

        $rows = self::fetchAll(
            "SELECT service_id, COUNT(*) AS c FROM ge_landing_pages
             WHERE industry_id = 0 AND status = 'published'
             GROUP BY service_id"
        );
        $map = [];
        foreach ($rows as $row) {
            $map[(int)$row['service_id']] = (int)$row['c'];
        }
        return $map;
    }

    public static function citySlugMapByService(int $serviceId): array
    {
        $rows = self::fetchAll(
            "SELECT lp.slug, c.slug AS city_slug
             FROM ge_landing_pages lp
             INNER JOIN ge_cities c ON c.id = lp.city_id
             WHERE lp.service_id = ? AND lp.industry_id = 0 AND lp.status = 'published'",
            'i',
            [$serviceId]
        );
        $map = [];
        foreach ($rows as $row) {
            $map[$row['city_slug']] = $row['slug'];
        }
        return $map;
    }

    public static function coverageSummary(): array
    {
        $serviceCount = ge_table_exists('ge_services') ? (int)(Service::count(true)) : 0;
        $cityCount = ge_table_exists('ge_cities') ? (int)(City::count(true)) : 0;
        $pageCount = self::count('published');
        $expected = $serviceCount * $cityCount;

        return [
            'services' => $serviceCount,
            'cities' => $cityCount,
            'pages' => $pageCount,
            'expected' => $expected,
            'coverage_pct' => $expected > 0 ? (int)round(($pageCount / $expected) * 100) : 0,
        ];
    }

    public static function forSitemap(int $limit = 50000, int $offset = 0): array
    {
        return self::fetchAll(
            "SELECT slug, updated_at, generated_at FROM ge_landing_pages WHERE status = 'published' ORDER BY id ASC LIMIT ? OFFSET ?",
            'ii', [$limit, $offset]
        );
    }

    public static function forFeed(int $limit = 100): array
    {
        return self::fetchAll(
            "SELECT lp.slug, lp.meta_title, lp.meta_description, lp.h1, lp.quick_answer, lp.keywords_json, lp.generated_at,
                    s.name AS service_name, c.name AS city_name
             FROM ge_landing_pages lp
             INNER JOIN ge_services s ON s.id = lp.service_id
             INNER JOIN ge_cities c ON c.id = lp.city_id
             WHERE lp.status = 'published' AND lp.industry_id = 0
             ORDER BY lp.generated_at DESC LIMIT ?",
            'i', [$limit]
        );
    }

    public static function indexStats(): array
    {
        $db = self::db();
        $hasIndexStatus = self::columnExists('index_status');
        $hasIsIndexed = self::columnExists('is_indexed');

        if ($hasIndexStatus && $hasIsIndexed) {
            return self::fetchOne(
                "SELECT COUNT(*) AS total,
                        SUM(CASE WHEN index_status = 'indexed' OR is_indexed = 1 THEN 1 ELSE 0 END) AS indexed,
                        SUM(CASE WHEN index_status = 'pending' THEN 1 ELSE 0 END) AS pending,
                        SUM(CASE WHEN index_status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                        SUM(CASE WHEN index_status = 'failed' THEN 1 ELSE 0 END) AS failed
                 FROM ge_landing_pages WHERE status = 'published'"
            ) ?? ['total' => 0, 'indexed' => 0, 'pending' => 0, 'submitted' => 0, 'failed' => 0];
        }

        $total = (int)(self::fetchOne("SELECT COUNT(*) AS total FROM ge_landing_pages WHERE status = 'published'")['total'] ?? 0);
        return ['total' => $total, 'indexed' => 0, 'pending' => $total, 'submitted' => 0, 'failed' => 0];
    }

    private static function columnExists(string $col): bool
    {
        static $cache = [];
        if (isset($cache[$col])) return $cache[$col];
        $r = self::db()->query("SHOW COLUMNS FROM ge_landing_pages LIKE '" . self::db()->real_escape_string($col) . "'");
        $cache[$col] = $r && $r->num_rows > 0;
        return $cache[$col];
    }
}
