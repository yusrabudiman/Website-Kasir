<?php

namespace App\Config;

class App {
    private static $config = [];

    public static function init() {
        self::$config = [
            'name' => $_ENV['APP_NAME'] ?? 'Website Kasir',
            'env' => $_ENV['APP_ENV'] ?? 'production',
            'debug' => $_ENV['APP_DEBUG'] ?? false,
            'url' => $_ENV['APP_URL'] ?? 'http://localhost',
            'timezone' => $_ENV['TIMEZONE'] ?? 'Asia/Jakarta',
            'locale' => $_ENV['LOCALE'] ?? 'id',
            'currency' => $_ENV['CURRENCY'] ?? 'IDR',
            'session' => [
                'lifetime' => $_ENV['SESSION_LIFETIME'] ?? 120,
                'secure' => $_ENV['SESSION_SECURE'] ?? false
            ]
        ];

        // Set timezone
        date_default_timezone_set(self::$config['timezone']);

        // Set locale
        setlocale(LC_ALL, self::$config['locale']);
    }

    public static function get($key, $default = null) {
        return self::$config[$key] ?? $default;
    }

    public static function set($key, $value) {
        self::$config[$key] = $value;
    }
}
