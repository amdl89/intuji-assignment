<?php

namespace App\Database;

use App\Utils\Config;

class DBFactory
{
    private static $instance;
    private \PDO $pdo;

    private function __construct()
    {
        $dbConfig = Config::get('db');

        try {
            $this->pdo = new \PDO($dbConfig['dbUrl']);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getDB(): static
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function query($query, $params = []): bool|\PDOStatement
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    public function getLastInsertId(): bool|string
    {
        return $this->pdo->lastInsertId();
    }

    public function fetch($query, $params = []): mixed
    {
        return $this->query($query, $params)->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchOne($query, $params = []): array | null
    {
        $fetchOneStmt = $this->query($query, $params);
        $result = $fetchOneStmt->fetchAll(\PDO::FETCH_ASSOC);

        if(!$result) {
            return null;
        }
        return $result[0];
    }

    public function getPDO(): \PDO
    {
        return $this->pdo;
    }
}
