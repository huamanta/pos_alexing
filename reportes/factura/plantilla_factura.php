<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= $tipoDocumento ?></title>

<style>
body {
      font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
      font-size: 12px;
    }
table {
    width: 100%;
    border-collapse: collapse;
}

.h2 { font-size: 16px; font-weight: bold; }
.round {
    border: 1px solid #000;
    border-radius: 10px;
    padding: 8px;
}
.info-factura-box h3, .info-factura-box p {
    margin: 0;
    padding: 0;
    line-height: 1.2; /* Adjust line height for closer packing */
}

.textcenter { text-align: center; }
.textright { text-align: right; }
.textleft { text-align: left; }

th {
    /* Remove background and general side borders */
    background: none; 
    border: none; /* Reset all borders */
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    padding: 5px;
    text-align: center; /* Center text by default */
}
thead th:first-child {
    border-left: 1px solid #000;
}
thead th:last-child {
    border-right: 1px solid #000;
}

td {
    padding: 4px;
}
</style>
</head>

<body>

<div id="page_pdf">

<!-- ================= ENCABEZADO ================= -->
<table>
<tr>
<td width="25%">
<?php if (!empty($logo)): ?>
<img src="<?= $logo ?>" width="160">
<?php endif; ?>
</td>

<td width="45%">
<span class="h2"><?= $nombreNegocio ?></span><br>
Teléfono: <?= $telefonoNegocio ?><br>
Email: <?= $emailNegocio ?><br>
<!--<span class="h2"><?= $empresa ?></span><br>-->
RUC: <?= $ruc ?><br>
<?= $direccion ?><br>
</td>

<td width="50%">
<div class="round info-factura-box" style="text-align: center; border-radius: 10px;">
    <h3>R.U.C. <?= $ruc ?></h3>
     <p class="h2"><?= $tipoDocumento ?></p>
     <p><?= $serie ?></p>
</div>
</td>
</tr>
</table>

<br>

<!-- ================= CLIENTE ================= -->


<label>
    <strong>Fecha Emisión: </strong><?= $fecha ?>&nbsp;&nbsp;
    <strong>Hora: </strong><?= $hora ?>
</label>
<table  style="width: 100%; border-collapse: separate; border-spacing: 0;">
    <thead>
        
    </thead>
    <tbody style="border: 1px solid black; border-radius: 8px; overflow: hidden; display: table-row-group;">
        <tr>
          <td style=" padding-left: 5px;"><strong>Cliente: </strong><?= $cliente ?></td>
        </tr>
        <tr>
          <td style=" padding-left: 5px;"><strong><?= $tipoDoc ?>: </strong><?= $numDoc ?></td>
        </tr>
        <tr>
          <td style=" padding-left: 5px;"><strong>Dirección: </strong><?= $direccionCliente ?></td>
        </tr>
        <tr>
          <td style=" padding-left: 5px;"><strong>Moneda: </strong><?= $moneda ?></td>
        </tr>
    </tbody>
</table>     


<!--fin de encabezado datos cliente-->
<br>

<!-- ================= DETALLE ================= -->
<table style="width: 100%; border-collapse: separate; border-spacing: 0;">
<thead style="border-radius: 5px; overflow: hidden; display: table-header-group;">
  <tr>
    <th style="border-top-left-radius: 5px;text-align: center;">CANT.</th>
    <th style="width: 250px;text-align: center;">DESCRIPCIÓN</th>
    <th style="text-align:center;">P. UNIT</th>
    <th  style="border-top-right-radius: 5px;text-align: center;">SUBTOTAL</th>
  </tr>
</thead>

<tbody>
<?= $detalle ?>
</tbody>
</table>

<br>

<!-- ================= TOTALES ================= -->
<table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 10px;">
    <tbody>
        <tr>
            <!-- QR Code -->
            <td rowspan="10" style="text-align: center; vertical-align: middle; width: 20%; border: none;">
                <?php
                    // Ensure the QR directory exists
                    if (!file_exists(__DIR__ . '/qr')) {
                        mkdir(__DIR__ . '/qr', 0777, true);
                    }
                    require_once __DIR__ . '/../../phpqrcode/qrlib.php';
                    $ruta_qr = __DIR__ . '/qr/' . preg_replace('/[^a-zA-Z0-9-]/', '', $serie) . '.png';
                    $texto_qr = $ruc . "|" . preg_replace('/[^0-9]/', '', $tipoDocumento) . "|" . $serie . "|" . number_format($igv, 2, '.', '') . "|" . number_format($total, 2, '.', '') . "|" . date('Y-m-d', strtotime(str_replace('/', '-', $fecha))) . "|" . $numDoc . "|";
                    QRcode::png($texto_qr, $ruta_qr, 'Q', 10, 2);
                    
                    // Embed image as base64 for dompdf reliability
                    $qr_type = pathinfo($ruta_qr, PATHINFO_EXTENSION);
                    $qr_data = file_get_contents($ruta_qr);
                    $qr_base64 = 'data:image/' . $qr_type . ';base64,' . base64_encode($qr_data);
                    echo '<img src="' . $qr_base64 . '" style="width: 120px;">';
                ?>
            </td>
            <!-- Importe en letras -->
            <td rowspan="10" style="border: 1px solid black; border-radius: 10px; vertical-align: top; padding: 8px; width: 50%;">
                <strong>IMPORTE EN LETRAS:</strong><br>
                <?= $totalLetras ?>
            </td>

            <td rowspan="10" style="width: 1%; border: none;">&nbsp;</td>

            <!-- Totals Rows -->
            <td style="text-align: right; font-weight: bold; padding: 3px;">Op. Gravada</td>
            <td style="border: 1px solid black; border-radius: 5px; text-align: center; font-size: 12px; padding: 3px;"><?= number_format($opGravada, 2) ?></td>
        </tr>
        <tr>
            <td style="text-align: right; font-weight: bold; padding: 3px;">Op. Exonerada</td>
            <td style="border: 1px solid black; border-radius: 5px; text-align: center; font-size: 12px; padding: 3px;"><?= number_format($opExonerada, 2) ?></td>
        </tr>
        <tr>
            <td style="text-align: right; font-weight: bold; padding: 3px;">Op. Inafecta</td>
            <td style="border: 1px solid black; border-radius: 5px; text-align: center; font-size: 12px; padding: 3px;"><?= number_format($opInafecta, 2) ?></td>
        </tr>
        <tr>
            <td style="text-align: right; font-weight: bold; padding: 3px;">ISC</td>
            <td style="border: 1px solid black; border-radius: 5px; text-align: center; font-size: 12px; padding: 3px;"><?= number_format($isc, 2) ?></td>
        </tr>
        <tr>
            <td style="text-align: right; font-weight: bold; padding: 3px;">IGV</td>
            <td style="border: 1px solid black; border-radius: 5px; text-align: center; font-size: 12px; padding: 3px;"><?= number_format($igv, 2) ?></td>
        </tr>
        <tr>
            <td style="text-align: right; font-weight: bold; padding: 3px;">ICBPER</td>
            <td style="border: 1px solid black; border-radius: 5px; text-align: center; font-size: 12px; padding: 3px;"><?= number_format($icbper, 2) ?></td>
        </tr>
        <tr>
            <td style="text-align: right; font-weight: bold; padding: 3px;">Otros Cargos</td>
            <td style="border: 1px solid black; border-radius: 5px; text-align: center; font-size: 12px; padding: 3px;"><?= number_format($otrosCargos, 2) ?></td>
        </tr>
        <tr>
            <td style="text-align: right; font-weight: bold; padding: 3px;">Otros Tributos</td>
            <td style="border: 1px solid black; border-radius: 5px; text-align: center; font-size: 12px; padding: 3px;"><?= number_format($otrosTributos, 2) ?></td>
        </tr>
        <tr>
            <td style="text-align: right; font-weight: bold; padding: 3px;">Monto de Redondeo</td>
            <td style="border: 1px solid black; border-radius: 5px; text-align: center; font-size: 12px; padding: 3px;"><?= number_format($redondeo, 2) ?></td>
        </tr>
        <tr>
            <td style="text-align: right; font-weight: bold; padding: 3px;">Importe Total</td>
            <td style="border: 1px solid black; border-radius: 5px; text-align: center; font-size: 12px; padding: 3px;"><?= number_format($total, 2) ?></td>
        </tr>
    </tbody>
</table>

<br>

<!-- ================= PIE ================= -->
<div class="round textcenter">
Representación impresa del comprobante electrónico<br>
<strong>¡Gracias por su compra!</strong>
</div>

</div>

</body>
</html>
