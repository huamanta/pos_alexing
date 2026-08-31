<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require '../modelos/Configuracion.php';
$configuracion = new Configuracion();
switch ($_GET['op']) {
    case 'listarConfiguracion':
        $idsucursal = $_SESSION['idsucursal'];
        $configuracion->listarConfiguracion($idsucursal);
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
        $configuracion->actualizarConfiguracionGeneral($idsucursal, $nombre, $telefono, $direccion, $email, $departamento, $provincia, $distrito, $ubigeo, $moneda);
        break;

    case 'actualizarConfiguracionMora':
        $idsucursal = $_SESSION['idsucursal'];
        $is_mora_credito = !empty($_POST['is_mora_credito']) ? 1 : 0;
        $valor_mora_credito = (float) ($_POST['valor_mora_credito'] ?? 0);
        $dias_gracia = (int) ($_POST['dias_gracia'] ?? 0);
        $configuracion->actualizarConfiguracionMora($idsucursal, $is_mora_credito, $valor_mora_credito, $dias_gracia);
        break;

    case 'actualizarConfiguracionCreditos':
        $idsucursal = $_SESSION['idsucursal'];
        $is_notificacion = !empty($_POST['is_notificacion']) ? 1 : 0;
        $is_calculo_mes = !empty($_POST['is_calculo_mes']) ? 1 : 0;
        $interes_defecto = (float) ($_POST['interes_defecto'] ?? 0);
        $is_descuento_anticipado = !empty($_POST['is_descuento_anticipado']) ? 1 : 0;
        $valor_descuento_anticipado = (float) ($_POST['valor_descuento_anticipado'] ?? 0);
        $dias_anticipacion = (int) ($_POST['dias_anticipacion'] ?? 0);
        $configuracion->actualizarConfiguracionCreditos($idsucursal, $is_notificacion, $is_calculo_mes, $interes_defecto, $is_descuento_anticipado, $valor_descuento_anticipado, $dias_anticipacion);
        break;

    case 'actualizarConfiguracionRefinanciamiento':
        $idsucursal = $_SESSION['idsucursal'];
        $is_refinanciamiento = !empty($_POST['is_refinanciamiento']) ? 1 : 0;
        $maximo_refinanciamientos = (int) ($_POST['maximo_refinanciamientos'] ?? 0);
        $configuracion->actualizarConfiguracionRefinanciamiento($idsucursal, $is_refinanciamiento, $maximo_refinanciamientos);
        break;

    case 'actualizarConfiguracionFacturacion':
        $idsucursal = $_SESSION['idsucursal'];
        $is_send_sunat = !empty($_POST['is_send_sunat']) ? 1 : 0;
        $ruc = $_POST['ruc'];
        $razon_social = $_POST['razon_social'];
        $monto_impuesto = $_POST['monto_impuesto'];
        $usuario_sol = $_POST['usuario_sol'] ?? '';
        $clave_sol = $_POST['clave_sol'] ?? '';
        $clave_certificado = $_POST['clave_certificado'] ?? '';
        $estado_certificado = $_POST['estado_certificado'];
        $configuracion->actualizarConfiguracionFacturacion($idsucursal, $is_send_sunat, $ruc, $razon_social, $monto_impuesto, $usuario_sol, $clave_sol, $_FILES['ruta_certificado'], $clave_certificado, $estado_certificado);
        break;

    default:
        # code...
        break;
}