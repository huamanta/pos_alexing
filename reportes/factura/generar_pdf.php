<?php
// generar_pdf.php
// ESTE ARCHIVO SOLO DISPARA LA CONVERSIÓN DESDE XML

require_once __DIR__ . '/../modelos/Procesar.php';

$procesar = new Procesar();

// 1️⃣ recibir XML (por GET o POST)
$rutaXml = $_GET['xml'] ?? $_POST['xml'] ?? '';

if (empty($rutaXml)) {
    die('Error: no se recibió la ruta del XML');
}

// 2️⃣ normalizar ruta (seguridad básica)
$rutaXml = realpath($rutaXml);

if (!$rutaXml || !file_exists($rutaXml)) {
    die('Error: el archivo XML no existe');
}

// 3️⃣ generar PDF FACTURA A4 desde XML
$procesar->convertirXmlFacturaPdf($rutaXml);

// ⚠️ NO pongas nada después
exit;
