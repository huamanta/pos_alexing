<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../modelos/MetodosPago.php';

$metodosPago = new MetodosPago();
$op = isset($_GET["op"]) ? limpiarCadena($_GET["op"]) : "";
$idsucursal = $_SESSION['idsucursal'] ?? null;

switch ($op) {
    case 'listar':
        $metodosPago->listarMetodosPago();
        break;

    case 'listarDetalleMetodo':
        $metodo = $_GET['nombre'];
        $metodosPago->listarDetalleMetodo($idsucursal, $metodo);
        break;

    default:
        echo "Operación no válida";
}