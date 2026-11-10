<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once "../modelos/Cajas.php";

$caja = new Cajas();

$idcaja = isset($_POST["idcaja"]) ? limpiarCadena($_POST["idcaja"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$numero = isset($_POST["numero"]) ? limpiarCadena($_POST["numero"]) : "";
$cargo = $_SESSION['cargo'];
$idsucursal = $_SESSION['idsucursal'];
$idusuario = $_SESSION['idusuario'];

switch ($_GET["op"]) {

    case 'guardaryeditar':
        // Forzar sucursal desde la sesión
        $idsucursal = $_SESSION['idsucursal'];
        if (empty($idcaja)) {
            $caja->insertar($nombre, $numero, $idsucursal);
        } else {
            $caja->editar($idcaja, $nombre, $numero);
        }
        break;

    case 'listar':
        $caja->listar($idsucursal);
        break;

    case 'desactivar':
        $caja->desactivar($idcaja);
        break;

    case 'activar':
        $caja->activar($idcaja);
        break;

    case 'mostrar':
        $caja->mostrar($idcaja);
        break;

    case 'historialcajas':
        $idcaja = isset($_REQUEST["idcaja"]) ? limpiarCadena($_REQUEST["idcaja"]) : "";
        $idusuario = $_SESSION['idusuario'];
        $caja->historialCajas($idcaja, $idusuario);
        break;

    case 'verificarCajaCierre':
        $aperturacajaid = $_REQUEST["aperturacajaid"];

    case 'listarMovimientosPorApertura':
        $aperturacajaid = $_REQUEST["aperturacajaid"];
        $caja->listarPorApertura($aperturacajaid);
        break;


    case 'listarCobrosPorApertura':
        $aperturacajaid = $_REQUEST["aperturacajaid"];
        $caja->listarCobrosPorApertura($aperturacajaid);
        break;

    case 'listarPagosPorApertura':
        $aperturacajaid = $_REQUEST["aperturacajaid"];
        $caja->listarPagosPorApertura($aperturacajaid);
        break;
}
?>