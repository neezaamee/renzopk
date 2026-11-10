<?php

declare(strict_types=1);

namespace App\Models;

final class GalleryItem extends Model
{
    protected string $table = 'gallery_items';

    protected array $fillable = [
        'type',
        'title',
        'caption',
        'category',
        'image_path',
        'youtube_id',
        'alt_text',
        'sort_order',
        'is_active',
    ];

    public function activeOrdered(?string $category = null): array
    {
        if ($category !== null && $category !== '') {
            $stmt = $this->db->prepare(
                'SELECT * FROM gallery_items
                 WHERE is_active = 1 AND LOWER(category) = LOWER(:category)
                 ORDER BY sort_order ASC, id DESC'
            );
            $stmt->execute(['category' => $category]);
            return $stmt->fetchAll();
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM gallery_items WHERE is_active = 1 ORDER BY sort_order ASC, id DESC'
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function distinctActiveCategories(): array
    {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT category FROM gallery_items
             WHERE is_active = 1 AND category IS NOT NULL AND category <> ''
             ORDER BY category ASC"
        );
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function allOrdered(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM gallery_items ORDER BY sort_order ASC, id DESC');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
