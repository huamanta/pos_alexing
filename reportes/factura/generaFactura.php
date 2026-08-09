<?php

// if (empty($_SESSION['nombre'])) {
//     echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
//     exit();
// }
require_once __DIR__ . '/../../configuraciones/bootstrap.php';
include __DIR__ . "/../../configuraciones/Conexion.php";
require_once __DIR__ . "/../../modelos/Helpers.php";
$helpers = new Helpers();

use Dompdf\Dompdf;
use Dompdf\Options;
use Luecano\NumeroALetras\NumeroALetras;

$formatter = new NumeroALetras();

if (empty($_GET["id"])) {
    echo "No es posible generar la factura.";
    exit();
}

$idventa = $_GET["id"];
$anulada = '';


// ================== VENTA ==================
$query = mysqli_query($conexion, "
    SELECT 
        v.idventa, 
        v.idsucursal,
        s.nombre as almacen, 
        v.idcliente, 
        p.nombre AS cliente, 
        p.direccion, 
        p.tipo_documento, 
        p.num_documento, 
        p.email, 
        p.telefono, 
        v.idpersonal, 
        u.nombre AS personal, 
        v.montoPagado, 
        v.formaPago, 
        DATE_FORMAT(v.fechadeposito, '%d/%m/%y') as fechadeposito, 
        v.numoperacion, 
        cp.nombre AS tipo_comprobante,
        v.serie_comprobante, 
        v.num_comprobante, 
        DATE_FORMAT(v.fecha_hora, '%d/%m/%Y') as fecha,
        DATE_FORMAT(v.fecha_hora, '%r') as hora,
        DATE_FORMAT(v.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex, 
        v.impuesto, 
        v.total_venta, 
        v.ventacredito, 
        v.estado,
        v.observacion,
        v.interes 
    FROM venta v 
    INNER JOIN comp_pago cp ON cp.idcomprobante_pago = v.idcomprobante_pago
    INNER JOIN persona p ON v.idcliente = p.idpersona 
    INNER JOIN personal u ON v.idpersonal = u.idpersonal
    INNER JOIN sucursal s ON v.idsucursal = s.idsucursal
    WHERE v.idventa = '$idventa'
");

if (!$query) {
    die("Error en consulta venta: " . mysqli_error($conexion));
}

$result = mysqli_num_rows($query);

if ($result <= 0) {
    echo "No se encontró la venta.";
    exit();
}

$factura = mysqli_fetch_assoc($query);
$idsucursal = $factura['idsucursal'];
$query_config = mysqli_query($conexion, "SELECT * FROM sucursal s INNER JOIN empresas e ON s.idempresa = e.idempresa WHERE s.idsucursal = $idsucursal");
$result_config = mysqli_num_rows($query_config);
if ($result_config > 0) {
    $configuracion = mysqli_fetch_assoc($query_config);
}

// ================== CUENTAS ==================
$query2 = mysqli_query($conexion, "
    SELECT 
        idventa, 
        fecharegistro, 
        SUM(deudatotal) AS totalDeuda, 
        DATE_FORMAT(fechavencimiento,'%d/%m/%y') as fechavencimiento, 
        abonototal 
    FROM cuentas_por_cobrar 
    WHERE idventa = '$idventa'
    GROUP BY idventa, fecharegistro, fechavencimiento, abonototal
");

if (!$query2) {
    die("Error en cuentas_por_cobrar: " . mysqli_error($conexion));
}

$cuentasc = mysqli_fetch_assoc($query2);

// ================== PAGOS ==================
$pagos = [];

$query_pagos = mysqli_query($conexion, "
    SELECT vp.metodo_pago, vp.monto, vp.nroOperacion, vp.fechaDeposito, vp.idbanco, b.nombre AS banco
    FROM venta_pago vp
    INNER JOIN bancos b ON b.idbanco = vp.idbanco
    WHERE idventa = '$idventa'
");

if ($query_pagos) {
    while ($row_pago = mysqli_fetch_assoc($query_pagos)) {
        $pagos[] = $row_pago;
    }
}

// ================== ESTADO ==================
if ($factura['estado'] == 'Nota Credito') {
    $anulada = '<img class="anulada" src="img/anulado.png" alt="Anulada">';
}

// ================== DETALLE ==================
$query_productos = mysqli_query($conexion, "
    SELECT 
    a.idproducto, 
    pg.contenedor, 
    a.nombre AS producto, 
    d.nombre_producto AS dproducto, 
    um.nombre AS unidadmedida, 
    CASE WHEN pg.codigo_extra = 'SIN CODIGO' THEN '-' ELSE a.codigo END AS codigo, 
    d.cantidad, 
    d.precio_venta,
    d.descuento as descuentodv,
    a.precioB, a.precioC, a.precioD, a.preciocigv,
    CASE 
        WHEN d.check_precio = 1 THEN d.precio_venta 
        ELSE (d.cantidad * d.precio_venta - d.descuento)
    END AS subtotal,
    ip.stock, 
    a.proigv,
    d.check_precio
FROM detalle_venta d 
LEFT JOIN producto a ON d.idproducto = a.idproducto 
LEFT JOIN producto_configuracion pg ON pg.idproducto = a.idproducto
LEFT JOIN inventario_producto ip ON ip.idproducto = a.idproducto
LEFT JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida
INNER JOIN venta v ON v.idventa = d.idventa
WHERE d.idventa = '$idventa'
");

if (!$query_productos) {
    die("Error en detalle_venta: " . mysqli_error($conexion));
}

$result_detalle = mysqli_num_rows($query_productos);

// ================== HTML ==================
ob_start();
include(dirname(__FILE__) . '/factura.php');
$html = ob_get_clean();

// ================== PDF ==================

$options = new Options();
$options->setIsRemoteEnabled(true);
$options->setChroot(realpath(__DIR__ . '/../../'));

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// ================== NOMBRE DOC ==================
if ($factura['tipo_comprobante'] == 'NCB' || $factura['tipo_comprobante'] == 'NCF') {
    $tipodoc = 'Nota_de_Credito';
} else {
    $tipodoc = $factura['tipo_comprobante'];
}

$dompdf->stream(
    $tipodoc . '_N_' . $factura['serie_comprobante'] . '-' . $factura['num_comprobante'] . '.pdf',
    ['Attachment' => 0]
);

exit;