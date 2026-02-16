<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
    session_start();

if (!isset($_SESSION["nombre"])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
    if ($_SESSION['pos'] == 1) {

        //Incluímos la clase Venta
        require_once "../modelos/Venta.php";
        require_once "Letras.php";
        $V = new EnLetras();

        $venta = new Venta();
        $rspta = $venta->ventacabecera($_GET["id"]);
        $reg = $rspta->fetch_object();

        // Obtenemos deuda si aplica
        require_once "../modelos/CuentasCobrar.php";
        $cc = new CuentasCobrar();
        $rsptacc = $cc->mostrarDeuda($_GET["id"]);
        $regcc = $rsptacc->fetch_object();

        $formaPago = ($reg->ventacredito == "Si") ? "CRÉDITO" : "CONTADO";

        // Datos de la empresa
        require_once "../modelos/Negocio.php";
        $cnegocio = new Negocio();
        $rsptan = $cnegocio->listar();
        $regn = $rsptan->fetch_object();
        $empresa = $regn->nombre;
        $ndocumento = $regn->ndocumento;
        $documento = $regn->documento;
        $telefono = $regn->telefono;
        $email = $regn->email;
        $pais = $regn->pais;
        $ciudad = $regn->ciudad;
        $imagen = $regn->logo;
        $direccion = $regn->direccion;
        /*require_once "../modelos/Categoria.php";
        $categoria = new Categoria();
        $rsptas = $categoria->mostrarSucursalTi($reg->idsucursal);
        $regs = $rsptas ? $rsptas->fetch_object() : null;

        if ($regs) {
            $direccion = $regs->direccion;  //  Dirección del almacén / sucursal
            $telefono = $regs->telefono ?: $telefono; // Si la sucursal tiene su propio teléfono
            $distrito = $regs->distrito ?: $distrito;
        } else {
            // Si no se encuentra la sucursal, usar la dirección del negocio
            $direccion = $regn->direccion;
        }*/

        // Calcular IGV
        $igv = 0;
        if ($reg->tipo_comprobante != 'Nota de Venta') {
            $igv = round(((($reg->total_venta) * ($reg->impuesto / ($reg->impuesto + 100)))), 2);
        }

        // Generar QR
        if ($reg->tipo_comprobante == 'Boleta') {
            $iddoc = '01';
            $iddocCliente = '6';
        } else if ($reg->tipo_comprobante == 'Factura') {
            $iddoc = "03";
            $iddocCliente = (strlen($reg->num_documento) == 8) ? "1" : "4";
        } else {
            $iddoc = '07';
            $iddocCliente = '6';
        }

        $texto = $documento . "|" . $iddoc . "|" . $reg->serie_comprobante . "|" . $reg->num_comprobante . "|" . $igv . "|" . $reg->total_venta . "|" . $reg->fecha . "|" . $iddocCliente . "|" . $reg->num_documento . "|";

        $ruta_qr = 'qr/' . 'img_' . uniqid() . '.png';
        if (file_exists("../phpqrcode/qrlib.php")) {
            require "../phpqrcode/qrlib.php";
            $tamaño = 4;
            $level = "Q";
            $framSize = 1;
            QRcode::png($texto, $ruta_qr, $level, $tamaño, $framSize);
        }
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <style>
        body { 
            font-family: 'Arial Narrow', Arial, sans-serif; 
            font-size: 9pt; 
            margin: 0; 
            padding: 0; 
            color: #000; 
        }
        .zona_impresion { width: 260px; margin: auto; }
        .ticket-header { text-align: center; margin-bottom: 5px; }
        .ticket-header img { margin-bottom: 3px; }
        .ticket-header strong { font-size: 11pt; display: block; margin-top: 2px; }
        .ticket-header div { font-size: 9pt; }
        .ticket-section { margin: 5px 0; font-size: 10pt; }
        .ticket-section table { width: 100%; border-collapse: collapse; }
        .ticket-section td { padding: 1px 0; vertical-align: top; }
        .line-separator { border-top: 1px dashed #000; margin: 5px 0; }
        .productos th { font-size: 8pt; text-align: left; border-bottom: 1px dashed #000; padding-bottom: 2px; }
        .productos td { font-size: 8pt; padding: 2px 0; }
        .totales td { font-size: 9pt; padding: 1px 0; }
        .totales b { font-size: 9pt; }
        .qr { margin-top: 5px; text-align: center; }
        .nota-final { text-align: center; font-size: 8pt; margin-top: 6px; border-top: 1px dashed #000; padding-top: 4px; }
    </style>
</head>

<body onload="window.print();">

<div class="zona_impresion">

    <!-- CABECERA EMPRESA -->
    <div class="ticket-header">
        <img src="../reportes/<?php echo $imagen; ?>" width="100" height="50"><br>
        <strong><?php echo $empresa; ?></strong>
        <div><?php echo $ndocumento; ?>: <?php echo $documento; ?></div>
        <div><?php echo $direccion; ?></div>
        <div>Tel: <?php echo $telefono; ?> | <?php echo $email; ?></div>
        <div><?php echo $ciudad . " - " . $pais; ?></div>
    </div>

    <!-- DATOS DOCUMENTO -->
    <div class="ticket-section" style="text-align:center;">
        <strong>
            <?php 
                if ($reg->tipo_comprobante == "NC") {
                    echo "NOTA DE CRÉDITO";
                } else if ($reg->tipo_comprobante == "Boleta" || $reg->tipo_comprobante == "Factura") {
                    echo $reg->tipo_comprobante . " ELECTRÓNICA";
                } else {
                    echo $reg->tipo_comprobante . " ELECTRÓNICA";
                }
            ?><br>
            <?php echo $reg->serie_comprobante . "-" . $reg->num_comprobante; ?>
        </strong>
    </div>

    <!-- CLIENTE -->
        <!-- CLIENTE -->
    <div class="ticket-section">
        <table>
            <tr><td><b>Almacen: </b></td><td style="font-size:12px"> <?php echo $reg->sucursal; ?></td></tr>
            <tr><td><b>Fecha: </b></td><td style="font-size:12px"> <?php echo $reg->fecha_kardex; ?></td></tr>
            <tr><td><b>Cliente: </b></td><td style="font-size:12px"> <?php echo $reg->cliente; ?></td></tr>
            <tr><td><b><?php echo $reg->tipo_documento;?>: </b></td><td style="font-size:12px"> <?php echo $reg->num_documento; ?></td></tr>
            <tr><td><b>Pago: </b></td><td style="font-size:12px"> <?php echo $formaPago; ?></td></tr>
            <tr><td><b>Obsv: </b></td><td style="font-size:12px"> <?php echo $reg->observacion; ?></td></tr>

            <?php 
                if ($reg->ventacredito == "Si") { 
                    $rsptacc = $cc->mostrarDeuda($_GET["id"]);
                    if ($rsptacc) {
                ?>
                    <tr><td colspan="2"><div class="line-separator"></div></td></tr>
                    <tr>
                        <td colspan="2"><b>Detalle de cuotas:</b></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table style="width:100%; font-size:11px; border-collapse:collapse;">
                                <tr>
                                    <th style="text-align:left;">Vence</th>
                                    <th style="text-align:right;">Deuda</th>
                                    <th style="text-align:right;">Abono</th>
                                    <th style="text-align:right;">Saldo</th>
                                </tr>
                                <?php while ($regcc = $rsptacc->fetch_object()) { ?>
                                <tr>
                                    <td><?php echo date("d/m", strtotime($regcc->fechavencimiento)); ?></td>
                                    <td style="text-align:right;">S/ <?php echo number_format($regcc->deudatotal,2,",","."); ?></td>
                                    <td style="text-align:right;">S/ <?php echo number_format($regcc->abonototal,2,",","."); ?></td>
                                    <td style="text-align:right;">S/ <?php echo number_format($regcc->saldo_pendiente,2,",","."); ?></td>
                                </tr>
                                <?php } ?>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="2"><div class="line-separator"></div></td></tr>
                <?php 
                    } 
                } 
                ?>


        </table>
    </div>


    <div class="line-separator"></div>

    <!-- DETALLES -->
    <table class="productos">
        <tr>
            <th style="width:35px;font-size:14px">Cant</th>
            <th style="text-align:center;font-size:14px">Descripción</th>
            <th style="text-align:center;font-size:14px">P. Unit</th>
            <th style="text-align:right;font-size:14px">Importe</th>
        </tr>
        <?php
        $rsptad = $venta->ventadetalle($_GET["id"]);
        $items = 0;
        $cantidad = 0; $subtotal = 0; $descuento = 0;
        $exonerado = 0;        
        $opgrav = 0;
        while ($regd = $rsptad->fetch_object()) {
            $items++;

            $linea_total = $regd->subtotal; // viene ya calculado por check_precio o cantidad*precio

            if ($regd->proigv == "No Gravada") {
                $exonerado += $linea_total;
            } else {
                $opgrav += $linea_total;
            }

            echo "<tr>";
            echo "<td style='font-size:14px'>".number_format($regd->cantidad,2,",",".")."</td>";
            echo "<td style='font-size:14px;text-align:center'>" . $regd->dproducto.' - '.$regd->contenedor.' '."</td>";
            echo "<td style='font-size:14px'>".number_format($regd->precio_venta,2,",",".")."</td>";
            echo "<td style='text-align:right;font-size:14px'>".number_format($linea_total,2,",",".")."</td>";
            echo "</tr>";

            $cantidad += $regd->cantidad;
            $subtotal += $linea_total;
            $descuento += $regd->descuento;
        }
        ?>
    </table>

    <div class="line-separator"></div>

    <!-- TOTALES -->
    <!-- TOTALES -->
<table class="totales" width="100%">
    <tr>
        <td align="right"><b>SUBTOTAL:</b></td>
        <td align="right">S/ <?php echo number_format($subtotal, 2, ",", "."); ?></td>
    </tr>

    <?php if ($reg->tipo_comprobante != 'Nota de Venta') { ?>
        <tr>
            <td align="right"><b>IGV (<?php echo number_format($reg->impuesto, 0); ?>%):</b></td>
            <td align="right">
                <b>S/ <?php
                    $igv = round(((($reg->total_venta) - $exonerado) * (($reg->impuesto) / ($reg->impuesto + 100))), 2);
                    echo number_format($igv, 2, ",", ".");
                ?></b>
            </td>
        </tr>
        <tr>
            <td align="right"><b>OP. GRAV:</b></td>
            <td align="right">
                <b>S/ <?php
                    $opgrav = (($reg->total_venta) - $exonerado) - $igv;
                    echo number_format($opgrav, 2, ",", ".");
                ?></b>
            </td>
        </tr>
        <?php if ($exonerado > 0) { ?>
            <tr>
                <td align="right"><b>EXONERADO:</b></td>
                <td align="right"><b>S/ <?php echo number_format($exonerado, 2, ",", "."); ?></b></td>
            </tr>
        <?php } ?>
    <?php } ?>

    <tr>
        <td align="right"><b>TOTAL:</b></td>
        <td align="right">S/ <?php echo number_format($reg->total_venta, 2, ",", "."); ?></td>
    </tr>
    <tr>
        <td align="right"><b>VUELTO:</b></td>
        <td align="right">S/ <?php echo number_format($reg->vuelto, 2, ",", "."); ?></td>
    </tr>
</table>


    <div class="line-separator"></div>

    <!-- PAGO DETALLE (RESUMEN + DESGLOSE) -->
    <div class="ticket-section">
        <?php if ($reg->ventacredito == "No") { ?>
            <div class="ticket-section">
                <b>PAGO CON:</b> S/ <?php echo number_format($reg->totalrecibido,2,",","."); ?><br>
                <?php
                // Desglose de métodos de pago y montos
                $rspagos = $venta->pagosPorVenta($_GET["id"]);
                if ($rspagos) {
                    while ($rp = $rspagos->fetch_object()) {
                        echo "- ".strtoupper($rp->metodo_pago).": S/ ".number_format($rp->monto,2,",",".")."<br>";
                    }
                }
                ?>
            </div>
        <?php } ?>

    </div>

    <div class="line-separator"></div>

    <!-- INFO FINAL -->
    <div class="ticket-section">
        <b>Vendedor:</b> <?php echo $reg->personal; ?><br>
        <b>Items:</b> <?php echo $items; ?>
    </div>

    <!-- QR -->
    <div class="qr">
        <?php if (file_exists($ruta_qr)) { ?>
            <img src="<?php echo $ruta_qr; ?>" width="90" height="90"><br>
        <?php } ?>
    </div>

    <div class="nota-final">
        ¡Gracias por su preferencia!<br>
        Este documento es representación impresa de la compra.
    </div>
</div>

<script type="text/javascript">
    window.onafterprint=function(){ window.close() }
</script>
</body>
</html>
<?php
    } else {
        echo 'No tiene permiso para visualizar el reporte';
    }
}
ob_end_flush();
?>
