<?php

declare(strict_types=1);

namespace App\Models;

final class Setting extends Model
{
    protected string $table = 'settings';

    protected array $fillable = [
        'setting_key',
        'setting_value',
        'setting_group',
        'updated_by_admin_id',
    ];

    public function allAsKeyValue(): array
    {
        $stmt = $this->db->prepare('SELECT setting_key, setting_value FROM settings');
        $stmt->execute();

        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(string) $row['setting_key']] = (string) ($row['setting_value'] ?? '');
        }

        return $result;
    }

    public function upsert(string $key, ?string $value, string $group, ?int $adminId): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO settings (setting_key, setting_value, setting_group, updated_by_admin_id)
             VALUES (:setting_key, :setting_value, :setting_group, :admin_id)
             ON DUPLICATE KEY UPDATE
                setting_value = VALUES(setting_value),
                setting_group = VALUES(setting_group),
                updated_by_admin_id = VALUES(updated_by_admin_id)'
        );
        $stmt->execute([
            'setting_key' => $key,
            'setting_value' => $value,
            'setting_group' => $group,
            'admin_id' => $adminId,
        ]);
    }
}

