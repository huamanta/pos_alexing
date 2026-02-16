<?php
require_once 'vendor/autoload.php';
require_once __DIR__ . '/src/Util.php';

use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;
use Greenter\Ws\Services\SunatEndpoints;

date_default_timezone_set('America/Lima');

$util = Util::getInstance();
$conexion = $util->abrirConexion();

if (!$conexion) {
    echo json_encode(['success' => false, 'message' => 'Error al conectar a la base de datos.']);
    exit;
}

$idguia = isset($_POST['idguia']) ? $_POST['idguia'] : null;
$motivo = isset($_POST['motivo']) ? $_POST['motivo'] : 'Error en los datos';

if (empty($idguia)) {
    echo json_encode(['success' => false, 'message' => 'No se ha seleccionado la guía para dar de baja.']);
    exit;
}

$guia_query = mysqli_query($conexion, "SELECT * FROM guia_remision WHERE idguia = $idguia");
$guia = mysqli_fetch_assoc($guia_query);
$idsucursal = $guia['idsucursal'];

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
    ->setDireccion($datos_sucursal['direccion']);

$company->setRuc($datos_empresa['documento'])
    ->setRazonSocial($datos_empresa['nombre'])
    ->setAddress($address);

$voided = new Voided();
$voided->setCorrelativo('1') // Correlativo del día
    ->setFecGeneracion(new DateTime())
    ->setFecComunicacion(new DateTime())
    ->setCompany($company);

$detail = new VoidedDetail();
$detail->setTipoDoc('09') // Guia de Remision
    ->setSerie($guia['serie_comprobante'])
    ->setCorrelativo($guia['num_comprobante'])
    ->setDescrMotivo($motivo);

$voided->setDetails([$detail]);

try {
    if ($datos_empresa["estado_certificado"] == "BETA") {
        $see = $util->getSee(SunatEndpoints::FE_BETA, $datos_empresa["estado_certificado"]);
    } elseif ($datos_empresa["estado_certificado"] == "PRODUCCION") {
        $see = $util->getSee(SunatEndpoints::FE_PRODUCCION, $datos_empresa["estado_certificado"]);
    }

    $res = $see->send($voided);
    $util->writeXml($voided, $see->getFactory()->getLastXml());

    if ($res->isSuccess()) {
        $ticket = $res->getTicket();
        
        $sql_update_guia = "UPDATE guia_remision SET estado = 'Anulado', estado_sunat = '3', ticket_baja = '$ticket' WHERE idguia = $idguia";
        mysqli_query($conexion, $sql_update_guia);

        mysqli_close($conexion);
        echo json_encode(['success' => true, 'ticket' => $ticket]);
    } else {
        mysqli_close($conexion);
        $error = $util->getErrorResponse($res->getError());
        echo json_encode(['success' => false, 'message' => $error]);
    }

} catch (Exception $e) {
    mysqli_close($conexion);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
