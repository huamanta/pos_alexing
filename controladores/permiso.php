<?php
session_start();
require_once "../modelos/Permiso.php";

$permiso = new Permiso();

$idpermiso = isset($_POST["idpermiso"]) ? limpiarCadena($_POST["idpermiso"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($idpermiso)) {
            echo $permiso->insertar($nombre) ? "Permiso registrado" : "No se pudo registrar";
        } else {
            echo $permiso->editar($idpermiso, $nombre) ? "Permiso actualizado" : "No se pudo actualizar";
        }
        break;

    case 'eliminar':
        echo $permiso->eliminar($idpermiso) ? "Permiso eliminado" : "No se pudo eliminar";
        break;

    case 'mostrar':
        echo json_encode($permiso->mostrar($idpermiso));
        break;

    case 'listar':
        $rspta = $permiso->listar();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $data[] = [
                "0" => '<button class="btn btn-warning" onclick="mostrar(' . $reg->idpermiso . ')"><i class="fas fa-edit"></i></button>' .
                       ' <button class="btn btn-danger" onclick="eliminar(' . $reg->idpermiso . ')"><i class="fa fa-trash"></i></button>',
                "1" => $reg->nombre
            ];
        }

        $results = [
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ];

        echo json_encode($results);
        break;

    // ================= SUBPERMISOS ==================

    case 'insertarsubpermiso':
    $idpermiso = isset($_POST["idpermiso"]) ? limpiarCadena($_POST["idpermiso"]) : null;
    $nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : null;

    if ($idpermiso && $nombre) {
        echo $permiso->insertarSubpermiso($idpermiso, $nombre);
    } else {
        echo "Error: Faltan datos (idpermiso o nombre)";
    }
    break;


    case 'listarsubpermiso':
        $rspta = $permiso->listarSubpermiso($_POST["idpermiso"]);
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = [
                "idsubpermiso" => $reg->idsubpermiso,
                "modulo" => $reg->modulo,
                "nombre" => $reg->nombre
            ];
        }
        echo json_encode($data);
        break;

    case 'eliminarsubpermiso':
        echo $permiso->eliminarSubpermiso($_POST["idsubpermiso"]);
        break;

    case 'insertaraccion':
        $idsubpermiso = isset($_POST["idsubpermiso"]) ? limpiarCadena($_POST["idsubpermiso"]) : null;
        $nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : null;
        $descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : '';

        if ($idsubpermiso && $nombre) {
            echo $permiso->insertarAccion($idsubpermiso, $nombre, $descripcion);
        } else {
            echo "Error: Faltan datos (idsubpermiso o nombre)";
        }
        break;

    case 'listaracciones':
        $rspta = $permiso->listarAcciones($_POST["idsubpermiso"]);
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = [
                "idaccion_permiso" => $reg->idaccion_permiso,
                "nombre" => $reg->nombre,
                "descripcion" => $reg->descripcion
            ];
        }
        echo json_encode($data);
        break;

    case 'eliminaraccion':
        echo $permiso->eliminarAccion($_POST["idaccion_permiso"]);
        break;


}
?>
