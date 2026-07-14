<?php
session_start();
date_default_timezone_set('America/Lima');
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
        // 🔒 Forzar sucursal desde la sesión
        $idsucursal = $_SESSION['idsucursal'];

        if (empty($idcaja)) {
            $rspta = $caja->insertar($nombre, $numero, $idsucursal);
            echo $rspta ? "Caja registrada correctamente" : "No se pudo registrar la caja";
        } else {
            $rspta = $caja->editar($idcaja, $nombre, $numero);
            echo $rspta ? "Caja actualizada correctamente" : "No se pudo actualizar la caja";
        }
        break;

    case 'listar':

        $rspta = $caja->listar($idsucursal);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            if ($reg->estado == 1) {
                $estado = '<span class="badge bg-green">ACTIVADO</span>';
            } elseif ($reg->estado == 2) {
                $estado = '<span class="badge bg-blue">ABIERTO</span>';
            } else {
                $estado = '<span class="badge bg-red">DESACTIVADO</span>';
            }

            $btnDesactivar = '<button class="btn btn-danger btn-xs" onclick="desactivar(' . $reg->idcaja . ')"><i class="fas fa-times-circle"></i></button>';
            if (!$reg->estado) {
                $btnDesactivar = ' <button class="btn btn-primary btn-xs" onclick="activar(' . $reg->idcaja . ')"><i class="fa fa-check"></i></button>';
            }
            ;
            $data[] = array(
                "0" => $reg->numero,
                "1" => $reg->nombre,
                "2" => $reg->personal,
                "3" => $reg->almacen,
                "4" => $estado,
                "5" => '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idcaja . ')"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-info btn-xs" onclick="historialCaja(' . $reg->idcaja . ')"><i class="fas fa-list"></i></button> '. $btnDesactivar

            );
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'desactivar':
        $rspta = $caja->desactivar($idcaja);
        echo $rspta;
        break;

    case 'activar':
        $rspta = $caja->activar($idcaja);
        echo $rspta ? "Caja activada" : "No se pudo activar la caja";
        break;

    case 'mostrar':
        $rspta = $caja->mostrar($idcaja);
        echo json_encode($rspta);
        break;

    case 'historialcajas':
        $limit = isset($_REQUEST["limit"]) ? limpiarCadena($_REQUEST["limit"]) : "";
        $offset = isset($_REQUEST["offset"]) ? limpiarCadena($_REQUEST["offset"]) : "";
        $idcaja = isset($_REQUEST["idcaja"]) ? limpiarCadena($_REQUEST["idcaja"]) : "";
        $rspta = $caja->historialCajas($idcaja, $limit, $offset);
        echo json_encode($rspta);
        break;

    case 'verificarCajaCierre':
        $aperturacajaid = $_REQUEST["aperturacajaid"];

    case 'listarMovimientosPorApertura':
        $aperturacajaid = $_REQUEST["aperturacajaid"];
        $rspta = $caja->listarPorApertura($aperturacajaid);
        echo $rspta;
        break;


    case 'listarCobrosPorApertura':
        $aperturacajaid = $_REQUEST["aperturacajaid"];
        $rspta = $caja->listarCobrrosPorApertura($aperturacajaid);
        echo json_encode($rspta);
        break;

    case 'listarPagosPorApertura':
        $aperturacajaid = $_REQUEST["aperturacajaid"];
        $rspta = $caja->listarPagosPorApertura($aperturacajaid);
        echo json_encode($rspta);
        break;
}
?>