<?php
require "../configuraciones/Conexion.php";
date_default_timezone_set('America/Lima');

class Contratos
{

    public function __construct()
    {
    }

    public function listar($fecha_inicio, $fecha_fin, $estado = '', $idsucursal = '')
    {
        $query = "SELECT 
                    c.idpersona,
                    c.nombre,
                    c.num_documento,
                    c.direccion,
                    v.formapago,
                    v.num_comprobante,
                    v.serie_comprobante,
                    v.tipo_comprobante,
                    v.total_venta,
                    d.iddocumento,
                    v.idventa,
                    d.fecha_contrato,
                    d.tipo,
                    d.correlativo,
                    d.estado
                    FROM documentacion d 
                    INNER JOIN venta v ON d.idventa = v.idventa 
                    LEFT JOIN persona c ON v.idcliente = c.idpersona
                    WHERE d.tipo = '1'";

        // Agregar filtros de fecha si se proporcionan
        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $query .= " AND DATE(d.fecha_contrato) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        }

        // Agregar filtro de estado
        if (!empty($estado) && $estado != 'Todos') {
            $estado_map = [
                'Aceptado' => 1,
                'Por Enviar' => 2,
                'Nota Credito' => 3,
                'Rechazado' => 4
            ];
            if (isset($estado_map[$estado])) {
                $query .= " AND d.estado = " . $estado_map[$estado];
            }
        }

        // Agregar filtro de sucursal
        if (!empty($idsucursal)) {
            $query .= " AND v.idsucursal = '$idsucursal'";
        }

        // Obtener total de registros sin paginación
        $query_total = $query;
        $total_result = ejecutarConsulta($query_total);
        $total_records = mysqli_num_rows($total_result);

        // Agregar paginación
        $query .= " ORDER BY d.fecha_contrato DESC";

        $contratos = ejecutarConsulta($query);
        $data = [];
        foreach ($contratos as $key => $value) {
            $data[] = [
                "0" => $value['fecha_contrato'],
                "1" => $value['num_documento'],
                "2" => $value['nombre'],
                "3" => $value['tipo'] == 1?"C":$value['tipo'] . str_pad($value['correlativo'], 9, '0', STR_PAD_LEFT),
                "4" => $value['estado'] == 1 ? '<span class="badge badge-success">Vigente</span>' : '<span class="badge badge-danger">Finalizado</span>',
                "5" => $value['formapago'],
                "6" => number_format($value['total_venta'], 2, '.', ','),
                "7" => '<button class="btn btn-info btn-sm" onclick="verContrato(' . $value['idventa'] . ')" title="Ver documentación del contrato"><i class="fa fa-copy"></i></button>
                        <button class="btn btn-primary btn-sm" onclick="descargarContrato(' . $value['idventa'] . ')" title="Descargar contrato"><i class="fa fa-lock"></i></button>
                        <button class="btn btn-secondary btn-sm" onclick="imprimirContrato(' . $value['idventa'] . ')" title="Imprimir contrato"><i class="fa fa-trash"></i></button>',
            ];
        }

        $results = [
            "sEcho" => intval($_GET['sEcho'] ?? 1),
            "iTotalRecords" => $total_records,
            "iTotalDisplayRecords" => $total_records,
            "aaData" => $data
        ];
        return json_encode($results);
    }
}
