<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../configuraciones/Conexion.php";

if (isset($_SESSION['idusuario'])) {
    $idusuario = $_SESSION['idusuario'];

    $sql = "UPDATE login_historial
            SET logout = NOW(), exito = 0
            WHERE idusuario = '$idusuario'
              AND exito = 1
              AND logout IS NULL
            ORDER BY fecha DESC
            LIMIT 1";
    ejecutarConsulta($sql);
}

// 🔥 IMPORTANTE: borrar cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// destruir sesión
session_unset();
session_destroy();

if (isset($_GET['ajax'])) {
    echo json_encode(['status' => true]);
} else {
    header("Location: ../index.php");
    exit;
}