<?php
$subtotal   = 0;
$iva        = 0;
$impuesto   = 0;
$tl_sniva   = 0;
$total      = 0;
$descuento = 0;
$exonerado = 0;
$opgrav = 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?php echo $factura['tipo_comprobante']; ?></title>
  <link rel="stylesheet" href="style.css">
  <style>
    thead:empty {
      display: none;
    }

    @font-face {
    font-family: 'Arial Narrow';
    src: url('pdf/vendor/fonts/arial-narrow.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
    }

    body {
      font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
      font-size: 12px;
    }

    table, th, td {
      font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
      font-size: 11px;
    }

    strong, b, h1, h2, h3, h4, h5, h6 {
      font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <?php echo $anulada; ?>
  <div id="page_pdf">
    <!-- ENCABEZADO EMPRESA -->
    <table id="factura_head">
      <tr>
        <td class="logo_factura">
          <div>
            <img src="../<?php echo $configuracion['logo']; ?>" width="180px">
          </div>
        </td>
        <td class="info_empresa">
          <?php
          if ($result_config > 0) {
            $iva = $configuracion['monto_impuesto'];
          ?>
            <div>
              <span class="h2"><?php echo $configuracion['nombre']; ?></span>
              <p>RUC <?php echo $configuracion['documento']; ?></p>
              <p><?php echo $configuracion['direccion']; ?></p>
              <p>Teléfono: <?php echo $configuracion['telefono']; ?></p>
              <p>Email: <?php echo $configuracion['email']; ?></p>
            </div>
          <?php } ?>
        </td>
        <td class="info_factura">
          <div class="round" style="text-align: center; border-radius: 10px;">
            <br>
            <h3>R.U.C. <?php echo $configuracion['documento']; ?></h3>
            <?php if ($factura['tipo_comprobante'] == 'Nota de Venta'): ?>
              <p class="h2"><?php echo strtoupper($factura['tipo_comprobante']); ?></p>
            <?php else: ?>
              <p class="h2"><?php echo strtoupper($factura['tipo_comprobante']); ?> ELECTRÓNICA</p>
            <?php endif; ?>
            <p><?php echo $factura['serie_comprobante'] . ' - ' . $factura['num_comprobante']; ?></p>
          </div>
        </td>
      </tr>
    </table>

    <!-- DATOS CLIENTE -->
    <?php
    $formaPago = ($factura['ventacredito'] == "Si") ? "CRÉDITO" : "CONTADO";
    ?>
    <label>
      <strong>Fecha Emisión: </strong><?php echo $factura['fecha']; ?>&nbsp;&nbsp;
      <strong>Hora: </strong><?php echo $factura['hora']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <strong>Almacén: </strong><?php echo $factura['almacen']; ?>
    </label>

    <table id="factura_detalle" style="width: 100%; border-collapse: separate; border-spacing: 0;">
      <thead>
      </thead>
      <tbody style="border: 1px solid black; border-radius: 8px; overflow: hidden; display: table-row-group;">
        <tr>
          <td style=" padding-left: 5px;"><strong>Cliente: </strong><?php echo $factura['cliente']; ?></td>
          <td style=" padding-left: 5px;"><strong>Forma de Pago: </strong><?php echo $formaPago; ?></td>
        </tr>
        <tr>
          <td style=" padding-left: 5px;"><strong><?php echo $factura['tipo_documento']; ?>: </strong><?php echo $factura['num_documento']; ?></td>
          <td style=" padding-left: 5px;">
            <?php if ($factura['ventacredito'] == "Si"): ?>
                <strong>Anticipo: </strong><?php echo number_format($factura['montoPagado'], 2, ",", "."); ?>
                <?php
                if (!empty($cuentasc['idventa'])) {
                    echo ' - ' . $factura['formaPago'] . ' ' . $factura['banco'] . ' OP: ' . $factura['numoperacion'] . ' ' . $factura['fechadeposito'];
                }
                ?>
            <?php endif; ?>
        </td>
        </tr>
        <tr>
          <td style="padding-left: 5px;"><strong>Dirección: </strong><?php echo $factura['direccion']; ?></td>
          <td style=" padding-left: 5px;">
            <?php if ($factura['ventacredito'] == "Si"): ?>
              <strong>Saldo: S/. </strong><?php echo number_format($cuentasc['totalDeuda'], 2, ",", "."); ?>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td  style="padding-left: 5px;"><strong>Ejecutivo: </strong><?php echo $factura['personal']; ?></td>
          <td  style="padding-left: 5px;"><strong>Observaciones: </strong><?php echo $factura['observacion']; ?></td>
        </tr>
      </tbody>
    </table>

    <!-- DETALLE DE PRODUCTOS -->
    <?php if ($result_detalle > 0): ?>
      <table id="factura_detalle" style="width: 100%; border-collapse: separate; border-spacing: 0;">
        <thead style="border: 1px solid black; border-radius: 5px; overflow: hidden; display: table-header-group;">
          <tr>
            <th style="border-top-left-radius: 5px;text-align: center;">CÓDIGO</th>
            <th style="text-align:center;">CANT.</th>
            <th style="text-align:center;">UM</th>
            <th style="width: 250px;text-align: center;">DESCRIPCIÓN</th>
            <!--<th>MARCA</th>-->
            <th style="text-align:center;">P.UNIT</th>
            <th style="text-align:center;">DESC</th>
            <th style="border-top-right-radius: 5px;text-align: center;">SUBTOTAL</th>
          </tr>
        </thead>
        <br>
        <tbody>
          <?php
          $descuento = 0;
          $exonerado = 0;
          $opgrav = 0;
          while ($row = mysqli_fetch_assoc($query_productos)) {
            ?>
                <tr>
                    <td class="textcenter" style="font-size: 12px"><?php echo $row['codigo']; ?></td>
                    <td class="textcenter" style="font-size: 12px"><?php echo round($row['cantidad'], 2); ?></td>
                    <td class="textcenter" style="font-size: 12px"><?php echo $row['contenedor']; ?></td>
                    <td width="250px" style="white-space: pre-line; line-height:1.2;">
                        <?php
                        $descripcion = $row['dproducto'];
                        if (strpos($descripcion, "\n") === false) {
                            $descripcion = wordwrap($descripcion, 45, "\n", true);
                        }
                        echo nl2br(htmlspecialchars($descripcion));
                        ?>
                    </td>
                    <td class="textcenter" style="font-size: 12px">
                      <?php echo number_format($row['precio_venta'], 2, ".", ""); ?>
                    </td>
                    <td class="textcenter" style="font-size: 12px">
                      <?php echo $row['descuentodv']; ?>
                    </td>
                    <td class="textcenter">
                      <?php echo number_format($row['subtotal'], 2, ".", ""); ?>
                    </td>
                </tr>
            <?php
                // ==== NUEVO BLOQUE DE CÁLCULO CORRECTO ====
                $precio_total = $row['subtotal'];
                $subtotal += $precio_total;

                if ($row['proigv'] == "No Gravada") {
                    $exonerado += $precio_total;
                } else {
                    $opgrav += $precio_total;
                }

                $descuento += $row['descuentodv'];
            }
            ?>
          ?>
        </tbody>
      </table>
    <?php endif; ?>

    <!-- TOTALES -->
    <?php
    // Totales
    // =================== TOTALES ===================
    if ($factura['tipo_comprobante'] == 'Nota de Venta') {
    // En nota de venta, no se discrimina IGV
    $opgrav = 0;
    $exonerado = 0;
    $igv = 0;
    $tl_sniva = 0;
} else {
    // Si hay productos no gravados (exonerados)
    if ($exonerado > 0 && $opgrav == 0) {
        // Boleta o factura exonerada
        $igv = 0;
        $tl_sniva = 0;
        $exonerado = $factura['total_venta'];
    } else {
        // Boleta o factura gravada (con IGV)
        $igv = round((($factura['total_venta'] - $exonerado) * ($iva / ($iva + 100))), 2);
        $opgrav = ($factura['total_venta'] - $exonerado) - $igv;
        $tl_sniva = $opgrav;
    }
}


    // Base imponible sin IGV (solo si es documento afecto)
    $tl_sniva = $opgrav;


    // Letras
    require_once "../Letras.php";
    $V = new EnLetras();
    $con_letra = strtoupper($V->ValorEnLetras(($factura['total_venta']), "CON"));
    ?>

    <table id="detalle_totales" style="width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 10px;">
      <tfoot>
        <tr>
          <!-- QR sin borde -->
          <td colspan="2" rowspan="6" style="text-align: center; vertical-align: middle; border: none;">
            <?php
              require "../../phpqrcode/qrlib.php";
              $ruta_qr = '../qr/img2.png';
              $texto = $configuracion['documento'] . "|" . $factura['serie_comprobante'] . "|" . $factura['num_comprobante'] . "|" . $iva . "|" . $factura['total_venta'] . "|" . $factura['fecha'] . "|" . $factura['num_documento'] . "|";
              QRcode::png($texto, $ruta_qr, "Q", 10, 3);
              echo '<img src="../qr/img2.png" width="130px" style="margin-top: 10px;"><br>';
            ?>
          </td>

          <!-- Importe en letras con estilo -->
          <td colspan="4" rowspan="6"
            style="
              border: 1px solid black;
              border-radius: 10px;
              vertical-align: top;
              padding: 8px;
              background-color: #f9f9f9;
              box-shadow: 0 0 4px rgba(0,0,0,0.1);
              border-collapse: separate;
              overflow: hidden;
            ">
            <strong>IMPORTE EN LETRAS:</strong><br>
            <?php echo $con_letra; ?>
          </td>

          <!-- Espacio entre importe y operaciones -->
          <td rowspan="6" style="width: 5px; border: none; background-color: #ffffff;"></td>

          <!-- Totales -->
          <td style="border: 1px solid black; border-right: none; text-align: right;  font-weight: 600;">TOTAL S/</td>
          <td style="border: 1px solid black; border-left: none; text-align: center; font-size: 12px;">
            <?php echo number_format($factura['total_venta'], 2, ".", ""); ?>
          </td>
        </tr>

        <tr>
          <td style="border: 1px solid black; border-right: none; text-align: right;  font-weight: 600;">OP. GRAVADA S/</td>
          <td style="border: 1px solid black; border-left: none; text-align: center; font-size: 12px;">
            <?php echo number_format($tl_sniva, 2, ".", ""); ?>
          </td>
        </tr>

        <tr>
          <td style="border: 1px solid black; border-right: none; text-align: right;  font-weight: 600;">OP. EXONERADO S/</td>
          <td style="border: 1px solid black; border-left: none; text-align: center; font-size: 12px;">
            <?php echo number_format($exonerado, 2, ".", ""); ?>
          </td>
        </tr>

        <tr>
          <td style="border: 1px solid black; border-right: none; text-align: right;  font-weight: 600;">DESCUENTO S/</td>
          <td style="border: 1px solid black; border-left: none; text-align: center; font-size: 12px;">
            <?php echo number_format($descuento, 2, ".", ""); ?>
          </td>
        </tr>

        <tr>
          <td style="border: 1px solid black; border-right: none; text-align: right; font-weight: 600;">
            IGV (<?php echo $iva; ?>%) S/
          </td>
          <td style="border: 1px solid black; border-left: none; text-align: center; font-size: 12px;">
            <?php echo number_format($igv, 2, ".", ""); ?>
          </td>
        </tr>

        <tr>
          <td style="border: 1px solid black; border-right: none; border-bottom: 1px solid black; text-align: right; font-weight: 600;">
            TOTAL A PAGAR S/
          </td>
          <td style="border: 1px solid black; border-left: none; border-bottom: 1px solid black; text-align: center; font-size: 12px;">
            <?php echo number_format($factura['total_venta'] , 2, ".", ""); ?>
          </td>
        </tr>
      </tfoot>

      <?php if (!empty($pagos)): ?>
      <tr>
        <td colspan="7" style="padding-top: 5px;">
          <div style="
            border: 1px solid #000;
            border-radius: 4px;
            padding: 5px 10px;
            background-color: #f2f2f2;
            width: 100%;
            box-sizing: border-box;
            font-size: 12px;
            line-height: 1.3;
          ">
            <strong>PAGOS REALIZADOS:</strong><br>
            <?php 
              $totalPagado = 0;
              foreach ($pagos as $pago) {
                  echo $pago['metodo_pago'] . ': S/. ' . number_format($pago['monto'], 2, ".", "");
                  if(!empty($pago['banco'])) echo ' | Banco: ' . $pago['banco'];
                  if(!empty($pago['nroOperacion'])) echo ' | OP: ' . $pago['nroOperacion'];
                  if(!empty($pago['fechaDeposito']) && $pago['fechaDeposito'] != '0000-00-00') {
                      echo ' | Fecha: ' . date('d/m/Y', strtotime($pago['fechaDeposito']));
                  }
                  echo '<br>';
                  $totalPagado += $pago['monto'];
              }
            ?>
            <br>
            <strong>Total Pagado: S/. </strong><?php echo number_format($totalPagado, 2, ".", ""); ?><br>
            <strong>Saldo: S/. </strong><?php echo number_format($factura['total_venta'] - $totalPagado, 2, ".", ""); ?>
          </div>
        </td>
      </tr>
      <?php endif; ?>
      <tr>
        <td colspan="7" style="padding-top: 5px;">
          <div style="
            border: 1px solid #000;
            border-radius: 4px;
            padding: 3px 6px;
            box-shadow: 0 0 2px rgba(0,0,0,0.05);
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            font-size: 12px;
            line-height: 1.2;
          ">
            <?php
              echo '<span>Representación Impresa de: </span>';
              echo '<span style="font-weight: 600;">' . $factura['tipo_comprobante'] . ' Electrónica</span>';
            ?>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="7" style="padding-top: 5px;">
          <div style="
            border: 1px solid #000;
            border-radius: 4px;
            padding: 3px 6px;
            box-shadow: 0 0 2px rgba(0,0,0,0.05);
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            font-size: 12px;
            line-height: 1.3;
          ">
            <strong>CUENTAS BANCARIAS:</strong><br>
            BCP: 123-45678901-0-12 &nbsp;|&nbsp; INTERBANK: 123-4567890123 &nbsp;|&nbsp; BBVA: 123-9876543210
          </div>
        </td>
      </tr>
    </table>

    <!-- NOTA -->
    <?php if ($factura['tipo_comprobante'] == 'Nota'): ?>
      <table id="factura_cliente">
        <tr>
          <td class="info_cliente">
            <div class="round">
              <table class="detalle_totales">
                <tr>
                  <td colspan="4" style="text-align: center; padding: 5px;">
                    <h3>DOCUMENTO NO VÁLIDO COMO COMPROBANTE DE PAGO</h3>
                  </td>
                </tr>
                <tr>
                  <td colspan="4" style="text-align: center;">Este comprobante puede ser canjeado por factura al momento de recoger su pedido</td>
                </tr>
              </table>
            </div>
          </td>
        </tr>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>
