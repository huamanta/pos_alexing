<?php
require_once __DIR__ . "/../configuraciones/Conexion.php";
require_once __DIR__ . "/Helpers.php";

class Guia extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }

    // ================== INSERTAR ==================
    public function insertar(
        $idsucursal,
        $idcliente,
        $idpersonal,
        $serie,
        $numero,
        $fecha_emision,
        $fecha_traslado,
        $factura_ref,
        $fecha_factura_ref,
        $tipo_transporte,
        $idtransportista,
        $peso,
        $punto_partida,
        $ubigeo_partida,
        $punto_llegada,
        $ubigeo_llegada,
        $atencion,
        $referencia,
        $idtrabajador,
        $idmotivo,
        $motivo_traslado_otro,
        $ord_compra,
        $ord_pedido,
        $observacion,
        $idproducto,
        $idproducto_configuracion,
        $idserie,
        $codigo,
        $nombre_producto,
        $cantidad,
        $unidad,
        $peso_det,
        $bultos,
        $lotes
    ) {

        try {

            $this->pdo->beginTransaction();

            // GUÍA
            $idguia = (new FluentSaver($this->pdo))
                ->table('guia_remision')
                ->data([
                    'idsucursal' => $idsucursal,
                    'idcliente' => $idcliente,
                    'idpersonal' => $idpersonal,
                    'serie_comprobante' => $serie,
                    'num_comprobante' => $numero,
                    'fecha_emision' => $fecha_emision,
                    'fecha_traslado' => $fecha_traslado,
                    'factura_ref' => $factura_ref,
                    'fecha_factura_ref' => $fecha_factura_ref,
                    'tipo_transporte' => $tipo_transporte,
                    'idtransportista' => $idtransportista,
                    'peso' => $peso,
                    'estado' => 'Por Enviar',
                    'punto_partida' => $punto_partida,
                    'ubigeo_partida' => $ubigeo_partida,
                    'punto_llegada' => $punto_llegada,
                    'ubigeo_llegada' => $ubigeo_llegada,
                    'atencion' => $atencion,
                    'referencia' => $referencia,
                    'idtrabajador' => $idtrabajador,
                    'idmotivo' => $idmotivo,
                    'motivo_traslado_otro' => $motivo_traslado_otro,
                    'ord_compra' => $ord_compra,
                    'ord_pedido' => $ord_pedido,
                    'observacion' => $observacion
                ])
                ->save();

            if (!$idguia) {
                throw new Exception('No se pudo registrar la guía de remisión.');
            }

            //DETALLES
            for ($i = 0; $i < count($idproducto); $i++) {

                $detalleSaver = (new FluentSaver($this->pdo))
                    ->table('detalle_guia')
                    ->data([
                        'idguia' => $idguia,
                        'idproducto' => $idproducto[$i],
                        'idproducto_configuracion' => $idproducto_configuracion[$i],
                        'idserie' => $idserie[$i],
                        'codigo' => $codigo[$i],
                        'nombre_producto' => $nombre_producto[$i],
                        'cantidad' => $cantidad[$i],
                        'unidad' => $unidad[$i],
                        'peso' => $peso_det[$i],
                        'bultos' => $bultos[$i],
                        'lotes' => $lotes[$i]
                    ])
                    ->save();

                if (!$detalleSaver) {
                    throw new Exception("No se pudo registrar el detalle {$i}.");
                }
            }
            $this->pdo->commit();
            return Response::json([
                "success" => true,
                "message" => "Gia de remisión creada correctamente."
            ]);

        } catch (Throwable $e) {
            $this->pdo->rollBack();
            Response::error($e->getMessage());
        }
    }

    // ================== EDITAR ==================
    public function editar(
        $idguia,
        $idsucursal,
        $idcliente,
        $idpersonal,
        $serie,
        $numero,
        $fecha_emision,
        $fecha_traslado,
        $factura_ref,
        $fecha_factura_ref,
        $tipo_transporte,
        $idtransportista,
        $peso,
        $punto_partida,
        $ubigeo_partida,
        $punto_llegada,
        $ubigeo_llegada,
        $atencion,
        $referencia,
        $idtrabajador,
        $idmotivo,
        $ord_compra,
        $ord_pedido,
        $observacion,
        $idproducto,
        $codigo,
        $nombre_producto,
        $cantidad,
        $unidad,
        $peso_det,
        $bultos,
        $lotes
    ) {
        $sql = "UPDATE guia_remision SET
                    idsucursal='$idsucursal',
                    idcliente='$idcliente',
                    idpersonal='$idpersonal',
                    serie_comprobante='$serie',
                    num_comprobante='$numero',
                    fecha_emision='$fecha_emision',
                    fecha_traslado='$fecha_traslado',
                    factura_ref='$factura_ref',
                    fecha_factura_ref='$fecha_factura_ref',
                    tipo_transporte='$tipo_transporte',
                    idtransportista='$idtransportista',
                    peso='$peso',
                    punto_partida='$punto_partida',
                    ubigeo_partida='$ubigeo_partida',
                    punto_llegada='$punto_llegada',
                    ubigeo_llegada='$ubigeo_llegada',
                    atencion='$atencion',
                    referencia='$referencia',
                    idtrabajador='$idtrabajador',
                    idmotivo='$idmotivo',
                    ord_compra='$ord_compra',
                    ord_pedido='$ord_pedido',
                    observacion='$observacion'
                WHERE idguia='$idguia'";
        ejecutarConsulta($sql);

        // Borrar detalle anterior
        $sqldel = "DELETE FROM detalle_guia WHERE idguia='$idguia'";
        ejecutarConsulta($sqldel);

        // Insertar nuevo detalle
        $num_elementos = 0;
        $sw = true;

        while ($num_elementos < count($idproducto)) {
            $sql_detalle = "INSERT INTO detalle_guia(
                                idguia, idproducto, codigo, nombre_producto,
                                cantidad, unidad, peso, bultos, lotes
                            ) VALUES (
                                '$idguia', '$idproducto[$num_elementos]', '$codigo[$num_elementos]',
                                '$nombre_producto[$num_elementos]', '$cantidad[$num_elementos]', '$unidad[$num_elementos]',
                                '$peso_det[$num_elementos]', '$bultos[$num_elementos]', '$lotes[$num_elementos]'
                            )";
            ejecutarConsulta($sql_detalle) or $sw = false;
            $num_elementos++;
        }

        return $sw;
    }

    // ================== MOSTRAR ==================
    public function mostrar($idguia)
    {
        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('guia_remision')
            ->where('idguia', '=', $idguia)
            ->first();
        $data = [
            'guia' => $data,
            'detalles' => self::listarDetalles($idguia)
        ];
        return Response::json($data);
    }

    // ================== LISTAR ==================
    public function listar($idsucursal, $fecha_inicio, $fecha_fin, $estado)
    {
        $sql = "SELECT g.idguia, g.serie_comprobante AS serie, g.num_comprobante AS numero,
                       g.fecha_emision, g.factura_ref, g.estado, g.atencion, g.estado_sunat, g.resumen_sunat,
                       p.nombre AS cliente
                FROM guia_remision g
                INNER JOIN persona p ON g.idcliente=p.idpersona
                WHERE g.fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'";

        if ($estado != 'Todos')
            $sql .= " AND g.estado='$estado'";
        if ($idsucursal != '' && $idsucursal != '0')
            $sql .= " AND g.idsucursal='$idsucursal'";

        $sql .= " ORDER BY g.idguia DESC";
        return ejecutarConsulta($sql);
    }

    // ================== LISTAR DETALLES ==================
    public function listarDetalles($idguia)
    {
        return (new DBQuery($this->pdo))
            ->select('*')
            ->from('detalle_guia')
            ->where('idguia', '=', $idguia)
            ->get();
    }

    public function anular($idguia)
    {
        $sql = "UPDATE guia_remision SET estado='Anulado' WHERE idguia='$idguia'";
        return ejecutarConsulta($sql);
    }

    public function send_sunat($idguia, $hash)
    {
        $sql = "UPDATE guia_remision SET estado_sunat='1',resumen_sunat='Aceptado',hash_cpe='$hash' WHERE idguia='$idguia'";
        return ejecutarConsulta($sql);
    }

    public function baja_sunat($idguia, $ticket)
    {
        $sql = "UPDATE guia_remision SET estado='Anulado',estado_sunat='3',ticket_baja='$ticket' WHERE idguia='$idguia'";
        return ejecutarConsulta($sql);
    }

    public function mostrarCabecera($idguia)
    {
        return (new DBQuery($this->pdo))
            ->select('g.*, s.nombre AS sucursal, p.nombre AS cliente, p.num_documento, p.direccion, 
        tr.nombre AS transportista, tr.num_documento AS num_documento_trans, pe.nombre AS personal')
            ->from('guia_remision g')
            ->join('sucursal s', 'g.idsucursal = s.idsucursal')
            ->join('persona p', 'g.idcliente = p.idpersona')
            ->join('persona tr', 'g.idtransportista = tr.idpersona')
            ->join('personal pe', 'g.idpersonal = pe.idpersonal')
            ->where('g.idguia', '=', $idguia)
            ->first();
    }

    public function listarDetalleTicket($idguia): array
    {
        return (new DBQuery($this->pdo))
            ->select('*')
            ->from('detalle_guia dg')
            ->join('producto p', 'p.idproducto = dg.idproducto')
            ->leftJoin('producto_configuracion pg', 'pg.idproducto_configuracion = dg.idproducto_configuracion')
            ->where('idguia', '=', $idguia)
            ->get();
    }

    public function getDepartamentos()
    {
        $sql = "SELECT * FROM ubigeo_peru_departments";
        return ejecutarConsulta($sql);
    }

    public function getProvincias($iddepartamento)
    {
        $sql = "SELECT * FROM ubigeo_peru_provinces WHERE department_id='$iddepartamento'";
        return ejecutarConsulta($sql);
    }

    public function getDistritos($idprovincia)
    {
        $sql = "SELECT * FROM ubigeo_peru_districts WHERE province_id='$idprovincia'";
        return ejecutarConsulta($sql);
    }
}
?>