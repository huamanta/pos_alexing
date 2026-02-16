<?php
require_once 'vendor/autoload.php';
require_once __DIR__ . '/src/Util.php';

use Greenter\Ws\Services\SunatEndpoints;

date_default_timezone_set('America/Lima');

function savedFile(string $filename, ?string $content): void {
    $fileDir = __DIR__ . '/files/'; // Changed from /cdrs/ to /files/
    if (!file_exists($fileDir)) {
        mkdir($fileDir, 0777, true);
    }
    $pathZip = $fileDir . DIRECTORY_SEPARATOR . $filename;
    file_put_contents($pathZip, $content);
}

function extractXmlFromCdrZip(string $zipFilePath, string $outputDir): ?string {
    error_log("Attempting to extract XML from ZIP: " . $zipFilePath);
    if (!file_exists($zipFilePath)) {
        error_log("ZIP file not found: " . $zipFilePath);
        return null;
    }

    $zip = new ZipArchive;
    $zipOpenResult = $zip->open($zipFilePath);
    if ($zipOpenResult === TRUE) {
        error_log("ZIP file opened successfully. Number of files: " . $zip->numFiles);
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            error_log("Inspecting file in ZIP: " . $filename);
            if (pathinfo($filename, PATHINFO_EXTENSION) == 'xml') {
                error_log("XML file found inside ZIP: " . $filename);
                $xmlContent = $zip->getFromIndex($i);
                $xmlOutputPath = $outputDir . DIRECTORY_SEPARATOR . basename($filename);
                file_put_contents($xmlOutputPath, $xmlContent);
                $zip->close();
                error_log("XML extracted and saved to: " . $xmlOutputPath);
                return $xmlOutputPath;
            }
        }
        $zip->close();
        error_log("No XML file found inside ZIP with .xml extension: " . $zipFilePath);
        return null;
    } else {
        error_log("Failed to open ZIP file: " . $zipFilePath . " Error code: " . $zipOpenResult);
        return null;
    }
}

$util = Util::getInstance();
$conexion = $util->abrirConexion();

if (!$conexion) {
    echo json_encode(['success' => false, 'message' => 'Error al conectar a la base de datos.']);
    exit;
}

$ticket = $_GET['ticket'] ?? null;
$idresumen = $_GET['idresumen'] ?? null;
$idpersonal = $_GET['idpersonal'] ?? null;

if (empty($ticket) || empty($idresumen) || empty($idpersonal)) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó ticket, ID de resumen o ID de personal.']);
    exit;
}

try {
    $debug_info = [];
    $debug_info['request_params'] = ['ticket' => $ticket, 'idresumen' => $idresumen, 'idpersonal' => $idpersonal];

    $empresa = mysqli_query($conexion, "SELECT * from datos_negocio");
    $datos_empresa = mysqli_fetch_assoc($empresa);

    if  ($datos_empresa["estado_certificado"]=="BETA"){
        $see = $util->getSee(SunatEndpoints::FE_BETA, $datos_empresa["estado_certificado"]);
    }elseif($datos_empresa["estado_certificado"]=="PRODUCCION"){
        $see = $util->getSee(SunatEndpoints::FE_PRODUCCION, $datos_empresa["estado_certificado"]);
    }

    $result = $see->getStatus($ticket);
    
    // Log key properties of the StatusResult object
    $debug_info['greenter_status_is_success'] = $result->isSuccess();
    if (!$result->isSuccess()) {
        $debug_info['greenter_status_error_code'] = $result->getError()->getCode();
        $debug_info['greenter_status_error_message'] = $result->getError()->getMessage();
        $debug_info['greenter_status_error_object'] = $result->getError(); // Log the error object directly as well, just in case
    } else {
        $cdr_response = $result->getCdrResponse();
        if ($cdr_response) {
            $debug_info['greenter_cdr_code'] = $cdr_response->getCode();
            $debug_info['greenter_cdr_description'] = $cdr_response->getDescription();
            $debug_info['greenter_cdr_response_object'] = $cdr_response; // Log the cdr response object directly
        } else {
            $debug_info['greenter_cdr_response'] = 'No CDR Response found (yet).';
        }
    }


    $resumen_query = mysqli_query($conexion, "SELECT * FROM resumen_diario WHERE idresumen = $idresumen");
    $resumen_actual = mysqli_fetch_assoc($resumen_query);
    $debug_info['resumen_actual_estado'] = $resumen_actual['estado'];


    if ($resumen_actual['estado'] === 'ACEPTADO' || $resumen_actual['estado'] === 'RECHAZADO') {
        echo json_encode(['success' => true, 'message' => 'Este resumen ya fue procesado. Estado: ' . $resumen_actual['estado'], 'debug' => $debug_info]);
        exit;
    }

    if ($result->isSuccess()) {
        $cdr = $result->getCdrResponse();
        // Save the CDR ZIP file
        $cdrZipFilename = 'R-' . $resumen_actual['nombre_xml'] . '.zip';
        $cdrZipPath = __DIR__ . '/cdrs/' . $cdrZipFilename;
        
        $cdrZipContent = $result->getCdrZip();
        $debug_info['cdr_zip_content_length'] = strlen($cdrZipContent); // Log the length of the ZIP content
        $debug_info['cdr_zip_path_attempted'] = $cdrZipPath; // Log the path where it's attempting to save

        savedFile($cdrZipFilename, $cdrZipContent); // This just saves the zip

        // Extract XML from the saved ZIP
        $xmlOutputPath = extractXmlFromCdrZip($cdrZipPath, __DIR__ . '/files/'); // Changed back to /files/
        if ($xmlOutputPath) {
            $debug_info['cdr_xml_extracted_path'] = $xmlOutputPath;
            // Preview first 500 characters of the extracted XML content
            $debug_info['cdr_xml_content_preview'] = substr(file_get_contents($xmlOutputPath), 0, 500); 
        } else {
            $debug_info['cdr_xml_extraction_error'] = 'Failed to extract XML from CDR ZIP.';
        }
        
        $respuesta_sunat = $cdr->getDescription();
        $codigo_respuesta = $cdr->getCode();
        $debug_info['sunat_response_code'] = $codigo_respuesta;
        $debug_info['sunat_response_desc'] = $respuesta_sunat;

        if ($codigo_respuesta == 0) { // 0 = Aceptado
            $nuevo_estado_resumen = 'ACEPTADO';
            $nuevo_estado_venta = 'Aceptado por resumen'; // Requerimiento del usuario
            $nuevo_dov_estado_venta = 'ACEPTADO';
            $nuevo_estadoS_venta = 'TERMINADO';

            // Update resumen_diario
            $sql_update_resumen = "UPDATE resumen_diario SET estado = ?, respuesta_sunat = ? WHERE idresumen = ?";
            $stmt_resumen = $conexion->prepare($sql_update_resumen);
            if ($stmt_resumen === false) { $debug_info['stmt_resumen_prepare_error'] = $conexion->error; }
            $debug_info['sql_update_resumen'] = $sql_update_resumen;
            $debug_info['bind_params_resumen'] = [$nuevo_estado_resumen, $respuesta_sunat, $idresumen];
            $stmt_resumen->bind_param('ssi', $nuevo_estado_resumen, $respuesta_sunat, $idresumen);
            $exec_resumen = $stmt_resumen->execute();
            if ($exec_resumen === false) { $debug_info['stmt_resumen_execute_error'] = $stmt_resumen->error; }
            $debug_info['stmt_resumen_execute_result'] = $exec_resumen;


            // Get all ventas from this summary
            $ventas_query = mysqli_query($conexion, "SELECT idventa FROM resumen_diario_detalle WHERE idresumen = $idresumen");
            
            // Update ventas
            $sql_update_venta = "UPDATE venta SET estado = ?, dov_Estado = ?, estadoS = ?, dov_Nombre = ?, dov_IdEmpleado = ? WHERE idventa = ?";
            $stmt_venta = $conexion->prepare($sql_update_venta);
            if ($stmt_venta === false) { $debug_info['stmt_venta_prepare_error'] = $conexion->error; }
            $debug_info['sql_update_venta'] = $sql_update_venta;
            
            while ($venta = mysqli_fetch_assoc($ventas_query)) {
                $idventa = $venta['idventa'];
                $debug_info['bind_params_venta'][] = [
                    'estado' => $nuevo_estado_venta,
                    'dov_Estado' => $nuevo_dov_estado_venta,
                    'estadoS' => $nuevo_estadoS_venta,
                    'dov_Nombre' => $resumen_actual['nombre_xml'],
                    'dov_IdEmpleado' => $idpersonal,
                    'idventa' => $idventa
                ];
                $stmt_venta->bind_param('ssssii', $nuevo_estado_venta, $nuevo_dov_estado_venta, $nuevo_estadoS_venta, $resumen_actual['nombre_xml'], $idpersonal, $idventa);
                $exec_venta = $stmt_venta->execute();
                if ($exec_venta === false) { $debug_info['stmt_venta_execute_error_idventa_' . $idventa] = $stmt_venta->error; }
                $debug_info['stmt_venta_execute_result_idventa_' . $idventa] = $exec_venta;
            }

        } else { // Rechazado u otro estado
            $nuevo_estado_resumen = 'RECHAZADO';
            $nuevo_estado_venta = 'Por Enviar'; // Revertir para poder reenviar
            $nuevo_dov_estado_venta = 'RECHAZADO';
            $nuevo_estadoS_venta = 'PENDIENTE'; // Revertir a pendiente

            // Update resumen_diario
            $sql_update_resumen_rej = "UPDATE resumen_diario SET estado = ?, respuesta_sunat = ? WHERE idresumen = ?";
            $stmt_resumen = $conexion->prepare($sql_update_resumen_rej);
            if ($stmt_resumen === false) { $debug_info['stmt_resumen_prepare_error_rej'] = $conexion->error; }
            $debug_info['sql_update_resumen_rej'] = $sql_update_resumen_rej;
            $debug_info['bind_params_resumen_rej'] = [$nuevo_estado_resumen, $respuesta_sunat, $idresumen];
            $stmt_resumen->bind_param('ssi', $nuevo_estado_resumen, $respuesta_sunat, $idresumen);
            $exec_resumen = $stmt_resumen->execute();
            if ($exec_resumen === false) { $debug_info['stmt_resumen_execute_error_rej'] = $stmt_resumen->error; }
            $debug_info['stmt_resumen_execute_result_rej'] = $exec_resumen;
            
            // Get all ventas from this summary and revert status
            $ventas_query = mysqli_query($conexion, "SELECT idventa FROM resumen_diario_detalle WHERE idresumen = $idresumen");
            $sql_update_venta_rej = "UPDATE venta SET estado = ?, dov_Estado = ?, estadoS = ? WHERE idventa = ?";
            $stmt_venta = $conexion->prepare($sql_update_venta_rej);
            if ($stmt_venta === false) { $debug_info['stmt_venta_prepare_error_rej'] = $conexion->error; }
            $debug_info['sql_update_venta_rej'] = $sql_update_venta_rej;

            while ($venta = mysqli_fetch_assoc($ventas_query)) {
                $idventa = $venta['idventa'];
                $debug_info['bind_params_venta_rej'][] = [
                    'estado' => $nuevo_estado_venta,
                    'dov_Estado' => $nuevo_dov_estado_venta,
                    'estadoS' => $nuevo_estadoS_venta,
                    'idventa' => $idventa
                ];
                $stmt_venta->bind_param('sssi', $nuevo_estado_venta, $nuevo_dov_estado_venta, $nuevo_estadoS_venta, $idventa);
                $exec_venta = $stmt_venta->execute();
                if ($exec_venta === false) { $debug_info['stmt_venta_execute_error_idventa_rej_' . $idventa] = $stmt_venta->error; }
                $debug_info['stmt_venta_execute_result_idventa_rej_' . $idventa] = $exec_venta;
            }
        }

        mysqli_close($conexion);
        echo json_encode(['success' => true, 'message' => "Respuesta SUNAT: " . $respuesta_sunat, 'debug' => $debug_info]);

    } else {
        mysqli_close($conexion);
        $greenter_error = $result->getError();
        $error_message = 'Error Code: ' . $greenter_error->getCode() . ' Description: ' . $greenter_error->getMessage();
        echo json_encode(['success' => false, 'message' => $error_message, 'debug' => $debug_info]);
    }

} catch (Exception $e) {
    mysqli_close($conexion);
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'debug' => $debug_info]);
}        ?>