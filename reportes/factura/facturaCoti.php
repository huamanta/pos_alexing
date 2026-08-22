<?php

$frecuenciaTexto = '';
$diasFrecuencia = 30;
$numCuotas = 1;
$logo = !empty($configuracion['logo']) ? $configuracion['logo'] : 'default.png';
$rutaLogo = realpath(__DIR__ . '/../../files/logos/' . $logo);

switch ($factura['frecuencia']) {

	case 1:
		$frecuenciaTexto = 'Diario';
		$diasFrecuencia = 1;
		$numCuotas = $factura['meses'] * 30;
		break;

	case 2:
		$frecuenciaTexto = 'Semanal';
		$diasFrecuencia = 7;
		$numCuotas = $factura['meses'] * 4;
		break;

	case 3:
		$frecuenciaTexto = 'Quincenal';
		$diasFrecuencia = 15;
		$numCuotas = $factura['meses'] * 2;
		break;

	case 4:
		$frecuenciaTexto = 'Mensual';
		$diasFrecuencia = 30;
		$numCuotas = $factura['meses'];
		break;

	case 5:
		$frecuenciaTexto = 'Bimestral';
		$diasFrecuencia = 60;
		$numCuotas = ceil($factura['meses'] / 2);
		break;

	case 6:
		$frecuenciaTexto = 'Trimestral';
		$diasFrecuencia = 90;
		$numCuotas = ceil($factura['meses'] / 3);
		break;

	case 7:
		$frecuenciaTexto = 'Semestral';
		$diasFrecuencia = 180;
		$numCuotas = ceil($factura['meses'] / 6);
		break;

	case 8:
		$frecuenciaTexto = 'Anual';
		$diasFrecuencia = 365;
		$numCuotas = ceil($factura['meses'] / 12);
		break;

	default:
		$frecuenciaTexto = '-';
		$diasFrecuencia = 30;
		$numCuotas = 1;
		break;
}

$subtotal = 0;
$iva = 0;
$impuesto = 0;
$tl_sniva = 0;
$total = 0;
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
			color: #222;
		}

		table,
		th,
		td {
			font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
			font-size: 11px;
			border-collapse: collapse;
		}

		strong,
		b,
		h1,
		h2,
		h3,
		h4,
		h5,
		h6 {
			font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
			font-weight: bold;
		}

		.textcenter {
			text-align: center;
		}

		.textright {
			text-align: right;
		}

		.round {
			border: 2px solid #0d47a1;
			padding: 10px;
		}

		.credito-box {
			margin-top: 10px;
			border: 1px solid #ffffff;
			border-radius: 8px;
			overflow: hidden;
		}

		.credito-header {
			background: #0d47a1;
			color: #fff;
			padding: 8px;
			font-weight: bold;
			font-size: 12px;
		}

		.credito-body {
			background: #ffffff;
			padding: 10px;
		}

		.credito-table {
			width: 100%;
		}

		.credito-table td {
			border: 1px solid #dcdcdc;
			padding: 6px;
		}

		.table-cuotas {
			width: 100%;
			margin-top: 10px;
		}

		.table-cuotas th {
			background: #0d47a1;
			color: #fff;
			border: 1px solid #dcdcdc;
			padding: 7px;
			font-size: 11px;
		}

		.table-cuotas td {
			border: 1px solid #dcdcdc;
			padding: 6px;
			text-align: center;
			font-size: 11px;
		}
	</style>

</head>

<body>

	<?php echo $anulada; ?>

	<div id="page_pdf">

		<table id="factura_head" width="100%" cellspacing="0" cellpadding="2" border="0">

			<tr>

				<td width="25%" align="left" valign="top">

					<div>
						<img src="file://<?php echo $rutaLogo; ?>" width="120" />
					</div>

				</td>

				<td width="45%" align="center" valign="top">

					<div>

						<h1 class="h1">
							<?php echo $configuracion['razon_social']; ?>
						</h1>

						<p>
							SUCURSAL: <?php echo $configuracion['nombre']; ?>
						</p>

						<p>
							RUC: <?php echo $configuracion['ruc']; ?>
						</p>

						<p>
							<?php echo $configuracion['direccion']; ?>
						</p>

						<p>
							Teléfono: <?php echo $configuracion['telefono']; ?>
						</p>

						<p>
							Email: <?php echo $configuracion['email']; ?>
						</p>

					</div>

				</td>

				<td class="info_factura" width="30%" align="right" valign="top">

					<div class="round" style="text-align: center; border-radius: 15px;">

						<br>

						<p>
							<strong>
								<h3>
									R. U. C. <?php echo $configuracion['ruc']; ?>
								</h3>
							</strong>
						</p>

						<p class="h2">
							COTIZACION
						</p>

						<p>
							<?php echo $factura['serie_comprobante'] . ' - ' . $factura['num_comprobante']; ?>
						</p>

					</div>

				</td>

			</tr>

		</table>

		<br>

		<table id="factura_detalle" style="width: 100%;">

			<thead>

				<tr>

					<th style="border: 1px solid black; width: 370px;">
						DATOS DEL CLIENTE
					</th>

					<th style="border: 1px solid black;">
						CONDICIONES GENERALES
					</th>

				</tr>

			</thead>

			<tbody style="border: 1px solid black;">

				<tr>

					<td style="border-right: 1px solid black; padding-left: 5px;">
						<strong>Cliente:</strong>
						<?php echo $factura['cliente']; ?>
					</td>

					<td style="border-right: 1px solid black; padding-left: 5px;">
						<strong>Forma Pago:</strong>
						<?php echo ($factura['formapago'] == 'Si') ? 'CRÉDITO' : 'CONTADO'; ?>
					</td>

				</tr>

				<tr>

					<td style="border-right: 1px solid black; padding-left: 5px;">
						<strong><?php echo $factura['tipo_documento']; ?>:</strong>
						<?php echo $factura['num_documento']; ?>
					</td>

					<td style="border-right: 1px solid black; padding-left: 5px;">
						<strong>Fecha:</strong>
						<?php echo $factura['fecha']; ?>
					</td>

				</tr>

				<tr>

					<td colspan="2" style="padding-left:5px;">
						<strong>Observación:</strong>
						<?php echo $factura['observacion']; ?>
					</td>

				</tr>

			</tbody>

		</table>

		<br>

		<table id="factura_detalle" style="width: 100%;">

			<thead>

				<tr>

					<th style="border: 1px solid black;" width="20px">CÓDIGO</th>
					<th style="border: 1px solid black;" width="20px">CANT.</th>
					<th style="border: 1px solid black;" width="20px">UM</th>
					<th style="border: 1px solid black;" width="250px">DESCRIPCIÓN</th>
					<th style="border: 1px solid black;" width="20px">P.UNIT</th>
					<th style="border: 1px solid black;" width="10px">DCTO</th>
					<th style="border: 1px solid black;" width="20px">TOTAL</th>

				</tr>

			</thead>

			<tbody>

				<?php
				$descuento = 0;
				$exonerado = 0;
				foreach ($detalles as $row) {
					?>

					<tr>

						<td style="border:1px solid #000;" class="textcenter">
							<?php echo $row['codigo']; ?>
						</td>

						<td style="border:1px solid #000;" class="textcenter">
							<?php echo round($row['cantidad'], 2); ?>
						</td>

						<td style="border:1px solid #000;" class="textcenter">
							<?php echo $row['unidadmedida']; ?>
						</td>

						<td style="border:1px solid #000; padding-left:5px;">
							<?php echo $row['producto']; ?>
						</td>

						<td style="border:1px solid #000;" class="textcenter">
							<?php echo $helpers->get_currency_symbol($row['precio_venta']); ?>
						</td>

						<td style="border:1px solid #000;" class="textcenter">
							<?php echo $helpers->get_currency_symbol($row['descuento']); ?>
						</td>

						<td style="border:1px solid #000;" class="textcenter">
							<?php echo $helpers->get_currency_symbol($row['subtotal']); ?>
						</td>

					</tr>

					<?php
					$subtotal += $row['subtotal'];
					$descuento += $row['descuento'];
				}
				?>

			</tbody>

			<tfoot>

				<tr>

					<td colspan="5"></td>

					<td style="border:1px solid #000;">
						<strong>TOTAL</strong>
					</td>

					<td style="border:1px solid #000;" class="textcenter">
						<strong><?php echo $helpers->get_currency_symbol($factura['total_venta']); ?></strong>
					</td>

				</tr>

			</tfoot>

		</table>

		<?php if (trim($factura['formapago']) == 'Si') { ?>

			<?php

			$totalVenta = floatval($factura['total_venta']);
			$inicial = floatval($factura['inicial']);
			$interes = floatval($factura['interes']);

			$saldoFinanciar = $totalVenta - $inicial;

			$totalInteres = ($saldoFinanciar * $interes) / 100;

			$totalCredito = $saldoFinanciar + $totalInteres;

			if ($numCuotas <= 0) {
				$numCuotas = 1;
			}

			$montoCuota = $totalCredito / $numCuotas;

			?>

			<br>

			<div class="credito-box">

				<div class="credito-header">
					DETALLE DEL CRÉDITO
				</div>

				<div class="credito-body">

					<table class="credito-table">

						<tr>

							<td><strong>Total Venta</strong></td>
							<td><?php echo $helpers->get_currency_symbol($totalVenta); ?></td>

							<td><strong>Inicial</strong></td>
							<td><?php echo $helpers->get_currency_symbol($inicial); ?></td>

						</tr>

						<tr>

							<td><strong>Saldo</strong></td>
							<td><?php echo $helpers->get_currency_symbol($saldoFinanciar); ?></td>

							<td><strong>Interés</strong></td>
							<td><?php echo number_format($interes, 2); ?> %</td>

						</tr>

						<tr>

							<td><strong>Frecuencia</strong></td>
							<td><?php echo $frecuenciaTexto; ?></td>

							<td><strong>N° Cuotas</strong></td>
							<td><?php echo $numCuotas; ?></td>

						</tr>

					</table>

					<br>

					<table class="table-cuotas">

						<thead>

							<tr>

								<th>#</th>
								<th>Fecha Pago</th>
								<th>Capital</th>
								<th>Interés</th>
								<th>Total Cuota</th>

							</tr>

						</thead>

						<tbody>

							<?php

							$capitalCuota = $saldoFinanciar / $numCuotas;
							$interesCuota = $totalInteres / $numCuotas;
							$capitalCuotaTotal = 0;
							$interesCuotaTotal = 0;
							$montoCuotaTotal = 0;

							for ($i = 1; $i <= $numCuotas; $i++) {

								$fechaPago = date(
									'd/m/Y',
									strtotime(
										'+' . ($diasFrecuencia * $i) . ' days',
										strtotime($factura['fecha_original'])
									)
								);
								$capitalCuotaTotal = $capitalCuotaTotal + $capitalCuota;
								$interesCuotaTotal = $interesCuotaTotal + $interesCuota;
								$montoCuotaTotal = $montoCuotaTotal + $montoCuota;

								?>

								<tr>

									<td><?php echo $i; ?></td>

									<td><?php echo $fechaPago; ?></td>

									<td>
										<?php echo $helpers->get_currency_symbol($capitalCuota); ?>
									</td>

									<td>
										<?php echo $helpers->get_currency_symbol($interesCuota); ?>
									</td>

									<td>
										<strong>
											<?php echo $helpers->get_currency_symbol($montoCuota); ?>
										</strong>
									</td>

								</tr>

							<?php } ?>
							<tr>
								<td colspan="2">TOTALES</td>
								<td>
									<?php echo $helpers->get_currency_symbol($capitalCuotaTotal); ?>
								</td>
								<td>
									<?php echo $helpers->get_currency_symbol($interesCuotaTotal); ?>
								</td>
								<td>
									<?php echo $helpers->get_currency_symbol($montoCuotaTotal); ?>
								</td>
							</tr>

						</tbody>

					</table>

				</div>

			</div>

		<?php } ?>

	</div>

</body>

</html>