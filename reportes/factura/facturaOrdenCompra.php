<?php
$subtotal 	= 0;
$iva 	 	= 0;
$impuesto 	= 0;
$tl_sniva   = 0;
$total 		= 0;
//print_r($configuracion); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Comprobante</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<?php echo $anulada; ?>
	<div id="page_pdf">
		<table id="factura_head">
			<tr>
				<td class="logo_factura">
					<div>
						<img src="../<?php echo $configuracion['logo']; ?>" width="150px">
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
					<?php
					}
					?>
				</td>
				<td class="info_factura">
					<div class="round" style="text-align: center;">
						<br>
						<p><strong>
								<h3>R. U. C. <?php echo $configuracion['documento']; ?>
							</strong></h3>
						</p>

						<p class="h2">
								ORDEN DE COMPRA
						</p>
						<p><?php echo $factura['serie_comprobante'] . ' - ' . $factura['num_comprobante']; ?></p>

					</div>
				</td>
			</tr>
		</table>

		<label><strong>Fecha Emisión : </strong><?php echo $factura['fecha']; ?></label>

		<table id="factura_detalle" style="width: 100%;">
			<thead>
				<tr>
					<th style="border: 1px solid black; width: 370px; overflow: auto;">DATOS DEL PROVEEDOR</th>
					<th style="border: 1px solid black;">CONDICIONES GENERALES</th>
				</tr>
			</thead>
			<tbody id="detalle_productos" style="border: 1px solid black;">
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong>Razón Social : </strong></label><?php echo $factura['proveedor']; ?></td>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong>Forma de Pago : </strong></label><?php echo $factura['formapago']; ?></td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong><?php echo $factura['tipo_documento']; ?> </strong></label><?php echo $factura['num_documento']; ?></td>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong>Facturar a : </strong></label><?php echo $factura['documento']; ?></td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px; font-size: 8pt;"><label><strong>Dirección : </strong></label><?php echo $factura['direccion']; ?></td>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong>Lugar de Entrega : </strong></label><?php echo $factura['lugar_entrega']; ?></td>
				</tr>
			</tbody>
			<!-- <tfoot id="detalle_totales"> -->
			</tfoot>
			<tr>
				<td style="border: 1px solid black; padding-left: 2px;" colspan="2"><strong>CONCEPTO : </strong><?php echo $factura['motivo_compra']; ?></td>
			</tr>
		</table>

		<table id="factura_detalle" style="width: 100%;">
			<thead>
				<tr>
					<th style="border: 1px solid black;" class="textcenter" width="5px">CÓDIGO.</th>
					<th style="border: 1px solid black;" class="textcenter" width="5px">CANT.</th>
					<th style="border: 1px solid black;" class="textcenter" width="20px">UM.</th>
					<th style="border: 1px solid black;" class="textcenter" width="250px">DESCRIPCIÓN</th>
					<th style="border: 1px solid black;" class="textcenter" width="80px">P.UNIT</th>
					<th style="border: 1px solid black;" class="textcenter" width="20px">TOTAL.</th>
				</tr>
			</thead>
			<tbody id="detalle_productos" style="border: 1px solid black;">

				<?php

				if ($result_detalle > 0) {

					while ($row = mysqli_fetch_assoc($query_productos)) {

				?>
						<tr>
							<td style="border-right: 1px solid black;" class="textcenter" width="5px"><?php echo $row['codigo']; ?></td>
							<td style="border-right: 1px solid black;" class="textcenter" width="5px"><?php echo round($row['cantidad'], 2); ?></td>
							<td style="border-right: 1px solid black;" class="textcenter" width="20px"><?php echo $row['unidadmedida']; ?></td>
							<td style="border-right: 1px solid black; padding-left: 5px;" width="250px"><?php echo $row['nombre_producto']; ?></td>
							<td style="border-right: 1px solid black;" class="textcenter" width="80px"><?php echo number_format($row['precio_compra'], 2, ",", ".")?></td>
							<td style="border-right: 1px solid black;" class="textcenter" width="20px"><?php echo number_format($row['subtotal'], 2, ",", ".")?></td>
						</tr>
				<?php
						$precio_total = $row['subtotal'];
						$subtotal = round($subtotal + $precio_total, 2);
					}
				}

				$xd = round(((($factura['total_compra'])) * (($iva) / ($iva + 100))), 2);

				$impuesto 	= round($subtotal * ($iva / 100), 2);
				$tl_sniva 	= round((($factura['total_compra'])) - $xd, 2);
				// $total 		= round($tl_sniva + $impuesto,2);
				// 
				?>
			</tbody>
			<?php

			//Convertimos el total en letras
			require_once "../Letras.php";
			$V = new EnLetras();
			$con_letra = strtoupper($V->ValorEnLetras($factura['total_compra'], "CON"));

			if ($factura['tipo_comprobante'] == 'Boleta') {
				$iddoc = '01';
				$iddocCliente = '6';
			} else if ($factura['tipo_comprobante'] == 'Factura') {
				$iddoc = "03";
				if (strlen($factura['num_documento']) == 8) {
					$iddocCliente = "1";
				} else {
					$iddocCliente = "4";
				}
			} else {
				$iddoc = '07';
				$iddocCliente = '6';
			}

			?>
			<!-- <tfoot id="detalle_totales"> -->
			<tr>
				<!-- <td style="border: 1px solid black;" class="textcenter"><strong></td> -->
				<td style="border: 1px solid black; padding-left: 5px;" colspan="4"><strong> SON : </strong> <?php echo $con_letra ?></td>
				<td style="border: 1px solid black;" class="textright"><span><strong> OP. GRAVADA S/</strong></span></td>
				<td style="border: 1px solid black;" class="textcenter"><span><?php echo number_format($tl_sniva, 2, ",", "."); ?></span></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td style="border: 1px solid black;" class="textright"><span><strong> IGV (<?php echo $iva; ?> %) S/</strong></span></td>
				<td style="border: 1px solid black;" class="textcenter"><span><?php echo $igv = number_format(((($factura['total_compra'])) * (($iva) / ($iva + 100))), 2, ',', '.'); ?></span></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td style="border: 1px solid black;" class="textright"><span><strong> TOTAL A PAGAR S/</strong></span></td>
				<td style="border: 1px solid black;" class="textcenter"><span><?php echo number_format($factura['total_compra'], 2, ",", ".")?></span></td>
			</tr>
			</tfoot>
		</table>

		<br>

		<!-- <h4 style="padding-left: 2px;"><strong>Nota : </strong></h4><?php echo $factura['nota']; ?> -->

		<table id="factura_detalle" style="width: 100%;">
			<thead>
				<tr>
					<th style="border: 1px solid black;">ELABORADO POR:</th>
					<th style="border: 1px solid black;">CONFORMIDAD:</th>
				</tr>
			</thead>
			<tbody id="detalle_productos" style="border: 1px solid black;">
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px; color:#FFFFFF;">h</td>
					<td style="border-right: 1px solid black; padding-left: 5px; color:#FFFFFF;">h</td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px; color:#FFFFFF;">h</td> 
					<td style="border-right: 1px solid black; padding-left: 5px; color:#FFFFFF;">h</td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px; text-align: center;">__________________________</td>
					<td style="border-right: 1px solid black; padding-left: 5px; text-align: center;">__________________________</td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px; text-align: center;"><label><strong> FIRMA ENCARGADO</strong></label></td>
					<td style="border-right: 1px solid black; padding-left: 5px; text-align: center;"><label><label><strong> FIRMA ALMACÉN</strong></td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong></strong></label></td>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label style="color: white;"><?php echo $factura['lugar_entrega']; ?></label></td>
				</tr>
			</tbody>
			<!-- <tfoot id="detalle_totales"> -->
			</tfoot>
		</table>

		<br>

		<table id="factura_detalle" style="width: 100%;">
			<tbody id="detalle_productos" style="border: 1px solid black;">
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px; padding-top: 10px;"><label><strong>NOTA IMPORTANTE : </strong></label></td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label>1. Enviar factura electrónica al correo: frank.fernandez@imprimaya.com.</td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label>2. Esta Orden es nula sin las firmas y sellos reglamentarios o autorizados.</td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px;">3. Nos reservamos el derecho de devolver la mercaderia que no esté de acuerdo a las especificaciones técnicas o en malas condiciones.</td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px; padding-bottom: 10px;"></td>
				</tr>
			</tbody>
			<!-- <tfoot id="detalle_totales"> -->
			</tfoot>
		</table>

	</div>

</body>

</html>