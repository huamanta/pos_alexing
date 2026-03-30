<?php
require "../../configuraciones/Conexion.php";
require "./Helpers.php";
$helpers = new Helpers();
date_default_timezone_set('America/Lima');

// Obtener ID del contrato desde parámetro encriptado
$idVenta = isset($_GET['idventa']) ? $helpers->encryptDecrypt('decrypt', $_GET['idventa']) : null;
// DATOS DINÁMICOS (puedes traerlos de BD basado en $idVenta)
$sqlNegocio = "SELECT * 
FROM datos_negocio 
ORDER BY id_negocio ASC 
LIMIT 1";
$resultNegocio = ejecutarConsultaSimpleFila($sqlNegocio);

$sqlVenta = "SELECT v.*, p.nombre AS nombre_cliente, p.num_documento AS num_documento_cliente, p.direccion AS direccion_cliente, p.telefono AS telefono_cliente, g.nombre AS nombre_garante, g.num_documento AS num_documento_garante 
             FROM venta v 
             INNER JOIN persona p ON v.idcliente = p.idpersona 
             INNER JOIN persona g ON v.idgarante = g.idpersona 
             WHERE v.idventa = $idVenta";
$resultVenta = ejecutarConsultaSimpleFila($sqlVenta);

$comprador = $resultVenta['nombre_cliente'] ?? '';
$dniComprador = $resultVenta['num_documento_cliente'] ?? '';
$direccionComprador = $resultVenta['direccion_cliente'] ?? '';
$celularComprador = $resultVenta['telefono_cliente'] ?? '';
$total = $resultVenta['total_venta'] ?? '';
$inicial = $resultVenta['totalrecibido'] ?? '';
$meses = $resultVenta['meses'] ?? '';
$numeroContrato = "C" . str_pad($resultVenta['idventa'], 9, '0', STR_PAD_LEFT);

$sqlSucursal = 'SELECT * FROM sucursal s INNER JOIN empresas e ON s.idempresa = e.idempresa WHERE s.idsucursal = ' . $resultVenta['idsucursal'];
$resultSucursal = ejecutarConsultaSimpleFila($sqlSucursal);



// Generación PDF con mPDF (server-side)
$garante = $resultVenta['nombre_garante'] ?? '';
$dniGarante = $resultVenta['num_documento_garante'] ?? '';
$fecha = $resultSucursal['distrito'] . ", " . $helpers->fechaLetras($resultVenta['fecha_hora']) ?? '';

// seleccionar detalle de la venta
$sqlDetalle = "SELECT * FROM detalle_venta dv INNER JOIN producto p ON dv.idproducto = p.idproducto WHERE dv.idventa = $idVenta";
$resultDetalle = ejecutarConsulta($sqlDetalle);

$data = [];
foreach ($resultDetalle as $row) {
    $data[] = [
        "idproducto" => $row['idproducto'],
        'nombre' => $row['nombre_producto'],
        "cantidad" => $row['cantidad'],
        "precio_venta" => $row['precio_venta'],
        "descuento" => $row['descuento']
    ];
}

$cuota = "619.00";

$dataFrecuencia = $helpers->getDataFrecuencia($resultVenta['frecuencia']);
$frecuenciaSm = $dataFrecuencia->short;
$frecuenciaTexto = $dataFrecuencia->texto;

ob_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Contrato</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 40px;
        }

        h2,
        h3 {
            text-align: center;
            margin: 5px;
        }

        p {
            text-align: justify;
            line-height: 1.5;
        }

        .header {
            text-align: center;
        }

        .section-title {
            font-weight: bold;
            margin-top: 15px;
        }

        .firma {
            margin-top: 50px;
            width: 100%;
        }

        .firma div {
            width: 30%;
            display: inline-block;
            text-align: center;
        }

        @media print {
            body {
                margin: 20px;
            }
        }
    </style>

</head>

<body>

    <div class="header">
        <h3><?php echo $resultNegocio['nombre']; ?></h3>
        <p><?php echo $resultSucursal['razon_social']; ?></p>
        <p>R.U.C. <?php echo $resultSucursal['ruc']; ?></p>
        <p>OFICINA: <?php echo $resultSucursal['nombre']; ?></p>

        <h2>CONTRATO DE COMPRA VENTA A PLAZOS</h2>
        <h3>N° <?php echo $numeroContrato; ?></h3>
    </div>

    <p>
        Conste por el presente documento el contrato de compra-venta a plazos con reserva de propiedad que celebran de
        una parte la empresa <b><?php echo $resultNegocio['nombre']; ?></b>, y de la otra parte el señor(a)
        <b><?php echo $comprador; ?></b>, identificado con DNI N° <?php echo $dniComprador; ?>, con domicilio en
        <?php echo $direccionComprador; ?>, celular <?php echo $celularComprador; ?>, con garante
        <?php echo $garante; ?> con DNI N° <?php echo $dniGarante; ?>.
    </p>

    <p class="section-title">PRIMERA: ANTECEDENTES</p>
    <p>El vendedor es propietario del siguiente vehículo:</p>

    <?php foreach ($data as $item): ?>
        <ul>
            <li>Marca:
                <?php echo $item['marca'] ?? ''; ?>
            </li>
            <li>Clase:
                <?php echo $item['clase'] ?? ''; ?>
            </li>
            <li>Combustible:
                <?php echo $item['combustible'] ?? ''; ?>
            </li>
            <li>Carrocería:
                <?php echo $item['carroceria'] ?? ''; ?>
            </li>
            <li>Color:
                <?php echo $item['color'] ?? ''; ?>
            </li>
            <li>Motor:
                <?php echo $item['motor'] ?? ''; ?>
            </li>
            <li>Serie:
                <?php echo $item['serie'] ?? ''; ?>
            </li>
            <li>Año:
                <?php echo $item['anio'] ?? ''; ?>
            </li>
            <li>Placa:
                <?php echo $item['placa'] ?? ''; ?>
            </li>
        </ul>
    <?php endforeach; ?>

    <p class="section-title">SEGUNDA: OBJETO</p>
    <p>
        El vendedor transfiere el vehículo al comprador bajo modalidad de pago a plazos con reserva de propiedad.
    </p>

    <p class="section-title">TERCERA: PLAZO</p>
    <p>
        El contrato tendrá una duración de <?php echo $meses; ?> <?php echo $frecuenciaSm; ?>.
    </p>

    <p class="section-title">CUARTA: PRECIO Y FORMA DE PAGO</p>
    <p>
        El valor total del vehículo es de S/ <?php echo $total; ?> <strong>(<?php echo $helpers->numeroALetrasMoneda($total); ?>)</strong>, con una cuota inicial de S/ <?php echo $inicial; ?>
        y <?php echo $meses; ?> cuotas <?php echo $frecuenciaTexto; ?> de S/ <?php echo $cuota; ?>.
    </p>

    <p class="section-title">QUINTA: RESERVA DE PROPIEDAD</p>
    <p>
        El vehículo será propiedad del vendedor hasta la cancelación total del monto.
    </p>

    <p class="section-title">SEXTA: INCUMPLIMIENTO</p>
    <p>
        El incumplimiento de pagos dará lugar a la resolución del contrato.
    </p>

    <p class="section-title">SÉPTIMA: RESPONSABILIDAD</p>
    <p>
        El comprador asume toda responsabilidad del vehículo desde su entrega.
    </p>

    <p style="text-align:right;"><?php echo $fecha; ?></p>

    <div class="firma">
        <div>
            ___________________________<br>
            VENDEDOR
        </div>

        <div>
            ___________________________<br>
            COMPRADOR
        </div>

        <div>
            ___________________________<br>
            GARANTE
        </div>
    </div>
    </script>

</body>
<?php
$html = ob_get_clean();
require_once __DIR__ . '/../../reportes/factura/pdf/vendor/autoload.php';

$options = new \Dompdf\Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new \Dompdf\Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('contrato_' . $numeroContrato . '.pdf', array('Attachment' => 0));
exit;

?>

</html>