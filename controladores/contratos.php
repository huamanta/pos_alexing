<?php
$op = $_GET['op'] ?? '';

switch ($op) {
    case 'listar':
        require_once "../modelos/Contratos.php";
        $contratos = new Contratos();
        $fecha_inicio = $_GET['fecha_inicio'] ?? '';
        $fecha_fin = $_GET['fecha_fin'] ?? '';
        $estado = $_GET['estado'] ?? '';
        $idsucursal = $_GET['idsucursal'] ?? '';

        $datos = $contratos->listar($fecha_inicio, $fecha_fin, $estado, $idsucursal);
        echo $datos;
        break;
}