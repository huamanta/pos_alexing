<?php
require "../../configuraciones/Conexion.php";
require "./HelpersService.php";
$helpers = new HelpersService();
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
$fecha = $resultSucursal['distrito'] . ", " . $helpers->fechaLetras($resultVenta['fecha_hora']) ?? '';

// seleccionar detalle de la venta
$sqlDetalle = "SELECT dv.*, p.nombre AS producto_nombre, m.nombre AS marca, mo.nombre AS modelo, p.color,
                       p.numserie AS serie, p.motor, p.anio_fabricacion AS anio, p.placa,
                       p.clase_vehiculo AS clase, p.tipo_vehiculo
                FROM detalle_venta dv
                LEFT JOIN producto_configuracion pg ON dv.idproducto = pg.id
                LEFT JOIN producto p ON p.idproducto = COALESCE(pg.idproducto, dv.idproducto)
                LEFT JOIN marca m ON m.idmarca = p.idmarca
                LEFT JOIN modelo mo ON mo.idmodelo = p.idmodelo
                WHERE dv.idventa = $idVenta";
$resultDetalle = ejecutarConsulta($sqlDetalle);

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
    <title>Contrato</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 11px;
            margin: 40px;
            line-height: 1.4;
            color: #000;
        }

        <?php echo HelpersService::getDocumentHeaderStyles(); ?>

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
    </style>
</head>

<body>

    <?php
    echo HelpersService::renderDocumentHeader(
        $resultNegocio['nombre'] ?? '',
        $resultSucursal['ruc'] ?? '',
        'CONTRATO DE VENTA AL CONTADO DE VEHICULO MOTORIZADO',
        $numeroContrato
    );
    ?>

    <p>
        Conste por el presente documento, el contrato de <b>VENTA AL CONTADO</b> de vehículo <b>NUEVO</b>, que celebran
        de
        una parte como <b>VENDEDOR</b>, la Empresa "<b><?php echo strtoupper($resultNegocio['nombre']); ?></b>", con RUC
        Nº <?php echo $resultSucursal['ruc']; ?>, representado
        por su Gerente General el señor <b>JESUS ROBERTO SURCO KACASACA</b>, identificado con DNI Nº <b>43978509</b>,
        con
        domicilio en <b>JR. JIMENEZ PIMENTEL NRO. 886, SAN MARTIN - SAN MARTIN - TARAPOTO</b>; con facultades
        inscrita en la partida electrónica N° 11070911 del registro de personas jurídicas de la Oficina Registral
        Tarapoto;
        y de la otra parte como <b>COMPRADOR</b> el(la) señor(a) <b><?php echo strtoupper($comprador); ?></b>,
        identificado con DNI
        Nº <b><?php echo $dniComprador; ?></b>, de estado civil soltero(a), con domicilio en
        <b><?php echo strtoupper($direccionComprador); ?></b> y con número de celular
        <b><?php echo $celularComprador; ?></b>, acompañado de <b><?php echo strtoupper($nombreAcompanante); ?></b> en
        calidad de <b><?php echo strtoupper($nombreTipoAcompanante); ?></b> en los siguientes términos:
    </p>

    <p><b class="clausula">PRIMERO.-</b> La Empresa <?php echo strtoupper($resultNegocio['nombre']); ?>, declara ser
        propietario y
        titular registral del vehículo
        <b>MOTOCICLETA</b> con las siguientes características:


        <?php foreach ($data as $item): ?>
            marca <b><?php echo $item['marca'] ?? 'N/A'; ?></b>, modelo <b><?php echo $item['modelo'] ?? 'N/A'; ?></b>,
            color
            <b><?php echo $item['color'] ?? 'N/A'; ?></b> con
            Nº serie <b><?php echo $item['serie'] ?? 'N/A'; ?></b>, Nº de motor
            <b><?php echo $item['motor'] ?? 'N/A'; ?></b>, año de
            fabricación <b><?php echo $item['anio'] ?? 'N/A'; ?></b> Nº de cilindro
            01, N° Placa de Rodaje <b><?php echo $item['placa'] ?? 'NUEVO'; ?></b>. El mismo que es <b>NUEVO</b>.

        <?php endforeach; ?>


        Bien mueble que fue reconocido físicamente en su calidad de vehículo MOTOCICLETA <b>NUEVO</b> por ambas partes
        con anterioridad.
    </p>

    <p><b class="clausula">SEGUNDO.-</b> El VENDEDOR, deja constancia que el vehículo MOTOCICLETA descrito en la
        cláusula primera, se
        encuentra en perfecto estado de conservación y funcionamiento, por ser este bien mueble en calidad de NUEVO.</p>

    <p><b class="clausula">TERCERO.-</b> El VENDEDOR, declara que, el vehículo MOTOCICLETA se encuentra, al momento de
        celebrarse
        este contrato, libre de toda carga, gravamen, derecho real de garantía, medida judicial o extrajudicial,
        papeletas
        en el SAT y en general de todo acto o circunstancia que impida, prive o limite la posesión o uso del bien; por
        tratarse de un bien mueble en calidad de NUEVO; no obstante a ellos se obliga a la evicción o saneamiento de
        ley; asimismo el alquiler-venta se hace Ad-Corpus.</p>

    <p><b class="clausula">CUARTO.-</b> El PRECIO FINAL pactado por ambas partes por la venta del vehículo MOTOCICLETA
        descrito en la
        cláusula primera, es de <b><?php echo $helpers->monedaFormt($total, $currency); ?></b> (
        <b><?php echo $helpers->numeroALetrasMoneda($total, $currency); ?></b> ), suma que el COMPRADOR abonará
        al VENDEDOR en su totalidad de <b><?php echo $helpers->monedaFormt($total, $currency); ?></b> (
        <b><?php echo $helpers->numeroALetrasMoneda($total, $currency); ?></b> ), importe que deberá ser
        cancelado en moneda nacional y en efectivo; asimismo, EL VENDEDOR, se le entregará un recibo por el importe
        pactado.
    </p>

    <p><b class="clausula">QUINTO.-</b> EL COMPRADOR acepta que, una vez realizada la compra, no habrá devolución del
        dinero bajo
        ninguna circunstancia. El COMPRADOR entendió que la compra es definitiva y no puede ser cancelada.</p>

    <p><b class="clausula">SEXTO.-</b> EL VENDEDOR, garantiza que el vehículo se encuentra en buen estado de
        funcionamiento sin garantías
        ni responsabilidades. EL COMPRADOR, declara haber inspeccionado el vehículo y estar satisfecho con su estado.
    </p>

    <p><b class="clausula">SÉPTIMO.-</b> El presente contrato es IRREVOCABLE y no puede ser modificado sin el
        consentimiento por escrito
        de ambas partes.</p>

    <p><b class="clausula">OCTAVO.-</b> Cualquier disputa o controversia que surja en la relación con el presente
        contrato será resuelta
        mediante instancias judiciales.
    </p>

    <p>
        <b class="clausula">NOVENO.-</b> Los contratantes declaran que existe la más justa y perfecta equivalencia entre
        el precio
        pactado y el
        valor del bien mueble, no teniendo nada que reclamarse al respecto. En fe y señal de conformidad, las partes
        firman el presente contrato en <?php echo $resultSucursal['direccion'] ?? 'Jr. Ex Carretera Yurimaguas S/n'; ?>
        el día <?php echo $helpers->fechaLetras($resultVenta['fecha_hora']); ?>.
    </p>

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