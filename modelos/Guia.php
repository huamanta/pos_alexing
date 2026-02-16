<?php
require "../configuraciones/Conexion.php";

class Guia
{
    public function __construct() {}

    // ================== INSERTAR ==================
    public function insertar(
        $idsucursal, $idcliente, $idpersonal, $serie, $numero,
        $fecha_emision, $fecha_traslado, $factura_ref, $fecha_factura_ref,
        $tipo_transporte, $idtransportista, $peso, $punto_partida, $ubigeo_partida,
        $punto_llegada, $ubigeo_llegada, $atencion, $referencia,
        $idtrabajador, $idmotivo, $ord_compra, $ord_pedido, $observacion,
        $idproducto, $codigo, $nombre_producto, $cantidad, $unidad, $peso_det, $bultos, $lotes
    ) {
        $sql = "INSERT INTO guia_remision (
                    idsucursal, idcliente, idpersonal, serie_comprobante, num_comprobante,
                    fecha_emision, fecha_traslado, factura_ref, fecha_factura_ref,
                    tipo_transporte, idtransportista, peso, estado, punto_partida, ubigeo_partida,
                    punto_llegada, ubigeo_llegada, atencion, referencia, idtrabajador,
                    idmotivo, ord_compra, ord_pedido, observacion
                ) VALUES (
                    '$idsucursal', '$idcliente', '$idpersonal', '$serie', '$numero',
                    '$fecha_emision', '$fecha_traslado', '$factura_ref', '$fecha_factura_ref',
                    '$tipo_transporte', '$idtransportista', '$peso', 'Por Enviar', '$punto_partida', '$ubigeo_partida',
                    '$punto_llegada', '$ubigeo_llegada', '$atencion', '$referencia', '$idtrabajador',
                    '$idmotivo', '$ord_compra', '$ord_pedido', '$observacion'
                )";
        $idguianew = ejecutarConsulta_retornarID($sql);

        // Insertar detalle
        $num_elementos = 0;
        $sw = true;

        while ($num_elementos < count($idproducto)) {
            $sql_detalle = "INSERT INTO detalle_guia(
                                idguia, idproducto, codigo, nombre_producto,
                                cantidad, unidad, peso, bultos, lotes
                            ) VALUES (
                                '$idguianew', '$idproducto[$num_elementos]', '$codigo[$num_elementos]',
                                '$nombre_producto[$num_elementos]', '$cantidad[$num_elementos]', '$unidad[$num_elementos]',
                                '$peso_det[$num_elementos]', '$bultos[$num_elementos]', '$lotes[$num_elementos]'
                            )";
            ejecutarConsulta($sql_detalle) or $sw = false;
            $num_elementos++;
        }

        return $sw;
    }

    // ================== EDITAR ==================
    public function editar(
        $idguia, $idsucursal, $idcliente, $idpersonal, $serie, $numero,
        $fecha_emision, $fecha_traslado, $factura_ref, $fecha_factura_ref,
        $tipo_transporte, $idtransportista, $peso, $punto_partida, $ubigeo_partida,
        $punto_llegada, $ubigeo_llegada, $atencion, $referencia,
        $idtrabajador, $idmotivo, $ord_compra, $ord_pedido, $observacion,
        $idproducto, $codigo, $nombre_producto, $cantidad, $unidad, $peso_det, $bultos, $lotes
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
    public function mostrar($idguia) {
        $sql = "SELECT * FROM guia_remision WHERE idguia='$idguia'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // ================== LISTAR ==================
    public function listar($idsucursal, $fecha_inicio, $fecha_fin, $estado) {
        $sql = "SELECT g.idguia, g.serie_comprobante AS serie, g.num_comprobante AS numero,
                       g.fecha_emision, g.factura_ref, g.estado, g.atencion,
                       p.nombre AS cliente
                FROM guia_remision g
                INNER JOIN persona p ON g.idcliente=p.idpersona
                WHERE g.fecha_emision BETWEEN '$fecha_inicio' AND '$fecha_fin'";

        if ($estado != 'Todos') $sql .= " AND g.estado='$estado'";
        if ($idsucursal != '' && $idsucursal != '0') $sql .= " AND g.idsucursal='$idsucursal'";

        $sql .= " ORDER BY g.idguia DESC";
        return ejecutarConsulta($sql);
    }

    // ================== LISTAR DETALLES ==================
    public function listarDetalles($idguia) {
        $sql = "SELECT * FROM detalle_guia WHERE idguia='$idguia'";
        return ejecutarConsulta($sql);
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
        $sql = "SELECT g.idguia, s.nombre AS sucursal, p.nombre AS cliente, p.num_documento, p.direccion, 
        tr.nombre AS transportista, tr.num_documento AS num_documento_trans, pe.nombre AS personal, 
        g.serie_comprobante, g.num_comprobante, g.fecha_emision, g.fecha_traslado, g.punto_partida, g.punto_llegada, g.estado
        FROM guia_remision g 
        INNER JOIN sucursal s ON g.idsucursal = s.idsucursal
        INNER JOIN persona p ON g.idcliente = p.idpersona
        INNER JOIN persona tr ON g.idtransportista = tr.idpersona
        INNER JOIN personal pe ON g.idpersonal = pe.idpersonal
        WHERE g.idguia='$idguia'";
        return ejecutarConsulta($sql);
    }

    public function listarDetalleTicket($idguia)
    {
      $sql = "SELECT * FROM detalle_guia WHERE idguia='$idguia'";
      return ejecutarConsulta($sql);
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
