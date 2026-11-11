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

    /**
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, mixed>}
     */
    public function paginateAdmin(array $options = []): array
    {
        return $this->paginateSql(
            '*',
            'slider_items',
            [],
            [],
            ['title', 'subtitle', 'cta_text', 'cta_url'],
            (string) ($options['q'] ?? ''),
            [
                'title' => 'title',
                'cta_text' => 'cta_text',
                'sort_order' => 'sort_order',
                'is_active' => 'is_active',
            ],
            'sort_order ASC, id ASC',
            (string) ($options['sort'] ?? ''),
            (string) ($options['dir'] ?? 'asc'),
            (int) ($options['page'] ?? 1),
            (int) ($options['per_page'] ?? 10)
        );
    }
}

