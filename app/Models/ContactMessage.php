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
}

