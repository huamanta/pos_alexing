<?php

use function Safe\error_log;

require "../configuraciones/Conexion.php";
require_once 'Persona.php';
require_once 'Helpers.php';

class Solicitudes extends Persona
{

    public function listarGeneralSolicitudes($idsucursal)
    {
        $result = (new DBQuery($this->pdo))
            ->select([
                "COUNT(*) AS total_solicitudes",
                "COALESCE(SUM(CASE WHEN s.estado = 'EN_PROCESO' THEN 1 ELSE 0 END), 0) AS en_proceso",
                "COALESCE(SUM(CASE WHEN s.estado = 'OBSERVADO' THEN 1 ELSE 0 END), 0) AS observado",
                "COALESCE(SUM(CASE WHEN s.estado = 'RECHAZADO' THEN 1 ELSE 0 END), 0) AS rechazado",
                "COALESCE(SUM(CASE WHEN s.estado = 'APROBADO' THEN 1 ELSE 0 END), 0) AS aprobado"
            ])
            ->from("solicitud_credito s")
            ->join("persona p", "p.idpersona = s.idcliente")
            ->where("s.idsucursal", "=", $idsucursal)
            ->first();

        return json_encode($result);
    }


    public function listarSolicitudes($idsucursal, $estado, $riesgo, $paso)
    {
        try {

            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 20;
            $search = $_GET['search'] ?? '';

            $paginator = (new DBQuery($this->pdo))
                ->select('s.*,
                    p.nombre cliente,
                    wp.nombre paso_actual_nombre,
                    (
                        SELECT DATEDIFF(
                            NOW(),
                            MAX(sw.fecha_inicio)
                        )
                        FROM solicitud_workflow sw
                        WHERE sw.idsolicitud=s.idsolicitud
                    ) dias_etapa')
                ->from('solicitud_credito s')
                ->join('persona p', 'p.idpersona=s.idcliente')
                ->join('workflow_paso wp', 'wp.idpaso=s.paso_actual')
                ->where('s.idsucursal', '=', $idsucursal);

            if (!empty($estado)) {
                $paginator->where('s.estado', '=', $estado);
            }

            if (!empty($riesgo)) {
                $paginator->where('s.riesgo', '=', $riesgo);
            }

            if (!empty($paso)) {
                $paginator->where('s.paso_actual', '=', $paso);
            }

            if ($search !== '') {
                $paginator->search($search, [
                    's.codigo',
                    'p.nombre',
                    's.estado',
                    's.riesgo'
                ]);
            }

            $response = $paginator
                ->orderBy('s.idsolicitud', 'DESC')
                ->paginate($page, $limit);

            $response['permissions'] = [
                'aprobar' => Helpers::getUserPermissionAccion('Aprobar solicitudes'),
                'pasos' => Helpers::getUserPermissionAccion('Ver flujo de pasos'),
                'archivos' => Helpers::getUserPermissionAccion('Ver archivos de solicitud')
            ];

            return json_encode($response);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function guardar(
        $idcliente,
        $idcotizacion,
        $ingreso_mensual,
        $inicial,
        $observacion,
        $idusuario,
        $idsucursal
    ) {

        try {

            ejecutarConsulta("START TRANSACTION");

            // Verificar si la cotización ya tiene una solicitud
            $sqlValidar = "SELECT idsolicitud
                   FROM solicitud_credito
                   WHERE idcotizacion = '$idcotizacion'
                   LIMIT 1";

            $solicitudExiste = ejecutarConsultaSimpleFila($sqlValidar);

            if ($solicitudExiste) {
                throw new Exception(
                    "La cotización ya tiene una solicitud de crédito registrada."
                );
            }


            $codigo = "SOL-" . date('YmdHis');

            $scoreCrediticio = json_decode(
                $this->scorecrediticiocliente($idcliente),
                true
            );

            $score = $scoreCrediticio['score'];
            $riesgo = $scoreCrediticio['riesgo'];

            $sql = "INSERT INTO solicitud_credito(
                    codigo,
                    idcliente,
                    idcotizacion,
                    idsucursal,
                    score,
                    riesgo,
                    estado,
                    paso_actual,
                    idusuario,
                    fecha_registro
                )
                VALUES(
                    '$codigo',
                    '$idcliente',
                    '$idcotizacion',
                    '$idsucursal',
                    '$score',
                    '$riesgo',
                    'EN_PROCESO',
                    1,
                    '$idusuario',
                    NOW()
                )";

            $idsolicitud = ejecutarConsulta_retornarID($sql);

            if (!$idsolicitud) {
                throw new Exception(
                    "No se pudo registrar la solicitud"
                );
            }

            // Workflow inicial
            $sqlWorkflow = "INSERT INTO solicitud_workflow(
                            idsolicitud,
                            idpaso,
                            fecha_inicio,
                            observacion,
                            idusuario
                        )
                        VALUES(
                            '$idsolicitud',
                            1,
                            NOW(),
                            'Solicitud creada',
                            '$idusuario'
                        )";

            if (!ejecutarConsulta($sqlWorkflow)) {
                throw new Exception(
                    "No se pudo registrar el workflow"
                );
            }

            // Evaluación inicial
            $sqlEvaluacion = "INSERT INTO solicitud_evaluacion(
                                idsolicitud,
                                ingreso_mensual,
                                egreso_mensual,
                                capacidad_pago,
                                inicial_validada,
                                score_manual,
                                observacion,
                                fecha_registro
                            )
                            VALUES(
                                '$idsolicitud',
                                '$ingreso_mensual',
                                0,
                                '$ingreso_mensual',
                                '$inicial',
                                '$score',
                                '$observacion',
                                NOW()
                            )";

            if (!ejecutarConsulta($sqlEvaluacion)) {
                throw new Exception(
                    "No se pudo registrar la evaluación"
                );
            }

            ejecutarConsulta("COMMIT");

            return json_encode([
                "status" => true,
                "idsolicitud" => $idsolicitud,
                "score" => $score,
                "riesgo" => $riesgo,
                "msg" => "Solicitud registrada correctamente"
            ]);
        } catch (Exception $e) {

            ejecutarConsulta("ROLLBACK");

            return json_encode([
                "status" => false,
                "msg" => $e->getMessage()
            ]);
        }
    }

    public function mostrar($idsolicitud)
    {
        $sql = "SELECT
            s.*,
            p.nombre as cliente,
            p.direccion,
            wp.nombre as paso_actual_nombre,
            se.*,
            vd.direccion_registrada,
            vd.resultado_verificacion,
            vd.comentarios,
            vd.fecha_verificacion
        FROM solicitud_credito s
        INNER JOIN persona p
            ON p.idpersona = s.idcliente
        INNER JOIN solicitud_evaluacion se
            ON se.idsolicitud = s.idsolicitud
        LEFT JOIN workflow_paso wp
            ON wp.idpaso = s.paso_actual
        LEFT JOIN verificaciones_domiciliarias vd
            ON vd.idverificacion = (
                SELECT MAX(vd2.idverificacion)
                FROM verificaciones_domiciliarias vd2
                WHERE vd2.idsolicitud = s.idsolicitud
            )
        WHERE s.idsolicitud = '$idsolicitud'";

        $resultado = ejecutarConsultaSimpleFila($sql);

        $sqlpasos = "SELECT * FROM workflow_paso";
        $datapasos = ejecutarConsulta($sqlpasos);
        $resultPasos = [];

        foreach ($datapasos as $paso) {
            $permisoNombre = "Puede realizar " . mb_strtolower($paso['nombre'], 'UTF-8');
            $tienePermiso = Helpers::getUserPermissionAccion($permisoNombre);
            if (!$tienePermiso) {
                continue;
            }
            $resultPasos[] = [
                'id' => (int) $paso['idpaso'],
                'label' => $paso['nombre'],
                'description' => $paso['descripcion']
            ];
        }

        $response = [
            'pasos' => $resultPasos,
            'data' => $resultado
        ];

        return json_encode($response);
    }

    private function insertarWorkflow($idsolicitud, $idpaso, $observacion, $idusuario, $estadoPaso = 'PENDIENTE', $fechaFin = null)
    {
        $fechaFin = $fechaFin ? "'$fechaFin'" : 'NULL';
        $sqlWorkflow = "INSERT INTO solicitud_workflow(
                            idsolicitud,
                            idpaso,
                            fecha_inicio,
                            fecha_fin,
                            estado,
                            observacion,
                            idusuario
                        )
                        VALUES(
                            '$idsolicitud',
                            '$idpaso',
                            NOW(),
                            $fechaFin,
                            '$estadoPaso',
                            '$observacion',
                            '$idusuario'
                        )";
        return ejecutarConsulta($sqlWorkflow);
    }

    private function marcarPasoCompletado($idsolicitud, $idpaso)
    {
        $sql = "UPDATE solicitud_workflow 
                SET estado='APROBADO', fecha_fin=NOW()
                WHERE idsolicitud='$idsolicitud' AND idpaso='$idpaso'";
        return ejecutarConsulta($sql);
    }

    private function estadoPorPaso($idpaso)
    {
        $map = [
            1 => 'EN_PROCESO',
            2 => 'PENDIENTE_DOCUMENTOS',
            3 => 'EN_PROCESO',
            4 => 'EN_PROCESO',
            5 => 'APROBADO'
        ];

        return isset($map[$idpaso]) ? $map[$idpaso] : 'EN_PROCESO';
    }

    public function avanzarPaso($idsolicitud, $idpaso, $observacion, $idusuario)
    {
        try {
            ejecutarConsulta("START TRANSACTION");

            // Marcar paso anterior como APROBADO
            $rowAnterior = ejecutarConsultaSimpleFila(
                "SELECT paso_actual FROM solicitud_credito WHERE idsolicitud='$idsolicitud'"
            );
            if ($rowAnterior && $rowAnterior['paso_actual']) {
                $this->marcarPasoCompletado($idsolicitud, $rowAnterior['paso_actual']);
            }

            $estado = $this->estadoPorPaso($idpaso);

            $sql = "UPDATE solicitud_credito
                    SET estado='$estado',
                        paso_actual='$idpaso',
                        fecha_actualizacion=NOW()
                    WHERE idsolicitud='$idsolicitud'";

            if (!ejecutarConsulta($sql)) {
                throw new Exception("No se pudo actualizar la solicitud al paso $idpaso");
            }

            if (!$this->insertarWorkflow($idsolicitud, $idpaso, $observacion, $idusuario, 'EN_PROCESO')) {
                throw new Exception("No se pudo registrar el workflow del paso $idpaso");
            }

            ejecutarConsulta("COMMIT");

            return json_encode([
                "status" => true,
                "msg" => "Solicitud actualizada al paso $idpaso"
            ]);
        } catch (Exception $e) {
            ejecutarConsulta("ROLLBACK");
            return json_encode([
                "status" => false,
                "msg" => $e->getMessage()
            ]);
        }
    }

    public function marcarObservado($idsolicitud, $observacion, $idusuario)
    {
        try {
            ejecutarConsulta("START TRANSACTION");

            $row = ejecutarConsultaSimpleFila("SELECT paso_actual FROM solicitud_credito WHERE idsolicitud='$idsolicitud'");
            $idpaso = $row['paso_actual'] ?? 1;

            $sql = "UPDATE solicitud_credito
                    SET estado='OBSERVADO',
                        fecha_actualizacion=NOW()
                    WHERE idsolicitud='$idsolicitud'";

            if (!ejecutarConsulta($sql)) {
                throw new Exception("No se pudo marcar la solicitud como observada");
            }

            if (!$this->insertarWorkflow($idsolicitud, $idpaso, $observacion, $idusuario, 'OBSERVADO')) {
                throw new Exception("No se pudo registrar el workflow de observación");
            }

            ejecutarConsulta("COMMIT");
            return json_encode([
                "status" => true,
                "msg" => "Solicitud observada"
            ]);
        } catch (Exception $e) {
            ejecutarConsulta("ROLLBACK");
            return json_encode([
                "status" => false,
                "msg" => $e->getMessage()
            ]);
        }
    }

    public function cargarDocumentacion($idsolicitud, $observacion, $idusuario, $observacion_evaluacion)
    {
        try {
            ejecutarConsulta("START TRANSACTION");

            // Marcar paso 1 como APROBADO
            $this->marcarPasoCompletado($idsolicitud, 1);

            $sql = "UPDATE solicitud_credito
                    SET estado='PENDIENTE_DOCUMENTOS',
                        paso_actual=2,
                        fecha_actualizacion=NOW()
                    WHERE idsolicitud='$idsolicitud'";

            if (!ejecutarConsulta($sql)) {
                throw new Exception("No se pudo pasar la solicitud a documentación");
            }

            $sql2 = "UPDATE solicitud_evaluacion SET observacion = '$observacion_evaluacion' WHERE idsolicitud='$idsolicitud'";
            if (!ejecutarConsulta($sql2)) {
                throw new Exception("No se pudo pasar la evaluacion a documentación");
            }

            if (!$this->insertarWorkflow($idsolicitud, 2, $observacion, $idusuario, 'EN_PROCESO')) {
                throw new Exception("No se pudo registrar el workflow de documentación");
            }

            ejecutarConsulta("COMMIT");
            return json_encode([
                "status" => true,
                "msg" => "Solicitud en paso 2: documentación pendiente"
            ]);
        } catch (Exception $e) {
            ejecutarConsulta("ROLLBACK");
            return json_encode([
                "status" => false,
                "msg" => $e->getMessage()
            ]);
        }
    }

    public function aprobarDocumentacion($idsolicitud, $observacion, $idusuario)
    {
        try {
            ejecutarConsulta("START TRANSACTION");

            // Marcar paso 2 como APROBADO
            $this->marcarPasoCompletado($idsolicitud, 2);

            $sql = "UPDATE solicitud_credito
                    SET estado='EN_PROCESO',
                        paso_actual=3,
                        fecha_actualizacion=NOW()
                    WHERE idsolicitud='$idsolicitud'";

            if (!ejecutarConsulta($sql)) {
                throw new Exception("No se pudo aprobar la documentación");
            }

            if (!$this->insertarWorkflow($idsolicitud, 3, $observacion, $idusuario, 'EN_PROCESO')) {
                throw new Exception("No se pudo registrar el workflow de aprobación de documentación");
            }

            ejecutarConsulta("COMMIT");
            return json_encode([
                "status" => true,
                "msg" => "Documentación aprobada, paso 3 listo"
            ]);
        } catch (Exception $e) {
            ejecutarConsulta("ROLLBACK");
            return json_encode([
                "status" => false,
                "msg" => $e->getMessage()
            ]);
        }
    }

    public function aprobarSolicitud($idsolicitud, $observacion, $idusuario, $notas_comite, $comite)
    {
        try {
            ejecutarConsulta("START TRANSACTION");

            if (empty($idsolicitud)) {
                throw new Exception("Solicitud inválida");
            }

            if (!is_array($comite) || empty($comite)) {
                throw new Exception("La solicitud no tiene responsables del comité registrados");
            }

            $sqlCotizacion = "SELECT idcotizacion
                          FROM solicitud_credito
                          WHERE idsolicitud='$idsolicitud'";

            $cotizacion = ejecutarConsultaSimpleFila($sqlCotizacion);

            if (!$cotizacion) {
                throw new Exception("No se encontró la cotización asociada");
            }

            $totalResponsables = count($comite);

            $aprobados = 0;
            $desaprobados = 0;
            $pendientes = 0;

            foreach ($comite as $responsable) {

                $idcomite = isset($responsable['idcomite_credito'])
                    ? intval($responsable['idcomite_credito'])
                    : 0;

                $estado = isset($responsable['estado'])
                    ? strtoupper(trim($responsable['estado']))
                    : 'PENDIENTE';

                if ($idcomite <= 0) {
                    throw new Exception("Uno de los integrantes del comité no es válido");
                }

                if ($estado === 'APROBADO') {
                    $aprobados++;
                } elseif ($estado === 'DESAPROBADO') {
                    $desaprobados++;
                } else {
                    $pendientes++;
                }
            }

            if ($pendientes > 0) {
                throw new Exception(
                    "Todos los responsables del comité deben emitir su decisión"
                );
            }

            if ($aprobados === $totalResponsables) {

                $estadoSolicitud = 'APROBADO';
                $estadoWorkflow = 'APROBADO';
                $observacionFinal = $observacion ?: 'Comité aprobado';
            } elseif ($desaprobados === $totalResponsables) {

                $estadoSolicitud = 'RECHAZADO';
                $estadoWorkflow = 'RECHAZADO';
                $observacionFinal = $observacion ?: 'Comité desaprobado';
            } else {

                throw new Exception(
                    "La solicitud no puede pasar al paso 5 porque existen decisiones diferentes entre los responsables del comité"
                );
            }

            foreach ($comite as $responsable) {

                $idcomite = intval($responsable['idcomite_credito']);
                $estado = strtoupper(trim($responsable['estado']));

                $sqlExiste = "SELECT idcomite_credito
                          FROM solicitud_comite
                          WHERE idsolicitud='$idsolicitud'
                          AND idcomite_credito='$idcomite'";

                $existe = ejecutarConsultaSimpleFila($sqlExiste);

                if ($existe) {

                    $sqlUpdate = "UPDATE solicitud_comite
                              SET estado='$estado'
                              WHERE idsolicitud='$idsolicitud'
                              AND idcomite_credito='$idcomite'";

                    if (!ejecutarConsulta($sqlUpdate)) {
                        throw new Exception("No se pudo actualizar la decisión del comité");
                    }
                } else {

                    $sqlInsert = "INSERT INTO solicitud_comite
                              (idsolicitud, idcomite_credito, estado)
                              VALUES
                              ('$idsolicitud', '$idcomite', '$estado')";

                    if (!ejecutarConsulta($sqlInsert)) {
                        throw new Exception("No se pudo registrar la decisión del comité");
                    }
                }
            }

            $this->marcarPasoCompletado($idsolicitud, 4);

            $sql_solicitud = "UPDATE solicitud_credito
                          SET estado='$estadoSolicitud',
                              paso_actual=5,
                              fecha_actualizacion=NOW()
                          WHERE idsolicitud='$idsolicitud'";

            if (!ejecutarConsulta($sql_solicitud)) {
                throw new Exception("No se pudo actualizar la solicitud");
            }

            $sql2 = "UPDATE solicitud_evaluacion
                 SET notas_comite='$notas_comite'
                 WHERE idsolicitud='$idsolicitud'";

            if (!ejecutarConsulta($sql2)) {
                throw new Exception("No se pudo actualizar las notas del comité");
            }

            $date = date('Y-m-d H:i:s');

            if (
                !$this->insertarWorkflow(
                    $idsolicitud,
                    5,
                    $observacionFinal,
                    $idusuario,
                    $estadoWorkflow,
                    $date
                )
            ) {
                throw new Exception("No se pudo registrar el workflow");
            }

            $fecha_aprobacion = date('Y-m-d H:i:s');

            $estadoCotizacion = $estadoSolicitud === 'APROBADO'
                ? 'APROBADO'
                : 'RECHAZADO';

            $update_cotizacion = "UPDATE cotizacion
                              SET fecha_aprobacion='$fecha_aprobacion',
                                  estado='$estadoCotizacion'
                              WHERE idcotizacion='{$cotizacion['idcotizacion']}'";

            if (!ejecutarConsulta($update_cotizacion)) {
                throw new Exception("No se pudo actualizar la cotización");
            }

            ejecutarConsulta("COMMIT");

            return json_encode([
                "status" => true,
                "msg" => $estadoSolicitud === 'APROBADO'
                    ? "Todos los responsables aprobaron la solicitud"
                    : "Todos los responsables desaprobaron la solicitud"
            ]);
        } catch (Exception $e) {

            ejecutarConsulta("ROLLBACK");

            return json_encode([
                "status" => false,
                "msg" => $e->getMessage()
            ]);
        }
    }


    public function workflow($idsolicitud)
    {

        $sql = "SELECT
                    sw.*,
                    wp.nombre paso
                FROM solicitud_workflow sw
                INNER JOIN workflow_paso wp
                    ON wp.idpaso=sw.idpaso
                WHERE sw.idsolicitud='$idsolicitud'
                ORDER BY sw.fecha_inicio ASC";

        $rspta = ejecutarConsulta($sql);

        $html = '';

        while ($reg = $rspta->fetch_object()) {

            $html .= '

            <div class="timeline">

                <div>

                    <i class="fas fa-check bg-success"></i>

                    <div class="timeline-item">

                        <span class="time">
                            ' . $reg->fecha_inicio . '
                        </span>

                        <h3 class="timeline-header">
                            ' . $reg->paso . '
                        </h3>

                        <div class="timeline-body">
                            ' . $reg->observacion . '
                        </div>

                    </div>

                </div>

            </div>';
        }

        return $html;
    }

    public function guardarVerificacionDomiciliaria($idsolicitud, $resultado, $comentarios, $idusuario, $direccion_registrada)
    {
        try {
            $row = ejecutarConsultaSimpleFila(
                "SELECT idcliente FROM solicitud_credito WHERE idsolicitud='$idsolicitud'"
            );
            if (!$row) {
                throw new Exception("Solicitud no encontrada");
            }
            $idcliente = $row['idcliente'];

            $sql = "INSERT INTO verificaciones_domiciliarias(
                        idsolicitud,
                        idcliente,
                        resultado_verificacion,
                        direccion_registrada,
                        comentarios,
                        idusuario,
                        estado
                    ) VALUES(
                        '$idsolicitud',
                        '$idcliente',
                        '$resultado',
                        '$direccion_registrada',
                        '$comentarios',
                        '$idusuario',
                        1
                    )";

            if (!ejecutarConsulta($sql)) {
                throw new Exception("No se pudo guardar la verificación domiciliaria");
            }

            return true;
        } catch (Exception $e) {
            error_log("Error en guardarVerificacionDomiciliaria: " . $e->getMessage());
            return false;
        }
    }

    public function guardarDocumento($idsolicitud, $tipo_documento, $archivo, $nombre_original, $descripcion = '')
    {
        $sql = "INSERT INTO solicitud_documento(
                    idsolicitud,
                    tipo_documento,
                    archivo,
                    nombre_original,
                    descripcion
                ) VALUES (
                    '$idsolicitud',
                    '$tipo_documento',
                    '$archivo',
                    '$nombre_original',
                    '$descripcion'
                )";

        return ejecutarConsulta($sql);
    }

    public function kpis()
    {

        $sql = "SELECT

                COUNT(*) total,

                SUM(
                    CASE
                        WHEN estado='APROBADO'
                        THEN 1
                        ELSE 0
                    END
                ) aprobados,

                SUM(
                    CASE
                        WHEN estado='OBSERVADO'
                        THEN 1
                        ELSE 0
                    END
                ) observados,

                SUM(
                    CASE
                        WHEN estado='RECHAZADO'
                        THEN 1
                        ELSE 0
                    END
                ) rechazados

                FROM solicitud_credito";

        return json_encode(
            ejecutarConsultaSimpleFila($sql)
        );
    }

    public function archivos($idsolicitud)
    {
        $sql = "SELECT
                *
            FROM solicitud_documento
            WHERE idsolicitud = '$idsolicitud'
            ORDER BY iddocumento DESC";

        $rspta = ejecutarConsulta($sql);

        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = $reg;
        }

        return json_encode($data);
    }

    public function comiteCredito($idsolicitud, $idsucursal)
    {
        $comite = (new DBQuery($this->pdo))
            ->select('cc.idcomite_credito, p.nombre, cc.cargo')
            ->from('comite_credito cc')
            ->join('personal p', 'p.idpersonal=cc.idpersonal')
            ->softDeletes('cc.deleted_at')
            ->where('cc.idsucursal', '=', $idsucursal)
            ->get();

        if (empty($comite)) {
            return Response::json([]);
        }

        foreach ($comite as &$persona) {
            $persona['estado'] = 'PENDIENTE';
        }

        if (!empty($idsolicitud)) {

            $solicitudes = (new DBQuery($this->pdo))
                ->select('idcomite_credito, estado')
                ->from('solicitud_comite')
                ->where('idsolicitud', '=', $idsolicitud)
                ->get();

            foreach ($solicitudes as $solicitud) {

                $idComiteSolicitud = intval($solicitud['idcomite_credito']);
                $estadoSolicitud = strtoupper(trim($solicitud['estado']));

                foreach ($comite as &$persona) {

                    $idComite = intval($persona['idcomite_credito']);

                    if ($idComite === $idComiteSolicitud) {
                        $persona['estado'] = $estadoSolicitud;
                        break;
                    }
                }
            }
        }

        return Response::json($comite);
    }

    public function agregarComiteCredito($idsucursal, $idpersonal, $cargo)
    {
        try {
            $saveData = (new FluentSaver($this->pdo))
                ->table('comite_credito')
                ->data([
                    'idsucursal' => $idsucursal,
                    'idpersonal' => $idpersonal,
                    'cargo' => $cargo,
                ])
                ->save();

            if (!$saveData) {
                throw new Exception("Error al crear el item", 1);
            }

            return Response::json([
                'success' => true,
                'message' => 'El integrante se ha guardado correctamente'
            ]);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }


    public function eliminarComiteCredito($idcomite_credito)
    {
        try {
            $deleted = (new FluentSaver($this->pdo))
                ->table('comite_credito')
                ->primaryKey('idcomite_credito')
                ->softDelete($idcomite_credito);

            if (!$deleted) {
                throw new Exception("No se pudo eliminar el registro");
            }


            return Response::json([
                "success" => true,
                "message" => "Registro eliminado correctamente"
            ]);
        } catch (Throwable $e) {
            return Response::error($e->getMessage());
        }
    }
}
