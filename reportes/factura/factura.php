<?php
$iva = $configuracion['monto_impuesto'] ?? 18;
$formaPago = ($factura['ventacredito'] == 'Si') ? 'CRÉDITO' : 'CONTADO';
$condicionPago = ($factura['ventacredito'] == 'Si') ? 'CRÉDITO 90 DÍAS' : 'CONTADO';

// Letras
$entero = floor($factura['total_venta']);
$decimales = str_pad(round(($factura['total_venta'] - $entero) * 100), 2, '0', STR_PAD_LEFT);
$texto = strtoupper($formatter->toWords($entero));
$con_letra = "{$texto} Y {$decimales}/100 SOLES";
$logo = !empty($configuracion['logo']) ? $configuracion['logo'] : 'default.png';
$rutaLogo = realpath(__DIR__ . '/../../files/logos/' . $logo);

// Preparar datos fiscales (4 almacenes como en COBEFAR)
$direccion = $configuracion['direccion'] ?? 'Cal. Carlos Pedemonte Nro. 145B Int. 2pis Urb. Lotizacion Ex Fundo El Pino Lima - Lima - San Luis';
$telefono = $configuracion['telefono'] ?? '428-1248';
$email = $configuracion['email'] ?? 'cobefar.ventas@gmail.com';
$razonSocial = strtoupper($configuracion['razon_social'] ?? 'DROGUERIA COBEFAR');
$web = 'www.cobefar.com.pe';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= $factura['tipo_comprobante'] ?> <?= $factura['serie_comprobante'] ?>-<?= $factura['num_comprobante'] ?>
    </title>

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

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        /* ========== ENCABEZADO ========== */
        .header {
            margin-bottom: 0;
        }

        .header td {
            vertical-align: top;
            padding: 0;
        }

        .logo-area {
            width: 33%;
            text-align: center;
            padding-right: 15px;
        }

        .logo-area img {
            max-width: 130px;
            max-height: 60px;
        }

        .logo-text .marca {
            color: #2d8a3e;
            font-size: 18px;
            font-weight: bold;
            font-style: italic;
            line-height: 1;
        }

        .logo-text .marca .slogan {
            font-size: 8px;
            font-style: normal;
            letter-spacing: 2px;
            vertical-align: super;
        }

        .logo-text .web {
            color: #d32027;
            font-size: 8px;
            margin-top: 3px;
        }

        .fiscal-area {
            width: 34%;
            font-size: 8px;
            line-height: 1.35;
            padding-right: 15px;
        }

        .fiscal-area .label {
            font-weight: bold;
        }

        .fiscal-area .line {
            margin-bottom: 1px;
        }

        .ruc-area {
            width: 33%;
        }

        .ruc-box {
            border: 1.2px solid #000;
            padding: 8px 16px;
            text-align: center;
            border-radius: 10px;
        }

        .ruc-box .ruc {
            font-size: 11px;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        .ruc-box .doc-title {
            font-size: 14px;
            font-weight: bold;
            margin: 16px 0;
            letter-spacing: 1px;
        }

        .ruc-box .doc-num {
            font-size: 12px;
        }

        /* ========== SECCION CLIENTE + INFO DERECHA ========== */
        .cliente-info {
            margin-top: 8px;
        }

        .cliente-info td {
            vertical-align: top;
        }

        .cliente-box {
            width: 95%;
            border: 1px solid #000;
            border-radius: 10px;
            padding: 6px 10px;
        }

        .cliente-box table td {
            padding: 1.5px 3px 1.5px 0;
            font-size: 8.5px;
            vertical-align: top;
        }

        .cliente-box .label {
            font-weight: bold;
            width: 70px;
        }

        .cliente-box .value {
            font-weight: normal;
        }

        .info-derecha {
            width: 35%;
            padding-left: 8px;
            font-size: 8.5px;
        }

        .info-derecha .row {
            margin-bottom: 3px;
        }

        .info-derecha .label {
            font-weight: bold;
        }

        /* ========== PUNTO PARTIDA / LLEGADA ========== */
        .puntos-section {
            margin-top: 10px;
            border: 1px solid #000;
        }

        .puntos-section td {
            vertical-align: top;
            padding: 10px;
            font-size: 8.5px;
        }

        .puntos-section .label {
            font-weight: bold;
        }

        .puntos-section .puntos-left {
            width: 65%;
        }

        .puntos-section .puntos-right {
            width: 35%;
            padding-left: 8px;
        }

        /* ========== TABLA VENDEDOR ========== */
        .vendedor-tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .vendedor-tabla th {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 8.5px;
            font-weight: bold;
            text-align: center;
        }

        .vendedor-tabla td {
            border: 1px solid #000;
            padding: 5px 6px;
            font-size: 8.5px;
            text-align: center;
        }

        /* ========== DETALLE DE PRODUCTOS ========== */
        .detalle-tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        .detalle-tabla th {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            line-height: 1.15;
        }

        .detalle-tabla td {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 8.5px;
            vertical-align: middle;
        }

        .detalle-tabla td.text-right {
            text-align: right;
        }

        .detalle-tabla td.text-center {
            text-align: center;
        }

        .detalle-tabla td.desc {
            text-align: left;
        }

        /* ========== SON (importe letras) ========== */
        .son-section {
            border-top: none;
            padding: 5px 10px;
            font-size: 9px;
            font-weight: bold;
        }

        /* ========== LINEA PUNTEADA ========== */
        .dotted-line {
            border: none;
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        /* ========== SECCION INFERIOR (NOTAS + TOTALES) ========== */
        .footer-section {
            margin-top: 5px;
        }

        .footer-section td {
            vertical-align: top;
            padding: 0;
        }

        .notas-cell {
            width: 55%;
            padding-right: 12px;
        }

        .notas-content {
            text-align: center;
            font-size: 8.5px;
            line-height: 1.4;
            padding: 4px 6px;
        }

        .notas-content strong {
            font-weight: bold;
        }

        .totales-cell {
            width: 45%;
        }

        .totales-tabla {
            width: 100%;
            border-collapse: collapse;
        }

        .totales-tabla td {
            padding: 2px 6px;
            font-size: 9px;
            border: none;
        }

        .totales-tabla td.etiqueta {
            text-align: left;
            padding-left: 20px;
        }

        .totales-tabla td.simbolo {
            text-align: right;
            width: 30px;
            padding-right: 2px;
        }

        .totales-tabla td.monto {
            text-align: right;
            width: 60px;
            padding-right: 15px;
        }

        .totales-tabla .importe-total td {
            border-top: 1px solid #000;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .totales-tabla .importe-total td.etiqueta {
            font-weight: bold;
            font-size: 10px;
            padding-left: 20px;
        }

        .totales-tabla .importe-total td.monto {
            font-weight: bold;
            font-size: 10px;
        }

        .anulada {
            position: fixed;
            top: 80;
            left: 80;
            width: 80%;
            height: 80%;
            object-fit: contain;
            z-index: 999999;
            pointer-events: none;
        }
    </style>
</head>

<body>

    <?= $anulada ?? '' ?>

    <!-- ========== ENCABEZADO PRINCIPAL ========== -->
    <table class="header">
        <tr>
            <!-- LOGO -->
            <td class="logo-area">
                <div class="logo-text">
                    <img src="file://<?= $rutaLogo ?>" height="60">
                </div>
            </td>

            <!-- DATOS FISCALES -->
            <td class="fiscal-area">
                <div class="line"><span class="label">Dom. Fiscal:</span> <?= $direccion ?></div>
                <div class="line"><span class="label">Almacén:</span> Av. Nicolás Arriola N°2955-2963, 2do. Piso Urb.
                    Mercurio - San Luis - Lima - Lima</div>
                <div class="line"><span class="label">Almacén:</span> Jr. Antonio Miroquezada N° 806 Int. 303 Lima -
                    Lima - Lima</div>
                <div class="line"><span class="label">Almacén:</span> Jr. Antonio Miroquezada N° 806 Int. 502 Lima -
                    Lima - Lima</div>
                <div class="line"><span class="label">Teléfono:</span> <?= $telefono ?></div>
                <div class="line"><span class="label">Email:</span> <?= $email ?></div>
            </td>

            <!-- RUC Y COMPROBANTE -->
            <td class="ruc-area">
                <div class="ruc-box">
                    <div class="ruc">R.U.C. <?= $configuracion['ruc'] ?></div>
                    <div class="doc-title"><?= strtoupper($factura['tipo_comprobante']) ?> ELECTRÓNICA</div>
                    <div class="doc-num">N° <?= $factura['serie_comprobante'] ?>-<?= $factura['num_comprobante'] ?>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- ========== CAJA CLIENTE + INFO DERECHA ========== -->
    <table class="cliente-info">
        <tr>
            <td>
                <div class="cliente-box">
                    <table>
                        <tr>
                            <td class="label" width="65">Cliente:</td>
                            <td colspan="3"><?= $factura['cliente'] ?></td>
                        </tr>
                        <tr>
                            <td class="label">Dirección:</td>
                            <td colspan="3"><?= $factura['direccion'] ?></td>
                        </tr>
                        <tr>
                            <td class="label">R.U.C.:</td>
                            <td colspan="3"><?= $factura['num_documento'] ?></td>
                        </tr>
                        <tr>
                            <td class="label">Nro O/C:</td>
                            <td colspan="3"><?= $factura['numoperacion'] ?? '' ?></td>
                        </tr>
                    </table>
                </div>
            </td>
            <td class="info-derecha">
                <div class="row"><span class="label">FECHA DE EMISIÓN:</span> <?= $factura['fecha'] ?></div>
                <div class="row"><span class="label">MONEDA:</span> Soles</div>
                <div class="row"><span class="label">ALMACÉN:</span> <?= $configuracion['nombre'] ?></div>
            </td>
        </tr>
    </table>

    <!-- ========== PUNTO PARTIDA / LLEGADA ========== -->
    <table class="puntos-section">
        <tr>
            <td class="puntos-left">
                <div><span class="label">Punto de partida :</span>
                    <?= $configuracion['direccion'] ?? 'AV. MARISCAL ELOY URETA N° 45-65 URB. EL PINO San Luis Lima Lima' ?>
                </div>
            </td>
            <td class="puntos-right">
                <div><span class="label">Fecha Entrega :</span></div>
                <div><span class="label">Ruc / Dni Transportista:</span></div>
            </td>
        </tr>
        <tr>
            <td class="puntos-left">
                <div><span class="label">Punto de llegada :</span> <?= $factura['direccion'] ?></div>
            </td>
            <td class="puntos-right">
                <div><span class="label">Transportista :</span></div>
                <div><span class="label">Licencia :</span></div>
            </td>
        </tr>
    </table>

    <!-- ========== VENDEDOR / COND. PAGO / GUIA / ORDEN ========== -->
    <table class="vendedor-tabla">
        <thead>
            <tr>
                <th width="32%">VENDEDOR</th>
                <th width="22%">COND. PAGO</th>
                <th width="23%">GUÍA DE REMISIÓN</th>
                <th width="23%">NRO. ORDEN DE VENTA</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= strtoupper($factura['personal']) ?></td>
                <td><?= $condicionPago ?></td>
                <td><?= $factura['serie_comprobante'] ?? '' ?></td>
                <td><?= $factura['num_comprobante'] ?? '' ?></td>
            </tr>
        </tbody>
    </table>

    <!-- ========== DETALLE DE PRODUCTOS ========== -->
    <table class="detalle-tabla">
        <thead>
            <tr>
                <th width="6%">CANT.</th>
                <th width="32%">DESCRIPCIÓN</th>
                <th width="6%">LAB.</th>
                <th width="9%">LOTE</th>
                <th width="9%">FECHA VC.</th>
                <th width="9%">P.UNITARIO</th>
                <th width="7%">DSCTO %</th>
                <th width="10%">V.VENTA NETO</th>
                <th width="12%">PRECIO VENTA</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $descuento = 0;
            $exonerado = 0;
            $opgrav = 0;
            $opinafecta = 0;
            $opgratuita = 0;
            $anticipo = 0;
            $descGlobales = 0;
            foreach ($detalles as $row) {
                $precio_total = $row['subtotal'];
                if ($row['proigv'] == 'No Gravada') {
                    $exonerado += $precio_total;
                } elseif ($row['proigv'] == 'Inafecta') {
                    $opinafecta += $precio_total;
                } elseif ($row['proigv'] == 'Gratuita') {
                    $opgratuita += $precio_total;
                } else {
                    $opgrav += $precio_total;
                }
                $descuento += $row['descuento'];

                // Cálculo del V.Venta Neto (precio unitario menos descuento unitario)
                $cant = max($row['cantidad'], 1);
                $dsctoUnit = $row['descuento'] / $cant;
                $vVentaNeto = $row['precio_venta'] - $dsctoUnit;

                // Porcentaje descuento
                $dsctoPct = ($row['precio_venta'] * $cant) > 0 ? ($row['descuento'] / ($row['precio_venta'] * $cant)) * 100 : 0;
                ?>
                <tr>
                    <td class="text-center"><?= round($row['cantidad'], 2) ?></td>
                    <td class="desc"><?= htmlspecialchars($row['dproducto']) ?></td>
                    <td class="text-center"><?= $row['contenedor'] ?? '' ?></td>
                    <td class="text-center"><?= buscarLotes($row['iddetalle_venta']) ?></td>
                    <td class="text-center"><?= buscarVencimientos($row['iddetalle_venta']) ?></td>
                    <td class="text-right"><?= $helpers->get_currency_symbol($row['precio_venta']) ?></td>
                    <td class="text-right"><?= $helpers->get_currency_symbol($dsctoPct) ?></td>
                    <td class="text-right"><?= $helpers->get_currency_symbol($vVentaNeto) ?></td>
                    <td class="text-right"><?= $helpers->get_currency_symbol($row['subtotal']) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <!-- ========== LINEA PUNTEADA SEPARADORA ========== -->
    <hr class="dotted-line">
    <!-- ========== IMPORTE EN LETRAS ========== -->
    <div class="son-section">
        SON: <?= $con_letra ?>
    </div>

    <!-- ========== LINEA PUNTEADA SEPARADORA ========== -->
    <hr class="dotted-line">

    <!-- ========== SECCIÓN INFERIOR: NOTAS + TOTALES ========== -->
    <?php
    $total = $factura['total_venta'];
    $igv = $factura['impuesto'];
    $subtotal = $total - $igv;
    ?>

    <table class="footer-section">
        <tr>
            <td class="notas-cell">
                <div class="notas-content">
                    <strong>Autorizado mediante N° R.S. 300-2014/SUNAT</strong><br>
                    Representación impresa de la Factura Electrónica. Puede ser consultada<br>
                    en <strong>www.cobefar.com.pe</strong><br><br>
                    <strong>Nota:</strong> No aceptamos devoluciones ni reclamos después de la entrega de la mercadería
                </div>
            </td>

            <td class="totales-cell">
                <table class="totales-tabla">
                    <tr>
                        <td class="etiqueta">Op.Gravada</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($opgrav) ?></td>
                    </tr>
                    <tr>
                        <td class="etiqueta">Anticipo</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($anticipo) ?></td>
                    </tr>
                    <tr>
                        <td class="etiqueta">Desc. Globales</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($descGlobales) ?></td>
                    </tr>
                    <tr>
                        <td class="etiqueta">I.G.V <?= $iva ?>%</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($igv) ?></td>
                    </tr>
                    <tr>
                        <td class="etiqueta">Op.Inafecta</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($opinafecta) ?></td>
                    </tr>
                    <tr>
                        <td class="etiqueta">Op.Exonerada</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($exonerado) ?></td>
                    </tr>
                    <tr>
                        <td class="etiqueta">Op.Gratuita</td>
                        <td class="monto"><?= $helpers->get_currency_symbol($opgratuita) ?></td>
                    </tr>
                    <tr class="importe-total">
                        <td class="etiqueta"><strong>Importe Total</strong></td>
                        <td class="monto"><strong><?= $helpers->get_currency_symbol($total) ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ========== PAGOS (opcional) ========== -->
    <?php if (!empty($pagos)): ?>
        <div style="border:1px solid #000; margin-top:8px; padding:6px 10px; font-size:8.5px;">
            <strong>PAGOS REALIZADOS:</strong><br>
            <?php
            $totalPagado = 0;
            foreach ($pagos as $pago) {
                echo $pago['metodo_pago'] . ': ' . $helpers->get_currency_symbol($pago['monto']);
                if (!empty($pago['banco']))
                    echo ' | Banco: ' . $pago['banco'];
                if (!empty($pago['nroOperacion']))
                    echo ' | OP: ' . $pago['nroOperacion'];
                echo '<br>';
                $totalPagado += $pago['monto'];
            }
            ?>
            <br>
            <strong>Total Pagado:</strong> <?= $helpers->get_currency_symbol($totalPagado) ?> &nbsp;&nbsp;
            <strong>Saldo:</strong> <?= $helpers->get_currency_symbol($total - $totalPagado) ?>
        </div>
    <?php endif; ?>

    <!-- ========== PIE ========== -->
    <div
        style="margin-top:8px; padding:6px 10px; border:1px solid #000; border-radius:6px; text-align:center; font-size:8.5px;">
        <strong>Representación impresa de la <?= strtoupper($factura['tipo_comprobante']) ?> ELECTRÓNICA</strong><br>
        Autorizado mediante SEE - Del Contribuyente.
    </div>

</body>

</html>