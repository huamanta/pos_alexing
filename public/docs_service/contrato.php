<?php
require "../../configuraciones/Conexion.php";
date_default_timezone_set('America/Lima');
// Función para encriptar/desencriptar
function encrypt_decrypt($action, $string) {
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

// Obtener ID del contrato desde parámetro encriptado
$idVenta = isset($_GET['contrato']) ? encrypt_decrypt('decrypt', $_GET['contrato']) : null;
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
$numeroContrato = "C" . str_pad($resultVenta['idventa'], 9, '0', STR_PAD_LEFT);

$sqlSucursal = 'SELECT * FROM sucursal s INNER JOIN empresas e ON s.idempresa = e.idempresa WHERE s.idsucursal = ' . $resultVenta['idsucursal'];
$resultSucursal = ejecutarConsultaSimpleFila($sqlSucursal);

$fecha = "Tarapoto, 24 de Marzo del 2026";

$comprador = $resultVenta['nombre'];

// Generación PDF con mPDF (server-side)
ob_start();

$dniComprador = $resultVenta['num_documento'];
$direccionComprador = $resultVenta['direccion'];
$celularComprador = $resultVenta['telefono'];

$garante = "JOKABE PAZ ROMERO";
$dniGarante = "00933140";

$vehiculo = [
    "marca" => "WANXIN",
    "clase" => "L5",
    "combustible" => "GASOLINA",
    "carroceria" => "TRIMOTO DE PASAJEROS",
    "color" => "AZUL",
    "motor" => "WX162FMJ226J30726",
    "serie" => "LDAPAK105TGD30726",
    "anio" => "2026",
    "placa" => "NUEVO"
];

$total = $resultVenta['total_venta'];
$inicial = $resultVenta['totalrecibido'];
$cuota = "619.00";
$frecuencia = $resultVenta['frecuencia'];
switch ($frecuencia) {
    case '1':
        $frecuenciaTexto = "diarias";
        $frecuenciaSm = "dias";
        break;
    case '2':
        $frecuenciaTexto = "semanales";
        $frecuenciaSm = "semanas";
        break;
    case '3':
        $frecuenciaTexto = "quincenales";
        $frecuenciaSm = "quincenas";
        break;
    case '4':
        $frecuenciaTexto = "mensuales";
        $frecuenciaSm = "meses";
        break;
    case "5":
        $frecuenciaTexto = "bimestrales";
        $frecuenciaSm = "bimestres";
        break;
    case "6":
        $frecuenciaTexto = "trimestrales";
        $frecuenciaSm = "trimestres";
        break;
    case '7':
        $frecuenciaTexto = "semestrales";
        $frecuenciaSm = "semestres";
        break;
    case "8":
        $frecuenciaTexto = "anuales";
        $frecuenciaSm = "años";
        break;
    default:
        $frecuenciaTexto = "mensuales";
        $frecuenciaSm = "meses";
}
$meses = $resultVenta['meses'];
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

h2, h3 {
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
Conste por el presente documento el contrato de compra-venta a plazos con reserva de propiedad que celebran de una parte la empresa <b><?php echo $resultNegocio['nombre']; ?></b>, y de la otra parte el señor(a) <b><?php echo $comprador; ?></b>, identificado con DNI N° <?php echo $dniComprador; ?>, con domicilio en <?php echo $direccionComprador; ?>, celular <?php echo $celularComprador; ?>, con garante <?php echo $garante; ?> con DNI N° <?php echo $dniGarante; ?>.
</p>

<p class="section-title">PRIMERA: ANTECEDENTES</p>
<p>El vendedor es propietario del siguiente vehículo:</p>

<ul>
    <li>Marca: <?php echo $vehiculo['marca']; ?></li>
    <li>Clase: <?php echo $vehiculo['clase']; ?></li>
    <li>Combustible: <?php echo $vehiculo['combustible']; ?></li>
    <li>Carrocería: <?php echo $vehiculo['carroceria']; ?></li>
    <li>Color: <?php echo $vehiculo['color']; ?></li>
    <li>Motor: <?php echo $vehiculo['motor']; ?></li>
    <li>Serie: <?php echo $vehiculo['serie']; ?></li>
    <li>Año: <?php echo $vehiculo['anio']; ?></li>
    <li>Placa: <?php echo $vehiculo['placa']; ?></li>
</ul>

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
El valor total del vehículo es de S/ <?php echo $total; ?>, con una cuota inicial de S/ <?php echo $inicial; ?> y <?php echo $meses; ?> cuotas <?php echo $frecuenciaTexto; ?> de S/ <?php echo $cuota; ?>.
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
    $dompdf->stream('contrato_'.$numeroContrato.'.pdf', array('Attachment' => 0));
    exit;

?>
</html>