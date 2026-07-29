<?php
$iva = $configuracion['monto_impuesto'] ?? 18;
$formaPago = ($factura['ventacredito'] == 'Si') ? 'CRÉDITO' : 'CONTADO';

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
<title><?= $factura['tipo_comprobante'] ?></title>

<style>
    body{
        font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
        font-size:11px;
        color:#000;
        margin:20px;
    }

    table{
        width:100%;
        border-collapse:collapse;
    }

    .header td{
        vertical-align:top;
    }

    .empresa{
        padding:5px 10px;
    }

    .empresa h2{
        margin:0;
        font-size:16px;
    }

    .empresa p{
        margin:2px 0;
        font-size:10px;
    }

    .ruc-box{
        border:2px solid #000;
        border-radius:8px;
        text-align:center;
        padding:8px;
    }

    .ruc-box h3{
        margin:0;
        font-size:14px;
    }

    .ruc-box h2{
        margin:5px 0;
        font-size:16px;
    }

    .cliente{
        margin-top:10px;
        border:1px solid #ffffff;
    }

    .cliente th{
        background:#e6e6e6;
        text-align:left;
        padding:5px;
        font-size:11px;
    }

    .cliente td{
        padding:4px 6px;
        border-top:1px solid #ddd;
    }

    .detalle{
        margin-top:10px;
    }

    .detalle th{
        background:#e6e6e6;
        border:1px solid #000;
        padding:5px;
        text-align:center;
        font-size:10px;
    }

    .detalle td{
        border:1px solid #999;
        padding:4px;
        font-size:10px;
    }

    .detalle tr:nth-child(even){
        background:#f9f9f9;
    }

    .text-right{text-align:right;}
    .text-center{text-align:center;}

    .totales{
        margin-top:10px;
    }

    .totales td{
        vertical-align:top;
    }

    .letras{
        border:1px solid #000;
        border-radius:6px;
        padding:8px;
        min-height:90px;
    }

    .totales-box{
        border:1px solid #000;
        border-radius:6px;
        overflow:hidden;
    }

    .totales-box table td{
        padding:5px 8px;
        border-bottom:1px solid #ddd;
    }

    .total-final{
        background:#000;
        color:#fff;
        font-weight:bold;
        font-size:13px;
    }

    .qr-box{
        text-align:center;
        font-size:9px;
        margin-top:5px;
    }

    .footer{
        margin-top:10px;
        border:1px solid #000;
        border-radius:6px;
        padding:6px;
        text-align:center;
        font-size:10px;
    }
</style>
</head>
<body>

<?= $anulada ?? '' ?>

<!-- ENCABEZADO -->
<table class="header">
    <tr>
        <td width="18%" class="text-center">
            <img src="file://<?php echo $rutaLogo; ?>" width="120"/>
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

<!-- CLIENTE -->
<table class="cliente">
    <tr><th colspan="4">DATOS DEL CLIENTE</th></tr>
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
        <td><?= $factura['fecha'] ?> <?= $factura['hora'] ?></td>
    </tr>
    <tr>
        <td><strong>Dirección:</strong></td>
        <td><?= $factura['direccion'] ?></td>
        <td><strong>Almacén:</strong></td>
        <td><?= $factura['almacen'] ?></td>
    </tr>
    <tr>
        <td><strong>Ejecutivo:</strong></td>
        <td><?= $factura['personal'] ?></td>
        <td><strong>Observación:</strong></td>
        <td><?= $factura['observacion'] ?></td>
    </tr>
</table>

<!-- DETALLE -->
<table class="detalle">
    <thead>
        <tr>
            <th width="12%">CÓDIGO</th>
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

        while ($row = mysqli_fetch_assoc($query_productos)) {
            $precio_total = $row['subtotal'];
            if ($row['proigv'] == 'No Gravada') {
                $exonerado += $precio_total;
            } else {
                $opgrav += $precio_total;
            }
            $descuento += $row['descuentodv'];
        ?>
        <tr>
            <td class="text-center"><?= $row['codigo'] ?></td>
            <td class="text-center"><?= round($row['cantidad'],2) ?></td>
            <td class="text-center"><?= $row['contenedor'] ?></td>
            <td><?= nl2br(htmlspecialchars(wordwrap($row['dproducto'],45,"\n",true))) ?></td>
            <td class="text-right"><?= number_format($row['precio_venta'],2,'.','') ?></td>
            <td class="text-right"><?= number_format($row['descuentodv'],2,'.','') ?></td>
            <td class="text-right"><?= number_format($row['subtotal'],2,'.','') ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php
if ($factura['tipo_comprobante'] == 'Nota de Venta') {
    $igv = 0;
    $tl_sniva = 0;
} else {
    $total = $factura['total_venta'] - $exonerado;
    $igv = Helpers::calcularIgv($total, $iva);
    $tl_sniva = Helpers::calcularBaseImponible($total, $iva);
}
?>

<!-- TOTALES -->
<table class="totales">
    <tr>
        <td width="18%" class="text-center">
            <?php
            require "../../phpqrcode/qrlib.php";
            $ruta_qr = './qr/img2.png';
            $textoQr = $configuracion['ruc']."|".$factura['serie_comprobante']."|".$factura['num_comprobante']."|".$iva."|".$factura['total_venta']."|".$factura['fecha']."|".$factura['num_documento']."|";
            QRcode::png($textoQr, $ruta_qr, "Q", 6, 2);
            ?>
            <img src="file://<?php echo realpath(__DIR__ . '/qr/img2.png'); ?>" width="100">
            <div class="qr-box">
                Consulte en SUNAT
            </div>
        </td>

        <td width="50%">
            <div class="letras">
                <strong>IMPORTE EN LETRAS:</strong><br>
                <?= $con_letra ?>
            </div>
        </td>

        <td width="32%">
            <div class="totales-box">
                <table>
                    <tr><td>Op. Gravada</td><td class="text-right"> <?= $helpers->get_currency_symbol($tl_sniva) ?></td></tr>
                    <tr><td>Op. Exonerada</td><td class="text-right"> <?= $helpers->get_currency_symbol($exonerado) ?></td></tr>
                    <tr><td>Descuento</td><td class="text-right"> <?= $helpers->get_currency_symbol($descuento) ?></td></tr>
                    <tr><td>IGV (<?= $iva ?>%)</td><td class="text-right"> <?= $helpers->get_currency_symbol($igv) ?></td></tr>
                    <tr class="total-final">
                        <td>TOTAL</td>
                        <td class="text-right">S/ <?= number_format($factura['total_venta'],2,'.','') ?></td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

<!-- PAGOS -->
<?php if (!empty($pagos)): ?>
<div class="footer" style="text-align:left;">
    <strong>PAGOS REALIZADOS</strong><br>
    <?php
    $totalPagado = 0;
    foreach ($pagos as $pago) {
        echo $pago['metodo_pago'].': S/ '.number_format($pago['monto'],2,'.','');
        if (!empty($pago['banco'])) echo ' | Banco: '.$pago['banco'];
        if (!empty($pago['nroOperacion'])) echo ' | OP: '.$pago['nroOperacion'];
        echo '<br>';
        $totalPagado += $pago['monto'];
    }
    ?>
    <br>
    <strong>Total Pagado:</strong> S/ <?= number_format($totalPagado,2,'.','') ?><br>
    <strong>Saldo:</strong> S/ <?= number_format($factura['total_venta'] - $totalPagado,2,'.','') ?>
</div>
<?php endif; ?>

<!-- PIE -->
<div class="footer">
    <strong>Representación impresa de la <?= strtoupper($factura['tipo_comprobante']) ?> ELECTRÓNICA</strong><br>
    Autorizado mediante SEE - Del Contribuyente.<br>
    Gracias por su preferencia.
</div>

</body>
</html>