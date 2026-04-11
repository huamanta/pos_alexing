<?php
require "../../configuraciones/Conexion.php";
require "./Helpers.php";
$helpers = new Helpers();
date_default_timezone_set('America/Lima');
// Función para encriptar/desencriptar

// Obtener ID del venta desde parámetro encriptado
$idVenta = isset($_GET['idventa']) ? $helpers->encryptDecrypt('decrypt', $_GET['idventa']) : null;
if ($idVenta == null) {
    echo "ID de venta no proporcionado.";
    exit;
}


// DATOS DINÁMICOS (puedes traerlos de BD basado en $idVenta)
$sqlNegocio = "SELECT * 
FROM datos_negocio 
ORDER BY id_negocio ASC 
LIMIT 1";
$resultNegocio = ejecutarConsultaSimpleFila($sqlNegocio);

$sqlVenta = "SELECT v.*, ta.nombre AS nombre_tipo_acompanante,
             a.nombre AS nombre_acompanante, a.num_documento AS dni_acompanante, a.telefono AS telefono_acompanante,
             p.nombre AS nombre_cliente, p.num_documento AS num_documento_cliente, p.direccion AS direccion_cliente, p.telefono AS telefono_cliente,
             g.nombre AS nombre_garante, g.num_documento AS num_documento_garante, g.telefono AS telefono_garante, g.direccion AS direccion_garante
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
$dniAcompanante = $resultVenta['dni_acompanante'] ?? '';
$telefonoAcompanante = $resultVenta['telefono_acompanante'] ?? '';
$nombreTipoAcompanante = $resultVenta['nombre_tipo_acompanante'] ?? '';
$telefonoGarante = $resultVenta['telefono_garante'] ?? '';
$direccionGarante = $resultVenta['direccion_garante'] ?? '';

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

// Detalle del vehículo vendido
$sqlDetalle = "SELECT dv.*, p.nombre AS producto_nombre, m.nombre AS marca, mo.nombre AS modelo, p.color,
                       p.numserie AS serie, p.motor, p.anio_fabricacion AS anio, p.placa,
                       p.clase_vehiculo AS clase, p.tipo_vehiculo
                FROM detalle_venta dv
                LEFT JOIN producto_configuracion pg ON dv.idproducto = pg.id
                LEFT JOIN producto p ON p.idproducto = COALESCE(pg.idproducto, dv.idproducto)
                LEFT JOIN marca m ON m.idmarca = p.idmarca
                LEFT JOIN modelo mo ON mo.idmodelo = p.idmodelo
                WHERE dv.idventa = $idVenta";

$resultDetalle = ejecutarConsultaSimpleFila($sqlDetalle);
$marcaProducto = !empty($resultDetalle['marca']) ? $resultDetalle['marca'] : '__________';
$modeloProducto = !empty($resultDetalle['modelo']) ? $resultDetalle['modelo'] : '__________';
$serieProducto = !empty($resultDetalle['serie']) ? $resultDetalle['serie'] : '__________';
$colorProducto = !empty($resultDetalle['color']) ? $resultDetalle['color'] : '__________';
$motorProducto = !empty($resultDetalle['motor']) ? $resultDetalle['motor'] : '__________';
$anioProducto = !empty($resultDetalle['anio']) ? $resultDetalle['anio'] : '__________';
$placaProducto = !empty($resultDetalle['placa']) ? $resultDetalle['placa'] : '__________';

// Cuotas y fechas de inicio/fin
$sqlCuotas = "SELECT deuda, MIN(fechavencimiento) AS fecha_inicio_cuota, MAX(fechavencimiento) AS fecha_fin_cuota
              FROM cuentas_por_cobrar WHERE idventa = $idVenta";
$resultCuotas = ejecutarConsultaSimpleFila($sqlCuotas);
$montoCuota = !empty($resultCuotas['deuda']) ? $resultCuotas['deuda'] : 0;
$fechaInicio = !empty($resultCuotas['fecha_inicio_cuota']) ? $helpers->fechaLetras($resultCuotas['fecha_inicio_cuota']) : '__________';
$fechaFin = !empty($resultCuotas['fecha_fin_cuota']) ? $helpers->fechaLetras($resultCuotas['fecha_fin_cuota']) : '__________';

$dataFrecuencia = $helpers->getDataFrecuencia($resultVenta['frecuencia'] ?? '1');
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

// Hora dinámica de la venta
$hora = date('H:i', strtotime($resultVenta['fecha_hora']));

// Generación PDF con Dompdf (server-side)
ob_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Orden entrega</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 11px;
            margin: 40px;
            line-height: 1.4;
            color: #000;
        }

        <?php echo Helpers::getDocumentHeaderStyles(); ?>

        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 15px;
            font-size: 12px;
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

        .table-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th {
            background-color: #cccccc;
            border: 1px solid #ffffff;
            font-size: 9px;
            padding: 0;
        }

        .table td {
            border: 1px solid #ffffff;
            font-size: 9px;
            padding: 0;
        }

        .table tr:nth-child(even) {
            background-color: #e2efd9;
        }

        .firma {
            margin-top: 60px;
            width: 100%;
        }

        .firma div {
            width: 23%;
            display: inline-block;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <?php
    echo Helpers::renderDocumentHeader(
        $resultNegocio['nombre'] ?? '',
        $resultSucursal['ruc'] ?? '',
        'ACTA DE ENTREGA Y RECEPCION DE UN VEHICULO TRIMOTO DE PASAJEROS',
        $numeroContrato
    );
    ?>

    <p>
        Siendo las <strong><?php echo $hora; ?></strong> horas, del día
        <strong><?php echo $helpers->fechaLetras($resultVenta['fecha_hora']); ?></strong>, en las instalaciones de la
        empresa <strong>"<?php echo strtoupper($resultNegocio['nombre']); ?>"</strong>,
        con RUC Nº <strong><?php echo $resultSucursal['ruc']; ?></strong>,
        sito en
        <strong><?php echo strtoupper($resultSucursal['direccion'] ?? $resultNegocio['direccion'] ?? ''); ?></strong>,
        del distrito de <strong><?php echo strtoupper($resultSucursal['distrito'] ?? ''); ?></strong>,
        se reúnen de una parte el Gerente General señor
        <strong><?php echo strtoupper($resultNegocio['gerente'] ?? 'NERY MARLENY CONDORI CHIRINOS'); ?></strong>,
        identificado con DNI Nº <strong><?php echo $resultNegocio['dni_gerente'] ?? '43960822'; ?></strong>,
        con facultades inscrita en la partida electrónica N° del registro de personas jurídicas de la Oficina Registral
        <?php echo $resultSucursal['distrito'] ?? 'Tarapoto'; ?>; quien es propietario del bien mueble;
        y de la otra parte, el(la) arrendatario(a)
        <strong><?php echo strtoupper($comprador); ?></strong>, identificado con DNI Nº
        <strong><?php echo $dniComprador; ?></strong>, de estado civil soltero(a), con domicilio en
        <strong><?php echo strtoupper($direccionComprador); ?></strong> y con número de celular
        <strong><?php echo $celularComprador; ?></strong>
        <?php if (!empty($nombreAcompanante)): ?>
            y su <?php echo strtolower($nombreTipoAcompanante) ?: 'cónyuge'; ?> (el)la señor(a)
            <strong><?php echo strtoupper($nombreAcompanante); ?></strong>
            <?php if (!empty($dniAcompanante)): ?> identificado con DNI Nº
                <strong><?php echo $dniAcompanante; ?></strong><?php endif; ?>
            <?php if (!empty($telefonoAcompanante)): ?> y número de celular
                <strong><?php echo $telefonoAcompanante; ?></strong><?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($garante)): ?>
            , con su GARANTE el señor(a) <strong><?php echo strtoupper($garante); ?></strong> con DNI N°
            <strong><?php echo $dniGarante; ?></strong>
            <?php if (!empty($direccionGarante)): ?> domiciliado en
                <strong><?php echo strtoupper($direccionGarante); ?></strong><?php endif; ?>
            <?php if (!empty($telefonoGarante)): ?> con celular N°
                <strong><?php echo $telefonoGarante; ?></strong><?php endif; ?>
        <?php endif; ?>,
        la finalidad de suscribir el presente ACTA DE ENTREGA Y RECEPCIÓN DE VEHÍCULO TRIMOTO DE PASAJEROS; acto que se
        efectúa por el ALQUILER del bien mueble que se encuentra en condición de NUEVO destinado única y exclusivamente
        para su uso como transporte de pasajeros, el mismo que inicia a partir del
        <strong><?php echo $fechaInicio; ?></strong> y finaliza indefectiblemente el
        <strong><?php echo $fechaFin; ?></strong> sin necesidad de aviso previo con una cuota
        <?php echo $frecuenciaTexto; ?> de
        <strong><?php echo $helpers->monedaFormt($montoCuota, $currency); ?>
            (<?php echo $helpers->numeroALetrasMoneda($montoCuota, $currency); ?>)</strong>.
        Dicho vehículo cuenta con las siguientes características:
    </p>

    <p class="section-title">CARACTERÍSTICAS DEL VEHÍCULO:</p>
    <table class="table-info">
        <tr>
            <td>Clase</td>
            <td>:<?php echo strtoupper($resultDetalle['nombre_producto'] ?? 'TRIMOTO DE PASAJEROS'); ?></td>
            <td>COLOR</td>
            <td>:<?php echo strtoupper($colorProducto); ?></td>
        </tr>
        <tr>
            <td>Marca</td>
            <td>:<?php echo strtoupper($marcaProducto); ?></td>
            <td>Nº Serie</td>
            <td>:<?php echo strtoupper($serieProducto); ?></td>
        </tr>
        <tr>
            <td>Modelo</td>
            <td>:<?php echo strtoupper($modeloProducto); ?></td>
            <td>Nº Motor</td>
            <td>:<?php echo strtoupper($motorProducto); ?></td>
        </tr>
        <tr>
            <td>Año</td>
            <td>:<?php echo strtoupper($anioProducto); ?></td>
            <td>Placa</td>
            <td>:<?php echo strtoupper($placaProducto); ?></td>
        </tr>
    </table>
    <p>
        Que, el propietario otorga la posesión del vehículo TRIMOTO DE PASAJEROS descrito líneas arriba al señor(a)
        <strong><?php echo strtoupper($comprador); ?></strong>, quien declara recibir el vehículo TRIMOTO DE PASAJEROS a
        su entera satisfacción,
        el mismo que es NUEVO, y se encuentra en perfecto estado de conservación y funcionamiento. Asimismo, se obliga a
        conservar el bien mueble en el mismo estado en que es recibido hasta el último pago de su cuota diaria, salvo el
        deterioro
        por el uso del servicio; y, se compromete a no introducir mejoras, cambios o modificaciones internas y externas
        al vehículo
        trimovil arrendado, sin el consentimiento expreso y por escrito del propietario. También, se le hace entrega de
        todos los
        accesorios y documentos; lo cual queda tajantemente prohibido de realizar alguna modificación, asimismo, se
        compromete
        a su debida custodia. A continuación detallamos todos las partes mecánicas y estáticas; accesorios y documentos
        recibidos:

    </p>
    <p class="section-title">PARTES MECÁNICAS Y ESTÁTICAS</p>
    <div class="line"></div>
    <table class="table">
        <tr>
            <th>DESCRIPCION</th>
            <th>TIPO / MARCA</th>
            <th>ESTADO DE CONSERVACIÓN</th>
            <th>DESCRIPCION</th>
            <th>TIPO / MARCA</th>
            <th>ESTADO DE CONSERVACIÓN</th>
        </tr>
        <tr>
            <td><strong>ESTRUCTURA COMPLETA</strong></td>
            <td>nannanan</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
            <td><strong>CARBURADOR</strong></td>
            <td>Original: Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

        <tr>
            <td><strong>LLANTA DELANTERA</strong></td>
            <td>Original: Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
            <td><strong>TUBO ESCAPE</strong></td>
            <td>Original: Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

        <tr>
            <td><strong>LLANTA DERECHA</strong></td>
            <td>Original: Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
            <td><strong>TACÓMETRO</strong></td>
            <td>Original: Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

        <tr>
            <td><strong>LLANTA IZQUIERDA</strong></td>
            <td>Original: Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
            <td><strong>FARO</strong></td>
            <td>Original: Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>
        <tr>
            <td><strong>AMORTIGUADORES</strong></td>
            <td>Original: Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
            <td><strong>TAPIZ GENERAL</strong></td>
            <td>Original: Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

        <tr>
            <td><strong>TELESCOPIOS</strong></td>
            <td>Original: Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
            <td></td>
            <td></td>
            <td></td>
    </table>
    <div class="line"></div>
    <p class="section-title">ACCESORIOS</p>
    <div class="line"></div>
    <table class="table">
        <tr>
            <th><strong>ACCESORIOS</strong></th>
            <th>TIPO / MARCA</th>
            <th>ESTADO DE CONSERVACIÓN</th>

            <th><strong>ACCESORIOS</strong></th>
            <th>TIPO / MARCA</th>
            <th>ESTADO DE CONSERVACIÓN</th>
        </tr>

        <tr>
            <td><strong>ESPEJOS</strong></td>
            <td>-</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>

            <td><strong>TANQUE</strong></td>
            <td>Original Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

        <tr>
            <td><strong>TAPAS LATERALES</strong></td>
            <td>-</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>

            <td><strong>AROS</strong></td>
            <td>Original Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

        <tr>
            <td><strong>PARRILLA</strong></td>
            <td>-</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>

            <td><strong>BOCA MASA</strong></td>
            <td>Original Si[ ] No[ ]</td>
            <td>Operativo Si[ ] No[ ]</td>
        </tr>

        <tr>
            <td><strong>BUJÍA</strong></td>
            <td>-</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>

            <td><strong>LUCES LATERALES</strong></td>
            <td>Original Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

        <tr>
            <td><strong>CHUPÓN</strong></td>
            <td>Si</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>

            <td><strong>FRENO DELANTERO Y POSTERIOR</strong></td>
            <td>Original Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

        <tr>
            <td><strong>BATERÍA</strong></td>
            <td>Original Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>

            <td><strong>CINTURÓN DE SEGURIDAD</strong></td>
            <td>Original Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

        <tr>
            <td><strong>CATALINAS</strong></td>
            <td>-</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>

            <td><strong>PLACA</strong></td>
            <td>-</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

        <tr>
            <td><strong>CADENAS</strong></td>
            <td>-</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>

            <td><strong>LOGOTIPO POSTERIOR</strong></td>
            <td>Original Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

        <tr>
            <td><strong>PIÑÓN</strong></td>
            <td>-</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>

            <td><strong>RAYOS</strong></td>
            <td>Original Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

        <tr>
            <td><strong>ZAPATAS</strong></td>
            <td>-</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>

            <td><strong>CARPA</strong></td>
            <td>Original Si[ ] No[ ]</td>
            <td>Nuevo[ ] Semi-Nuevo[ ]</td>
        </tr>

    </table>
    <div class="line"></div>
    <p class="section-title">DOCUMENTACIÓN</p>
    <table>
        <tr>
            <td>
                <li>Copia de Tarjeta de Propiedad Legalizada</li>
            </td>
            <td>:<strong>SI[ ] NO[ ]</strong></td>
        </tr>
        <tr>
            <td>
                <li>SOAT vigente</li>
            </td>
            <td>:<strong>SI[ ] NO[ ]</strong></td>
        </tr>
        <tr>
            <td>
                <li>Llaves</li>
            </td>
            <td>:<strong>SI[ ] NO[ ]</strong></td>
        </tr>
        <tr>
            <td>
                <li>Permiso de circulación</li>
            </td>
            <td>:<strong>SI[ ] NO[ ]</strong></td>
        </tr>
    </table>

    <p>
        En caso de pérdida o robo de los documentos, asumiré la responsabilidad de todo el proceso y costo para la
        recuperación
        y/u obtención. Si el bien mueble es robado, asumo la responsabilidad de devolver el vehículo o cancelar la
        totalidad del valor del bien pactado entre las partes, en un lapso de 30 días calendarios.
        En caso de incumplimiento del presente acta, estoy obligado a entregar el vehículo trimovil personalmente en las
        instalaciones de la empresa <strong><?php echo strtoupper($resultNegocio['nombre']); ?></strong>, en las mismas
        condiciones que fue recibido y todos los
        documentos
        dados a mi custodia, con el simple requerimiento verbal o mediante carta notarial, asimismo, pagar en calidad de
        penalidad compensatorio un importe ascendente a <?php echo $helpers->monedaFormt(35, $currency); ?>
        (<?php echo $helpers->numeroALetrasMoneda(35, $currency); ?>), por cada día de demora
        en la
        entrega del vehículo trimovil. De igual forma, faculto a la empresa
        <strong><?php echo strtoupper($resultNegocio['nombre']); ?></strong>, en caso de
        incumplimiento,
        retener el vehículo donde se le encuentre ubicado o en su defecto tomar acciones legales frente a las instancias
        pertinentes, denunciándome por los delitos contra el patrimonio en cualquiera de sus modalidades en que hubiera
        incurrido.
    </p>

    <br>
    <div class="condiciones">
        <div style="width: 100%; margin-bottom: 4px;">
            <span style="font-weight: bold;">OBSERVACIONES:</span>
        </div>
        <div style="width: 100%; border-bottom: 1px dotted #000; height: 14px;"></div>
        <div style="width: 100%; border-bottom: 1px dotted #000; height: 20px;"></div>
    </div>
    <br>
    <p>
        No habiendo nada más que hacer constar, se da por concluida la entrega y recepción del vehículo TRIMOTO DE
        PASAJEROS a las <strong><?php echo $hora; ?></strong> horas del día
        <strong><?php echo $helpers->fechaLetras($resultVenta['fecha_hora']); ?></strong>
        firmando la presente acta en señal de conformidad, y para
        mayor veracidad se certifica notarialmente mi firma.
    </p>

    <br><br>

    <div class="firma">
        <div>_________________<br>GERENTE</div>
        <div>_________________<br><?php echo strtoupper($comprador); ?></div>
        <?php if (!empty($nombreAcompanante)): ?>
            <div>_________________<br><?php echo strtoupper($nombreAcompanante); ?></div>
        <?php else: ?>
            <div>_________________<br>PAREJA/CÓNYUGE</div>
        <?php endif; ?>
        <?php if (!empty($garante)): ?>
            <div>_________________<br><?php echo strtoupper($garante); ?></div>
        <?php else: ?>
            <div>_________________<br>GARANTE</div>
        <?php endif; ?>
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
$dompdf->stream('orden_entrega_' . $numeroContrato . '.pdf', array('Attachment' => 0));
exit;

?>

</html>