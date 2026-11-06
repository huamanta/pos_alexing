<?php

class Conexion
{
    private static ?PDO $pdo = null;

    private static function cargarConfig()
    {
        return [
            'host' => env('DB_HOST'),
            'db' => env('DB_DATABASE'),
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD')
        ];
    }

    public static function conectar(): PDO
    {
        if (self::$pdo === null) {
            $config = self::cargarConfig();

            self::$pdo = new PDO(
                "mysql:host={$config['host']};dbname={$config['db']};charset=utf8mb4",
                $config['user'],
                $config['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false
                ]
            );
        }

        return self::$pdo;
    }
}