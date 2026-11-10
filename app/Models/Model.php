<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use InvalidArgumentException;
use PDO;

abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';

    /** @var list<string> */
    protected array $fillable = [];

    protected PDO $db;

    public function __construct(?PDO $pdo = null)
    {
        $this->db = $pdo ?? Database::connection();
    }

    public function find(int|string $id): ?array
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `%s` = :id LIMIT 1',
            $this->safeIdentifier($this->table),
            $this->safeIdentifier($this->primaryKey)
        );
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function findBy(string $column, mixed $value): ?array
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `%s` = :value LIMIT 1',
            $this->safeIdentifier($this->table),
            $this->safeIdentifier($column)
        );
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function all(string $orderBy = 'id', string $direction = 'ASC'): array
    {
        $dir = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $sql = sprintf(
            'SELECT * FROM `%s` ORDER BY `%s` %s',
            $this->safeIdentifier($this->table),
            $this->safeIdentifier($orderBy),
            $dir
        );
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function where(array $conditions, string $orderBy = 'id', string $direction = 'ASC'): array
    {
        $dir = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $table = $this->safeIdentifier($this->table);
        $orderCol = $this->safeIdentifier($orderBy);

        if ($conditions === []) {
            return $this->all($orderBy, $dir);
        }

        $clauses = [];
        $params = [];
        foreach ($conditions as $column => $value) {
            $col = $this->safeIdentifier((string) $column);
            if ($value === null) {
                $clauses[] = "`{$col}` IS NULL";
                continue;
            }
            $param = 'w_' . $col;
            $clauses[] = "`{$col}` = :{$param}";
            $params[$param] = $value;
        }

        $sql = sprintf(
            'SELECT * FROM `%s` WHERE %s ORDER BY `%s` %s',
            $table,
            implode(' AND ', $clauses),
            $orderCol,
            $dir
        );
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function create(array $data): int|string
    {
        $payload = $this->filterFillable($data);
        if ($payload === []) {
            throw new InvalidArgumentException('No fillable attributes provided for insert.');
        }

        $columns = array_keys($payload);
        $colSql = implode(', ', array_map(fn (string $c): string => '`' . $this->safeIdentifier($c) . '`', $columns));
        $paramSql = implode(', ', array_map(fn (string $c): string => ':' . $c, $columns));

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $this->safeIdentifier($this->table),
            $colSql,
            $paramSql
        );

        $stmt = $this->db->prepare($sql);
        $stmt->execute($payload);

        $lastId = $this->db->lastInsertId();
        return is_numeric($lastId) && (int) $lastId > 0 ? (int) $lastId : $lastId;
    }

    public function update(int|string $id, array $data): bool
    {
        $payload = $this->filterFillable($data);
        if ($payload === []) {
            return false;
        }

        $sets = [];
        foreach (array_keys($payload) as $column) {
            $safeCol = $this->safeIdentifier($column);
            $sets[] = "`{$safeCol}` = :{$safeCol}";
        }

        $payload['_pk_id'] = $id;
        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE `%s` = :_pk_id',
            $this->safeIdentifier($this->table),
            implode(', ', $sets),
            $this->safeIdentifier($this->primaryKey)
        );

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($payload);
    }

    public function delete(int|string $id): bool
    {
        $sql = sprintf(
            'DELETE FROM `%s` WHERE `%s` = :id',
            $this->safeIdentifier($this->table),
            $this->safeIdentifier($this->primaryKey)
        );
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function count(array $conditions = []): int
    {
        $table = $this->safeIdentifier($this->table);
        if ($conditions === []) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM `{$table}`");
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        }

        $clauses = [];
        $params = [];
        foreach ($conditions as $column => $value) {
            $col = $this->safeIdentifier((string) $column);
            if ($value === null) {
                $clauses[] = "`{$col}` IS NULL";
                continue;
            }
            $param = 'c_' . $col;
            $clauses[] = "`{$col}` = :{$param}";
            $params[$param] = $value;
        }

        $sql = sprintf('SELECT COUNT(*) FROM `%s` WHERE %s', $table, implode(' AND ', $clauses));
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function beginTransaction(): void
    {
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
        }
    }

    public function commit(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->commit();
        }
    }

    public function rollBack(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }

    protected function filterFillable(array $data): array
    {
        if ($this->fillable === []) {
            return [];
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function safeIdentifier(string $identifier): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException('Invalid SQL identifier: ' . $identifier);
        }

        return $identifier;
    }
}

