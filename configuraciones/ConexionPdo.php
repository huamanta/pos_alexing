<?php
class Conexion
{
    private static string $host;
    private static string $db;
    private static string $user;
    private static string $pass;

    private static function cargarConfig()
    {
        self::$host = env('DB_HOST');
        self::$db = env('DB_DATABASE');
        self::$user = env('DB_USERNAME');
        self::$pass = env('DB_PASSWORD');
    }

    public static function conectar()
    {
        self::cargarConfig();

        return new PDO(
            "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=utf8mb4",
            self::$user,
            self::$pass
        );
    }
}