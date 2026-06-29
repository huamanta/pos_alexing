<?php
require '../modelos/Configuracion.php';
$configuracion = new Configuracion();
session_start();
switch ($_GET['op']) {
    case 'listarConfiguracion':
        $idsucursal = $_SESSION['idsucursal'];
        $res = $configuracion->listarConfiguracion($idsucursal);
        echo $res;
        break;

    case 'actualizarConfiguracionMora':
        $idsucursal = $_SESSION['idsucursal'];
        $is_mora_credito = !empty($_POST['is_mora_credito']) ? 1 : 0;
        $valor_mora = $_POST['valor_mora'];
        $res = $configuracion->actualizarConfiguracionMora($idsucursal, $is_mora_credito, $valor_mora);
        echo $res;
        break;
    
    default:
        # code...
        break;
}