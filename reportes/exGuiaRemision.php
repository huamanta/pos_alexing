<?php

require_once __DIR__ . '/../configuraciones/bootstrap.php';
include __DIR__ . "/../configuraciones/Conexion.php";
require_once __DIR__ . "/../modelos/Guia.php";
require_once __DIR__ . "/../modelos/Negocio.php";
require_once __DIR__ . "/../modelos/Helpers.php";
$helpers = new Helpers();
$guiaClass = new Guia();
$negocio = new Negocio();

use Dompdf\Dompdf;
use Dompdf\Options;

if (empty($_GET["id"])) {
    echo "No es posible generar la guía.";
    exit();
}

$idguia = $_GET["id"];

// ================== CABECERA GUÍA ==================
$guia = $guiaClass->mostrarCabecera($idguia);

if (!$guia) {
    echo "No se encontró la guía.";
    exit();
}
$idsucursal = $guia['idsucursal'];

// ================== DATOS EMPRESA/SUCURSAL ==================
$configuracion = $negocio->listar($guia['idsucursal']);

// ================== DETALLE GUÍA ==================
$detalles = $guiaClass->listarDetalleTicket($guia['idguia']);


// ================== FUNCIONES IGUALES QUE EN FACTURA ==================
function buscarLotesData($iddetalle_guia): array
{
    global $conexion;
    $iddetalle_guia = (int) $iddetalle_guia;
    $sql = mysqli_query($conexion, "
        SELECT codigo_lote, fecha_vencimiento
        FROM detalle_guia_lote
        WHERE iddetalle_guia = $iddetalle_guia
    ");
    if (!$sql || mysqli_num_rows($sql) === 0)
        return [];
    $lotes = [];
    while ($row = mysqli_fetch_assoc($sql))
        $lotes[] = $row;
    return $lotes;
}

function buscarLotes($iddetalle_guia): string
{
    $lotes = buscarLotesData($iddetalle_guia);
    if (empty($lotes))
        return '';
    return implode(', ', array_column($lotes, 'codigo_lote'));
}

function buscarVencimientos($iddetalle_guia): string
{
    $lotes = buscarLotesData($iddetalle_guia);
    if (empty($lotes))
        return '';
    $fechas = [];
    foreach ($lotes as $l) {
        if (!empty($l['fecha_vencimiento'])) {
            $fechas[] = date('d/m/Y', strtotime($l['fecha_vencimiento']));
        }
    }
    return implode(', ', $fechas);
}

// ================== CAPTURAR HTML ==================
ob_start();
include(dirname(__FILE__) . '/guia.php');
$html = ob_get_clean();

// ================== RENDERIZAR PDF IGUAL QUE FACTURA ==================
$options = new Options();
$options->setIsRemoteEnabled(true);
$options->setChroot(realpath(__DIR__ . '/../../'));

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// ================== DESCARGA / VISTA ==================
$nombrePdf = 'GuiaRemision_' . $guia['serie_comprobante'] . '-' . $guia['num_comprobante'] . '.pdf';
$dompdf->stream($nombrePdf, ['Attachment' => 0]);

exit;