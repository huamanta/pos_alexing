<?php
require_once "Helpers.php";
$numeroContrato = "C000000001";
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cronograma de Pagos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .container {
            width: 100%;
            margin: auto;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
        }

        .sub {
            font-size: 12px;
        }

        .info {
            margin-top: 15px;
        }

        .info table {
            width: 100%;
        }

        .info td {
            padding: 3px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table th, .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .table th {
            background: #f2f2f2;
        }

        .section-title {
            margin-top: 20px;
            font-weight: bold;
            border-bottom: 1px solid #000;
        }

        .totales {
            margin-top: 15px;
        }

        .totales td {
            padding: 4px;
        }

        .footer {
            margin-top: 20px;
            font-size: 11px;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div class="title">CRONOGRAMA DE PAGOS</div>
        <div class="sub">CONTRATO C000001231</div>
        <div class="sub">Generado: 29/03/2026</div>
    </div>

    <!-- CLIENTE -->
    <div class="info">
        <table>
            <tr>
                <td><strong>Cliente:</strong> ERIKA SOLANGE FLORES LOZANO</td>
                <td><strong>DNI/RUC:</strong> 70240377</td>
            </tr>
            <tr>
                <td><strong>Teléfono:</strong> 968754886</td>
                <td><strong>Dirección:</strong> JR. AVIACIÓN Nº 164</td>
            </tr>
            <tr>
                <td><strong>Monto Total:</strong> S/ 0.00</td>
                <td><strong>Inicial:</strong> S/ 0.00</td>
            </tr>
        </table>
    </div>

    <!-- PRODUCTO -->
    <div class="section-title">Detalle del Producto</div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Placa</th>
                <th>Color</th>
                <th>Serie</th>
                <th>Motor</th>
                <th>Condición</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>JIAPENG URBAN 110S</td>
                <td>JIAPENG</td>
                <td>URBAN 110S</td>
                <td>-</td>
                <td>ROSADO</td>
                <td>H93X3JLP2T5900522</td>
                <td>JP1P52FMH2601202113</td>
                <td>NUEVO</td>
            </tr>
        </tbody>
    </table>

    <!-- CRONOGRAMA -->
    <div class="section-title">Cronograma</div>

    <table class="table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Tipo</th>
                <th>Fec. Venc.</th>
                <th>Fec. Pago</th>
                <th>Tipo Pago</th>
                <th>Cuota</th>
                <th>Moras</th>
                <th>Total</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="9">Sin registros</td>
            </tr>
        </tbody>
    </table>

    <!-- TOTALES -->
    <div class="totales">
        <table>
            <tr>
                <td><strong>Total Cuotas:</strong> S/ 0.00</td>
                <td><strong>Total Moras:</strong> S/ 0.00</td>
                <td><strong>Total Pagos:</strong> S/ 0.00</td>
                <td><strong>Saldo:</strong> S/ 0.00</td>
            </tr>
        </table>
    </div>

    <!-- RESUMEN -->
    <div class="footer">
        <p><strong>Letras atrasadas:</strong> 0 | <strong>Monto atrasado:</strong> S/ 0.00</p>
        <p><strong>Letras pendientes:</strong> 0 | <strong>Total pendiente:</strong> S/ 0.00</p>

        <br>

        <table width="100%">
            <tr>
                <td><strong>Monto Pagado:</strong> S/ 0.00</td>
                <td><strong>Saldo por pagar:</strong> S/ 0.00</td>
            </tr>
        </table>
    </div>

</div>

</body>
<?php
$html = ob_get_clean();
require_once __DIR__ . '/../../reportes/factura/pdf/vendor/autoload.php';

$options = new \Dompdf\Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new \Dompdf\Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('cronograma_pagos_' . $numeroContrato . '.pdf', array('Attachment' => 0));
exit;

?>
</html>