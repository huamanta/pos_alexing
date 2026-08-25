<?php

require_once __DIR__ . '/../../configuraciones/bootstrap.php';
require_once __DIR__ . "/../../configuraciones/Conexion.php";
require_once __DIR__ . "/../../modelos/Helpers.php";
require_once __DIR__ . "/../../modelos/Venta.php";
require_once __DIR__ . "/../../modelos/Negocio.php";
require_once __DIR__ . "/../../core/Constants.php";

use Dompdf\Dompdf;
use Dompdf\Options;
use Luecano\NumeroALetras\NumeroALetras;

$helpers = new Helpers();
$formatter = new NumeroALetras();
$venta = new Venta();
$negocio = new Negocio();

if (empty($_GET["id"])) {
    echo "No es posible generar la factura.";
    exit();
}

$idventa = $_GET["id"];
$anulada = '';


// ================== VENTA ==================
$factura = $venta->ventacabecera($idventa);
$configuracion = $negocio->listar($factura['idsucursal']);

// ================== CUENTAS ==================
$cuentasc = $venta->cuentasPorCobrar($idventa);

// ================== PAGOS ==================
$pagos = $venta->pagosPorVenta($idventa);
   

// ================== ESTADO ==================
if ($factura['estado'] == 'Nota Credito') {
    $anulada = '<img class="anulada" src="img/anulado.png" alt="Anulada">';
}

// ================== DETALLE ==================
$detalles = $venta->ventadetalle($idventa);

function buscarLotes($iddetalle_venta): string
{
    $venta = new Venta();
    $lotes = $venta->lotesProducto($iddetalle_venta);

    $resultado = [];

    foreach ($lotes as $lote) {
        $resultado[] = $lote['codigo_lote'];
    }

    return implode(', ', $resultado);
}

function buscarVencimientos($iddetalle_venta): string
{
    $venta = new Venta();
    $lotes = $venta->lotesProducto($iddetalle_venta);

    $resultado = [];

    foreach ($lotes as $lote) {

        if (!empty($lote['fecha_vencimiento'])) {

            $resultado[] = date(
                'd/m/Y',
                strtotime($lote['fecha_vencimiento'])
            );
        }
    }

    return implode(', ', $resultado);
}

// ================== HTML ==================
ob_start();
include(dirname(__FILE__) . '/factura.php');
$html = ob_get_clean();

// ================== PDF ==================

$options = new Options();
$options->setIsRemoteEnabled(true);
$options->setChroot(realpath(__DIR__ . '/../../'));

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// ================== NOMBRE DOC ==================
if ($factura['tipo_comprobante'] == 'NCB' || $factura['tipo_comprobante'] == 'NCF') {
    $tipodoc = 'Nota_de_Credito';
} else {
    $tipodoc = $factura['tipo_comprobante'];
}

$dompdf->stream(
    $tipodoc . '_N_' . $factura['serie_comprobante'] . '-' . $factura['num_comprobante'] . '.pdf',
    ['Attachment' => 0]
);

exit;