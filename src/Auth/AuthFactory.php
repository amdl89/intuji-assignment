<?php

namespace App\Auth;

use App\Database\DBFactory;

class Auth
{
    private static $instance;
    private array|null $userInfo;
    private DBFactory $db;

    private function __construct()
    {
        $this->db = DBFactory::getDB();
    }

    public static function getAuth(): static
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function loginUsingId($userId): bool
    {
        if ($user = $this->getUserById($userId)) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_unset();
                session_destroy();
                session_start();
            }
            $_SESSION['__USER_INFO'] = $user;
            return true;
        }
        return false;
    }

    public function getUserInfo(): array|null
    {
        return $_SESSION['__USER_INFO'] ?? null;
    }

    public function getUserById(int $userId)
    {
        $user = $this->db->fetchOne(
            'SELECT * FROM users where id = :userId LIMIT 1',
            ['userId' => $userId,]
        );

        if(!$user) {
            return null;
        }
        return $user[0];
    }

}