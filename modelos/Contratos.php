<?php
require "../configuraciones/Conexion.php";
date_default_timezone_set('America/Lima');

class Contratos
{

    public function __construct()
    {
    }

    public function listar($fecha_inicio, $fecha_fin, $idsucursal = '', $estado = '', $condicion = '', $frecuencia = '')
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
                    d.estado,
                    v.frecuencia,
                    c.latitude,
                    c.longitude
                    FROM documentacion d 
                    INNER JOIN venta v ON d.idventa = v.idventa 
                    LEFT JOIN persona c ON v.idcliente = c.idpersona
                    WHERE d.tipo = '1'";

        // Agregar filtros de fecha si se proporcionan
        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $query .= " AND DATE(d.fecha_contrato) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        }

        // Agregar filtro de estado (1 = pagado, 2 = pendiente)
        if (!empty($estado)) {
            if ($estado == 1) {
                $query .= " AND NOT EXISTS (
                    SELECT 1 
                    FROM cuentas_por_cobrar cpc
                    WHERE cpc.idventa = v.idventa
                    AND cpc.abonototal < cpc.deudatotal
                )";
            }

            if ($estado == 2) {
                $query .= " AND EXISTS (
                    SELECT 1 
                    FROM cuentas_por_cobrar cpc
                    WHERE cpc.idventa = v.idventa
                    AND cpc.abonototal < cpc.deudatotal
                )";
            }
        }

        // Agregar filtro de sucursal
        if (!empty($idsucursal)) {
            $query .= " AND v.idsucursal = '$idsucursal'";
        }

        // Agregar filtro de condición (1 = normal, 2 = moroso)
        if (!empty($condicion)) {

            // MOROSOS 
            if ($condicion == 2) {
                $query .= " AND EXISTS (
                    SELECT 1 
                    FROM cuentas_por_cobrar cpc
                    WHERE cpc.idventa = v.idventa
                    AND cpc.abonototal < cpc.deudatotal
                    AND cpc.fechavencimiento < CURDATE()
                )";
            }

            //NORMALES
            if ($condicion == 1) {
                $query .= " AND NOT EXISTS (
                    SELECT 1 
                    FROM cuentas_por_cobrar cpc
                    WHERE cpc.idventa = v.idventa
                    AND cpc.abonototal < cpc.deudatotal
                    AND cpc.fechavencimiento < CURDATE()
                )";
            }
        }

        // Agregar filtro de frecuencia
        if (!empty($frecuencia)) {
            $query .= " AND v.frecuencia = '$frecuencia'";
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

            if ($value['estado'] == 0) {
                $btnAnular = '';
                $btnRetencion = '';
                $btnAmortizar = '';
                $status = '<span class="badge badge-danger">Anulado</span>';
            } else if ($value['estado'] == 2) {
                $btnAnular = '';
                $btnRetencion = '';
                $btnAmortizar = '';
                $status = '<span class="badge badge-info">Finalizado</span>';
            } else {
                $btnAnular = '<button class="btn btn-danger btn-sm"
                            onclick="eliminarContrato(' . (int) $value['idventa'] . ')"
                            title="Eliminar contrato">
                            <i class="fa fa-trash"></i>
                        </button>';
                if ($statusRetencion == true) {
                    $btnRetencion = '<button class="btn btn-danger btn-sm" onclick="quitarRetencion(' . $value['idventa'] . ', ' . $verifiarRetencion['data']['idretencion'] . ')" title="Quitar retención">
                                <i class="fa fa-unlock"></i>
                            </button>';
                    $btnAmortizar = '';
                    $status = '<span class="badge badge-warning">Retenido</span>';
                } else {
                    $cuenta_cobrar = $this->cuentasCobrar($value['idpersona'], $value['idventa']);
                    $doc = $cuenta_cobrar['tipo_comprobante'] . '-' . $cuenta_cobrar['serie_comprobante'] . '-' . $cuenta_cobrar['num_comprobante'];
                    $saldo = round(floatval($cuenta_cobrar['saldo_pendiente']), 2);
                    $btnRetencion = '<button class="btn btn-primary btn-sm" onclick="retenerContrato(' . $value['idventa'] . ')" title="Retener">
                                <i class="fa fa-lock"></i>
                            </button>';
                    $btnAmortizar = '<button class="btn btn-warning btn-sm"
                            onclick=\'verCuotasCredito(' . $cuenta_cobrar['idventa'] . ',
                            ' . json_encode($saldo) . ',
                            ' . json_encode($doc) . ',
                            ' . json_encode($cuenta_cobrar['nota']) . '
                            )\'
                            title="Amortizar contrato">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </button>';

                    $status = '<span class="badge badge-success">Vigente</span>';
                }
            }

            $data[] = [
                "0" => $value['fecha_contrato'],
                "1" => $this->estadoCuotas($value['idventa']),
                "2" => $value['num_documento'],
                "3" => $value['nombre'],
                "4" => $this->verVehiculoVendido($value['idventa']),
                "5" => $this->tiposDocumentacion($value['tipo']) . ($value['tipo'] == 1 ? str_pad($value['correlativo'], 9, '0', STR_PAD_LEFT) : ''),
                '6' => $value['serie_comprobante'] . '-' . $value['num_comprobante'],
                "7" => $status,
                "8" => $value['formapago'],
                '9' => $this->getDataFrecuencia($value['frecuencia'])->texto,
                "10" => number_format($value['total_venta'], 2, '.', ','),
                "11" => '
                        <button class="btn btn-success btn-sm"
                            onclick=\'verContrato(
                                ' . (int) $value['idventa'] . ',
                                ' . (int) $value['idpersona'] . ',
                                ' . json_encode($value['nombre']) . '
                            )\'
                            title="Ver documentación del contrato">
                            <i class="fa fa-copy"></i>
                        </button>

                        ' . $btnRetencion . '

                        <button class="btn btn-info btn-sm"
                            onclick=\'verUbicacionCliente(
                                ' . json_encode($value['latitude']) . ',
                                ' . json_encode($value['longitude']) . ',
                                ' . json_encode($value['direccion']) . '
                            )\'
                            title="Ver ubicación del cliente">
                            <i class="fas fa-search-location"></i>
                        </button>

                        ' . $btnAmortizar . ' ' . $btnAnular,
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


    public function cuentasCobrar($idcliente, $idventa)
    {
        $sql = "SELECT
                    v.idventa,
                    DATE_FORMAT(v.fecha_hora, '%d/%m/%y | %H:%i:%s %p') AS fecha_venta,
                    v.tipo_comprobante,
                    v.serie_comprobante,
                    v.num_comprobante,
                    v.total_venta,
                    v.nota,
                    SUM(cc.abonototal) AS total_abonado,
                    SUM(cc.deuda) AS saldo_pendiente
                FROM venta v
                INNER JOIN cuentas_por_cobrar cc ON cc.idventa = v.idventa
                WHERE v.idcliente = '$idcliente'
                  AND v.idventa = '$idventa'
                GROUP BY v.idventa, v.fecha_hora, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, v.total_venta
                ORDER BY v.idventa DESC LIMIT 1";

        $data = ejecutarConsultaSimpleFila($sql);
        return $data;
    }


    public function verVehiculoVendido($idventa)
    {

        $idventa = (int) $idventa;

        $sql = "SELECT * FROM detalle_venta 
            WHERE idventa = $idventa
            LIMIT 1";

        $data = ejecutarConsultaSimpleFila($sql);

        return $data['nombre_producto'];
    }

    public static function getDataFrecuencia($frecuencia)
    {
        $frecuenciaTexto = "";
        $frecuenciaSm = "";

        switch ($frecuencia) {
            case '1':
                $frecuenciaTexto = "diarias";
                $frecuenciaSm = "dias";
                break;
            case '2':
                $frecuenciaTexto = "semanales";
                $frecuenciaSm = "semanas";
                break;
            case '3':
                $frecuenciaTexto = "quincenales";
                $frecuenciaSm = "quincenas";
                break;
            case '4':
                $frecuenciaTexto = "mensuales";
                $frecuenciaSm = "meses";
                break;
            case "5":
                $frecuenciaTexto = "bimestrales";
                $frecuenciaSm = "bimestres";
                break;
            case "6":
                $frecuenciaTexto = "trimestrales";
                $frecuenciaSm = "trimestres";
                break;
            case '7':
                $frecuenciaTexto = "semestrales";
                $frecuenciaSm = "semestres";
                break;
            case "8":
                $frecuenciaTexto = "anuales";
                $frecuenciaSm = "años";
                break;
            default:
                $frecuenciaTexto = "mensuales";
                $frecuenciaSm = "meses";
        }

        return (object) [
            "texto" => $frecuenciaTexto,
            "short" => $frecuenciaSm
        ];
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

    public function anularContrato($idventa)
    {
        if (empty($idventa)) {
            return [
                'status' => false,
                'message' => 'ID de venta vacío'
            ];
        }

        $sql1 = "UPDATE documentacion 
            SET estado = 0
            WHERE idventa = '$idventa'";

        $ok1 = ejecutarConsulta($sql1);

        $sql2 = "UPDATE venta 
            SET estado_venta = 0 
            WHERE idventa = '$idventa'";

        $ok2 = ejecutarConsulta($sql2);

        if ($ok1 && $ok2) {
            return [
                'status' => true,
                'message' => 'Contrato anulado correctamente'
            ];
        }

        return [
            'status' => false,
            'message' => 'No se pudo anular el contrato'
        ];
    }
}
