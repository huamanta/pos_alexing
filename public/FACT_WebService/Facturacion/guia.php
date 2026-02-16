<?php
require_once 'vendor/autoload.php';

use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Despatch\Despatch;
use Greenter\Model\Despatch\DespatchDetail;
use Greenter\Model\Despatch\Shipment;
use Greenter\Ws\Services\SunatEndpoints;

require_once 'Util.php';

$idguia = $_GET['idguia'];

$util = Util::getInstance();
$conexion = $util->abrirConexion();
mysqli_set_charset($conexion, "utf8");

if ($conexion) {
    $sql = "SELECT g.*, c.nombre as cliente_nombre, c.num_documento as cliente_doc, t.nombre as transportista_nombre, t.num_documento as transportista_doc
            FROM guia_remision g
            INNER JOIN persona c ON g.idcliente = c.idpersona
            INNER JOIN persona t ON g.idtransportista = t.idpersona
            WHERE g.idguia = '$idguia'";
    $resultado = mysqli_query($conexion, $sql);
    $guia = $resultado->fetch_assoc();

    $sql_detalles = "SELECT * FROM detalle_guia WHERE idguia = '$idguia'";
    $resultado_detalles = mysqli_query($conexion, $sql_detalles);

    $almacen = mysqli_query($conexion, "SELECT * from sucursal where idsucursal = '" . $guia['idsucursal'] . "'");
    $datos_sucursal = $almacen->fetch_assoc();

    $empresadatos = mysqli_query($conexion, "SELECT * from datos_negocio");
    $datos_negocio = $empresadatos->fetch_assoc();

    mysqli_close($conexion);

    $client = new Client();
    $client->setTipoDoc('6') // RUC
        ->setNumDoc($guia['cliente_doc'])
        ->setRznSocial($guia['cliente_nombre']);

    $companyAdress = new Address();
    $companyAdress->setUbigueo($datos_sucursal['ubigeo'])
        ->setDistrito($datos_sucursal['distrito'])
        ->setProvincia($datos_sucursal['provincia'])
        ->setDepartamento($datos_sucursal['departamento'])
        ->setDireccion($datos_sucursal['direccion']);

    $company = new Company();
    $company->setRuc($datos_negocio['documento'])
        ->setNombreComercial($datos_negocio['nombre'])
        ->setRazonSocial($datos_negocio['nombre'])
        ->setAddress($companyAdress);

    $detalles = [];
    while ($detalle = $resultado_detalles->fetch_assoc()) {
        $item = new DespatchDetail();
        $item->setCantidad($detalle['cantidad'])
            ->setUnidad('NIU')
            ->setDescripcion($detalle['nombre_producto'])
            ->setCodigo($detalle['codigo']);
        $detalles[] = $item;
    }

    $shipment = new Shipment();
    $shipment->setCodTraslado($guia['idmotivo']) // Cat. 20
        ->setModTraslado($guia['tipo_transporte'] == '0' ? '01' : '02') // 01: Publico, 02: Privado
        ->setFecTraslado(new DateTime($guia['fecha_traslado']))
        ->setIndTransbordo(false)
        ->setPesoTotal($guia['peso'])
        ->setUndPesoTotal('KGM')
        ->setPartida(new Address($guia['punto_partida'], $guia['ubigeo_partida']))
        ->setLlegada(new Address($guia['punto_llegada'], $guia['ubigeo_llegada']));

    if ($guia['tipo_transporte'] == '0') { // Transporte Publico
        $transportista = new Client();
        $transportista->setTipoDoc('6') // RUC
            ->setNumDoc($guia['transportista_doc'])
            ->setRznSocial($guia['transportista_nombre']);
        $shipment->setTransportista($transportista);
    } else { // Transporte Privado
        // Conductor y Vehiculo (se necesitarían más campos en la BD)
    }

    $despatch = new Despatch();
    $despatch->setTipoDoc('09') // Guia de Remision
        ->setSerie($guia['serie_comprobante'])
        ->setCorrelativo($guia['num_comprobante'])
        ->setFechaEmision(new DateTime($guia['fecha_emision']))
        ->setCompany($company)
        ->setDestinatario($client)
        ->setEnvio($shipment)
        ->setDetails($detalles);
        
    $endpoint = $datos_negocio['estado_certificado'] == "BETA" ? SunatEndpoints::GUIA_REMISION_BETA : SunatEndpoints::GUIA_REMISION_PRODUCCION;
    $see = $util->getSee($endpoint, $datos_negocio['estado_certificado']);

    $res = $see->send($despatch);
    $util->writeXml($despatch, $see->getFactory()->getLastXml());

    if ($res->isSuccess()) {
        $cdr = $res->getCdrResponse();
        $util->writeCdr($despatch, $res->getCdrZip());
        $hash = $see->getFactory()->getLastXml();

        $dom = new DOMDocument;
        $dom->loadXML($hash);
        $digest = $dom->getElementsByTagName('DigestValue')->item(0)->nodeValue;

        $conexion = $util->abrirConexion();
        $sql = "UPDATE guia_remision SET estado = 'Aceptado', hash_cpe = '$digest', resumen_sunat = '" . $cdr->getDescription() . "' WHERE idguia = '$idguia'";
        mysqli_query($conexion, $sql);
        mysqli_close($conexion);
        
        $util->showResponse($despatch, $cdr);
    } else {
        echo $util->getErrorResponse($res->getError());
    }
}
?>
