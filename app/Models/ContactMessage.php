<?php

declare(strict_types=1);

namespace App\Models;

final class ContactMessage extends Model
{
    protected string $table = 'contact_messages';

    protected array $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'inquiry_type',
        'message',
        'is_read',
        'read_at',
        'ip_address',
    ];

    public function allOrdered(?string $filter = null): array
    {
        if ($filter === 'unread') {
            $stmt = $this->db->prepare(
                'SELECT * FROM contact_messages WHERE is_read = 0 ORDER BY created_at DESC, id DESC'
            );
            $stmt->execute();
            return $stmt->fetchAll();
        }

        if ($filter === 'career') {
            $stmt = $this->db->prepare(
                "SELECT * FROM contact_messages WHERE inquiry_type = 'career' ORDER BY created_at DESC, id DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll();
        }

        $stmt = $this->db->prepare('SELECT * FROM contact_messages ORDER BY created_at DESC, id DESC');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function markRead(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE contact_messages SET is_read = 1, read_at = COALESCE(read_at, NOW()) WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, mixed>}
     */
    public function paginateAdmin(array $options = [], ?string $filter = null): array
    {
        $where = [];
        if ($filter === 'unread') {
            $where[] = 'is_read = 0';
        } elseif ($filter === 'career') {
            $where[] = "inquiry_type = 'career'";
        }

        return $this->paginateSql(
            '*',
            'contact_messages',
            $where,
            [],
            ['name', 'email', 'phone', 'subject', 'inquiry_type', 'message'],
            (string) ($options['q'] ?? ''),
            [
                'is_read' => 'is_read',
                'name' => 'name',
                'inquiry_type' => 'inquiry_type',
                'created_at' => 'created_at',
            ],
            'created_at DESC, id DESC',
            (string) ($options['sort'] ?? ''),
            (string) ($options['dir'] ?? 'asc'),
            (int) ($options['page'] ?? 1),
            (int) ($options['per_page'] ?? 10)
        );
    }
}

