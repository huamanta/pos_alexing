<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Util.php';
require_once __DIR__ . '/SunatGenerateToken.php';
require_once __DIR__ . '/Consultas.php';
require_once __DIR__ . '/Operaciones.php';

function writeLog(array $data)
{
    $path = __DIR__ . '/guia_remision.log';
    $entry = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    @file_put_contents($path, $entry, FILE_APPEND | LOCK_EX);
}

$input = $_POST;
$idguia = isset($input['idguia']) ? trim($input['idguia']) : '';
if ($idguia === '') {
    echo json_encode(['success' => false, 'message' => 'No se recibió el id de la guía.']);
    exit;
}

writeLog(['event' => 'consultar_ticket inicio', 'idguia' => $idguia, 'post_data' => $input]);

$util = Util::getInstance();
$conexion = $util->abrirConexion();
if (!$conexion) {
    echo json_encode(['success' => false, 'message' => 'No se pudo conectar a la base de datos.']);
    exit;
}
mysqli_set_charset($conexion, "utf8mb4");

$sql = "SELECT ticket_sunat, serie_comprobante, num_comprobante, resumen_sunat FROM guia_remision WHERE idguia = '" . mysqli_real_escape_string($conexion, $idguia) . "' LIMIT 1";
writeLog(['event' => 'consultar_ticket SQL', 'sql' => $sql]);

$guiaResult = mysqli_query($conexion, $sql);
$guia = $guiaResult ? $guiaResult->fetch_assoc() : null;

if (!$guia) {
    writeLog(['event' => 'guia not found', 'idguia' => $idguia, 'sql_error' => mysqli_error($conexion)]);
    mysqli_close($conexion);
    echo json_encode(['success' => false, 'message' => 'No existe esta guía en la base de datos.', 'debug' => ['idguia' => $idguia]]);
    exit;
}

writeLog(['event' => 'guia encontrada', 'guia' => $guia]);

$emisorRuc = '';
$datosNegocioResult = mysqli_query($conexion, "SELECT documento FROM datos_negocio LIMIT 1");
if ($datosNegocioResult) {
    $datosNegocioRow = $datosNegocioResult->fetch_assoc();
    $emisorRuc = trim($datosNegocioRow['documento'] ?? '');
}
writeLog(['event' => 'datos_negocio obtenido', 'emisor_ruc' => $emisorRuc]);

$ticket = trim($guia['ticket_sunat'] ?? '');

// Si no hay ticket guardado, intenta extraerlo del resumen
if (empty($ticket) && !empty($guia['resumen_sunat'])) {
    if (preg_match('/Ticket:\s*([a-zA-Z0-9\-]+)/', $guia['resumen_sunat'], $matches)) {
        $ticket = $matches[1];
    }
}

if (empty($ticket)) {
    mysqli_close($conexion);
    echo json_encode(['success' => false, 'message' => 'No existe ticket SUNAT para esta guía. Primero envíela a SUNAT.']);
    exit;
}

$tokenGenerator = new SunatGenerateToken([]);
$tokenResult = $tokenGenerator->generateSunatToken();
if (!$tokenResult['success'] || empty($tokenResult['response']['access_token'])) {
    mysqli_close($conexion);
    echo json_encode(['success' => false, 'message' => 'No se pudo obtener token SUNAT.', 'data' => $tokenResult['response']]);
    exit;
}

$token = $tokenResult['response']['access_token'];
$consultas = new Consultas();
writeLog([
    'timestamp' => date('c'),
    'event' => 'ConsultaTicket request',
    'idguia' => $idguia,
    'ticket' => $ticket,
    'token_present' => !empty($token)
]);
$ticketResult = $consultas->ConsultaTicket($token, $ticket);
writeLog([
    'timestamp' => date('c'),
    'event' => 'ConsultaTicket response',
    'idguia' => $idguia,
    'ticket' => $ticket,
    'response' => $ticketResult
]);

if ($ticketResult === false || (is_array($ticketResult) && isset($ticketResult['success']) && $ticketResult['success'] === false)) {
    mysqli_close($conexion);
    $errorData = is_array($ticketResult) && isset($ticketResult['data']) ? $ticketResult['data'] : null;
    $message = 'Error al consultar el ticket SUNAT.';
    if (is_array($errorData) && isset($errorData['error'])) {
        $message = $errorData['error'];
        if (isset($errorData['message'])) {
            $message .= ' - ' . $errorData['message'];
        }
    }
    writeLog([
        'timestamp' => date('c'),
        'event' => 'ConsultaTicket error',
        'idguia' => $idguia,
        'ticket' => $ticket,
        'message' => $message,
        'errorData' => $errorData
    ]);
    echo json_encode(['success' => false, 'message' => $message, 'data' => $errorData]);
    exit;
}

// Log the full response for debugging
writeLog([
    'timestamp' => date('c'),
    'event' => 'ConsultaTicket full response',
    'response_full' => $ticketResult
]);

// Parse SUNAT response using correct field structure
$codRespuesta = null;  // '0'=OK, '98'=PROCESSING, '99'=ERROR, '1033'=ALREADY SENT
$numTicket = null;
$desError = '';
$numError = null;

// Extract ticket if returned
if (isset($ticketResult['numTicket'])) {
    $numTicket = trim((string)$ticketResult['numTicket']);
}

// Extract response code
if (isset($ticketResult['codRespuesta'])) {
    $codRespuesta = trim((string)$ticketResult['codRespuesta']);
}

// Extract error information if present
if (isset($ticketResult['error']) && is_array($ticketResult['error'])) {
    if (isset($ticketResult['error']['desError'])) {
        $desError = trim((string)$ticketResult['error']['desError']);
    }
    if (isset($ticketResult['error']['numError'])) {
        $numError = trim((string)$ticketResult['error']['numError']);
    }
}

// Map SUNAT code to status string
$status = null;
if ($codRespuesta === '0') {
    $status = 'ACEPTADO';
} elseif ($codRespuesta === '98') {
    $status = 'PROCESANDO';
} elseif ($codRespuesta === '99') {
    $status = 'RECHAZADO';
} elseif ($numError === '1033') {
    $status = 'ACEPTADO';  // Already sent before
}

$message = $desError;

// Simple status check using exact SUNAT codes
$isAccepted = ($status === 'ACEPTADO');
$isProcessing = ($status === 'PROCESANDO');
$isRejected = ($status === 'RECHAZADO');

// Map to our database estado_sunat values: 1=Aceptado, 2=Procesando, 3=Rechazado
$estado_sunat = '2';  // Default: processing
$estado = '';

if ($isAccepted) {
    $estado_sunat = '1';
    $estado = 'Aceptado';
} elseif ($isProcessing) {
    $estado_sunat = '2';
} elseif ($isRejected) {
    $estado_sunat = '3';
    $estado = 'Rechazado';
}

// Build summary for database
$summary = '';
if (!empty($message)) {
    $summary = $message;
}
if ($status !== null) {
    $summary = trim(($summary !== '' ? $summary . ' | ' : '') . 'Estado: ' . $status);
}
// If nothing, at least record when it was checked
if (empty($summary)) {
    $summary = 'Consulta SUNAT: ' . date('Y-m-d H:i:s');
}
// Special case: code 1033 already sent
if ($numError === '1033') {
    $summary = 'Guía ya fue registrada en SUNAT previamente';
}

$cdr_path = '';
if ($isAccepted && isset($ticketResult['arcCdr']) && !empty($ticketResult['arcCdr'])) {
    try {
        $cdrBase64 = $ticketResult['arcCdr'];
        $ruc = $emisorRuc ?: '';
        $serie = isset($guia['serie_comprobante']) ? trim($guia['serie_comprobante']) : '';
        $correlativo = isset($guia['num_comprobante']) ? str_pad($guia['num_comprobante'], 8, '0', STR_PAD_LEFT) : '';
        $fileName = $ruc . '-09-' . $serie . '-' . $correlativo;
        $operaciones = new Operaciones();
        $generatedFile = $operaciones->ConvertirBase64_Zip($cdrBase64, $fileName);
        if ($generatedFile !== false) {
            $cdr_path = basename($generatedFile);
            writeLog([
                'timestamp' => date('c'),
                'event' => 'CDR saved',
                'idguia' => $idguia,
                'filename' => $cdr_path,
                'path' => $generatedFile,
            ]);
        } else {
            writeLog([
                'timestamp' => date('c'),
                'event' => 'CDR save failed',
                'idguia' => $idguia,
                'filename' => 'R-' . $fileName . '.zip',
            ]);
        }
    } catch (Exception $e) {
        writeLog([
            'timestamp' => date('c'),
            'event' => 'CDR save error',
            'idguia' => $idguia,
            'error' => $e->getMessage()
        ]);
    }
}

$updateSql = "UPDATE guia_remision SET estado_sunat = '" . mysqli_real_escape_string($conexion, $estado_sunat) . "', resumen_sunat = '" . mysqli_real_escape_string($conexion, $summary) . "'";
// Ensure we persist the ticket in the DB so we can trace async requests
if (!empty($ticket)) {
    $updateSql .= ", ticket_sunat='" . mysqli_real_escape_string($conexion, $ticket) . "'";
}
if (!empty($cdr_path)) {
    $updateSql .= ", cdr_sunat='" . mysqli_real_escape_string($conexion, $cdr_path) . "'";
}
if ($estado === 'Aceptado') {
    $updateSql .= ", estado='Aceptado'";
} elseif ($estado === 'Rechazado') {
    $updateSql .= ", estado='Rechazado'";
}
$updateSql .= " WHERE idguia='" . mysqli_real_escape_string($conexion, $idguia) . "'";
mysqli_query($conexion, $updateSql);
$conexionError = mysqli_error($conexion);
mysqli_close($conexion);

// Build user-friendly message
$user_message = '';

if ($isAccepted) {
    if ($numError === '1033') {
        $user_message = '✓ Guía ya fue registrada en SUNAT (confirmado).';
    } else {
        $user_message = '✓ La guía ha sido ACEPTADA por SUNAT.';
    }
    if (!empty($message)) {
        $user_message .= ' ' . $message;
    }
} elseif ($isProcessing) {
    $user_message = '⏳ La guía está EN PROCESO en SUNAT. Consulte nuevamente en unos minutos.';
    if (!empty($message)) {
        $user_message .= ' ' . $message;
    }
} elseif ($isRejected) {
    $user_message = '✗ La guía fue RECHAZADA por SUNAT.';
    if (!empty($message)) {
        $user_message .= ' Motivo: ' . $message;
    }
} else {
    // No clear status
    $user_message = 'Consulta realizada. ';
    if (!empty($codRespuesta)) {
        $user_message .= 'Código SUNAT: ' . $codRespuesta . '. ';
    }
    if (!empty($message)) {
        $user_message .= $message . '. ';
    }
    $user_message = rtrim($user_message);
}

$response = [
    'success' => true,
    'message' => $user_message,
    'ticket' => $ticket,
    'status' => $status,
    'codRespuesta' => $codRespuesta,
    'data' => $ticketResult,
];

// Add estado_envio flag for frontend
if ($isProcessing) {
    $response['estado_envio'] = 'en_proceso';
} elseif ($isAccepted) {
    $response['estado_envio'] = 'aceptado';
} elseif ($isRejected) {
    $response['estado_envio'] = 'rechazado';
}
if ($conexionError !== '') {
    $response['warning'] = 'No se pudo guardar el estado en la base de datos: ' . $conexionError;
}

echo json_encode($response);
