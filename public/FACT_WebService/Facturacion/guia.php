<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../core/FluentQuery.php';
require_once __DIR__ . '/../../../core/FluentSave.php';
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Despatch\Despatch;
use Greenter\Model\Despatch\DespatchDetail;
use Greenter\Model\Despatch\Direction;
use Greenter\Model\Despatch\Driver;
use Greenter\Model\Despatch\Shipment;
use Greenter\Model\Despatch\Transportist;
use Greenter\Model\Despatch\Vehicle;
use Greenter\Model\Sale\Document;

require_once __DIR__ . '/src/Util.php';
require_once __DIR__ . '/guia_remision/Operaciones.php';
require_once __DIR__ . '/guia_remision/SunatGenerateToken.php';
require_once __DIR__ . '/guia_remision/SunatSendDocument.php';
require_once __DIR__ . '/guia_remision/Consultas.php';

$pdo = Conexion::conectar();

function mapTipoDocumentoSunat($tipoDocumento)
{
    $tipo = strtoupper(trim((string) $tipoDocumento));
    switch ($tipo) {
        case 'DNI':
            return '1';
        case 'RUC':
            return '6';
        case 'CE':
        case 'CARNET DE EXTRANJERIA':
            return '4';
        case 'PASAPORTE':
            return '7';
        default:
            return '0';
    }
}

function esTransportePrivado($tipoTransporte)
{
    if ($tipoTransporte === null || $tipoTransporte === '') {
        return false;
    }

    if (is_bool($tipoTransporte)) {
        return $tipoTransporte;
    }

    if (is_int($tipoTransporte)) {
        return $tipoTransporte === 1;
    }

    if (is_numeric($tipoTransporte)) {
        return (int) $tipoTransporte === 1;
    }

    $tipo = strtolower(trim((string) $tipoTransporte));
    return in_array($tipo, array('privado', 'si', 'true', '1', '01', '02'), true);
}

function normalizarPlacaVehiculo($placa)
{
    $placa = trim((string) $placa);
    if ($placa === '') {
        return '';
    }

    $placa = strtoupper($placa);
    $placaSinSeparadores = preg_replace('/[^A-Z0-9]/', '', $placa);

    if ($placaSinSeparadores === '') {
        return '';
    }

    return $placaSinSeparadores;
}

function obtenerMotivosTrasladoGre()
{
    return array(
        '01' => 'Venta',
        '02' => 'Compra',
        '03' => 'Venta con entrega a terceros',
        '04' => 'Traslado entre establecimientos de la misma empresa',
        '05' => 'Consignacion',
        '06' => 'Devolucion',
        '07' => 'Recojo de bienes transformados',
        '08' => 'Importacion',
        '09' => 'Exportacion',
        '13' => 'Otros',
        '14' => 'Venta sujeta a confirmacion del comprador',
        '17' => 'Traslado de bienes para transformacion',
        '18' => 'Traslado emisor itinerante CP',
        '19' => 'Traslado a zona primaria',
        '10' => 'Traslado zona secundaria',
        '11' => 'Otro motivo de traslado',
    );
}

function normalizarCodigoMotivoTraslado($codigo)
{
    $valor = preg_replace('/\D/', '', (string) $codigo);
    if ($valor === '') {
        return '';
    }

    return str_pad($valor, 2, '0', STR_PAD_LEFT);
}

function obtenerDescripcionMotivoTraslado($codigo, array $guia = array())
{
    $motivos = obtenerMotivosTrasladoGre();
    $codigo = normalizarCodigoMotivoTraslado($codigo);
    if (in_array($codigo, array('13', '11'), true)) {
        $otro = trim((string) ($guia['motivo_traslado_otro'] ?? ''));
        if ($otro !== '') {
            return $otro;
        }
    }
    return isset($motivos[$codigo]) ? $motivos[$codigo] : 'Otro motivo de traslado';
}

function obtenerDatosConductor($guia)
{
    $nombres = trim((string) ($guia['chofer_nombre'] ?? ''));
    $apellidos = trim((string) ($guia['chofer_apellido'] ?? ''));

    if ($nombres === '' && $apellidos === '') {
        $documento = trim((string) ($guia['chofer_documento'] ?? ''));
        if ($documento === '') {
            $documento = trim((string) ($guia['licencia_conducir'] ?? ''));
        }
        if ($documento !== '') {
            return ['CONDUCTOR', $documento];
        }
    }

    return [$nombres, $apellidos];
}

function normalizarReferenciaConductor($valor)
{
    $valor = trim((string) $valor);
    if ($valor === '') {
        return '';
    }

    return preg_match('/^Q/i', $valor) ? $valor : 'Q' . $valor;
}

function obtenerTipoDocumentoRelacionado($referencia)
{
    $referencia = strtoupper(trim((string) $referencia));
    if ($referencia === '' || strpos($referencia, '-') === false) {
        return null;
    }

    $serie = explode('-', $referencia, 2)[0];
    $prefijo = substr($serie, 0, 1);

    if ($prefijo === 'F') {
        return '01';
    }
    if ($prefijo === 'B') {
        return '03';
    }

    return null;
}

function obtenerEstadoSunatDesdeRespuesta($data)
{
    $status = null;
    $message = '';
    $codRespuesta = null;
    $numError = null;

    if (isset($data['codRespuesta'])) {
        $codRespuesta = trim((string) $data['codRespuesta']);
    }

    if (isset($data['error']) && is_array($data['error'])) {
        if (isset($data['error']['desError'])) {
            $message = trim((string) $data['error']['desError']);
        }
        if (isset($data['error']['numError'])) {
            $numError = trim((string) $data['error']['numError']);
        }
    }

    if ($codRespuesta === '0') {
        $status = 'ACEPTADO';
    } elseif ($codRespuesta === '98') {
        $status = 'PROCESANDO';
    } elseif ($codRespuesta === '99') {
        $status = 'RECHAZADO';
    } elseif ($numError === '1033') {
        $status = 'ACEPTADO';
    } elseif (isset($data['status']) && is_string($data['status'])) {
        $status = strtoupper(trim($data['status']));
    } elseif (isset($data['estado']) && is_string($data['estado'])) {
        $status = strtoupper(trim($data['estado']));
    }

    if ($message === '' && isset($data['mensaje']) && is_string($data['mensaje'])) {
        $message = trim($data['mensaje']);
    }
    if ($message === '' && isset($data['descripcion']) && is_string($data['descripcion'])) {
        $message = trim($data['descripcion']);
    }
    if ($message === '' && isset($data['message']) && is_string($data['message'])) {
        $message = trim($data['message']);
    }

    return [
        'status' => $status,
        'message' => $message,
        'codRespuesta' => $codRespuesta,
        'numError' => $numError,
    ];
}

function guardarCdrSunatDesdeRespuesta($ticketResult, $guia, $rucEmisor)
{
    $arcCdr = isset($ticketResult['arcCdr']) ? trim((string) $ticketResult['arcCdr']) : '';
    if ($arcCdr === '') {
        return ['success' => false, 'path' => '', 'message' => 'No existe el CDR en la respuesta de SUNAT.'];
    }

    $ruc = trim((string) $rucEmisor);
    $serie = isset($guia['serie_comprobante']) ? trim((string) $guia['serie_comprobante']) : '';
    $correlativo = isset($guia['num_comprobante']) ? str_pad((string) $guia['num_comprobante'], 8, '0', STR_PAD_LEFT) : '';
    $fileName = ($ruc !== '' ? $ruc : 'SINRUC') . '-09-' . $serie . '-' . $correlativo;

    $operaciones = new Operaciones();
    $generatedFile = $operaciones->ConvertirBase64_Zip($arcCdr, $fileName);

    if ($generatedFile === false || $generatedFile === '') {
        return ['success' => false, 'path' => '', 'message' => 'No se pudo guardar el CDR en disco.'];
    }

    return [
        'success' => true,
        'path' => basename($generatedFile),
        'full_path' => $generatedFile,
        'message' => 'CDR guardado correctamente.',
    ];
}

function consultarTicketSunat($tokenArgs, $ticket, $guia, $util)
{
    $tokenGenerator = new SunatGenerateToken($tokenArgs);
    $tokenResult = $tokenGenerator->generateSunatToken();

    if (!$tokenResult['success'] || empty($tokenResult['response']['access_token'])) {
        return [
            'success' => false,
            'message' => 'No se pudo obtener token SUNAT.',
            'data' => $tokenResult['response'] ?? [],
        ];
    }

    $consultas = new Consultas();
    $ticketResult = $consultas->ConsultaTicket($tokenResult['response']['access_token'], $ticket);

    if ($ticketResult === false || (is_array($ticketResult) && isset($ticketResult['success']) && $ticketResult['success'] === false)) {
        $errorData = is_array($ticketResult) && isset($ticketResult['data']) ? $ticketResult['data'] : null;
        $message = 'Error al consultar el ticket SUNAT.';
        if (is_array($errorData) && isset($errorData['error'])) {
            $message = $errorData['error'];
            if (isset($errorData['message'])) {
                $message .= ' - ' . $errorData['message'];
            }
        }
        return ['success' => false, 'message' => $message, 'data' => $errorData];
    }

    $estado = obtenerEstadoSunatDesdeRespuesta($ticketResult);
    $estado['success'] = true;
    $estado['data'] = $ticketResult;

    return $estado;
}

$idguia = isset($_GET['idguia']) ? $_GET['idguia'] : '';

if ($idguia === '') {
    echo json_encode(['success' => false, 'message' => 'No se recibio la guia.']);
    exit;
}

$util = Util::getInstance();


$guia = (new DBQuery($pdo))
    ->select('g.*, c.nombre AS cliente_nombre,
               c.num_documento AS cliente_doc,
               c.tipo_documento AS cliente_tipo_doc,
               COALESCE(ptr.nombre, pt.nombre) AS transportista_nombre,
               COALESCE(ptr.num_documento, pt.num_documento) AS transportista_doc,
               COALESCE(ptr.tipo_documento, pt.tipo_documento) AS transportista_tipo_doc')
    ->from('guia_remision g')
    ->join('persona c', 'g.idcliente = c.idpersona')
    ->leftJoin('personal ptr', 'g.idtransportista = ptr.idpersonal')
    ->leftJoin('persona pt', 'g.idtransportista = pt.idpersona')
    ->where('g.idguia', '=', $idguia)
    ->first();

if (!$guia) {
    echo json_encode(['success' => false, 'message' => 'No se encontro la guia solicitada.']);
    exit;
}

$motivosTraslado = obtenerMotivosTrasladoGre();
$guia['idmotivo'] = normalizarCodigoMotivoTraslado($guia['idmotivo'] ?? '');
if (empty($guia['idmotivo']) || !isset($motivosTraslado[(string) $guia['idmotivo']])) {
    echo json_encode(['success' => false, 'message' => 'La guia no tiene un motivo de traslado GRE valido.']);
    exit;
}

if (in_array($guia['idmotivo'], array('13', '11'), true) && trim((string) ($guia['motivo_traslado_otro'] ?? '')) === '') {
    echo json_encode(['success' => false, 'message' => 'La guia con motivo Otros debe especificar el motivo de traslado.']);
    exit;
}

if (empty($guia['ubigeo_partida']) || empty($guia['punto_partida']) || empty($guia['ubigeo_llegada']) || empty($guia['punto_llegada'])) {
    echo json_encode(['success' => false, 'message' => 'La guia no tiene completos los puntos de partida y llegada.']);
    exit;
}

if ((float) $guia['peso'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'La guia debe tener un peso total mayor a cero.']);
    exit;
}

// datos de la sucursal y facturacion
$datos_sucursal = (new DBQuery($pdo))
    ->select('*')
    ->from('sucursal s')
    ->join('empresas e', 's.idempresa = e.idempresa')
    ->where('s.idsucursal', '=', $guia['idsucursal'])
    ->first();
if (!$datos_sucursal) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos de sucursal o negocio para generar la guia.']);
    exit;
}

$client = new Client();
$client->setTipoDoc(mapTipoDocumentoSunat($guia['cliente_tipo_doc']))
    ->setNumDoc($guia['cliente_doc'])
    ->setRznSocial($guia['cliente_nombre']);

$companyAdress = new Address();
$companyAdress->setUbigueo($datos_sucursal['ubigeo'])
    ->setDistrito($datos_sucursal['distrito'])
    ->setProvincia($datos_sucursal['provincia'])
    ->setDepartamento($datos_sucursal['departamento'])
    ->setDireccion($datos_sucursal['direccion']);

$company = new Company();
$company->setRuc($datos_sucursal['ruc'])
    ->setNombreComercial($datos_sucursal['razon_social'])
    ->setRazonSocial($datos_sucursal['razon_social'])
    ->setAddress($companyAdress);

$detalles = (new DBQuery($pdo))
    ->select('*')
    ->from('detalle_guia')
    ->where('idguia', '=', $idguia)
    ->get();
$cantidadBultos = 0;
foreach ($detalles as $detalle) {
    $item = new DespatchDetail();
    $item->setCantidad((float) $detalle['cantidad'])
        ->setUnidad($detalle['unidad'] ?: 'NIU')
        ->setDescripcion($detalle['nombre_producto'])
        ->setCodigo($detalle['codigo']);
    $detalles[] = $item;
    $cantidadBultos += (int) $detalle['bultos'];
}

$partida = new Direction($guia['ubigeo_partida'], $guia['punto_partida']);
$llegada = new Direction($guia['ubigeo_llegada'], $guia['punto_llegada']);

$esPrivado = esTransportePrivado($guia['tipo_transporte']);

$shipment = new Shipment();
$shipment->setCodTraslado((string) $guia['idmotivo'])
    ->setDesTraslado(obtenerDescripcionMotivoTraslado($guia['idmotivo'], $guia))
    ->setModTraslado($esPrivado ? '02' : '01')
    ->setFecTraslado(new DateTime($guia['fecha_traslado']))
    ->setIndTransbordo(false)
    ->setPesoTotal((float) $guia['peso'])
    ->setUndPesoTotal('KGM')
    ->setNumBultos($cantidadBultos > 0 ? $cantidadBultos : null)
    ->setPartida($partida)
    ->setLlegada($llegada);

if ($esPrivado) {
    $choferTipoDoc = mapTipoDocumentoSunat(isset($guia['chofer_tipo_documento']) ? $guia['chofer_tipo_documento'] : 'DNI');
    $choferDoc = trim((string) ($guia['chofer_documento'] ?? ''));
    if ($choferDoc === '') {
        $choferDoc = trim((string) ($guia['licencia_conducir'] ?? ''));
    }
    [$choferNombres, $choferApellidos] = obtenerDatosConductor($guia);
    $placaVehiculo = normalizarPlacaVehiculo($guia['placa_vehiculo'] ?? '');

    $vehiculo = new Vehicle();
    $vehiculo->setPlaca($placaVehiculo);

    $chofer = new Driver();
    $licenciaConductor = normalizarReferenciaConductor($guia['licencia_conducir'] ?? '');

    $chofer->setTipo('Principal')
        ->setTipoDoc($choferTipoDoc)
        ->setNroDoc($choferDoc)
        ->setLicencia($licenciaConductor)
        ->setNombres($choferNombres)
        ->setApellidos($choferApellidos);

    $shipment->setVehiculo($vehiculo)
        ->setChoferes([$chofer]);
} else {
    $transportista = new Transportist();
    $transportista->setTipoDoc(mapTipoDocumentoSunat($guia['transportista_tipo_doc']))
        ->setNumDoc($guia['transportista_doc'])
        ->setRznSocial($guia['transportista_nombre']);

    $shipment->setTransportista($transportista);
}

$despatch = new Despatch();
$despatch->setVersion('2022')
    ->setTipoDoc('09')
    ->setSerie($guia['serie_comprobante'])
    ->setCorrelativo($guia['num_comprobante'])
    ->setFechaEmision(new DateTime($guia['fecha_emision']))
    ->setCompany($company)
    ->setDestinatario($client)
    ->setEnvio($shipment)
    ->setDetails($detalles);

$observacion = trim((string) ($guia['observacion'] ?? ''));
if ($observacion !== '') {
    $despatch->setObservacion($observacion);
}

$tipoDocRelacionado = obtenerTipoDocumentoRelacionado($guia['factura_ref'] ?? '');
if ($tipoDocRelacionado !== null && !empty($guia['factura_ref'])) {
    $relDoc = new Document();
    $relDoc->setTipoDoc($tipoDocRelacionado)
        ->setNroDoc(trim((string) $guia['factura_ref']));
    $despatch->setRelDoc($relDoc);
}

$see = $util->getSee($guia['idsucursal'], 'GRE');

$xml = $see->getXmlSigned($despatch);
$util->writeXml($despatch, $xml);

$operaciones = new Operaciones();
$operaciones->CrearZip($despatch->getName());

$tokenArgs = [
    'sunat_client_id' => $datos_sucursal['client_id'] ?? '',
    'sunat_client_secret' => $datos_sucursal['client_secret'] ?? '',
    'ruc' => $datos_sucursal['ruc'] ?? '',
    'usuario_sol' => $datos_sucursal['usuario_sol'] ?? '',
    'clave_sol' => $datos_sucursal['clave_sol'] ?? '',
];

$sender = new SunatSendDocument();
$sendResult = $sender->send($tokenArgs, $despatch->getName());

if ($sendResult['success']) {
    $data = $sendResult['data'];

    // Log la respuesta de SUNAT para debugging
    error_log("SunatSendDocument response: " . json_encode($data));

    $ticket = trim($data['numTicket'] ?? $data['ticket'] ?? $data['ticket_sunat'] ?? '');
    error_log("Extracted ticket: '" . $ticket . "' (length: " . strlen($ticket) . ")");
    error_log("Full data response: " . json_encode($data));

    $respuestaInicial = obtenerEstadoSunatDesdeRespuesta($data);
    $status = $respuestaInicial['status'];
    $message = $respuestaInicial['message'];
    if ($message === '') {
        $message = json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    $consultaTicket = ['success' => false, 'data' => []];
    if ($ticket !== '' && ($status === null || $status === 'PROCESANDO' || $status === 'PROCESSING' || $status === 'ACEPTADO' || $status === 'AUTORIZADO' || $status === 'ACCEPTED' || $status === 'VALIDADO')) {
        $consultaTicket = consultarTicketSunat($tokenArgs, $ticket, $guia, $util);
        if ($consultaTicket['success']) {
            $status = $consultaTicket['status'];
            if (!empty($consultaTicket['message'])) {
                $message = $consultaTicket['message'];
            }
        } else {
            $message = $consultaTicket['message'] ?? $message;
        }
    }

    $acceptedStatuses = ['ACEPTADO', 'AUTORIZADO', 'ACCEPTED', 'VALIDADO'];
    $isAccepted = !empty($status) && in_array($status, $acceptedStatuses, true);
    $isRejected = !empty($status) && in_array($status, ['RECHAZADO', 'REJECTED'], true);
    $sunatState = $isAccepted ? '1' : ($isRejected ? '3' : '2');
    $estadoSql = $isAccepted ? ", estado = 'Aceptado'" : ($isRejected ? ", estado = 'Rechazado'" : '');

    $cdrPath = '';
    if ($isAccepted) {
        $ticketResponseData = [];
        if (isset($consultaTicket['data']) && is_array($consultaTicket['data'])) {
            $ticketResponseData = $consultaTicket['data'];
        }
        if (empty($ticketResponseData) && isset($data['arcCdr'])) {
            $ticketResponseData = $data;
        }
        if (!empty($ticketResponseData['arcCdr'])) {
            $cdrResult = guardarCdrSunatDesdeRespuesta($ticketResponseData, $guia, $datos_negocio['documento'] ?? '');
            if ($cdrResult['success']) {
                $cdrPath = $cdrResult['path'];
                error_log('CDR guardado: ' . $cdrPath);
            } else {
                error_log('CDR no guardado: ' . $cdrResult['message']);
            }
        }
    }

    $summary = trim($message);
    if (!empty($ticket)) {
        $summary .= ($summary !== '' ? ' | ' : '') . 'Ticket: ' . $ticket;
    }
    if (!empty($status)) {
        $summary .= ($summary !== '' ? ' | ' : '') . 'Status: ' . $status;
    }
    if (!empty($cdrPath)) {
        $summary .= ($summary !== '' ? ' | ' : '') . 'CDR: ' . $cdrPath;
    }

    $hash = $see->getFactory()->getLastXml();
    $dom = new DOMDocument();
    $dom->loadXML($hash);
    $digest = $dom->getElementsByTagName('DigestValue')->item(0)->nodeValue;

    // Prepare ticket for DB (always save if present in response)
    $ticketValue = '';
    if (!empty($ticket)) {
        $ticketValue = $ticket;
    }

    $update = (new FluentSaver($pdo))
        ->table('guia_remision')
        ->primaryKey('idguia')
        ->data([
            'idguia' => $idguia,
            'estado_sunat' => $sunatState,
            'ticket_sunat' => $ticketValue,
            'hash_cpe' => $summary,
            'resumen_sunat' => $cdrPath,
        ])
        ->update();

    // Verify the ticket was saved
    $row = (new DBQuery($pdo))
        ->select('ticket_sunat')
        ->from('guia_remision')
        ->where('idguia', '=', $idguia)
        ->first();

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Error de verificacion de envio a SUNAT.']);
    }


    echo json_encode([
        'success' => true,
        'message' => $message,
        'ticket' => $ticket,
        'sent_to_sunat' => true,
        'estado_envio' => $isAccepted ? 'aceptado' : ($isRejected ? 'rechazado' : 'en_proceso'),
        'status' => $status,
        'cdr_path' => $cdrPath,
        'data' => $data,
    ]);
} else {
    $errorMessage = '';
    if (isset($sendResult['data']['error'])) {
        $errorMessage = is_string($sendResult['data']['error']) ? $sendResult['data']['error'] : json_encode($sendResult['data']['error'], JSON_UNESCAPED_UNICODE);
    } else {
        $errorMessage = json_encode($sendResult['data'], JSON_UNESCAPED_UNICODE);
    }
    echo json_encode(['success' => false, 'message' => $errorMessage, 'data' => $sendResult['data']]);
}
