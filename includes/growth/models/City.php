<?php
namespace Growth\Models;

class City extends BaseModel
{
    public static function all(bool $activeOnly = false): array
    {
        $sql = "SELECT * FROM ge_cities" . ($activeOnly ? " WHERE status = 'active'" : "") . " ORDER BY name ASC";
        return self::fetchAll($sql);
    }

    public static function find(int $id): ?array
    {
        return self::fetchOne("SELECT * FROM ge_cities WHERE id = ? LIMIT 1", 'i', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::fetchOne("SELECT * FROM ge_cities WHERE slug = ? LIMIT 1", 's', [$slug]);
    }

    public static function count(bool $activeOnly = false): int
    {
        $sql = "SELECT COUNT(*) AS c FROM ge_cities" . ($activeOnly ? " WHERE status = 'active'" : "");
        $row = self::fetchOne($sql);
        return (int)($row['c'] ?? 0);
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO ge_cities (name, slug, state, country, population, latitude, longitude, city_description, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $lat = !empty($data['latitude']) ? (float)$data['latitude'] : 0.0;
        $lng = !empty($data['longitude']) ? (float)$data['longitude'] : 0.0;
        self::execute($sql, 'ssssiddds', [
            $data['name'], $data['slug'], $data['state'] ?? null,
            $data['country'] ?? 'India',
            (int)($data['population'] ?? 0),
            $lat, $lng,
            $data['city_description'] ?? null,
            $data['status'] ?? 'active',
        ]);
        return self::lastId();
    }

    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE ge_cities SET name=?, slug=?, state=?, country=?, population=?, latitude=?, longitude=?, city_description=?, status=? WHERE id=?";
        $lat = !empty($data['latitude']) ? (float)$data['latitude'] : 0.0;
        $lng = !empty($data['longitude']) ? (float)$data['longitude'] : 0.0;
        return self::execute($sql, 'ssssidddssi', [
            $data['name'], $data['slug'], $data['state'] ?? null,
            $data['country'] ?? 'India',
            (int)($data['population'] ?? 0),
            $lat, $lng,
            $data['city_description'] ?? null,
            $data['status'] ?? 'active',
            $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        return self::execute("DELETE FROM ge_cities WHERE id = ?", 'i', [$id]);
    }
}
