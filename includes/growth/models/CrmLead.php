<?php
namespace Growth\Models;

class CrmLead extends BaseModel
{
    public static function all(int $limit = 100): array
    {
        if (!ge_table_exists('ge_crm_leads')) return [];
        return self::fetchAll("SELECT * FROM ge_crm_leads ORDER BY created_at DESC LIMIT ?", 'i', [$limit]);
    }

    public static function create(array $data): int
    {
        self::execute(
            "INSERT INTO ge_crm_leads (name, email, phone, service_interest, city, source, message, status, meta_json) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            'sssssssss',
            [$data['name'], $data['email'], $data['phone'] ?? null, $data['service_interest'] ?? null,
             $data['city'] ?? null, $data['source'] ?? 'website', $data['message'] ?? null,
             $data['status'] ?? 'new', $data['meta_json'] ?? null]
        );
        return self::lastId();
    }

    public static function updateStatus(int $id, string $status): bool
    {
        return self::execute("UPDATE ge_crm_leads SET status = ? WHERE id = ?", 'si', [$status, $id]);
    }

    public static function stats(): array
    {
        if (!ge_table_exists('ge_crm_leads')) return ['total' => 0, 'new' => 0, 'won' => 0];
        return self::fetchOne(
            "SELECT COUNT(*) AS total,
                    SUM(CASE WHEN status='new' THEN 1 ELSE 0 END) AS new,
                    SUM(CASE WHEN status='won' THEN 1 ELSE 0 END) AS won
             FROM ge_crm_leads"
        ) ?? ['total' => 0, 'new' => 0, 'won' => 0];
    }
}
