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
		@font-face {
			font-family: 'Arial Narrow';
			src: url('pdf/vendor/fonts/arial-narrow.ttf') format('truetype');
			font-weight: normal;
			font-style: normal;
		}

		* {
			box-sizing: border-box;
		}

		body {
			font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
			font-size: 11px;
			color: #263238;
			margin: 0;
			padding: 0;
		}

		table,
		th,
		td {
			font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
			font-size: 10.5px;
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

		.textleft {
			text-align: left;
		}

		/* ENCABEZADO */

		.header-table {
			width: 100%;
			border-collapse: collapse;
		}

		.logo-cell {
			width: 22%;
			vertical-align: top;
			padding: 5px 10px 5px 20px;
		}

		.logo-cell img {
			max-width: 125px;
			max-height: 100px;
		}

		.company-cell {
			width: 48%;
			vertical-align: top;
			padding: 2px 10px;
		}

		.company-name {
			font-size: 17px;
			font-weight: bold;
			color: #0d47a1;
			margin: 0 0 5px 0;
		}

		.company-info {
			font-size: 10px;
			line-height: 1.45;
			color: #455a64;
			margin: 0;
		}

		.document-cell {
			width: 30%;
			vertical-align: top;
		}

		.document-box {
			border: 1.5px solid #0d47a1;
			border-radius: 10px;
			overflow: hidden;
			text-align: center;
		}

		.document-ruc {
			background: #0d47a1;
			color: #fff;
			font-size: 12px;
			font-weight: bold;
			padding: 7px;
		}

		.document-title {
			color: #0d47a1;
			font-size: 16px;
			font-weight: bold;
			padding: 7px 5px 2px;
		}

		.document-number {
			font-size: 13px;
			font-weight: bold;
			color: #263238;
			padding: 3px 5px 9px;
		}

		/* SEPARADOR */

		.header-line {
			border-bottom: 2px solid #0d47a1;
			margin-top: 7px;
			margin-bottom: 10px;
		}

		/* SECCIONES */

		.section-title {
			background: #0d47a1;
			color: #fff;
			font-size: 10.5px;
			font-weight: bold;
			padding: 6px 8px;
		}

		.info-table {
			width: 100%;
			border: 1px solid #d7dee3;
			border-radius: 5px;
		}

		.info-table td {
			border: 1px solid #d7dee3;
			padding: 6px 8px;
			vertical-align: middle;
		}

		.info-label {
			font-weight: bold;
			color: #37474f;
		}

		.info-value {
			color: #263238;
		}

		/* DETALLE */

		.detail-table {
			width: 100%;
			margin-top: 10px;
			border: 1px solid #cfd8dc;
		}

		.detail-table thead th {
			background: #0d47a1;
			color: #fff;
			border: 1px solid #0d47a1;
			padding: 7px 5px;
			font-size: 10px;
			font-weight: bold;
			text-align: center;
		}

		.detail-table tbody td {
			border: 1px solid #dce3e7;
			padding: 6px 5px;
			vertical-align: middle;
		}

		.detail-table tbody tr:nth-child(even) {
			background: #f7f9fb;
		}

		.detail-table .description {
			text-align: left;
			padding-left: 7px;
		}

		.detail-table .number {
			text-align: right;
		}

		.detail-table .center {
			text-align: center;
		}

		.detail-total td {
			background: #eef3f8;
			border: 1px solid #cfd8dc;
			padding: 7px;
			font-weight: bold;
		}

		.detail-total-label {
			text-align: right;
			color: #263238;
		}

		.detail-total-value {
			text-align: right;
			color: #0d47a1;
			font-size: 12px;
		}

		/* CRÉDITO */

		.credito-box {
			margin-top: 12px;
			border: 1px solid #cfd8dc;
			border-radius: 8px;
			overflow: hidden;
		}

		.credito-header {
			background: #0d47a1;
			color: #fff;
			padding: 7px 9px;
			font-size: 11px;
			font-weight: bold;
		}

		.credito-body {
			background: #fff;
		}

		.credito-table {
			width: 100%;
			border: 1px solid #dce3e7;
		}

		.credito-table td {
			border: 1px solid #dce3e7;
			padding: 6px 8px;
		}

		.credito-table td:nth-child(odd) {
			background: #f3f6f9;
			font-weight: bold;
			width: 17%;
			color: #37474f;
		}

		.credito-table td:nth-child(even) {
			width: 33%;
		}

		/* CUOTAS */

		.table-cuotas {
			width: 100%;
			border: 1px solid #cfd8dc;
		}

		.table-cuotas thead th {
			background: #455a64;
			color: #fff;
			border: 1px solid #455a64;
			padding: 6px;
			font-size: 10px;
			text-align: center;
		}

		.table-cuotas tbody td {
			border: 1px solid #dce3e7;
			padding: 5px;
			text-align: center;
		}

		.table-cuotas tbody tr:nth-child(even) {
			background: #f7f9fb;
		}

		.table-cuotas .total-row td {
			background: #e9eef3;
			font-weight: bold;
			color: #0d47a1;
			padding: 6px;
		}

		/* UTILIDADES */

		.muted {
			color: #607d8b;
		}

		.amount {
			text-align: right;
			white-space: nowrap;
		}

		.spacer {
			height: 8px;
		}

		thead:empty {
			display: none;
		}
	</style>

</head>

<body>

	<?php echo $anulada; ?>

	<div id="page_pdf">

		<table class="header-table">
			<tr>
				<td class="logo-cell">
					<?php if ($rutaLogo && file_exists($rutaLogo)) { ?>
						<img src="file://<?php echo $rutaLogo; ?>" alt="Logo">
					<?php } ?>
				</td>

				<td class="company-cell">
					<div class="company-name">
						<?php echo $configuracion['razon_social']; ?>
					</div>

					<div class="company-info">
						<strong>Sucursal:</strong> <?php echo $configuracion['nombre']; ?><br>
						<strong>RUC:</strong> <?php echo $configuracion['ruc']; ?><br>
						<?php echo $configuracion['direccion']; ?><br>
						<strong>Teléfono:</strong> <?php echo $configuracion['telefono']; ?><br>
						<strong>Email:</strong> <?php echo $configuracion['email']; ?>
					</div>
				</td>

				<td class="document-cell">
					<div class="document-box">
						<div class="document-ruc">
							R.U.C. <?php echo $configuracion['ruc']; ?>
						</div>

						<div class="document-title">
							COTIZACIÓN
						</div>

						<div class="document-number">
							<?php echo $factura['serie_comprobante'] . ' - ' . $factura['num_comprobante']; ?>
						</div>
					</div>
				</td>
			</tr>
		</table>

		<div class="header-line"></div>

		<br>

		<table class="info-table">
			<tr>
				<td colspan="2" class="section-title">
					DATOS GENERALES
				</td>
			</tr>

			<tr>
				<td width="50%">
					<span class="info-label">Cliente:</span>
					<span class="info-value"><?php echo $factura['cliente']; ?></span>
				</td>

				<td width="50%">
					<span class="info-label">Forma de pago:</span>
					<span class="info-value">
						<?php echo ($factura['formapago'] == 'Si') ? 'CRÉDITO' : 'CONTADO'; ?>
					</span>
				</td>
			</tr>

			<tr>
				<td>
					<span class="info-label">
						<?php echo $factura['tipo_documento']; ?>:
					</span>
					<span class="info-value">
						<?php echo $factura['num_documento']; ?>
					</span>
				</td>

				<td>
					<span class="info-label">Fecha:</span>
					<span class="info-value">
						<?php echo $factura['fecha']; ?>
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<span class="info-label">Observación:</span>
					<span class="info-value">
						<?php echo !empty($factura['observacion']) ? $factura['observacion'] : '-'; ?>
					</span>
				</td>
			</tr>
		</table>

		<br>

		<table class="detail-table">
			<thead>
				<tr>
					<th width="10%">CÓDIGO</th>
					<th width="7%">CANT.</th>
					<th width="8%">U.M.</th>
					<th width="35%">DESCRIPCIÓN</th>
					<th width="13%">P. UNIT.</th>
					<th width="12%">DCTO.</th>
					<th width="15%">TOTAL</th>
				</tr>
			</thead>

			<tbody>
				<?php
				$descuento = 0;
				$exonerado = 0;

				foreach ($detalles as $row) {
					?>
					<tr>
						<td class="center">
							<?php echo $row['codigo']; ?>
						</td>

						<td class="center">
							<?php echo round($row['cantidad'], 2); ?>
						</td>

						<td class="center">
							<?php echo $row['unidadmedida']; ?>
						</td>

						<td class="description">
							<?php echo $row['producto']; ?>
						</td>

						<td class="amount">
							<?php echo $helpers->get_currency_symbol($row['precio_venta'], $currency); ?>
						</td>

						<td class="amount">
							<?php echo $helpers->get_currency_symbol($row['descuento'], $currency); ?>
						</td>

						<td class="amount">
							<strong>
								<?php echo $helpers->get_currency_symbol($row['subtotal'], $currency); ?>
							</strong>
						</td>
					</tr>
					<?php

					$subtotal += $row['subtotal'];
					$descuento += $row['descuento'];
				}
				?>
			</tbody>

			<tfoot>
				<tr class="detail-total">
					<td colspan="5"></td>

					<td class="detail-total-label">
						TOTAL
					</td>

					<td class="detail-total-value">
						<?php echo $helpers->get_currency_symbol($factura['total_venta'], $currency); ?>
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


			<div class="credito-header">
				DETALLE DEL CRÉDITO
			</div>

			<div class="credito-body">

				<table class="credito-table">
					<tr>
						<td>Total venta</td>
						<td>
							<?php echo $helpers->get_currency_symbol($totalVenta, $currency); ?>
						</td>

						<td>Inicial</td>
						<td>
							<?php echo $helpers->get_currency_symbol($inicial, $currency); ?>
						</td>
					</tr>

					<tr>
						<td>Saldo</td>
						<td>
							<?php echo $helpers->get_currency_symbol($saldoFinanciar, $currency); ?>
						</td>

						<td>Interés</td>
						<td>
							<?php echo number_format($interes, 2); ?> %
						</td>
					</tr>

					<tr>
						<td>Total financiado</td>
						<td>
							<strong>
								<?php echo $helpers->get_currency_symbol($totalCredito, $currency); ?>
							</strong>
						</td>

						<td>Frecuencia</td>
						<td>
							<?php echo $frecuenciaTexto; ?>
						</td>
					</tr>

					<tr>
						<td>N° cuotas</td>
						<td>
							<?php echo $numCuotas; ?>
						</td>

						<td>Cuota</td>
						<td>
							<strong>
								<?php echo $helpers->get_currency_symbol($montoCuota, $currency); ?>
							</strong>
						</td>
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
									<?php echo $helpers->get_currency_symbol($capitalCuota, $currency); ?>
								</td>

								<td>
									<?php echo $helpers->get_currency_symbol($interesCuota, $currency); ?>
								</td>

								<td>
									<strong>
										<?php echo $helpers->get_currency_symbol($montoCuota, $currency); ?>
									</strong>
								</td>

							</tr>

						<?php } ?>
						<tr class="total-row">
							<td colspan="2">TOTALES</td>

							<td>
								<?php echo $helpers->get_currency_symbol($capitalCuotaTotal, $currency); ?>
							</td>

							<td>
								<?php echo $helpers->get_currency_symbol($interesCuotaTotal, $currency); ?>
							</td>

							<td>
								<?php echo $helpers->get_currency_symbol($montoCuotaTotal, $currency); ?>
							</td>
						</tr>

					</tbody>

				</table>

			</div>


		<?php } ?>

	</div>

</body>

</html>