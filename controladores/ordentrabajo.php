<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . "/../modelos/OrdenTrabajo.php";
$ordentrabajo = new OrdenTrabajo();
$idsucursal = $_SESSION['idsucursal'];
$op = $_GET['op'];

switch ($op) {
    case 'selectPersonal':
        echo $ordentrabajo->selectPersonal($idsucursal);
        break;
    
    default:
        # code...
        break;
}