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
            $verifiarRetencion = $this->buscarRetencion($value['idventa']);
            $statusRetencion = $verifiarRetencion['estado'];
            if ($statusRetencion == true) {
                $btnRetencion = '<button class="btn btn-danger btn-sm" onclick="quitarRetencion(' . $value['idventa'] . ', ' . $verifiarRetencion['data']['idretencion'] . ')" title="Quitar retención">
                                <i class="fa fa-unlock"></i>
                            </button>';
                $btnVerContrato = '<button class="btn btn-info btn-sm" title="Ver documentación del contrato" disabled><i class="fa fa-copy"></i></button>';
            } else {
                $btnRetencion = '<button class="btn btn-primary btn-sm" onclick="retenerContrato(' . $value['idventa'] . ')" title="Retener">
                                <i class="fa fa-lock"></i>
                            </button>';

                $btnVerContrato = '<button class="btn btn-info btn-sm" onclick="verContrato(' . $value['idventa'] . ', ' . $value['idpersona'] . ',\'' . $value['nombre'] . '\')" title="Ver documentación del contrato"><i class="fa fa-copy"></i></button>';
            }
            $data[] = [
                "0" => $value['fecha_contrato'],
                "1" => $this->estadoCuotas($value['idventa']),
                "2" => $value['num_documento'],
                "3" => $value['nombre'],
                "4" => $this->tiposDocumentacion($value['tipo']) . ($value['tipo'] == 1 ? str_pad($value['correlativo'], 9, '0', STR_PAD_LEFT) : ''),
                "5" => $statusRetencion ? '<span class="badge badge-danger">Retenido</span>' : '<span class="badge badge-success">Vigente</span>',
                "6" => $value['formapago'],
                "7" => number_format($value['total_venta'], 2, '.', ','),
                "8" => $btnVerContrato . '
                        ' . $btnRetencion . '
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


    public function estadoCuotas($idventa)
    {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE 
                    WHEN abonototal < deudatotal 
                    AND fechavencimiento < CURDATE()
                    THEN 1 ELSE 0 
                END) as atrasadas
            FROM cuentas_por_cobrar
            WHERE idventa = '$idventa'";

        $row = ejecutarConsultaSimpleFila($sql);

        $total = $row['total'] ?? 0;
        $atrasadas = $row['atrasadas'] ?? 0;

        $porcentaje = ($total > 0) ? ($atrasadas / $total) * 100 : 0;

        // COLOR
        if ($porcentaje == 0) {
            $color = "success";
        } elseif ($porcentaje <= 30) {
            $color = "warning";
        } elseif ($porcentaje <= 60) {
            $color = "orange";
        } else {
            $color = "danger";
        }

        return "<span class='badge bg-$color' style='font-size: 12px; padding: 10px;'>
                $atrasadas
            </span>";
    }

    public function tiposDocumentacion($tipo)
    {
        if ($tipo == 1) {
            return "C";
        } elseif ($tipo == 2) {
            return "AE";
        } elseif ($tipo == 3) {
            return "OR";
        } elseif ($tipo == 4) {
            return "CP";
        } elseif ($tipo == 5) {
            return "CV";
        } else {
            return "";
        }
    }

    public function buscarRetencion($idventa)
    {
        $idventa = intval($idventa);

        $sql = "SELECT * FROM retenciones 
                WHERE idventa = $idventa 
                AND estado = 1
                ORDER BY fecha DESC 
                LIMIT 1";

        $data = ejecutarConsultaSimpleFila($sql);

        $retenido = ($data && $data['estado'] == 1);

        if ($retenido) {
            return array(
                "estado" => true,
                "data" => $data
            );
        }

        return array(
            "estado" => false,
            "data" => null
        );
    }

    public function retenerContrato($idventa, $motivo)
    {
        $fecha_retenido = date('Y-m-d H:i:s');
        $sql = "INSERT INTO retenciones (idventa, motivo, fecha) VALUES ('$idventa', '$motivo', '$fecha_retenido')";
        $result = ejecutarConsulta($sql);
        if ($result) {
            return ["status" => true, "message" => "Contrato retenido exitosamente."];
        } else {
            return ["status" => false, "message" => "Error al retener el contrato."];
        }
    }

    public function quitarRetencion($idventa, $idretencion)
    {
        $sql = "UPDATE retenciones SET estado = 0 WHERE idretencion = '$idretencion' AND idventa = '$idventa'";
        $result = ejecutarConsulta($sql);
        if ($result) {
            return ["status" => true, "message" => "Retención quitada exitosamente."];
        } else {
            return ["status" => false, "message" => "Error al quitar la retención."];
        }
    }

    public function selectUsuarios($idventa, $idsucursal)
    {
        $sql = "SELECT * FROM venta WHERE idventa = $idventa";
        $venta = ejecutarConsultaSimpleFila($sql);

        $sql = "SELECT *
                FROM usuario_sucursal us
                INNER JOIN usuario u ON us.idusuario = u.idusuario
                INNER JOIN personal p ON p.idpersonal = u.idpersonal
                WHERE us.idsucursal = '$idsucursal'";

        $result = ejecutarConsulta($sql);

        if ($result) {
            return ["status" => true, "data" => $result, "idvendedor" => $venta['idPersonal']];
        } else {
            return ["status" => false, "data" => null];
        }
    }
}
