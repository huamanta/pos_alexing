<?php
require "../configuraciones/Conexion.php";

class Resumen
{
    public function __construct()
    {
    }

        public function listarBoletasParaResumen($fecha_inicio, $fecha_fin, $idsucursal)
        {
            $sql = "SELECT
                        v.idventa,
                        v.tipo_comprobante,
                        v.serie_comprobante,
                        v.num_comprobante,
                        v.total_venta,
                        v.impuesto,
                        v.estado,
                        p.num_documento as cliente_doc,
                        p.tipo_documento as cliente_tipo_doc
                    FROM venta v
                    INNER JOIN persona p ON v.idcliente = p.idpersona
                    WHERE v.tipo_comprobante = 'Boleta'
                    AND DATE(v.fecha_hora) >= '$fecha_inicio'
                    AND DATE(v.fecha_hora) <= '$fecha_fin'
                    AND v.idsucursal = '$idsucursal'
                    AND v.estado = 'Por Enviar'";        
        return ejecutarConsulta($sql);
    }

    public function listarDetalleVenta($idventa)
    {
        $sql="SELECT
                dv.idproducto,
                dv.cantidad,
                dv.precio_venta,
                (dv.cantidad * dv.precio_venta) as subtotal,
                p.proigv,
                p.codigo as codigo_producto
              FROM detalle_venta dv
              INNER JOIN producto_configuracion pc ON dv.idproducto = pc.id
              INNER JOIN producto p ON pc.idproducto = p.idproducto
              WHERE dv.idventa='$idventa'";

        return ejecutarConsulta($sql);
    }

    public function listarResumenes($fecha_inicio, $fecha_fin, $idsucursal)
    {
        $sql = "SELECT *, nombre_xml FROM resumen_diario
                WHERE fecha_generacion >= '$fecha_inicio' AND fecha_generacion <= '$fecha_fin' AND idsucursal = '$idsucursal'
                ORDER BY idresumen DESC";
        return ejecutarConsulta($sql);
    }}
?>