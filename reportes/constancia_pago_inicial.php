<?php

ob_start();

require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../configuraciones/Conexion.php';
require_once __DIR__ . '/../modelos/Helpers.php';
require_once __DIR__ . '/../modelos/Venta.php';
require_once __DIR__ . '/../modelos/Negocio.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$helpers = new Helpers();

if (empty($_GET['id'])) {
    http_response_code(400);
    echo 'No se indicó la venta.';
    exit;
}

$idventa = (int) $_GET['id'];

$formato = (($_GET['formato'] ?? 'ticket') === 'a4')
    ? 'a4'
    : 'ticket';

$ventaModel = new Venta();

$venta = $ventaModel->ventacabecera($idventa);

if (!$venta) {
    http_response_code(404);
    echo 'No se encontró la venta.';
    exit;
}

$negocio = new Negocio();

$idsucursal = (int) ($venta['idsucursal'] ?? 0);

$sucursal = $negocio->listar($idsucursal);

if (!is_array($sucursal)) {
    $sucursal = [];
}

// =====================================================
// DATOS VENTA
// =====================================================

$totalVenta = (float) ($venta['total_venta'] ?? 0);

$inicial = (float) ($venta['totalrecibido'] ?? 0)
    + (float) ($venta['totaldeposito'] ?? 0);

$saldoPendiente = max(
    $totalVenta - $inicial,
    0
);

$cliente = trim(
    (string) ($venta['cliente'] ?? 'Cliente')
);

$cliente = $cliente ?: 'Cliente';

$dni = trim(
    (string) ($venta['num_documento'] ?? '')
);

$dni = $dni ?: '-';

$tipoDocumento = trim(
    (string) ($venta['tipo_documento'] ?? '')
);

$fecha = trim(
    (string) (
        $venta['fecha_hora']
        ?? $venta['fecha']
        ?? date('d/m/Y H:i')
    )
);

$metodoPago = strtoupper(
    trim(
        (string) (
            $venta['formapago']
            ?? 'Efectivo'
        )
    )
);

$metodoPago = $metodoPago ?: 'EFECTIVO';

$serie = trim(
    (string) ($venta['serie_comprobante'] ?? '')
);

$numero = trim(
    (string) ($venta['num_comprobante'] ?? '')
);

$ventaRef = '';

if ($serie !== '' || $numero !== '') {
    $ventaRef = $serie . ' - ' . $numero;
}

$tipoComprobante = strtoupper(
    trim(
        (string) (
            $venta['tipo_comprobante']
            ?? ''
        )
    )
);

$direccionCliente = trim(
    (string) ($venta['direccion'] ?? '')
);

$personal = trim(
    (string) ($venta['personal'] ?? '')
);

$observacion = trim(
    (string) ($venta['observacion'] ?? '')
);

// =====================================================
// DATOS SUCURSAL
// =====================================================

$razonSocial = trim(
    (string) ($sucursal['razon_social'] ?? '')
);

$nombreSucursal = trim(
    (string) ($sucursal['nombre'] ?? '')
);

$ruc = trim(
    (string) ($sucursal['ruc'] ?? '')
);

$direccion = trim(
    (string) ($sucursal['direccion'] ?? '')
);

$telefono = trim(
    (string) ($sucursal['telefono'] ?? '')
);

$email = trim(
    (string) ($sucursal['email'] ?? '')
);

$logo = !empty($sucursal['logo'])
    ? trim((string) $sucursal['logo'])
    : 'default.png';

if ($razonSocial === '') {
    $razonSocial = 'ALEXING';
}

// =====================================================
// LOGO
// =====================================================

$logoPath = realpath(
    __DIR__ . '/../files/logos/' . $logo
);

$logoSrc = '';

if ($logoPath && file_exists($logoPath)) {

    $logoSrc = 'file://' . str_replace(
        '\\',
        '/',
        $logoPath
    );
}

if ($logoSrc === '') {

    $defaultLogoPath = realpath(
        __DIR__ . '/../files/logos/default.png'
    );

    if ($defaultLogoPath && file_exists($defaultLogoPath)) {

        $logoSrc = 'file://' . str_replace(
            '\\',
            '/',
            $defaultLogoPath
        );
    }
}

// =====================================================
// MONEDA
// =====================================================

$moneda = 'S/';

try {

    $moneda = $helpers->get_currency_symbol(0);

    if (empty($moneda)) {
        $moneda = 'S/';
    }

} catch (Throwable $e) {

    $moneda = 'S/';
}

$isTicket = $formato === 'ticket';

// =====================================================
// DOMPDF
// =====================================================

$options = new Options();

$options->setIsRemoteEnabled(true);

$options->set(
    'defaultFont',
    'Helvetica'
);

$options->setChroot(
    realpath(__DIR__ . '/../')
);

$dompdf = new Dompdf($options);

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <style>

        <?php if ($isTicket) { ?>

        @page {
            size: 80mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            width: 80mm;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .ticket {
            width: 72mm;
            padding: 8px;
        }

        .empresa {
            font-size: 12px;
        }

        .titulo {
            font-size: 12px;
        }

        .subtitulo {
            font-size: 10px;
        }

        .importe {
            font-size: 18px;
        }

        .saldo {
            font-size: 14px;
        }

        .small {
            font-size: 9px;
        }

        .logo-ticket {
            width: 80px;
            height: auto;
        }

        <?php } else { ?>

        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .a4 {
            width: 100%;
        }

        .logo-a4 {
            width: 120px;
            height: auto;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            vertical-align: top;
        }

        .empresa-a4 {
            padding: 5px 10px;
        }

        .empresa-a4 h2 {
            margin: 0 0 5px 0;
            font-size: 17px;
        }

        .empresa-a4 p {
            margin: 3px 0;
            font-size: 10px;
        }

        .ruc-box {
            border: 2px solid #000;
            border-radius: 8px;
            text-align: center;
            padding: 9px 6px;
        }

        .ruc-box h3 {
            margin: 0;
            font-size: 13px;
        }

        .ruc-box h2 {
            margin: 6px 0;
            font-size: 15px;
        }

        .ruc-box strong {
            font-size: 12px;
        }

        .cliente {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .cliente th {
            background: #e6e6e6;
            border: 1px solid #999;
            text-align: left;
            padding: 6px;
            font-size: 11px;
        }

        .cliente td {
            border: 1px solid #999;
            padding: 6px;
            font-size: 10px;
        }

        .titulo-a4 {
            margin-top: 14px;
            border: 1px solid #000;
            border-radius: 6px;
            text-align: center;
            padding: 9px;
        }

        .titulo-a4 .principal {
            font-size: 17px;
            font-weight: bold;
        }

        .titulo-a4 .secundario {
            margin-top: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .pago-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .pago-box td {
            border: 1px solid #999;
            padding: 8px;
            font-size: 11px;
        }

        .pago-box .label {
            background: #e6e6e6;
            font-weight: bold;
            width: 25%;
        }

        .importe-a4 {
            border: 2px solid #000;
            border-radius: 8px;
            text-align: center;
            padding: 14px;
            margin-top: 12px;
        }

        .importe-a4 .texto {
            font-size: 12px;
            font-weight: bold;
        }

        .importe-a4 .monto {
            font-size: 25px;
            font-weight: bold;
            margin-top: 7px;
        }

        .resumen {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .resumen td {
            border: 1px solid #999;
            padding: 7px 9px;
            font-size: 11px;
        }

        .resumen .etiqueta {
            background: #e6e6e6;
            font-weight: bold;
        }

        .resumen .monto {
            text-align: right;
        }

        .resumen .saldo-final td {
            font-size: 13px;
            font-weight: bold;
            border-top: 2px solid #000;
        }

        .son {
            border: 1px solid #000;
            border-radius: 6px;
            padding: 9px;
            margin-top: 12px;
            font-size: 10px;
        }

        .observacion-a4 {
            border: 1px solid #999;
            border-radius: 6px;
            padding: 8px;
            margin-top: 12px;
            font-size: 10px;
        }

        .footer-a4 {
            margin-top: 15px;
            border: 1px solid #000;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            font-size: 10px;
            line-height: 1.4;
        }

        <?php } ?>

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .line {
            border-top:
                <?php echo $isTicket
                    ? '1px dashed #000'
                    : '1px solid #000'; ?>;

            margin: 6px 0;
        }

        .empresa {
            font-weight: bold;
            text-transform: uppercase;
        }

        .sucursal {
            font-size:
                <?php echo $isTicket
                    ? '9px'
                    : '13px'; ?>;

            font-weight: bold;
        }

        .datos {
            font-size:
                <?php echo $isTicket
                    ? '9px'
                    : '12px'; ?>;

            line-height: 1.3;
        }

        .titulo {
            font-weight: bold;
            text-transform: uppercase;
        }

        .subtitulo {
            font-weight: bold;
            text-transform: uppercase;
        }

        .importe {
            font-weight: bold;
            text-align: center;
            margin: 6px 0;
        }

        .saldo {
            font-weight: bold;
        }

        .concepto {
            text-align: center;

            font-size:
                <?php echo $isTicket
                    ? '9px'
                    : '13px'; ?>;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .label {
            width: 30%;
            font-weight: bold;
        }

        .valor {
            width: 70%;
        }

        .footer {
            text-align: center;

            font-size:
                <?php echo $isTicket
                    ? '9px'
                    : '11px'; ?>;

            line-height: 1.3;
        }

    </style>

</head>

<body>

<?php if ($isTicket) { ?>

    <!-- =====================================================
         TICKET
         ===================================================== -->

    <div class="ticket">

        <div class="center">

            <?php if ($logoSrc !== '') { ?>

                <img
                    src="<?php echo htmlspecialchars($logoSrc, ENT_QUOTES, 'UTF-8'); ?>"
                    class="logo-ticket"
                    alt="Logo">

                <br>

            <?php } ?>

            <div class="empresa">

                <?php echo htmlspecialchars(
                    $razonSocial,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>

            </div>

            <?php if ($nombreSucursal !== '') { ?>

                <div class="sucursal">

                    SUCURSAL:
                    <?php echo htmlspecialchars(
                        $nombreSucursal,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </div>

            <?php } ?>

            <div class="datos">

                <?php if ($ruc !== '') { ?>

                    RUC:
                    <?php echo htmlspecialchars(
                        $ruc,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                    <br>

                <?php } ?>

                <?php if ($direccion !== '') { ?>

                    <?php echo htmlspecialchars(
                        $direccion,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                    <br>

                <?php } ?>

                <?php if ($telefono !== '') { ?>

                    Tel:
                    <?php echo htmlspecialchars(
                        $telefono,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                <?php } ?>

            </div>

        </div>

        <div class="line"></div>

        <div class="center">

            <div class="titulo">
                CONSTANCIA DE PAGO
            </div>

            <div class="subtitulo">
                PAGO INICIAL
            </div>

        </div>

        <div class="line"></div>

        <div class="center">

            <div class="bold">
                VENTA
            </div>

            <?php if ($ventaRef !== '') { ?>

                <div>
                    <?php echo htmlspecialchars(
                        $ventaRef,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </div>

            <?php } ?>

            <?php if ($tipoComprobante !== '') { ?>

                <div class="small">

                    <?php echo htmlspecialchars(
                        $tipoComprobante,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </div>

            <?php } ?>

        </div>

        <div class="line"></div>

        <table>

            <tr>
                <td class="label">Cliente:</td>

                <td class="valor">
                    <?php echo htmlspecialchars(
                        $cliente,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </td>
            </tr>

            <tr>
                <td class="label">Doc:</td>

                <td class="valor">
                    <?php echo htmlspecialchars(
                        $dni,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </td>
            </tr>

            <tr>
                <td class="label">Fecha:</td>

                <td class="valor">
                    <?php echo htmlspecialchars(
                        $fecha,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </td>
            </tr>

            <tr>
                <td class="label">Pago:</td>

                <td class="valor">
                    <?php echo htmlspecialchars(
                        $metodoPago,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </td>
            </tr>

        </table>

        <div class="line"></div>

        <div class="center">

            <div class="bold">
                CONCEPTO
            </div>

            <div class="concepto">
                Pago inicial de venta a crédito
            </div>

        </div>

        <div class="line"></div>

        <div class="center bold">
            IMPORTE PAGADO
        </div>

        <div class="importe">

            <?php echo $helpers->get_currency_symbol($inicial); ?>

        </div>

        <div class="center small">

            Forma de pago:

            <strong>
                <?php echo htmlspecialchars(
                    $metodoPago,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </strong>

        </div>

        <div class="line"></div>

        <table>

            <tr>
                <td class="label">
                    Total venta:
                </td>

                <td class="right">
                    <?php echo $helpers->get_currency_symbol($totalVenta); ?>
                </td>
            </tr>

            <tr>
                <td class="label">
                    Inicial:
                </td>

                <td class="right">
                    <?php echo $helpers->get_currency_symbol($inicial); ?>
                </td>
            </tr>

            <tr>
                <td class="label">
                    Saldo:
                </td>

                <td class="right saldo">
                    <?php echo $helpers->get_currency_symbol($saldoPendiente); ?>
                </td>
            </tr>

        </table>

        <div class="line"></div>

        <div class="footer">

            Este documento es únicamente<br>
            una constancia del pago inicial.<br>
            La venta cuenta con su respectivo<br>
            comprobante de pago.

        </div>

        <div class="line"></div>

        <div class="center footer">

            Gracias por su compra

        </div>

    </div>

<?php } else { ?>

    <!-- =====================================================
         A4
         ===================================================== -->

    <div class="a4">

        <!-- ENCABEZADO -->

        <table class="header">

            <tr>

                <td width="18%" class="center">

                    <?php if ($logoSrc !== '') { ?>

                        <img
                            src="<?php echo htmlspecialchars(
                                $logoSrc,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            class="logo-a4"
                            alt="Logo">

                    <?php } ?>

                </td>

                <td width="52%" class="empresa-a4">

                    <h2>

                        <?php echo htmlspecialchars(
                            $razonSocial,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </h2>

                    <?php if ($nombreSucursal !== '') { ?>

                        <p>

                            <strong>Sucursal:</strong>

                            <?php echo htmlspecialchars(
                                $nombreSucursal,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                        </p>

                    <?php } ?>

                    <?php if ($direccion !== '') { ?>

                        <p>

                            <strong>Dirección:</strong>

                            <?php echo htmlspecialchars(
                                $direccion,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                        </p>

                    <?php } ?>

                    <?php if ($telefono !== '') { ?>

                        <p>

                            <strong>Teléfono:</strong>

                            <?php echo htmlspecialchars(
                                $telefono,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                        </p>

                    <?php } ?>

                    <?php if ($email !== '') { ?>

                        <p>

                            <strong>Correo:</strong>

                            <?php echo htmlspecialchars(
                                $email,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                        </p>

                    <?php } ?>

                </td>

                <td width="30%">

                    <div class="ruc-box">

                        <?php if ($ruc !== '') { ?>

                            <h3>

                                RUC
                                <?php echo htmlspecialchars(
                                    $ruc,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </h3>

                        <?php } ?>

                        <h2>
                            CONSTANCIA DE PAGO
                        </h2>

                        <strong>
                            PAGO INICIAL
                        </strong>

                    </div>

                </td>

            </tr>

        </table>

        <!-- TITULO -->

        <div class="titulo-a4">

            <div class="principal">
                CONSTANCIA DE PAGO
            </div>

            <div class="secundario">
                CONSTANCIA DE PAGO INICIAL DE VENTA A CRÉDITO
            </div>

        </div>

        <!-- DATOS GENERALES -->

        <table class="cliente">

            <tr>

                <th colspan="4">
                    DATOS GENERALES
                </th>

            </tr>

            <tr>

                <td width="18%">
                    <strong>Cliente:</strong>
                </td>

                <td width="32%">

                    <?php echo htmlspecialchars(
                        $cliente,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

                <td width="18%">
                    <strong>Forma Pago:</strong>
                </td>

                <td width="32%">

                    <?php echo htmlspecialchars(
                        $metodoPago,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

            </tr>

            <tr>

                <td>
                    <strong>Documento:</strong>
                </td>

                <td>

                    <?php echo htmlspecialchars(
                        $tipoDocumento,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                    <?php if ($tipoDocumento !== '') { ?>
                        -
                    <?php } ?>

                    <?php echo htmlspecialchars(
                        $dni,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

                <td>
                    <strong>Fecha:</strong>
                </td>

                <td>

                    <?php echo htmlspecialchars(
                        $fecha,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

            </tr>

            <tr>

                <td>
                    <strong>Dirección:</strong>
                </td>

                <td>

                    <?php echo htmlspecialchars(
                        $direccionCliente ?: '-',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

                <td>
                    <strong>Almacén:</strong>
                </td>

                <td>

                    <?php echo htmlspecialchars(
                        $nombreSucursal ?: '-',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

            </tr>

            <tr>

                <td>
                    <strong>Venta:</strong>
                </td>

                <td>

                    <?php echo htmlspecialchars(
                        $ventaRef ?: '-',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

                <td>
                    <strong>Comprobante:</strong>
                </td>

                <td>

                    <?php echo htmlspecialchars(
                        $tipoComprobante ?: '-',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

            </tr>

            <?php if ($personal !== '' || $observacion !== '') { ?>

                <tr>

                    <td>
                        <strong>Ejecutivo:</strong>
                    </td>

                    <td>

                        <?php echo htmlspecialchars(
                            $personal ?: '-',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </td>

                    <td>
                        <strong>Observación:</strong>
                    </td>

                    <td>

                        <?php echo htmlspecialchars(
                            $observacion ?: '-',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </td>

                </tr>

            <?php } ?>

        </table>

        <!-- INFORMACION DEL PAGO -->

        <table class="pago-box">

            <tr>

                <td class="label">
                    Concepto
                </td>

                <td>
                    Pago inicial de venta a crédito
                </td>

            </tr>

            <tr>

                <td class="label">
                    Forma de pago
                </td>

                <td>

                    <?php echo htmlspecialchars(
                        $metodoPago,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

            </tr>

            <tr>

                <td class="label">
                    Referencia de venta
                </td>

                <td>

                    <?php echo htmlspecialchars(
                        $ventaRef ?: '-',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

            </tr>

        </table>

        <!-- IMPORTE -->

        <div class="importe-a4">

            <div class="texto">
                IMPORTE DEL PAGO INICIAL
            </div>

            <div class="monto">

                <?php echo $helpers->get_currency_symbol($inicial); ?>

            </div>

        </div>

        <!-- RESUMEN -->

        <table class="resumen">

            <tr>

                <td class="etiqueta" width="65%">
                    Total de la venta
                </td>

                <td class="monto">
                    <?php echo $helpers->get_currency_symbol($totalVenta); ?>
                </td>

            </tr>

            <tr>

                <td class="etiqueta">
                    Pago inicial realizado
                </td>

                <td class="monto">
                    <?php echo $helpers->get_currency_symbol($inicial ); ?>
                </td>

            </tr>

            <tr class="saldo-final">

                <td class="etiqueta">
                    SALDO PENDIENTE
                </td>

                <td class="monto">
                    <?php echo $helpers->get_currency_symbol($saldoPendiente); ?>
                </td>

            </tr>

        </table>

        <!-- IMPORTE EN LETRAS -->

        <?php

        $entero = floor($inicial);

        $decimales = str_pad(
            round(($inicial - $entero) * 100),
            2,
            '0',
            STR_PAD_LEFT
        );

        try {

            $formatter = new \Luecano\NumeroALetras\NumeroALetras();

            $textoLetras = strtoupper(
                $formatter->toWords($entero)
            );

            $conLetra = "{$textoLetras} Y {$decimales}/100 SOLES";

        } catch (Throwable $e) {

            $conLetra = '';

        }

        ?>

        <?php if ($conLetra !== '') { ?>

            <div class="son">

                <strong>SON:</strong>

                <?php echo htmlspecialchars(
                    $conLetra,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>

            </div>

        <?php } ?>

        <!-- OBSERVACION -->

        <div class="observacion-a4">

            <strong>IMPORTANTE:</strong>

            Este documento constituye únicamente una
            constancia del pago inicial realizado por el cliente.
            La venta cuenta con su respectivo comprobante de pago.

        </div>

        <!-- PIE -->

        <div class="footer-a4">

            <strong>
                CONSTANCIA DE PAGO INICIAL
            </strong>

            <br>

            Documento generado por el sistema de ventas.

            <br>

            Gracias por su preferencia.

        </div>

    </div>

<?php } ?>

</body>

</html>

<?php

$html = ob_get_clean();

$dompdf->loadHtml(
    $html,
    'UTF-8'
);

if ($isTicket) {

    $dompdf->setPaper(
        [0, 0, 226.77, 700],
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
    [
        'Attachment' => false
    ]
);

exit;