<?php
require "../../configuraciones/Conexion.php";
require "./HelpersService.php";
$helpers = new  HelpersService();
date_default_timezone_set('America/Lima');

// Obtener ID del venta desde parámetro encriptado
$idVenta = isset($_GET['idventa']) ? $helpers->encryptDecrypt('decrypt', $_GET['idventa']) : null;
if ($idVenta == null) {
    echo "ID de venta no proporcionado.";
    exit;
}

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
             p.nombre AS nombre_cliente, p.num_documento AS num_documento_cliente, p.direccion AS direccion_cliente, p.telefono AS telefono_cliente, p.email AS correo_cliente,
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
$telefonoComprador = $resultVenta['telefono_cliente'] ?? '';
$correoComprador = $resultVenta['correo_cliente'] ?? '';
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
$resultDetalle = ejecutarConsulta($sqlDetalle);

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

$cuenta_cobrar = "SELECT * FROM cuentas_por_cobrar WHERE idventa = $idVenta";
$resultCuentaCobrar = ejecutarConsulta($cuenta_cobrar);

ob_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Cronograma de Pagos</title>
    <style>
        @page {
            margin: 10mm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            color: #000;
        }

        <?php echo HelpersService::getDocumentHeaderStyles(); ?>

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
            font-size: 12px;
            padding: 2px;
        }

        .table td {
            border: 1px solid #ffffff;
            font-size: 12px;
            padding: 2px;
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

        .totales {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .totales th {
            border: 1px solid #c9c9c9;
            font-size: 12px;
            padding: 2px;
        }

        .totales td {
            border: 1px solid #c9c9c9;
            font-size: 12px;
            padding: 2px;
        }

        .table-footer {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table-footer th {
            border: 1px solid #c9c9c9;
            font-size: 12px;
            padding: 2px;
        }

        .table-footer td {
            border: 1px solid #c9c9c9;
            font-size: 12px;
            padding: 2px;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- HEADER -->
        <?php
        echo HelpersService::renderDocumentHeader(
            $resultNegocio['nombre'] ?? '',
            $resultSucursal['ruc'] ?? '',
            'CRONOGRAMA DE PAGOS'
        );
        ?>
        <br>
        <div class="header" style="margin-top: -8px;">
            <div class="subempresa">CONTRATO <?php echo $numeroContrato ?? 'N/A'; ?></div>
            <div class="subempresa">Generado: <?php echo date('d/m/Y H:i:s'); ?></div>
        </div>

        <!-- CLIENTE -->
        <div class="info">
            <table class="table-info">
                <tr>
                    <td><strong>Cliente:</strong></td>
                    <td><?php echo $comprador ?? 'N/A'; ?></td>
                </tr>
                <tr>
                    <td><strong>DNI/RUC:</strong></td>
                    <td><?php echo $dniComprador ?? 'N/A'; ?></td>
                </tr>
                <tr>
                    <td><strong>Teléfono:</strong></td>
                    <td><?php echo $telefonoComprador ?? 'N/A'; ?></td>
                </tr>
                <tr>
                    <td><strong>Correo:</strong></td>
                    <td><?php echo $correoComprador ?? 'N/A'; ?></td>
                </tr>
                <tr>
                    <td><strong>Dirección:</strong></td>
                    <td><?php echo $direccionComprador ?? 'N/A'; ?></td>
                </tr>

            </table>
            <table class="table-info">
                <tr>
                    <td><strong>Monto Total:</strong>
                        <?php echo $helpers->monedaFormt($resultVenta['total_venta'] ?? 0, $currency); ?>
                    </td>
                    <td><strong>Inicial:</strong>
                        <?php echo $helpers->monedaFormt(($resultVenta['totalrecibido'] ?? 0) + ($resultVenta['totaldeposito'] ?? 0), $currency); ?>
                    </td>
                    <td><strong>Capital:</strong> <?php echo $helpers->monedaFormt(0, $currency); ?>
                    </td>
                </tr>
            </table>
            <br>
        </div>

        <!-- PRODUCTO -->
        <div class="section-title">Detalle del Producto</div>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Placa</th>
                    <th>Color</th>
                    <th>Serie</th>
                    <th>Motor</th>
                    <th>Condición</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $contador = 1;
                while ($row = mysqli_fetch_assoc($resultDetalle)) {
                    echo '<tr>';
                    echo '<td>' . $contador++ . '</td>';
                    echo '<td>' . ($row['nombre_producto'] ?? '-') . '</td>';
                    echo '<td>' . ($row['marca'] ?? '-') . '</td>';
                    echo '<td>' . ($row['modelo'] ?? '-') . '</td>';
                    echo '<td>' . ($row['placa'] ?? '-') . '</td>';
                    echo '<td>' . ($row['color'] ?? '-') . '</td>';
                    echo '<td>' . ($row['serie'] ?? '-') . '</td>';
                    echo '<td>' . ($row['motor'] ?? '-') . '</td>';
                    echo '<td>' . ($row['condicion'] ?? 'NUEVO') . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- CRONOGRAMA -->
        <div class="section-title">Cronograma</div>

        <table class="table">
            <tr>
                <th>N°</th>
                <th>Tipo</th>
                <th>Fec. Venc.</th>
                <th>Fec. Pago</th>
                <th>Tipo Pago</th>
                <th>Cuota</th>
                <th>Moras</th>
                <th>Total</th>
                <th>Pagado</th>
            </tr>
            <tbody>
                <?php
                $contador = 1;
                $hoy = date('Y-m-d');

                $cantidadLetrasAtrasadas = 0;
                $montoAtrasado = 0;
                $cantidadLetrasPendientes = 0;
                $montoTotalPendiente = 0;

                $montoPagado = 0;
                $interesPagado = 0;
                $descuentoPagado = 0;

                $saldoMonto = 0;
                $saldoInteres = 0;
                $saldoDescuento = 0;

                while ($row = mysqli_fetch_assoc($resultCuentaCobrar)) {
                    $deuda = floatval($row['deuda'] ?? 0);
                    $mora = floatval($row['mora'] ?? 0);
                    $abonototal = floatval($row['abonototal'] ?? 0);
                    $totalFilaPendiente = max($deuda + $mora, 0);

                    $montoPagado += $abonototal;
                    $saldoMonto += $deuda;
                    $saldoInteres += $mora;

                    if ($deuda > 0) {
                        $cantidadLetrasPendientes++;
                        $montoTotalPendiente += $totalFilaPendiente;

                        $fechaVenc = $row['fechavencimiento'] ?? null;
                        if (!empty($fechaVenc) && strtotime($fechaVenc) < strtotime($hoy)) {
                            $cantidadLetrasAtrasadas++;
                            $montoAtrasado += $totalFilaPendiente;
                        }
                    }

                    echo '<tr>';
                    echo '<td>' . $contador++ . '</td>';
                    echo '<td>LETRA</td>';
                    echo '<td>' . ($row['fechavencimiento'] ?? '-') . '</td>';
                    echo '<td>' . ($row['fecha_hora'] ?? '-') . '</td>';
                    echo '<td>-</td>';
                    echo '<td>' . $helpers->monedaFormt($deuda, $currency) . '</td>';
                    echo '<td>' . $helpers->monedaFormt($mora, $currency) . '</td>';
                    echo '<td>' . $helpers->monedaFormt($totalFilaPendiente, $currency) . '</td>';
                    echo '<td>' . $helpers->monedaFormt($abonototal, $currency) . '</td>';
                    echo '</tr>';
                }

                $totalPagado = $montoPagado + $interesPagado - $descuentoPagado;
                $totalSaldo = $saldoMonto + $saldoInteres - $saldoDescuento;
                ?>
            </tbody>
        </table>

        <!-- TOTALES -->
        <table class="totales">
            <tr>
                <td><strong>Letras atrasadas:</strong> <?php echo number_format($cantidadLetrasAtrasadas, 0); ?></td>
                <td><strong>Monto atrasado:</strong>
                    <?php echo $helpers->monedaFormt($montoAtrasado, $currency); ?></td>
                <td><strong>Letras pendientes:</strong> <?php echo number_format($cantidadLetrasPendientes, 0); ?></td>
                <td><strong>Total pendiente:</strong>
                    <?php echo $helpers->monedaFormt($montoTotalPendiente, $currency); ?></td>
            </tr>
        </table>

        <!-- RESUMEN -->
        <br>
        <table class="table-footer">
            <tr>
                <th></th>
                <th>MONTO</th>
                <th>INTERES</th>
                <th>DESCUENTO</th>
                <th>TOTAL</th>
            </tr>
            <tr>
                <td><strong>Monto Pagado:</strong></td>
                <td><?php echo $helpers->monedaFormt($montoPagado, $currency); ?></td>
                <td><?php echo $helpers->monedaFormt($interesPagado, $currency); ?></td>
                <td><?php echo $helpers->monedaFormt($descuentoPagado, $currency); ?></td>
                <td><?php echo $helpers->monedaFormt($totalPagado, $currency); ?></td>
            </tr>
            <tr>
                <td><strong>Saldo x pagar:</strong></td>
                <td><?php echo $helpers->monedaFormt($saldoMonto, $currency); ?></td>
                <td><?php echo $helpers->monedaFormt($saldoInteres, $currency); ?></td>
                <td><?php echo $helpers->monedaFormt($saldoDescuento, $currency); ?></td>
                <td><?php echo $helpers->monedaFormt($totalSaldo, $currency); ?></td>
            </tr>
        </table>

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
$dompdf->stream('cronograma_pagos_' . $numeroContrato . '.pdf', array('Attachment' => 0));
exit;

?>

</html>