<?php  
require "../configuraciones/Conexion.php";

class ReporteConsolidado
{
    public function __construct(){}

    // Convierte ejecutarConsulta() en un array sin modificar Conexion.php
    private function consultaToArray($sql)
    {
        $query = ejecutarConsulta($sql);
        $data = array();

        while ($reg = $query->fetch_object()) {
            $data[] = $reg;
        }
        return $data;
    }

    public function listarVentasDetalle($inicio, $fin, $sucursal)
    {
        $sql = "
            SELECT 
                v.fecha_hora,
                CONCAT(v.tipo_comprobante, ' ', v.serie_comprobante, '-', v.num_comprobante) AS tipo_comprobante,
                p.nombre AS producto,
                dv.cantidad,
                dv.precio_venta,
                dv.cantidad * dv.precio_venta AS subtotal
            FROM venta v
            INNER JOIN detalle_venta dv ON dv.idventa = v.idventa
            INNER JOIN producto p ON p.idproducto = dv.idproducto
            WHERE v.estado NOT IN ('Anulado','Nota Crédito')
            AND v.ventacredito = 'No'
            AND v.idsucursal = '$sucursal'
            AND DATE(v.fecha_hora) BETWEEN '$inicio' AND '$fin'
            ORDER BY v.fecha_hora DESC
        ";
        return $this->consultaToArray($sql);
    }

    public function listarComprasDetalle($inicio, $fin, $sucursal)
    {
        $sql = "
            SELECT 
                c.fecha_hora,
                CONCAT(c.tipo_comprobante, ' ', c.serie_comprobante, '-', c.num_comprobante) AS comprobante,
                pr.nombre AS proveedor,
                p.nombre AS producto,
                dc.cantidad,
                dc.precio_compra,
                dc.cantidad * dc.precio_compra AS subtotal
            FROM compra c
            INNER JOIN detalle_compra dc ON dc.idcompra = c.idcompra
            INNER JOIN producto p ON p.idproducto = dc.idproducto
            INNER JOIN persona pr ON pr.idpersona = c.idproveedor
            WHERE c.idsucursal = '$sucursal'
            AND DATE(c.fecha_hora) BETWEEN '$inicio' AND '$fin'
            ORDER BY c.fecha_hora DESC
        ";
        return $this->consultaToArray($sql);
    }

    public function listarIngresosDetalle($inicio, $fin, $sucursal)
    {
        $sql = "
            SELECT 
                m.fecha,
                ca.descripcion,
                m.monto
            FROM movimiento m
            INNER JOIN concepto_movimiento ca ON m.idconcepto_movimiento = ca.idconcepto_movimiento
            WHERE m.tipo = 'Ingresos'
            AND m.idsucursal = '$sucursal'
            AND DATE(m.fecha) BETWEEN '$inicio' AND '$fin'
            ORDER BY m.fecha DESC
        ";
        return $this->consultaToArray($sql);
    }

    public function listarEgresosDetalle($inicio, $fin, $sucursal)
    {
        $sql = "
            SELECT 
                m.fecha,
                ca.descripcion,
                m.monto
            FROM movimiento m
            INNER JOIN concepto_movimiento ca ON m.idconcepto_movimiento = ca.idconcepto_movimiento
            WHERE m.tipo = 'Egresos'
            AND m.idsucursal = '$sucursal'
            AND DATE(m.fecha) BETWEEN '$inicio' AND '$fin'
            ORDER BY m.fecha DESC
        ";
        return $this->consultaToArray($sql);
    }

   public function listarAmortizaciones($inicio, $fin, $sucursal)
{
    $sql = "
        SELECT 
            v.idventa,
            v.tipo_comprobante,
            CONCAT(v.tipo_comprobante, '-', v.serie_comprobante, '-', v.num_comprobante) AS comprobante,
            v.total_venta AS monto_total,  -- El total de la venta (ingresos)
            SUM(dcc.montopagado) AS monto_pagado,  -- Monto pagado con efectivo
            SUM(dcc.montotarjeta) AS monto_tarjeta,  -- Monto pagado con tarjeta
            (SUM(dcc.montopagado) + SUM(dcc.montotarjeta)) AS monto_total_amortizacion,  -- Total amortizado (efectivo + tarjeta)
            MAX(dcc.fechapago) AS fecha_ultima_amortizacion,  -- Fecha de la última amortización
            GROUP_CONCAT(DATE(dcc.fechapago) ORDER BY dcc.fechapago ASC SEPARATOR ', ') AS lista_fechas_amortizacion,  -- Listado de fechas de amortización
            GROUP_CONCAT((dcc.montopagado + dcc.montotarjeta) ORDER BY dcc.fechapago ASC SEPARATOR ', ') AS lista_montos_amortizacion,  -- Listado de montos amortizados
            p.precio_compra,  -- Precio de compra por producto
            -- Cálculo de la utilidad sin comisión (utilidadSC)
            (CASE 
                WHEN dv.check_precio = 1 THEN dv.precio_venta
                ELSE (dv.cantidad * dv.precio_venta)
            END - ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra)) AS utilidadSC,  -- Utilidad sin comisión
            -- Aquí multiplicamos la cantidad total (cantidad * contenedor) por el precio de compra unitario
            ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra) AS total_compra,  -- Total de la compra
            -- Ajuste a la utilidad si los abonos superan el total de compra
            (CASE 
                WHEN (SUM(dcc.montopagado) + SUM(dcc.montotarjeta)) > ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra) 
                THEN ((SUM(dcc.montopagado) + SUM(dcc.montotarjeta)) - ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra))
                ELSE 0
            END) AS ajuste_utilidad  -- Ajuste de utilidad si abonos son mayores que compra
        FROM detalle_cuentas_por_cobrar dcc
        INNER JOIN cuentas_por_cobrar cpc ON cpc.idcpc = dcc.idcpc
        INNER JOIN venta v ON cpc.idventa = v.idventa
        INNER JOIN detalle_venta dv ON dv.idventa = v.idventa  -- Relación con los productos vendidos
        INNER JOIN producto_configuracion pg ON dv.idproducto = pg.id
        INNER JOIN producto p ON pg.idproducto = p.idproducto
        WHERE DATE(dcc.fechapago) BETWEEN '$inicio' AND '$fin'
        AND v.idsucursal = '$sucursal'
        GROUP BY v.idventa, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, v.total_venta
        ORDER BY fecha_ultima_amortizacion DESC";

    return $this->consultaToArray($sql);
}





    public function resumenConsolidado($inicio, $fin, $sucursal)
    {
        $sql = "
            SELECT 
                (SELECT IFNULL(SUM(total_venta), 0)
                 FROM venta 
                 WHERE DATE(fecha_hora) BETWEEN '$inicio' AND '$fin'
                 AND idsucursal='$sucursal' AND ventacredito = 'No') AS ventas,

                (SELECT IFNULL(SUM(total_compra), 0)
                 FROM compra 
                 WHERE DATE(fecha_hora) BETWEEN '$inicio' AND '$fin'
                 AND idsucursal='$sucursal') AS compras,

                (SELECT IFNULL(SUM(monto), 0)
                 FROM movimiento 
                 WHERE tipo='Ingresos'
                 AND DATE(fecha) BETWEEN '$inicio' AND '$fin'
                 AND idsucursal='$sucursal') AS ingresos,

                (SELECT IFNULL(SUM(monto), 0)
                 FROM movimiento 
                 WHERE tipo='Egresos'
                 AND DATE(fecha) BETWEEN '$inicio' AND '$fin'
                 AND idsucursal='$sucursal') AS egresos,

                -- Cálculo de la Utilidad Real
                (SELECT IFNULL(SUM(
                (CASE 
                    WHEN dv.check_precio = 1 THEN dv.precio_venta
                    ELSE (dv.cantidad * dv.precio_venta)
                END)
                -
                ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra)
            ), 0)
            FROM detalle_venta dv
            INNER JOIN venta v ON v.idventa = dv.idventa
            INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
            INNER JOIN producto p ON p.idproducto = pg.idproducto
            WHERE DATE(v.fecha_hora) BETWEEN '$inicio' AND '$fin'
            AND v.idsucursal='$sucursal'
            AND v.ventacredito='No'
            AND v.estado NOT IN ('Anulado','Nota Credito')
            ) AS utilidad_real,

            -- TOTAL EGRESOS
            (SELECT IFNULL(SUM(m.monto),0)
             FROM movimiento m
             WHERE m.tipo='Egresos'
             AND DATE(m.fecha) BETWEEN '$inicio' AND '$fin'
             AND m.idsucursal='$sucursal') AS egresos,

            -- INCLUIR AMORTIZACIONES + SU UTILIDAD
            (SELECT IFNULL(SUM(dcc.montopagado + dcc.montotarjeta),0)
             FROM detalle_cuentas_por_cobrar dcc
             INNER JOIN cuentas_por_cobrar cpc ON cpc.idcpc = dcc.idcpc
             INNER JOIN venta v ON v.idventa = cpc.idventa
             WHERE DATE(dcc.fechapago) BETWEEN '$inicio' AND '$fin'
             AND v.idsucursal='$sucursal'
            ) AS amortizaciones
        ";

        return ejecutarConsultaSimpleFila($sql);
    }

public function resumenPorMeses($inicio, $fin, $sucursal)
{
    $sql = "
        WITH meses AS (
            SELECT DISTINCT DATE_FORMAT(fecha_hora,'%Y-%m-01') AS mes 
            FROM venta
            WHERE idsucursal='$sucursal' AND DATE(fecha_hora) BETWEEN '$inicio' AND '$fin'

            UNION

            SELECT DISTINCT DATE_FORMAT(fecha_hora,'%Y-%m-01') 
            FROM compra
            WHERE idsucursal='$sucursal' AND DATE(fecha_hora) BETWEEN '$inicio' AND '$fin'

            UNION

            SELECT DISTINCT DATE_FORMAT(fecha,'%Y-%m-01') 
            FROM movimiento
            WHERE idsucursal='$sucursal' AND DATE(fecha) BETWEEN '$inicio' AND '$fin'

            UNION

            SELECT DISTINCT DATE_FORMAT(dcc.fechapago,'%Y-%m-01')
            FROM detalle_cuentas_por_cobrar dcc
            INNER JOIN cuentas_por_cobrar cpc ON cpc.idcpc = dcc.idcpc
            INNER JOIN venta v ON v.idventa = cpc.idventa
            WHERE v.idsucursal='$sucursal'
              AND DATE(dcc.fechapago) BETWEEN '$inicio' AND '$fin'
        )

        SELECT 
            DATE_FORMAT(m.mes,'%Y-%m') AS mes,
            ELT(MONTH(m.mes),
                'Enero','Febrero','Marzo','Abril','Mayo','Junio',
                'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
            ) AS mes_nombre,

            IFNULL(v.total_ventas,0) AS total_ventas,
            IFNULL(c.total_compras,0) AS total_compras,
            IFNULL(i.total_ingresos,0) AS total_ingresos,
            IFNULL(e.total_egresos,0) AS total_egresos,

            IFNULL(a.total_amortizaciones,0) AS amortizaciones,
            IFNULL(a.utilidad_amortizaciones,0) AS utilidad_amortizaciones,

            (
                IFNULL(u.utilidad_real,0)
                + IFNULL(a.utilidad_amortizaciones,0)
                - IFNULL(e.total_egresos,0)
            ) AS utilidad

        FROM meses m

        -- Ventas al contado
        LEFT JOIN (
            SELECT DATE_FORMAT(fecha_hora,'%Y-%m-01') AS mes, SUM(total_venta) AS total_ventas
            FROM venta
            WHERE idsucursal='$sucursal' AND DATE(fecha_hora) BETWEEN '$inicio' AND '$fin' AND ventacredito = 'No'
            GROUP BY DATE_FORMAT(fecha_hora,'%Y-%m-01')
        ) v ON v.mes = m.mes

        -- Compras
        LEFT JOIN (
            SELECT DATE_FORMAT(fecha_hora,'%Y-%m-01') AS mes, SUM(total_compra) AS total_compras
            FROM compra
            WHERE idsucursal='$sucursal' AND DATE(fecha_hora) BETWEEN '$inicio' AND '$fin'
            GROUP BY DATE_FORMAT(fecha_hora,'%Y-%m-01')
        ) c ON c.mes = m.mes

        -- Ingresos
        LEFT JOIN (
            SELECT DATE_FORMAT(fecha,'%Y-%m-01') AS mes, SUM(monto) AS total_ingresos
            FROM movimiento
            WHERE tipo='Ingresos' AND idsucursal='$sucursal' AND DATE(fecha) BETWEEN '$inicio' AND '$fin'
            GROUP BY DATE_FORMAT(fecha,'%Y-%m-01')
        ) i ON i.mes = m.mes

        -- Egresos
        LEFT JOIN (
            SELECT DATE_FORMAT(fecha,'%Y-%m-01') AS mes, SUM(monto) AS total_egresos
            FROM movimiento
            WHERE tipo='Egresos' AND idsucursal='$sucursal' AND DATE(fecha) BETWEEN '$inicio' AND '$fin'
            GROUP BY DATE_FORMAT(fecha,'%Y-%m-01')
        ) e ON e.mes = m.mes

        -- Amortizaciones con utilidad correcta
        LEFT JOIN (
            SELECT 
                A.mes,
                A.total_amortizaciones,
                IFNULL(C.total_compra,0) AS total_compra,
                A.total_amortizaciones - IFNULL(C.total_compra,0) AS utilidad_amortizaciones
            FROM 
            (
                SELECT 
                    DATE_FORMAT(dcc.fechapago,'%Y-%m-01') AS mes,
                    SUM(dcc.montopagado + dcc.montotarjeta) AS total_amortizaciones
                FROM detalle_cuentas_por_cobrar dcc
                INNER JOIN cuentas_por_cobrar cpc ON cpc.idcpc = dcc.idcpc
                INNER JOIN venta v ON v.idventa = cpc.idventa
                WHERE DATE(dcc.fechapago) BETWEEN '$inicio' AND '$fin'
                  AND v.idsucursal='$sucursal'
                GROUP BY DATE_FORMAT(dcc.fechapago,'%Y-%m-01')
            ) AS A
            LEFT JOIN
            (
                SELECT 
                    DATE_FORMAT(dcc.fechapago,'%Y-%m-01') AS mes,
                    SUM(DISTINCT dv.iddetalle_venta * 0 + dv.cantidad * dv.cantidad_contenedor * p.precio_compra) AS total_compra
                FROM detalle_cuentas_por_cobrar dcc
                INNER JOIN cuentas_por_cobrar cpc ON cpc.idcpc = dcc.idcpc
                INNER JOIN venta v ON v.idventa = cpc.idventa
                INNER JOIN detalle_venta dv ON dv.idventa = v.idventa
                INNER JOIN producto_configuracion pg ON dv.idproducto = pg.id
                INNER JOIN producto p ON p.idproducto = pg.idproducto
                WHERE DATE(dcc.fechapago) BETWEEN '$inicio' AND '$fin'
                  AND v.idsucursal='$sucursal'
                GROUP BY DATE_FORMAT(dcc.fechapago,'%Y-%m-01')
            ) AS C ON C.mes = A.mes
        ) a ON a.mes = m.mes

        -- Utilidad real de ventas al contado
        LEFT JOIN (
            SELECT 
                DATE_FORMAT(v.fecha_hora,'%Y-%m-01') AS mes,
                SUM(
                    (CASE WHEN dv.check_precio = 1 THEN dv.precio_venta ELSE (dv.cantidad * dv.precio_venta) END)
                    - (dv.cantidad * dv.cantidad_contenedor * p.precio_compra)
                ) AS utilidad_real
            FROM detalle_venta dv
            INNER JOIN venta v ON v.idventa = dv.idventa
            INNER JOIN producto_configuracion pg ON dv.idproducto = pg.id
            INNER JOIN producto p ON p.idproducto = pg.idproducto
            WHERE DATE(v.fecha_hora) BETWEEN '$inicio' AND '$fin'
              AND v.idsucursal='$sucursal'
              AND v.ventacredito='No'
            GROUP BY DATE_FORMAT(v.fecha_hora,'%Y-%m-01')
        ) u ON u.mes = m.mes

        ORDER BY m.mes DESC
    ";

    return $this->consultaToArray($sql);
}


public function obtenerDatosNegocio() {
    $sql = "SELECT * FROM datos_negocio WHERE condicion = '1' LIMIT 1";
    return ejecutarConsultaSimpleFila($sql);
}


}
?>
