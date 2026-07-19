<?php
ob_start();
session_start();
if (!isset($_SESSION["idusuario"])) {
  echo "Debe ingresar al sistema correctamente para visualizar el reporte";
  exit;
}

use Dompdf\Dompdf;
use Dompdf\Options;

$fecha_inicio = $_GET['fechai'];
$fecha_fin = $_GET['fechaf'];
$idcliente = $_GET['idcliente'];
$idsucursal = $_SESSION['idsucursal'];

require_once "../modelos/Consultas.php";
require_once "../modelos/Venta.php";
require_once "../modelos/CuentasCobrar.php";
require_once "../modelos/Helpers.php";

$consulta = new Consultas();
$CC = new CuentasCobrar();
$venta = new Venta();
$helpers = new Helpers();

$rspta = $consulta->ventasfechacliente(
  $fecha_inicio,
  $fecha_fin,
  $idcliente,
  $idsucursal
);

$estadocuenta = 0;

$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

@page{
    margin:20px;
}

body{
    font-family:DejaVu Sans;
    font-size:11px;
    color:#000;
}

h2{
    text-align:center;
    border:1px solid #000;
    padding:8px;
    margin-bottom:15px;
}

.info{
    margin-bottom:15px;
}

.info p{
    margin:3px 0;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    border:1px solid #000;
    background:#d9d9d9;
    padding:5px;
}

td{
    border:1px solid #000;
    padding:4px;
}

.productos th{
    background:#25C6E5;
    color:#fff;
}

.separador{
    height:18px;
}

.total{
    background:#d9d9d9;
    font-weight:bold;
}

.right{
    text-align:right;
}

.center{
    text-align:center;
}

</style>

</head>

<body>

<h2>REPORTE POR COBRAR CONSOLIDADO</h2>

<div class="info">

<p><strong>FECHA INICIO:</strong> ' . date("d/m/Y", strtotime($fecha_inicio)) . '</p>

<p><strong>FECHA FIN:</strong> ' . date("d/m/Y", strtotime($fecha_fin)) . '</p>

<p><strong>DETALLE DE DEUDA</strong></p>

</div>

';

while ($reg = $rspta->fetch_object()) {

  $codigoComprobante =
    $reg->tipo_comprobante .
    " - " .
    $reg->serie_comprobante .
    " - " .
    $reg->num_comprobante;

  $tipoVenta =
    ($reg->ventacredito == "Si")
    ? "CRÉDITO"
    : "CONTADO";

  $html .= '

    <table>

    <tr>

        <th width="35%">Cliente</th>

        <th width="35%">Comprobante</th>

        <th width="15%">Total Venta</th>

        <th width="15%">Tipo Venta</th>

    </tr>

    <tr>

        <td>' . htmlspecialchars($reg->cliente) . '</td>

        <td>' . $codigoComprobante . '</td>

        <td class="right">' . $helpers->get_currency_symbol($reg->total_venta) . '</td>

        <td class="center">' . $tipoVenta . '</td>

    </tr>

    </table>

    <br>

    ';
  $rsptad = $venta->ventadetalle($reg->idventa);

  $rsptacc = $CC->deudacliente($reg->idventa);

  $deudatotal = 0;

while ($regv = $rsptacc->fetch_object()) {
    $deudatotal += $regv->deuda;
}

  $html .= '

    <table class="productos">

        <tr>

            <th width="11%">CÓDIGO</th>

            <th width="49%">PRODUCTO</th>

            <th width="8%">CANT</th>

            <th width="10%">P.U.</th>

            <th width="10%">DESC.</th>

            <th width="12%">TOTAL</th>

        </tr>

    ';

  while ($regd = $rsptad->fetch_object()) {

    $codigo = $regd->codigo == "SIN CODIGO"
      ? "-"
      : $regd->codigo;

    $html .= '

        <tr>

            <td class="center">
                ' . htmlspecialchars($codigo) . '
            </td>

            <td>
                ' . htmlspecialchars($regd->producto) . '
            </td>

            <td class="center">
                ' . $regd->cantidad . '
            </td>

            <td class="right">
                ' . $helpers->get_currency_symbol($regd->precio_venta) . '
            </td>

            <td class="right">
                ' . $helpers->get_currency_symbol($regd->descuento) . '
            </td>

            <td class="right">
                ' . $helpers->get_currency_symbol($regd->subtotal) . '
            </td>

        </tr>

        ';

  }

  $html .= '

        <tr>

            <td colspan="6" class="right">

                <strong>

                    DEUDA PENDIENTE :
                    ' . $helpers->get_currency_symbol($deudatotal) . '

                </strong>

            </td>

        </tr>

    </table>

    <div class="separador"></div>

    ';

  $estadocuenta += $deudatotal;

}

$html .= '

<table style="margin-top:20px;">

    <tr>

        <td
            class="total right"
            style="font-size:13px;padding:8px;"
        >

            DEUDA TOTAL :
            ' . $helpers->get_currency_symbol($estadocuenta) . '

        </td>

    </tr>

</table>

</body>
</html>

';

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html, 'UTF-8');

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream(
  "Reporte_Cuentas_Cobrar_Consolidado.pdf",
  [
    "Attachment" => false
  ]
);

ob_end_flush();