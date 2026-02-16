<?php

require_once "../modelos/Negocio.php";

class Procesar
{
    public function convertirXmlTicket($rutaXml)
    {
        libxml_use_internal_errors(true);

        if (!file_exists($rutaXml)) {
            return "<center>Error: archivo XML no encontrado</center>";
        }

        $contenido = file_get_contents($rutaXml);
        if (!$contenido || trim($contenido) === '') {
            return "<center>Error: XML vacío</center>";
        }

        // Quitar BOM
        $contenido = preg_replace('/^\xEF\xBB\xBF/', '', $contenido);

        // Quitar xml-stylesheet
        $contenido = preg_replace('/<\?xml-stylesheet.*?\?>/i', '', $contenido);

        // No convertir doble UTF-8, lo manejamos en valor()
        
        // ====================== CARGAR CON DOM ======================
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;

        if (!$dom->loadXML($contenido, LIBXML_NOCDATA | LIBXML_NOBLANKS)) {
            return "<center>Error: DOM no pudo cargar el XML</center>";
        }

        // ====================== DOM XPATH ======================
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $xpath->registerNamespace('cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');

        // ================= DATOS XML =================
        $serie        = $this->valor($xpath, '//cbc:ID');
        $tipoCodigo   = $this->valor($xpath, '//cbc:InvoiceTypeCode');
        $tipoDocumento = 'COMPROBANTE ELECTRÓNICO';
        switch ($tipoCodigo) {
            case '01': $tipoDocumento = 'FACTURA ELECTRÓNICA'; break;
            case '03': $tipoDocumento = 'BOLETA ELECTRÓNICA'; break;
            case '07': $tipoDocumento = 'NOTA DE CRÉDITO ELECTRÓNICA'; break;
            case '08': $tipoDocumento = 'NOTA DE DÉBITO ELECTRÓNICA'; break;
        }

        $fecha  = $this->valor($xpath, '//cbc:IssueDate');
        $hora   = $this->valor($xpath, '//cbc:IssueTime');

        $fechaFormateada = $fecha !== '' ? date('d-m-y', strtotime($fecha)) : '';
        $horaFormateada  = $hora !== '' ? substr($hora, 0, 5) : '';

        $ruc             = $this->valor($xpath, '//cac:AccountingSupplierParty//cbc:ID');
        $nombreComercial = $this->valor($xpath, '//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name');
        $nombreLegal     = $this->valor($xpath, '//cac:PartyLegalEntity/cbc:RegistrationName');
        $empresa         = $nombreComercial !== '' ? $nombreComercial : $nombreLegal;
        $direccion       = $this->valor($xpath, '//cac:RegistrationAddress//cbc:Line');
        $docCliente      = $this->valor($xpath, '//cac:AccountingCustomerParty//cbc:ID');
        $cliente         = $this->valor($xpath, '//cac:AccountingCustomerParty//cbc:RegistrationName');

        // ================= TIPO DOC CLIENTE =================
        $tipoDocCliente = 'DOC';
        $lenDoc = strlen(trim($docCliente));
        if ($lenDoc === 8) $tipoDocCliente = 'DNI';
        elseif ($lenDoc === 11) $tipoDocCliente = 'RUC';

        $total        = $this->valor($xpath, '//cac:LegalMonetaryTotal/cbc:PayableAmount');
        $totalLetras  = $this->numeroALetras((float)$total);

		// ================= LOGO DEL NEGOCIO =================
		$rutaLogo = '';
        $negocio = new Negocio();
        $rsptaN = $negocio->listar();
        if ($rsptaN) {
            $regN = $rsptaN->fetch_object();
            if (!empty($regN->logo)) {
                $rutaFisica = __DIR__ . '/../reportes/' . $regN->logo;
                if (file_exists($rutaFisica)) {
                    $rutaLogo = dirname($_SERVER['PHP_SELF']) . '/../reportes/' . $regN->logo;
                }
            }
        }
        $logoHtml = $rutaLogo ? "<img src='{$rutaLogo}' style='max-width:120px;height:auto;'>" : '';

        // ================= DETALLE =================
        $items = $xpath->query('//cac:InvoiceLine');
        if ($items->length === 0) return "<center>Error: no se encontraron productos</center>";

        $detalle = '';
        foreach ($items as $item) {
            $cantidad    = $this->valor($xpath, 'cbc:InvoicedQuantity', $item) ?: '1';
            $descripcion = $this->valor($xpath, 'cac:Item/cbc:Description', $item);
            $importe     = $this->valor($xpath, 'cbc:LineExtensionAmount', $item) ?: '0.00';

            $detalle .= "
            <tr>
                <td class='textcenter' style='font-size: 12px'>{$cantidad}</td>
                <td class='textcenter' style='font-size: 12px'>{$descripcion}</td>
                <td style='text-align:right'>S/ ".number_format((float)$importe,2)."</td>
            </tr>";
        }

        // ================= HTML TICKET =================
        return "
        <html>
        <head>
        <meta charset='utf-8'>
        <style>
            body { font-family: monospace; font-size: 8pt; margin:0; padding:0; color:#000; }
            .zona_impresion { width:260px; margin:auto; }
            .ticket-header { text-align:center; margin-bottom:5px; }
            .ticket-header img { display:block; margin:0 auto 6px auto; max-width:120px; height:auto; }
            .ticket-header strong { font-size:11pt; display:block; }
            table { width:100%; border-collapse:collapse; }
            td, th { padding:2px 0; }
            .line-separator { border-top:1px dashed #000; margin:5px 0; }
            .productos th { font-size:8pt; border-bottom:1px dashed #000; }
            .productos td { font-size:8pt; }
            .totales td { font-size:9pt; }
            .nota-final { text-align:center; font-size:8pt; margin-top:6px; }
        </style>
        </head>
        <body>
        <div class='zona_impresion'>
            <div class='ticket-header'>
                {$logoHtml}
                <strong>{$nombreComercial}</strong>
                <small>Razón Social: {$nombreLegal}</small>
                <div>RUC: {$ruc}</div>
                <div>{$direccion}</div>
            </div>
            <div class='line-separator'></div>

            <div style='text-align:center'>
                <strong>{$tipoDocumento}</strong><br>{$serie}
            </div>

            <div class='line-separator'></div>

            <table>
                <tr><td><b>Fecha:</b></td><td>{$fechaFormateada} &nbsp; <b>Hora:</b> {$horaFormateada}</td></tr>
                <tr><td><b>Cliente:</b></td><td>{$cliente}</td></tr>
                <tr><td><b>{$tipoDocCliente}:</b></td><td>{$docCliente}</td></tr>
            </table>

            <div class='line-separator'></div>

            <table class='productos'>
                <tr><th>Cant</th><th>Descripción</th><th style='text-align:right'>Importe</th></tr>
                {$detalle}
            </table>

            <div class='line-separator'></div>

            <table class='totales'>
                <tr><td align='right'><b>TOTAL:</b></td><td align='right'><b>S/ ".number_format((float)$total,2)."</b></td></tr>
            </table>

            <div class='line-separator'></div>

            <div style='font-size:8pt; text-align:center;'><b>SON:</b><br>{$totalLetras}</div>
            <div class='line-separator'></div>

            <div class='nota-final'>
                ¡Gracias por su compra!<br>
                Representación impresa del comprobante
            </div>
        </div>
        </body>
        </html>";
    }

    //  Helper XPath seguro UTF-8
    private function valor(DOMXPath $xp, string $query, DOMNode $ctx = null): string
    {
        $nodelist = $ctx ? $xp->query($query, $ctx) : $xp->query($query);
        if ($nodelist && $nodelist->length > 0) {
            $valor = trim($nodelist->item(0)->nodeValue);
            if (!mb_check_encoding($valor, 'UTF-8')) {
                $valor = mb_convert_encoding($valor, 'UTF-8', 'ISO-8859-1');
            }
            return $valor;
        }
        return '';
    }

    //  Convertir número a letras
    private function numeroALetras($numero)
    {
        $unidad = [
            '', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS',
            'SIETE', 'OCHO', 'NUEVE', 'DIEZ', 'ONCE', 'DOCE',
            'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE',
            'DIECIOCHO', 'DIECINUEVE', 'VEINTE'
        ];
        $decenas = [2 => 'VEINTI',3=>'TREINTA',4=>'CUARENTA',5=>'CINCUENTA',
                    6=>'SESENTA',7=>'SETENTA',8=>'OCHENTA',9=>'NOVENTA'];
        $centenas = [1=>'CIENTO',2=>'DOSCIENTOS',3=>'TRESCIENTOS',4=>'CUATROCIENTOS',
                     5=>'QUINIENTOS',6=>'SEISCIENTOS',7=>'SETECIENTOS',8=>'OCHOCIENTOS',9=>'NOVECIENTOS'];

        $numero = number_format($numero,2,'.','');
        [$entero,$decimal] = explode('.',$numero);

        if($entero==0) $letras='CERO';
        else{
            $letras='';
            if($entero==100) $letras='CIEN';
            else{
                if($entero>=100){
                    $letras .= $centenas[floor($entero/100)].' ';
                    $entero %= 100;
                }
                if($entero<=20) $letras .= $unidad[$entero];
                else{
                    $dec = floor($entero/10);
                    $uni = $entero%10;
                    if($entero>=21 && $entero<=29) $letras .= $decenas[2].$unidad[$uni];
                    else{
                        $letras .= $decenas[$dec];
                        if($uni>0) $letras .= ' Y '.$unidad[$uni];
                    }
                }
            }
        }
        return trim($letras)." CON {$decimal}/100 SOLES";
    }

    public function convertirXmlTicketPdf($rutaXml)
    {
        $html = $this->convertirXmlTicket($rutaXml);

        require_once __DIR__ . '/../reportes/factura/pdf/vendor/autoload.php';

        // limpiar buffer
        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="ticket.pdf"');

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Courier');

        $dompdf = new \Dompdf\Dompdf($options);

        // tamaño ticket 80mm
        $dompdf->setPaper([0, 0, 226.77, 600], 'portrait');

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();

        $dompdf->stream('ticket.pdf', ['Attachment' => false]);
        exit;
    }

    public function convertirXmlFacturaPdf($rutaXml)
{
    require_once __DIR__ . '/../reportes/factura/pdf/vendor/autoload.php';

    if (ob_get_length()) ob_end_clean();

    // 🔥 LEER XML CORRECTAMENTE
    $datos = $this->leerXmlFacturaDatos($rutaXml);

    // 🔥 CREAR VARIABLES PARA PLANTILLA
    extract($datos);

    // 🔥 HTML A4
    ob_start();
    require __DIR__ . '/../reportes/factura/plantilla_factura.php';
    $html = ob_get_clean();

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);

    $pdf = new \Dompdf\Dompdf($options);
    $pdf->setPaper('A4', 'portrait');
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();

    $pdf->stream('factura.pdf', ['Attachment' => false]);
    exit;
}

private function leerXmlFacturaDatos($rutaXml)
{
    libxml_use_internal_errors(true);

    // Leer contenido XML y limpiar BOM y xml-stylesheet
    $contenido = file_get_contents($rutaXml);
    $contenido = preg_replace('/^\xEF\xBB\xBF/', '', $contenido);
    $contenido = preg_replace('/<\?xml-stylesheet.*?\?>/i', '', $contenido);

    // Cargar XML
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadXML($contenido, LIBXML_NOCDATA | LIBXML_NOBLANKS);

    $xp = new DOMXPath($dom);
    $xp->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
    $xp->registerNamespace('cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
    $xp->registerNamespace('sac', 'urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1');

    // ================= DATOS GENERALES =================
    $serie = $this->valor($xp, '//cbc:ID');
    $tipo  = $this->valor($xp, '//cbc:InvoiceTypeCode');

    $tipoDocumento = 'COMPROBANTE ELECTRÓNICO';
    switch ($tipo) {
        case '01': $tipoDocumento = 'FACTURA ELECTRÓNICA'; break;
        case '03': $tipoDocumento = 'BOLETA ELECTRÓNICA'; break;
    }

    $fecha = date('d/m/Y', strtotime($this->valor($xp, '//cbc:IssueDate')));
    $hora  = substr($this->valor($xp, '//cbc:IssueTime'), 0, 5);

    // ================= PROVEEDOR =================
    $ruc       = $this->valor($xp, '//cac:AccountingSupplierParty//cbc:ID');
    $empresa   = $this->valor($xp, '//cac:AccountingSupplierParty//cac:PartyLegalEntity/cbc:RegistrationName');
    $direccion = $this->valor($xp, '//cac:AccountingSupplierParty//cac:PartyLegalEntity/cac:RegistrationAddress//cbc:Line');
    $ciudad    = $this->valor($xp, '//cac:AccountingSupplierParty//cac:PartyLegalEntity/cac:RegistrationAddress//cbc:CityName');
    $distrito  = $this->valor($xp, '//cac:AccountingSupplierParty//cac:PartyLegalEntity/cac:RegistrationAddress//cbc:District');
    $provincia = $this->valor($xp, '//cac:AccountingSupplierParty//cac:PartyLegalEntity/cac:RegistrationAddress//cbc:CountrySubentity');
    $pais      = $this->valor($xp, '//cac:AccountingSupplierParty//cac:PartyLegalEntity/cac:RegistrationAddress//cac:Country/cbc:IdentificationCode');

    // ================= CLIENTE =================
    $numDoc  = $this->valor($xp, '//cac:AccountingCustomerParty//cbc:ID');
    $cliente = $this->valor($xp, '//cac:AccountingCustomerParty//cac:PartyLegalEntity/cbc:RegistrationName');
    $tipoDoc = strlen($numDoc) === 8 ? 'DNI' : 'RUC';
    $direccionCliente = $this->valor($xp, '//cac:AccountingCustomerParty//cac:PartyLegalEntity/cac:RegistrationAddress//cbc:Line');

    // ================= DETALLE Y MONTOS =================
    $detalle = '';
    $opGravada     = 0;
    $opExonerada   = 0;
    $opInafecta    = 0;
    $igv           = 0;
    $isc           = 0;
    $icbper        = 0;
    
    $items = $xp->query('//cac:InvoiceLine');
    foreach ($items as $i) {
        $cant = $this->valor($xp, 'cbc:InvoicedQuantity', $i);
        $desc = $this->valor($xp, 'cac:Item/cbc:Description', $i);
        // Corregido: Usar PriceAmount para P. Unit
        $precioUnitario = (float) $this->valor($xp, './/cac:Price/cbc:PriceAmount', $i);
        $imp  = (float) $this->valor($xp, 'cbc:LineExtensionAmount', $i);

        $detalle .= "
        <tr>
            <td>{$cant}</td>
            <td>{$desc}</td>
            <td class='textright'>S/ ".number_format($precioUnitario, 2)."</td>
            <td class='textright'>S/ ".number_format($imp, 2)."</td>
        </tr>";

        // Determinar tipo de afectación por línea con XPaths específicos
        $igvSubtotal = $xp->query('.//cac:TaxTotal/cac:TaxSubtotal[cac:TaxCategory/cac:TaxScheme/cbc:ID = "1000"]', $i)->item(0);
        $exoSubtotal = $xp->query('.//cac:TaxTotal/cac:TaxSubtotal[cac:TaxCategory/cac:TaxScheme/cbc:ID = "9997"]', $i)->item(0);
        $inaSubtotal = $xp->query('.//cac:TaxTotal/cac:TaxSubtotal[cac:TaxCategory/cac:TaxScheme/cbc:ID = "9998"]', $i)->item(0);

        if ($igvSubtotal) {
            // Se encontró un subtotal de IGV (Gravado)
            $taxTypeCode = $this->valor($xp, 'cac:TaxCategory/cbc:TaxExemptionReasonCode', $igvSubtotal);
            $lineIgvAmount = (float) $this->valor($xp, 'cbc:TaxAmount', $igvSubtotal);
            
            // Regla de negocio: si es 'Gravado' (10) pero el IGV es 0, tratar como 'Exonerado'
            if ($taxTypeCode == '10' && $lineIgvAmount == 0) {
                $opExonerada += $imp;
            } else {
                // Es una operación gravada normal
                $opGravada += $imp;
                $igv += $lineIgvAmount;
            }
        } elseif ($exoSubtotal) {
            // Se encontró un subtotal de tipo Exonerado
            $opExonerada += $imp;
        } elseif ($inaSubtotal) {
            // Se encontró un subtotal de tipo Inafecto
            $opInafecta += $imp;
        } else {
            // Fallback por si no viene por ID de tributo sino solo por código de exoneración
            $taxTypeCode = $this->valor($xp, './/cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cbc:TaxExemptionReasonCode', $i);
            if($taxTypeCode == '20'){
                 $opExonerada += $imp;
            } elseif(in_array($taxTypeCode, ['30', '31', '32', '33', '34', '35', '36', '40'])){
                 $opInafecta += $imp;
            }
        }
        
        // Buscar ICBPER por línea
        $icbperLine = $this->valor($xp, './/cac:TaxTotal/cac:TaxSubtotal[cac:TaxCategory/cac:TaxScheme/cbc:ID = "7152"]/cbc:TaxAmount', $i);
        if ($icbperLine !== '') {
            $icbper += (float)$icbperLine;
        }
    }
    
    // ================= OTROS TRIBUTOS Y CARGOS GLOBALES =================
    $otrosCargos   = (float) $this->valor($xp, '//cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount');
    $redondeo      = (float) $this->valor($xp, '//cac:LegalMonetaryTotal/cbc:PayableRoundingAmount');
    $total         = (float) $this->valor($xp, '//cac:LegalMonetaryTotal/cbc:PayableAmount');
    $otrosTributos = 0; // Inicializar

    // Sumar ISC global si existe
    $iscGlobal = $this->valor($xp, '//cac:TaxTotal/cac:TaxSubtotal[cac:TaxCategory/cac:TaxScheme/cbc:ID = "2000"]/cbc:TaxAmount');
    if($iscGlobal !== ''){
        $isc = (float) $iscGlobal;
    }


    // ================= DATOS DEL NEGOCIO (para logo y contacto) =================
    $rutaLogo = '';
    $nombreNegocio = '';
    $telefonoNegocio = '';
    $emailNegocio = '';

    $negocio = new Negocio();
    $rsptaN = $negocio->listar();
    if ($rsptaN) {
        $regN = $rsptaN->fetch_object();
        if ($regN) { // Ensure $regN is not null
            $nombreNegocio = $regN->nombre;
            $telefonoNegocio = $regN->telefono;
            $emailNegocio = $regN->email;
            
            if (!empty($regN->logo)) {
                $rutaFisica = __DIR__ . '/../reportes/' . $regN->logo;
                if (file_exists($rutaFisica)) {
                    $rutaLogo = $rutaFisica; // Usar la ruta absoluta del sistema de archivos
                }
            }
        }
    }

    // ================= TOTAL EN LETRAS =================
    $totalLetras = $this->valor($xp, '//cbc:Note');
    if(empty($totalLetras)){
        $totalLetras = $this->numeroALetras((float)$total);
    }

    $moneda = $this->valor($xp, '//cbc:DocumentCurrencyCode');
    switch ($moneda) {
        case 'PEN':
            $moneda = 'SOLES';
            break;
        case 'USD':
            $moneda = 'DÓLARES AMERICANOS';
            break;
        default:
            // Keep the original code if it's not PEN or USD
            break;
    }

    return [
        'logo' => $rutaLogo,
        'nombreNegocio' => $nombreNegocio, // New
        'telefonoNegocio' => $telefonoNegocio, // New
        'emailNegocio' => $emailNegocio, // New
        'empresa' => $empresa,
        'ruc' => $ruc,
        'direccion' => $direccion,
        'ciudad' => $ciudad,
        'distrito' => $distrito,
        'provincia' => $provincia,
        'pais' => $pais,
        'email' => $emailNegocio, // Use business email, not XML's customer email if it's there
        'tipoDocumento' => $tipoDocumento,
        'serie' => $serie,
        'fecha' => $fecha,
        'hora' => $hora,
        'cliente' => $cliente,
        'tipoDoc' => $tipoDoc,
        'numDoc' => $numDoc,
        'direccionCliente' => $direccionCliente,
        'detalle' => $detalle,
        'opGravada' => $opGravada,
        'opExonerada' => $opExonerada,
        'opInafecta' => $opInafecta,
        'igv' => $igv,
        'isc' => $isc,
        'icbper' => $icbper,
        'otrosTributos' => $otrosTributos,
        'otrosCargos' => $otrosCargos,
        'redondeo' => $redondeo,
        'total' => $total,
        'totalLetras' => $totalLetras,
        'moneda' => $moneda // New
    ];
}


}
