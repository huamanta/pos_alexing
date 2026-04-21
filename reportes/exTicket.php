<?php
ob_start();
if (strlen(session_id()) < 1)
    session_start();

if (!isset($_SESSION["nombre"])) {
    echo 'Acceso denegado';
    exit();
}

require_once "../modelos/Venta.php";
require_once "../modelos/CuentasCobrar.php";
require_once "../modelos/Negocio.php";
require_once "Letras.php";
require "../public/docs_service/HelpersService.php";

$helpers = new HelpersService();
$venta = new Venta();
$cc = new CuentasCobrar();
$negocio = new Negocio();
$V = new EnLetras();

$reg = $venta->ventacabecera($_GET["id"])->fetch_object();
$regn = $negocio->listar()->fetch_object();

$formaPago = ($reg->ventacredito == "Si") ? "CRÉDITO" : "CONTADO";

// ===== DETALLE =====
$rsptad = $venta->ventadetalle($_GET["id"]);

$subtotal = 0;
$exonerado = 0;
$gravado = 0;
$detalle = [];

while ($d = $rsptad->fetch_object()) {
    $linea = $d->subtotal;
    $subtotal += $linea;

    if ($d->proigv == "No Gravada")
        $exonerado += $linea;
    else
        $gravado += $linea;

    $detalle[] = $d;
}

// ===== IGV =====
$igv = ($reg->tipo_comprobante != 'Nota de Venta')
    ? round($gravado * ($reg->impuesto / 100), 2)
    : 0;

$total = $reg->total_venta;

// ===== CREDITO =====
$total_abono = 0;
$total_deuda = 0;

if ($reg->ventacredito == "Si") {
    $rs = $cc->mostrarDeuda($_GET["id"]);
    while ($c = $rs->fetch_object()) {
        $total_deuda += $c->deudatotal;
        $total_abono += $c->abonototal;
    }
}
$saldo = $total_deuda - $total_abono;

// ===== LETRAS =====
$total_letras = $V->ValorEnLetras($total, "SOLES");

$sqlSucursal = 'SELECT * FROM sucursal s 
INNER JOIN empresas e ON s.idempresa = e.idempresa 
WHERE s.idsucursal = ' . $reg->idsucursal;

$resultSucursal = ejecutarConsultaSimpleFila($sqlSucursal);
$currency = $helpers->getCurrencyCode($resultSucursal['idsucursal'] ?? 0);

?>

<html>

<head>
    <meta charset="utf-8">

    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            width: 80mm;
            margin: 0;
        }

        .ticket {
            width: 72mm;
            padding: 4px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .small {
            font-size: 10px;
        }
    </style>
</head>

<body onload="imprimir()">

    <div class="ticket">

        <!-- EMPRESA -->
        <div class="center">
            <img src="../reportes/<?php echo $regn->logo; ?>" width="80"><br>
            <span class="bold"><?php echo $regn->nombre; ?></span><br>
            RUC: <?php echo $regn->documento; ?><br>
            <?php echo $regn->direccion; ?><br>
            Tel: <?php echo $regn->telefono; ?>
        </div>

        <div class="line"></div>

        <!-- COMPROBANTE -->
        <div class="center bold">
            <?php echo strtoupper($reg->tipo_comprobante); ?> ELECTRÓNICA<br>
            <?php echo $reg->serie_comprobante . "-" . $reg->num_comprobante; ?>
        </div>

        <div class="line"></div>

        <!-- CLIENTE -->
        <div class="small">
            Cliente: <?php echo $reg->cliente; ?><br>
            Doc: <?php echo $reg->num_documento; ?><br>
            Fecha: <?php echo $reg->fecha_kardex; ?><br>
            Pago: <?php echo $formaPago; ?>
        </div>

        <div class="line"></div>

        <!-- DETALLE -->
        <table>
            <tr class="bold">
                <td>Cant</td>
                <td>Descripción</td>
                <td class="right">Imp</td>
            </tr>

            <?php foreach ($detalle as $d) { ?>
                <tr>
                    <td><?php echo number_format($d->cantidad, 2); ?></td>
                    <td><?php echo substr($d->dproducto, 0, 16); ?></td>
                    <td class="right"><?php echo $helpers->monedaFormt($d->subtotal, $currency); ?></td>
                </tr>
            <?php } ?>
        </table>

        <div class="line"></div>

        <!-- TOTALES -->
        <table>

            <tr>
                <td>OP. GRAVADAS:</td>
                <td class="right"><?php echo $helpers->monedaFormt($gravado, $currency); ?></td>
            </tr>

            <?php if ($exonerado > 0) { ?>
                <tr>
                    <td>EXONERADO:</td>
                    <td class="right"><?php echo $helpers->monedaFormt($exonerado, $currency); ?></td>
                </tr>
            <?php } ?>

            <tr>
                <td><?php echo $resultSucursal['nombre_impuesto']; ?>(<?php echo $resultSucursal['monto_impuesto']; ?>%):</td>
                <td class="right"><?php echo $helpers->monedaFormt($igv, $currency); ?></td>
            </tr>

            <tr class="bold">
                <td>TOTAL:</td>
                <td class="right"><?php echo $helpers->monedaFormt($total, $currency); ?></td>
            </tr>

        </table>

        <div class="line"></div>

        <!-- LETRAS -->
        <div class="center small">
            SON: <?php echo $total_letras; ?>
        </div>

        <!-- CREDITO -->
        <?php if ($reg->ventacredito == "Si") { ?>
            <div class="line"></div>
            <table>
                <tr>
                    <td>Inicial:</td>
                    <td class="right"><?php echo $helpers->monedaFormt($total_abono, $currency); ?></td>
                </tr>
                <tr class="bold">
                    <td>Saldo:</td>
                    <td class="right"><?php echo $helpers->monedaFormt($saldo, $currency); ?></td>
                </tr>
            </table>
        <?php } ?>

        <div class="line"></div>

        <div class="center small">
            Gracias por su compra<br>
            Vendedor: <?php echo $reg->personal; ?>
        </div>

    </div>

    <script>
        function imprimir() {
            window.print();
            setTimeout(() => window.close(), 500);
        }
    </script>

</body>

</html>

<?php ob_end_flush(); ?>