<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = (string) Env::get('DB_HOST', '127.0.0.1');
        $port = (string) Env::get('DB_PORT', '3306');
        $database = (string) Env::get('DB_DATABASE', 'renzo');
        $username = (string) Env::get('DB_USERNAME', 'root');
        $password = (string) (Env::get('DB_PASSWORD', '') ?? '');
        $configuredDbTz = trim((string) (Env::get('DB_TIMEZONE', '') ?? ''));
        $dbTimezone = $configuredDbTz !== '' ? $configuredDbTz : (new \DateTimeImmutable('now'))->format('P');

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

        try {
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage(), 0, $e);
        }

        $stmt = $pdo->prepare('SET time_zone = :tz');
        $stmt->execute(['tz' => $dbTimezone]);

        self::$connection = $pdo;

        return self::$connection;
    }
}

