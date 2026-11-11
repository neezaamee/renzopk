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

    /**
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, mixed>}
     */
    public function paginateAdmin(array $options = [], ?string $status = null): array
    {
        $where = [];
        $params = [];
        if ($status !== null && $status !== '') {
            $where[] = 'r.status = :status';
            $params['status'] = $status;
        }

        return $this->paginateSql(
            'r.*, a.name AS handled_by_name',
            'reservations r LEFT JOIN admins a ON a.id = r.handled_by_admin_id',
            $where,
            $params,
            ['r.id', 'r.customer_name', 'r.customer_phone', 'r.customer_email', 'r.status', 'a.name'],
            (string) ($options['q'] ?? ''),
            [
                'id' => 'r.id',
                'customer_name' => 'r.customer_name',
                'party_size' => 'r.party_size',
                'reservation_date' => 'r.reservation_date',
                'status' => 'r.status',
            ],
            'r.reservation_date ASC, r.reservation_time ASC, r.id DESC',
            (string) ($options['sort'] ?? ''),
            (string) ($options['dir'] ?? 'asc'),
            (int) ($options['page'] ?? 1),
            (int) ($options['per_page'] ?? 10)
        );
    }
}

