<?php
require_once "../modelos/Resumen.php";
if (strlen(session_id()) < 1) {
    session_start();
}

$resumen = new Resumen();

switch ($_GET["op"]) {
    case 'listar_boletas':
        $fecha_inicio = $_REQUEST["fecha_inicio"];
        $fecha_fin = $_REQUEST["fecha_fin"];
        $idsucursal = $_REQUEST["idsucursal"];

        $rspta = $resumen->listarBoletasParaResumen($fecha_inicio, $fecha_fin, $idsucursal);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => '<input type="checkbox" name="idventa[]" value="' . $reg->idventa . '">',
                "1" => $reg->tipo_comprobante . ' ' . $reg->serie_comprobante . '-' . $reg->num_comprobante,
                "2" => $reg->cliente_tipo_doc . ' - ' . $reg->cliente_doc,
                "3" => number_format($reg->total_venta, 2),
                "4" => '<span class="badge bg-yellow">' . $reg->estado . '</span>'
            );
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'generar_resumen':
        $idventas = isset($_POST['idventas']) ? $_POST['idventas'] : null;
        $fecha_resumen = isset($_POST['fecha_resumen']) ? $_POST['fecha_resumen'] : null;
        $idsucursal = isset($_POST['idsucursal']) ? $_POST['idsucursal'] : null;
        $idpersonal = isset($_POST['idpersonal']) ? $_POST['idpersonal'] : null;

        if (empty($idventas)) {
            echo json_encode(['success' => false, 'message' => 'No se han seleccionado boletas.']);
            exit;
        }

        // URL del script que usa Greenter
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/test/public/FACT_WebService/Facturacion/resumen.php';
        
        $postData = [
            'idventas' => $idventas,
            'fecha_resumen' => $fecha_resumen,
            'idsucursal' => $idsucursal,
            'idpersonal' => $idpersonal
        ];

        // Iniciar cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            echo json_encode(['success' => false, 'message' => 'Error en cURL: ' . curl_error($ch)]);
        } else {
            if ($httpcode == 200) {
                echo $response;
            } else {
                echo json_encode(['success' => false, 'message' => 'Error en el servidor al procesar el resumen. Código: ' . $httpcode, 'details' => $response]);
            }
        }

        // Cerrar cURL
        curl_close($ch);
        break;

    case 'listar_resumenes':
        $fecha_inicio = $_REQUEST["fecha_inicio"];
        $fecha_fin = $_REQUEST["fecha_fin"];
        $idsucursal = $_REQUEST["idsucursal"];

        $rspta = $resumen->listarResumenes($fecha_inicio, $fecha_fin, $idsucursal);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $estado_display = '';
            $download_button = '';

            if ($reg->estado === 'ACEPTADO') {
                $estado_display = '<span class="badge bg-success">' . $reg->estado . '</span>';
                $zip_filename = 'R-' . $reg->nombre_xml . '.zip';
                $xml_filename = str_replace('.zip', '.xml', $zip_filename); // Generate XML filename

                // Construct download URL for ZIP
                $download_zip_url = 'public/FACT_WebService/Facturacion/cdrs/' . $zip_filename;
                // Construct download URL for XML
                $download_xml_url = 'public/FACT_WebService/Facturacion/cdrs/' . $xml_filename;

                $download_button = '
                    <a href="'.$download_zip_url.'" target="_blank" class="btn btn-warning btn-xs" title="Descargar CDR ZIP">
                        <i class="fa fa-download"></i> ZIP
                    </a>
                    <a href="'.$download_xml_url.'" target="_blank" class="btn btn-primary btn-xs ml-1" title="Descargar CDR XML">
                        <i class="fa fa-download"></i> XML
                    </a>';
            } else if ($reg->estado === 'ENVIADO') {
                $estado_display = '<span class="badge bg-info">' . $reg->estado . '</span>';
            } else if ($reg->estado === 'RECHAZADO') {
                $estado_display = '<span class="badge bg-danger">' . $reg->estado . '</span>';
            } else {
                $estado_display = '<span class="badge bg-secondary">' . $reg->estado . '</span>';
            }

            $data[] = array(
                "0" => date("d/m/Y", strtotime($reg->fecha_generacion)),
                "1" => $reg->ticket,
                "2" => str_pad($reg->correlativo, 3, '0', STR_PAD_LEFT),
                "3" => $estado_display,
                "4" => '<button class="btn btn-info btn-xs" onclick="consultarTicket(\''.$reg->ticket.'\', '.$reg->idresumen.')">Consultar</button>' . $download_button
            );
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'consultar_ticket':
        $ticket = $_GET['ticket'] ?? null;
        $idresumen = $_GET['idresumen'] ?? null;
        $idpersonal = $_GET['idpersonal'] ?? null;

        if (empty($ticket) || empty($idresumen)) {
            echo json_encode(['success' => false, 'message' => 'Faltan parámetros.']);
            exit;
        }

        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/test/public/FACT_WebService/Facturacion/consultacdrresumen.php?ticket=' . $ticket . '&idresumen=' . $idresumen . '&idpersonal=' . $idpersonal;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            echo json_encode(['success' => false, 'message' => 'Error en cURL: ' . curl_error($ch)]);
        } else {
             // Always echo the response directly for debugging
            echo $response;
        }
        curl_close($ch);
        break;
}
?>