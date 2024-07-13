<?php

namespace App;

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

    public function getPDO(): \PDO
    {
        return $this->pdo;
    }
}