<?php

declare(strict_types=1);

namespace App\Models;

final class MenuItemVariant extends Model
{
    protected string $table = 'menu_item_variants';

    protected array $fillable = [
        'menu_item_id',
        'name',
        'price',
        'is_available',
        'sort_order',
    ];

    public function forMenuItem(int $menuItemId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM menu_item_variants
             WHERE menu_item_id = :menu_item_id
             ORDER BY sort_order ASC, id ASC'
        );
        $stmt->execute(['menu_item_id' => $menuItemId]);

        return $stmt->fetchAll();
    }

    public function deleteByMenuItem(int $menuItemId): void
    {
        $stmt = $this->db->prepare('DELETE FROM menu_item_variants WHERE menu_item_id = :menu_item_id');
        $stmt->execute(['menu_item_id' => $menuItemId]);
    }
}

