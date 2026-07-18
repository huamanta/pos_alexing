<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once "../modelos/Solicitudes.php";
$credito = new Solicitudes();

switch ($_GET["op"]) {
    case 'listarGeneralSolicitudes':
        $idsucursal = $_SESSION['idsucursal'];
        $result = $credito->listarGeneralSolicitudes($idsucursal);
        echo $result;
        break;

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

    case 'documentacion':
        $idsolicitud = isset($_POST['idsolicitud'])
            ? intval($_POST['idsolicitud'])
            : 0;
        $observacion = isset($_POST['observacion'])
            ? limpiarCadena($_POST['observacion'])
            : 'Documentación cargada';
        $observacion_evaluacion = isset($_POST['observacion_evaluacion'])
            ? limpiarCadena($_POST['observacion_evaluacion'])
            : '';
        $idusuario = $_SESSION['idusuario'];

        echo $credito->cargarDocumentacion(
            $idsolicitud,
            $observacion,
            $idusuario,
            $observacion_evaluacion
        );

    break;

    case 'avanzarPaso':
        $idsolicitud = isset($_POST['idsolicitud'])
            ? intval($_POST['idsolicitud'])
            : 0;
        $idpaso = isset($_POST['idpaso'])
            ? intval($_POST['idpaso'])
            : 0;
        $observacion = isset($_POST['observacion'])
            ? limpiarCadena($_POST['observacion'])
            : 'Avanzando al siguiente paso';
        $idusuario = $_SESSION['idusuario'];

        echo $credito->avanzarPaso(
            $idsolicitud,
            $idpaso,
            $observacion,
            $idusuario
        );

    break;

    case 'subirDocumento':
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            empty($_POST) &&
            empty($_FILES) &&
            isset($_SERVER['CONTENT_LENGTH']) &&
            $_SERVER['CONTENT_LENGTH'] > 0
        ) {

            echo json_encode([
                'status' => false,
                'msg' => 'El archivo excede el tamaño máximo permitido por el servidor.'
            ]);

            break;
        }

        $idsolicitud = isset($_POST['idsolicitud'])
            ? intval($_POST['idsolicitud'])
            : 0;
        $idusuario = $_SESSION['idusuario'];

        $tipo_documento = isset($_POST['tipo_documento'])
            ? limpiarCadena($_POST['tipo_documento'])
            : 'Documento';

        $descripcion = isset($_POST['descripcion'])
            ? limpiarCadena($_POST['descripcion'])
            : '';

        if (!isset($_FILES['archivo']) || empty($_FILES['archivo']['tmp_name'])) {
            echo json_encode([
                'status' => false,
                'msg' => 'No se recibió ningún archivo.'
            ]);
            break;
        }

        $uploadDir = "../files/solicitudes/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = $_FILES['archivo']['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeName = 'sol_' . $idsolicitud . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
        $targetPath = $uploadDir . $safeName;

        if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $targetPath)) {
            echo json_encode([
                'status' => false,
                'msg' => 'Error al subir el archivo.'
            ]);
            break;
        }

        if (!$credito->guardarDocumento($idsolicitud, $tipo_documento, $safeName, $originalName, $descripcion)) {
            echo json_encode([
                'status' => false,
                'msg' => 'No se pudo registrar el documento.'
            ]);
            break;
        }

        echo json_encode([
            'status' => true,
            'msg' => 'Documento subido correctamente'
        ]);

    break;

    case 'aprobarDocumentacion':
        $idsolicitud = isset($_POST['idsolicitud'])
            ? intval($_POST['idsolicitud'])
            : 0;
        $observacion = isset($_POST['observacion'])
            ? limpiarCadena($_POST['observacion'])
            : 'Documentación aprobada';
        $idusuario = $_SESSION['idusuario'];

        echo $credito->aprobarDocumentacion(
            $idsolicitud,
            $observacion,
            $idusuario
        );

    break;

    case 'observarSolicitud':
        $idsolicitud = isset($_POST['idsolicitud'])
            ? intval($_POST['idsolicitud'])
            : 0;
        $observacion = isset($_POST['observacion'])
            ? limpiarCadena($_POST['observacion'])
            : 'Solicitud observada';
        $idusuario = $_SESSION['idusuario'];

        echo $credito->marcarObservado(
            $idsolicitud,
            $observacion,
            $idusuario
        );

    break;

    case 'aprobarSolicitud':
        $idsolicitud = isset($_POST['idsolicitud'])
            ? intval($_POST['idsolicitud'])
            : 0;
        $observacion = isset($_POST['observacion'])
            ? limpiarCadena($_POST['observacion'])
            : 'Solicitud aprobada';
        $notas_comite = isset($_POST['notas_comite'])
            ? limpiarCadena($_POST['notas_comite'])
            : '';
        $idusuario = $_SESSION['idusuario'];

        echo $credito->aprobarSolicitud(
            $idsolicitud,
            $observacion,
            $idusuario,
            $notas_comite
        );

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

    case 'verificacionDomiciliaria':
        $idsolicitud = isset($_POST['idsolicitud'])
            ? intval($_POST['idsolicitud'])
            : 0;
        $resultado = isset($_POST['resultado'])
            ? limpiarCadena($_POST['resultado'])
            : '';
        $comentarios = isset($_POST['comentarios'])
            ? limpiarCadena($_POST['comentarios'])
            : '';
        $direccion_registrada = isset($_POST['direccion_registrada'])
            ? limpiarCadena($_POST['direccion_registrada'])
            : '';
        $idpaso = 4;
        $observacion = "Verificacion domiciliaria: $resultado. $comentarios";
        $idusuario = $_SESSION['idusuario'];

        if (!$credito->guardarVerificacionDomiciliaria($idsolicitud, $resultado, $comentarios, $idusuario, $direccion_registrada)) {
            echo json_encode([
                'status' => false,
                'msg' => 'No se pudo guardar la verificacion domiciliaria'
            ]);
            break;
        }

        if($resultado === 'CONFORME'){
            echo $credito->avanzarPaso($idsolicitud, $idpaso, $observacion, $idusuario);
            break;
        }

        echo json_encode([
            'status' => true,
            'msg' => 'Se ha guardado la verificacion con estado'. $resultado
        ]);

    break;

}

?>