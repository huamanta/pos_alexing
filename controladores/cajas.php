<?php
session_start();
date_default_timezone_set('America/Lima');
require_once "../modelos/Cajas.php";

$caja = new Cajas();

$idcaja = isset($_POST["idcaja"]) ? limpiarCadena($_POST["idcaja"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$numero = isset($_POST["numero"]) ? limpiarCadena($_POST["numero"]) : "";

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
        $cargo = $_SESSION['cargo'];
        $idsucursal = $_SESSION['idsucursal']; // 🔒 Filtrar por sucursal logueada

        $rspta = $caja->listar($cargo, $idsucursal);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            if ($reg->estado == 1) {
                $estado = '<span class="badge bg-green">ACTIVADO</span>';
            } elseif ($reg->estado == 2) {
                $estado = '<span class="badge bg-blue">ABIERTO</span>';
            } else {
                $estado = '<span class="badge bg-red">DESACTIVADO</span>';
            }

            $data[] = array(
                "0" => $reg->numero,
                "1" => $reg->nombre,
                "2" => $reg->personal,
                "3" => $reg->almacen,
                "4" => $estado,
                "5" => ($reg->estado)
                    ? '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idcaja . ')"><i class="fas fa-edit"></i></button>' .
                      ' <button class="btn btn-danger btn-xs" onclick="desactivar(' . $reg->idcaja . ')"><i class="fas fa-times-circle"></i></button>'
                    : '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idcaja . ')"><i class="fas fa-edit"></i></button>' .
                      ' <button class="btn btn-primary btn-xs" onclick="activar(' . $reg->idcaja . ')"><i class="fa fa-check"></i></button>'
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
        echo $rspta ? "Caja desactivada" : "No se pudo desactivar la caja";
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
        $fecha_inicio = isset($_REQUEST["fecha_inicio"]) ? limpiarCadena($_REQUEST["fecha_inicio"]) : "";
        $fecha_fin = isset($_REQUEST["fecha_fin"]) ? limpiarCadena($_REQUEST["fecha_fin"]) : "";
        $rspta = $caja->historialCajas($fecha_inicio, $fecha_fin);
        echo json_encode($rspta);
        break;

    case 'listarMovimientosPorApertura':
        $aperturacajaid = $_REQUEST["aperturacajaid"];
        $rspta = $caja->listarPorApertura($aperturacajaid);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->fecha,
                "1" => $reg->descripcion,
                "2" => ($reg->tipo == 'Egresos')
                        ? '<span class="badge bg-red">EGRESO</span>'
                        : '<span class="badge bg-green">INGRESO</span>',
                "3" => $reg->formapago,
                "4" => $reg->monto,
                "5" => '<div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                              <i class="fa fa-list-ul"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" onclick="mostrarMovimiento('.$reg->idmovimiento.')">Editar</a>
                              <a class="dropdown-item" onclick="eliminarMovimiento('.$reg->idmovimiento.')">Eliminar</a>
                            </div>
                         </div>',
            );
        }

        echo json_encode([
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ]);
        break;
}
?>
