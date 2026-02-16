<?php
// logout.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluye la conexión a la BD (ajusta la ruta según tu estructura)
require_once __DIR__ . "/../configuraciones/Conexion.php";

// Si hay un usuario logueado, actualiza la columna exito y logout
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

// Limpiar y destruir sesión
session_unset();
session_destroy();

// Verificamos si es AJAX o acceso directo
if (isset($_GET['ajax'])) {
    // Para llamadas AJAX
    echo json_encode(['status' => true]);
} else {
    // Para clic en salir desde el navegador
    header("Location: ../index.php"); // Ajusta al login
    exit;
}
