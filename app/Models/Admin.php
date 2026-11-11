<?php

declare(strict_types=1);

namespace App\Models;

final class Admin extends Model
{
    protected string $table = 'admins';

    protected array $fillable = [
        'name',
        'email',
        'role',
        'is_active',
        'last_login_at',
    ];

    public function findActiveByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM admins WHERE email = :email AND is_active = 1 LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function findActiveById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM admins WHERE id = :id AND is_active = 1 LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function touchLastLogin(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE admins SET last_login_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, mixed>}
     */
    public function paginateAdmin(array $options = []): array
    {
        return $this->paginateSql(
            '*',
            'admins',
            [],
            [],
            ['name', 'email', 'role'],
            (string) ($options['q'] ?? ''),
            [
                'name' => 'name',
                'email' => 'email',
                'role' => 'role',
                'is_active' => 'is_active',
                'last_login_at' => 'last_login_at',
            ],
            'id ASC',
            (string) ($options['sort'] ?? ''),
            (string) ($options['dir'] ?? 'asc'),
            (int) ($options['page'] ?? 1),
            (int) ($options['per_page'] ?? 10)
        );
    }
}

