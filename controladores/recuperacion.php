<?php
session_start();
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once "../modelos/venta/Recuperacion.php";
$recuperacion = new Recuperacion();

switch ($_GET["op"]) {
    case 'listarCandidatos':
        $idsucursal = $_SESSION['idsucursal'];
        $resp = $recuperacion->listarCandidatosRecuperacion($idsucursal);
        echo $resp;
        break;

    case 'verCandidato':
        $idventa = $_GET['idventa'];
        $resp = $recuperacion->verCandidato($idventa);
        echo $resp;
        break;

    case 'registrar':
        $idusuario = $_POST['idusuario'];
        $idventa = $_POST['idventa'];
        $idpersona = $_POST['idpersona'];
        $idserie = $_POST['idserie'];
        $resp = $recuperacion->registrarRecuperacion($idusuario, $idventa, $idpersona, $idserie);
        echo $resp;
        break;

    case 'listarRecuperaciones':
        $idsucursal = $_SESSION['idsucursal'];
        $estado = $_GET['estado'] ?? null;
        $resp = $recuperacion->listarRecuperaciones($idsucursal, $estado);
        echo $resp;
        break;

    case 'verRecuperacion':
        $idsucursal = $_SESSION['idsucursal'];
        $idrecuperacion = $_GET['idrecuperacion'];
        $resp = $recuperacion->verRecuperacion($idsucursal, $idrecuperacion);
        echo $resp;
        break;

    case 'listarCompromisos':
        $idsucursal = $_SESSION['idsucursal'];
        $resp = $recuperacion->listarCompromisos($idsucursal);
        echo $resp;
        break;

    case "cumplirCompromiso":
        $idcompromiso_pago = $_POST["idcompromiso_pago"];
        echo $recuperacion->cumplirCompromiso($idcompromiso_pago);
        break;

    case "verCompromiso":
        $idcompromiso_pago = $_GET["idcompromiso_pago"];
        echo $recuperacion->verCompromiso($idcompromiso_pago);
        break;

    case "eliminarCompromiso":
        $idcompromiso_pago = $_POST["idcompromiso_pago"];
        echo $recuperacion->eliminarCompromiso($idcompromiso_pago);
        break;

    case "actualizarEstadoRecuperacion":
        $idrecuperacion = $_POST["idrecuperacion"];
        $estado = $_POST["estado"];
        $observacion = $_POST["observacion"] ?? "";
        echo $recuperacion->actualizarEstadoRecuperacion($idrecuperacion, $estado, $observacion);
        break;

    case "guardarDocumento":
        echo $recuperacion->guardarDocumento(
            $_POST,
            $_FILES["archivo"]
        );
        break;
}