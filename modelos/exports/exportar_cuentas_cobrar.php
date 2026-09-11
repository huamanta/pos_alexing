<?php

require_once __DIR__ . '/../../configuraciones/bootstrap.php';
require_once __DIR__ . '/../../configuraciones/Conexion.php';
require_once __DIR__ . '/../Helpers.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

date_default_timezone_set('America/Lima');

$inicio = $_GET['fecha_inicio'] ?? '';
$fin = $_GET['fecha_fin'] ?? '';
$idsucursal = $_SESSION['idsucursal'] ?? '';
$idcliente = $_GET['idcliente'] ?? '';

$helpers = new Helpers();
$negocio = $helpers->dataSucursal($idsucursal);

$razonSocialNegocio = $negocio['razon_social'] ?? 'Empresa';
$nombreNegocio = $negocio['nombre'] ?? 'Empresa';
$rucNegocio = $negocio['ruc'] ?? '';
$direccionNegocio = $negocio['direccion'] ?? '';
$telefonoNegocio = $negocio['telefono'] ?? '';
$usuarioGenera = $_SESSION['nombre'] ?? 'Sistema';

$clienteFiltro = 'Todos';

if (!empty($idcliente) && $idcliente !== 'Todos') {
    $cliente = ejecutarConsultaSimpleFila("
        SELECT nombre
        FROM persona
        WHERE idpersona = '$idcliente'
        LIMIT 1
    ");

    $clienteFiltro = $cliente['nombre'] ?? $idcliente;
}

$condiciones = [];

$condiciones[] = "v.ventacredito = 'Si'";

if (!empty($idsucursal) && $idsucursal !== 'Todos') {
    $condiciones[] = "v.idsucursal = '$idsucursal'";
}

if (!empty($idcliente) && $idcliente !== 'Todos') {
    $condiciones[] = "v.idcliente = '$idcliente'";
}

if (!empty($inicio) && !empty($fin)) {
    $condiciones[] = "DATE(cc.fecharegistro) BETWEEN '$inicio' AND '$fin'";
}

$condicionSql = 'WHERE ' . implode(' AND ', $condiciones);

$periodoTexto = 'Todos los registros';

if (!empty($inicio) && !empty($fin)) {
    $periodoTexto =
        date('d/m/Y', strtotime($inicio)) .
        ' al ' .
        date('d/m/Y', strtotime($fin));
}

/*
|--------------------------------------------------------------------------
| DETALLE DE CUENTAS POR COBRAR
|--------------------------------------------------------------------------
*/

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
    cc.fecharegistro,
    cc.fechavencimiento,
    cc.deuda,

    v.idventa,
    v.idcliente,
    v.total_venta,
    v.totalrecibido,
    v.totaldeposito,
    v.serie_comprobante,
    v.num_comprobante,

    CONCAT(
        COALESCE(cp.nombre, ''),
        ' ',
        COALESCE(v.serie_comprobante, ''),
        '-',
        COALESCE(v.num_comprobante, '')
    ) AS comprobante,

    DATE(v.fecha_hora) AS fecha_venta,

    c.nombre AS cliente,
    c.num_documento AS documento,
    c.telefono,
    c.direccion,

    CASE
        WHEN cc.fechavencimiento IS NULL
        THEN NULL
        ELSE DATEDIFF(
            CURDATE(),
            cc.fechavencimiento
        )
    END AS dias_atraso,

    (
        SELECT MAX(dcp.fechapago)
        FROM detalle_cuentas_por_cobrar dcp
        WHERE dcp.idcpc = cc.idcpc
    ) AS ultimo_pago,

    (
        SELECT dcp.formapago
        FROM detalle_cuentas_por_cobrar dcp
        WHERE dcp.idcpc = cc.idcpc
        ORDER BY dcp.iddcpc DESC
        LIMIT 1
    ) AS forma_pago,

    (
        SELECT dcp.observacion
        FROM detalle_cuentas_por_cobrar dcp
        WHERE dcp.idcpc = cc.idcpc
        ORDER BY dcp.iddcpc DESC
        LIMIT 1
    ) AS ultima_observacion,

    (
        SELECT p.nombre
        FROM detalle_cuentas_por_cobrar dcp
        LEFT JOIN personal p
            ON p.idpersonal = dcp.idpersonal
        WHERE dcp.idcpc = cc.idcpc
        ORDER BY dcp.iddcpc DESC
        LIMIT 1
    ) AS gestor,

    (
        SELECT r.observacion
        FROM compromiso_pago r
        WHERE r.idcpc = cc.idcpc
        ORDER BY r.idcompromiso_pago DESC
        LIMIT 1
    ) AS ultimo_compromiso,

    GREATEST(
        COALESCE(cc.deudatotal, 0) -
        COALESCE(cc.abonototal, 0),
        0
    ) AS saldo_pendiente

FROM cuentas_por_cobrar cc

INNER JOIN venta v
    ON v.idventa = cc.idventa

INNER JOIN persona c
    ON c.idpersona = v.idcliente

LEFT JOIN comp_pago cp
    ON cp.idcomprobante_pago = v.idcomprobante_pago

$condicionSql

ORDER BY
    c.nombre ASC,
    v.idventa ASC,
    CASE
        WHEN cc.fechavencimiento IS NULL
        THEN 1
        ELSE 0
    END ASC,
    cc.fechavencimiento ASC,
    cc.idcpc ASC
";

$detalle = ejecutarConsulta($sqlDetalle);

$rows = [];

while ($row = $detalle->fetch_assoc()) {
    $rows[] = $row;
}

$rowsAgrupados = [];

foreach ($rows as $row) {

    $clienteKey = (string) ($row['idcliente'] ?? 'sin-cliente');

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

/*
|--------------------------------------------------------------------------
| RESUMEN
|--------------------------------------------------------------------------
*/

$sqlSummary = "
SELECT

    COUNT(DISTINCT v.idcliente) AS total_clientes,

    COUNT(DISTINCT cc.idventa) AS total_creditos,

    COUNT(*) AS total_cuotas,

    COALESCE(
        SUM(COALESCE(cc.deuda_base, 0)),
        0
    ) AS capital_financiado,

    COALESCE(
        SUM(COALESCE(cc.abonototal, 0)),
        0
    ) AS total_cobrado,

    COALESCE(
        SUM(COALESCE(cc.interes, 0)),
        0
    ) AS intereses,

    COALESCE(
        SUM(COALESCE(cc.mora, 0)),
        0
    ) AS mora,

    COALESCE(
        SUM(COALESCE(cc.descuento, 0)),
        0
    ) AS descuentos,

    COALESCE(
        SUM(
            GREATEST(
                COALESCE(cc.deudatotal, 0) -
                COALESCE(cc.abonototal, 0),
                0
            )
        ),
        0
    ) AS saldo_pendiente,

    SUM(
        CASE
            WHEN
                GREATEST(
                    COALESCE(cc.deudatotal, 0) -
                    COALESCE(cc.abonototal, 0),
                    0
                ) > 0
                AND cc.fechavencimiento IS NOT NULL
                AND cc.fechavencimiento < CURDATE()
            THEN 1
            ELSE 0
        END
    ) AS cuotas_vencidas,

    SUM(
        CASE
            WHEN
                GREATEST(
                    COALESCE(cc.deudatotal, 0) -
                    COALESCE(cc.abonototal, 0),
                    0
                ) <= 0
            THEN 1
            ELSE 0
        END
    ) AS cuotas_canceladas,

    SUM(
        CASE
            WHEN
                GREATEST(
                    COALESCE(cc.deudatotal, 0) -
                    COALESCE(cc.abonototal, 0),
                    0
                ) > 0
            THEN 1
            ELSE 0
        END
    ) AS cuotas_pendientes,

    SUM(
        CASE
            WHEN
                GREATEST(
                    COALESCE(cc.deudatotal, 0) -
                    COALESCE(cc.abonototal, 0),
                    0
                ) > 0
                AND cc.fechavencimiento IS NULL
            THEN 1
            ELSE 0
        END
    ) AS cuotas_sin_fecha

FROM cuentas_por_cobrar cc

INNER JOIN venta v
    ON v.idventa = cc.idventa

$condicionSql
";

$summary = ejecutarConsultaSimpleFila($sqlSummary);

$summary = $summary ?: [];

/*
|--------------------------------------------------------------------------
| EXCEL
|--------------------------------------------------------------------------
*/

$spreadsheet = new Spreadsheet();

$spreadsheet
    ->getProperties()
    ->setTitle('Reporte de Ventas a Crédito')
    ->setSubject('Cuentas por cobrar')
    ->setDescription('Reporte detallado de ventas a crédito y cuentas por cobrar')
    ->setCreator($usuarioGenera)
    ->setCompany($nombreNegocio);

$colorPrincipal = '1F4E78';
$colorSecundario = '2F5597';
$colorCliente = '4F81BD';
$colorSubtotal = 'D9EAF7';
$colorPendiente = 'FFF2CC';
$colorVencida = 'F4CCCC';
$colorCancelada = 'C6EFCE';
$colorCompromiso = 'DDEBF7';
$colorSinFecha = 'E7E6E6';
$colorBlanco = 'FFFFFF';
$colorNaranja = 'FCE4D6';

$moneda = '"S/ "#,##0.00';

/*
|--------------------------------------------------------------------------
| HOJA RESUMEN
|--------------------------------------------------------------------------
*/

$sheetResumen = $spreadsheet->getActiveSheet();
$sheetResumen->setTitle('Resumen General');

$sheetResumen->mergeCells('A1:F1');
$sheetResumen->mergeCells('A2:F2');
$sheetResumen->mergeCells('A3:F3');

$sheetResumen->setCellValue(
    'A1',
    $razonSocialNegocio
);

$sheetResumen->setCellValue(
    'A2',
    'REPORTE DE VENTAS A CRÉDITO Y CUENTAS POR COBRAR'
);

$sheetResumen->setCellValue(
    'A3',
    'Generado el ' .
    date('d/m/Y H:i:s') .
    ' por ' .
    $usuarioGenera
);

$sheetResumen->getStyle('A1:F3')->applyFromArray([
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

$sheetResumen->getStyle('A1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 15
    ]
]);

$sheetResumen->getStyle('A2:F2')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 13,
        'color' => ['rgb' => $colorBlanco]
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => $colorPrincipal]
    ]
]);

$sheetResumen->setCellValue(
    'A5',
    'DATOS DEL REPORTE'
);

$sheetResumen->mergeCells('A5:B5');

$sheetResumen->getStyle('A5:B5')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => $colorBlanco]
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => $colorSecundario]
    ]
]);

$datosReporte = [
    ['Empresa', $razonSocialNegocio],
    ['RUC', $rucNegocio ?: '-'],
    ['Sucursal', $nombreNegocio ?: 'Todas'],
    ['Dirección', $direccionNegocio ?: '-'],
    ['Teléfono', $telefonoNegocio ?: '-'],
    ['Cliente', $clienteFiltro],
    ['Periodo consultado', $periodoTexto],
    ['Tipo de operación', 'VENTAS A CRÉDITO ÚNICAMENTE'],
    [
        'Consideración',
        'Se muestran créditos aunque no tengan fecha de vencimiento.'
    ],
    ['Generado por', $usuarioGenera]
];

$filaDatos = 6;

foreach ($datosReporte as $dato) {

    $sheetResumen->setCellValue(
        'A' . $filaDatos,
        $dato[0]
    );

    $sheetResumen->setCellValue(
        'B' . $filaDatos,
        $dato[1]
    );

    $filaDatos++;
}

$sheetResumen->getStyle('A6:A15')->applyFromArray([
    'font' => [
        'bold' => true
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => $colorSubtotal]
    ]
]);

$sheetResumen->getStyle('A6:B15')->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN
        ]
    ],
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
    ]
]);

$sheetResumen->setCellValue(
    'A17',
    'RESUMEN FINANCIERO'
);

$sheetResumen->mergeCells('A17:B17');

$sheetResumen->getStyle('A17:B17')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => $colorBlanco]
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => $colorSecundario]
    ]
]);

$summaryItems = [
    [
        'Clientes con crédito',
        (int) ($summary['total_clientes'] ?? 0),
        'numero'
    ],
    [
        'Ventas a crédito',
        (int) ($summary['total_creditos'] ?? 0),
        'numero'
    ],
    [
        'Cuotas registradas',
        (int) ($summary['total_cuotas'] ?? 0),
        'numero'
    ],
    [
        'Capital financiado',
        (float) ($summary['capital_financiado'] ?? 0),
        'moneda'
    ],
    [
        'Total cobrado',
        (float) ($summary['total_cobrado'] ?? 0),
        'moneda'
    ],
    [
        'Intereses',
        (float) ($summary['intereses'] ?? 0),
        'moneda'
    ],
    [
        'Mora',
        (float) ($summary['mora'] ?? 0),
        'moneda'
    ],
    [
        'Descuentos',
        (float) ($summary['descuentos'] ?? 0),
        'moneda'
    ],
    [
        'Saldo pendiente',
        (float) ($summary['saldo_pendiente'] ?? 0),
        'moneda'
    ],
    [
        'Cuotas vencidas',
        (int) ($summary['cuotas_vencidas'] ?? 0),
        'numero'
    ],
    [
        'Cuotas pendientes',
        (int) ($summary['cuotas_pendientes'] ?? 0),
        'numero'
    ],
    [
        'Cuotas canceladas',
        (int) ($summary['cuotas_canceladas'] ?? 0),
        'numero'
    ],
    [
        'Cuotas sin fecha de vencimiento',
        (int) ($summary['cuotas_sin_fecha'] ?? 0),
        'numero'
    ]
];

$filaResumen = 18;

foreach ($summaryItems as $item) {

    $sheetResumen->setCellValue(
        'A' . $filaResumen,
        $item[0]
    );

    $sheetResumen->setCellValue(
        'B' . $filaResumen,
        $item[1]
    );

    if ($item[2] === 'moneda') {
        $sheetResumen
            ->getStyle('B' . $filaResumen)
            ->getNumberFormat()
            ->setFormatCode($moneda);
    }

    $filaResumen++;
}

$sheetResumen->getStyle(
    'A18:B' . ($filaResumen - 1)
)->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN
        ]
    ]
]);

$sheetResumen->getColumnDimension('A')->setWidth(34);
$sheetResumen->getColumnDimension('B')->setWidth(55);
$sheetResumen->getColumnDimension('C')->setWidth(18);
$sheetResumen->getColumnDimension('D')->setWidth(18);
$sheetResumen->getColumnDimension('E')->setWidth(18);
$sheetResumen->getColumnDimension('F')->setWidth(18);

$sheetResumen->getRowDimension(2)->setRowHeight(28);
$sheetResumen->freezePane('A6');

/*
|--------------------------------------------------------------------------
| HOJA ESTADO DE CUENTA
|--------------------------------------------------------------------------
*/

$sheetEstado = $spreadsheet->createSheet();
$sheetEstado->setTitle('Estado de Cuenta');

$sheetEstado->mergeCells('A1:V1');

$sheetEstado->setCellValue(
    'A1',
    'ESTADO DETALLADO DE VENTAS A CRÉDITO'
);

$sheetEstado->getStyle('A1:V1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 14,
        'color' => ['rgb' => $colorBlanco]
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => $colorPrincipal]
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

$sheetEstado->setCellValue('A2', 'Empresa');
$sheetEstado->setCellValue('B2', $razonSocialNegocio);

$sheetEstado->setCellValue('A3', 'Sucursal');
$sheetEstado->setCellValue('B3', $nombreNegocio ?: 'Todas');

$sheetEstado->setCellValue('A4', 'Cliente');
$sheetEstado->setCellValue('B4', $clienteFiltro);

$sheetEstado->setCellValue('A5', 'Periodo');
$sheetEstado->setCellValue('B5', $periodoTexto);

$headersEstado = [
    'N°',
    'Estado',
    'Cliente',
    'Documento',
    'Venta',
    'Comprobante',
    'Fecha venta',
    'Cuota',
    'Vencimiento',
    'Días atraso',
    'Capital',
    'Interés',
    'Mora',
    'Descuento',
    'Total cuota',
    'Total abonado',
    'Saldo pendiente',
    'Último pago',
    'Forma de pago',
    'Compromiso',
    'Gestor',
    'Observación'
];

$sheetEstado->fromArray(
    $headersEstado,
    null,
    'A7'
);

$sheetEstado->getStyle('A7:V7')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => $colorBlanco]
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => $colorSecundario]
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN
        ]
    ]
]);

$fila = 8;

foreach ($rowsAgrupados as $clienteGrupo) {

    $sheetEstado->mergeCells(
        'A' . $fila . ':V' . $fila
    );

    $sheetEstado->setCellValue(
        'A' . $fila,
        'CLIENTE: ' .
        $clienteGrupo['cliente'] .
        ' | DOC: ' .
        ($clienteGrupo['documento'] ?: '-') .
        ' | TEL: ' .
        ($clienteGrupo['telefono'] ?: '-')
    );

    $sheetEstado->getStyle(
        'A' . $fila . ':V' . $fila
    )->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => $colorBlanco]
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => $colorCliente]
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ]
    ]);

    $fila++;

    $numeroCuota = [];
    $capitalCliente = 0;
    $abonadoCliente = 0;
    $saldoCliente = 0;
    $ventas = [];

    foreach ($clienteGrupo['filas'] as $row) {

        $idVenta = (string) ($row['idventa'] ?? '');

        if (!isset($numeroCuota[$idVenta])) {
            $numeroCuota[$idVenta] = 0;
        }

        $numeroCuota[$idVenta]++;

        $capital = (float) ($row['deuda_base'] ?? 0);
        $interes = (float) ($row['interes'] ?? 0);
        $mora = (float) ($row['mora'] ?? 0);
        $descuento = (float) ($row['descuento'] ?? 0);
        $totalCuota = (float) ($row['deudatotal'] ?? 0);
        $abonado = (float) ($row['abonototal'] ?? 0);
        $saldo = (float) ($row['saldo_pendiente'] ?? 0);

        $capitalCliente += $capital;
        $abonadoCliente += $abonado;
        $saldoCliente += $saldo;

        if (!isset($ventas[$idVenta])) {
            $ventas[$idVenta] = [
                'comprobante' => $row['comprobante'] ?? '',
                'capital' => 0,
                'abonado' => 0,
                'saldo' => 0,
                'cuotas' => 0
            ];
        }

        $ventas[$idVenta]['capital'] += $capital;
        $ventas[$idVenta]['abonado'] += $abonado;
        $ventas[$idVenta]['saldo'] += $saldo;
        $ventas[$idVenta]['cuotas']++;

        $sinFecha = empty($row['fechavencimiento']);

        $diasAtraso = $sinFecha
            ? null
            : (int) ($row['dias_atraso'] ?? 0);

        if ($saldo <= 0) {

            $estado = 'CANCELADA';
            $colorEstado = $colorCancelada;

        } elseif ($sinFecha) {

            $estado = 'SIN FECHA DE VENCIMIENTO';
            $colorEstado = $colorSinFecha;

        } elseif ($diasAtraso > 0) {

            $estado = 'VENCIDA';
            $colorEstado = $colorVencida;

        } elseif ($diasAtraso >= -3) {

            $estado = 'PRÓXIMA A VENCER';
            $colorEstado = $colorPendiente;

        } else {

            $estado = 'PENDIENTE';
            $colorEstado = $colorBlanco;
        }

        $compromiso = trim(
            (string) ($row['ultimo_compromiso'] ?? '')
        );

        if ($compromiso !== '' && $saldo > 0) {
            $estado = 'COMPROMISO DE PAGO';
            $colorEstado = $colorCompromiso;
        }

        $vencimiento = $sinFecha
            ? 'SIN FECHA'
            : date(
                'd/m/Y',
                strtotime($row['fechavencimiento'])
            );

        $fechaVenta = !empty($row['fecha_venta'])
            ? date(
                'd/m/Y',
                strtotime($row['fecha_venta'])
            )
            : '-';

        $ultimoPago = !empty($row['ultimo_pago'])
            ? date(
                'd/m/Y',
                strtotime($row['ultimo_pago'])
            )
            : '-';

        $diasAtrasoMostrar = (
            !$sinFecha &&
            $diasAtraso !== null &&
            $diasAtraso > 0
        ) ? $diasAtraso : '-';

        $valores = [
            $fila - 7,
            $estado,
            $row['cliente'] ?? '',
            $row['documento'] ?? '',
            $idVenta,
            $row['comprobante'] ?? '',
            $fechaVenta,
            $numeroCuota[$idVenta],
            $vencimiento,
            $diasAtrasoMostrar,
            $capital,
            $interes,
            $mora,
            $descuento,
            $totalCuota,
            $abonado,
            $saldo,
            $ultimoPago,
            $row['forma_pago'] ?? '-',
            $compromiso !== '' ? $compromiso : '-',
            $row['gestor'] ?? '-',
            $row['ultima_observacion'] ?? '-'
        ];

        $sheetEstado->fromArray(
            $valores,
            null,
            'A' . $fila
        );

        $sheetEstado->getStyle(
            'A' . $fila . ':V' . $fila
        )->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $colorEstado]
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ]
        ]);

        foreach ([
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q'
        ] as $col) {

            $sheetEstado
                ->getStyle($col . $fila)
                ->getNumberFormat()
                ->setFormatCode($moneda);
        }

        $fila++;
    }

    foreach ($ventas as $idVenta => $ventaResumen) {

        $sheetEstado->mergeCells(
            'A' . $fila . ':J' . $fila
        );

        $sheetEstado->setCellValue(
            'A' . $fila,
            'RESUMEN VENTA ' .
            $idVenta .
            ' | ' .
            $ventaResumen['comprobante'] .
            ' | ' .
            $ventaResumen['cuotas'] .
            ' cuota(s)'
        );

        $sheetEstado->setCellValue(
            'K' . $fila,
            $ventaResumen['capital']
        );

        $sheetEstado->setCellValue(
            'P' . $fila,
            $ventaResumen['abonado']
        );

        $sheetEstado->setCellValue(
            'Q' . $fila,
            $ventaResumen['saldo']
        );

        $sheetEstado->getStyle(
            'A' . $fila . ':Q' . $fila
        )->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $colorNaranja]
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        foreach (['K', 'P', 'Q'] as $col) {

            $sheetEstado
                ->getStyle($col . $fila)
                ->getNumberFormat()
                ->setFormatCode($moneda);
        }

        $fila++;
    }

    $sheetEstado->mergeCells(
        'A' . $fila . ':J' . $fila
    );

    $sheetEstado->setCellValue(
        'A' . $fila,
        'TOTAL DEL CLIENTE'
    );

    $sheetEstado->setCellValue(
        'K' . $fila,
        $capitalCliente
    );

    $sheetEstado->setCellValue(
        'P' . $fila,
        $abonadoCliente
    );

    $sheetEstado->setCellValue(
        'Q' . $fila,
        $saldoCliente
    );

    $sheetEstado->getStyle(
        'A' . $fila . ':Q' . $fila
    )->applyFromArray([
        'font' => [
            'bold' => true
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => $colorSubtotal]
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ]
    ]);

    foreach (['K', 'P', 'Q'] as $col) {

        $sheetEstado
            ->getStyle($col . $fila)
            ->getNumberFormat()
            ->setFormatCode($moneda);
    }

    $fila += 2;
}

if (empty($rows)) {

    $sheetEstado->mergeCells('A8:V8');

    $sheetEstado->setCellValue(
        'A8',
        'NO SE ENCONTRARON VENTAS A CRÉDITO CON LOS FILTROS SELECCIONADOS.'
    );

    $sheetEstado->getStyle('A8:V8')->applyFromArray([
        'font' => [
            'bold' => true
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER
        ]
    ]);
}

$sheetEstado->freezePane('A8');

$columnWidths = [
    'A' => 7,
    'B' => 25,
    'C' => 30,
    'D' => 15,
    'E' => 10,
    'F' => 25,
    'G' => 13,
    'H' => 9,
    'I' => 17,
    'J' => 11,
    'K' => 14,
    'L' => 12,
    'M' => 12,
    'N' => 12,
    'O' => 14,
    'P' => 15,
    'Q' => 16,
    'R' => 14,
    'S' => 18,
    'T' => 30,
    'U' => 25,
    'V' => 40
];

foreach ($columnWidths as $col => $width) {
    $sheetEstado
        ->getColumnDimension($col)
        ->setWidth($width);
}

/*
|--------------------------------------------------------------------------
| HISTORIAL DE PAGOS
|--------------------------------------------------------------------------
*/

$sheetPagos = $spreadsheet->createSheet();
$sheetPagos->setTitle('Historial de Pagos');

$sheetPagos->mergeCells('A1:J1');

$sheetPagos->setCellValue(
    'A1',
    'HISTORIAL DE PAGOS DE VENTAS A CRÉDITO'
);

$sheetPagos->getStyle('A1:J1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 14,
        'color' => ['rgb' => $colorBlanco]
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => $colorPrincipal]
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

$sheetPagos->setCellValue('A2', 'Periodo');
$sheetPagos->setCellValue('B2', $periodoTexto);

$headersPagos = [
    'N°',
    'Cliente',
    'Documento',
    'Venta',
    'Comprobante',
    'Cuota',
    'Fecha de pago',
    'Monto pagado',
    'Forma de pago',
    'Observación'
];

$sheetPagos->fromArray(
    $headersPagos,
    null,
    'A4'
);

$sheetPagos->getStyle('A4:J4')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => $colorBlanco]
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => $colorSecundario]
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN
        ]
    ]
]);

$filaPago = 5;
$numeroPago = 1;
$totalPagadoHistorial = 0;

foreach ($rows as $row) {

    $idcpc = $row['idcpc'] ?? '';

    if (empty($idcpc)) {
        continue;
    }

    $pagos = ejecutarConsulta("
        SELECT
            dcp.iddcpc,
            dcp.fechapago,
            dcp.montopagado,
            dcp.montotarjeta,
            dcp.formapago,
            dcp.observacion
        FROM detalle_cuentas_por_cobrar dcp
        WHERE dcp.idcpc = '$idcpc'
        ORDER BY
            dcp.fechapago ASC,
            dcp.iddcpc ASC
    ");

    while ($pago = $pagos->fetch_assoc()) {

        $montoPagado =
            (float) ($pago['montopagado'] ?? 0) +
            (float) ($pago['montotarjeta'] ?? 0);

        $totalPagadoHistorial += $montoPagado;

        $fechaPago = !empty($pago['fechapago'])
            ? date(
                'd/m/Y',
                strtotime($pago['fechapago'])
            )
            : '-';

        $sheetPagos->fromArray([
            $numeroPago,
            $row['cliente'] ?? '',
            $row['documento'] ?? '',
            $row['idventa'] ?? '',
            $row['comprobante'] ?? '',
            $row['idcpc'] ?? '',
            $fechaPago,
            $montoPagado,
            $pago['formapago'] ?? '-',
            $pago['observacion'] ?? '-'
        ], null, 'A' . $filaPago);

        $sheetPagos->getStyle(
            'A' . $filaPago . ':J' . $filaPago
        )->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ]
        ]);

        $sheetPagos
            ->getStyle('H' . $filaPago)
            ->getNumberFormat()
            ->setFormatCode($moneda);

        $filaPago++;
        $numeroPago++;
    }
}

if ($filaPago > 5) {

    $sheetPagos->setCellValue(
        'G' . $filaPago,
        'TOTAL COBRADO'
    );

    $sheetPagos->setCellValue(
        'H' . $filaPago,
        $totalPagadoHistorial
    );

    $sheetPagos->getStyle(
        'G' . $filaPago . ':H' . $filaPago
    )->applyFromArray([
        'font' => [
            'bold' => true
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => $colorSubtotal]
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ]
    ]);

    $sheetPagos
        ->getStyle('H' . $filaPago)
        ->getNumberFormat()
        ->setFormatCode($moneda);

} else {

    $sheetPagos->mergeCells('A5:J5');

    $sheetPagos->setCellValue(
        'A5',
        'NO SE ENCONTRARON PAGOS REGISTRADOS PARA LOS CRÉDITOS MOSTRADOS.'
    );

    $sheetPagos->getStyle('A5:J5')->applyFromArray([
        'font' => [
            'italic' => true
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER
        ]
    ]);
}

$sheetPagos->freezePane('A5');

$columnWidthsPagos = [
    'A' => 8,
    'B' => 30,
    'C' => 15,
    'D' => 10,
    'E' => 25,
    'F' => 10,
    'G' => 16,
    'H' => 16,
    'I' => 18,
    'J' => 40
];

foreach ($columnWidthsPagos as $col => $width) {
    $sheetPagos
        ->getColumnDimension($col)
        ->setWidth($width);
}

$spreadsheet->setActiveSheetIndex(0);

$filename =
    'reporte_ventas_credito_cuentas_cobrar_' .
    date('Ymd_His') .
    '.xlsx';

header(
    'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
);

header(
    'Content-Disposition: attachment; filename="' .
    $filename .
    '"'
);

header('Cache-Control: max-age=0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;