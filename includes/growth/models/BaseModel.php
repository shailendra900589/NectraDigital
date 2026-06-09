<?php
namespace Growth\Models;

abstract class BaseModel
{
    protected static function db(): \mysqli
    {
        return ge_conn();
    }

    protected static function fetchAll(string $sql, string $types = '', array $params = []): array
    {
        $db = self::db();
        if ($types === '') {
            $res = $db->query($sql);
            return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        }
        $stmt = $db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    protected static function fetchOne(string $sql, string $types = '', array $params = []): ?array
    {
        $rows = self::fetchAll($sql, $types, $params);
        return $rows[0] ?? null;
    }

    protected static function execute(string $sql, string $types = '', array $params = []): bool
    {
        $stmt = self::db()->prepare($sql);
        if (!$stmt) return false;
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        return $stmt->execute();
    }

    protected static function lastId(): int
    {
        return (int)self::db()->insert_id;
    }
}
