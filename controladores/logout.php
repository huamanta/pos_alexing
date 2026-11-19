<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../configuraciones/ConexionPdo.php';

if (isset($_SESSION['idusuario'])) {
    $idusuario = $_SESSION['idusuario'];
    $pdo = Conexion::conectar();
    // Buscar el último historial de inicio de sesión abierto
    $historial = (new DBQuery($pdo))
        ->select(['idlogin_historial'])
        ->from('login_historial')
        ->where('idusuario', '=', $idusuario)
        ->where('exito', '=', 1)
        ->whereNull('logout')
        ->orderBy('fecha', 'DESC')
        ->limit(1)
        ->first();

    if ($historial) {
        (new FluentSaver($pdo))
            ->table('login_historial')
            ->data([
                'logout' => date('Y-m-d H:i:s'),
                'exito' => 0
            ])
            ->where('idlogin_historial', '=', $historial['idlogin_historial'])
            ->update();
    }
}

// Eliminar cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destruir sesión
session_unset();
session_destroy();

if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => true]);
} else {
    header('Location: ../index.php');
    exit;
}
