<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../modelos/MetodosPago.php';

$metodosPago = new MetodosPago();
$op = $_GET['op'];

switch ($op) {
    case 'listar':
        $metodosPago->listarMetodosPago();
        break;

    case 'listarDetalleMetodo':
        $metodo = $_GET['nombre'];
        $metodosPago->listarDetalleMetodo($metodo);
        break;

    default:
        echo "Operación no válida";
}