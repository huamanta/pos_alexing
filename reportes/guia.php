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
            font-size: 8.5px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.2;
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
            padding: 8px 10px;
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

        .label {
            font-weight: bold;
            display: inline-block;
            min-width: 90px;
        }

        .detalle th,
        .detalle td {
            border: 1px solid #000;
            padding: 5px 4px;
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
    </style>
</head>

<body>

    <?php
    // Logo convertido a ruta segura
    $logo = !empty($configuracion['logo']) ? $configuracion['logo'] : 'default.png';
    $rutaLogo = realpath(__DIR__ . '/../files/logos/' . $logo);
    ?>

    <table class="header-table">
        <tr>
            <td width="33%" class="text-center">
                <div class="logo-text">
                    <img src="file://<?= $rutaLogo ?>" height="60">
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
                <div></div><span class="label">Motivo Traslado:</span> <?= $guia['motivo_traslado'] ?? 'VENTA' ?></div>
                <div></div><span class="label">Modalidad:</span>
                <?= $guia['modalidad_traslado'] ?? 'TRANSPORTE PÚBLICO' ?></div>
            </td>
            <td>
                <div><span class="label">Punto de
                        Partida:</span><br><?= $guia['punto_partida'] ?? ($configuracion['direccion'] ?? '') ?></div>
                <div><span class="label">Punto de Llegada:</span><br><?= $guia['punto_llegada'] ?? '' ?></div>
            </td>
        </tr>
    </table>

    <table class="box" style="margin-top:6px;">
        <tr>
            <td colspan="4">
                <label>DATOS DEL CONDUCTOR Y UNIDAD DE TRANSPORTE</label>
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
            <td><strong>Numero NTC:</strong></td>
            <td><?= $guia['num_ntc'] ?? '' ?></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td><strong>Razon social</strong></td>
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
                // Cambia aquí si tu campo se llama distinto: usa iddetalle_venta o iddetalle_guia
                $lote = "buscarLotes";
                $fecv = "buscarVencimientos";
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
    <div style="padding:0 8px; font-weight:bold;">Total de Items: <?= round($itemTotal, 0) ?></div>
    <div class="box text-center" style="margin-top:10px;">
        <strong>GUÍA DE REMISIÓN ELECTRÓNICA - REPRESENTACIÓN IMPRESA</strong><br>
        Autorizado mediante SEE - Del Contribuyente.<br>
        Generada el <?= date("d/m/Y H:i:s") ?>
    </div>

</body>

</html>