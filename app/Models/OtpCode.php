<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class OtpCode extends Model
{
    protected string $table = 'otp_codes';

    protected array $fillable = [
        'admin_id',
        'code_hash',
        'expires_at',
        'attempts',
        'used_at',
        'ip_address',
    ];

    public function createForAdmin(int $adminId, string $codeHash, string $ipAddress, int $expiryMinutes = 10): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO otp_codes (admin_id, code_hash, expires_at, attempts, ip_address)
             VALUES (:admin_id, :code_hash, TIMESTAMPADD(MINUTE, :expiry, NOW()), 0, :ip_address)'
        );
        $stmt->bindValue(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->bindValue(':code_hash', $codeHash, PDO::PARAM_STR);
        $stmt->bindValue(':expiry', $expiryMinutes, PDO::PARAM_INT);
        $stmt->bindValue(':ip_address', $ipAddress, PDO::PARAM_STR);
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    public function invalidateUnusedForAdmin(int $adminId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE otp_codes SET used_at = NOW() WHERE admin_id = :admin_id AND used_at IS NULL'
        );
        $stmt->bindValue(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function countRecentRequests(int $adminId, int $windowMinutes = 10): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM otp_codes
             WHERE admin_id = :admin_id
               AND created_at >= TIMESTAMPADD(MINUTE, :neg_minutes, NOW())'
        );
        $stmt->bindValue(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->bindValue(':neg_minutes', -abs($windowMinutes), PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function sumRecentAttempts(int $adminId, int $windowMinutes = 10): int
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(attempts), 0) FROM otp_codes
             WHERE admin_id = :admin_id
               AND created_at >= TIMESTAMPADD(MINUTE, :neg_minutes, NOW())'
        );
        $stmt->bindValue(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->bindValue(':neg_minutes', -abs($windowMinutes), PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function findLatestValidForAdmin(int $adminId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM otp_codes
             WHERE admin_id = :admin_id
               AND used_at IS NULL
               AND expires_at > NOW()
             ORDER BY id DESC
             LIMIT 1'
        );
        $stmt->bindValue(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function incrementAttemptIfAllowed(int $id, int $maxAttempts = 5): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE otp_codes
             SET attempts = attempts + 1
             WHERE id = :id
               AND attempts < :max_attempts
               AND used_at IS NULL
               AND expires_at > NOW()'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':max_attempts', $maxAttempts, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    public function markUsed(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE otp_codes SET used_at = NOW() WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
