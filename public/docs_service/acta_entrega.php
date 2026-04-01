<?php
require "../../configuraciones/Conexion.php";
date_default_timezone_set('America/Lima');
// Función para encriptar/desencriptar
function encrypt_decrypt($action, $string)
{
    if ($action == 'encrypt') {
        $output = base64_encode($string);
        $output = str_replace(['=', '/', '+'], ['', '_', '-'], $output);
        return $output;
    } else if ($action == 'decrypt') {
        $string = str_replace(['_', '-'], ['/', '+'], $string);
        $mod4 = strlen($string) % 4;
        if ($mod4) {
            $string .= substr('====', $mod4);
        }
        return base64_decode($string);
    }
    return false;
}

// Obtener ID del venta desde parámetro encriptado
$idVenta = isset($_GET['idventa']) ? encrypt_decrypt('decrypt', $_GET['idventa']) : null;
if ($idVenta == null) {
    echo "ID de venta no proporcionado.";
    exit;
}

// buscar actaentrega
$sqlActa = "SELECT * FROM documentacion WHERE idventa = $idVenta AND tipo = '2'";
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


// DATOS DINÁMICOS (puedes traerlos de BD basado en $idVenta)
$sqlNegocio = "SELECT * 
FROM datos_negocio 
ORDER BY id_negocio ASC 
LIMIT 1";
$resultNegocio = ejecutarConsultaSimpleFila($sqlNegocio);

$sqlVenta = "SELECT * FROM venta v 
             INNER JOIN persona p ON v.idcliente = p.idpersona 
             WHERE v.idventa = $idVenta";
$resultVenta = ejecutarConsultaSimpleFila($sqlVenta);
$numeroContrato = "AE" . str_pad($resultActa['correlativo'], 9, '0', STR_PAD_LEFT);

// Generación PDF con mPDF (server-side)
ob_start();

$fecha = "24 de Marzo del 2026";
$hora = "18:39";

$arrendatario = "FABIANA DIAZ PAZ";
$dni_arrendatario = "74615224";
$direccion = "JR. JOSE CARLOS MARIATEGUI 139";
$celular = "961518855";

$garante = "JOKABE PAZ ROMERO";
$dni_garante = "00933140";

$marca = "WANXIN";
$modelo = "WX150-A";
$color = "AZUL";
$serie = "LDAPAK105TGD30726";
$motor = "WX162FMJ226J30726";
$anio = "2026";
$placa = "NUEVO";

$cuota = "619.00";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Acta de entrega</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 40px;
        }

        h1,
        h2,
        h3 {
            text-align: center;
            margin: 5px;
        }

        .section {
            margin-top: 15px;
            text-align: justify;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table td,
        .table th {
            border: 1px solid #000;
            padding: 5px;
        }

        .firmas {
            margin-top: 60px;
            width: 100%;
        }

        .firma {
            width: 30%;
            display: inline-block;
            text-align: center;
        }

        .linea {
            margin-top: 60px;
            border-top: 1px solid #000;
        }
    </style>

</head>

<body>

    <h2>SURCO MOTORS S.A.C.</h2>
    <h3>ALQUILER VENTA DE VEHÍCULOS MOTORIZADOS</h3>
    <h3>R.U.C. 20601614082</h3>
    <p style="text-align:center;">JR. JIMENEZ PIMENTEL Nº 886</p>

    <h3>ACTA DE ENTREGA Y RECEPCIÓN DE VEHÍCULO</h3>

    <div class="section">
        Siendo las <b><?= $hora ?></b> horas del día <b><?= $fecha ?></b>, se reúnen las partes:

        <br><br>

        El arrendador y el arrendatario <b><?= $arrendatario ?></b>, identificado con DNI Nº
        <b><?= $dni_arrendatario ?></b>, con domicilio en <b><?= $direccion ?></b>, celular
        <b><?= $celular ?></b>; y su garante <b><?= $garante ?></b> con DNI Nº
        <b><?= $dni_garante ?></b>.

        <br><br>

        Se realiza la entrega de un vehículo con las siguientes características:
    </div>

    <table class="table">
        <tr>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Color</th>
            <th>Serie</th>
            <th>Motor</th>
            <th>Año</th>
            <th>Placa</th>
        </tr>
        <tr>
            <td><?= $marca ?></td>
            <td><?= $modelo ?></td>
            <td><?= $color ?></td>
            <td><?= $serie ?></td>
            <td><?= $motor ?></td>
            <td><?= $anio ?></td>
            <td><?= $placa ?></td>
        </tr>
    </table>

    <div class="section">
        El vehículo se entrega en perfectas condiciones y se establece una cuota mensual de
        <b>S/ <?= $cuota ?></b> soles.

        <br><br>

        El arrendatario se compromete a conservar el vehículo en buen estado, no realizar
        modificaciones sin autorización y devolverlo en las mismas condiciones.

        <br><br>

        En caso de incumplimiento, se aplicará una penalidad de S/ 35.00 por día de retraso.
    </div>

    <div class="section">
        <b>OBSERVACIONES:</b>
        <br><br>
        ______________________________________________________________
        <br><br>
        ______________________________________________________________
    </div>

    <div class="firmas">

        <div class="firma">
            <div class="linea"></div>
            GERENTE GENERAL
        </div>

        <div class="firma">
            <div class="linea"></div>
            <?= $arrendatario ?><br>
            ARRENDATARIO
        </div>

        <div class="firma">
            <div class="linea"></div>
            <?= $garante ?><br>
            GARANTE
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
$dompdf->stream('orden_entrega_' . $numeroContrato . '.pdf', array('Attachment' => 0));
exit;

?>

</html>