<?php
ob_start();
require_once __DIR__ . '/../configuraciones/bootstrap.php';
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

$ventaData = $venta->ventacabecera($_GET["id"]);
$sucursal = $negocio->listar($ventaData['idsucursal']);

$formaPago = ($ventaData['ventacredito'] == "Si") ? "CRÉDITO" : "CONTADO";

// ===== DETALLE =====
$detalles = $venta->ventadetalle($_GET["id"]);

$subtotal = 0;
$exonerado = 0;
$gravado = 0;
$detalle = [];

foreach ($detalles as $d) {

    $linea = $d['subtotal'];
    $subtotal += $linea;

    if ($d['proigv'] == "No Gravada") {
        $exonerado += $linea;
    } else {
        $gravado += $linea;
    }


    $detalle[] = $d;
}

// ===== IGV =====
$igv = $ventaData['impuesto'];

$total = $ventaData['total_venta'];

// ===== CREDITO =====
$total_abono = 0;
$total_deuda = 0;
$inicial = $ventaData['totalrecibido'];

if ($ventaData['ventacredito'] == "Si") {
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
WHERE s.idsucursal = ' . $ventaData['idsucursal'];

$currency = $helpers->getCurrencyCode($ventaData['idsucursal']);
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
            padding: 8px;
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
            <img src="../files/logos/<?php echo !empty($sucursal['logo']) ? $sucursal['logo'] : 'default.png'; ?>"
                width="80">
            <br>
            <h1 class="bold"><?php echo $sucursal['razon_social']; ?></h1>
            <span class="bold">sucursal: <?php echo $sucursal['nombre']; ?></span><br>
            RUC: <?php echo $sucursal['ruc']; ?><br>
            <?php echo $sucursal['direccion']; ?><br>
            Tel: <?php echo $sucursal['telefono']; ?>
        </div>

        <div class="line"></div>

        <!-- COMPROBANTE -->
        <div class="center bold">
            <?php echo strtoupper($ventaData['tipo_comprobante']); ?> ELECTRÓNICA<br>
            <?php echo $ventaData['serie_comprobante'] . "-" . $ventaData['num_comprobante']; ?>
        </div>

        <div class="line"></div>

        <!-- CLIENTE -->
        <div class="small">
            Cliente: <?php echo $ventaData['cliente']; ?><br>
            Doc: <?php echo $ventaData['num_documento']; ?><br>
            Fecha: <?php echo $ventaData['fecha_kardex']; ?><br>
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
                    <td><?php echo number_format($d['cantidad'], 2); ?></td>
                    <td><?php echo substr($d['dproducto'], 0, 16); ?></td>
                    <td class="right"><?php echo $helpers->monedaFormt($d['subtotal'], $currency); ?></td>
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
                <td><?php echo $sucursal['nombre_impuesto']; ?>(<?php echo $sucursal['monto_impuesto']; ?>%):
                </td>
                <td class="right"><?php echo $helpers->monedaFormt($igv, $currency); ?></td>
            </tr>
            <tr>
                <td>SUBTOTAL:</td>
                <td class="right"><?php echo $helpers->monedaFormt($total - $igv, $currency); ?></td>
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
        <?php if ($ventaData['ventacredito'] == "Si") { ?>
            <div class="line"></div>
            <table>
                <tr>
                    <td>Inicial:</td>
                    <td class="right"><?php echo $helpers->monedaFormt($inicial, $currency); ?></td>
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
            Vendedor: <?php echo $ventaData['personal']; ?>
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