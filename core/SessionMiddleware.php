<?php
require_once __DIR__ . '/../core/Response.php';

$controlador = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
$operacion = $_GET['op'] ?? '';

$rutasPublicas = [
    'usuario.php' => ['verificar'],
    'generaFacturaCoti.php' => true
];

$esRutaPublica = $rutasPublicas[$controlador] ?? false;

if (is_array($esRutaPublica)) {
    $esRutaPublica = in_array($operacion, $esRutaPublica, true);
}

if (
    !$esRutaPublica &&
    empty($_SESSION['idusuario'])
) {
    Response::json([
        'status' => 0,
        'message' => 'Sesión expirada. Inicie sesión nuevamente para continuar.'
    ]);
}