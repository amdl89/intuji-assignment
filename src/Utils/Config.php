<?php

namespace App\Utils;

class Config
{
    private static array $config;

    public static function get(string $key, mixed $default = null)
    {
        self::loadConfigFile();
        return self::$config[$key] ?? $default;
    }

    private static function loadConfigFile(): void
    {
        if (!isset(self::$config)) {
            self::$config = require __DIR__ . '/../../config/app.php';
        }
    }
}
