<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . "/../modelos/facturacion/Bancos.php";
$bancos = new Bancos();
$op = $_GET['op'];
$idsucursal = $_SESSION['idsucursal'];

switch ($op) {
    case 'listar':
        $bancos->listar();
        break;

    case 'listarMovimientos':
        $idbanco = $_GET['idbanco'];
        $bancos->listarMovimientos($idbanco);
        break;
    
    default:
        echo 'No existe la opcion';
        break;
}