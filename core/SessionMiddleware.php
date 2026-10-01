<?php
require_once __DIR__ . '/../core/Response.php';
$controlador = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
$operacion = $_GET['op'] ?? '';

$rutasPublicas = [
    'usuario.php' => [
        'verificar'
    ]
];

$esRutaPublica = isset($rutasPublicas[$controlador])
    && in_array($operacion, $rutasPublicas[$controlador], true);

if (
    !$esRutaPublica &&
    (!isset($_SESSION['idusuario']) || empty($_SESSION['idusuario']))
) {
    Response::json([
        'status' => 0,
        'message' => 'Sesión expirada. Inicie sesión nuevamente para continuar.'
    ]);
}
