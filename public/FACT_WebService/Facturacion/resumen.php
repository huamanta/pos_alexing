<?php
require_once 'vendor/autoload.php';
require_once __DIR__ . '/src/Util.php';

use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Ws\Services\SunatEndpoints;

date_default_timezone_set('America/Lima');

$util = Util::getInstance();
$conexion = $util->abrirConexion();

if (!$conexion) {
    echo json_encode(['success' => false, 'message' => 'Error al conectar a la base de datos.']);
    exit;
}

// Get data from POST
$idventas = isset($_POST['idventas']) ? $_POST['idventas'] : null;
$fecha_resumen = isset($_POST['fecha_resumen']) ? $_POST['fecha_resumen'] : date('Y-m-d');
$idsucursal = isset($_POST['idsucursal']) ? $_POST['idsucursal'] : null;


if (empty($idventas) || !is_array($idventas)) {
    echo json_encode(['success' => false, 'message' => 'No se han seleccionado boletas para el resumen.']);
    exit;
}

// 1. Get Company data
$empresa = mysqli_query($conexion, "SELECT * from datos_negocio");
$datos_empresa = mysqli_fetch_assoc($empresa);
$sucursal_data = mysqli_query($conexion, "SELECT * from sucursal where idsucursal = '" . $idsucursal . "'");
$datos_sucursal = mysqli_fetch_assoc($sucursal_data);

$company = new \Greenter\Model\Company\Company();
$address = new \Greenter\Model\Company\Address();
$address->setUbigueo($datos_sucursal['ubigeo'])
    ->setDistrito($datos_sucursal['distrito'])
    ->setProvincia($datos_sucursal['provincia'])
    ->setDepartamento($datos_sucursal['departamento'])
    ->setUrbanizacion('-')
    ->setCodLocal('0000')
    ->setDireccion($datos_sucursal['direccion']);

$company->setRuc($datos_empresa['documento'])
    ->setNombreComercial($datos_empresa['nombre'])
    ->setRazonSocial($datos_empresa['nombre'])
    ->setAddress($address);


// 2. Generate Summary number for the day
$sql_correlativo = "SELECT COUNT(*) as total FROM resumen_diario WHERE fecha_generacion = '$fecha_resumen'";
$res_correlativo = mysqli_query($conexion, $sql_correlativo);
$correlativo_row = mysqli_fetch_assoc($res_correlativo);
$correlativo = $correlativo_row['total'] + 1;

// 3. Create Summary object
$sum = new Summary();
$sum->setCorrelativo($correlativo)
    ->setFecGeneracion(new DateTime($fecha_resumen))
    ->setFecResumen(new DateTime($fecha_resumen))
    ->setCompany($company);

// 4. Loop through sales and add details
$detalles = [];
foreach ($idventas as $idventa) {
    $venta_query = mysqli_query($conexion, "SELECT v.*, p.tipo_documento as doc_tipo, p.num_documento as doc_num FROM venta v INNER JOIN persona p ON v.idcliente = p.idpersona WHERE v.idventa = $idventa");
    $venta = mysqli_fetch_assoc($venta_query);

    $det = new SummaryDetail();
    $det->setTipoDoc($venta['tipo_comprobante'] == 'Factura' ? '01' : '03')
        ->setSerieNro($venta['serie_comprobante'] . '-' . $venta['num_comprobante'])
        ->setClienteNro($venta['doc_num'])
        ->setClienteTipo($venta['doc_tipo'] === 'RUC' ? '6' : '1')
        ->setEstado('1') // 1: Adicionar
        ->setMtoOperGravadas(round($venta['total_venta'] / 1.18, 2)) // Asumiendo 18% IGV
        ->setMtoIGV(round($venta['total_venta'] - ($venta['total_venta'] / 1.18), 2))
        ->setTotal($venta['total_venta']);
    
    $detalles[] = $det;
}
$sum->setDetails($detalles);


// 5. Send to SUNAT
try {
    $debug_info = [];
    $debug_info['request_params'] = ['idventas' => $idventas, 'fecha_resumen' => $fecha_resumen, 'idsucursal' => $idsucursal];

    if  ($datos_empresa["estado_certificado"]=="BETA"){
        $see = $util->getSee(SunatEndpoints::FE_BETA, $datos_empresa["estado_certificado"]);
    }elseif($datos_empresa["estado_certificado"]=="PRODUCCION"){
        $see = $util->getSee(SunatEndpoints::FE_PRODUCCION, $datos_empresa["estado_certificado"]);
    }

    $res = $see->send($sum);
    $util->writeXml($sum, $see->getFactory()->getLastXml());

    if ($res->isSuccess()) {
        $ticket = $res->getTicket();
        $debug_info['generated_ticket'] = $ticket;

        // 6. Save to database
        $nombre_xml = $sum->getName();
        $sql_insert_resumen = "INSERT INTO resumen_diario (fecha_generacion, fecha_envio, correlativo, ticket, nombre_xml, idsucursal, estado) VALUES ('$fecha_resumen', NOW(), '$correlativo', '$ticket', '$nombre_xml', '$idsucursal', 'ENVIADO')";
        $debug_info['sql_insert_resumen'] = $sql_insert_resumen;
        $insert_resumen_result = mysqli_query($conexion, $sql_insert_resumen);
        $debug_info['insert_resumen_success'] = $insert_resumen_result;
        if ($insert_resumen_result === false) { $debug_info['insert_resumen_error'] = mysqli_error($conexion); }
        $idresumen = mysqli_insert_id($conexion);
        $debug_info['inserted_idresumen'] = $idresumen;


        foreach ($idventas as $idventa) {
            $sql_insert_detalle = "INSERT INTO resumen_diario_detalle (idresumen, idventa) VALUES ('$idresumen', '$idventa')";
            $debug_info['sql_insert_detalle'][] = $sql_insert_detalle;
            $insert_detalle_result = mysqli_query($conexion, $sql_insert_detalle);
            if ($insert_detalle_result === false) { $debug_info['insert_detalle_error'][] = mysqli_error($conexion); }


            $sql_update_venta = "UPDATE venta SET estado = 'En Resumen' WHERE idventa = $idventa";
            $debug_info['sql_update_venta'][] = $sql_update_venta;
            $update_venta_result = mysqli_query($conexion, $sql_update_venta);
            if ($update_venta_result === false) { $debug_info['update_venta_error'][] = mysqli_error($conexion); }
        }

        mysqli_close($conexion);
        echo json_encode(['success' => true, 'ticket' => $ticket, 'debug' => $debug_info]);

    } else {
        mysqli_close($conexion);
        $error = $util->getErrorResponse($res->getError());
        echo json_encode(['success' => false, 'message' => $error, 'debug' => $debug_info]);
    }

} catch (Exception $e) {
    mysqli_close($conexion);
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'debug' => $debug_info]);
}
?>