<?php

require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../configuraciones/Conexion.php';
require_once __DIR__ . '/../modelos/Venta.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (empty($_GET['id'])) {
    http_response_code(400);
    echo 'No se indicó la venta.';
    exit;
}

$idventa = (int) $_GET['id'];
$formato = (($_GET['formato'] ?? 'ticket') === 'a4') ? 'a4' : 'ticket';

$ventaModel = new Venta();
$venta = $ventaModel->mostrar($idventa);

if (!$venta) {
    http_response_code(404);
    echo 'No se encontró la venta.';
    exit;
}

$totalVenta = (float) ($venta['total_venta'] ?? 0);

$inicial = (float) ($venta['totalrecibido'] ?? 0)
    + (float) ($venta['totaldeposito'] ?? 0);

$saldoPendiente = max($totalVenta - $inicial, 0);

$cliente = trim((string) ($venta['cliente'] ?? 'Cliente')) ?: 'Cliente';
$dni = trim((string) ($venta['num_documento'] ?? '')) ?: '-';

$fecha = trim(
    (string) ($venta['fecha_hora'] ?? $venta['fecha'] ?? date('d/m/Y H:i'))
);

$metodoPago = strtoupper(
    trim((string) ($venta['formapago'] ?? 'Efectivo'))
) ?: 'EFECTIVO';

$ventaRef = trim(
    (string) ($venta['serie_comprobante'] ?? '')
    . '-'
    . (string) ($venta['num_comprobante'] ?? '')
);

$ventaRef = $ventaRef === '-' ? '-' : $ventaRef;

function formatoMoneda(float $valor): string
{
    return 'S/ ' . number_format($valor, 2, '.', ',');
}

$isTicket = $formato === 'ticket';

$pageCss = $isTicket
    ? 'size: 58mm auto; margin: 0;'
    : 'size: A4 portrait; margin: 18mm;';

$bodyWidth = $isTicket ? '48mm' : '100%';

$bodyPadding = $isTicket
    ? '4px 2px'
    : '18px';

$titleSize = $isTicket ? '7px' : '26px';
$subtitleSize = $isTicket ? '8px' : '18px';
$bigSize = $isTicket ? '11px' : '34px';
$smallSize = $isTicket ? '6px' : '13px';
$normalSize = $isTicket ? '7px' : '12px';
$conceptSize = $isTicket ? '6px' : '13px';

$divider = $isTicket
    ? '1px dashed #222'
    : '1px solid #1f2937';

$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Constancia de Pago Inicial</title>

    <style>

        @page {
            ' . $pageCss . '
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #fff;
            color: #111;
        }

        body {
            width: ' . $bodyWidth . ';
            max-width: ' . $bodyWidth . ';
            margin: 0 auto;
            padding: ' . $bodyPadding . ';
            box-sizing: border-box;
            font-size: ' . $normalSize . ';
            line-height: 1.15;
        }

        .a4-card {
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
        }

        .center {
            text-align: center;
        }

        .titulo {
            font-weight: 700;
            letter-spacing: 0;
        }

        .linea {
            border-top: ' . $divider . ';
            margin: 4px 0;
        }

        .label {
            font-weight: 700;
        }

        .importe {
            font-size: ' . $bigSize . ';
            font-weight: 700;
            text-align: center;
            margin: 4px 0;
        }

        .small {
            font-size: ' . $smallSize . ';
        }

        .concepto {
            font-size: ' . $conceptSize . ';
        }

        table {
            width: 100%;
            max-width: 420px;
            border-collapse: collapse;
            margin: 0 auto;
        }

        td {
            padding: 1px 0;
            vertical-align: top;
        }

        td:first-child {
            width: 30%;
        }

        td:last-child {
            width: 70%;
            word-wrap: break-word;
            text-align: left;
        }

        .footer {
            font-size: ' . $smallSize . ';
            text-align: center;
            line-height: 1.15;
        }

    </style>
</head>

<body>

    <div class="a4-card">

        <div class="center">

            <div
                class="titulo"
                style="font-size:' . $titleSize . ';">
                ALEXING
            </div>

            <div
                class="titulo"
                style="font-size:' . $subtitleSize . ';">
                CONSTANCIA DE PAGO
            </div>

            <div
                class="titulo"
                style="font-size:' . ($isTicket ? '7px' : '16px') . ';">
                PAGO INICIAL
            </div>

        </div>

        <div class="linea"></div>

        <table>

            <tr>
                <td class="label">Cliente:</td>
                <td>' .
                    htmlspecialchars(
                        $cliente,
                        ENT_QUOTES,
                        'UTF-8'
                    ) .
                '</td>
            </tr>

            <tr>
                <td class="label">DNI:</td>
                <td>' .
                    htmlspecialchars(
                        $dni,
                        ENT_QUOTES,
                        'UTF-8'
                    ) .
                '</td>
            </tr>

            <tr>
                <td class="label">Fecha:</td>
                <td>' .
                    htmlspecialchars(
                        $fecha,
                        ENT_QUOTES,
                        'UTF-8'
                    ) .
                '</td>
            </tr>

            <tr>
                <td class="label">Venta:</td>
                <td>' .
                    htmlspecialchars(
                        $ventaRef,
                        ENT_QUOTES,
                        'UTF-8'
                    ) .
                '</td>
            </tr>

        </table>

        <div class="linea"></div>

        <div class="center titulo">
            CONCEPTO
        </div>

        <div class="center concepto">
            Pago inicial de venta a crédito
        </div>

        <div class="linea"></div>

        <div class="center titulo">
            IMPORTE PAGADO
        </div>

        <div class="importe">
            ' . formatoMoneda($inicial) . '
        </div>

        <div class="center small">
            <strong>Forma de pago:</strong>
            ' .
            htmlspecialchars(
                $metodoPago,
                ENT_QUOTES,
                'UTF-8'
            ) .
        '</div>

        <div class="linea"></div>

        <div class="center titulo">
            Saldo pendiente: ' .
            formatoMoneda($saldoPendiente) .
        '</div>

        <div class="linea"></div>

        <div class="footer">
            Este documento es únicamente<br>
            una constancia del pago inicial.<br>
            La venta cuenta con su respectivo<br>
            comprobante de pago.
        </div>

        <div class="linea"></div>

        <div class="footer">
            Gracias por su compra
        </div>

    </div>

</body>
</html>';

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html, 'UTF-8');

if ($isTicket) {
    $dompdf->setPaper(
        [0, 0, 164.41, 500],
        'portrait'
    );
} else {
    $dompdf->setPaper(
        'A4',
        'portrait'
    );
}

$dompdf->render();

$dompdf->stream(
    'constancia_pago_inicial_' . $idventa . '.pdf',
    ['Attachment' => false]
);

exit;

