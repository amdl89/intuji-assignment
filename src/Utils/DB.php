<?php

namespace App\Utils;

class DB
{
    private static self $instance;
    private \PDO $pdo;

    private function __construct()
    {
        $dbUrl = getenv('DB_URL');

        try {
            $this->pdo = new \PDO($dbUrl);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getDB(): static
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
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

    public function getPDO(): \PDO
    {
        return $this->pdo;
    }
}
