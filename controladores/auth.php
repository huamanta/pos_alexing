<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../configuraciones/Conexion.php';

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

if (!isset($_SESSION['idusuario'])) {
    echo json_encode(["status" => false]);
    exit;
}

$idusuario = $_SESSION['idusuario'];
$logout = date('Y-m-d H:i:s');
$ip = getClientIP();

$sql = "UPDATE login_historial
        SET exito = 0, logout = ?, ip = ?
        WHERE idusuario = ? AND exito = 1
        ORDER BY fecha DESC
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssi", $logout, $ip, $idusuario);
$stmt->execute();

session_destroy();

echo json_encode(["status" => true]);
