<?php

declare(strict_types=1);

namespace App\Models;

final class Deal extends Model
{
    protected string $table = 'deals';

    protected array $fillable = [
        'title',
        'slug',
        'description',
        'type',
        'discount_label',
        'coupon_code',
        'image_path',
        'start_datetime',
        'end_datetime',
        'is_active',
        'sort_order',
    ];

    public function activeVisible(?int $limit = null, ?string $type = null): array
    {
        $params = [];
        $typeClause = '';
        if ($type !== null && $type !== '') {
            $typeClause = ' AND type = :type';
            $params['type'] = $type;
        }

        $sql = 'SELECT * FROM deals
                WHERE is_active = 1
                  AND (start_datetime IS NULL OR start_datetime <= NOW())
                  AND (end_datetime IS NULL OR end_datetime >= NOW())'
            . $typeClause
            . ' ORDER BY sort_order ASC, id DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT :lim';
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function findVisibleByCoupon(string $couponCode): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM deals
             WHERE type = 'coupon'
               AND coupon_code = :coupon_code
               AND is_active = 1
               AND (start_datetime IS NULL OR start_datetime <= NOW())
               AND (end_datetime IS NULL OR end_datetime >= NOW())
             LIMIT 1"
        );
        $stmt->execute(['coupon_code' => $couponCode]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function allOrdered(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM deals ORDER BY sort_order ASC, id DESC');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
