<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require '../modelos/Configuracion.php';
$configuracion = new Configuracion();
switch ($_GET['op']) {
    case 'listarConfiguracion':
        $idsucursal = $_GET['idsucursal'] ?? $_SESSION['idsucursal'];
        $res = $configuracion->listarConfiguracion($idsucursal);
        echo $res;
        break;

    case 'actualizarConfiguracionMora':
        $idsucursal = $_SESSION['idsucursal'];
        $is_mora_credito = !empty($_POST['is_mora_credito']) ? 1 : 0;
        $valor_mora_credito = $_POST['valor_mora_credito'];
        $res = $configuracion->actualizarConfiguracionMora($idsucursal, $is_mora_credito, $valor_mora_credito);
        echo $res;
        break;
    
    case 'actualizarConfiguracionCreditos':
        $idsucursal = $_SESSION['idsucursal'];
        $is_notificacion = !empty($_POST['is_notificacion']) ? 1 : 0;
        $dias_gracia = $_POST['dias_gracia'];
        $interes_defecto = $_POST['interes_defecto'];
        $is_descuento_anticipado = !empty($_POST['is_descuento_anticipado']) ? 1 : 0;
        $valor_descuento_anticipado = $_POST['valor_descuento_anticipado'];
        $dias_anticipacion = $_POST['dias_anticipacion'];
        $res = $configuracion->actualizarConfiguracionCreditos($idsucursal, $is_notificacion, $dias_gracia, $interes_defecto, $is_descuento_anticipado, $valor_descuento_anticipado, $dias_anticipacion);
        echo $res;
        break;

    case 'actualizarConfiguracionRefinanciamiento':
        $idsucursal = $_SESSION['idsucursal'];
        $is_refinanciamiento = !empty($_POST['is_refinanciamiento']) ? 1 : 0;
        $maximo_refinanciamientos = $_POST['maximo_refinanciamientos'];
        $res = $configuracion->actualizarConfiguracionRefinanciamiento($idsucursal, $is_refinanciamiento, $maximo_refinanciamientos);
        echo $res;
        break;

    default:
        # code...
        break;
}