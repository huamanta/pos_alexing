<?php
declare(strict_types=1);
header("Content-type: text/html; charset=utf8");

require_once 'vendor/autoload.php';

use Greenter\Model\Response\StatusCdrResult;
use Greenter\Ws\Services\ConsultCdrService;
use Greenter\Ws\Services\SoapClient;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\Model\Response\CdrResponse;

$errorMsg = null;
$filename = null;

// Se reciben parámetros por GET (ejemplo)
$idVenta = $_GET['idventa'];
$codColab = $_GET['codColab'];

/**
 * Verifica campos requeridos.
 *
 * @param array<string, string> $items
 * @return bool
 */
function validateFields(array $items): bool {
    global $errorMsg;
    $required = ['rucSol', 'userSol', 'passSol', 'ruc', 'tipo', 'serie', 'numero'];
    foreach ($required as $key) {
        if (!isset($items[$key]) || empty($items[$key])) {
            $errorMsg = 'El campo ' . $key . ' es requerido';
            return false;
        }
    }
    return true;
}

/**
 * Crea el servicio de consulta CDR con credenciales.
 *
 * @param string|null $user
 * @param string|null $password
 * @return ConsultCdrService
 */
function getCdrStatusService(?string $user, ?string $password): ConsultCdrService {
    // Usar FE_PRODUCCION si ya estás en producción.
    $ws = new SoapClient(SunatEndpoints::FE_CONSULTA_CDR.'?wsdl');
    $ws->setCredentials($user, $password);
    $service = new ConsultCdrService();
    $service->setClient($ws);
    return $service;
}

/**
 * Guarda el CDR en la carpeta "files" (creándola si no existe).
 *
 * @param string $filename
 * @param string|null $content
 */
function savedFile(string $filename, ?string $content): void {
    $fileDir = __DIR__ . '/files/'; // Changed from /cdrs/ to /files/
    if (!file_exists($fileDir)) {
        mkdir($fileDir, 0777, true);
    }
    $pathZip = $fileDir . DIRECTORY_SEPARATOR . $filename;
    file_put_contents($pathZip, $content);
}

/**
 * Genera el nombre (sin prefijo "R-") basado en ruc, tipo, serie y correlativo formateado.
 *
 * @param array $fields
 * @return string
 */
function generateFilename(array $fields): string {
    // Formato: ruc-tipo-serie-correlativo.zip
    $correlativo = Correlativo($fields['numero']);
    return $fields['ruc'] . '-' . $fields['tipo'] . '-' . $fields['serie'] . '-' . $correlativo . '.zip';
}

/**
 * Formatea el número agregando ceros a la izquierda.
 *
 * @param string|int $Correlativo
 * @return string
 */
function Correlativo($Correlativo): string {
    $Correlativo = (string)$Correlativo;
    $length = strlen($Correlativo);
    switch ($length) {
        case 1: return '000000' . $Correlativo;
        case 2: return '00000' . $Correlativo;
        case 3: return '0000' . $Correlativo;
        case 4: return '000' . $Correlativo;
        case 5: return '00' . $Correlativo;
        case 6: return '0' . $Correlativo;
        default: return $Correlativo;
    }
}

/**
 * Llama al servicio de SUNAT y retorna un objeto StatusCdrResult.
 *
 * @param array<string,string> $fields
 * @return StatusCdrResult|null
 */
function process(array $fields): ?StatusCdrResult {
    if (!validateFields($fields)) {
        return null;
    }
    $service = getCdrStatusService($fields['rucSol'].$fields['userSol'], $fields['passSol']);

    // Consulta el CDR usando getStatusCdr
    $result = $service->getStatusCdr(
        $fields['ruc'],
        $fields['tipo'],
        $fields['serie'],
        (int)$fields['numero']
    );
    return $result;
}

//-----------------------------------------------------------------------------------------
// EJECUCIÓN PRINCIPAL

// Consulta de datos del negocio
$conexion = Util::getInstance()->abrirConexion();
$res = mysqli_query($conexion, "SELECT * FROM datos_negocio");
if ($res) {  
    foreach ($res as $column) {
        $ruc        = $column['documento'];
        $usuario    = $column['usuario_sol'];
        $contrasena = $column['clave_sol'];
    }
}

// Consulta de datos de la venta
$res = mysqli_query($conexion, "SELECT v.idventa AS id, v.tipo_comprobante AS tipoDoc, 
    v.num_comprobante AS numDoc, v.serie_comprobante AS serDoc
    FROM detalle_venta dv
    INNER JOIN venta v ON dv.idventa = v.idventa
    WHERE dv.idventa = '".$idVenta."'");
if ($res) {
    foreach ($res as $column) {
        $tipoDocVenta = $column['tipoDoc']; // 'Factura', 'Boleta', etc.
        $numeroDOC    = $column['numDoc'];
        $serieDOC     = $column['serDoc'];
        $IdDOV        = $column['id'];
    }
}

// Mapear el tipo de comprobante a SUNAT
if ($tipoDocVenta == 'Factura') {
    $tipo = '01';
} elseif ($tipoDocVenta == 'Boleta') {
    $tipo = '03';
} elseif ($tipoDocVenta == 'Nota') {
    echo "<script>window.location='/SistemaFAC/vistas/venta.php';</script>";
    exit;
}

// Preparar $fields para la consulta
$fields = [
    'rucSol'  => $ruc,
    'userSol' => $usuario,
    'passSol' => $contrasena,
    'ruc'     => $ruc,
    'tipo'    => $tipo,
    'serie'   => $serieDOC,
    'numero'  => $numeroDOC
];

// Se genera el nombre del archivo (prefijado con "R-")
$filenameBase = generateFilename($fields);
$filenameFull = 'R-' . $filenameBase;
$filePath = __DIR__ . '/cdrs/' . $filenameFull;

// Llamada al servicio de SUNAT
$result = process($fields);

//-----------------------------------------------------------------------------------------
// INTERPRETAR RESULTADO

// Caso 1: Consulta exitosa (isSuccess() == true)
if ($result && $result->isSuccess()) {
    if ($result->getCdrZip()) {
        savedFile($filenameFull, $result->getCdrZip());
    }
    mysqli_query($conexion, "UPDATE venta 
        SET dov_Estado='ACEPTADO', estado='Aceptado', dov_Nombre='".$filenameFull."', dov_IdEmpleado='".$codColab."' 
        WHERE idventa='".$IdDOV."'");
    echo "El Comprobante SÍ está en SUNAT (ACEPTADO).";
    exit;
}

// Caso 2: Si no esSuccess(), revisar error y cdrResponse
$error = $result ? $result->getError() : null;

if ($error) {
    // Manejo especial para error 0125
    if ($error->getCode() === "0125") {
        if (file_exists($filePath)) {
            mysqli_query($conexion, "UPDATE venta 
                SET dov_Estado='ACEPTADO', estado='Aceptado', dov_Nombre='".$filenameFull."', dov_IdEmpleado='".$codColab."' 
                WHERE idventa='".$IdDOV."'");
            echo "El Comprobante está informado a SUNAT (Error 0125, archivo CDR encontrado).";
            exit;
        } else {
            mysqli_query($conexion, "UPDATE venta 
                SET dov_Estado='ACEPTADO', estado='Aceptado', dov_Nombre='".$filenameFull."', dov_IdEmpleado='".$codColab."' 
                WHERE idventa='".$IdDOV."'");
            echo "El Comprobante está informado a SUNAT (Error 0125, actualizado manualmente).";
            exit;
        }
    }
    
    // Mostrar mayor detalle sobre el error
    $errCode = $error->getCode();
    $errMsg  = $error->getMessage();
    // Verifica si existe método getDetail para obtener más información
    $errDetail = (method_exists($error, 'getDetail') && $error->getDetail()) ? $error->getDetail() : 'No hay detalle adicional.';
    echo "Error al consultar SUNAT: [$errCode] $errMsg. Detalle: $errDetail";
    exit;
} else {
    // Caso: Sin error, revisar cdrResponse
    $cdr = $result->getCdrResponse();
    if ($cdr) {
        $code = $cdr->getCode();
        $desc = $cdr->getDescription();
        switch ($code) {
            case '98':
                echo "El comprobante está en proceso en SUNAT. Intente más tarde. Descripción: $desc";
                break;
            case '99':
                echo "El comprobante NO existe en SUNAT. Descripción: $desc";
                break;
            case '39':
                echo "El comprobante está anulado. Descripción: $desc";
                break;
            default:
                echo "SUNAT devolvió código: $code. Descripción: $desc";
                break;
        }
    } else {
        echo "No se obtuvo CDR ni error. Posible falla temporal de SUNAT.";
    }
}
?>