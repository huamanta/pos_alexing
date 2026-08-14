<?php
require_once __DIR__ . '/../../configuraciones/bootstrap.php';
require_once __DIR__ . '/../../configuraciones/Conexion.php';
require_once __DIR__ . '/../Helpers.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('America/Lima');

$inicio = $_GET['fecha_inicio'] ?? null;
$fin = $_GET['fecha_fin'] ?? null;
$idsucursal = $_SESSION['idsucursal'];
$idcliente = $_GET['idcliente'] ?? '';

if (!$inicio || !$fin) {
    die('Debe enviar los parámetros inicio y fin.');
}

$helpers = new Helpers();
$negocio = $helpers->dataSucursal($idsucursal);
$razonSocialNegocio = $negocio['razon_social'] ?? 'Empresa';
$nombreNegocio = $negocio['nombre'] ?? 'Empresa';
$rucNegocio = $negocio['ruc'] ?? '';
$direccionNegocio = $negocio['direccion'] ?? '';
$telefonoNegocio = $negocio['telefono'] ?? '';

$usuarioGenera = $_SESSION['nombre'] ?? 'Sistema';

$clienteFiltro = '';
if (!empty($idcliente) && $idcliente !== 'Todos') {
    $cliente = ejecutarConsultaSimpleFila("SELECT nombre FROM persona WHERE idpersona = '$idcliente' LIMIT 1");
    $clienteFiltro = $cliente['nombre'] ?? $idcliente;
}

$condiciones = [];
if (!empty($idsucursal) && $idsucursal !== 'Todos') {
    $condiciones[] = "v.idsucursal = '$idsucursal'";
}
if (!empty($idcliente) && $idcliente !== 'Todos') {
    $condiciones[] = "v.idcliente = '$idcliente'";
}
$condicionSql = !empty($condiciones) ? ' AND ' . implode(' AND ', $condiciones) : '';

$sqlDetalle = "
SELECT
    cc.idcpc,
    cc.estado_pago,
    cc.deuda_base,
    cc.interes,
    cc.mora,
    cc.descuento,
    cc.deudatotal,
    cc.abonototal,
    cc.fechavencimiento,
    cc.deuda,
    v.idventa,
    v.idcliente,
    CONCAT(cp.nombre, ' ', v.serie_comprobante, '-', v.num_comprobante) AS comprobante,
    DATE(v.fecha_hora) AS fecha_venta,
    c.nombre AS cliente,
    c.num_documento AS documento,
    c.telefono,
    c.direccion,
    DATEDIFF(CURDATE(), cc.fechavencimiento) AS dias_atraso,
    (SELECT MAX(dcp.fechapago) FROM detalle_cuentas_por_cobrar dcp WHERE dcp.idcpc = cc.idcpc) AS ultimo_pago,
    (SELECT dcp.formapago FROM detalle_cuentas_por_cobrar dcp WHERE dcp.idcpc = cc.idcpc ORDER BY dcp.iddcpc DESC LIMIT 1) AS forma_pago,
    (SELECT dcp.observacion FROM detalle_cuentas_por_cobrar dcp WHERE dcp.idcpc = cc.idcpc ORDER BY dcp.iddcpc DESC LIMIT 1) AS ultima_observacion,
    (SELECT p.nombre FROM detalle_cuentas_por_cobrar dcp LEFT JOIN personal p ON p.idpersonal = dcp.idpersonal WHERE dcp.idcpc = cc.idcpc ORDER BY dcp.iddcpc DESC LIMIT 1) AS gestor,
    (SELECT r.observacion FROM compromiso_pago r WHERE r.idcpc = cc.idcpc ORDER BY r.idcompromiso_pago DESC LIMIT 1) AS ultimo_compromiso,
    GREATEST((cc.deudatotal - cc.abonototal), 0) AS saldo_pendiente
FROM cuentas_por_cobrar cc
INNER JOIN venta v ON v.idventa = cc.idventa
INNER JOIN persona c ON c.idpersona = v.idcliente
LEFT JOIN comp_pago cp ON cp.idcomprobante_pago = v.idcomprobante_pago
WHERE DATE(cc.fecharegistro) BETWEEN '$inicio' AND '$fin'
$condicionSql
ORDER BY cc.fechavencimiento ASC, cc.idcpc ASC";

$detalle = ejecutarConsulta($sqlDetalle);
$rows = [];
while ($row = $detalle->fetch_assoc()) {
    $rows[] = $row;
}

$rowsAgrupados = [];
foreach ($rows as $row) {
    $clienteKey = (string) ($row['idcliente'] ?? $row['cliente'] ?? 'sin-cliente');
    if (!isset($rowsAgrupados[$clienteKey])) {
        $rowsAgrupados[$clienteKey] = [
            'cliente' => $row['cliente'] ?? 'Sin cliente',
            'documento' => $row['documento'] ?? '',
            'telefono' => $row['telefono'] ?? '',
            'direccion' => $row['direccion'] ?? '',
            'filas' => []
        ];
    }
    $rowsAgrupados[$clienteKey]['filas'][] = $row;
}
ksort($rowsAgrupados);

$summary = ejecutarConsultaSimpleFila("
SELECT
    COUNT(DISTINCT v.idcliente) AS total_clientes,
    COUNT(DISTINCT cc.idventa) AS total_creditos,
    COUNT(*) AS total_cuotas,
    SUM(COALESCE(cc.deuda_base, cc.deudatotal, 0)) AS capital_financiado,
    SUM(COALESCE(cc.abonototal, 0)) AS capital_cobrado,
    SUM(COALESCE(cc.interes, 0)) AS intereses_cobrados,
    SUM(COALESCE(cc.mora, 0) + COALESCE(cc.mora_pagada, 0)) AS mora_generada,
    SUM(COALESCE(cc.mora_pagada, 0)) AS mora_cobrada,
    SUM(COALESCE(cc.descuento, 0)) AS descuentos_aplicados,
    SUM(GREATEST(COALESCE(cc.deudatotal, 0) - COALESCE(cc.abonototal, 0), 0)) AS saldo_pendiente,
    SUM(CASE WHEN COALESCE(cc.deudatotal, 0) - COALESCE(cc.abonototal, 0) > 0 AND DATEDIFF(CURDATE(), cc.fechavencimiento) > 0 THEN 1 ELSE 0 END) AS clientes_morosos,
    SUM(CASE WHEN COALESCE(cc.deudatotal, 0) - COALESCE(cc.abonototal, 0) <= 0 THEN 1 ELSE 0 END) AS creditos_cancelados,
    SUM(CASE WHEN COALESCE(cc.deudatotal, 0) - COALESCE(cc.abonototal, 0) > 0 THEN 1 ELSE 0 END) AS creditos_vigentes
FROM cuentas_por_cobrar cc
INNER JOIN venta v ON v.idventa = cc.idventa
WHERE DATE(cc.fecharegistro) BETWEEN '$inicio' AND '$fin'
$condicionSql
");

$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
    ->setTitle('Reporte de Cuentas por Cobrar')
    ->setCreator($usuarioGenera)
    ->setCompany($nombreNegocio);

$sheetResumen = $spreadsheet->getActiveSheet();
$sheetResumen->setTitle('Resumen General');

$sheetResumen->mergeCells('A1:L1');
$sheetResumen->mergeCells('A2:L2');
$sheetResumen->mergeCells('A3:L3');
$sheetResumen->setCellValue('A1', $razonSocialNegocio);
$sheetResumen->setCellValue('A2', 'REPORTE DE CUENTAS POR COBRAR DETALLADO');
$sheetResumen->setCellValue('A3', 'Generado el ' . date('d/m/Y H:i:s') . ' por ' . $usuarioGenera);
$sheetResumen->setCellValue('A5', 'Sucursal');
$sheetResumen->setCellValue('B5', $nombreNegocio ?: 'Todas');
$sheetResumen->setCellValue('A6', 'Cliente');
$sheetResumen->setCellValue('B6', $clienteFiltro ?: 'Todos');
$sheetResumen->setCellValue('A7', 'Rango');
$sheetResumen->setCellValue('B7', $inicio . ' al ' . $fin);
$sheetResumen->setCellValue('A8', 'Filtros');
$sheetResumen->setCellValue('B8', 'Fecha de registro / Sucursal / Cliente');

$sheetResumen->getStyle('A1:A3')->applyFromArray([
    'font' => ['bold' => true, 'size' => 13],
    'alignment' => ['horizontal' => 'center']
]);
$sheetResumen->getStyle('A5:B8')->applyFromArray([
    'font' => ['bold' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9EAF7']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
]);

$summaryRow = 10;
$sheetResumen->setCellValue('A' . $summaryRow, 'Indicador');
$sheetResumen->setCellValue('B' . $summaryRow, 'Valor');
$sheetResumen->getStyle('A' . $summaryRow . ':B' . $summaryRow)->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
    'alignment' => ['horizontal' => 'center']
]);
$summaryRow++;

$summaryItems = [
    ['Total de clientes', $summary['total_clientes'] ?? 0],
    ['Total de créditos', $summary['total_creditos'] ?? 0],
    ['Total de cuotas', $summary['total_cuotas'] ?? 0],
    ['Capital financiado', (float) ($summary['capital_financiado'] ?? 0)],
    ['Capital cobrado', (float) ($summary['capital_cobrado'] ?? 0)],
    ['Intereses cobrados', (float) ($summary['intereses_cobrados'] ?? 0)],
    ['Mora generada', (float) ($summary['mora_generada'] ?? 0)],
    ['Mora cobrada', (float) ($summary['mora_cobrada'] ?? 0)],
    ['Descuentos aplicados', (float) ($summary['descuentos_aplicados'] ?? 0)],
    ['Saldo pendiente', (float) ($summary['saldo_pendiente'] ?? 0)],
    ['Clientes morosos', (int) ($summary['clientes_morosos'] ?? 0)],
    ['Créditos cancelados', (int) ($summary['creditos_cancelados'] ?? 0)],
    ['Créditos vigentes', (int) ($summary['creditos_vigentes'] ?? 0)],
];

foreach ($summaryItems as $idx => $item) {
    $sheetResumen->setCellValue('A' . ($summaryRow + $idx), $item[0]);
    $sheetResumen->setCellValue('B' . ($summaryRow + $idx), $item[1]);
    if (is_numeric($item[1])) {
        $sheetResumen->getStyle('B' . ($summaryRow + $idx))->getNumberFormat()->setFormatCode('"S/ "#,##0.00');
    }
}
$sheetResumen->getStyle('A' . ($summaryRow) . ':B' . ($summaryRow + count($summaryItems) - 1))
    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
$sheetResumen->getColumnDimension('A')->setWidth(28);
$sheetResumen->getColumnDimension('B')->setWidth(22);
$sheetResumen->setAutoFilter('A10:B' . ($summaryRow + count($summaryItems) - 1));

$sheetDetalle = $spreadsheet->createSheet();
$sheetDetalle->setTitle('Detalle Cuentas por Cobrar');

$headers = [
    'N°', 'Estado', 'Cliente', 'Documento', 'Teléfono', 'Dirección', 'Venta', 'Comprobante', 'Fecha Venta',
    'N° Cuota', 'Fecha Vencimiento', 'Días Atraso', 'Capital', 'Interés', 'Mora', 'Descuento', 'Total Cuota',
    'Total Abonado', 'Saldo Pendiente', 'Último Pago', 'Forma de Pago', 'Último Compromiso', 'Estado Compromiso',
    'Monto Comprometido', 'Gestor de Cobranza', 'Última Observación'
];

$sheetDetalle->mergeCells('A1:Z1');
$sheetDetalle->setCellValue('A1', 'DETALLE DE CUENTAS POR COBRAR');
$sheetDetalle->getStyle('A1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
    'alignment' => ['horizontal' => 'center']
]);
$sheetDetalle->setCellValue('A2', 'Empresa');
$sheetDetalle->setCellValue('B2', $razonSocialNegocio);
$sheetDetalle->setCellValue('A3', 'Sucursal');
$sheetDetalle->setCellValue('B3', $nombreNegocio ?: 'Todas');
$sheetDetalle->setCellValue('A4', 'Rango');
$sheetDetalle->setCellValue('B4', $inicio . ' al ' . $fin);
$sheetDetalle->setCellValue('A5', 'Usuario');
$sheetDetalle->setCellValue('B5', $usuarioGenera);

$startRow = 7;
$sheetDetalle->fromArray($headers, null, 'A' . $startRow);
$sheetDetalle->getStyle('A' . $startRow . ':Z' . $startRow)->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2F5597']],
    'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
]);

$rowIdx = $startRow + 1;
foreach ($rowsAgrupados as $clienteGrupo) {
    $sheetDetalle->setCellValue('A' . $rowIdx, 'CLIENTE: ' . $clienteGrupo['cliente']);
    $sheetDetalle->setCellValue('B' . $rowIdx, 'DOC: ' . ($clienteGrupo['documento'] ?: '-'));
    $sheetDetalle->setCellValue('C' . $rowIdx, 'TEL: ' . ($clienteGrupo['telefono'] ?: '-'));
    $sheetDetalle->mergeCells('A' . $rowIdx . ':Z' . $rowIdx);
    $sheetDetalle->getStyle('A' . $rowIdx . ':Z' . $rowIdx)->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    $rowIdx++;

    $cuotasPorVenta = [];
    $subtotalCapital = 0;
    $subtotalAbonado = 0;
    $subtotalSaldo = 0;
    $ventasAgrupadas = [];
    foreach ($clienteGrupo['filas'] as $row) {
        $estadoCuota = 'Vigente';
        $saldo = (float) ($row['saldo_pendiente'] ?? 0);
        $dias = (int) ($row['dias_atraso'] ?? 0);
        if ($saldo <= 0) {
            $estadoCuota = 'Cancelada';
        } elseif ($dias > 0) {
            $estadoCuota = 'Vencida';
        } elseif ($dias >= -3) {
            $estadoCuota = 'Próxima a vencer';
        }

        $compromiso = trim((string) ($row['ultimo_compromiso'] ?? ''));
        if (!empty($compromiso)) {
            $estadoCuota = 'Compromiso pendiente';
        }

        $ventaKey = (string) ($row['idventa'] ?? 'sin-venta');
        if (!isset($cuotasPorVenta[$ventaKey])) {
            $cuotasPorVenta[$ventaKey] = 0;
            $ventasAgrupadas[$ventaKey] = [
                'idventa' => $row['idventa'] ?? '',
                'comprobante' => $row['comprobante'] ?? '',
                'capital' => 0,
                'abonado' => 0,
                'saldo' => 0,
                'cuotas' => 0
            ];
        }
        $cuotasPorVenta[$ventaKey]++;
        $ventasAgrupadas[$ventaKey]['capital'] += (float) ($row['deuda_base'] ?? 0);
        $ventasAgrupadas[$ventaKey]['abonado'] += (float) ($row['abonototal'] ?? 0);
        $ventasAgrupadas[$ventaKey]['saldo'] += (float) ($row['saldo_pendiente'] ?? 0);
        $ventasAgrupadas[$ventaKey]['cuotas']++;

        $subtotalCapital += (float) ($row['deuda_base'] ?? 0);
        $subtotalAbonado += (float) ($row['abonototal'] ?? 0);
        $subtotalSaldo += (float) ($row['saldo_pendiente'] ?? 0);

        $rowValues = [
            $cuotasPorVenta[$ventaKey],
            $estadoCuota,
            $row['cliente'] ?? '',
            $row['documento'] ?? '',
            $row['telefono'] ?? '',
            $row['direccion'] ?? '',
            $row['idventa'] ?? '',
            $row['comprobante'] ?? '',
            $row['fecha_venta'] ?? '',
            $row['idcpc'] ?? '',
            $row['fechavencimiento'] ?? '',
            $row['dias_atraso'] ?? '',
            (float) ($row['deuda_base'] ?? 0),
            (float) ($row['interes'] ?? 0),
            (float) ($row['mora'] ?? 0),
            (float) ($row['descuento'] ?? 0),
            (float) ($row['deudatotal'] ?? 0),
            (float) ($row['abonototal'] ?? 0),
            (float) ($row['saldo_pendiente'] ?? 0),
            $row['ultimo_pago'] ?? '',
            $row['forma_pago'] ?? '',
            $compromiso,
            !empty($compromiso) ? 'Pendiente' : '',
            !empty($compromiso) ? 0 : '',
            $row['gestor'] ?? '',
            $row['ultima_observacion'] ?? ''
        ];

        $sheetDetalle->fromArray($rowValues, null, 'A' . $rowIdx);

        if ($estadoCuota === 'Cancelada') {
            $fill = 'C6EFCE';
        } elseif ($estadoCuota === 'Próxima a vencer') {
            $fill = 'FFF2CC';
        } elseif ($estadoCuota === 'Vencida' || $estadoCuota === 'Compromiso pendiente') {
            $fill = $estadoCuota === 'Compromiso pendiente' ? 'DDEBF7' : 'F4CCCC';
        } elseif ($estadoCuota === 'Refinanciado') {
            $fill = 'D9D9D9';
        } else {
            $fill = 'FFFFFF';
        }

        $sheetDetalle->getStyle('A' . $rowIdx . ':Z' . $rowIdx)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fill]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        foreach (['M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'] as $col) {
            $sheetDetalle->getStyle($col . $rowIdx)->getNumberFormat()->setFormatCode('"S/ "#,##0.00');
        }

        $sheetDetalle->getStyle('K' . $rowIdx)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $sheetDetalle->getStyle('J' . $rowIdx)->getNumberFormat()->setFormatCode('0');
        $sheetDetalle->getStyle('L' . $rowIdx)->getNumberFormat()->setFormatCode('0');
        $rowIdx++;
    }

    foreach ($ventasAgrupadas as $ventaAgrupada) {
        $sheetDetalle->setCellValue('A' . $rowIdx, 'VENTA ' . ($ventaAgrupada['idventa'] ?: '-'));
        $sheetDetalle->setCellValue('B' . $rowIdx, $ventaAgrupada['comprobante'] ?: '-');
        $sheetDetalle->setCellValue('C' . $rowIdx, 'Cuotas: ' . $ventaAgrupada['cuotas']);
        $sheetDetalle->setCellValue('M' . $rowIdx, $ventaAgrupada['capital']);
        $sheetDetalle->setCellValue('R' . $rowIdx, $ventaAgrupada['abonado']);
        $sheetDetalle->setCellValue('S' . $rowIdx, $ventaAgrupada['saldo']);
        $sheetDetalle->mergeCells('A' . $rowIdx . ':L' . $rowIdx);
        $sheetDetalle->getStyle('A' . $rowIdx . ':S' . $rowIdx)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDE9D9']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        $sheetDetalle->getStyle('M' . $rowIdx . ':S' . $rowIdx)->getNumberFormat()->setFormatCode('"S/ "#,##0.00');
        $rowIdx++;
    }

    $sheetDetalle->setCellValue('A' . $rowIdx, 'SUBTOTAL CLIENTE');
    $sheetDetalle->setCellValue('M' . $rowIdx, $subtotalCapital);
    $sheetDetalle->setCellValue('R' . $rowIdx, $subtotalAbonado);
    $sheetDetalle->setCellValue('S' . $rowIdx, $subtotalSaldo);
    $sheetDetalle->mergeCells('A' . $rowIdx . ':L' . $rowIdx);
    $sheetDetalle->getStyle('A' . $rowIdx . ':S' . $rowIdx)->applyFromArray([
        'font' => ['bold' => true],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EDEDED']],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    $sheetDetalle->getStyle('M' . $rowIdx . ':S' . $rowIdx)->getNumberFormat()->setFormatCode('"S/ "#,##0.00');
    $rowIdx++;
}

$sheetDetalle->freezePane('A8');
$sheetDetalle->setAutoFilter('A7:Z' . ($rowIdx - 1));
foreach (range('A', 'Z') as $col) {
    $sheetDetalle->getColumnDimension($col)->setAutoSize(true);
}

$spreadsheet->setActiveSheetIndex(0);

$filename = 'reporte_cuentas_cobrar_' . date('Ymd_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
