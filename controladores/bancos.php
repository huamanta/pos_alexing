<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . "/../modelos/facturacion/Bancos.php";
$bancos = new Bancos();
$op = $_GET['op'];
$idsucursal = $_SESSION['idsucursal'];
$idbanco = $_GET['idbanco'] ?? null;

switch ($op) {
    case 'listar':
        $bancos->listar();
        break;

    case 'listarMovimientos':
        $bancos->listarMovimientos($idbanco);
        break;

    case 'guardaryeditar':
        $data = json_decode(file_get_contents('php://input'), true);
        if(empty($idbanco)){
            $bancos->insertar($data);
        }else{
            $bancos->editar($idbanco, $data);
        }
        break;

    case 'mostrar':
        $bancos->mostrar($idbanco);
        break;

    case 'eliminar':
        $bancos->eliminar($idbanco);
        break;
    
    default:
        echo 'No existe la opcion';
        break;
}