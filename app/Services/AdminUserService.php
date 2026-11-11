<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Admin;
use InvalidArgumentException;

final class AdminUserService
{
    private const ALLOWED_ROLES = ['owner', 'manager'];

    private Admin $admins;

    public function __construct(?Admin $admins = null)
    {
        $this->admins = $admins ?? new Admin();
    }

    public function listAll(): array
    {
        return $this->admins->all('id', 'ASC');
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, mixed>}
     */
    public function paginateAdmin(array $options = []): array
    {
        return $this->admins->paginateAdmin($options);
    }

    public function create(array $input): int
    {
        $name = trim((string) ($input['name'] ?? ''));
        $email = strtolower(trim((string) ($input['email'] ?? '')));
        $role = trim((string) ($input['role'] ?? 'manager'));

        if ($name === '') {
            throw new InvalidArgumentException('Admin name is required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please enter a valid email address.');
        }

        if (!in_array($role, self::ALLOWED_ROLES, true)) {
            throw new InvalidArgumentException('Role must be owner or manager.');
        }

        if ($this->admins->findBy('email', $email) !== null) {
            throw new InvalidArgumentException('An admin with this email already exists.');
        }

        return (int) $this->admins->create([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'is_active' => 1,
        ]);
    }

    public function toggleActive(int $targetId, int $actingAdminId): void
    {
        if ($targetId === $actingAdminId) {
            throw new InvalidArgumentException('You cannot deactivate your own active session account.');
        }

        $admin = $this->admins->find($targetId);
        if ($admin === null) {
            throw new InvalidArgumentException('Admin account not found.');
        }

        $this->admins->update($targetId, [
            'is_active' => empty($admin['is_active']) ? 1 : 0,
        ]);
    }
}

