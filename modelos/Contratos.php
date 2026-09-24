<?php
require_once __DIR__ . "/../configuraciones/bootstrap.php";
require_once __DIR__ . "/../configuraciones/Conexion.php";
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";
date_default_timezone_set('America/Lima');

class Contratos extends Helpers
{

    public function __construct()
    {
        parent::__construct();
    }

    public function listar($fecha_inicio, $fecha_fin, $idsucursal = '', $estado = '', $condicion = '', $frecuencia = '')
    {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 20;
        $search = $_GET['search'] ?? '';

        $paginator = (new DBQuery($this->pdo))
            ->select('c.idpersona,
                    c.nombre,
                    c.num_documento,
                    c.direccion,
                    v.formapago,
                    v.ventacredito,
                    v.num_comprobante,
                    v.serie_comprobante,
                    v.idcomprobante_pago,
                    v.total_venta,
                    d.iddocumento,
                    v.idventa,
                    d.fecha_contrato,
                    d.tipo,
                    d.correlativo,
                    d.estado,
                    v.frecuencia,
                    c.latitude,
                    c.longitude,
                    dv.nombre_producto')
            ->from('documentacion d ')
            ->join('venta v', 'd.idventa = v.idventa')
            ->join('detalle_venta dv', 'dv.idventa = v.idventa')
            ->join('persona c', 'v.idcliente = c.idpersona')
            ->where('d.tipo', '=', 1)
            ->where('v.idsucursal', '=', $idsucursal);

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $paginator->whereBetween(
                'DATE(d.fecha_contrato)',
                $fecha_inicio,
                $fecha_fin
            );
        }

        // Agregar filtro de estado (0 = Anulado, 1 = pendiente, 2 = finalizado )
        if (!empty($estado)) {
            $paginator->where('d.estado', '=', $estado);
        }

        // Agregar filtro frecuencia
        if (!empty($frecuencia)) {
            $paginator->where('v.frecuencia', '=', $frecuencia);
        }

        if (!empty($condicion)) {

            // Morosos
            if ($condicion == 2) {
                $paginator->whereExists("
                                    SELECT 1
                                    FROM cuentas_por_cobrar cpc
                                    WHERE cpc.idventa = v.idventa
                                    AND cpc.abonototal < cpc.deudatotal
                                    AND cpc.fechavencimiento < CURDATE()
                                ");
            }

            if ($condicion == 1) {
                $paginator->whereNotExists("
                                    SELECT 1
                                    FROM cuentas_por_cobrar cpc
                                    WHERE cpc.idventa = v.idventa
                                    AND cpc.abonototal < cpc.deudatotal
                                    AND cpc.fechavencimiento < CURDATE()
                                ");
            }
        }

        if ($search !== '') {
            $paginator->search($search, [
                'c.nombre',
                'dv.nombre_producto'
            ]);
        }

        $response = $paginator
            ->orderBy('d.iddocumento', 'DESC')
            ->paginate($page, $limit);

        foreach ($response['data'] as &$value) {
            $retencion = $this->buscarRetencion($value['idventa']);
            $value['retencion'] = $retencion;
            $value['estado_cuotas'] = $this->estadoCuotas($value['idventa']);
            $value['vehiculo'] = $this->verVehiculoVendido($value['idventa']);
            $value['documento'] = $this->tiposDocumentacion($value['tipo'])
                . ($value['tipo'] == 1
                    ? str_pad($value['correlativo'], 9, '0', STR_PAD_LEFT)
                    : '');
            $value['frecuencia_texto'] =
                $this->getDataFrecuencia($value['frecuencia'])->texto;
            $value['total_venta_formateado'] =
                Helpers::get_currency_symbol($value['total_venta']);
            // Solo flags para el frontend
            $value['puede_anular'] = $value['estado'] == 1;
            $value['puede_retener'] =
                $value['estado'] == 1 && !$retencion['estado'];
            $value['puede_quitar_retencion'] =
                $value['estado'] == 1 && $retencion['estado'];
            $value['puede_amortizar'] =
                $value['estado'] == 1 && !$retencion['estado'];
        }

        unset($value);

        unset($value);

        return Response::json($response);

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

        return ['color' => $color, 'atrasados' => $atrasadas];
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
                "id" => $data['idretencion']
            );
        }

        return array(
            "estado" => false,
            "id" => null
        );
    }

    public function retenerContrato($idventa, $motivo)
    {
        $fecha_retenido = date('Y-m-d H:i:s');
        try {
            $resultado = (new FluentSaver($this->pdo))
                ->table('retenciones')
                ->data([
                    'idventa' => $idventa,
                    'motivo' => $motivo,
                    'fecha' => $fecha_retenido
                ])
                ->save();
            if (!$resultado) {
                throw new Exception('No se pudo crear la retención');
            }
            return json_encode([
                'success' => true,
                'message' => 'Retención creada exitosamente'
            ]);
        } catch (Throwable $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function quitarRetencion($idventa, $idretencion)
    {
        try {
            $resultado = (new FluentSaver($this->pdo))
                ->table('retenciones')
                ->primaryKey('idretencion')
                ->data([
                    'idretencion' => $idretencion,
                    'estado' => 0
                ])
                ->update();
            if (!$resultado) {
                throw new Exception('No se pudo quitar la retención');
            }

            return json_encode([
                'success' => true,
                'message' => 'Retención quitada exitosamente'
            ]);
        } catch (Throwable $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
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
