<?php
// No iniciar sesión aquí - ya debería estar iniciada desde plantilla.php
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

// Destruir sesión completamente
$_SESSION = array();



session_destroy();

// Redirigir al login usando JavaScript (ya que los headers ya fueron enviados)
echo '<script>window.location.href = "login";</script>';
exit;
?>