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
					<div class="round" style="text-align: center; border-radius: 15px;" >
						<br>
						<p><strong>
								<h3>R. U. C. <?php echo $configuracion['documento']; ?>
							</strong></h3>
						</p>
						<p class="h2">
							COTIZACION
						</p>
						<p><?php echo $factura['serie_comprobante'] . ' - ' . $factura['num_comprobante']; ?></p>
					</div>
				</td>
			</tr>
		</table>

		<label>
	      <strong>Fecha Emisión: </strong><?php echo $factura['fecha']; ?>&nbsp;&nbsp;
	      <strong>Hora: </strong><?php echo $factura['hora']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	      <strong>Almacén: </strong><?php echo $factura['almacen']; ?>
	    </label>

		<table id="factura_detalle" style="width: 100%;">
			<thead>
				<tr>
					<th style="border: 1px solid black; width: 370px; overflow: auto;">DATOS DEL CLIENTE</th>
					<th style="border: 1px solid black;">CONDICIONES GENERALES</th>
				</tr>
			</thead>
			<tbody id="detalle_productos" style="border: 1px solid black;">
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong>Señor(es) : </strong></label><?php echo $factura['cliente']; ?></td>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong>Forma de Pago : </strong></label><?php echo $factura['formapago']; ?></td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong><?php echo $factura['tipo_documento']; ?> : </strong></label><?php echo $factura['num_documento']; ?></td>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong>Tiempo de Producción : </strong></label><?php echo $factura['tiempo_pro']; ?></td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong>Dirección : </strong></label><?php echo $factura['direccion']; ?></td>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong>Validez de la Oferta : </strong></label><?php echo $factura['nota']; ?></td>
				</tr>
				<tr>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong>Observaciones : </strong></label><?php echo $factura['observacion']; ?></td>
					
				</tr>
				<!-- <tr>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong> : </strong></label><?php echo $factura['direccion']; ?></td>
					<td style="border-right: 1px solid black; padding-left: 5px;"><label><strong>Validez de la Oferta : </strong></label><?php echo $factura['nota']; ?></td>
				</tr> -->
			</tbody>
			<tr>
				<td style="border: 1px solid black; padding-left: 2px;" colspan="2"><strong>Nota : </strong><?php echo $factura['titulo']; ?></td>
			</tr>
		</table>

		<table id="factura_detalle" style="width: 100%;">
			<thead>
				<tr>
					<th style="border: 1px solid black;" width="20px">CÓDIGO.</th>
					<th style="border: 1px solid black;" class="textcenter" width="20px">CANT.</th>
					<th style="border: 1px solid black;" class="textcenter" width="20px">UM.</th>
					<th style="border: 1px solid black;" class="textcenter" width="250px">DESCRIPCIÓN</th>
					<th style="border: 1px solid black;" class="textcenter" width="20px">P.UNIT</th>
					<th style="border: 1px solid black;" class="textcenter" width="10px">DCTO.</th>
					<th style="border: 1px solid black;" class="textcenter" width="20px">TOTAL.</th>
				</tr>
			</thead>
			<tbody id="detalle_productos" style="border: 1px solid black;">

				<?php

				$descuento = 0;

				$exonerado = 0;

				if ($result_detalle > 0) {

					while ($row = mysqli_fetch_assoc($query_productos)) {

						if ($row['proigv'] == "No Gravada") {
							$exonerado = $exonerado + ($row['cantidad'] * $row['precio_venta']);
						} else {
							$exonerado = 0;
						}

				?>
						<tr>
							<td style="border-right: 1px solid black;" class="textcenter" width="20px"><?php echo $row['codigo']; ?></td>
							<td style="border-right: 1px solid black;" class="textcenter" width="20px"><?php echo round($row['cantidad'], 2); ?></td>
							<td style="border-right: 1px solid black;" class="textcenter" width="20px"><?php echo $row['unidadmedida']; ?></td>
							<td style="border-right: 1px solid black; padding-left: 5px;" width="250px"><?php echo $row['producto']; ?></td>
							<td style="border-right: 1px solid black;" class="textcenter" width="20px"><?php echo number_format($row['precio_venta'], 2, ",", "."); ?></td>
							<td style="border-right: 1px solid black;" class="textcenter" width="10px"><?php echo number_format($row['descuento'], 2, ",", "."); ?></td>
							<td style="border-right: 1px solid black;" class="textcenter" width="20px"><?php echo number_format($row['subtotal'], 2, ",", "."); ?></td>
						</tr>
				<?php
						$precio_total = $row['subtotal'];
						$subtotal = round($subtotal + $precio_total, 2);
						$descuento = $row['descuento'] + $row['descuento'];
					}
				}

				$xd = round(((($factura['total_venta'] + $descuento) - $exonerado) * (($iva) / ($iva + 100))), 2);

				// $impuesto 	= round($subtotal * ($iva / 100), 2);
				$tl_sniva 	= round((($factura['total_venta'] + $descuento) - $exonerado) - $xd, 2);
				// $total 		= round($tl_sniva + $impuesto,2);
				// 
				?>
			</tbody>
			<?php

			//Convertimos el total en letras
			require_once "../Letras.php";
			$V = new EnLetras();
			$con_letra = strtoupper($V->ValorEnLetras($factura['total_venta'], "CON"));

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

			$texto = $configuracion['documento'] . "|" . $iddoc . "|" . $factura['serie_comprobante'] . "|" . $factura['num_comprobante'] . "|" . $iva . "|" . $factura['total_venta'] . "|" . $factura['fecha'] . "|" . $iddocCliente . "|" . $factura['num_documento'] . "|";

			if (file_exists("../../phpqrcode/qrlib.php")) {
				require "../../phpqrcode/qrlib.php";

				$ruta_qr = '../qr/' . 'img2.png';

				$tamaño = 10;

				$level = "Q";

				$framSize = 3;

				QRcode::png($texto, $ruta_qr, $level, $tamaño, $framSize);

				if (file_exists($ruta_qr)) {
					$error = 0;
					$mensaje = "Archivo QR, generado";
				}
			} else {
				$error = 1;
				$mensaje = "No Existe la libreria";
			}

			?>
			<tfoot id="detalle_totales">
				<tr>
					<td style="border: 1px solid black; padding-left: 5px;" colspan="4"><strong> SON : </strong> <?php echo $con_letra ?></td>
					<td style="border: 1px solid black;" colspan="2" class="textright"><span>OP. GRAVADA S/</span></td>
					<td style="border: 1px solid black;" class="textcenter"><span>
						<?php
							if($factura['igv'] == 'Si'){
							echo number_format($tl_sniva, 2, ",", "0");
							}else{
								echo '0,00';
							} 
						?>
					</span></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style="border: 1px solid black;" colspan="2" class="textright"><span>OP. EXONERADO S/</span></td>
					<td style="border: 1px solid black;" class="textcenter"><span>
						<?php echo number_format($exonerado - $descuento, 2); ?>
					</span></td>
				</tr>
				<tr>
				<td colspan="3" style="margin-top: 300px;">PRECIOS :<strong>
						<?php 
						
						if($factura['igv'] == 'Si'){

							echo 'INCLUYEN IGV';

						}else{

							echo 'NO INCLUYEN IGV';

						}
						
						?></strong></td>
					<td></td>
					<td style="border: 1px solid black;" colspan="2" class="textright"><span>DESCUENTO S/</span></td>
					<td style="border: 1px solid black;" class="textcenter"><span><?php echo number_format($descuento, 2, ",", "0"); ?></span></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style="border: 1px solid black;" colspan="2" class="textright"><span>IGV (<?php echo $iva; ?> %) S/</span></td>
					<td style="border: 1px solid black;" class="textcenter"><span>
						<?php
							if($factura['igv'] == 'Si'){

								echo $igv = number_format(((($factura['total_venta'] + $descuento) - $exonerado) * (($iva) / ($iva + 100))), 2, ",", "0");
						
							}else{
								echo '0,00';
							}
						?></span></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style="border: 1px solid black;" colspan="2" class="textright"><span>TOTAL A PAGAR S/</span></td>
					<td style="border: 1px solid black;" class="textcenter"><span><?php echo number_format($factura['total_venta'], 2); ?></span></td>
				</tr>
			</tfoot>
		</table>

	</div>

</body>

</html>