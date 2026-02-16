<?php
require_once "../modelos/Procesar.php";

$procesar = new Procesar();

switch ($_GET["op"]) {

    case 'convertir_xml':

        if (!isset($_FILES['xml'])) {
            echo "<center>Error: No se envió ningún archivo</center>";
            return;
        }

        $formato = $_POST['formato'] ?? 'html'; // html | pdf

        $archivo = $_FILES['xml']['tmp_name'];
        $extension = strtolower(pathinfo($_FILES['xml']['name'], PATHINFO_EXTENSION));

        if ($extension !== 'xml') {
            echo "<center>El archivo no es XML</center>";
            return;
        }

        // 👉 HTML (actual)
        if ($formato === 'html') {

            $html = $procesar->convertirXmlTicket($archivo);
            echo $html;
            return;
        }
        // 👉 PDF
        if ($formato === 'pdf') {
            $procesar->convertirXmlFacturaPdf($archivo);
            return;
        }

    break;

}
