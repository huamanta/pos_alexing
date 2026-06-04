<?php

require "../configuraciones/Conexion.php";
require_once './Persona.php';

class Solicitudes extends Persona
{

    public function listarSolicitudes(
        $idsucursal,
        $search = '',
        $start = 0,
        $length = 10,
        $estado = '',
        $riesgo = '',
        $paso = '',
        $texto = ''
    ) {

        $where = "AND s.idsucursal = '$idsucursal'";

        if (!empty($texto)) {
            $search = $texto;
        }

        if (!empty($search)) {

            $search = mysqli_real_escape_string(
                $GLOBALS['conexion'],
                $search
            );

            $where .= " AND (
                s.codigo LIKE '%$search%'
                OR p.nombre LIKE '%$search%'
                OR s.estado LIKE '%$search%'
                OR s.riesgo LIKE '%$search%'
            )";
        }

        if (!empty($estado)) {
            $where .= " AND s.estado='$estado'";
        }

        if (!empty($riesgo)) {
            $where .= " AND s.riesgo='$riesgo'";
        }

        if (!empty($paso)) {
            $where .= " AND s.paso_actual='$paso'";
        }

        $sqlTotal = "SELECT COUNT(*) total
                    FROM solicitud_credito s
                    INNER JOIN persona p
                        ON p.idpersona=s.idcliente
                    WHERE 1=1 $where";

        $total = ejecutarConsultaSimpleFila($sqlTotal);

        $sql = "SELECT
                    s.*,
                    p.nombre cliente,
                    wp.nombre paso_actual_nombre,

                    (
                        SELECT DATEDIFF(
                            NOW(),
                            MAX(sw.fecha_inicio)
                        )
                        FROM solicitud_workflow sw
                        WHERE sw.idsolicitud=s.idsolicitud
                    ) dias_etapa

                FROM solicitud_credito s

                INNER JOIN persona p
                    ON p.idpersona=s.idcliente

                LEFT JOIN workflow_paso wp
                    ON wp.idpaso=s.paso_actual

                WHERE 1=1 $where

                ORDER BY s.idsolicitud DESC

                LIMIT $start,$length";

        $rspta = ejecutarConsulta($sql);

        $data = array();

        while ($reg = $rspta->fetch_object()) {

            $data[] = array(

                "0" => $reg->codigo,

                "1" => $reg->cliente,

                "2" => '<span class="badge badge-dark">'
                    . $reg->score .
                    '</span>',

                "3" => $reg->riesgo,

                "4" => $reg->paso_actual_nombre,

                "5" => intval($reg->dias_etapa) . ' día(s)',

                "6" => $reg->estado,

                "7" => date(
                    'd/m/Y H:i',
                    strtotime($reg->fecha_registro)
                ),

                "8" => '

                <div class="btn-group">

                    <button
                        class="btn btn-info btn-sm"
                        onclick="verSolicitud(' . $reg->idsolicitud . ')">
                        <i class="fa fa-eye"></i>
                    </button>

                    <button
                        class="btn btn-warning btn-sm"
                        onclick="verWorkflow(' . $reg->idsolicitud . ')">
                        <i class="fa fa-route"></i>
                    </button>

                    <button
                        class="btn btn-success btn-sm"
                        onclick="verArchivos(' . $reg->idsolicitud . ')">
                        <i class="fa fa-folder"></i>
                    </button>

                </div>'
            );
        }

        return array(
            "recordsTotal" => (int) $total['total'],
            "recordsFiltered" => (int) $total['total'],
            "data" => $data
        );
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
                                'SI',
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
                    p.nombre as cliente
                FROM solicitud_credito s
                INNER JOIN persona p
                    ON p.idpersona=s.idcliente
                WHERE s.idsolicitud='$idsolicitud'";

        return json_encode(
            ejecutarConsultaSimpleFila($sql)
        );
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

    public function archivos($idsolicitud)
    {

        $sql = "SELECT *
                FROM solicitud_documento
                WHERE idsolicitud='$idsolicitud'";

        $rspta = ejecutarConsulta($sql);

        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = $reg;
        }

        return json_encode($data);
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
}