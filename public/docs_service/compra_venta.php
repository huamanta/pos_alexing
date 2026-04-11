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
$sqlVenta = "SELECT v.*, ta.nombre AS nombre_tipo_acompanante, a.nombre AS nombre_acompanante, p.nombre AS nombre_cliente, p.num_documento AS num_documento_cliente, p.direccion AS direccion_cliente, p.telefono AS telefono_cliente, g.nombre AS nombre_garante, g.num_documento AS num_documento_garante 
             FROM venta v 
             INNER JOIN persona p ON v.idcliente = p.idpersona 
             LEFT JOIN persona g ON v.idgarante = g.idpersona 
             LEFT JOIN persona a ON v.idacompanante = a.idpersona
             LEFT JOIN tipoacompanante ta ON v.idtipoacompanante = ta.idtipoacompanante
             WHERE v.idventa = $idVenta";
$resultVenta = ejecutarConsultaSimpleFila($sqlVenta);


$comprador = $resultVenta['nombre_cliente'] ?? '';
$dniComprador = $resultVenta['num_documento_cliente'] ?? '';
$direccionComprador = $resultVenta['direccion_cliente'] ?? '';
$celularComprador = $resultVenta['telefono_cliente'] ?? '';
$total = $resultVenta['total_venta'] ?? '';
$inicial = $resultVenta['totalrecibido'] ?? '';
$meses = $resultVenta['meses'] ?? '';
$nombreAcompanante = $resultVenta['nombre_acompanante'] ?? '';
$nombreTipoAcompanante = $resultVenta['nombre_tipo_acompanante'] ?? '';

$sqlSucursal = 'SELECT * FROM sucursal s INNER JOIN empresas e ON s.idempresa = e.idempresa WHERE s.idsucursal = ' . $resultVenta['idsucursal'];
$resultSucursal = ejecutarConsultaSimpleFila($sqlSucursal);
$idSucursal = $resultVenta['idsucursal'] ?? 0;
if (!$idSucursal) {
    $idSucursal = $resultSucursal['idsucursal'] ?? 0; // Valor por defecto si no se encuentra la sucursal
}
$currency = $helpers->getCurrencyCode($idSucursal);


// Generación PDF con mPDF (server-side)
$garante = $resultVenta['nombre_garante'] ?? '';
$dniGarante = $resultVenta['num_documento_garante'] ?? '';
$simbolo = $resultSucursal['moneda'] ?? '';
$fecha = $resultSucursal['distrito'] . ", " . $helpers->fechaLetras($resultVenta['fecha_hora']) ?? '';

// seleccionar detalle de la venta
$sqlDetalle = "SELECT dv.*, p.nombre AS producto_nombre, p.fabricante AS marca, p.modelo, p.color,
              p.numserie AS serie, p.motor, p.anio_fabricacion AS anio, p.placa,
              p.clase_vehiculo AS clase, p.tipo_vehiculo
          FROM detalle_venta dv
          LEFT JOIN producto_configuracion pg ON dv.idproducto = pg.id
          LEFT JOIN producto p ON p.idproducto = COALESCE(pg.idproducto, dv.idproducto)
          WHERE dv.idventa = $idVenta";
$resultDetalle = ejecutarConsulta($sqlDetalle);

$monto = $helpers->monedaFormt($_GET["monto"] ?? '0.00', $currency);

$data = [];
foreach ($resultDetalle as $row) {
    $data[] = [
        "idproducto" => $row['idproducto'],
        'nombre' => $row['producto_nombre'] ?? 'N/A',
        'marca' => $row['marca'] ?? 'N/A',
        'modelo' => $row['modelo'] ?? 'N/A',
        'color' => $row['color'] ?? 'N/A',
        'serie' => $row['serie'] ?? 'N/A',
        'motor' => $row['motor'] ?? 'N/A',
        'anio' => $row['anio'] ?? 'N/A',
        'placa' => $row['placa'] ?? 'NUEVO',
        'clase' => $row['clase'] ?? 'N/A',
        'tipo_vehiculo' => $row['tipo_vehiculo'] ?? 'N/A',
        "cantidad" => $row['cantidad'],
        "precio_venta" => $row['precio_venta'],
        "descuento" => $row['descuento']
    ];
}

$item = $data[0] ?? [];

$cuota = "619.00";

$dataFrecuencia = $helpers->getDataFrecuencia($resultVenta['frecuencia'] ?? '1');
$frecuenciaSm = $dataFrecuencia->short;
$frecuenciaTexto = $dataFrecuencia->texto;

// buscar actaentrega
$sqlActa = "SELECT * FROM documentacion WHERE idventa = $idVenta AND tipo = '1'";
$resultActa = ejecutarConsultaSimpleFila($sqlActa);
if (!$resultActa) {
    echo '
    <style>
        .notfound-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.4);
            z-index: 9999;
        }

        .notfound-box {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            width: 350px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            animation: fadeIn 0.3s ease;
        }

        .notfound-box i {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 15px;
        }

        .notfound-box h3 {
            margin: 10px 0;
            color: #333;
        }

        .notfound-box p {
            color: #666;
            font-size: 14px;
        }

        .notfound-box button {
            margin-top: 15px;
            padding: 8px 15px;
            border: none;
            background: #dc3545;
            color: white;
            border-radius: 8px;
            cursor: pointer;
        }

        .notfound-box button:hover {
            background: #c82333;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>

    <div class="notfound-container">
        <div class="notfound-box">
            <i class="fa fa-file-circle-xmark"></i>
            <h3>Documento no encontrado</h3>
            <p>No se encontró el acta de entrega para esta venta.</p>
            <button onclick="window.close()">Cerrar</button>
        </div>
    </div>
    ';
    exit;
}

$numeroContrato = $helpers->tiposDocumentacion($resultActa['tipo']) . str_pad($resultActa['correlativo'], 9, '0', STR_PAD_LEFT);


ob_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Compra venta</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 11px;
            margin: 40px;
            line-height: 1.4;
            color: #000;
        }

        <?php echo Helpers::getDocumentHeaderStyles(); ?>

        .titulo {
            font-weight: bold;
            text-align: center;
            font-size: 15px;
            text-decoration: underline;
        }

        .numero {
            text-align: center;
            font-weight: bold;
            margin-bottom: 13px;
        }

        p {
            text-align: justify;
            margin: 5px 0;
            font-size: 13px;
        }

        .clausula {
            font-weight: bold;
            text-decoration: underline;
        }

        .firma {
            margin-top: 60px;
            width: 100%;
        }

        .firma div {
            width: 45%;
            display: inline-block;
            text-align: center;
            font-size: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    <?php
    echo Helpers::renderDocumentHeader(
        $resultNegocio['nombre'] ?? '',
        $resultSucursal['ruc'] ?? '',
        'CONTRATO DE COMPRA - VENTA VEHICULAR'
    );
    ?>
    <br>

    <p>
        Conste por el presente documento, el contrato de COMPRA Y VENTA conforme al artículo
        1529 del C.C. del vehículo de Placa de rodaje <strong>N° <?php echo $item['placa'] ?? 'NUEVO'; ?></strong> que
        celebran de una parte como
        <strong>"VENDEDOR"</strong> al Señor(a): <strong><?php echo $item['vendedor'] ?? 'N/A'; ?></strong>.
        identificado con DNI <strong> Nº <?php echo $item['dni_vendedor'] ?? 'N/A'; ?></strong> domiciliado en
        el JR. JIMENEZ PIMENTEL NRO. 886 SAN MARTIN - SAN MARTIN - TARAPOTO
        distrito de TARAPOTO provincia de SAN MARTIN departamento de SAN MARTIN y de otra
        parte como, <strong>"COMPRADOR"</strong>, El(La) .Sr(a). <strong><?php echo $comprador ?? 'N/A'; ?></strong>,
        identificado con DNI <strong> Nº <?php echo $dniComprador ?? 'N/A'; ?></strong> con domicilio en
        <strong><?php echo $direccionComprador ?? 'N/A'; ?></strong>. Quien manifiesta que su
        <?php echo $nombreAcompanante ?? 'N/A'; ?> no interviene en la aplicación de los artículos
        Nº 315 y 886 inciso 1 del Código Civil, quien en adelante se les denominaran las partes:
        VENDEDOR Y COMPRADOR respectivamente, en los términos y condiciones siguientes:
    </p>

    <p>
        <b class="clausula">PRIMERO.-</b>
        El VENDEDOR declara que es propietario del vehículo de las siguientes características:
    </p>

    <table class="table">
        <tr>
            <td><strong>CLASE</strong></td>
            <td>: <?php echo $item['clase'] ?? 'N/A'; ?></td>
            <td><strong>COLOR</strong></td>
            <td>: <?php echo $item['color'] ?? 'N/A'; ?></td>
        </tr>
        <tr>
            <td><strong>MARCA</strong></td>
            <td>: <?php echo $item['marca'] ?? 'N/A'; ?></td>
            <td><strong>N° SERIE</strong></td>
            <td>: <?php echo $item['serie'] ?? 'N/A'; ?></td>
        </tr>
        <tr>
            <td><strong>MODELO</strong></td>
            <td>: <?php echo $item['modelo'] ?? 'N/A'; ?></td>
            <td><strong>N° MOTOR</strong></td>
            <td>: <?php echo $item['motor'] ?? 'N/A'; ?></td>
        </tr>
        <tr>
            <td><strong>AÑO FABRICACIÓN</strong></td>
            <td>: <?php echo $item['anio'] ?? 'N/A'; ?></td>
            <td><strong>N° PLACA</strong></td>
            <td>: <?php echo $item['placa'] ?? 'NUEVO'; ?></td>
    </table>

    <p>
        Sin reserva ni restricción alguna y por tanto no se encuentra impedido de enajenarlo:
    </p>
    <br>
    <p>
        <b class="clausula">SEGUNDO.-</b>
        El precio pactado por la COMPRA – VENTA del vehículo a que se refiere la
        cláusula anterior es de <?php echo $monto ?? '0.00'; ?> ( <?php echo $helpers->numeroALetrasMoneda($total, $currency); ?>) cancelado en su totalidad, donde se
        firmará todo los documentos de acuerdo a Ley del vehículo por su cancelación.
    </p>
    <br>
    <p>
        <b class="clausula">TERCERO.-</b>
        Igualmente la parte COMPRADOR a su vez, haber recibido el vehículo materia del
        presente contrato a su entera satisfacción y sin responsabilidad alguna en el futuro para la
        parte VENDEDOR en la parte técnica- mecánica por ser vehículo usado.
    </p>
    <br>
    <p>
        <b class="clausula">CUARTO.-</b>
        Ambos contratantes declaran conocer la situación legal (embargos, multas,
        cualquier afectación, cargo, etc). del vehículo mediante el correspondiente certificado de
        gravamen, expedido por la Dirección de Circulación Terrestre de la jurisdicción en la que Está
        escrito la unidad que se transfiere y conocida su situación jurídica administrativa antes de
        celebrar el presente contrato; quedando obligado la parte VENDEDOR en todo caso a la
        evicción y saneamiento de Ley.
    </p>

    <p>
        Así mismo el comprador será de su total responsabilidad cualquier accidente futura que sufra
        con el vehículo con choques, con heridos y muertos, por atropello o cualquier otro percance.

    </p>
    <br>
    <p>
        <b class="clausula">QUINTO.-</b>
        Concordante con la cláusula primera la parte VENDEDOR declara que el vehículo,
        que se enajena, no se encuentra inscrito en el registro fiscal, de ventas a plazos de otro tipo de
        afección: igualmente tratándose de vehículo de servicio público de empresa legalmente
        constituida y autorizada, no se encuentra inscrito en el registro de prendas de vehículo,
        pudiendo a la parte COMPRADOR solicitar constancia certificada de dichos registros para
        asegurar la eficacia de la operación realizada.
    </p>
    <br>
    <p><b class="clausula">CLAUSULA ADICIONAL.-</b>
        El COMPRADOR declara recibir la motocicleta en buen estado de
        funcionamiento, la tarjeta de propiedad, SOAT, placa que derive de ella. En conformidad de todas las clausulas
        legalizan sus firmas las partes COMPRADOR y la parte VENDEDOR
    </p>
    <br>
    <p class="text-right"><?php echo $fecha; ?></p>

    <br><br>

    <div class="firma">
        <div>
            ________________________________________<br>
            FIRMA Y SELLO DEL GERENTE GENERAL<br>
            ARRENDADOR-VENDEDOR
        </div>

        <div>
            ________________________________________<br>
            <?php echo strtoupper($comprador); ?><br>
            ARRENDADOR-COMPRADOR
        </div>
    </div>

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