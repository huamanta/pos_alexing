<?php
// Iniciar sesión solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('America/Lima');
// Función para obtener IP real
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    } else {
        return '0.0.0.0';
    }
}

// Incluir archivo de conexión
require_once __DIR__ . '/../../configuraciones/Conexion.php';

if (isset($_SESSION['idusuario'])) {

    $idusuario = $_SESSION['idusuario'];
    $logout = date('Y-m-d H:i:s');
    $ip = getClientIP();

    // Actualizar el último login exitoso
    $sql = "UPDATE login_historial 
            SET exito = 0, logout = ?, ip = ?
            WHERE idusuario = ? AND exito = 1
            ORDER BY fecha DESC 
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssi", $logout, $ip, $idusuario);
    $stmt->execute();
}

// Destruir sesión
session_destroy();

// Redirigir al login
echo '<script>window.location = "ingreso";</script>';
exit;
?>
