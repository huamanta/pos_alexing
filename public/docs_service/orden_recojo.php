<?php
require "../../configuraciones/Conexion.php";
require "./HelpersService.php";
$helpers = new HelpersService();
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


// Generación PDF con mPDF (server-side)
$garante = $resultVenta['nombre_garante'] ?? '';
$dniGarante = $resultVenta['num_documento_garante'] ?? '';
$fecha = $resultSucursal['distrito'] . ", " . $helpers->fechaLetras($resultVenta['fecha_hora']) ?? '';

// Detalle del vehículo vendido
$sqlDetalle = "SELECT dv.*, p.idproducto, p.nombre AS producto_nombre, m.nombre AS marca, mo.nombre AS modelo, ps.color,
                       ps.numero_serie AS serie, ps.numero_motor, ps.anio_fabricacion AS anio, ps.placa,
                       ps.clase_vehiculo AS clase, ps.tipo_vehiculo
                FROM detalle_venta dv
                LEFT JOIN producto p ON p.idproducto = dv.idproducto
                LEFT JOIN producto_configuracion pg ON dv.idproducto = pg.idproducto_configuracion
                INNER JOIN producto_serie ps ON ps.idproducto = p.idproducto
                LEFT JOIN marca m ON m.idmarca = p.idmarca
                LEFT JOIN modelo mo ON mo.idmodelo = p.idmodelo
                WHERE dv.idventa = $idVenta";
$resultDetalle = ejecutarConsultaSimpleFila($sqlDetalle);
$marcaProducto = !empty($resultDetalle['marca']) ? $resultDetalle['marca'] : '__________';
$modeloProducto = !empty($resultDetalle['modelo']) ? $resultDetalle['modelo'] : '__________';
$serieProducto = !empty($resultDetalle['serie']) ? $resultDetalle['serie'] : '__________';
$colorProducto = !empty($resultDetalle['color']) ? $resultDetalle['color'] : '__________';
$placaProducto = !empty($resultDetalle['placa']) ? $resultDetalle['placa'] : '__________';

// Cuotas y fechas de inicio/fin
$sqlCuotas = "SELECT
    (
        SELECT deuda
        FROM cuentas_por_cobrar
        WHERE idventa = $idVenta
        ORDER BY fechavencimiento
        LIMIT 1
    ) AS deuda,
    MIN(fechavencimiento) AS fecha_inicio_cuota,
    MAX(fechavencimiento) AS fecha_fin_cuota
FROM cuentas_por_cobrar
WHERE idventa = $idVenta";
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
    <title>Orden de Recojo</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 11px;
            margin: 40px;
            line-height: 1.4;
            color: #000;
        }

        .header {
            text-align: center;
        }

        .empresa {
            color: #5b8db8;
            font-weight: bold;
            font-size: 16px;
        }

        .subempresa {
            color: #7a7a7a;
            font-weight: bold;
            font-size: 15px;
        }

        .ruc {
            color: #7a7a7a;
            font-size: 13px;
        }

        .line {
            border-top: 1px solid #999;
            margin: 10px 0;
        }

        .titulo {
            font-weight: bold;
            text-align: center;
            font-size: 13px;
        }

        .numero {
            text-align: center;
            font-weight: bold;
            margin-bottom: 13px;
        }

        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 15px;
            font-size: 15px;
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

        .fecha-derecha {
            width: 100%;
            text-align: right;
            font-size: 12px;
            margin-top: 6px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="empresa"><?php echo strtoupper($resultNegocio['nombre'] ?? ''); ?></div>
        <div class="subempresa">ALQUILER VENTA DE VEHÍCULOS MOTORIZADOS</div>
        <div class="ruc">R.U.C. <?php echo $resultSucursal['ruc']; ?></div>

        <div class="line"></div>
        <br>
        <div class="titulo section-title">ORDEN DE RECOJO DE LA MERCADERÍAS</div>
    </div>
    <br>

    <p class="texto">
        CLIENTE <strong><?php echo strtoupper($comprador); ?></strong>, IDENTIFICADO CON DNI N°
        <strong><?php echo $dniComprador ?: '__________'; ?></strong>, POR MEDIO DE LA PRESENTE HAGO ENTREGA
        DE LA(S) SIGUIENTE(S) MERCADERIA(S): CLASE
        <strong><?php echo strtoupper($resultDetalle['producto_nombre'] ?? 'TRIMOTO DE PASAJEROS'); ?></strong>;
        MARCA <strong><?php echo strtoupper($marcaProducto); ?></strong>;
        MODELO <strong><?php echo strtoupper($modeloProducto); ?></strong>;
        COLOR <strong><?php echo strtoupper($colorProducto); ?></strong>;
        PLACA <strong><?php echo strtoupper($placaProducto); ?></strong>;
        SERIE <strong><?php echo strtoupper($serieProducto); ?></strong>. LA(S) MISMA(S) QUE QUEDARA(N)
        EN GARANTIA HASTA LA CANCELACION DE MI DEUDA EN UN PLAZO MAXIMO DE <strong>03 DIAS</strong>.
    </p>
    <p class="texto">
        CASO CONTRARIO NO TENDRE QUE RECLAMAR DE CONFORMIDAD CON LO ESTIPULADO EN LA CLAUSULA 7
        DE NUESTRO CONTRATO, AUTORIZO DESDE YA A
        <strong><?php echo strtoupper($resultNegocio['nombre'] ?? ''); ?></strong> A DAR ESTRICTO CUMPLIMIENTO A LO
        SENALADO EN LA CLAUSULA 7 DE LA ACOTADA RELACION CONTRACTUAL, ESTO ES LUEGO DE PRODUCIDO EL
        RECOJO DEL PRODUCTO.
    </p>

    <br>
    <div class="condiciones">
        <div style="width: 100%; margin-bottom: 4px;">
            <span style="font-weight: bold;">CONDICIONES DEL VEHÍCULO:</span>
            <span
                style="display: inline-block; width: 72%; border-bottom: 1px dotted #000; height: 14px; vertical-align: middle;"></span>
        </div>
        <div style="width: 100%; border-bottom: 1px dotted #000; height: 14px;"></div>
        <div style="width: 100%; border-bottom: 1px dotted #000; height: 20px;"></div>
        <div style="width: 100%; border-bottom: 1px dotted #000; height: 20px;"></div>
    </div>
    <br>
    <table style="width: 100%; border-collapse: collapse; margin-top: 6px;">
        <tr>
            <td style="text-align: right;">
                <table style="border-collapse: collapse; margin-left: auto; margin-right: 0;">
                    <tr>
                        <td>FECHA:........................................................................</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

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
require_once __DIR__ . '/../../vendor/autoload.php';

$options = new \Dompdf\Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new \Dompdf\Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('orden_recojo_' . $numeroContrato . '.pdf', array('Attachment' => 0));
exit;

?>

</html>