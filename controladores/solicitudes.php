<?php

require_once "../modelos/Solicitudes.php";
session_start();

$credito = new Solicitudes();

switch ($_GET["op"]) {

    case 'listarSolicitudes':

        $draw   = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
        $start  = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset($_GET['length']) ? intval($_GET['length']) : 10;

        $search = "";

        if (isset($_GET['search']['value'])) {
            $search = $_GET['search']['value'];
        }

        $estado = isset($_GET['estado'])
            ? limpiarCadena($_GET['estado'])
            : "";

        $riesgo = isset($_GET['riesgo'])
            ? limpiarCadena($_GET['riesgo'])
            : "";

        $paso = isset($_GET['paso'])
            ? limpiarCadena($_GET['paso'])
            : "";

        $texto = isset($_GET['texto'])
            ? limpiarCadena($_GET['texto'])
            : "";

        $idsucursal = $_SESSION['idsucursal'];

        $result = $credito->listarSolicitudes(
            $idsucursal,
            $search,
            $start,
            $length,
            $estado,
            $riesgo,
            $paso,
            $texto
        );

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $result["recordsTotal"],
            "recordsFiltered" => $result["recordsFiltered"],
            "data" => $result["data"]
        ]);

    break;

    case 'guardar':

        $idcliente = isset($_POST['idcliente'])
            ? limpiarCadena($_POST['idcliente'])
            : "";

        $ingreso_mensual = isset($_POST['ingreso_mensual'])
            ? limpiarCadena($_POST['ingreso_mensual'])
            : 0;

        $inicial = isset($_POST['inicial'])
            ? limpiarCadena($_POST['inicial'])
            : 0;

        $observacion = isset($_POST['observacion'])
            ? limpiarCadena($_POST['observacion'])
            : "";

        $idcotizacion = isset($_POST['idcotizacion']) ? limpiarCadena($_POST['idcotizacion']) : "";

        $idusuario = $_SESSION['idusuario'];

        $idsucursal = $_SESSION['idsucursal'];

        echo $credito->guardar(
            $idcliente,
            $idcotizacion,
            $ingreso_mensual,
            $inicial,
            $observacion,
            $idusuario,
            $idsucursal
        );

    break;

    case 'mostrarSolicitud':
        $idsolicitud = isset($_GET['idsolicitud'])
            ? intval($_GET['idsolicitud'])
            : 0;
        echo $credito->mostrar($idsolicitud);

    break;

    case 'workflow':

        $idsolicitud = isset($_POST['idsolicitud'])
            ? intval($_POST['idsolicitud'])
            : 0;

        echo $credito->workflow($idsolicitud);

    break;

    case 'archivos':

        $idsolicitud = isset($_POST['idsolicitud'])
            ? intval($_POST['idsolicitud'])
            : 0;

        echo $credito->archivos($idsolicitud);

    break;

    case 'kpis':

        echo $credito->kpis();

    break;

}

?>