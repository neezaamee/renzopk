<?php

declare(strict_types=1);

namespace App\Models;

final class Reservation extends Model
{
    protected string $table = 'reservations';

    protected array $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'party_size',
        'reservation_date',
        'reservation_time',
        'special_requests',
        'status',
        'admin_notes',
        'handled_by_admin_id',
    ];

    public function allWithAdmin(?string $status = null): array
    {
        if ($status !== null && $status !== '') {
            $stmt = $this->db->prepare(
                'SELECT r.*, a.name AS handled_by_name
                 FROM reservations r
                 LEFT JOIN admins a ON a.id = r.handled_by_admin_id
                 WHERE r.status = :status
                 ORDER BY r.reservation_date ASC, r.reservation_time ASC, r.id DESC'
            );
            $stmt->execute(['status' => $status]);
            return $stmt->fetchAll();
        }

        $stmt = $this->db->prepare(
            'SELECT r.*, a.name AS handled_by_name
             FROM reservations r
             LEFT JOIN admins a ON a.id = r.handled_by_admin_id
             ORDER BY r.reservation_date ASC, r.reservation_time ASC, r.id DESC'
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findWithAdmin(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, a.name AS handled_by_name
             FROM reservations r
             LEFT JOIN admins a ON a.id = r.handled_by_admin_id
             WHERE r.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }
}

