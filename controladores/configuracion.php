<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require '../modelos/Configuracion.php';
$configuracion = new Configuracion();
switch ($_GET['op']) {
    case 'listarConfiguracion':
        $idsucursal = $_SESSION['idsucursal'];
        $res = $configuracion->listarConfiguracion($idsucursal);
        echo $res;
        break;

    case 'actualizarConfiguracionGeneral':
        $idsucursal = $_POST['idsucursal'];
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];
        $departamento = $_POST['departamento'];
        $provincia = $_POST['provincia'];
        $distrito = $_POST['distrito'];
        $ubigeo = $_POST['ubigeo'];
        $moneda = $_POST['moneda'];
        $res = $configuracion->actualizarConfiguracionGeneral($idsucursal, $nombre, $telefono, $direccion, $email, $departamento, $provincia, $distrito, $ubigeo, $moneda);
        echo $res;
        break;

    case 'actualizarConfiguracionMora':
        $idsucursal = $_SESSION['idsucursal'];
        $is_mora_credito = !empty($_POST['is_mora_credito']) ? 1 : 0;
        $valor_mora_credito = $_POST['valor_mora_credito'];
        $dias_gracia = $_POST['dias_gracia'];
        $res = $configuracion->actualizarConfiguracionMora($idsucursal, $is_mora_credito, $valor_mora_credito, $dias_gracia);
        echo $res;
        break;
    
    case 'actualizarConfiguracionCreditos':
        $idsucursal = $_SESSION['idsucursal'];
        $is_notificacion = !empty($_POST['is_notificacion']) ? 1 : 0;
        $is_calculo_mes = !empty($_POST['is_calculo_mes']) ? 1 : 0;
        $interes_defecto = $_POST['interes_defecto'];
        $is_descuento_anticipado = !empty($_POST['is_descuento_anticipado']) ? 1 : 0;
        $valor_descuento_anticipado = $_POST['valor_descuento_anticipado'];
        $dias_anticipacion = $_POST['dias_anticipacion'];
        $res = $configuracion->actualizarConfiguracionCreditos($idsucursal, $is_notificacion, $is_calculo_mes, $interes_defecto, $is_descuento_anticipado, $valor_descuento_anticipado, $dias_anticipacion);
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