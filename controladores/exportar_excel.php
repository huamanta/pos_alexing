<?php
require "../configuraciones/Conexion.php";
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/* ---------------------------------------------------------
   Lectura de parámetros
--------------------------------------------------------- */
$inicio = $_GET['inicio'] ?? null;
$fin    = $_GET['fin']    ?? null;

if (!$inicio || !$fin) {
    die("Fechas no enviadas");
}

/* =========================================================
   DATOS DEL NEGOCIO
   ========================================================= */
$neg = ejecutarConsultaSimpleFila("SELECT * FROM datos_negocio LIMIT 1");
$nombre_negocio = $neg['nombre'] ?? '';
$ruc_negocio    = $neg['documento'] ?? '';
$direccion_negocio = $neg['direccion'] ?? '';
$telefono_negocio  = $neg['telefono'] ?? '';

/* =========================================================
   FUNCIONES AUXILIARES
   ========================================================= */
function crearEncabezado(Worksheet $sheet, $nombre_negocio, $ruc, $direccion, $telefono, $inicio, $fin)
{
    // Encabezado simple centrado (retorna fila inicial para datos)
    $sheet->mergeCells("A1:G1");
    $sheet->mergeCells("A2:G2");
    $sheet->mergeCells("A3:G3");
    $sheet->mergeCells("A4:G4");
    $sheet->setCellValue("A1", $nombre_negocio);
    $sheet->setCellValue("A2", "RUC: $ruc");
    $sheet->setCellValue("A3", $direccion);
    $sheet->setCellValue("A4", "Tel: $telefono");
    $sheet->mergeCells("A6:G6");
    $sheet->setCellValue("A6", "REPORTE DE TRABAJADORES — DEL $inicio AL $fin");

    $sheet->getStyle("A1:A4")->applyFromArray(['font' => ['bold' => true, 'size' => 11], 'alignment' => ['horizontal' => 'center']]);
    $sheet->getStyle("A6")->applyFromArray(['font' => ['bold' => true, 'size' => 13], 'alignment' => ['horizontal' => 'center']]);

    return 8; // fila inicial de datos
}

/* =========================================================
   OBTENER LISTA DE TRABAJADORES (que tengan asistencias o adelantos en el rango)
   ========================================================= */
$sql_trabajadores = "
SELECT DISTINCT p.idpersonal, p.nombre
FROM personal p
LEFT JOIN asistencias a ON a.idpersonal = p.idpersonal AND a.fecha BETWEEN '$inicio' AND '$fin'
LEFT JOIN movimiento m ON m.idpersonal = p.idpersonal AND m.descripcion LIKE '%Adelanto%' AND m.tipo = 'Egresos' AND DATE(m.fecha) BETWEEN '$inicio' AND '$fin'
WHERE a.idpersonal IS NOT NULL OR m.idpersonal IS NOT NULL
ORDER BY p.nombre
";
$rs_trab = ejecutarConsulta($sql_trabajadores);
$trabajadores = [];
while ($r = $rs_trab->fetch_assoc()) {
    $trabajadores[] = ['id' => $r['idpersonal'], 'nombre' => $r['nombre']];
}

/* Si no hay trabajadores -> salir */
if (empty($trabajadores)) {
    die("No hay registros de trabajadores en el rango seleccionado.");
}

/* =========================================================
   CREAMOS EL EXCEL
   ========================================================= */
$excel = new Spreadsheet();

/* ---------------------------------------------------------
   HOJA 1 - Resumen General (por trabajador)
--------------------------------------------------------- */
$hojaResumen = $excel->getActiveSheet();
$hojaResumen->setTitle("Resumen General");
$fila = crearEncabezado($hojaResumen, $nombre_negocio, $ruc_negocio, $direccion_negocio, $telefono_negocio, $inicio, $fin);

// Encabezados
$hojaResumen->setCellValue("A$fila", "Trabajador");
$hojaResumen->setCellValue("B$fila", "Días Trabajados");
$hojaResumen->setCellValue("C$fila", "Total Ganado");
$hojaResumen->setCellValue("D$fila", "Total Adelantos");
$hojaResumen->setCellValue("E$fila", "Total Neto");
$hojaResumen->getStyle("A$fila:E$fila")->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
    'alignment' => ['horizontal' => 'center']
]);
$fila++;

$totalG = 0.0; $totalA = 0.0; $totalDiasGlobal = 0;

/* Para acelerar, precalculamos totales por trabajador con queries agrupadas */
foreach ($trabajadores as $t) {
    $idp = $t['id'];

    // Total ganado (asistencias)
    $r1 = ejecutarConsulta("SELECT IFNULL(SUM(monto),0) AS total FROM asistencias WHERE idpersonal = '$idp' AND fecha BETWEEN '$inicio' AND '$fin' AND estado = 'asistio'")->fetch_assoc();
    $g = floatval($r1['total']);

    // Total adelantos (movimientos)
    $r2 = ejecutarConsulta("SELECT IFNULL(SUM(monto),0) AS total FROM movimiento WHERE idpersonal = '$idp' AND descripcion LIKE '%Adelanto%' AND tipo = 'Egresos' AND DATE(fecha) BETWEEN '$inicio' AND '$fin'")->fetch_assoc();
    $a = floatval($r2['total']);

    // Dias trabajados
    $r3 = ejecutarConsulta("SELECT COUNT(DISTINCT DATE(fecha)) AS dias FROM asistencias WHERE idpersonal = '$idp' AND fecha BETWEEN '$inicio' AND '$fin' AND estado = 'asistio'")->fetch_assoc();
    $d = intval($r3['dias']);

    $n = $g - $a;

    $hojaResumen->setCellValue("A$fila", $t['nombre']);
    $hojaResumen->setCellValue("B$fila", $d);
    $hojaResumen->setCellValue("C$fila", $g);
    $hojaResumen->setCellValue("D$fila", $a);
    $hojaResumen->setCellValue("E$fila", $n);

    $hojaResumen->getStyle("C$fila:E$fila")->getNumberFormat()->setFormatCode('"S/ "#,##0.00');

    $totalG += $g;
    $totalA += $a;
    $totalDiasGlobal += $d;
    $fila++;
}

/* Totales generales */
$hojaResumen->setCellValue("A$fila", "TOTAL GENERAL:");
$hojaResumen->setCellValue("B$fila", $totalDiasGlobal);
$hojaResumen->setCellValue("C$fila", $totalG);
$hojaResumen->setCellValue("D$fila", $totalA);
$hojaResumen->setCellValue("E$fila", $totalG - $totalA);
$hojaResumen->getStyle("A$fila:E$fila")->applyFromArray(['font' => ['bold' => true]]);
$hojaResumen->getStyle("C$fila:E$fila")->getNumberFormat()->setFormatCode('"S/ "#,##0.00');

$hojaResumen->getStyle("A8:E$fila")->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);
foreach (range('A','E') as $col) $hojaResumen->getColumnDimension($col)->setAutoSize(true);

/* =========================================================
   HOJAS POR TRABAJADOR: POR SEMANA (LUN - DOM)
   ========================================================= */
/* Utils: convertir a DateTime y obtener lunes de la semana */
$start = new DateTime($inicio);
$end   = new DateTime($fin);
$end->setTime(0,0,0);

// Normalizar start al LUNES de su semana (si quieres empezar la semana LUN)
$startMon = clone $start;
$wd = intval($startMon->format('N')); // 1 (Mon) - 7 (Sun)
if ($wd !== 1) {
    $startMon->modify('-' . ($wd - 1) . ' days'); // ir al Lunes anterior
}

// Iterar semanas
$periodStart = clone $startMon;
$oneWeek = new DateInterval('P7D');

foreach ($trabajadores as $tIndex => $t) {
    $hoja = new Worksheet($excel, \substr($t['nombre'],0,30)); // nombre de hoja limitado
    $excel->addSheet($hoja);
    $fila = crearEncabezado($hoja, $nombre_negocio, $ruc_negocio, $direccion_negocio, $telefono_negocio, $inicio, $fin);

    // Título trabajador
    $hoja->setCellValue("A$fila", "TRABAJADOR: " . $t['nombre']);
    $hoja->getStyle("A$fila")->applyFromArray(['font' => ['bold' => true]]);
    $fila += 2;

    // Recorremos semana por semana hasta pasar $fin
    $weekStart = clone $periodStart;
    while ($weekStart <= $end) {
        $weekEnd = (clone $weekStart)->modify('+6 days');

        // Calculamos rangos intersectados con [inicio, fin]
        $actualStart = max($weekStart->format('Y-m-d'), $start->format('Y-m-d'));
        $actualEnd   = min($weekEnd->format('Y-m-d'), $end->format('Y-m-d'));

        // Si no hay intersección con el rango inicial -> saltar
        if ($actualStart > $actualEnd) {
            $weekStart->add($oneWeek);
            continue;
        }

        // Imprimir cabecera de la semana
        $hoja->setCellValue("A$fila", "Semana: " . $weekStart->format('Y-m-d') . "  AL  " . $weekEnd->format('Y-m-d'));
        $hoja->getStyle("A$fila")->applyFromArray(['font' => ['bold' => true]]);
        $fila++;

        // Encabezado días LUN - DOM
        /* ---------------------------------------------------------
   NUEVO CUADRO DE DÍAS — VERTICAL (IZQUIERDA)
--------------------------------------------------------- */

/* ---------------------------------------------------------
   CUADRO SEMANA: DÍAS Y ADELANTOS (ALINEADOS Y LIMPIOS)
--------------------------------------------------------- */

$dias = ["LUNES","MARTES","MIERCOLES","JUEVES","VIERNES","SABADO","DOMINGO"];
$montosSemana = [];
$totalIngresoSemana = 0.0;

// Guardamos la fila inicial para ambos cuadros
$filaDiasInicio = $fila;
$filaAdelInicio  = $fila;

// -------------------- CUADRO DÍAS (IZQUIERDA) --------------------
$hoja->setCellValue("A{$fila}", "DÍA");
$hoja->setCellValue("B{$fila}", "MONTO S/");

// Estilo encabezado días
$hoja->getStyle("A{$fila}:B{$fila}")->applyFromArray([
    'font' => ['bold' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BDD7EE']],
    'alignment' => ['horizontal' => 'center']
]);

$fila++;
$inicioFilaDias = $fila;

for ($i = 0; $i < 7; $i++) {
    $d = (clone $weekStart)->modify("+$i days");
    $dStr = $d->format('Y-m-d');

    $hoja->setCellValue("A{$fila}", $dias[$i]);

    if ($dStr < $inicio || $dStr > $fin) {
        $hoja->setCellValue("B{$fila}", "");
        $montosSemana[$i] = 0;
    } else {
        $idp = $t['id'];
        $rq = ejecutarConsulta("SELECT IFNULL(SUM(monto),0) AS total 
                                FROM asistencias 
                                WHERE idpersonal='$idp' 
                                  AND DATE(fecha)='$dStr' 
                                  AND estado='asistio'")->fetch_assoc();
        $m = floatval($rq['total']);
        $montosSemana[$i] = $m;

        // Mostrar vacío si 0
        $hoja->setCellValue("B{$fila}", $m);
        $hoja->getStyle("B{$fila}")->getNumberFormat()->setFormatCode('"S/ "#,##0.00');
        $totalIngresoSemana += $m;
    }

    $fila++;
}

$ultimaFilaDias = $fila - 1;

// Bordes y alineación derecha en montos
$hoja->getStyle("A{$inicioFilaDias}:B{$ultimaFilaDias}")
     ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
$hoja->getStyle("B{$inicioFilaDias}:B{$ultimaFilaDias}")
     ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

// -------------------- CUADRO ADELANTOS (DERECHA) --------------------
$colA = "D"; // Fecha
$colB = "E"; // Descripción
$colC = "F"; // Importe

$filaAd = $filaDiasInicio; // arrancar en la misma fila que el encabezado días

// Encabezado adelantos
$hoja->setCellValue("{$colA}{$filaAd}", "FECHA");
$hoja->setCellValue("{$colB}{$filaAd}", "DESCRIPCIÓN");
$hoja->setCellValue("{$colC}{$filaAd}", "IMPORTE S/");

$hoja->getStyle("{$colA}{$filaAd}:{$colC}{$filaAd}")->applyFromArray([
    'font' => ['bold' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BDD7EE']],
    'alignment' => ['horizontal' => 'center']
]);

$filaAd++;
$inicioFilaAdel = $filaAd;

$qAd = "
SELECT fecha, descripcion, monto 
FROM movimiento
WHERE idpersonal='{$t['id']}' 
  AND descripcion LIKE '%Adelanto%'
  AND tipo='Egresos'
  AND DATE(fecha) BETWEEN '{$weekStart->format('Y-m-d')}' AND '{$weekEnd->format('Y-m-d')}'
ORDER BY fecha
";

$rsAd = ejecutarConsulta($qAd);
$totalAdelSemana = 0.0;

if ($rsAd->num_rows > 0) {
    while ($ad = $rsAd->fetch_assoc()) {
        $hoja->setCellValue("{$colA}{$filaAd}", date('d/m/Y', strtotime($ad['fecha'])));
        $hoja->setCellValue("{$colB}{$filaAd}", $ad['descripcion']);
        $hoja->setCellValue("{$colC}{$filaAd}", $ad['monto']);
        $hoja->getStyle("{$colC}{$filaAd}")->getNumberFormat()->setFormatCode('"S/ "#,##0.00');
        $totalAdelSemana += floatval($ad['monto']);
        $filaAd++;
    }
} else {
    $hoja->setCellValue("{$colA}{$filaAd}", "—");
    $hoja->setCellValue("{$colB}{$filaAd}", "Sin adelantos");
    $hoja->setCellValue("{$colC}{$filaAd}", "");
    $filaAd++;
}

$ultimaFilaAdel = $filaAd - 1;

// Bordes solo donde hay datos y alinear importes a la derecha
$hoja->getStyle("{$colA}{$inicioFilaAdel}:{$colC}{$ultimaFilaAdel}")
     ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
$hoja->getStyle("{$colC}{$inicioFilaAdel}:{$colC}{$ultimaFilaAdel}")
     ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

// -------------------- TOTAL ADELANTOS --------------------
$filaTot = $ultimaFilaAdel + 1;

$hoja->setCellValue("{$colA}{$filaTot}", "TOTAL ADELANTOS S/");
$hoja->setCellValue("{$colC}{$filaTot}", $totalAdelSemana);
$hoja->getStyle("{$colC}{$filaTot}")->getNumberFormat()->setFormatCode('"S/ "#,##0.00');

// Fondo gris y negrita para total
$hoja->getStyle("{$colA}{$filaTot}:{$colC}{$filaTot}")->applyFromArray([
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9D9D9']],
    'font' => ['bold' => true]
]);

// Bordes del total
$hoja->getStyle("{$colA}{$filaTot}:{$colC}{$filaTot}")
     ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Ajustar la siguiente fila para seguir (separador de 2 filas)
$fila = max($ultimaFilaDias, $filaTot) + 2;


        /* ---------------------------------------------------------
           CÁLCULOS FINALES (según lo pedido)
           - Dscto 20% tienda
           - Neto a pagar
           - Adelantos (ya calculado)
           - A cuenta (igual a adelantos)
           - Entregado en efectivo = neto - adelantos
           --------------------------------------------------------- */
        $dscto = $totalIngresoSemana * 0.20;
        $neto  = $totalIngresoSemana - $dscto;
        $adel  = $totalAdelSemana; // por definición
        $a_cuenta = $adel;
        $entregado_efectivo = $neto - $adel;

        $hoja->setCellValue("A{$fila}", "DSCTO 20% - TIENDA");
        $hoja->setCellValue("B{$fila}", $dscto);
        $hoja->getStyle("B{$fila}")->getNumberFormat()->setFormatCode('"S/ "#,##0.00');
        $fila++;

        $hoja->setCellValue("A{$fila}", "NETO A PAGAR S/");
        $hoja->setCellValue("B{$fila}", $neto);
        $hoja->getStyle("B{$fila}")->getNumberFormat()->setFormatCode('"S/ "#,##0.00');
        $fila++;

        $hoja->setCellValue("A{$fila}", "ADELANTOS S/");
        $hoja->setCellValue("B{$fila}", $adel);
        $hoja->getStyle("B{$fila}")->getNumberFormat()->setFormatCode('"S/ "#,##0.00');
        $fila++;

        $hoja->setCellValue("A{$fila}", "A CUENTA S/");
        //$hoja->setCellValue("B{$fila}", $a_cuenta);
        $hoja->setCellValue("B{$fila}", 0);
        $hoja->getStyle("B{$fila}")->getNumberFormat()->setFormatCode('"S/ "#,##0.00');
        $fila++;

        $hoja->setCellValue("A{$fila}", "ENTREGADO EN EFECTIVO S/");
        $hoja->setCellValue("B{$fila}", $entregado_efectivo);
        $hoja->getStyle("B{$fila}")->getNumberFormat()->setFormatCode('"S/ "#,##0.00');
        $fila += 2;

        
        // Separador entre semanas
        $fila += 1;

        // Avanzar a la siguiente semana
        $weekStart->add($oneWeek);
    } // end semanas

    // Auto-ajustar columnas y terminar hoja
    foreach (range('A', 'G') as $col) $hoja->getColumnDimension($col)->setAutoSize(true);
}

/* Quitamos la hoja por defecto si la hojaResumen no es la activa (ya la dejamos como hoja 0) */
$excel->setActiveSheetIndex(0);

/* =========================================================
   DESCARGAR EL ARCHIVO
   ========================================================= */
$fecha_hoy = date('Y-m-d');
$filename = "reporte_trabajadores_{$fecha_hoy}.xlsx";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

$writer = new Xlsx($excel);
$writer->save("php://output");
exit;
