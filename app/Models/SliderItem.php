<?php

declare(strict_types=1);

namespace App\Models;

final class SliderItem extends Model
{
    protected string $table = 'slider_items';

    protected array $fillable = [
        'title',
        'subtitle',
        'cta_text',
        'cta_url',
        'image_path',
        'alt_text',
        'sort_order',
        'is_active',
    ];

    public function activeOrdered(): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM slider_items WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function allOrdered(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM slider_items ORDER BY sort_order ASC, id ASC');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

