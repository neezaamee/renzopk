<?php

declare(strict_types=1);

namespace App\Models;

final class MenuCategory extends Model
{
    protected string $table = 'menu_categories';

    protected array $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active',
    ];

    public function activeOrdered(?int $limit = null): array
    {
        if ($limit !== null) {
            $stmt = $this->db->prepare(
                'SELECT * FROM menu_categories WHERE is_active = 1 ORDER BY sort_order ASC, id ASC LIMIT :lim'
            );
            $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM menu_categories WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function allOrderedWithItemCounts(): array
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, COUNT(i.id) AS items_count
             FROM menu_categories c
             LEFT JOIN menu_items i ON i.category_id = c.id
             GROUP BY c.id
             ORDER BY c.sort_order ASC, c.id ASC'
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, mixed>}
     */
    public function paginateAdmin(array $options = []): array
    {
        return $this->paginateSql(
            'c.*, COUNT(i.id) AS items_count',
            'menu_categories c LEFT JOIN menu_items i ON i.category_id = c.id',
            [],
            [],
            ['c.name', 'c.description'],
            (string) ($options['q'] ?? ''),
            [
                'sort_order' => 'c.sort_order',
                'name' => 'c.name',
                'items_count' => 'COUNT(i.id)',
                'is_active' => 'c.is_active',
            ],
            'c.sort_order ASC, c.id ASC',
            (string) ($options['sort'] ?? ''),
            (string) ($options['dir'] ?? 'asc'),
            (int) ($options['page'] ?? 1),
            (int) ($options['per_page'] ?? 10),
            'c.id',
            'c.id'
        );
    }
}

