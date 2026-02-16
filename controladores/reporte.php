<?php 
require_once "../modelos/Reporte.php";

$reporte = new ReporteConsolidado();

// Recibir parámetros
$fecha_inicio = isset($_POST["fecha_inicio"]) ? $_POST["fecha_inicio"] : "";
$fecha_fin    = isset($_POST["fecha_fin"]) ? $_POST["fecha_fin"] : "";
$idsucursal   = isset($_POST["idsucursal2"]) ? $_POST["idsucursal2"] : "";

switch ($_GET["op"]) {
    case 'listar':
        $data = [];

        // Llamadas a los métodos de listar
        $ventas    = $reporte->listarVentasDetalle($fecha_inicio, $fecha_fin, $idsucursal);
        $compras   = $reporte->listarComprasDetalle($fecha_inicio, $fecha_fin, $idsucursal);
        $ingresos  = $reporte->listarIngresosDetalle($fecha_inicio, $fecha_fin, $idsucursal);
        $egresos   = $reporte->listarEgresosDetalle($fecha_inicio, $fecha_fin, $idsucursal);
        $resumen   = $reporte->resumenConsolidado($fecha_inicio, $fecha_fin, $idsucursal);
        $resumen_meses = $reporte->resumenPorMeses($fecha_inicio, $fecha_fin, $idsucursal);
        $empresa = $reporte->obtenerDatosNegocio(); // crea o usa el método que ya tengas

        // Obtener las amortizaciones
        $amortizaciones = $reporte->listarAmortizaciones($fecha_inicio, $fecha_fin, $idsucursal); // Método que crearé en el modelo

        // Responder con todos los datos incluyendo amortizaciones
        echo json_encode([
            "ventas"         => $ventas,
            "compras"        => $compras,
            "ingresos"       => $ingresos,
            "egresos"        => $egresos,
            "resumen"        => $resumen,
            "resumen_meses"  => $resumen_meses,
            "amortizaciones" => $amortizaciones,  // Asegúrate de incluir este campo
            "empresa" => $empresa
        ]);

    break;
}

?>
