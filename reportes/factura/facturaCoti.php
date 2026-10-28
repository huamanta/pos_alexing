<?php

$frecuenciaTexto = '';
$mesesPorCuota = 1;
$numCuotas = (int) $factura['cuotas'];

$logo = !empty($configuracion['logo']) ? $configuracion['logo'] : 'default.png';
$rutaLogo = realpath(__DIR__ . '/../../files/logos/' . $logo);

switch ($factura['frecuencia']) {

    case 1:
        $frecuenciaTexto = 'Diario';
        $mesesPorCuota = 1 / 30;
        break;

    case 2:
        $frecuenciaTexto = 'Semanal';
        $mesesPorCuota = $calculo30Dias ? 1 / 5 : 1 / 4;
        break;

    case 3:
        $frecuenciaTexto = 'Quincenal';
        $mesesPorCuota = 1 / 2;
        break;

    case 4:
        $frecuenciaTexto = 'Mensual';
        $mesesPorCuota = 1;
        break;

    case 5:
        $frecuenciaTexto = 'Bimestral';
        $mesesPorCuota = 2;
        break;

    case 6:
        $frecuenciaTexto = 'Trimestral';
        $mesesPorCuota = 3;
        break;

    case 7:
        $frecuenciaTexto = 'Semestral';
        $mesesPorCuota = 6;
        break;

    case 8:
        $frecuenciaTexto = 'Anual';
        $mesesPorCuota = 12;
        break;

    default:
        $frecuenciaTexto = '-';
        $mesesPorCuota = 1;
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

	<link rel="stylesheet" href="../../vistas/css/variables.css">
	<link rel="stylesheet" href="./style.css">
	<link rel="stylesheet" href="./css/cotizacion.css">
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

			if ($numCuotas <= 0) {
				$numCuotas = 1;
			}

			$capitalCuota = $saldoFinanciar / $numCuotas;
			$interesCuota = $totalInteres;
			$totalCredito = $saldoFinanciar + ($interesCuota * $numCuotas);
			$montoCuota = $capitalCuota + $interesCuota;

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

						$capitalCuotaTotal = 0;
						$interesCuotaTotal = 0;
						$montoCuotaTotal = 0;

						$fechaBase = strtotime($factura['fecha_original']);

						for ($i = 1; $i <= $numCuotas; $i++) {

							switch ($factura['frecuencia']) {

								case 1:
									$fechaPago = date(
										'd/m/Y',
										strtotime('+' . $i . ' day', $fechaBase)
									);
									break;

								case 2:
									$dias = $calculo30Dias ? 5 : 7;

									$fechaPago = date(
										'd/m/Y',
										strtotime('+' . ($i * $dias) . ' days', $fechaBase)
									);
									break;

								case 3:
									$fechaPago = date(
										'd/m/Y',
										strtotime('+' . ($i * 15) . ' days', $fechaBase)
									);
									break;

								case 4:
									if ($calculo30Dias) {
										$fechaPago = date(
											'd/m/Y',
											strtotime('+' . ($i * 30) . ' days', $fechaBase)
										);
									} else {
										$fechaPago = date(
											'd/m/Y',
											strtotime('+' . $i . ' month', $fechaBase)
										);
									}
									break;

								case 5:
									if ($calculo30Dias) {
										$fechaPago = date(
											'd/m/Y',
											strtotime('+' . ($i * 60) . ' days', $fechaBase)
										);
									} else {
										$fechaPago = date(
											'd/m/Y',
											strtotime('+' . ($i * 2) . ' months', $fechaBase)
										);
									}
									break;

								case 6:
									if ($calculo30Dias) {
										$fechaPago = date(
											'd/m/Y',
											strtotime('+' . ($i * 90) . ' days', $fechaBase)
										);
									} else {
										$fechaPago = date(
											'd/m/Y',
											strtotime('+' . ($i * 3) . ' months', $fechaBase)
										);
									}
									break;

								case 7:
									if ($calculo30Dias) {
										$fechaPago = date(
											'd/m/Y',
											strtotime('+' . ($i * 180) . ' days', $fechaBase)
										);
									} else {
										$fechaPago = date(
											'd/m/Y',
											strtotime('+' . ($i * 6) . ' months', $fechaBase)
										);
									}
									break;

								case 8:
									if ($calculo30Dias) {
										$fechaPago = date(
											'd/m/Y',
											strtotime('+' . ($i * 360) . ' days', $fechaBase)
										);
									} else {
										$fechaPago = date(
											'd/m/Y',
											strtotime('+' . $i . ' year', $fechaBase)
										);
									}
									break;

								default:
									if ($calculo30Dias) {
										$fechaPago = date(
											'd/m/Y',
											strtotime('+' . ($i * 30) . ' days', $fechaBase)
										);
									} else {
										$fechaPago = date(
											'd/m/Y',
											strtotime('+' . $i . ' month', $fechaBase)
										);
									}
									break;
							}

							$capitalCuotaTotal += $capitalCuota;
							$interesCuotaTotal += $interesCuota;
							$montoCuotaTotal += $montoCuota;

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

							<td colspan="2">
								TOTALES
							</td>

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