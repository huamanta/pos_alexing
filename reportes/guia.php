<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>GUÍA DE REMISIÓN <?= $guia['serie_comprobante'] ?>-<?= $guia['num_comprobante'] ?></title>
    <style>
        @page {
            margin: 8mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.45;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .b {
            font-weight: bold;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo-img {
            max-width: 130px;
            max-height: 60px;
        }

        .ruc-box {
            border: 1.2px solid #000;
            border-radius: 10px;
            padding: 7px 9px;
            text-align: center;
        }

        .ruc {
            font-size: 11px;
        }

        .doc-title {
            font-size: 11px;
            font-weight: bold;
            margin: 4px 0;
        }

        .doc-num {
            font-size: 12px;
            font-weight: bold;
        }

        .box {
            border: 1px solid #000;
            border-radius: 6px;
            padding: 10px;
            margin-top: 6px;
        }

        .box td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            min-width: 100px;
        }

        .box div,
        .info-box div {
            margin-bottom: 5px;
        }

        .box div:last-child,
        .info-box div:last-child {
            margin-bottom: 0;
        }

        .detalle th,
        .detalle td {
            border: 1px solid #000;
            padding: 6px 4px;
            font-size: 8.5px;
        }

        .detalle th {
            background: #f0f0f0;
        }

        .dotted {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .company-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 7px;
            text-transform: uppercase;
        }

        .info-box {
            border: 1px solid #000;
            border-radius: 6px;
            margin-top: 7px;
            overflow: hidden;
        }

        .info-box td {
            padding: 8px 9px;
            vertical-align: top;
        }

        .info-box .divider {
            border-left: 1px solid #000;
        }

        .section-title {
            background: #1f2937;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 5px 8px;
        }

        .data-table td {
            border-bottom: 1px solid #d1d5db;
            padding: 5px 7px;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .field-name {
            font-weight: bold;
            width: 115px;
            background: #f7f7f7;
        }

        .detalle {
            margin-top: 8px;
        }

        .detalle th {
            color: #fff;
            background: #374151;
            border-color: #374151;
            text-transform: uppercase;
        }

        .detalle td {
            border-color: #aeb4bb;
        }

        .detalle td {
            line-height: 1.35;
        }

        .total-bar {
            border: 1px solid #000;
            border-radius: 4px;
            margin-top: 8px;
            padding: 7px 9px;
            text-align: right;
            background: #f7f7f7;
        }

        .footer-box {
            border: 1px solid #000;
            border-radius: 6px;
            margin-top: 12px;
            padding: 8px;
            text-align: center;
            background: #fafafa;
        }
    </style>
</head>

<body>

    <?php
    // Logo convertido a ruta segura
    $logo = !empty($configuracion['logo']) ? $configuracion['logo'] : 'default.png';
    $rutaLogo = realpath(__DIR__ . '/../files/logos/' . $logo);
    if (!$rutaLogo || !is_readable($rutaLogo)) {
        $rutaLogo = realpath(__DIR__ . '/../files/logos/default.png');
    }
    $logoExtension = $rutaLogo ? strtolower(pathinfo($rutaLogo, PATHINFO_EXTENSION)) : '';
    $logoMime = match ($logoExtension) {
        'jpg', 'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        default => 'application/octet-stream',
    };
    $logoData = $rutaLogo ? 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($rutaLogo)) : '';
    ?>

    <table class="header-table">
        <tr>
            <td width="33%" class="text-center">
                <div class="logo-text">
                    <?php if ($logoData): ?>
                        <img src="<?= htmlspecialchars($logoData, ENT_QUOTES, 'UTF-8') ?>" height="60">
                    <?php endif; ?>
                </div>
            </td>
            <td width="34%">
                <div><span class="label">Razón Social:</span>
                    <?= strtoupper($configuracion['razon_social'] ?? 'DROGUERIA COBEFAR') ?>
                </div>
                <div><span class="label">Dom. Fiscal:</span> <?= $configuracion['direccion'] ?? '' ?></div>
                <div><span class="label">Teléfono:</span> <?= $configuracion['telefono'] ?? '' ?></div>
                <div><span class="label">Email:</span> <?= $configuracion['email'] ?? '' ?></div>
            </td>
            <td width="33%">
                <div class="ruc-box">
                    <div class="ruc">R.U.C. <?= $configuracion['ruc'] ?? '' ?></div>
                    <div class="doc-title">GUÍA DE REMISIÓN ELECTRÓNICA</div>
                    <div class="doc-num"><?= $guia['serie_comprobante'] ?>-<?= $guia['num_comprobante'] ?></div>
                </div>
            </td>
        </tr>
    </table>

    <table class="box">
        <tr>
            <td>
                <div><span class="label">Destinatario:</span> <?= $guia['cliente'] ?></div>
                <div><span class="label">R.U.C./DNI:</span> <?= $guia['num_documento'] ?></div>
                <div><span class="label">Dirección:</span> <?= $guia['direccion_cliente'] ?? '' ?></div>
            </td>
            <td>
                <div><span class="label">Fecha Emisión:</span> <?= $guia['fecha_emision'] ?></div>
                <div><span class="label">Fecha Traslado:</span> <?= $guia['fecha_traslado'] ?></div>
            </td>
        </tr>
    </table>

    <table class="box">
        <tr>
            <td>
                <div><span class="label">Motivo Traslado:</span> <?= $guia['motivo_traslado'] ?? 'VENTA' ?></div>
                <div><span class="label">Modalidad:</span>
                <?= $guia['modalidad_traslado'] ?? 'TRANSPORTE PÚBLICO' ?></div>
            </td>
            <td>
                <div><span class="label">Punto de Partida:</span>
                    <?= $guia['punto_partida'] ?? ($configuracion['direccion'] ?? '') ?>
                </div>
                <div><span class="label">Punto de Llegada:</span>
                    <?= $guia['punto_llegada'] ?? '' ?>
                </div>
            </td>
        </tr>
    </table>

    <table class="box" style="margin-top:6px;">
        <tr>
            <td colspan="4">
                <strong>DATOS DEL CONDUCTOR Y UNIDAD DE TRANSPORTE</strong>
            </td>
        </tr>
        <tr>
            <td><strong>Conductor:</strong></td>
            <td><?= $guia['personal'] ?? '' ?></td>
            <td><strong>Marca:</strong></td>
            <td><?= $guia['marca'] ?? '' ?></td>
        </tr>
        <tr>
            <td><strong>DNI:</strong></td>
            <td><?= $guia['dni_conductor'] ?? '' ?></td>
            <td><strong>Placa:</strong></td>
            <td><?= $guia['placa_vehiculo'] ?? '' ?></td>
        </tr>
        <tr>
            <td><strong>Licencia:</strong></td>
            <td><?= $guia['licencia_conductor'] ?? '' ?></td>
            <td><strong>RUC:</strong></td>
            <td><?= $guia['num_documento_trans'] ?? '' ?></td>
        </tr>
        <tr>
            <td><strong>Tarjeta circulación:</strong></td>
            <td><?= $guia['tarjeta_circulacion'] ?? '' ?></td>
            <td><strong>Número NTC:</strong></td>
            <td><?= $guia['num_ntc'] ?? '' ?></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td><strong>Razón social del transportista:</strong></td>
            <td><?= $guia['razon_social'] ?? '' ?></td>
        </tr>
    </table>

    <table class="detalle" style="margin-top:8px;">
        <thead>
            <tr>
                <th width="8%">CANT.</th>
                <th width="47%">DESCRIPCIÓN</th>
                <th width="10%">LAB.</th>
                <th width="12%">LOTE</th>
                <th width="13%">FEC. VENC.</th>
                <th width="10%">UNIDAD</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $itemTotal = 0;
            foreach ($detalles as $row) {
                $itemTotal += $row['cantidad'];
                $lote = !empty($row['iddetalle_guia']) ? buscarLotes($row['iddetalle_guia']) : '';
                $fecv = !empty($row['iddetalle_guia']) ? buscarVencimientos($row['iddetalle_guia']) : '';
                ?>
                <tr>
                    <td class="text-center"><?= round($row['cantidad'], 2) ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td class="text-center"><?= $row['contenedor'] ?? '' ?></td>
                    <td class="text-center"><?= $lote ?></td>
                    <td class="text-center"><?= $fecv ?></td>
                    <td class="text-center">
                        <?= $row['contenedor'] ? $row['contenedor'] . ' X ' . $row['cantidad_contenedor'] : 'UNIDAD X 1' ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <hr class="dotted">
    <div style="padding:4px 8px 0; font-weight:bold;">Cantidad total de ítems: <?= round($itemTotal, 0) ?></div>
    <div class="box text-center" style="margin-top:10px;">
        <strong>GUÍA DE REMISIÓN ELECTRÓNICA - REPRESENTACIÓN IMPRESA</strong><br>
        Autorizado mediante SEE - Del Contribuyente.<br>
        Generada el <?= date("d/m/Y H:i:s") ?>
    </div>

</body>

</html>