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
}

