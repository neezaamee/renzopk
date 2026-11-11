<?php

declare(strict_types=1);

namespace App\Models;

final class Tag extends Model
{
    protected string $table = 'tags';

    protected array $fillable = [
        'name',
        'slug',
        'color',
    ];

    /**
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, mixed>}
     */
    public function paginateAdmin(array $options = []): array
    {
        return $this->paginateSql(
            '*',
            'tags',
            [],
            [],
            ['name', 'slug', 'color'],
            (string) ($options['q'] ?? ''),
            [
                'name' => 'name',
                'slug' => 'slug',
                'color' => 'color',
            ],
            'name ASC, id ASC',
            (string) ($options['sort'] ?? ''),
            (string) ($options['dir'] ?? 'asc'),
            (int) ($options['page'] ?? 1),
            (int) ($options['per_page'] ?? 10)
        );
    }
}

