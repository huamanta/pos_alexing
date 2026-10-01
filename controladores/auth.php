<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../core/FluentQuery.php';
require_once __DIR__ . '/../core/FluentSave.php';
require_once __DIR__ . '/../configuraciones/ConexionPdo.php';

function getClientIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    }

    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

if (!isset($_SESSION['idusuario'])) {
    Response::json(["status" => false]);
}

$idusuario = $_SESSION['idusuario'];
$logout = date('Y-m-d H:i:s');
$ip = getClientIP();

$pdo = Conexion::conectar();

$login = (new DBQuery($pdo))
    ->from('login_historial')
    ->where('idusuario', '=', $idusuario)
    ->where('exito', '=', 1)
    ->orderBy('fecha', 'DESC')
    ->first();

if ($login) {
    (new FluentSaver($pdo))
        ->table('login_historial')
        ->where('id', '=', $login['id'])
        ->data([
            'exito' => 0,
            'logout' => $logout,
            'ip' => $ip
        ])
        ->timestamps(false)
        ->update();
}

session_destroy();

Response::json(["status" => true]);