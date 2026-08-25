<?php

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

$iva = $configuracion['monto_impuesto'] ?? 18;
$formaPago = ($factura['ventacredito'] == 'Si') ? 'CRÉDITO' : 'CONTADO';
$condicionPago = ($factura['ventacredito'] == 'Si') ? 'CRÉDITO 90 DÍAS' : 'CONTADO';

// Letras
$entero = floor($factura['total_venta']);
$decimales = str_pad(round(($factura['total_venta'] - $entero) * 100), 2, '0', STR_PAD_LEFT);
$texto = strtoupper($formatter->toWords($entero));
$con_letra = "{$texto} Y {$decimales}/100 SOLES";
$logo = !empty($configuracion['logo']) ? $configuracion['logo'] : 'default.png';
$rutaLogo = realpath(__DIR__ . '/../../files/logos/' . $logo);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= $factura['tipo_comprobante'] ?> <?= $factura['serie_comprobante'] ?>-<?= $factura['num_comprobante'] ?>
    </title>

    <style>
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            vertical-align: top;
        }

        .empresa {
            padding: 5px 10px;
        }

        .empresa h2 {
            margin: 0;
            font-size: 16px;
        }

        .empresa p {
            margin: 2px 0;
            font-size: 10px;
        }

        .ruc-box {
            border: 2px solid #000;
            border-radius: 8px;
            text-align: center;
            padding: 8px;
        }

        .ruc-box h3 {
            margin: 0;
            font-size: 14px;
        }

        .ruc-box h2 {
            margin: 5px 0;
            font-size: 16px;
        }

        .cliente {
            margin-top: 10px;
            border: 1px solid #ffffff;
        }

        .cliente th {
            background: #e6e6e6;
            text-align: left;
            padding: 5px;
            font-size: 11px;
        }

        .cliente td {
            padding: 4px 6px;
            border-top: 1px solid #ddd;
        }

        .detalle {
            margin-top: 10px;
        }

        .detalle th {
            background: #e6e6e6;
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 10px;
        }

        .detalle td {
            border: 1px solid #999;
            padding: 4px;
            font-size: 10px;
        }

        .detalle tr:nth-child(even) {
            background: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totales {
            margin-top: 10px;
        }

        .totales td {
            vertical-align: top;
        }

        .letras {
            border: 1px solid #000;
            border-radius: 6px;
            padding: 8px;
            min-height: 90px;
        }

        .totales-box {
            border: 1px solid #000;
            border-radius: 6px;
            overflow: hidden;
        }

        .totales-box table td {
            padding: 5px 8px;
            border-bottom: 1px solid #ddd;
        }

        .total-final {
            background: #000;
            color: #fff;
            font-weight: bold;
            font-size: 13px;
        }

        .qr-box {
            text-align: center;
            font-size: 9px;
            margin-top: 5px;
        }

        .footer {
            margin-top: 10px;
            border: 1px solid #000;
            border-radius: 6px;
            padding: 6px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <?= $anulada ?? '' ?>

    <!-- ========== ENCABEZADO PRINCIPAL ========== -->
    <table class="header">
        <tr>
            <td width="18%" class="text-center">
                <img src="file://<?php echo $rutaLogo; ?>" width="120" />
            </td>

            <td width="52%" class="empresa">
                <h2><?= $configuracion['razon_social'] ?></h2>
                <p><strong>Sucursal:</strong> <?= $configuracion['nombre'] ?></p>
                <p><strong>Dirección:</strong> <?= $configuracion['direccion'] ?></p>
                <p><strong>Teléfono:</strong> <?= $configuracion['telefono'] ?></p>
                <p><strong>Correo:</strong> <?= $configuracion['email'] ?? '' ?></p>
            </td>

            <td width="30%">
                <div class="ruc-box">
                    <h3>RUC <?= $configuracion['ruc'] ?></h3>
                    <h2><?= strtoupper($factura['tipo_comprobante']) ?> ELECTRÓNICA</h2>
                    <strong><?= $factura['serie_comprobante'] . ' - ' . $factura['num_comprobante'] ?></strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- ========== CAJA CLIENTE + INFO DERECHA ========== -->
    <table class="cliente">
        <tr>
            <th colspan="4">DATOS GENERALES</th>
        </tr>
        <tr>
            <td width="18%"><strong>Cliente:</strong></td>
            <td width="32%"><?= $factura['cliente'] ?></td>
            <td width="18%"><strong>Forma Pago:</strong></td>
            <td width="32%"><?= $formaPago ?></td>
        </tr>
        <tr>
            <td><strong>Documento:</strong></td>
            <td><?= $factura['tipo_documento'] ?> - <?= $factura['num_documento'] ?></td>
            <td><strong>Fecha:</strong></td>
            <td><?= $factura['fecha'] ?> </td>
        </tr>
        <tr>
            <td><strong>Dirección:</strong></td>
            <td><?= $factura['direccion'] ?></td>
            <td><strong>Almacén:</strong></td>
            <td><?= $configuracion['nombre'] ?></td>
        </tr>
        <tr>
            <td><strong>Ejecutivo:</strong></td>
            <td><?= $factura['personal'] ?></td>
            <td><strong>Observación:</strong></td>
            <td><?= $factura['observacion'] ?></td>
        </tr>
    </table>



    <!-- ========== DETALLE DE PRODUCTOS ========== -->
    <table class="detalle">
        <thead>
            <tr>
                <th width="8%">CANT.</th>
                <th width="8%">UM</th>
                <th width="40%">DESCRIPCIÓN</th>
                <th width="12%">P.UNIT</th>
                <th width="8%">DSCTO</th>
                <th width="12%">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $descuento = 0;
            $exonerado = 0;
            $opgrav = 0;
            $opinafecta = 0;
            $opgratuita = 0;
            $anticipo = 0;
            $descGlobales = 0;
            foreach ($detalles as $row) {
                $precio_total = $row['subtotal'];
                if ($row['proigv'] == 'No Gravada') {
                    $exonerado += $precio_total;
                } elseif ($row['proigv'] == 'Inafecta') {
                    $opinafecta += $precio_total;
                } elseif ($row['proigv'] == 'Gratuita') {
                    $opgratuita += $precio_total;
                } else {
                    $opgrav += $precio_total;
                }
                $descuento += $row['descuento'];

                // Cálculo del V.Venta Neto (precio unitario menos descuento unitario)
                $cant = max($row['cantidad'], 1);
                $dsctoUnit = $row['descuento'] / $cant;
                $vVentaNeto = $row['precio_venta'] - $dsctoUnit;

                // Porcentaje descuento
                $dsctoPct = ($row['precio_venta'] * $cant) > 0 ? ($row['descuento'] / ($row['precio_venta'] * $cant)) * 100 : 0;
                ?>
                <tr>
                    <td class="text-center"><?= round($row['cantidad'], 2) ?></td>
                    <td class="text-center"><?= $row['contenedor'] ?? '' ?></td>
                    <td class="desc"><?= htmlspecialchars($row['dproducto']) ?></td>
                    <td class="text-right"><?= $helpers->get_currency_symbol($row['precio_venta']) ?></td>
                    <td class="text-right"><?= $helpers->get_currency_symbol($dsctoPct) ?></td>
                    <td class="text-right"><?= $helpers->get_currency_symbol($row['subtotal']) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- ========== LINEA PUNTEADA SEPARADORA ========== -->
    <hr class="dotted-line">
    <!-- ========== IMPORTE EN LETRAS ========== -->
    <div class="son-section">
        SON: <?= $con_letra ?>
    </div>

    <!-- ========== LINEA PUNTEADA SEPARADORA ========== -->
    <hr class="dotted-line">

    <!-- ========== SECCIÓN INFERIOR: NOTAS + TOTALES ========== -->
    <?php
    $total = $factura['total_venta'];
    $igv = $factura['impuesto'];
    $subtotal = $total - $igv;
    ?>

    <table class="totales">
        <tr>
            <td width="18%" class="text-center">
                <?php
                $options = new QROptions([
                    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                    'scale' => 6,
                ]);

                $textoQr = $configuracion['ruc'] . "|" .
                    $factura['serie_comprobante'] . "|" .
                    $factura['num_comprobante'] . "|" .
                    $iva . "|" .
                    $factura['total_venta'] . "|" .
                    $factura['fecha'] . "|" .
                    $factura['num_documento'] . "|";

                $qr = (new QRCode($options))->render($textoQr);
                ?>

                <img src="<?= $qr ?>" width="100" height="100">

                <div class="qr-box">
                    Consulte en SUNAT
                </div>
            </td>
            <td width="50%">
            </td>
            <td class="totales-cell">
                <table class="totales-tabla">
                    <tr>
                        <td class="etiqueta">Op.Gravada</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($opgrav) ?></td>
                    </tr>
                    <tr>
                        <td class="etiqueta">Anticipo</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($anticipo) ?></td>
                    </tr>
                    <tr>
                        <td class="etiqueta">Desc. Globales</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($descGlobales) ?></td>
                    </tr>
                    <tr>
                        <td class="etiqueta">I.G.V <?= $iva ?>%</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($igv) ?></td>
                    </tr>
                    <tr>
                        <td class="etiqueta">Op.Inafecta</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($opinafecta) ?></td>
                    </tr>
                    <tr>
                        <td class="etiqueta">Op.Exonerada</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($exonerado) ?></td>
                    </tr>
                    <tr>
                        <td class="etiqueta">Op.Gratuita</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($opgratuita) ?></td>
                    </tr>
                    <tr class="importe-total">
                        <td class="etiqueta"><strong>Importe Total</strong></td>
                        <td class="monto"><strong><?= $helpers->get_currency_symbol($total) ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ========== PAGOS (opcional) ========== -->
    <?php if (!empty($pagos)): ?>
        <div style="border:1px solid #000; margin-top:8px; padding:6px 10px; font-size:8.5px;">
            <strong>PAGOS REALIZADOS:</strong><br>
            <?php
            $totalPagado = 0;
            foreach ($pagos as $pago) {
                echo $pago['metodo_pago'] . ': ' . $helpers->get_currency_symbol($pago['monto']);
                if (!empty($pago['banco']))
                    echo ' | Banco: ' . $pago['banco'];
                if (!empty($pago['nroOperacion']))
                    echo ' | OP: ' . $pago['nroOperacion'];
                echo '<br>';
                $totalPagado += $pago['monto'];
            }
            ?>
            <br>
            <strong>Total Pagado:</strong> <?= $helpers->get_currency_symbol($totalPagado) ?> &nbsp;&nbsp;
            <strong>Saldo:</strong> <?= $helpers->get_currency_symbol($total - $totalPagado) ?>
        </div>
    <?php endif; ?>

    <!-- ========== PIE ========== -->
    <div class="footer">
        <strong>Representación impresa de la <?= strtoupper($factura['tipo_comprobante']) ?> ELECTRÓNICA</strong><br>
        Autorizado mediante SEE - Del Contribuyente.<br>
        Gracias por su preferencia.
    </div>

</body>

</html>