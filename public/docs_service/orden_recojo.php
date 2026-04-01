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
$sqlActa = "SELECT * FROM documentacion WHERE idventa = $idVenta AND tipo = '3'";
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
$numeroContrato = "OR" . str_pad($resultActa['correlativo'], 9, '0', STR_PAD_LEFT);

// Generación PDF con mPDF (server-side)
ob_start();
$empresa = "SURCO MOTORS S.A.C.";
$ruc = "20601614082";
$direccion = "JR: JIMENEZ PIMENTEL Nº 886";

$cliente = "FABIANA DIAZ PAZ";
$dni = "74615224";

$vehiculo = "TRIMOTO DE PASAJEROS";
$marca = "WANXIN";
$modelo = "WX150-A";
$color = "AZUL";
$placa = "NUEVO";
$serie = "LDAPAK105TGD30726";

$fecha = date("d/m/Y");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Orden de Recojo</title>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 40px;
    font-size: 14px;
}

h1, h2, h3 {
    text-align: center;
    margin: 0;
}

.titulo {
    font-weight: bold;
    text-align: center;
    margin-top: 15px;
    margin-bottom: 20px;
}

.linea {
    border-bottom: 1px solid #000;
    display: inline-block;
    width: 300px;
}

.texto {
    text-align: justify;
    line-height: 1.6;
}

.condiciones {
    margin-top: 20px;
}

.firmas {
    margin-top: 60px;
    display: flex;
    justify-content: space-between;
    text-align: center;
}

.firma {
    width: 30%;
}

@media print {
    button {
        display: none;
    }
}
</style>
</head>

<body>

<h2><?php echo $empresa; ?></h2>
<p style="text-align:center;">ALQUILER VENTA DE VEHÍCULOS MOTORIZADOS</p>
<p style="text-align:center;">R.U.C. <?php echo $ruc; ?></p>
<p style="text-align:center;">OFICINA: <?php echo $direccion; ?></p>

<h3 class="titulo">ORDEN DE RECOJO DE LA MERCADERÍA</h3>

<p class="texto">
CLIENTE <?php echo $cliente; ?>, IDENTIFICADO DNI Nº <?php echo $dni; ?>, 
POR MEDIO DE LA PRESENTE HAGO ENTREGA DE LA(S) SIGUIENTE MERCADERÍA(S): 
CLASE <?php echo $vehiculo; ?>; MARCA <?php echo $marca; ?>; 
MODELO <?php echo $modelo; ?>; COLOR <?php echo $color; ?>; 
PLACA <?php echo $placa; ?>; SERIE <?php echo $serie; ?>. 
LA(S) MISMA(S) QUE QUEDARÁ(N) EN GARANTÍA HASTA LA CANCELACIÓN DE MI DEUDA 
EN UN PLAZO MÁXIMO DE 03 DÍAS.
</p>

<p class="texto">
CASO CONTRARIO NO TENDRÉ QUE RECLAMAR DE CONFORMIDAD CON LO ESTIPULADO EN LA CLÁUSULA 6 
DE NUESTRO CONTRATO, AUTORIZO DESDE YA A <?php echo $empresa; ?> DAR ESTRICTO CUMPLIMIENTO 
A LO SEÑALADO EN LA CLÁUSULA 6 DE LA ACOTADA RELACIÓN CONTRACTUAL.
</p>

<div class="condiciones">
<strong>CONDICIONES DEL VEHÍCULO:</strong><br><br>
_____________________________________________<br><br>
_____________________________________________<br><br>
_____________________________________________<br><br>
_____________________________________________<br>
</div>

<br><br>
<strong>FECHA:</strong> <?php echo $fecha; ?>

<div class="firmas">
    <div class="firma">
        _________________________<br>
        FIRMA Y SELLO DEL GERENTE GENERAL<br>
        ARRENDADOR - VENDEDOR
    </div>

    <div class="firma">
        _________________________<br>
        <?php echo $cliente; ?><br>
        ARRENDADOR - COMPRADOR
    </div>

    <div class="firma">
        _________________________<br>
        <?php echo $garante; ?><br>
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
$dompdf->stream('orden_recojo_' . $numeroContrato . '.pdf', array('Attachment' => 0));
exit;

?>
</html>