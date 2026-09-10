<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../modelos/OrdenTrabajo.php';
require_once __DIR__ . '/../modelos/Helpers.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (strlen(session_id()) < 1) {
    session_start();
}

if (!isset($_SESSION['nombre'])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
    exit;
}

if (empty($_SESSION['idusuario'])) {
    echo 'No tiene permiso para visualizar el reporte';
    exit;
}

if (empty($_GET['id'])) {
    echo 'No es posible generar la orden de trabajo.';
    exit;
}

$ordenTrabajo = new OrdenTrabajo();
$helpers = new Helpers();
$data = $ordenTrabajo->dataOrdenTrabajo((int) $_GET['id']);

if (!$data || empty($data['ordenTrabajo'])) {
    echo 'Orden de trabajo no encontrada';
    exit;
}

$orden = $data['ordenTrabajo'];
$mecanicos = $data['mecanicos'] ?? [];
$repuestos = $data['repuestos'] ?? [];

$sucursal = $helpers->dataSucursal((int) ($orden['idsucursal'] ?? 0));
$empresa = $sucursal['nombre'] ?? 'Empresa';
$ndocumento = $sucursal['ruc'] ?? 'RUC';
$documento = $sucursal['documento'] ?? '';
$direccion = $sucursal['direccion'] ?? '';
$telefono = $sucursal['telefono'] ?? '';
$email = $sucursal['email'] ?? '';
$logoNombre = !empty($sucursal['logo']) ? $sucursal['logo'] : 'default.png';
$logoRuta = realpath(__DIR__ . '/../files/logos/' . $logoNombre);
$logo = ($logoRuta && file_exists($logoRuta)) ? 'file://' . $logoRuta : '';

$numeroOrden = $orden['numero'] ?? '---';
$tipoOrden = $orden['tipo'] ?? '---';
$estadoOrden = $orden['estado'] ?? '---';
$fechaInicio = $orden['fecha_inicio'] ?? '---';
$fechaFin = !empty($orden['fecha_fin']) ? $orden['fecha_fin'] : '---';
$vehiculoNombre = !empty($orden['productoRelacionado']['nombre']) ? $orden['productoRelacionado']['nombre'] : '---';
$observaciones = !empty($orden['observaciones']) ? $orden['observaciones'] : (!empty($orden['observaciones_costos']) ? $orden['observaciones_costos'] : 'Sin observaciones');

$listaMecanicos = '';
if (!empty($mecanicos)) {
    foreach ($mecanicos as $m) {
        $listaMecanicos .= '<tr><td>' . htmlspecialchars($m['nombre_personal'] ?? $m['nombre'] ?? '---', ENT_QUOTES, 'UTF-8') . '</td></tr>';
    }
} else {
    $listaMecanicos = '<tr><td>Sin mecánicos asignados</td></tr>';
}

$listaRepuestos = '';
$subtotalRepuestos = 0;
if (!empty($repuestos)) {
    foreach ($repuestos as $r) {
        $cantidad = (float) ($r['cantidad'] ?? 1);
        $precioUnitario = (float) ($r['precio_unitario'] ?? $r['precio'] ?? 0);
        $subtotalItem = (float) ($r['subtotal'] ?? ($cantidad * $precioUnitario));
        $subtotalRepuestos += $subtotalItem;

        $cantidadHtml = htmlspecialchars((string) $cantidad, ENT_QUOTES, 'UTF-8');
        $nombreHtml = htmlspecialchars($r['nombre_producto'] ?? $r['nombre'] ?? '---', ENT_QUOTES, 'UTF-8');
        $precioHtml = htmlspecialchars($helpers->get_currency_symbol($subtotalItem), ENT_QUOTES, 'UTF-8');
        $listaRepuestos .= '<tr><td style="width: 70px;">' . $cantidadHtml . '</td><td>' . $nombreHtml . '</td><td style="text-align:right; font-weight:bold;">' . $precioHtml . '</td></tr>';
    }
} else {
    $listaRepuestos = '<tr><td colspan="3">Sin repuestos agregados</td></tr>';
}

$otrosGastos = (float) ($orden['otros_gastos'] ?? 0) + (float) ($orden['servicios_externos'] ?? 0) + (float) ($orden['pintura'] ?? 0) + (float) ($orden['transporte'] ?? 0) + (float) ($orden['lavado'] ?? 0);
$manoObra = 0;
$totalEstimado = $subtotalRepuestos + $manoObra + $otrosGastos;

$subtotalRepuestosFmt = $helpers->get_currency_symbol($subtotalRepuestos);
$manoObraFmt = $helpers->get_currency_symbol($manoObra);
$otrosGastosFmt = $helpers->get_currency_symbol($otrosGastos);
$totalEstimadoFmt = $helpers->get_currency_symbol($totalEstimado);

$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 18px 22px 16px 22px;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #263238;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        table {
            border-collapse: collapse;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #0d47a1;
            margin-bottom: 10px;
        }
        .header td {
            vertical-align: top;
        }
        .logo-cell {
            width: 22%;
            padding: 4px 10px 6px 12px;
        }
        .logo-cell img {
            max-width: 125px;
            max-height: 82px;
        }
        .company-cell {
            width: 48%;
            padding: 2px 8px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #0d47a1;
            margin-bottom: 5px;
        }
        .company-meta {
            font-size: 9.5px;
            line-height: 1.45;
            color: #455a64;
        }
        .doc-box {
            width: 30%;
            border: 1.5px solid #0d47a1;
            text-align: center;
            border-radius: 8px;
            overflow: hidden;
        }
        .doc-title {
            background: #0d47a1;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            padding: 7px 4px;
        }
        .doc-number {
            font-size: 13px;
            color: #263238;
            padding: 8px 5px;
            font-weight: bold;
        }
        .doc-label {
            color: #0d47a1;
            font-size: 14px;
            font-weight: bold;
            padding: 7px 4px 1px;
        }
        .meta-grid {
            width: 100%;
            border: 1px solid #d7dee3;
            margin-bottom: 12px;
        }
        .meta-grid td {
            padding: 6px 8px;
            border: 1px solid #d7dee3;
            font-size: 10px;
            vertical-align: middle;
        }
        .meta-grid td.label {
            width: 18%;
            font-weight: bold;
            color: #37474f;
            background: #f3f6f9;
        }
        .section-title {
            background: #0d47a1;
            color: #fff;
            font-size: 10.5px;
            font-weight: bold;
            padding: 6px 8px;
            margin-top: 10px;
        }
        table.detail {
            width: 100%;
            border: 1px solid #cfd8dc;
        }
        table.detail th {
            background: #455a64;
            color: #fff;
            font-size: 10px;
            padding: 6px;
            text-align: left;
            border: 1px solid #455a64;
        }
        table.detail td {
            font-size: 10px;
            padding: 6px;
            border: 1px solid #dce3e7;
            vertical-align: top;
        }
        table.detail tbody tr:nth-child(even) {
            background: #f7f9fb;
        }
        .amount {
            text-align: right;
            white-space: nowrap;
        }
        .center {
            text-align: center;
        }
        .notes {
            border: 1px solid #d7dee3;
            padding: 8px;
            background: #f7f9fb;
            color: #455a64;
            line-height: 1.5;
            margin-top: 0;
        }
        .totals {
            width: 48%;
            margin: 10px 0 0 auto;
            border: 1px solid #cfd8dc;
        }
        .totals td {
            padding: 6px 8px;
            border: 1px solid #dce3e7;
            font-size: 10px;
        }
        .totals td.value {
            text-align: right;
            font-weight: bold;
        }
        .totals td.total-row {
            background: #0d47a1;
            color: white;
            font-size: 11px;
            font-weight: bold;
        }
        .signature-box {
            width: 100%;
            margin-top: 28px;
        }
        .signature-box td {
            width: 50%;
            text-align: center;
            padding-top: 24px;
            font-size: 10px;
            color: #455a64;
        }
        .line {
            border-top: 1px solid #263238;
            margin: 0 30px;
            padding-top: 6px;
        }
        .footer {
            margin-top: 14px;
            font-size: 9px;
            color: #607d8b;
            text-align: center;
            border-top: 1px solid #d7dee3;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="page">
        <table class="header">
            <tr>
                <td class="logo-cell">
                ' . (!empty($logo) ? '<img src="' . $logo . '" alt="logo">' : '') . '
                </td>
                <td class="company-cell">
                    <div class="company-name">' . htmlspecialchars($empresa, ENT_QUOTES, 'UTF-8') . '</div>
                    <div class="company-meta">
                    ' . htmlspecialchars($ndocumento, ENT_QUOTES, 'UTF-8') . ': ' . htmlspecialchars($documento, ENT_QUOTES, 'UTF-8') . '<br>
                    ' . htmlspecialchars($direccion, ENT_QUOTES, 'UTF-8') . '<br>
                    Tel: ' . htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') . '<br>
                    ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '
                    </div>
                </td>
                <td class="doc-box">
                    <div class="doc-title">' . htmlspecialchars($ndocumento, ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($documento, ENT_QUOTES, 'UTF-8') . '</div>
                    <div class="doc-label">ORDEN DE TRABAJO</div>
                    <div class="doc-number">N° ' . htmlspecialchars($numeroOrden, ENT_QUOTES, 'UTF-8') . '</div>
                </td>
            </tr>
        </table>

        <table class="meta-grid">
            <tr>
                <td class="label">Tipo</td>
                <td>' . htmlspecialchars($tipoOrden, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="label">Estado</td>
                <td>' . htmlspecialchars($estadoOrden, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td class="label">Fecha inicio</td>
                <td>' . htmlspecialchars($fechaInicio, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="label">Fecha fin</td>
                <td>' . htmlspecialchars($fechaFin, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td class="label">Vehículo</td>
                <td colspan="3">' . htmlspecialchars($vehiculoNombre, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
        </table>

        <div class="section-title">Mecánicos asignados</div>
        <table class="detail">
            <thead>
                <tr>
                    <th>Nombre del mecánico</th>
                </tr>
            </thead>
            <tbody>
                ' . $listaMecanicos . '
            </tbody>
        </table>

        <div class="section-title">Repuestos</div>
        <table class="detail">
            <thead>
                <tr>
                    <th style="width: 70px; text-align:center;">Cant.</th>
                    <th>Descripción</th>
                    <th style="width: 110px; text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                ' . $listaRepuestos . '
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td>Subtotal repuestos</td>
                <td class="value">' . htmlspecialchars($subtotalRepuestosFmt, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td>Mano de obra</td>
                <td class="value">' . htmlspecialchars($manoObraFmt, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td>Otros gastos</td>
                <td class="value">' . htmlspecialchars($otrosGastosFmt, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr>
                <td class="total-row">Total estimado</td>
                <td class="value total-row">' . htmlspecialchars($totalEstimadoFmt, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
        </table>

        <div class="section-title">Observaciones</div>
        <div class="notes">' . htmlspecialchars($observaciones, ENT_QUOTES, 'UTF-8') . '</div>

        <table class="signature-box">
            <tr>
                <td>
                    <div class="line">Cliente</div>
                </td>
                <td>
                    <div class="line">Responsable</div>
                </td>
            </tr>
        </table>

        <div class="footer">Documento generado por el sistema • Orden de trabajo</div>
    </div>
</body>
</html>';

$options = new Options();
$options->setIsRemoteEnabled(true);
$options->setChroot(realpath(__DIR__ . '/..'));

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A5', 'portrait');
$dompdf->render();

$nombrePdf = 'OrdenTrabajo_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $numeroOrden) . '.pdf';
$dompdf->stream($nombrePdf, ['Attachment' => 0]);
exit;