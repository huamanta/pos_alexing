<?php
ob_start();
if (strlen(session_id()) < 1) session_start();
if (!isset($_SESSION["nombre"])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
    exit;
}
if ($_SESSION['almacen'] != 1) {
    echo 'No tiene permiso para visualizar el reporte';
    exit;
}
require('PDF_MC_Table.php');
require_once "../modelos/Producto.php";

$pdf = new PDF_MC_Table();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// ================= ENCABEZADO MEJORADO =================
// Título principal
$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 10, utf8_decode('REPORTE DE INVERSIÓN'), 0, 1, 'C');

// Subtítulo
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(52, 58, 64);
$pdf->Cell(0, 8, utf8_decode('Análisis por Producto'), 0, 1, 'C');

// Línea decorativa
$pdf->SetDrawColor(52, 152, 219);
$pdf->SetLineWidth(0.8);
$pdf->Line(20, $pdf->GetY() + 2, 190, $pdf->GetY() + 2);
$pdf->Ln(6);

// Información de contexto
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(108, 117, 125);
$pdf->Cell(95, 5, utf8_decode('Usuario: ' . $_SESSION["nombre"]), 0, 0, 'L');
$pdf->Cell(95, 5, 'Fecha: ' . date('d/m/Y'), 0, 1, 'R');
$pdf->Cell(95, 5, utf8_decode('Departamento: Almacén'), 0, 0, 'L');
$pdf->Cell(95, 5, 'Hora: ' . date('H:i:s'), 0, 1, 'R');
$pdf->Ln(8);

// ================= RESUMEN EJECUTIVO =================
// Calcular totales primero
$producto = new Producto();
$rspta_preview = $producto->listarProductosCompra();
$totalProductos = 0;
$inversionTotal = 0;

while ($reg = $rspta_preview->fetch_object()) {
    $totalProductos++;
    $inversionTotal += ($reg->stock * $reg->precio_compra);
}

// Cuadro de resumen
$pdf->SetFillColor(248, 249, 250);
$pdf->SetDrawColor(222, 226, 230);
$pdf->SetLineWidth(0.3);
$pdf->Rect(15, $pdf->GetY(), 180, 22, 'FD');

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(52, 58, 64);
$pdf->SetXY(20, $pdf->GetY() + 4);
$pdf->Cell(80, 6, utf8_decode('Total de Productos:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, number_format($totalProductos, 0), 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetX(20);
$pdf->Cell(80, 6, utf8_decode('Inversión Total:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(40, 167, 69);
$pdf->Cell(0, 6, 'S/ ' . number_format($inversionTotal, 2), 0, 1, 'L');

$pdf->Ln(10);

// ================= CABECERA TABLA MEJORADA =================
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(52, 152, 219);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetDrawColor(52, 152, 219);
$pdf->SetLineWidth(0.3);

$pdf->Cell(60, 9, 'PRODUCTO', 1, 0, 'C', true);
$pdf->Cell(35, 9, utf8_decode('CATEGORÍA'), 1, 0, 'C', true);
$pdf->Cell(22, 9, 'STOCK', 1, 0, 'C', true);
$pdf->Cell(28, 9, 'P. COMPRA', 1, 0, 'C', true);
$pdf->Cell(35, 9, utf8_decode('INVERSIÓN'), 1, 1, 'C', true);

// ================= CUERPO TABLA CON FILAS ALTERNADAS =================
$pdf->SetWidths([60, 35, 22, 28, 35]);
$pdf->SetFont('Arial', '', 8.5);
$pdf->SetTextColor(33, 37, 41);
$pdf->SetDrawColor(222, 226, 230);

// Resetear consulta
$rspta = $producto->listarProductosCompra();
$totalGeneral = 0;
$contador = 0;

while ($reg = $rspta->fetch_object()) {
    $total = $reg->stock * $reg->precio_compra;
    $totalGeneral += $total;
    $contador++;
    
    // Filas alternadas para mejor legibilidad
    if ($contador % 2 == 0) {
        $pdf->SetFillColor(248, 249, 250);
    } else {
        $pdf->SetFillColor(255, 255, 255);
    }
    
    $pdf->Row([
        utf8_decode($reg->nombre . ' - ' . $reg->unidadmedida),
        utf8_decode($reg->categoria),
        number_format($reg->stock, 0),
        'S/ ' . number_format($reg->precio_compra, 2),
        'S/ ' . number_format($total, 2)
    ]);
}

// ================= TOTAL CON DISEÑO MEJORADO =================
$pdf->Ln(1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(233, 236, 239);
$pdf->SetTextColor(52, 58, 64);
$pdf->SetDrawColor(52, 152, 219);
$pdf->SetLineWidth(0.5);

$pdf->Cell(145, 9, utf8_decode('INVERSIÓN TOTAL EN ALMACÉN'), 1, 0, 'R', true);

$pdf->SetFillColor(40, 167, 69);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(35, 9, 'S/ ' . number_format($totalGeneral, 2), 1, 1, 'C', true);

// ================= PIE DE PÁGINA =================
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(108, 117, 125);
$pdf->MultiCell(0, 4, utf8_decode(
    "Este reporte muestra la inversión actual en productos de almacén, calculada multiplicando el stock disponible por el precio de compra unitario. " .
    "Los valores mostrados representan el capital invertido en inventario a la fecha de generación del reporte."
), 0, 'L');

// Línea final
$pdf->Ln(3);
$pdf->SetDrawColor(222, 226, 230);
$pdf->SetLineWidth(0.3);
$pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());

// Nota final
$pdf->Ln(3);
$pdf->SetFont('Arial', '', 7);
$pdf->SetTextColor(134, 142, 150);
$pdf->Cell(0, 4, utf8_decode('Documento generado automáticamente - Sistema de Gestión de Inventario'), 0, 1, 'C');
$pdf->Cell(0, 4, 'Página ' . $pdf->PageNo(), 0, 0, 'C');

$pdf->Output('I', 'Reporte_Inversion_' . date('Y-m-d') . '.pdf');
ob_end_flush();