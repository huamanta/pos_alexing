<?php
// No iniciar sesión aquí - ya debería estar iniciada desde plantilla.php
// Función para obtener IP real
function getClientIP()
{
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

require_once __DIR__ . '/../../configuraciones/ConexionPdo.php';

if (isset($_SESSION['idusuario'])) {

    $idusuario = $_SESSION['idusuario'];
    $logout = date('Y-m-d H:i:s');
    $ip = getClientIP();
    $pdo = Conexion::conectar();

    $registro = (new DBQuery($pdo))
        ->select('*')
        ->from('login_historial')
        ->where('idusuario', '=', $idusuario)
        ->where('exito', '=', 1)
        ->orderBy('fecha', 'DESC')
        ->first();

    if ($registro !== null) {
        (new FluentSaver($pdo))
            ->table('login_historial')
            ->primaryKey('idhistorial')
            ->timestamps(false)
            ->data([
                'idhistorial' => $registro['idhistorial'],
                'exito' => 0,
                'logout' => $logout,
                'ip' => $ip
            ])
            ->update();
    }
}

// Destruir sesión completamente
$_SESSION = array();

session_destroy();

header('Location: ' . $_SERVER['PHP_SELF'] . '?ruta=login');
exit;
