<?php
require_once __DIR__ . '/../../configuraciones/bootstrap.php';
require_once __DIR__ . "/../../configuraciones/Conexion.php";
require_once __DIR__ . "/../../modelos/Helpers.php";
require_once __DIR__ . "/../../modelos/Cotizaciones.php";
require_once __DIR__ . "/../../modelos/Negocio.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$cotizacion = new Cotizacion();
$negocio = new Negocio();
$helpers = new Helpers();

if (empty($_GET["id"]) || !ctype_digit((string) $_GET["id"])) {
    http_response_code(400);
    echo "No es posible generar la factura.";
    exit();
}

$idventa = (int) $_GET["id"];
$anulada = '';
$factura = $cotizacion->listarDataCotizacion($idventa);

if (empty($factura)) {
    http_response_code(404);
    echo "Cotización no encontrada.";
    exit();
}

$configuracion = $negocio->listar($factura['idsucursal']);
$detalles = $cotizacion->listarDataDetalleCotizacion($idventa);
$currency = $helpers->get_currency_code($factura['idsucursal']);
$calculo30Dias = $helpers->verificarMes30Dias($factura['idsucursal']);

ob_start();
include(dirname('__FILE__') . '/facturaCoti.php');
$html = ob_get_clean();

// instantiate and use the dompdf class

$options = new Options();
$options->setIsRemoteEnabled(true);
$options->setChroot(realpath(__DIR__ . '/../../'));

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('letter', 'portrait');
// Render the HTML as PDF
$dompdf->render();
// Output the generated PDF to Browser
$dompdf->stream('Cotización_N°_' . $factura['serie_comprobante'] . '-' . $factura['num_comprobante'] . '.pdf', array('Attachment' => 0));
exit;