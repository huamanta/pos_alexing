<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . "/../modelos/HistorialPagos.php";

$historialPagos = new HistorialPagos();

$op = isset($_GET["op"]) ? limpiarCadena($_GET["op"]) : "";
$idsucursal = $_SESSION['idsucursal'] ?? null;

switch ($op) {
    case 'listar':
        $fecha_inicio = isset($_GET['fecha_inicio']) ? limpiarCadena($_GET['fecha_inicio']) : null;
        $fecha_fin = isset($_GET['fecha_fin']) ? limpiarCadena($_GET['fecha_fin']) : null;
        $historialPagos->listarHistorialPagos($idsucursal, $fecha_inicio, $fecha_fin);
        break;

    default:
        echo json_encode(["error" => "Operación no válida"]);
        break;
}
