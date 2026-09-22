<?php

ob_start();

require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../configuraciones/Conexion.php';
require_once __DIR__ . '/../modelos/CuentasCobrar.php';
require_once __DIR__ . '/../modelos/Venta.php';
require_once __DIR__ . '/../modelos/Negocio.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (empty($_GET['id'])) {
    http_response_code(400);
    echo 'No se indicó el pago.';
    exit;
}

$idpago = (int) $_GET['id'];

$venta = new Venta();
$cuentaCobrar = new CuentasCobrar();
$negocio = new Negocio();

/*
|--------------------------------------------------------------------------
| PAGO
|--------------------------------------------------------------------------
|
| Ajusta este método si en tu modelo tiene otro nombre.
|
*/

$pago = $cuentaCobrar->obtenerCuentaPorCobrar($idpago);
$venta = $venta->ventacabecera($pago['idventa']);
$detalle = $cuentaCobrar->obtenerUltimoPagoCuota($pago['idcpc']);

if (!$pago) {
    http_response_code(404);
    echo 'No se encontró el pago.';
    exit;
}

/*
|--------------------------------------------------------------------------
| SUCURSAL
|--------------------------------------------------------------------------
*/

$idsucursal = (int) ($pago['idsucursal'] ?? 0);

$sucursal = $negocio->listar($idsucursal);

if (!is_array($sucursal)) {
    $sucursal = [];
}

/*
|--------------------------------------------------------------------------
| DATOS EMPRESA
|--------------------------------------------------------------------------
*/

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

$logo = !empty($sucursal['logo'])
    ? trim((string) $sucursal['logo'])
    : 'default.png';

if ($razonSocial === '') {
    $razonSocial = 'ALEXING';
}

/*
|--------------------------------------------------------------------------
| LOGO
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| DATOS CLIENTE
|--------------------------------------------------------------------------
*/

$cliente = trim(
    (string) ($venta['cliente'] ?? 'Cliente')
);

$cliente = $cliente ?: 'Cliente';

$documento = trim(
    (string) ($venta['num_documento'] ?? '')
);

$documento = $documento ?: '-';

/*
|--------------------------------------------------------------------------
| VENTA
|--------------------------------------------------------------------------
*/

$serie = trim(
    (string) ($venta['serie_comprobante'] ?? '')
);

$numero = trim(
    (string) ($venta['num_comprobante'] ?? '')
);

$ventaRef = '';

if ($serie !== '' || $numero !== '') {
    $ventaRef = $serie . '-' . $numero;
}

$ventaRef = $ventaRef ?: '-';

/*
|--------------------------------------------------------------------------
| CUOTA
|--------------------------------------------------------------------------
*/

$numeroCuota = $cuentaCobrar->obtenerNumeroCuota($pago['idventa'], $idpago);

if ($numeroCuota === '') {
    $numeroCuota = '-';
}

/*
|--------------------------------------------------------------------------
| FECHA
|--------------------------------------------------------------------------
*/

$fecha = trim((string) ($detalle['fechapago'] ?? date('d/m/Y H:i')));

/*
|--------------------------------------------------------------------------
| FORMA DE PAGO
|--------------------------------------------------------------------------
*/

$metodoPago = strtoupper(trim((string) ($detalle['formapago'] ?? 'EFECTIVO')));
$metodoPago = $metodoPago ?: 'EFECTIVO';

/*
|--------------------------------------------------------------------------
| MONTOS
|--------------------------------------------------------------------------
*/

$importe = (float) ($pago['abonototal'] ?? 0);
$totalVenta = (float) ($pago['deudatotal'] ?? 0);
$saldoActual = (float) ($pago['deuda'] ?? max($totalVenta - $importe, 0));

$banco = trim((string) ($detalle['banco'] ?? ''));
$nroOperacion = trim((string) ($detalle['op'] ?? ''));

/*
|--------------------------------------------------------------------------
| DOMPDF
|--------------------------------------------------------------------------
*/

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

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .empresa {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .sucursal {
            font-size: 9px;
            font-weight: bold;
        }

        .datos {
            font-size: 9px;
            line-height: 1.3;
        }

        .logo {
            width: 80px;
            height: auto;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .titulo {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .subtitulo {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 2px;
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
            width: 38%;
            font-weight: bold;
        }

        .valor {
            width: 62%;
        }

        .importe-label {
            font-size: 10px;
            font-weight: bold;
        }

        .importe {
            font-size: 21px;
            font-weight: bold;
            margin: 5px 0;
        }

        .saldo-anterior {
            font-size: 11px;
        }

        .saldo-actual {
            font-size: 15px;
            font-weight: bold;
        }

        .cuota {
            border: 1px dashed #000;
            padding: 7px;
            margin: 5px 0;
        }

        .cuota-numero {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .cuota-descripcion {
            text-align: center;
            font-size: 9px;
            margin-top: 3px;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            line-height: 1.4;
        }

        .operacion {
            font-size: 9px;
        }
    </style>

</head>

<body>

    <div class="ticket">

        <!-- EMPRESA -->

        <div class="center">

            <?php if ($logoSrc !== '') { ?>

                <img
                    src="<?php echo htmlspecialchars(
                                $logoSrc,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                    class="logo"
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

        <!-- TITULO -->

        <div class="center">

            <div class="titulo">
                CONSTANCIA DE PAGO
            </div>

            <div class="subtitulo">
                PAGO DE CUOTA
            </div>

        </div>

        <div class="line"></div>

        <!-- CLIENTE -->

        <table>

            <tr>

                <td class="label">
                    Cliente:
                </td>

                <td class="valor">

                    <?php echo htmlspecialchars(
                        $cliente,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

            </tr>

            <tr>

                <td class="label">
                    Documento:
                </td>

                <td class="valor">

                    <?php echo htmlspecialchars(
                        $documento,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

            </tr>

            <tr>

                <td class="label">
                    Venta:
                </td>

                <td class="valor">

                    <?php echo htmlspecialchars(
                        $ventaRef,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

            </tr>

            <tr>

                <td class="label">
                    Fecha:
                </td>

                <td class="valor">

                    <?php echo htmlspecialchars(
                        $fecha,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

            </tr>

        </table>

        <div class="line"></div>

        <!-- CUOTA -->

        <div class="cuota">

            <div class="cuota-numero">

                CUOTA
                <?php echo htmlspecialchars(
                    $numeroCuota,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>

            </div>

            <div class="cuota-descripcion">

                Pago de cuota de crédito

            </div>

        </div>

        <!-- IMPORTE -->

        <div class="center">

            <div class="importe-label">

                IMPORTE PAGADO

            </div>

            <div class="importe">
                <?php echo $helpers->get_currency_symbol($importe); ?>
            </div>

        </div>

        <div class="line"></div>

        <!-- FORMA DE PAGO -->

        <table>

            <tr>

                <td class="label">
                    Forma de pago:
                </td>

                <td class="valor">

                    <?php echo htmlspecialchars(
                        $metodoPago,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>

            </tr>

            <?php if ($banco !== '') { ?>

                <tr>

                    <td class="label">
                        Banco:
                    </td>

                    <td class="valor">

                        <?php echo htmlspecialchars(
                            $banco,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </td>

                </tr>

            <?php } ?>

            <?php if ($nroOperacion !== '') { ?>

                <tr>

                    <td class="label">
                        Operación:
                    </td>

                    <td class="valor">

                        <?php echo htmlspecialchars(
                            $nroOperacion,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </td>

                </tr>

            <?php } ?>

        </table>

        <div class="line"></div>

        <!-- SALDOS -->

        <table>

            <?php if ($totalVenta > 0) { ?>

                <tr>

                    <td class="label">
                        Total cuota:
                    </td>

                    <td class="right">
                        <?php echo $helpers->get_currency_symbol($totalVenta); ?>
                    </td>

                </tr>

            <?php } ?>

            <tr>

                <td class="label">
                    Pago realizado:
                </td>

                <td class="right">
                    <?php echo $helpers->get_currency_symbol($importe); ?>
                </td>

            </tr>

            <tr>

                <td class="label">
                    SALDO ACTUAL:
                </td>

                <td class="right saldo-actual">
                    <?php echo $helpers->get_currency_symbol($saldoActual); ?>
                </td>

            </tr>

        </table>

        <div class="line"></div>

        <!-- CONFIRMACION -->

        <div class="center footer">

            <strong>
                PAGO REGISTRADO
            </strong>

            <br><br>

            Se deja constancia de la recepción
            del pago correspondiente a la cuota
            indicada.

        </div>

        <div class="line"></div>

        <div class="center footer">

            Gracias por su preferencia

        </div>

    </div>

</body>

</html>

<?php

$html = ob_get_clean();

$dompdf->loadHtml(
    $html,
    'UTF-8'
);

$dompdf->setPaper(
    [0, 0, 226.77, 650],
    'portrait'
);

$dompdf->render();

$dompdf->stream(
    'constancia_pago_cuota_' . $idpago . '.pdf',
    [
        'Attachment' => false
    ]
);

exit;
