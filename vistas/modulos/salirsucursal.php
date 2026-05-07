<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Eliminar solo idsucursal
unset($_SESSION['idsucursal']);

// Volver a la página anterior
$redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';

header("Location: $redirect");
exit;