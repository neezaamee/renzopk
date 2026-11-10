<?php

declare(strict_types=1);

namespace App\Models;

final class MenuItem extends Model
{
    protected string $table = 'menu_items';

    protected array $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image_path',
        'is_featured',
        'is_available',
        'is_active',
        'sort_order',
    ];

    public function activeForMenu(): array
    {
        $stmt = $this->db->prepare(
            'SELECT i.*
             FROM menu_items i
             INNER JOIN menu_categories c ON c.id = i.category_id
             WHERE c.is_active = 1 AND i.is_active = 1
             ORDER BY c.sort_order ASC, i.sort_order ASC, i.id ASC'
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function activeForCategory(int $categoryId, int $limit = 3): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM menu_items
             WHERE category_id = :category_id
               AND is_active = 1
             ORDER BY sort_order ASC, id ASC
             LIMIT :lim'
        );
        $stmt->bindValue(':category_id', $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function featuredActive(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            'SELECT i.*, c.name AS category_name
             FROM menu_items i
             INNER JOIN menu_categories c ON c.id = i.category_id
             WHERE c.is_active = 1
               AND i.is_active = 1
               AND i.is_featured = 1
             ORDER BY i.sort_order ASC, i.id ASC
             LIMIT :lim'
        );
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function allWithCategory(): array
    {
        $stmt = $this->db->prepare(
            'SELECT i.*, c.name AS category_name
             FROM menu_items i
             INNER JOIN menu_categories c ON c.id = i.category_id
             ORDER BY c.sort_order ASC, i.sort_order ASC, i.id ASC'
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

