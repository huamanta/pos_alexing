<?php
//Inclu¨ªmos inicialmente la conexi¨®n a la base de datos
require "../configuraciones/Conexion.php";
date_default_timezone_set('America/Lima');

class Consultas
{
	//Implementamos nuestro constructor
	public function __construct()
	{
	}

	public function TotalUtilidadNetaPV($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal, $idproducto)
{
    // Si idsucursal es un array, convertir a lista
    if (is_array($idsucursal)) {
        $idsucursal_list = implode(",", $idsucursal);
    } else {
        $idsucursal_list = $idsucursal;
    }

    $sql = "SELECT 
                ROUND(
                    IFNULL(SUM(utilidad),0) 
                    - IFNULL((
                        SELECT SUM(m.monto) 
                        FROM movimiento m 
                        WHERE m.tipo = 'Egresos'
                          AND DATE(m.fecha) BETWEEN '$fecha_inicio' AND '$fecha_fin'";

    // Filtros para egresos
    if ($idvendedor != "Todos" && !empty($idvendedor)) {
        $sql .= " AND m.idpersonal = '$idvendedor'";
    }

    if ($idsucursal != "Todos" && $idsucursal != "0" && !empty($idsucursal)) {
        if (strpos($idsucursal_list, ",") !== false) {
            $sql .= " AND m.idsucursal IN ($idsucursal_list)";
        } else {
            $sql .= " AND m.idsucursal = '$idsucursal_list'";
        }
    }

    $sql .= "), 0)
                , 2) AS utilidad_neta
            FROM (
                SELECT 
                    (
                        CASE 
                            WHEN dv.check_precio = 1 THEN dv.precio_venta
                            ELSE (dv.cantidad * dv.precio_venta)
                        END
                        - ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra)
                    ) AS utilidad
                FROM detalle_venta dv
                INNER JOIN venta v ON v.idventa = dv.idventa
                INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
                INNER JOIN producto p ON p.idproducto = pg.idproducto
                WHERE v.ventacredito = 'No'
                  AND v.estado IN ('Aceptado','Por Enviar','Activado')
                  AND v.tipo_comprobante IN ('Nota de Venta','Boleta','Factura')
                  AND DATE(v.fecha_hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'";

    // Filtros de ventas
    if ($idvendedor != "Todos" && !empty($idvendedor)) {
        $sql .= " AND v.idPersonal = '$idvendedor'";
    }

    if ($idproducto != "Todos" && !empty($idproducto)) {
        $sql .= " AND p.idproducto = '$idproducto'";
    }

    if ($idsucursal != "Todos" && $idsucursal != "0" && !empty($idsucursal)) {
        if (strpos($idsucursal_list, ",") !== false) {
            $sql .= " AND v.idsucursal IN ($idsucursal_list)";
        } else {
            $sql .= " AND v.idsucursal = '$idsucursal_list'";
        }
    }

    $sql .= ") AS subquery";

    return ejecutarConsultaSimpleFila($sql);
}



	public function TotalCantidadPV2($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal,$idproducto)
		{
		    // Comienza con la parte común de la consulta
		    $sql = "SELECT ROUND(IFNULL(SUM(cantidad),0), 2) as total_cantidad
		            FROM (SELECT (dv.cantidad * dv.precio_venta) as precio, 
		                         (dv.cantidad * dv.cantidad_contenedor) as cantidad ,
		                         v.descuento as descuento 
		                  FROM detalle_venta dv
		                  INNER JOIN venta v ON v.idventa = dv.idventa
		                  INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
		                  INNER JOIN producto p ON p.idproducto = pg.idproducto
		                  INNER JOIN persona pe ON pe.idpersona = v.idcliente
		                  WHERE v.ventacredito = 'Si' 
		                    AND v.tipo_comprobante IN ('Nota de Venta','Boleta','Factura')
		                    AND v.estado IN ('Activado','Aceptado','Por Enviar')
		                    AND DATE(v.fecha_hora) >= '$fecha_inicio'
		                    AND DATE(v.fecha_hora) <= '$fecha_fin' ";

		    // Condiciones adicionales basadas en los parámetros proporcionados
		    if ($idvendedor != "Todos" && $idvendedor != null) {
		        $sql .= " AND v.idcliente = '$idvendedor'";
		    }
		    if ($idsucursal != "Todos" && $idsucursal != null) {
		        $sql .= " AND v.idsucursal = '$idsucursal'";
		    }
		     if ($idproducto != "Todos" && $idproducto != null) {
		        $sql .= " AND p.idproducto = '$idproducto'";
		    }
		    // Cierra la subconsulta
		    $sql .= ") AS subquery";
		    return ejecutarConsultaSimpleFila($sql);
		}
	public function TotalCompraPV2($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal,$idproducto)
		{
		    // Comienza con la parte común de la consulta
		    $sql = "SELECT ROUND(IFNULL(SUM(precioCompra),0), 2) as total_precioCompra
		            FROM (SELECT (dv.cantidad * dv.precio_venta) as precio, 
		                         ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra) as precioCompra, 
		                         (dv.cantidad * dv.precio_venta) - ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra) as utilidad,
		                         v.descuento as descuento 
		                  FROM detalle_venta dv
		                  INNER JOIN venta v ON v.idventa = dv.idventa
		                  INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
		                  INNER JOIN producto p ON p.idproducto = pg.idproducto
		                  INNER JOIN persona pe ON pe.idpersona = v.idcliente
		                  WHERE v.ventacredito = 'Si' 
		                    AND v.tipo_comprobante IN ('Nota de Venta','Boleta','Factura')
		                     AND v.estado IN ('Activado','Aceptado','Por Enviar')
		                    AND DATE(v.fecha_hora) >= '$fecha_inicio'
		                    AND DATE(v.fecha_hora) <= '$fecha_fin' ";

		    // Condiciones adicionales basadas en los parámetros proporcionados
		    if ($idvendedor != "Todos" && $idvendedor != null) {
		        $sql .= " AND v.idcliente = '$idvendedor'";
		    }
		    if ($idsucursal != "Todos" && $idsucursal != null) {
		        $sql .= " AND v.idsucursal = '$idsucursal'";
		    }
		     if ($idproducto != "Todos" && $idproducto != null) {
		        $sql .= " AND p.idproducto = '$idproducto'";
		    }
		    // Cierra la subconsulta
		    $sql .= ") AS subquery";
		    return ejecutarConsultaSimpleFila($sql);
		}

	public function TotalVentaPV2($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal,$idproducto)
		{
		    // Comienza con la parte común de la consulta
		    $sql = "SELECT ROUND(IFNULL(SUM(precio),0), 2) as total_precio
		            FROM (SELECT (dv.cantidad * dv.precio_venta) as precio, 
		                         ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra) as precioCompra, 
		                         (dv.cantidad * dv.precio_venta) - ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra) as utilidad,
		                         v.descuento as descuento 
		                  FROM detalle_venta dv
		                  INNER JOIN venta v ON v.idventa = dv.idventa
		                  INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
		                  INNER JOIN producto p ON p.idproducto = pg.idproducto
		                  INNER JOIN persona pe ON pe.idpersona = v.idcliente
		                  WHERE v.ventacredito = 'Si' 
		                    AND v.tipo_comprobante IN ('Nota de Venta','Boleta','Factura')
		                     AND v.estado IN ('Activado','Aceptado','Por Enviar')
		                    AND DATE(v.fecha_hora) >= '$fecha_inicio'
		                    AND DATE(v.fecha_hora) <= '$fecha_fin' ";

		    // Condiciones adicionales basadas en los parámetros proporcionados
		    if ($idvendedor != "Todos" && $idvendedor != null) {
		        $sql .= " AND v.idcliente = '$idvendedor'";
		    }
		    if ($idsucursal != "Todos" && $idsucursal != null) {
		        $sql .= " AND v.idsucursal = '$idsucursal'";
		    }
		     if ($idproducto != "Todos" && $idproducto != null) {
		        $sql .= " AND p.idproducto = '$idproducto'";
		    }
		    // Cierra la subconsulta
		    $sql .= ") AS subquery";
		    return ejecutarConsultaSimpleFila($sql);
		}
	public function TotalUtilidadPV2($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal,$idproducto)
		{
		    // Comienza con la parte común de la consulta
		    $sql = "SELECT ROUND(IFNULL(SUM(utilidad),0), 2) as total_utilidad
		            FROM (SELECT (dv.cantidad * dv.precio_venta) as precio, 
		                         ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra) as precioCompra, 
		                         (dv.cantidad * dv.precio_venta) - ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra) as utilidad,
		                         v.descuento as descuento 
		                  FROM detalle_venta dv
		                  INNER JOIN venta v ON v.idventa = dv.idventa
		                  INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
		                  INNER JOIN producto p ON p.idproducto = pg.idproducto
		                  INNER JOIN persona pe ON pe.idpersona = v.idcliente
		                  WHERE v.ventacredito = 'Si' 
		                    AND v.tipo_comprobante IN ('Nota de Venta','Boleta','Factura')
		                     AND v.estado IN ('Activado','Aceptado','Por Enviar')
		                    AND DATE(v.fecha_hora) >= '$fecha_inicio'
		                    AND DATE(v.fecha_hora) <= '$fecha_fin' ";

		    // Condiciones adicionales basadas en los parámetros proporcionados
		    if ($idvendedor != "Todos" && $idvendedor != null) {
		        $sql .= " AND v.idcliente = '$idvendedor'";
		    }
		    if ($idsucursal != "Todos" && $idsucursal != null) {
		        $sql .= " AND v.idsucursal = '$idsucursal'";
		    }
		     if ($idproducto != "Todos" && $idproducto != null) {
		        $sql .= " AND p.idproducto = '$idproducto'";
		    }
		    // Cierra la subconsulta
		    $sql .= ") AS subquery";
		    return ejecutarConsultaSimpleFila($sql);
		}

	public function TotalCantidadPV($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal,$idproducto)
		{
		    // Comienza con la parte común de la consulta
		    $sql = "SELECT ROUND(IFNULL(SUM(cantidad),0), 2) as total_cantidad
		            FROM (SELECT (dv.cantidad * dv.precio_venta) as precio, 
		                         (dv.cantidad * dv.cantidad_contenedor) as cantidad , 
		                         pe.nombre as nombreVendedor,
		                         v.descuento as descuento 
		                  FROM detalle_venta dv
		                  INNER JOIN venta v ON v.idventa = dv.idventa
		                  INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
		                  INNER JOIN producto p ON p.idproducto = pg.idproducto
		                  INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
		                  WHERE v.ventacredito = 'No' 
		                    AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
		                    AND v.tipo_comprobante IN ('Nota de Venta','Boleta','Factura') 
		                    AND DATE(v.fecha_hora) >= '$fecha_inicio'
		                    AND DATE(v.fecha_hora) <= '$fecha_fin' ";

		    // Condiciones adicionales basadas en los parámetros proporcionados
		    if ($idvendedor != "Todos" && $idvendedor != null) {
		        $sql .= " AND v.idPersonal = '$idvendedor'";
		    }
		    if ($idsucursal != "Todos" && $idsucursal != null) {
		        $sql .= " AND v.idsucursal = '$idsucursal'";
		    }
		     if ($idproducto != "Todos" && $idproducto != null) {
		        $sql .= " AND p.idproducto = '$idproducto'";
		    }
		    // Cierra la subconsulta
		    $sql .= ") AS subquery";
		    return ejecutarConsultaSimpleFila($sql);
		}

	

	public function TotalCompraPV($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal,$idproducto)
		{
		    // Comienza con la parte común de la consulta
		    $sql = "SELECT ROUND(IFNULL(SUM(precioCompra),0), 2) as total_precioCompra
		            FROM (SELECT (dv.cantidad * dv.precio_venta) as precio, 
		                         ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra) as precioCompra, 
		                         (dv.cantidad * dv.precio_venta) - ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra) as utilidad, 
		                         pe.nombre as nombreVendedor,
		                         v.descuento as descuento 
		                  FROM detalle_venta dv
		                  INNER JOIN venta v ON v.idventa = dv.idventa
		                  INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
		                  INNER JOIN producto p ON p.idproducto = pg.idproducto
		                  INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
		                  WHERE v.ventacredito = 'No' 
		                    AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
		                    AND v.tipo_comprobante IN ('Nota de Venta','Boleta','Factura') 
		                    AND DATE(v.fecha_hora) >= '$fecha_inicio'
		                    AND DATE(v.fecha_hora) <= '$fecha_fin' ";

		    // Condiciones adicionales basadas en los parámetros proporcionados
		    if ($idvendedor != "Todos" && $idvendedor != null) {
		        $sql .= " AND v.idPersonal = '$idvendedor'";
		    }
		    if ($idsucursal != "Todos" && $idsucursal != null) {
		        $sql .= " AND v.idsucursal = '$idsucursal'";
		    }
		     if ($idproducto != "Todos" && $idproducto != null) {
		        $sql .= " AND p.idproducto = '$idproducto'";
		    }
		    // Cierra la subconsulta
		    $sql .= ") AS subquery";
		    return ejecutarConsultaSimpleFila($sql);
		}

	public function TotalVentaPV($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal, $idproducto)
{
    // Si idsucursal es un array, lo convertimos en lista
    if (is_array($idsucursal)) {
        $idsucursal_list = implode(",", $idsucursal);
    } else {
        $idsucursal_list = $idsucursal;
    }

    $sql = "SELECT 
                ROUND(IFNULL(SUM(
                    CASE 
                        WHEN dv.check_precio = 1 THEN dv.precio_venta
                        ELSE (dv.cantidad * dv.precio_venta)
                    END
                ),0), 2) AS total_precio
            FROM detalle_venta dv
            INNER JOIN venta v ON v.idventa = dv.idventa
            INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
            INNER JOIN producto p ON p.idproducto = pg.idproducto
            INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
            WHERE v.ventacredito = 'No'
              AND v.estado IN ('Activado','Por Enviar','Aceptado')
              AND v.tipo_comprobante IN ('Nota de Venta','Boleta','Factura')
              AND DATE(v.fecha_hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'";

    // Filtros dinámicos
    if ($idvendedor != "Todos" && !empty($idvendedor)) {
        $sql .= " AND v.idPersonal = '$idvendedor'";
    }

    if ($idproducto != "Todos" && !empty($idproducto)) {
        $sql .= " AND p.idproducto = '$idproducto'";
    }

    if ($idsucursal != "Todos" && $idsucursal != "0" && !empty($idsucursal)) {
        if (strpos($idsucursal_list, ",") !== false) {
            $sql .= " AND v.idsucursal IN ($idsucursal_list)";
        } else {
            $sql .= " AND v.idsucursal = '$idsucursal_list'";
        }
    }

    return ejecutarConsultaSimpleFila($sql);
}



	public function TotalUtilidadPV($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal, $idproducto)
{
    // Si idsucursal es un array, convertir a lista
    if (is_array($idsucursal)) {
        $idsucursal_list = implode(",", $idsucursal);
    } else {
        $idsucursal_list = $idsucursal;
    }

    $sql = "SELECT 
                ROUND(IFNULL(SUM(
                    (
                        CASE 
                            WHEN dv.check_precio = 1 THEN dv.precio_venta
                            ELSE (dv.cantidad * dv.precio_venta)
                        END
                    ) - ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra)
                ),0), 2) AS total_utilidad
            FROM detalle_venta dv
            INNER JOIN venta v ON v.idventa = dv.idventa
            INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
            INNER JOIN producto p ON p.idproducto = pg.idproducto
            INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
            WHERE v.ventacredito = 'No'
              AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
              AND v.tipo_comprobante IN ('Nota de Venta','Boleta','Factura')
              AND DATE(v.fecha_hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'";

    // Filtros dinámicos
    if ($idvendedor != "Todos" && !empty($idvendedor)) {
        $sql .= " AND v.idPersonal = '$idvendedor'";
    }

    if ($idproducto != "Todos" && !empty($idproducto)) {
        $sql .= " AND p.idproducto = '$idproducto'";
    }

    if ($idsucursal != "Todos" && $idsucursal != "0" && !empty($idsucursal)) {
        if (strpos($idsucursal_list, ",") !== false) {
            $sql .= " AND v.idsucursal IN ($idsucursal_list)";
        } else {
            $sql .= " AND v.idsucursal = '$idsucursal_list'";
        }
    }

    return ejecutarConsultaSimpleFila($sql);
}




	public function TotalCompraCantidad($fecha_inicio, $fecha_fin, $idproveedor, $idsucursal)
		{
		    // Comienza con la parte común de la consulta
		    $sql = "SELECT IFNULL(SUM(dc.cantidad),0) as total_compra 
		            FROM detalle_compra dc 
		            INNER JOIN compra c ON c.idcompra = dc.idcompra
		            WHERE c.estado != 'Anulado' 
		            AND c.tipo_c = 'Compra' 
		            AND DATE(c.fecha_hora) >= '$fecha_inicio' 
		            AND DATE(c.fecha_hora) <= '$fecha_fin'";

		    // Condiciones adicionales basadas en los parámetros proporcionados
		    if ($idproveedor != "Todos" && $idproveedor != null) {
		        $sql .= " AND c.idproveedor = '$idproveedor'";
		    }

		    if ($idsucursal != "Todos" && $idsucursal != null) {
		        $sql .= " AND c.idsucursal = '$idsucursal'";
		    }

		    return ejecutarConsultaSimpleFila($sql);
		}


	public function TotalCompraProveedor($fecha_inicio, $fecha_fin, $idproveedor, $idsucursal)
	{
	    // Consulta base
	    $sql = "SELECT IFNULL(SUM(c.total_compra),0) as total_compra
	            FROM compra c
	            INNER JOIN persona pe ON pe.idpersona = c.idproveedor
	            WHERE c.estado != 'Anulado'
	              AND c.tipo_c = 'Compra'
	              AND DATE(c.fecha_hora) >= '$fecha_inicio'
	              AND DATE(c.fecha_hora) <= '$fecha_fin'";

	    // Condicionales dinámicas
	    if ($idproveedor != "Todos" && $idproveedor != null) {
	        $sql .= " AND c.idproveedor = '$idproveedor'";
	    }
	    if ($idsucursal != "Todos" && $idsucursal != null) {
	        $sql .= " AND c.idsucursal = '$idsucursal'";
	    }

	    return ejecutarConsultaSimpleFila($sql);
	}


	public function mostrarTotalSalidaTarjeta($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql1 = "SELECT IFNULL(( SELECT SUM(totaldeposito) as totalEfectivo1 FROM compra WHERE compracredito = 'No' AND formapago != 'Efectivo' AND estado != 'Anulado' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'), 0) as totalEfectivo1";
			$efectivo1 = ejecutarConsultaSimpleFila($sql1);
			$sql2 = "SELECT IFNULL(( SELECT sum(montotarjeta) as totalEfectivo2 FROM detalle_cuentas_por_pagar WHERE formapago != 'Efectivo' AND estado != 'Anulado' AND DATE(fechapago)>='$fecha_inicio' AND DATE(fechapago)<='$fecha_fin'), 0) as totalEfectivo2";
			$efectivo2 = ejecutarConsultaSimpleFila($sql2);
			$arrayName = array('total' => $efectivo1['totalEfectivo1'] + $efectivo2['totalEfectivo2']);

		} else if ($idsucursal != "Todos" && $idvendedor == "Todos") {
			$sql1 = "SELECT IFNULL(( SELECT SUM(totaldeposito) as totalEfectivo1 FROM compra WHERE compracredito = 'No' AND formapago != 'Efectivo' AND estado != 'Anulado' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND idsucursal='$idsucursal'), 0) as totalEfectivo1";
			$efectivo1 = ejecutarConsultaSimpleFila($sql1);
			$sql2 = "SELECT IFNULL(( SELECT sum(montotarjeta) as totalEfectivo2 FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra WHERE dcc.formapago != 'Efectivo' AND estado != 'Anulado' AND DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND c.idsucursal = '$idsucursal'), 0) as totalEfectivo2";
			$efectivo2 = ejecutarConsultaSimpleFila($sql2);
			$arrayName = array('total' => $efectivo1['totalEfectivo1'] + $efectivo2['totalEfectivo2']);

		} else if ($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql1 = "SELECT IFNULL(( SELECT SUM(totaldeposito) as totalEfectivo1 FROM compra WHERE compracredito = 'No' AND formapago != 'Efectivo' AND estado != 'Anulado' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND idpersonal='$idvendedor'), 0) as totalEfectivo1";
			$efectivo1 = ejecutarConsultaSimpleFila($sql1);
			$sql2 = "SELECT IFNULL(( SELECT sum(montotarjeta) as totalEfectivo2 FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra WHERE dcc.formapago != 'Efectivo' AND estado != 'Anulado' AND DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND c.idpersonal = '$idvendedor'), 0) as totalEfectivo2";
			$efectivo2 = ejecutarConsultaSimpleFila($sql2);
			$arrayName = array('total' => $efectivo1['totalEfectivo1'] + $efectivo2['totalEfectivo2']);

		} else {
			$sql1 = "SELECT IFNULL(( SELECT SUM(totaldeposito) as totalEfectivo1 FROM compra WHERE compracredito = 'No' AND formapago != 'Efectivo' AND estado != 'Anulado' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND idpersonal='$idvendedor' AND idsucursal='$idsucursal'), 0) as totalEfectivo1";
			$efectivo1 = ejecutarConsultaSimpleFila($sql1);
			$sql2 = "SELECT IFNULL(( SELECT sum(montotarjeta) as totalEfectivo2 FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra WHERE dcc.formapago != 'Efectivo' AND estado != 'Anulado' AND DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND c.idpersonal = '$idvendedor' AND c.idsucursal='$idsucursal'), 0) as totalEfectivo2";
			$efectivo2 = ejecutarConsultaSimpleFila($sql2);
			$arrayName = array('total' => $efectivo1['totalEfectivo1'] + $efectivo2['totalEfectivo2']);
		}

		return $arrayName;
	}

	// compras en efectivo//

	public function mostrarTotalSalidaEfectivo($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql1 = "SELECT IFNULL(( SELECT SUM(totalrecibido) as totalEfectivo1 FROM compra WHERE compracredito = 'No'  AND estado IN('REGISTRADO') AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'), 0) as totalEfectivo1";
			$efectivo1 = ejecutarConsultaSimpleFila($sql1);
			$sql2 = "SELECT IFNULL(( SELECT sum(montopagado) as totalEfectivo2 FROM detalle_cuentas_por_pagar WHERE  DATE(fechapago)>='$fecha_inicio' AND DATE(fechapago)<='$fecha_fin'), 0) as totalEfectivo2";
			$efectivo2 = ejecutarConsultaSimpleFila($sql2);
			$arrayName = array('total' => $efectivo1['totalEfectivo1'] + $efectivo2['totalEfectivo2']);

		} else if ($idsucursal != "Todos" && $idvendedor == "Todos"){
			$sql1 = "SELECT IFNULL(( SELECT SUM(totalrecibido) as totalEfectivo1 FROM compra WHERE compracredito = 'No'  AND estado IN('REGISTRADO')  AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND idsucursal='$idsucursal'), 0) as totalEfectivo1";
			$efectivo1 = ejecutarConsultaSimpleFila($sql1);
			$sql2 = "SELECT IFNULL(( SELECT sum(montopagado) as totalEfectivo2 FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra  WHERE   DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND c.idsucursal = '$idsucursal'), 0) as totalEfectivo2";
			$efectivo2 = ejecutarConsultaSimpleFila($sql2);
			$arrayName = array('total' => $efectivo1['totalEfectivo1'] + $efectivo2['totalEfectivo2']);

		} else if ($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql1 = "SELECT IFNULL(( SELECT SUM(totalrecibido) as totalEfectivo1 FROM compra WHERE compracredito = 'No'  AND estado IN('REGISTRADO')  AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND idpersonal='$idvendedor'), 0) as totalEfectivo1";
			$efectivo1 = ejecutarConsultaSimpleFila($sql1);
			$sql2 = "SELECT IFNULL(( SELECT sum(montopagado) as totalEfectivo2 FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra  WHERE   DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND c.idpersonal = '$idvendedor'), 0) as totalEfectivo2";
			$efectivo2 = ejecutarConsultaSimpleFila($sql2);
			$arrayName = array('total' => $efectivo1['totalEfectivo1'] + $efectivo2['totalEfectivo2']);

		} else {
			$sql1 = "SELECT IFNULL(( SELECT SUM(totalrecibido) as totalEfectivo1 FROM compra WHERE compracredito = 'No'  AND estado IN('REGISTRADO')  AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND idpersonal='$idvendedor' AND idsucursal='$idsucursal'), 0) as totalEfectivo1";
			$efectivo1 = ejecutarConsultaSimpleFila($sql1);
			$sql2 = "SELECT IFNULL(( SELECT sum(montopagado) as totalEfectivo2 FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra  WHERE   DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND c.idpersonal = '$idvendedor' AND c.idsucursal='$idsucursal'), 0) as totalEfectivo2";
			$efectivo2 = ejecutarConsultaSimpleFila($sql2);
			$arrayName = array('total' => $efectivo1['totalEfectivo1'] + $efectivo2['totalEfectivo2']);
		}

		return $arrayName;
	}

	public function mostrarTotalTarjeta($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{
	    // Construir condiciones dinámicas
	    $condSucursal = ($idsucursal != "Todos") ? " AND v.idsucursal='$idsucursal'" : "";
	    $condVendedor = ($idvendedor != "Todos") ? " AND v.idPersonal='$idvendedor'" : "";

	    // Consulta para ventas directas con tarjeta
	    $sql1 = "SELECT IFNULL((SELECT SUM(vp.monto) as totalEfectivo1
	            FROM venta v
	            INNER JOIN venta_pago vp ON v.idventa = vp.idventa
	            WHERE v.ventacredito = 'No'
	              AND vp.metodo_pago != 'Efectivo'
	              AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
	              AND DATE(v.fecha_hora) >= '$fecha_inicio'
	              AND DATE(v.fecha_hora) <= '$fecha_fin'
	              $condSucursal
	              $condVendedor), 0) as totalEfectivo1";
	    $efectivo1 = ejecutarConsultaSimpleFila($sql1);

	    // Consulta para pagos con tarjeta en cuentas por cobrar
	    $sql2 = "SELECT IFNULL((SELECT SUM(dcc.montotarjeta) as totalEfectivo2
	            FROM detalle_cuentas_por_cobrar dcc
	            INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc
	            INNER JOIN venta v ON v.idventa = cc.idventa
	            INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
	            WHERE dcc.formapago != 'Efectivo'
	              AND DATE(dcc.fechapago) >= '$fecha_inicio'
	              AND DATE(dcc.fechapago) <= '$fecha_fin'
	              $condSucursal
	              $condVendedor), 0) as totalEfectivo2";
	    $efectivo2 = ejecutarConsultaSimpleFila($sql2);

	    // Retornar total
	    $arrayName = array('total' => $efectivo1['totalEfectivo1'] + $efectivo2['totalEfectivo2']);
	    return $arrayName;
	}


	public function mostrarTotalEfectivoC($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{
	    // Construir condiciones dinámicas
	    $condSucursal = ($idsucursal != "Todos") ? " AND v.idsucursal='$idsucursal'" : "";
	    $condVendedor = ($idvendedor != "Todos") ? " AND v.idPersonal='$idvendedor'" : "";

	    // Consulta para efectivo de ventas directas + anticipos de crédito
	    $sql1 = "
	        SELECT IFNULL(SUM(
	            CASE 
	                WHEN v.ventacredito = 'No' THEN IFNULL(vp.monto,0)     -- ventas normales en efectivo
	                WHEN v.ventacredito = 'Si' THEN IFNULL(v.montoPagado,0) -- anticipos de crédito
	                ELSE 0
	            END
	        ),0) AS totalEfectivo1
	        FROM venta v
	        LEFT JOIN venta_pago vp ON v.idventa = vp.idventa AND vp.metodo_pago = 'Efectivo'
	        WHERE v.tipo_comprobante IN ('Nota de Venta','Boleta','Factura')
	          AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
	          AND DATE(v.fecha_hora) >= '$fecha_inicio'
	          AND DATE(v.fecha_hora) <= '$fecha_fin'
	          $condSucursal
	          $condVendedor
	    ";
	    $efectivo1 = ejecutarConsultaSimpleFila($sql1);

	    // Consulta para efectivo de pagos por cobrar
	    $sql2 = "
	        SELECT IFNULL(SUM(dcc.montopagado), 0) AS totalEfectivo2
	        FROM detalle_cuentas_por_cobrar dcc
	        INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc
	        INNER JOIN venta v ON v.idventa = cc.idventa
	        WHERE DATE(dcc.fechapago) >= '$fecha_inicio'
	          AND DATE(dcc.fechapago) <= '$fecha_fin'
	          $condSucursal
	          $condVendedor
	    ";
	    $efectivo2 = ejecutarConsultaSimpleFila($sql2);

	    // Sumar los dos totales
	    return ['total' => $efectivo1['totalEfectivo1'] + $efectivo2['totalEfectivo2']];
	}

	public function mostrarTotalEgresosTar($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (SELECT sum(totaldeposito) as totalIngresos FROM movimiento WHERE formapago != 'Efectivo' AND DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Egresos'), 0) as totalEgresos";
		} else if($idsucursal != "Todos" && $idvendedor == "Todos"){

			$sql = "SELECT IFNULL( (SELECT sum(totaldeposito) as totalIngresos FROM movimiento WHERE  formapago != 'Efectivo' AND DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Egresos' AND idsucursal='$idsucursal'), 0) as totalEgresos";
		} else if($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql = "SELECT IFNULL( (SELECT sum(totaldeposito) as totalIngresos FROM movimiento WHERE  formapago != 'Efectivo' AND DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Egresos' AND idpersonal='$idvendedor'), 0) as totalEgresos";
		} else {
			$sql = "SELECT IFNULL( (SELECT sum(totaldeposito) as totalIngresos FROM movimiento WHERE  formapago != 'Efectivo' AND DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Egresos' AND idpersonal='$idvendedor' AND idsucursal='$idsucursal'), 0) as totalEgresos";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalTCompras($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{
		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT ((select ifnull(sum(totalrecibido),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'  AND tipo_comprobante = 'Boleta' AND compracredito= 'no' AND estado IN ('REGISTRADO')) + 
			(select ifnull(sum(totalrecibido),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Factura' AND compracredito= 'no' AND estado IN ('REGISTRADO')) +
		   (select ifnull(sum(totalrecibido),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND estado IN ('REGISTRADO')) +
		   (select ifnull(sum(dcc.montopagado),0) from detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND cc.condicion = 1)
		   ) + ((select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'no' AND estado IN ('REGISTRADO')) + 
        (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Factura' AND compracredito= 'no' AND estado IN ('REGISTRADO')) +
       (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND estado IN ('REGISTRADO')) +
       (select ifnull(sum(montotarjeta),0) from detalle_cuentas_por_pagar WHERE DATE(fechapago)>='$fecha_inicio' AND DATE(fechapago)<='$fecha_fin' AND formapago != 'Efectivo')
       ) AS total_compra";
		} else if ($idsucursal != "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT ((select ifnull(sum(totalrecibido),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'  AND tipo_comprobante = 'Boleta' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal') + 
			(select ifnull(sum(totalrecibido),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Factura' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal') + 
			(select ifnull(sum(totalrecibido),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal') + +
		   (select ifnull(sum(dcc.montopagado),0) from detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra=c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND cc.condicion = 1 AND c.idsucursal = '$idsucursal')
		   ) + ((select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal') + 
	        (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Factura' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal') +
	       (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal') +
	       (select ifnull(sum(montotarjeta),0) from detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra=c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND c.idsucursal = '$idsucursal')
	       ) AS total_compra";
		} else if ($idsucursal == "Todos" && $idvendedor != "Todos") {
			$sql = "SELECT ((select ifnull(sum(totalrecibido),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'  AND tipo_comprobante = 'Boleta' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor') + 
			(select ifnull(sum(totalrecibido),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Factura' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor') + 
			(select ifnull(sum(totalrecibido),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor') + +
		   (select ifnull(sum(dcc.montopagado),0) from detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra=c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND cc.condicion = 1 AND c.idpersonal='$idvendedor')
		   ) + ((select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor') + 
	        (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Factura' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor') +
	       (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor') +
	       (select ifnull(sum(montotarjeta),0) from detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra=c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND c.idpersonal='$idvendedor')
	       ) AS total_compra";
		} else {
$sql = "SELECT ((select ifnull(sum(totalrecibido),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'  AND tipo_comprobante = 'Boleta' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor' AND idsucursal='$idsucursal') + 
			(select ifnull(sum(totalrecibido),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Factura' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor' AND idsucursal='$idsucursal') + 
			(select ifnull(sum(totalrecibido),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor' AND idsucursal='$idsucursal') + +
		   (select ifnull(sum(dcc.montopagado),0) from detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra=c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND cc.condicion = 1 AND c.idpersonal='$idvendedor' AND c.idsucursal='$idsucursal')
		   ) + ((select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor' AND idsucursal='$idsucursal') + 
	        (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Factura' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor' AND idsucursal='$idsucursal') +
	       (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor' AND idsucursal='$idsucursal') +
	       (select ifnull(sum(montotarjeta),0) from detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra=c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND c.idpersonal='$idvendedor' AND c.idsucursal='$idsucursal')
	       ) AS total_compra";		}
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalTickets($fecha_inicio, $fecha_fin, $idsucursal)
	{

		if ($idsucursal == "Todos" || $idsucursal == null) {

			$sql = "SELECT IFNULL(count(idcompra),0) as totalcuentacompra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Ticket' AND estado IN ('REGISTRADO')";
		} else {

			$sql = "SELECT IFNULL(count(idcompra),0) as totalcuentacompra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Ticket' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal'";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalFacturascount($fecha_inicio, $fecha_fin, $idsucursal)
	{

		if ($idsucursal == "Todos" || $idsucursal == null) {

			$sql = "SELECT IFNULL(count(idcompra),0) as totalcuentacompraf FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Factura' AND estado IN ('REGISTRADO')";
		} else {

			$sql = "SELECT IFNULL(count(idcompra),0) as totalcuentacompraf FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Factura' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal'";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalBoletascount($fecha_inicio, $fecha_fin, $idsucursal)
	{

		if ($idsucursal == "Todos" || $idsucursal == null) {

			$sql = "SELECT IFNULL(count(idcompra),0) as totalcuentacompraf FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Boleta' AND estado IN ('REGISTRADO')";
		} else {

			$sql = "SELECT IFNULL(count(idcompra),0) as totalcuentacompraf FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Boleta' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal'";
		}

		return ejecutarConsultaSimpleFila($sql);
	}


	public function mostrarTotalNotasCompraCaja($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (SELECT sum(totalrecibido) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'  AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND estado IN ('REGISTRADO')), 0) as total_compra";
		} else if($idsucursal != "Todos" && $idvendedor == "Todos"){

			$sql = "SELECT IFNULL( (SELECT sum(totalrecibido) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'  AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND idsucursal='$idsucursal' AND estado IN ('REGISTRADO')), 0) as total_compra";
		} else if($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql = "SELECT IFNULL( (SELECT sum(totalrecibido) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'  AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND idpersonal='$idvendedor' AND estado IN ('REGISTRADO')), 0) as total_compra";
		} else {
			$sql = "SELECT IFNULL( (SELECT sum(totalrecibido) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'  AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND idpersonal='$idvendedor' AND idsucursal='$idsucursal' AND estado IN ('REGISTRADO')), 0) as total_compra";
		}


		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarTotalNotasCompraTCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (select sum(totaldeposito) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND estado IN ('REGISTRADO')), 0) as total_compra";
		} else if($idsucursal != "Todos" && $idvendedor == "Todos"){

			$sql = "SELECT IFNULL( (select sum(totaldeposito) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND idsucursal='$idsucursal' AND estado IN ('REGISTRADO')), 0) as total_compra";
		} else if($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql = "SELECT IFNULL( (select sum(totaldeposito) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND idpersonal='$idvendedor' AND estado IN ('REGISTRADO')), 0) as total_compra";
		} else {
			$sql = "SELECT IFNULL( (select sum(totaldeposito) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Ticket' AND compracredito= 'no' AND idpersonal='$idvendedor' AND idsucursal='$idsucursal' AND estado IN ('REGISTRADO')), 0) as total_compra";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalEfectivoSalida($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor)
{
    if ($idsucursal == "Todos" && $idvendedor =="Todos") {
        $sql = "SELECT ((SELECT IFNULL(SUM(totalrecibido), 0) FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Boleta' AND compracredito = 'No' AND estado IN ('REGISTRADO')) + 
        (SELECT IFNULL(SUM(totalrecibido), 0) FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Factura' AND compracredito = 'No' AND estado IN ('REGISTRADO')) +
        (SELECT IFNULL(SUM(totalrecibido), 0) FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Ticket' AND compracredito = 'No' AND estado IN ('REGISTRADO')) +
        (SELECT IFNULL(SUM(dcc.montopagado), 0) FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp WHERE DATE(fechapago) >= '$fecha_inicio' AND DATE(fechapago) <= '$fecha_fin' AND cc.condicion = 1)
        ) AS total_compra";
    } else if ($idsucursal != "Todos" && $idvendedor =="Todos"){
        $sql = "SELECT ((SELECT IFNULL(SUM(totalrecibido), 0) FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin' AND tipo_comprobante = 'Boleta' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idsucursal = '$idsucursal') + 
        (SELECT IFNULL(SUM(totalrecibido), 0) FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Factura' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idsucursal = '$idsucursal') +
        (SELECT IFNULL(SUM(totalrecibido), 0) FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Ticket' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idsucursal = '$idsucursal') +
        (SELECT IFNULL(SUM(dcc.montopagado), 0) FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra=c.idcompra WHERE DATE(fechapago) >= '$fecha_inicio' AND DATE(fechapago) <= '$fecha_fin' AND cc.condicion = 1 AND c.idsucursal='$idsucursal')
        ) AS total_compra";
    } else if($idsucursal == "Todos" && $idvendedor !="Todos"){
    	$sql = "SELECT ((SELECT IFNULL(SUM(totalrecibido), 0) FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin' AND tipo_comprobante = 'Boleta' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idpersonal = '$idvendedor') + 
        (SELECT IFNULL(SUM(totalrecibido), 0) FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Factura' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idpersonal = '$idvendedor') +
        (SELECT IFNULL(SUM(totalrecibido), 0) FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Ticket' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idpersonal = '$idvendedor') +
        (SELECT IFNULL(SUM(dcc.montopagado), 0) FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra=c.idcompra WHERE DATE(fechapago) >= '$fecha_inicio' AND DATE(fechapago) <= '$fecha_fin' AND cc.condicion = 1 AND c.idpersonal='$idvendedor')
        ) AS total_compra";
    } else {
    	$sql = "SELECT ((SELECT IFNULL(SUM(totalrecibido), 0) FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin' AND tipo_comprobante = 'Boleta' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idpersonal = '$idvendedor' AND idsucursal = '$idsucursal') + 
        (SELECT IFNULL(SUM(totalrecibido), 0) FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Factura' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idpersonal = '$idvendedor' AND idsucursal = '$idsucursal') +
        (SELECT IFNULL(SUM(totalrecibido), 0) FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Ticket' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idpersonal = '$idvendedor' AND idsucursal = '$idsucursal') +
        (SELECT IFNULL(SUM(dcc.montopagado), 0) FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra=c.idcompra WHERE DATE(fechapago) >= '$fecha_inicio' AND DATE(fechapago) <= '$fecha_fin' AND cc.condicion = 1 AND c.idpersonal='$idvendedor' AND c.idsucursal = '$idsucursal')
        ) AS total_compra";
    }

    return ejecutarConsultaSimpleFila($sql);
}


	public function mostrarTotalTransferenciaSalida($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT ((select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'No' AND estado IN ('REGISTRADO')) + 
		        (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'efectivo' AND tipo_comprobante = 'Factura' AND compracredito= 'No' AND estado IN ('REGISTRADO')) +
		       (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'efectivo' AND tipo_comprobante = 'Ticket' AND compracredito= 'No' AND estado IN ('REGISTRADO')) +
		       (select ifnull(sum(dcc.montotarjeta),0) from detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND cc.condicion = 1)
		       ) AS total_compra";
		} else if ($idsucursal != "Todos" && $idvendedor == "Todos") {
			$sql = "SELECT ((select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal') + 
		        (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'efectivo' AND tipo_comprobante = 'Factura' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal') +
		       (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'efectivo' AND tipo_comprobante = 'Ticket' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal') +
		       (select ifnull(sum(dcc.montotarjeta),0) from detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND cc.condicion = 1 AND c.idsucursal='$idsucursal')
		       ) AS total_compra ";
		} else if ($idsucursal == "Todos" && $idvendedor != "Todos") {
			$sql = "SELECT ((select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor') + 
		        (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'efectivo' AND tipo_comprobante = 'Factura' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor') +
		       (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'efectivo' AND tipo_comprobante = 'Ticket' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor') +
		       (select ifnull(sum(dcc.montotarjeta),0) from detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND cc.condicion = 1 AND c.idpersonal='$idvendedor')
		       ) AS total_compra ";
		} else {
			$sql = "SELECT ((select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor' AND idsucursal='$idsucursal') + 
		        (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'efectivo' AND tipo_comprobante = 'Factura' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor' AND idsucursal='$idsucursal') +
		       (select ifnull(sum(totaldeposito),0) from compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'efectivo' AND tipo_comprobante = 'Ticket' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor' AND idsucursal='$idsucursal') +
		       (select ifnull(sum(dcc.montotarjeta),0) from detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND cc.condicion = 1 AND c.idpersonal='$idvendedor' AND c.idsucursal='$idsucursal')
		       ) AS total_compra ";
		}


		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarTotalCuentasPagarVentaCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (SELECT sum(dcc.montopagado) as total_venta FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin'  AND cc.condicion = 1), 0) as total_venta";
		} else if($idsucursal != "Todos" && $idvendedor == "Todos"){

			$sql = "SELECT IFNULL( (SELECT sum(dcc.montopagado) as total_venta FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin'  AND cc.condicion = 1  AND c.idsucursal='$idsucursal'), 0) as total_venta";
		} else if($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql = "SELECT IFNULL( (SELECT sum(dcc.montopagado) as total_venta FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin'  AND cc.condicion = 1  AND c.idPersonal='$idvendedor'), 0) as total_venta";
		} else{
			$sql = "SELECT IFNULL( (SELECT sum(dcc.montopagado) as total_venta FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin'  AND cc.condicion = 1  AND c.idPersonal='$idvendedor' AND c.idsucursal='$idsucursal'), 0) as total_venta";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarTotalCuentasPagarVentaTCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (SELECT sum(dcc.montotarjeta) as total_venta FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND cc.condicion = 1), 0) as total_venta";
		} else if($idsucursal != "Todos" && $idvendedor == "Todos"){
			$sql = "SELECT IFNULL( (SELECT sum(dcc.montotarjeta) as total_venta FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND cc.condicion = 1 AND c.idsucursal='$idsucursal'), 0) as total_venta ";
		} else if($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql = "SELECT IFNULL( (SELECT sum(dcc.montotarjeta) as total_venta FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND cc.condicion = 1 AND c.idpersonal='$idvendedor'), 0) as total_venta ";
		} else {
			$sql = "SELECT IFNULL( (SELECT sum(dcc.montotarjeta) as total_venta FROM detalle_cuentas_por_pagar dcc INNER JOIN cuentas_por_pagar cc ON cc.idcpp = dcc.idcpp INNER JOIN compra c ON cc.idcompra = c.idcompra WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND cc.condicion = 1 AND c.idsucursal='$idsucursal' AND c.idpersonal='$idvendedor'), 0) as total_venta ";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarTotalFacturasTCajaSalida($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {
			$sql = "SELECT IFNULL( (select sum(totaldeposito) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Factura' AND compracredito= 'No' AND estado IN ('REGISTRADO')), 0) as total_compra";
		} else if ($idsucursal != "Todos" && $idvendedor == "Todos"){
			$sql = "SELECT IFNULL( (select sum(totaldeposito) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Factura' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idsucursal='$idsucursal'), 0) as total_compra";
		} else if ($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql = "SELECT IFNULL( (select sum(totaldeposito) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Factura' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor'), 0) as total_compra";
		} else {
			$sql = "SELECT IFNULL( (select sum(totaldeposito) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Factura' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idpersonal='$idvendedor' AND idsucursal='$idsucursal'), 0) as total_compra";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarTotalFacturasCajaSalida($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor)
{
    if ($idsucursal == "Todos" && $idvendedor == "Todos") {
        $sql = "SELECT IFNULL( (SELECT sum(totalrecibido) as total_compra FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin' AND tipo_comprobante = 'Factura' AND compracredito = 'No' AND estado IN ('REGISTRADO')), 0) as total_compra";
    } else if ($idsucursal != "Todos" && $idvendedor == "Todos"){
        $sql = "SELECT IFNULL( (SELECT sum(totalrecibido) as total_compra FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Factura' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idsucursal = '$idsucursal'), 0) as total_compra";
    } else if($idsucursal == "Todos" && $idvendedor != "Todos"){
    	$sql = "SELECT IFNULL( (SELECT sum(totalrecibido) as total_compra FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Factura' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idpersonal = '$idvendedor'), 0) as total_compra";
    } else {
    	$sql = "SELECT IFNULL( (SELECT sum(totalrecibido) as total_compra FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Factura' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idpersonal = '$idvendedor' AND idsucursal='$idsucursal'), 0) as total_compra";
    }

    return ejecutarConsultaSimpleFila($sql);
}

	/*public function mostrarTotalFacturasCajaSalida($fecha_inicio, $fecha_fin, $idsucursal)
{
    $sql = "SELECT IFNULL((SELECT sum(total_compra) as total_compra 
                          FROM compra 
                          WHERE DATE(fecha_hora) >= '$fecha_inicio' 
                          AND DATE(fecha_hora) <= '$fecha_fin' 
                          AND formapago = 'Efectivo' 
                          AND tipo_comprobante = 'Factura' 
                          AND compracredito = 'No' 
                          AND estado IN ('REGISTRADO')";
    
    if ($idsucursal != "Todos" && $idsucursal != null) {
        $sql .= " AND idsucursal = '$idsucursal'";
    }

    $sql .= "), 0) as total_compra";

    return ejecutarConsultaSimpleFila($sql);
}*/


	public function mostrarTotalBoletasTCajaSalida($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (select sum(totaldeposito) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'No' AND estado IN ('REGISTRADO')), 0) as total_compra";
		} else if($idsucursal != "Todos" && $idvendedor == "Todos"){

			$sql = "SELECT IFNULL( (select sum(totaldeposito) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idsucursal = '$idsucursal'), 0) as total_compra";
		} else if($idsucursal == "Todos" && $idvendedor !="Todos"){
			$sql = "SELECT IFNULL( (select sum(totaldeposito) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idpersonal = '$idvendedor'), 0) as total_compra";
		} else {
			$sql = "SELECT IFNULL( (select sum(totaldeposito) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'No' AND estado IN ('REGISTRADO') AND idpersonal = '$idvendedor' AND idsucursal = '$idsucursal'), 0) as total_compra";
		}

		return ejecutarConsultaSimpleFila($sql);
	}


	/*public function mostrarTotalBoletasCajaSalida($fecha_inicio, $fecha_fin, $idsucursal)
	{

		if ($idsucursal == "Todos" || $idsucursal == null) {

			$sql = "SELECT IFNULL( (SELECT sum(total_compra) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago = 'Efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'No' AND estado IN ('REGISTRADO')), 0) as total_compra";
		} else {

			$sql = "SELECT IFNULL( (SELECT sum(total_compra) as total_compra FROM compra WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago = 'Efectivo' AND tipo_comprobante = 'Boleta' AND compracredito= 'No' AND estado IN ('REGISTRADO')), 0) as total_compra AND idsucursal='$idsucursal'";
		}


		return ejecutarConsultaSimpleFila($sql);
	}*/

	public function mostrarTotalBoletasCajaSalida($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor)
	{
	    if ($idsucursal == "Todos" && $idvendedor=="Todos") {
	        $sql = "SELECT IFNULL( (SELECT sum(totalrecibido) as total_compra FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Boleta' AND compracredito = 'No' AND estado IN ('REGISTRADO')), 0) as total_compra";
	    } else if($idsucursal != "Todos" && $idvendedor=="Todos"){
	        $sql = "SELECT IFNULL( (SELECT sum(totalrecibido) as total_compra FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Boleta' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idsucursal = '$idsucursal'), 0) as total_compra";
	    } else if($idsucursal == "Todos" && $idvendedor !="Todos"){
	    	$sql = "SELECT IFNULL( (SELECT sum(totalrecibido) as total_compra FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Boleta' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idpersonal = '$idvendedor'), 0) as total_compra";
	    } else {
	        $sql = "SELECT IFNULL( (SELECT sum(totalrecibido) as total_compra FROM compra WHERE DATE(fecha_hora) >= '$fecha_inicio' AND DATE(fecha_hora) <= '$fecha_fin'  AND tipo_comprobante = 'Boleta' AND compracredito = 'No' AND estado IN ('REGISTRADO') AND idsucursal = '$idsucursal' AND idpersonal = '$idvendedor'), 0) as total_compra";

	    }

	    return ejecutarConsultaSimpleFila($sql);
	}





	public function reportesdigemid($fecha_inicio, $fecha_fin, $idsucursal)
	{
		if ($idsucursal == "Todos") {

			$sql = "SELECT p.nombre, p.registrosan, i.cantidad, i.precio_venta, i.nlote, i.fvencimiento FROM detalle_compra i 
			INNER JOIN compra c ON c.idcompra = i.idcompra
			INNER JOIN producto p ON i.idproducto=p.idproducto 
			WHERE DATE(c.fecha_hora) >= '$fecha_inicio' AND DATE(c.fecha_hora) <= '$fecha_fin'";
		} else {

			$sql = "SELECT p.nombre, p.registrosan, i.cantidad, i.precio_venta, i.nlote, i.fvencimiento FROM detalle_compra i 
			INNER JOIN compra c ON c.idcompra = i.idcompra
			INNER JOIN producto p ON i.idproducto=p.idproducto 
			WHERE DATE(c.fecha_hora) >= '$fecha_inicio' AND DATE(c.fecha_hora) <= '$fecha_fin' AND i.idsucursal = '$idsucursal'";
		}

		return ejecutarConsulta($sql);
	}

	public function reportesvencimiento($idsucursal)
	{
		if ($idsucursal == "Todos") {

			$sql = "SELECT p.nombre, p.registrosan, i.cantidad, i.nlote, i.fvencimiento, DATEDIFF(i.fvencimiento, NOW()) AS dias_transcurridos2
				FROM detalle_compra i
				INNER JOIN compra c ON c.idcompra = i.idcompra
				INNER JOIN producto p ON i.idproducto = p.idproducto
				WHERE DATEDIFF(i.fvencimiento, NOW()) <= 180";
		} else {

			$sql = "SELECT p.nombre, p.registrosan, i.cantidad, i.nlote, i.fvencimiento, DATEDIFF(i.fvencimiento, NOW()) AS dias_transcurridos2
				FROM detalle_compra i
				INNER JOIN compra c ON c.idcompra = i.idcompra
				INNER JOIN producto p ON i.idproducto = p.idproducto
				WHERE DATEDIFF(i.fvencimiento, NOW()) <= 180 AND i.idsucursal = '$idsucursal'";
		}

		return ejecutarConsulta($sql);
	}

	public function calcularDiasVencimiento2($dias_transcurridos2)
	{
		// code...
		$data = 'Sin rango';
		$dias_vencidos = $dias_transcurridos2 * -1;
		if ($dias_transcurridos2 == 0) {
			$data = '<span class="badge bg-red">S/V</span>';
		} else if ($dias_transcurridos2 == 1) {
			$data = '<span style="font-size:15px" class="badge bg-red">Vence mañana</span>';
		} else if ($dias_transcurridos2 > 1 && $dias_transcurridos2 <= 365) {
			$data = '<span style="font-size:15px" class="badge bg-red">Vence en ' . $dias_transcurridos2 . ' dias</span>';
		} else if ($dias_transcurridos2 > 365 && $dias_transcurridos2 <= 730) {
			$data = '<span style="font-size:15px"  class="badge bg-orange">Vence en un año</span>';
		} else if ($dias_transcurridos2 > 730 && $dias_transcurridos2 <= 1095) {
			$data = '<span style="font-size:15px"  class="badge bg-green">Vence en dos años</span>';
		} else if ($dias_transcurridos2 > 1095 && $dias_transcurridos2 <= 1461) {
			$data = '<span style="color: green;font-size:15px">Vence en tres años</span>';
		} else if ($dias_transcurridos2 == -1) {
			$data = '<span style="font-size:15px" class="badge bg-red">Venció Ayer</span>';
		} else if ($dias_transcurridos2 < -1 && $dias_transcurridos2 >= -365) {
			$data = '<span style="font-size:15px" class="badge bg-red">Venció hace ' . $dias_vencidos . ' dias</span>';
		} else if ($dias_transcurridos2 < -365 && $dias_transcurridos2 >= -720) {
			$data = '<span style="font-size:15px" class="badge bg-red">Venció hace un año</span>';
		} else if ($dias_transcurridos2 < -720 && $dias_transcurridos2 >= -1085) {
			$data = '<span style="font-size:15px" class="badge bg-red">Venció hace dos años</span>';
		}
		return $data;
	}

	public function comprasfecha($fecha_inicio, $fecha_fin, $idsucursal)
	{

		if ($idsucursal == "Todos") {

			$sql = "SELECT DATE(i.fecha_hora) as fecha,u.nombre as personal, p.nombre as proveedor,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado FROM compra i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN personal u ON i.idpersonal=u.idpersonal WHERE DATE(i.fecha_hora)>='$fecha_inicio' AND DATE(i.fecha_hora)<='$fecha_fin'";
		} else {

			$sql = "SELECT DATE(i.fecha_hora) as fecha,u.nombre as personal, p.nombre as proveedor,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado FROM compra i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN personal u ON i.idpersonal=u.idpersonal WHERE DATE(i.fecha_hora)>='$fecha_inicio' AND DATE(i.fecha_hora)<='$fecha_fin' AND i.idsucursal = '$idsucursal'";
		}

		return ejecutarConsulta($sql);
	}


	public function mostrarTotalPedidos($fecha_inicio, $fecha_fin, $idsucursal)
{

    if ($idsucursal == "Todos" || $idsucursal == null) {
        $sql = "SELECT v.idsucursal, dv.nombre_producto as nombre, p.stock, 
                       SUM(dv.cantidad) AS cantidad_vendida,
                       COALESCE(cantidad_comprada.cantidad, 0) AS cantidad_comprada
                FROM venta v 
                INNER JOIN detalle_venta dv ON dv.idventa = v.idventa
                INNER JOIN producto_configuracion pc ON pc.id = dv.idproducto
                INNER JOIN producto p ON p.idproducto = pc.idproducto
                LEFT JOIN (SELECT dc.idproducto, SUM(dc.cantidad) AS cantidad
                           FROM detalle_compra dc
                           INNER JOIN compra c ON c.idcompra = dc.idcompra
                           GROUP BY dc.idproducto) AS cantidad_comprada ON cantidad_comprada.idproducto = p.idproducto
                WHERE DATE(v.fecha_hora) >= '$fecha_inicio' 
                AND DATE(v.fecha_hora) <= '$fecha_fin' 
                AND p.stock <= '5'
                GROUP BY v.idsucursal, dv.nombre_producto, p.stock";
    } else {
        $sql = "SELECT v.idsucursal, dv.nombre_producto as nombre, p.stock, 
                       SUM(dv.cantidad) AS cantidad_vendida,
                       COALESCE(cantidad_comprada.cantidad, 0) AS cantidad_comprada
                FROM venta v 
                INNER JOIN detalle_venta dv ON dv.idventa = v.idventa
                INNER JOIN producto_configuracion pc ON pc.id = dv.idproducto
                INNER JOIN producto p ON p.idproducto = pc.idproducto
                LEFT JOIN (SELECT dc.idproducto, SUM(dc.cantidad) AS cantidad
                           FROM detalle_compra dc
                           INNER JOIN compra c ON c.idcompra = dc.idcompra
                           GROUP BY dc.idproducto) AS cantidad_comprada ON cantidad_comprada.idproducto = p.idproducto
                WHERE DATE(v.fecha_hora) >= '$fecha_inicio' 
                AND DATE(v.fecha_hora) <= '$fecha_fin' 
                AND p.stock <= '5' 
                AND v.idsucursal = '$idsucursal'
                GROUP BY v.idsucursal, dv.nombre_producto, p.stock";
    }

    return ejecutarConsulta($sql);
}



	public function deudasfechacliente($fecha_inicio, $fecha_fin, $idcliente, $idsucursal)
	{

		if ($idsucursal == "Todos" and $idcliente == "Todos") {

			$sql = "SELECT v.idcliente,p.nombre as cliente, SUM(cc.deudatotal) as deudatotal
			FROM venta v
			INNER JOIN cuentas_por_cobrar cc
			ON v.idventa = cc.idventa
			INNER JOIN persona p
			ON v.idcliente = p.idpersona
			WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin'
			GROUP BY v.idcliente";
		} else if ($idsucursal == "Todos" and $idcliente != "Todos") {

			$sql = "SELECT v.idcliente,p.nombre as cliente, SUM(cc.deudatotal) as deudatotal
			FROM venta v
			INNER JOIN cuentas_por_cobrar cc
			ON v.idventa = cc.idventa
			INNER JOIN persona p
			ON v.idcliente = p.idpersona
			WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.idcliente = '$idcliente'
			GROUP BY v.idcliente";
		} else if ($idsucursal != "Todos" and $idcliente == "Todos") {

			$sql = "SELECT v.idcliente,p.nombre as cliente, SUM(cc.deudatotal) as deudatotal
			FROM venta v
			INNER JOIN cuentas_por_cobrar cc
			ON v.idventa = cc.idventa
			INNER JOIN persona p
			ON v.idcliente = p.idpersona
			WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.idsucursal = '$idsucursal'
			GROUP BY v.idcliente";
		} else {

			$sql = "SELECT v.idcliente,p.nombre as cliente, SUM(cc.deudatotal) as deudatotal
			FROM venta v
			INNER JOIN cuentas_por_cobrar cc
			ON v.idventa = cc.idventa
			INNER JOIN persona p
			ON v.idcliente = p.idpersona
			WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.idsucursal = '$idsucursal' AND v.idcliente = '$idcliente'
			GROUP BY v.idcliente";
		}

		return ejecutarConsulta($sql);
	}

	public function ventasfechacliente($fecha_inicio, $fecha_fin, $idcliente, $idsucursal)
	{

		if ($idsucursal == "Todos" and $idcliente == "Todos") {

			$sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,u.nombre as personal, p.nombre as cliente,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.ventacredito,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";
		} else if ($idsucursal == "Todos" and $idcliente != "Todos") {

			$sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,u.nombre as personal, p.nombre as cliente,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.ventacredito,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.idcliente='$idcliente' AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";
		} else if ($idsucursal != "Todos" and $idcliente == "Todos") {

			$sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,u.nombre as personal, p.nombre as cliente,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.ventacredito,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta') AND v.idsucursal = '$idsucursal'";
		} else {

			$sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,u.nombre as personal, p.nombre as cliente,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.ventacredito,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.idcliente='$idcliente' AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta') AND v.idsucursal = '$idsucursal'";
		}

		return ejecutarConsulta($sql);
	}

	public function ventasfechavendedor($fecha_inicio, $fecha_fin, $idcliente, $idsucursal)
	{
		if ($idcliente == "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT DATE(v.fecha_hora) as fecha,u.nombre as personal, p.nombre as cliente,v.comisionV,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.descuento,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta') AND v.estado != 'Nota Credito' AND v.estado != 'Anulado'";
		} else if ($idcliente != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT DATE(v.fecha_hora) as fecha,u.nombre as personal, p.nombre as cliente,v.comisionV,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.descuento,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND u.idpersonal='$idcliente' AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta') AND v.estado != 'Nota Credito' AND v.estado != 'Anulado'";
		} else if ($idcliente == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT DATE(v.fecha_hora) as fecha,u.nombre as personal, p.nombre as cliente,v.comisionV,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.descuento,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta') AND v.idsucursal = '$idsucursal' AND v.estado != 'Nota Credito' AND v.estado != 'Anulado'";
		} else {
			$sql = "SELECT DATE(v.fecha_hora) as fecha,u.nombre as personal, p.nombre as cliente,v.comisionV,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.descuento,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta') AND v.idsucursal = '$idsucursal' AND u.idpersonal='$idcliente' AND v.estado != 'Nota Credito' AND v.estado != 'Anulado'";
		}

		return ejecutarConsulta($sql);
	}

	public function ventasfechaservicio($fecha_inicio, $fecha_fin, $idproducto, $idcliente, $idsucursal)
	{

		if ($idproducto == "Todos" and $idcliente == "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT v.fecha_hora, p.nombre, um.nombre as unidadmedida,dv.cantidad, (dv.cantidad * dv.precio_venta) as precio, (dv.cantidad * p.precio_compra) as precioCompra, (dv.cantidad * dv.precio_venta) - (dv.cantidad * p.precio_compra) as utilidad, pe.nombre as nombreVendedor FROM detalle_venta dv
			INNER JOIN venta v
			ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg
			ON pg.id = dv.idproducto
			INNER JOIN producto p
			ON p.idproducto = pg.idproducto
			INNER JOIN personal pe
			ON pe.idpersonal = v.idPersonal
			INNER JOIN unidad_medida um
			ON p.idunidad_medida = um.idunidad_medida
			INNER JOIN categoria c
			ON c.idcategoria = p.idcategoria
				WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND c.nombre = 'SERVICIO' AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado') AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta') AND v.documento_rel = ''";
		} else if ($idproducto == "Todos" and $idcliente != "Todos" and $idsucursal != "Todos") {

			$sql = "SELECT v.fecha_hora, p.nombre, um.nombre as unidadmedida,dv.cantidad, (dv.cantidad * dv.precio_venta) as precio, (dv.cantidad * p.precio_compra) as precioCompra, (dv.cantidad * dv.precio_venta) - (dv.cantidad * p.precio_compra) as utilidad, pe.nombre as nombreVendedor FROM detalle_venta dv
			INNER JOIN venta v
			ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg
			ON pg.id = dv.idproducto
			INNER JOIN producto p
			ON p.idproducto = pg.idproducto
			INNER JOIN personal pe
			ON pe.idpersonal = v.idPersonal
			INNER JOIN unidad_medida um
			ON p.idunidad_medida = um.idunidad_medida
			INNER JOIN categoria c
			ON c.idcategoria = p.idcategoria
	        WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND pe.idpersonal = '$idcliente' AND v.idsucursal = '$idsucursal' AND c.nombre = 'SERVICIO' AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado') AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";
		} else if ($idcliente == "Todos" and $idproducto == "Todos" and $idsucursal != "Todos") {

			$sql = "SELECT v.fecha_hora, p.nombre, um.nombre as unidadmedida,dv.cantidad, (dv.cantidad * dv.precio_venta) as precio, (dv.cantidad * p.precio_compra) as precioCompra, (dv.cantidad * dv.precio_venta) - (dv.cantidad * p.precio_compra) as utilidad, pe.nombre as nombreVendedor FROM detalle_venta dv
			INNER JOIN venta v
			ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg
			ON pg.id = dv.idproducto
			INNER JOIN producto p
			ON p.idproducto = pg.idproducto
			INNER JOIN personal pe
			ON pe.idpersonal = v.idPersonal
			INNER JOIN unidad_medida um
			ON p.idunidad_medida = um.idunidad_medida
			INNER JOIN categoria c
			ON c.idcategoria = p.idcategoria
	        WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.idsucursal = '$idsucursal' AND c.nombre != 'SERVICIO' AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado') AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";
		} else if ($idcliente == "Todos" and $idproducto != "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT v.fecha_hora, p.nombre, um.nombre as unidadmedida,dv.cantidad, (dv.cantidad * dv.precio_venta) as precio, (dv.cantidad * p.precio_compra) as precioCompra, (dv.cantidad * dv.precio_venta) - (dv.cantidad * p.precio_compra) as utilidad, pe.nombre as nombreVendedor FROM detalle_venta dv
			INNER JOIN venta v
			ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg
			ON pg.id = dv.idproducto
			INNER JOIN producto p
			ON p.idproducto = pg.idproducto
			INNER JOIN personal pe
			ON pe.idpersonal = v.idPersonal
			INNER JOIN unidad_medida um
			ON p.idunidad_medida = um.idunidad_medida
			INNER JOIN categoria c
			ON c.idcategoria = p.idcategoria
	        WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND p.idproducto = '$idproducto' AND c.nombre = 'SERVICIO' AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado') AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";
		} else {

			$sql = "SELECT v.fecha_hora, p.nombre, um.nombre as unidadmedida,dv.cantidad, (dv.cantidad * dv.precio_venta) as precio, (dv.cantidad * p.precio_compra) as precioCompra, (dv.cantidad * dv.precio_venta) - (dv.cantidad * p.precio_compra) as utilidad, pe.nombre as nombreVendedor FROM detalle_venta dv
			INNER JOIN venta v
			ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg
			ON pg.id = dv.idproducto
			INNER JOIN producto p
			ON p.idproducto = pg.idproducto
			INNER JOIN personal pe
			ON pe.idpersonal = v.idPersonal
			INNER JOIN unidad_medida um
			ON p.idunidad_medida = um.idunidad_medida
			INNER JOIN categoria c
			ON c.idcategoria = p.idcategoria
	        WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND p.idproducto = '$idproducto' AND pe.idpersonal = '$idcliente' AND v.idsucursal = '$idsucursal' AND c.nombre = 'SERVICIO' AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado') AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";
		}

		return ejecutarConsulta($sql);
	}

public function ventasfechaproducto($fecha_inicio, $fecha_fin, $idproducto, $idcliente, $idsucursal)
{
    if (is_array($idsucursal)) {
        $idsucursal_list = implode(",", $idsucursal);
    } else {
        $idsucursal_list = $idsucursal;
    }

    $sql = "SELECT 
                v.fecha_kardex, 
                p.nombre, 
                pg.contenedor,
                -- cantidad real (unidades considerando contenedor)
                (dv.cantidad * dv.cantidad_contenedor) AS cantidad_total,
                -- precio unitario tal como se guardó (útil para inspección)
                dv.precio_venta AS precio_unitario_guardado,
                -- flag que indica si el precio almacenado ya es total (1) o unitario (0)
                dv.check_precio,
                -- precio final para el ítem (subtotal): respeta check_precio
                CASE 
                    WHEN dv.check_precio = 1 THEN dv.precio_venta
                    ELSE (dv.cantidad * dv.precio_venta)
                END AS precio_total,
                -- precio de compra total basado en cantidad_total
                ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra) AS precioCompra,
                -- utilidad sin considerar comisión (precio_total - costo_total)
                (CASE 
                    WHEN dv.check_precio = 1 THEN dv.precio_venta
                    ELSE (dv.cantidad * dv.precio_venta)
                END - ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra)) AS utilidadSC,
                -- utilidad final considerando comisión por venta (resta v.comisionV)
                ((CASE 
                    WHEN dv.check_precio = 1 THEN dv.precio_venta
                    ELSE (dv.cantidad * dv.precio_venta)
                END - ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra)) - v.comisionV) AS utilidad,
                v.comisionV, 
                pe.nombre AS nombreVendedor,
                -- margen utilidad en porcentaje (protegiendo división por cero)
                (CASE 
                    WHEN (CASE WHEN dv.check_precio = 1 THEN dv.precio_venta ELSE (dv.cantidad * dv.precio_venta) END) = 0 
                        THEN 0 
                    ELSE (((CASE WHEN dv.check_precio = 1 THEN dv.precio_venta ELSE (dv.cantidad * dv.precio_venta) END) - ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra)) / (CASE WHEN dv.check_precio = 1 THEN dv.precio_venta ELSE (dv.cantidad * dv.precio_venta) END)) * 100
                END) AS margen_utilidad,
                um.nombre AS unidadmedida
            FROM detalle_venta dv
            INNER JOIN venta v ON v.idventa = dv.idventa
            INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
            INNER JOIN producto p ON p.idproducto = pg.idproducto
            INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
            INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
            INNER JOIN categoria c ON c.idcategoria = p.idcategoria
            INNER JOIN sucursal s ON s.idsucursal = v.idsucursal
            INNER JOIN usuario_sucursal us ON us.idsucursal = s.idsucursal
            INNER JOIN usuario u ON u.idusuario = us.idusuario
            WHERE DATE(v.fecha_hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'
            AND v.ventacredito = 'No'
            AND c.nombre != 'SERVICIO'
            AND v.estado NOT IN ('Anulado', 'Nota Credito')
            AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')
            AND u.idpersonal = '" . $_SESSION['idpersonal'] . "'";

    if ($idproducto != "Todos") {
        $sql .= " AND p.idproducto = '$idproducto'";
    }

    if ($idcliente != "Todos") {
        $sql .= " AND pe.idpersonal = '$idcliente'";
    }
    if ($idsucursal != "Todos" && $idsucursal != "0" && !empty($idsucursal)) {
        if (strpos($idsucursal_list, ",") !== false) {
            $sql .= " AND v.idsucursal IN ($idsucursal_list)";
        } else {
            $sql .= " AND v.idsucursal = '$idsucursal_list'";
        }
    }
    $sql .= " ORDER BY v.fecha_kardex DESC";

    return ejecutarConsulta($sql);
}

	public function ventasfechaproducto2($fecha_inicio, $fecha_fin, $idproducto, $idcliente, $idsucursal)
	{

		if ($idproducto == "Todos" and $idcliente == "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT v.fecha_hora, p.nombre, pg.contenedor,(dv.cantidad*dv.cantidad_contenedor) as cantidad, (dv.cantidad * dv.precio_venta) as precio, ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as precioCompra,(dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as utilidadSC ,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))-v.comisionV) as utilidad,v.comisionV, pe.nombre as nombreVendedor,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))/(dv.cantidad * dv.precio_venta))*100 as margen_utilidad
			FROM detalle_venta dv
			INNER JOIN venta v
			ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg
			ON pg.id = dv.idproducto
			INNER JOIN producto p
			ON p.idproducto = pg.idproducto
			INNER JOIN persona pe
			ON pe.idpersona = v.idcliente
			INNER JOIN unidad_medida um
			ON p.idunidad_medida = um.idunidad_medida
			INNER JOIN categoria c
			ON c.idcategoria = p.idcategoria
				WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.ventacredito='Si' AND c.nombre != 'SERVICIO' AND v.estado!='Anulado' AND v.estado != 'Nota Credito'  AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";
		} else if ($idproducto == "Todos" and $idcliente != "Todos" and $idsucursal != "Todos") {

			$sql = "SELECT v.fecha_hora, p.nombre, pg.contenedor,(dv.cantidad*dv.cantidad_contenedor) as cantidad, (dv.cantidad * dv.precio_venta) as precio, ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as precioCompra,(dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as utilidadSC ,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))-v.comisionV) as utilidad,v.comisionV, pe.nombre as nombreVendedor,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))/(dv.cantidad * dv.precio_venta))*100 as margen_utilidad  FROM detalle_venta dv
			INNER JOIN venta v
			ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg
			ON pg.id = dv.idproducto
			INNER JOIN producto p
			ON p.idproducto = pg.idproducto
			INNER JOIN persona pe
			ON pe.idpersona = v.idcliente
			INNER JOIN unidad_medida um
			ON p.idunidad_medida = um.idunidad_medida
			INNER JOIN categoria c
			ON c.idcategoria = p.idcategoria
	        WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.ventacredito='Si' AND pe.idpersona = '$idcliente' AND v.idsucursal = '$idsucursal' AND c.nombre != 'SERVICIO' AND v.estado!='Anulado' AND v.estado != 'Nota Credito'  AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";
		} else if ($idcliente == "Todos" and $idproducto == "Todos" and $idsucursal != "Todos") {

			$sql = "SELECT v.fecha_hora, p.nombre, pg.contenedor,(dv.cantidad*dv.cantidad_contenedor) as cantidad, (dv.cantidad * dv.precio_venta) as precio, ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as precioCompra,(dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as utilidadSC ,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))-v.comisionV) as utilidad,v.comisionV, pe.nombre as nombreVendedor,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))/(dv.cantidad * dv.precio_venta))*100 as margen_utilidad  FROM detalle_venta dv
			INNER JOIN venta v
			ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg
			ON pg.id = dv.idproducto
			INNER JOIN producto p
			ON p.idproducto = pg.idproducto
			INNER JOIN persona pe
			ON pe.idpersona = v.idcliente
			INNER JOIN unidad_medida um
			ON p.idunidad_medida = um.idunidad_medida
			INNER JOIN categoria c
			ON c.idcategoria = p.idcategoria
	        WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.ventacredito='Si' AND v.idsucursal = '$idsucursal' AND c.nombre != 'SERVICIO' AND v.estado!='Anulado' AND v.estado != 'Nota Credito'  AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";
		} else if ($idcliente != "Todos" and $idproducto == "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT v.fecha_hora, p.nombre, pg.contenedor,(dv.cantidad*dv.cantidad_contenedor) as cantidad, (dv.cantidad * dv.precio_venta) as precio, ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as precioCompra,(dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as utilidadSC ,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))-v.comisionV) as utilidad,v.comisionV, pe.nombre as nombreVendedor,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))/(dv.cantidad * dv.precio_venta))*100 as margen_utilidad  FROM detalle_venta dv
			INNER JOIN venta v
			ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg
			ON pg.id = dv.idproducto
			INNER JOIN producto p
			ON p.idproducto = pg.idproducto
			INNER JOIN persona pe
			ON pe.idpersona = v.idcliente
			INNER JOIN unidad_medida um
			ON p.idunidad_medida = um.idunidad_medida
			INNER JOIN categoria c
			ON c.idcategoria = p.idcategoria
	        WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.ventacredito='Si' AND pe.idpersona = '$idcliente' AND c.nombre != 'SERVICIO' AND v.estado!='Anulado' AND v.estado != 'Nota Credito'  AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";

			
		} else if ($idcliente != "Todos" and $idproducto != "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT v.fecha_hora, p.nombre, pg.contenedor,(dv.cantidad*dv.cantidad_contenedor) as cantidad, (dv.cantidad * dv.precio_venta) as precio, ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as precioCompra,(dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as utilidadSC ,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))-v.comisionV) as utilidad,v.comisionV, pe.nombre as nombreVendedor,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))/(dv.cantidad * dv.precio_venta))*100 as margen_utilidad  FROM detalle_venta dv
			INNER JOIN venta v
			ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg
			ON pg.id = dv.idproducto
			INNER JOIN producto p
			ON p.idproducto = pg.idproducto
			INNER JOIN persona pe
			ON pe.idpersona = v.idcliente
			INNER JOIN unidad_medida um
			ON p.idunidad_medida = um.idunidad_medida
			INNER JOIN categoria c
			ON c.idcategoria = p.idcategoria
	        WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.ventacredito='Si' AND pe.idpersona = '$idcliente' AND p.idproducto = '$idproducto' AND c.nombre != 'SERVICIO' AND v.estado!='Anulado' AND v.estado != 'Nota Credito'  AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";
	    } else if ($idcliente == "Todos" and $idproducto != "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT v.fecha_hora, p.nombre, pg.contenedor,(dv.cantidad*dv.cantidad_contenedor) as cantidad, (dv.cantidad * dv.precio_venta) as precio, ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as precioCompra,(dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as utilidadSC ,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))-v.comisionV) as utilidad,v.comisionV, pe.nombre as nombreVendedor,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))/(dv.cantidad * dv.precio_venta))*100 as margen_utilidad  FROM detalle_venta dv
			INNER JOIN venta v
			ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg
			ON pg.id = dv.idproducto
			INNER JOIN producto p
			ON p.idproducto = pg.idproducto
			INNER JOIN persona pe
			ON pe.idpersona = v.idcliente
			INNER JOIN unidad_medida um
			ON p.idunidad_medida = um.idunidad_medida
			INNER JOIN categoria c
			ON c.idcategoria = p.idcategoria
	        WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.ventacredito='Si'  AND p.idproducto = '$idproducto' AND c.nombre != 'SERVICIO' AND v.estado!='Anulado' AND v.estado != 'Nota Credito'  AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";

	    } else {
			$sql = "SELECT v.fecha_hora, p.nombre, pg.contenedor,(dv.cantidad*dv.cantidad_contenedor) as cantidad, (dv.cantidad * dv.precio_venta) as precio, ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as precioCompra,(dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as utilidadSC ,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))-v.comisionV) as utilidad,v.comisionV, pe.nombre as nombreVendedor ,(((dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra))/(dv.cantidad * dv.precio_venta))*100 as margen_utilidad  FROM detalle_venta dv
			INNER JOIN venta v
			ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg
			ON pg.id = dv.idproducto
			INNER JOIN producto p
			ON p.idproducto = pg.idproducto
			INNER JOIN persona pe
			ON pe.idpersona = v.idcliente
			INNER JOIN unidad_medida um
			ON p.idunidad_medida = um.idunidad_medida
			INNER JOIN categoria c
			ON c.idcategoria = p.idcategoria
	        WHERE DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.ventacredito='Si' AND p.idproducto = '$idproducto' AND pe.idpersona = '$idcliente' AND v.idsucursal = '$idsucursal' AND c.nombre != 'SERVICIO' AND v.estado!='Anulado' AND v.estado != 'Nota Credito' AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')";
		}

		return ejecutarConsulta($sql);
	}

public function ventasfechaproductoproveedor($fecha_inicio, $fecha_fin, $idproducto, $idproveedor, $idsucursal)
	{

		if ($idproducto == "Todos" and $idproveedor == "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT c.fecha_kardex,pe.nombre as proveedor,c.tipo_comprobante,c.serie_comprobante,c.num_comprobante,c.idproveedor,p.nombre,dc.cantidad,(dc.cantidad * dc.precio_compra) as precio
					FROM detalle_compra dc
					INNER JOIN producto p ON dc.idproducto = p.idproducto
					INNER JOIN compra c ON c.idcompra= dc.idcompra
					INNER JOIN persona pe ON pe.idpersona = c.idproveedor
					WHERE DATE(c.fecha_hora)>='$fecha_inicio' AND DATE(c.fecha_hora)<='$fecha_fin' AND c.estado IN('REGISTRADO')";
		} else if ($idproducto == "Todos" and $idproveedor != "Todos" and $idsucursal != "Todos") {

			$sql = "SELECT c.fecha_kardex,pe.nombre as proveedor,c.tipo_comprobante,c.serie_comprobante,c.num_comprobante,p.nombre,dc.cantidad,(dc.cantidad * dc.precio_compra) as precio
					FROM detalle_compra dc
					INNER JOIN producto p ON dc.idproducto = p.idproducto
					INNER JOIN compra c ON c.idcompra= dc.idcompra
					INNER JOIN persona pe ON pe.idpersona = c.idproveedor
					WHERE DATE(c.fecha_hora)>='$fecha_inicio' AND DATE(c.fecha_hora)<='$fecha_fin' AND c.estado IN('REGISTRADO') AND c.idproveedor = '$idproveedor' AND c.idsucursal='$idsucursal'";
		} else if ($idproveedor == "Todos" and $idproducto == "Todos" and $idsucursal != "Todos") {

			$sql = "SELECT c.fecha_kardex,pe.nombre as proveedor,c.tipo_comprobante,c.serie_comprobante,c.num_comprobante,c.idproveedor,p.nombre,dc.cantidad,(dc.cantidad * dc.precio_compra) as precio
					FROM detalle_compra dc
					INNER JOIN producto p ON dc.idproducto = p.idproducto
					INNER JOIN compra c ON c.idcompra= dc.idcompra
					INNER JOIN persona pe ON pe.idpersona = c.idproveedor
					WHERE DATE(c.fecha_hora)>='$fecha_inicio' AND DATE(c.fecha_hora)<='$fecha_fin' AND c.estado IN('REGISTRADO') AND c.idsucursal='$idsucursal'";
		} else if ($idproveedor == "Todos" and $idproducto != "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT c.fecha_kardex,pe.nombre as proveedor,c.tipo_comprobante,c.serie_comprobante,c.num_comprobante,c.idproveedor,p.nombre,dc.cantidad,(dc.cantidad * dc.precio_compra) as precio
					FROM detalle_compra dc
					INNER JOIN producto p ON dc.idproducto = p.idproducto
					INNER JOIN compra c ON c.idcompra= dc.idcompra
					INNER JOIN persona pe ON pe.idpersona = c.idproveedor
					WHERE DATE(c.fecha_hora)>='$fecha_inicio' AND DATE(c.fecha_hora)<='$fecha_fin' AND c.estado IN('REGISTRADO')  AND p.idproducto='$idproducto'";
		} else if ($idproveedor != "Todos" and $idproducto != "Todos" and $idsucursal == "Todos")  {

			$sql = "SELECT c.fecha_kardex,pe.nombre as proveedor,c.tipo_comprobante,c.serie_comprobante,c.num_comprobante,c.idproveedor,p.nombre,dc.cantidad,(dc.cantidad * dc.precio_compra) as precio
					FROM detalle_compra dc
					INNER JOIN producto p ON dc.idproducto = p.idproducto
					INNER JOIN compra c ON c.idcompra= dc.idcompra
					INNER JOIN persona pe ON pe.idpersona = c.idproveedor
					WHERE DATE(c.fecha_hora)>='$fecha_inicio' AND DATE(c.fecha_hora)<='$fecha_fin' AND c.estado IN('REGISTRADO') AND c.idproveedor = '$idproveedor' AND p.idproducto='$idproducto'";
		} else if ($idproveedor != "Todos" and $idproducto == "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT c.fecha_kardex,pe.nombre as proveedor,c.tipo_comprobante,c.serie_comprobante,c.num_comprobante,c.idproveedor,p.nombre,dc.cantidad,(dc.cantidad * dc.precio_compra) as precio
					FROM detalle_compra dc
					INNER JOIN producto p ON dc.idproducto = p.idproducto
					INNER JOIN compra c ON c.idcompra= dc.idcompra
					INNER JOIN persona pe ON pe.idpersona = c.idproveedor
					WHERE DATE(c.fecha_hora)>='$fecha_inicio' AND DATE(c.fecha_hora)<='$fecha_fin' AND c.estado IN('REGISTRADO') AND c.idproveedor = '$idproveedor'";
		} else{
			$sql = "SELECT c.fecha_kardex,pe.nombre as proveedor,c.tipo_comprobante,c.serie_comprobante,c.num_comprobante,c.idproveedor,p.nombre,dc.cantidad,(dc.cantidad * dc.precio_compra) as precio
					FROM detalle_compra dc
					INNER JOIN producto p ON dc.idproducto = p.idproducto
					INNER JOIN compra c ON c.idcompra= dc.idcompra
					INNER JOIN persona pe ON pe.idpersona = c.idproveedor
					WHERE DATE(c.fecha_hora)>='$fecha_inicio' AND DATE(c.fecha_hora)<='$fecha_fin' AND c.estado IN('REGISTRADO') AND c.idproveedor = '$idproveedor' AND c.idsucursal='$idsucursal' AND p.idproducto='$idproducto'";
		}

		return ejecutarConsulta($sql);
	}

/*$sql="SELECT date_format(c.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha, CONCAT('Compra Nacional') as motivo,concat_ws('-', c.serie_comprobante, c.num_comprobante) as comprobante, dt.cantidad as cantidad, CONCAT('0') as salida, dt.precio_compra as precio,
		format(dt.precio_compra * dt.cantidad,2) as valor, CONCAT('0') as stock, CONCAT('0') as valorexis
		from compra c
		INNER JOIN detalle_compra dt
		ON c.idcompra = dt.idcompra
		where dt.idproducto = '$idproducto' AND c.tipo_c = 'Compra'
        
        UNION ALL
        
        SELECT date_format(k.created_at,'%d/%m/%y | %H:%i:%s %p') as fecha, CASE WHEN k.tipo_movimiento = 1 THEN 'Salida' ELSE k.cantidad END as motivo, 'SIN COMPROBANTE' as comprobante, CASE WHEN k.tipo_movimiento = 0 THEN k.cantidad ELSE 0 END as cantidad, CASE WHEN k.tipo_movimiento = 1 THEN cantidad ELSE 0 END as salida, p.precio_compra as precio,
		format(p.precio_compra * k.cantidad,2) as valor, CONCAT('0') as stock, CONCAT('0') as valorexis
        FROM kardex k
        INNER JOIN producto p
        ON k.idproducto = p.idproducto
        WHERE k.idproducto = '$idproducto'
		
		UNION ALL
		
		SELECT date_format(c.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha, CONCAT('Venta Nacional') as motivo,concat_ws('-', c.serie_comprobante, c.num_comprobante) as comprobante, CONCAT('0') as cantidad,  dt.cantidad as salida, p.precio_compra as precio,
		format(p.precio_compra * dt.cantidad,2) as valor, CONCAT('0') as stock, CONCAT('0') as valorexis
		FROM venta c
		INNER JOIN detalle_venta dt
		ON c.idventa = dt.idventa
        INNER JOIN producto_configuracion pg
        ON dt.idproducto = pg.id
        INNER JOIN producto p
        ON pg.idproducto = p.idproducto
		WHERE dt.tipo != 'generar' AND pg.idproducto = '$idproducto'
		
		ORDER BY fecha ASC";*/

	/*public function listarKardex($fecha_inicio, $fecha_fin, $idproducto, $idcliente, $idsucursal)
	{

		
		if($idproducto != 'Todos'){
			$sql = "SELECT * FROM kardex WHERE idproducto = '$idproducto' ORDER BY created_at DESC";
		}else{
			$sql = "SELECT * FROM kardex ORDER BY created_at DESC";
		}

		return ejecutarConsulta($sql);

	}*/


	public function listarKardex($fecha_inicio, $fecha_fin, $idproducto, $idcliente, $idsucursal)
    {
        // Inicia la consulta base
        $sql = "SELECT k.* 
                FROM kardex k 
                JOIN producto p ON k.idproducto = p.idproducto 
                JOIN categoria c ON p.idcategoria = c.idcategoria 
                WHERE DATE(k.fecha_kardex)>='$fecha_inicio' AND DATE(k.fecha_kardex)<='$fecha_fin'
                AND c.nombre != 'SERVICIO'";
    
        // Añadir condiciones según los parámetros
        if ($idproducto != 'Todos') {
            $sql .= " AND k.idproducto = '$idproducto'";
        }
        if ($idsucursal != 'Todos') {
            $sql .= " AND k.idsucursal = '$idsucursal'";
        }
    
        // Ordenar por fecha del movimiento (no por id)
        $sql .= " ORDER BY k.fecha_kardex DESC";
    
        return ejecutarConsulta($sql);
    }

	public function verProducto($idproducto){
		$sql = "SELECT * FROM producto WHERE idproducto = '$idproducto'";
		$producto = ejecutarConsulta($sql)->fetch_object();
		if($producto){
			return $producto->nombre;
		}else{
			return "--";
		}
	}

	public function verSucursal($idsucursal){
		$sql = "SELECT * FROM sucursal WHERE idsucursal = '$idsucursal'";
		$sucursal = ejecutarConsulta($sql)->fetch_object();
		if($sucursal){
			return $sucursal->nombre;
		}else{
			return "--";
		}
	}

	public function verProveedor($idproveedor){
	    $sql = "SELECT * FROM persona WHERE idpersona = '$idproveedor'";
	    $persona = ejecutarConsulta($sql)->fetch_object();
	    if($persona){
	        return $persona->nombre;  // Nombre del proveedor
	    } else {
	        return "--";  // Si no se encuentra, retorna "--"
	    }
	}




	public function ventadetallecomprobante($fecha_inicio, $fecha_fin, $idproducto, $idcliente, $idsucursal, $tipo_comprobante)
	{

		if ($idproducto == "Todos" and $idcliente == "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT p.nombre, pg.contenedor, um.nombre as unidadmedida,v.tipo_comprobante as comprobante,
			SUM(dv.cantidad) as cantidad,
			SUM(dv.cantidad * dv.precio_venta) as precio,
			SUM(dv.cantidad * p.precio_compra) as precioCompra,
			SUM(dv.cantidad * dv.precio_venta) - SUM(dv.cantidad * p.precio_compra) as utilidad,
			pe.nombre as nombreVendedor
			 FROM detalle_venta dv
			 INNER JOIN venta v ON v.idventa = dv.idventa
			 INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto	
			 INNER JOIN producto p ON p.idproducto = pg.idproducto
			 INNER JOIN categoria c ON c.idcategoria = p.idcategoria
			 INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
			 INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
			 WHERE DATE(v.fecha_hora) >= '$fecha_inicio' AND DATE(v.fecha_hora) <= '$fecha_fin'
			   AND c.nombre != 'SERVICIO' 
			   AND v.tipo_comprobante = '$tipo_comprobante'
			   AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta') 
			   AND v.documento_rel = ''
			 GROUP BY p.nombre, um.nombre, pe.nombre, v.tipo_comprobante";
		} else if ($idproducto == "Todos" and $idcliente != "Todos" and $idsucursal != "Todos") {

			$sql = "SELECT p.nombre, pg.contenedor, um.nombre as unidadmedida,v.tipo_comprobante as comprobante,
			SUM(dv.cantidad) as cantidad,
			SUM(dv.cantidad * dv.precio_venta) as precio,
			SUM(dv.cantidad * p.precio_compra) as precioCompra,
			SUM(dv.cantidad * dv.precio_venta) - SUM(dv.cantidad * p.precio_compra) as utilidad,
			pe.nombre as nombreVendedor
			 FROM detalle_venta dv
			 INNER JOIN venta v ON v.idventa = dv.idventa
			 INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto	
			 INNER JOIN producto p ON p.idproducto = pg.idproducto
			 INNER JOIN categoria c ON c.idcategoria = p.idcategoria
			 INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
			 INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
			 WHERE DATE(v.fecha_hora) >= '$fecha_inicio' AND DATE(v.fecha_hora) <= '$fecha_fin'
			   AND c.nombre != 'SERVICIO' 
			   AND v.tipo_comprobante = '$tipo_comprobante'
			   AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta') 
			   AND v.documento_rel = ''
			 GROUP BY p.nombre, um.nombre, pe.nombre, v.tipo_comprobante";
		} else if ($idcliente == "Todos" and $idproducto == "Todos" and $idsucursal != "Todos") {

			$sql = "SELECT p.nombre, pg.contenedor, um.nombre as unidadmedida,v.tipo_comprobante as comprobante,
			SUM(dv.cantidad) as cantidad,
			SUM(dv.cantidad * dv.precio_venta) as precio,
			SUM(dv.cantidad * p.precio_compra) as precioCompra,
			SUM(dv.cantidad * dv.precio_venta) - SUM(dv.cantidad * p.precio_compra) as utilidad,
			pe.nombre as nombreVendedor
			 FROM detalle_venta dv
			 INNER JOIN venta v ON v.idventa = dv.idventa
			 INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto	
			 INNER JOIN producto p ON p.idproducto = pg.idproducto
			 INNER JOIN categoria c ON c.idcategoria = p.idcategoria
			 INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
			 INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
			 WHERE DATE(v.fecha_hora) >= '$fecha_inicio' AND DATE(v.fecha_hora) <= '$fecha_fin'
			   AND c.nombre != 'SERVICIO' 
			   AND v.tipo_comprobante = '$tipo_comprobante'
			   AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta') 
			   AND v.documento_rel = ''
			 GROUP BY p.nombre, um.nombre, pe.nombre, v.tipo_comprobante";
		} else if ($idcliente == "Todos" and $idproducto != "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT p.nombre, pg.contenedor, um.nombre as unidadmedida,v.tipo_comprobante as comprobante,
			SUM(dv.cantidad) as cantidad,
			SUM(dv.cantidad * dv.precio_venta) as precio,
			SUM(dv.cantidad * p.precio_compra) as precioCompra,
			SUM(dv.cantidad * dv.precio_venta) - SUM(dv.cantidad * p.precio_compra) as utilidad,
			pe.nombre as nombreVendedor
			 FROM detalle_venta dv
			 INNER JOIN venta v ON v.idventa = dv.idventa
			 INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto	
			 INNER JOIN producto p ON p.idproducto = pg.idproducto
			 INNER JOIN categoria c ON c.idcategoria = p.idcategoria
			 INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
			 INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
			 WHERE DATE(v.fecha_hora) >= '$fecha_inicio' AND DATE(v.fecha_hora) <= '$fecha_fin'
			   AND c.nombre != 'SERVICIO' 
			   AND v.tipo_comprobante = '$tipo_comprobante'
			   AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta') 
			   AND v.documento_rel = ''
			 GROUP BY p.nombre, um.nombre, pe.nombre, v.tipo_comprobante";
		} else {

			$sql = "SELECT p.nombre, pg.contenedor, um.nombre as unidadmedida,v.tipo_comprobante as comprobante,
			SUM(dv.cantidad) as cantidad,
			SUM(dv.cantidad * dv.precio_venta) as precio,
			SUM(dv.cantidad * p.precio_compra) as precioCompra,
			SUM(dv.cantidad * dv.precio_venta) - SUM(dv.cantidad * p.precio_compra) as utilidad,
			pe.nombre as nombreVendedor
			 FROM detalle_venta dv
			 INNER JOIN venta v ON v.idventa = dv.idventa
			 INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto	
			 INNER JOIN producto p ON p.idproducto = pg.idproducto
			 INNER JOIN categoria c ON c.idcategoria = p.idcategoria
			 INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
			 INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
			 WHERE DATE(v.fecha_hora) >= '$fecha_inicio' AND DATE(v.fecha_hora) <= '$fecha_fin'
			   AND c.nombre != 'SERVICIO' 
			   AND v.tipo_comprobante = '$tipo_comprobante'
			   AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta') 
			   AND v.documento_rel = ''
			 GROUP BY p.nombre, um.nombre, pe.nombre, v.tipo_comprobante";
		}

		return ejecutarConsulta($sql);
	}

	public function ventadetallecomprobante2($fecha_inicio, $fecha_fin, $idproducto, $idcliente, $idsucursal)
	{

		if ($idproducto == "Todos" and $idcliente == "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT 
			    v.tipo_comprobante AS comprobante,
			    v.serie_comprobante,
			    v.num_comprobante,
			    SUM(dv.cantidad) AS cantidad,
			    SUM(dv.cantidad * dv.precio_venta) AS precio,
			    SUM(dv.cantidad * p.precio_compra) AS precioCompra,
			    IFNULL(agg.abonos, 0) AS abonos,
			    (IFNULL(agg.abonos, 0) - SUM(dv.cantidad * p.precio_compra)) AS utilidad
			FROM detalle_venta dv
			INNER JOIN venta v ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto    
			INNER JOIN producto p ON p.idproducto = pg.idproducto
			INNER JOIN categoria c ON c.idcategoria = p.idcategoria
			INNER JOIN persona pe ON pe.idpersona = v.idcliente
			INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
			LEFT JOIN (
			    SELECT cpc.idventa, SUM(dcpc.montopagado) AS abonos
			    FROM cuentas_por_cobrar cpc
			    INNER JOIN detalle_cuentas_por_cobrar dcpc ON cpc.idcpc = dcpc.idcpc
			    GROUP BY cpc.idventa
			) agg ON agg.idventa = v.idventa
			WHERE DATE(v.fecha_hora) >= '$fecha_inicio'
			  AND DATE(v.fecha_hora) <= '$fecha_fin'
			  AND c.nombre != 'SERVICIO'
			  AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')
			  AND v.estado!='Anulado' AND v.estado != 'Nota Credito'
			  AND v.documento_rel = ''
			  AND v.ventacredito = 'Si'
			GROUP BY v.tipo_comprobante, v.serie_comprobante, v.num_comprobante";

		} else if ($idproducto == "Todos" and $idcliente != "Todos" and $idsucursal != "Todos") {

			$sql = "SELECT 
				    v.tipo_comprobante AS comprobante,
				    v.serie_comprobante,
				    v.num_comprobante,
				    SUM(dv.cantidad) AS cantidad,
				    SUM(dv.cantidad * dv.precio_venta) AS precio,
				    SUM(dv.cantidad * p.precio_compra) AS precioCompra,
				    IFNULL(agg.abonos, 0) AS abonos,
				    (IFNULL(agg.abonos, 0) - SUM(dv.cantidad * p.precio_compra)) AS utilidad
				FROM detalle_venta dv
				INNER JOIN venta v ON v.idventa = dv.idventa
				INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto    
				INNER JOIN producto p ON p.idproducto = pg.idproducto
				INNER JOIN categoria c ON c.idcategoria = p.idcategoria
				INNER JOIN persona pe ON pe.idpersona = v.idcliente
				INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
				LEFT JOIN (
				    SELECT cpc.idventa, SUM(dcpc.montopagado) AS abonos
				    FROM cuentas_por_cobrar cpc
				    INNER JOIN detalle_cuentas_por_cobrar dcpc ON cpc.idcpc = dcpc.idcpc
				    GROUP BY cpc.idventa
				) agg ON agg.idventa = v.idventa
				WHERE DATE(v.fecha_hora) >= '$fecha_inicio'
				  AND DATE(v.fecha_hora) <= '$fecha_fin'
				  AND c.nombre != 'SERVICIO'
				  AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')
				  AND v.estado!='Anulado' AND v.estado != 'Nota Credito'
				  AND v.documento_rel = ''
				  AND v.ventacredito = 'Si'
				  AND pe.idpersona = '$idcliente'
				  AND v.idsucursal = '$idsucursal'
				GROUP BY v.tipo_comprobante, v.serie_comprobante, v.num_comprobante";

		} else if ($idcliente == "Todos" and $idproducto == "Todos" and $idsucursal != "Todos") {

			$sql = "SELECT 
				    v.tipo_comprobante AS comprobante,
				    v.serie_comprobante,
				    v.num_comprobante,
				    SUM(dv.cantidad) AS cantidad,
				    SUM(dv.cantidad * dv.precio_venta) AS precio,
				    SUM(dv.cantidad * p.precio_compra) AS precioCompra,
				    IFNULL(agg.abonos, 0) AS abonos,
				    (IFNULL(agg.abonos, 0) - SUM(dv.cantidad * p.precio_compra)) AS utilidad
				FROM detalle_venta dv
				INNER JOIN venta v ON v.idventa = dv.idventa
				INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto    
				INNER JOIN producto p ON p.idproducto = pg.idproducto
				INNER JOIN categoria c ON c.idcategoria = p.idcategoria
				INNER JOIN persona pe ON pe.idpersona = v.idcliente
				INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
				LEFT JOIN (
				    SELECT cpc.idventa, SUM(dcpc.montopagado) AS abonos
				    FROM cuentas_por_cobrar cpc
				    INNER JOIN detalle_cuentas_por_cobrar dcpc ON cpc.idcpc = dcpc.idcpc
				    GROUP BY cpc.idventa
				) agg ON agg.idventa = v.idventa
				WHERE DATE(v.fecha_hora) >= '$fecha_inicio'
				  AND DATE(v.fecha_hora) <= '$fecha_fin'
				  AND c.nombre != 'SERVICIO'
				  AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')
				  AND v.estado!='Anulado' AND v.estado != 'Nota Credito'
				  AND v.documento_rel = ''
				  AND v.ventacredito = 'Si'
				  AND v.idsucursal = '$idsucursal'
				GROUP BY v.tipo_comprobante, v.serie_comprobante, v.num_comprobante";

		} else if ($idcliente != "Todos" and $idproducto == "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT 
				    v.tipo_comprobante AS comprobante,
				    v.serie_comprobante,
				    v.num_comprobante,
				    SUM(dv.cantidad) AS cantidad,
				    SUM(dv.cantidad * dv.precio_venta) AS precio,
				    SUM(dv.cantidad * p.precio_compra) AS precioCompra,
				    IFNULL(agg.abonos, 0) AS abonos,
				    (IFNULL(agg.abonos, 0) - SUM(dv.cantidad * p.precio_compra)) AS utilidad
				FROM detalle_venta dv
				INNER JOIN venta v ON v.idventa = dv.idventa
				INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto    
				INNER JOIN producto p ON p.idproducto = pg.idproducto
				INNER JOIN categoria c ON c.idcategoria = p.idcategoria
				INNER JOIN persona pe ON pe.idpersona = v.idcliente
				INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
				LEFT JOIN (
				    SELECT cpc.idventa, SUM(dcpc.montopagado) AS abonos
				    FROM cuentas_por_cobrar cpc
				    INNER JOIN detalle_cuentas_por_cobrar dcpc ON cpc.idcpc = dcpc.idcpc
				    GROUP BY cpc.idventa
				) agg ON agg.idventa = v.idventa
				WHERE DATE(v.fecha_hora) >= '$fecha_inicio'
				  AND DATE(v.fecha_hora) <= '$fecha_fin'
				  AND c.nombre != 'SERVICIO'
				  AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')
				  AND v.estado!='Anulado' AND v.estado != 'Nota Credito'
				  AND v.documento_rel = ''
				  AND v.ventacredito = 'Si'
				  AND pe.idpersona = '$idcliente'
				GROUP BY v.tipo_comprobante, v.serie_comprobante, v.num_comprobante";

		}else if ($idcliente == "Todos" and $idproducto != "Todos" and $idsucursal == "Todos") {

			$sql = "SELECT 
				    v.tipo_comprobante AS comprobante,
				    v.serie_comprobante,
				    v.num_comprobante,
				    SUM(dv.cantidad) AS cantidad,
				    SUM(dv.cantidad * dv.precio_venta) AS precio,
				    SUM(dv.cantidad * p.precio_compra) AS precioCompra,
				    IFNULL(agg.abonos, 0) AS abonos,
				    (IFNULL(agg.abonos, 0) - SUM(dv.cantidad * p.precio_compra)) AS utilidad
				FROM detalle_venta dv
				INNER JOIN venta v ON v.idventa = dv.idventa
				INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto    
				INNER JOIN producto p ON p.idproducto = pg.idproducto
				INNER JOIN categoria c ON c.idcategoria = p.idcategoria
				INNER JOIN persona pe ON pe.idpersona = v.idcliente
				INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
				LEFT JOIN (
				    SELECT cpc.idventa, SUM(dcpc.montopagado) AS abonos
				    FROM cuentas_por_cobrar cpc
				    INNER JOIN detalle_cuentas_por_cobrar dcpc ON cpc.idcpc = dcpc.idcpc
				    GROUP BY cpc.idventa
				) agg ON agg.idventa = v.idventa
				WHERE DATE(v.fecha_hora) >= '$fecha_inicio'
				  AND DATE(v.fecha_hora) <= '$fecha_fin'
				  AND c.nombre != 'SERVICIO'
				  AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')
				  AND v.estado!='Anulado' AND v.estado != 'Nota Credito'
				  AND v.documento_rel = ''
				  AND v.ventacredito = 'Si'
				  AND p.idproducto = '$idproducto'
				GROUP BY v.tipo_comprobante, v.serie_comprobante, v.num_comprobante";

		} else {

			$sql = "SELECT 
				    v.tipo_comprobante AS comprobante,
				    v.serie_comprobante,
				    v.num_comprobante,
				    SUM(dv.cantidad) AS cantidad,
				    SUM(dv.cantidad * dv.precio_venta) AS precio,
				    SUM(dv.cantidad * p.precio_compra) AS precioCompra,
				    IFNULL(agg.abonos, 0) AS abonos,
				    (IFNULL(agg.abonos, 0) - SUM(dv.cantidad * p.precio_compra)) AS utilidad
				FROM detalle_venta dv
				INNER JOIN venta v ON v.idventa = dv.idventa
				INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto    
				INNER JOIN producto p ON p.idproducto = pg.idproducto
				INNER JOIN categoria c ON c.idcategoria = p.idcategoria
				INNER JOIN persona pe ON pe.idpersona = v.idcliente
				INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
				LEFT JOIN (
				    SELECT cpc.idventa, SUM(dcpc.montopagado) AS abonos
				    FROM cuentas_por_cobrar cpc
				    INNER JOIN detalle_cuentas_por_cobrar dcpc ON cpc.idcpc = dcpc.idcpc
				    GROUP BY cpc.idventa
				) agg ON agg.idventa = v.idventa
				WHERE DATE(v.fecha_hora) >= '$fecha_inicio'
				  AND DATE(v.fecha_hora) <= '$fecha_fin'
				  AND c.nombre != 'SERVICIO'
				  AND v.tipo_comprobante IN ('Factura','Boleta','Nota de Venta')
				  AND v.estado!='Anulado' AND v.estado != 'Nota Credito'
				  AND v.documento_rel = ''
				  AND v.ventacredito = 'Si'
				  AND pe.idpersona = '$idcliente'
				  AND p.idproducto = '$idproducto'
				  AND v.idsucursal = '$idsucursal'
				GROUP BY v.tipo_comprobante, v.serie_comprobante, v.num_comprobante";

		}

		return ejecutarConsulta($sql);
	}



	public function totalcomprahoy($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT IFNULL(SUM(total_compra),0) as total_compra FROM compra WHERE estado != 'Anulado' AND tipo_c='Compra' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT IFNULL(SUM(total_compra),0) as total_compra FROM compra WHERE estado != 'Anulado' AND tipo_c='Compra' AND idpersonal = '$idvendedor' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT IFNULL(SUM(total_compra),0) as total_compra FROM compra WHERE estado != 'Anulado' AND tipo_c='Compra' AND idsucursal = '$idsucursal' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else {
			$sql = "SELECT IFNULL(SUM(total_compra),0) as total_compra FROM compra WHERE estado != 'Anulado' AND tipo_c='Compra' AND idpersonal = '$idvendedor' AND idsucursal = '$idsucursal' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		}
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalcomprahoyC($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT IFNULL(SUM(total_compra),0) as total_compra FROM compra WHERE formapago != 'Efectivo' AND estado != 'Anulado' AND tipo_c='Compra' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT IFNULL(SUM(total_compra),0) as total_compra FROM compra WHERE formapago != 'Efectivo' AND estado != 'Anulado' AND tipo_c='Compra' AND idpersonal = '$idvendedor' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT IFNULL(SUM(total_compra),0) as total_compra FROM compra WHERE formapago != 'Efectivo' AND estado != 'Anulado' AND tipo_c='Compra' AND idsucursal = '$idsucursal' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else {
			$sql = "SELECT IFNULL(SUM(total_compra),0) as total_compra FROM compra WHERE formapago != 'Efectivo' AND estado != 'Anulado' AND tipo_c='Compra' AND idpersonal = '$idvendedor' AND idsucursal = '$idsucursal' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		}
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalcomprahoyefectivo($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT IFNULL(SUM(totalrecibido),0) as total_compra FROM compra WHERE estado != 'Anulado'  AND tipo_c='Compra' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT IFNULL(SUM(totalrecibido),0) as total_compra FROM compra WHERE estado != 'Anulado'  AND tipo_c='Compra' AND idpersonal = '$idvendedor' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT IFNULL(SUM(totalrecibido),0) as total_compra FROM compra WHERE estado != 'Anulado'  AND tipo_c='Compra' AND idsucursal = '$idsucursal' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else {
			$sql = "SELECT IFNULL(SUM(totalrecibido),0) as total_compra FROM compra WHERE estado != 'Anulado'  AND tipo_c='Compra' AND idpersonal = '$idvendedor' AND idsucursal = '$idsucursal' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		}
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalcomprahoyyape($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT IFNULL(SUM(totaldeposito),0) as total_compra FROM compra WHERE estado != 'Anulado' AND  formapago = 'yape' AND tipo_c='Compra' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT IFNULL(SUM(totaldeposito),0) as total_compra FROM compra WHERE estado != 'Anulado' AND  formapago = 'yape' AND tipo_c='Compra' AND idpersonal = '$idvendedor' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT IFNULL(SUM(totaldeposito),0) as total_compra FROM compra WHERE estado != 'Anulado' AND  formapago = 'yape' AND tipo_c='Compra' AND idsucursal = '$idsucursal' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else {
			$sql = "SELECT IFNULL(SUM(totaldeposito),0) as total_compra FROM compra WHERE estado != 'Anulado' AND  formapago = 'yape' AND tipo_c='Compra' AND idpersonal = '$idvendedor' AND idsucursal = '$idsucursal' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		}
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalcomprahoyplin($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT IFNULL(SUM(totaldeposito),0) as total_compra FROM compra WHERE estado != 'Anulado' AND  formapago = 'plin' AND tipo_c='Compra' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT IFNULL(SUM(totaldeposito),0) as total_compra FROM compra WHERE estado != 'Anulado' AND  formapago = 'plin' AND tipo_c='Compra' AND idpersonal = '$idvendedor' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT IFNULL(SUM(totaldeposito),0) as total_compra FROM compra WHERE estado != 'Anulado' AND  formapago = 'plin' AND tipo_c='Compra' AND idsucursal = '$idsucursal' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else {
			$sql = "SELECT IFNULL(SUM(totaldeposito),0) as total_compra FROM compra WHERE estado != 'Anulado' AND  formapago = 'plin' AND tipo_c='Compra' AND idpersonal = '$idvendedor' AND idsucursal = '$idsucursal' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		}
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalcomprahoyop($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT IFNULL(SUM(totaldeposito),0) as total_compra FROM compra WHERE estado != 'Anulado' AND  formapago IN ('Transferencia','Tarjeta','Deposito') AND tipo_c='Compra' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT IFNULL(SUM(totaldeposito),0) as total_compra FROM compra WHERE estado != 'Anulado' AND  formapago IN ('Transferencia','Tarjeta','Deposito') AND tipo_c='Compra' AND idpersonal = '$idvendedor' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT IFNULL(SUM(totaldeposito),0) as total_compra FROM compra WHERE estado != 'Anulado' AND  formapago IN ('Transferencia','Tarjeta','Deposito') AND tipo_c='Compra' AND idsucursal = '$idsucursal' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else {
			$sql = "SELECT IFNULL(SUM(totaldeposito),0) as total_compra FROM compra WHERE estado != 'Anulado' AND  formapago IN ('Transferencia','Tarjeta','Deposito') AND tipo_c='Compra' AND idpersonal = '$idvendedor' AND idsucursal = '$idsucursal' AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		}
		return ejecutarConsultaSimpleFila($sql);
	}


	public function totalventahoy($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT IFNULL(SUM(total_venta-descuento),0) as total_venta FROM venta WHERE ventacredito = 'No' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT IFNULL(SUM(total_venta-descuento),0) as total_venta FROM venta WHERE ventacredito = 'No' AND idpersonal = '$idvendedor' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT IFNULL(SUM(total_venta-descuento),0) as total_venta FROM venta WHERE ventacredito = 'No' AND idsucursal = '$idsucursal' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else {
			$sql = "SELECT IFNULL(SUM(total_venta-descuento),0) as total_venta FROM venta WHERE ventacredito = 'No' AND idpersonal = '$idvendedor' AND idsucursal = '$idsucursal' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		}
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalcuentasporcobrar($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT IFNULL(SUM(deudatotal),0) as totaldeuda FROM cuentas_por_cobrar where condicion=1 AND DATE(fecharegistro)>='$fecha_inicio' AND DATE(fecharegistro)<='$fecha_fin'";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT IFNULL(SUM(deudatotal),0) as totaldeuda FROM cuentas_por_cobrar cc INNER JOIN venta v ON cc.idventa=v.idventa where cc.condicion=1 AND v.idpersonal = '$idvendedor' AND DATE(cc.fecharegistro)>='$fecha_inicio' AND DATE(cc.fecharegistro)<='$fecha_fin'";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT IFNULL(SUM(cc.deudatotal),0) as totaldeuda FROM cuentas_por_cobrar cc INNER JOIN venta v ON cc.idventa=v.idventa where cc.condicion=1 AND v.idsucursal = '$idsucursal' AND DATE(cc.fecharegistro)>='$fecha_inicio' AND DATE(cc.fecharegistro)<='$fecha_fin'";
		} else {
			$sql = "SELECT IFNULL(SUM(cc.deudatotal),0) as totaldeuda FROM cuentas_por_cobrar cc INNER JOIN venta v ON cc.idventa=v.idventa where condicion=1 AND v.idpersonal = '$idvendedor' AND v.idsucursal = '$idsucursal' AND DATE(cc.fecharegistro)>='$fecha_inicio' AND DATE(cc.fecharegistro)<='$fecha_fin'";
		}
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalcuentasporpagar($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT IFNULL(SUM(deudatotal),0) as totaldeuda FROM cuentas_por_pagar where condicion=1 AND DATE(fecharegistro)>='$fecha_inicio' AND DATE(fecharegistro)<='$fecha_fin'";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT IFNULL(SUM(deudatotal),0) as totaldeuda FROM cuentas_por_pagar cc INNER JOIN compra v ON cc.idcompra=v.idcompra where cc.condicion=1 AND v.idpersonal = '$idvendedor' AND DATE(cc.fecharegistro)>='$fecha_inicio' AND DATE(cc.fecharegistro)<='$fecha_fin'";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT IFNULL(SUM(cc.deudatotal),0) as totaldeuda FROM cuentas_por_pagar cc INNER JOIN compra v ON cc.idcompra=v.idcompra where cc.condicion=1 AND v.idsucursal = '$idsucursal' AND DATE(cc.fecharegistro)>='$fecha_inicio' AND DATE(cc.fecharegistro)<='$fecha_fin'";
		} else {
			$sql = "SELECT IFNULL(SUM(cc.deudatotal),0) as totaldeuda FROM cuentas_por_pagar cc INNER JOIN compra v ON cc.idcompra=v.idcompra where condicion=1 AND v.idpersonal = '$idvendedor' AND v.idsucursal = '$idsucursal' AND DATE(cc.fecharegistro)>='$fecha_inicio' AND DATE(cc.fecharegistro)<='$fecha_fin'";
		}
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalabonospagados($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT IFNULL(SUM(montopagado),0) as totaldeuda FROM detalle_cuentas_por_pagar  where  DATE(fechapago)>='$fecha_inicio' AND DATE(fechapago)<='$fecha_fin'";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT IFNULL(SUM(dcpp.montopagado),0) as totaldeuda FROM detalle_cuentas_por_pagar dcpp INNER JOIN cuentas_por_pagar cpp ON cpp.idcpp=dcpp.idcpp INNER JOIN compra c ON c.idcompra=cpp.idcompra  where  DATE(dcpp.fechapago)>='$fecha_inicio' AND DATE(dcpp.fechapago)<='$fecha_fin' AND c.idpersonal='$idvendedor'";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT IFNULL(SUM(dcpp.montopagado),0) as totaldeuda FROM detalle_cuentas_por_pagar dcpp INNER JOIN cuentas_por_pagar cpp ON cpp.idcpp=dcpp.idcpp INNER JOIN compra c ON c.idcompra=cpp.idcompra  where  DATE(dcpp.fechapago)>='$fecha_inicio' AND DATE(dcpp.fechapago)<='$fecha_fin' AND c.idsucursal='$idsucursal'";
		} else {
			$sql = "SELECT IFNULL(SUM(dcpp.montopagado),0) as totaldeuda FROM detalle_cuentas_por_pagar dcpp INNER JOIN cuentas_por_pagar cpp ON cpp.idcpp=dcpp.idcpp INNER JOIN compra c ON c.idcompra=cpp.idcompra  where  DATE(dcpp.fechapago)>='$fecha_inicio' AND DATE(dcpp.fechapago)<='$fecha_fin' AND c.idsucursal='$idsucursal' AND c.idpersonal='$idvendedor'";
		}
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalabonoscobrados($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT IFNULL(SUM(montopagado),0) as totaldeuda FROM detalle_cuentas_por_cobrar  where  DATE(fechapago)>='$fecha_inicio' AND DATE(fechapago)<='$fecha_fin'";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT IFNULL(SUM(dcpc.montopagado),0) as totaldeuda FROM detalle_cuentas_por_cobrar dcpc INNER JOIN cuentas_por_cobrar cpc ON cpc.idcpc=dcpc.idcpc INNER JOIN venta v ON v.idventa=cpc.idventa  where  DATE(dcpc.fechapago)>='$fecha_inicio' AND DATE(dcpc.fechapago)<='$fecha_fin' AND v.idpersonal='$idvendedor'";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT IFNULL(SUM(dcpc.montopagado),0) as totaldeuda FROM detalle_cuentas_por_cobrar dcpc INNER JOIN cuentas_por_cobrar cpc ON cpc.idcpc=dcpc.idcpc INNER JOIN venta v ON v.idventa=cpc.idventa  where  DATE(dcpc.fechapago)>='$fecha_inicio' AND DATE(dcpc.fechapago)<='$fecha_fin' AND v.idsucursal='$idsucursal'";
		} else {
			$sql = "SELECT IFNULL(SUM(dcpc.montopagado),0) as totaldeuda FROM detalle_cuentas_por_cobrar dcpc INNER JOIN cuentas_por_cobrar cpc ON cpc.idcpc=dcpc.idcpc INNER JOIN venta v ON v.idventa=cpc.idventa  where  DATE(dcpc.fechapago)>='$fecha_inicio' AND DATE(dcpc.fechapago)<='$fecha_fin' AND v.idsucursal='$idsucursal' AND v.idpersonal='$idvendedor'";
		}
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalutilidadhoy($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT ROUND(IFNULL(SUM(utilidad),0), 2) as total_utilidad
					FROM (
					    SELECT 
					        v.fecha_hora, 
					        p.nombre, 
					        pg.contenedor,
					        dv.cantidad_contenedor, 
					        (dv.cantidad * dv.precio_venta) as precio, ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as precioCompra, (dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as utilidad, pe.nombre as nombreVendedor , v.descuento as descuento 
					    FROM detalle_venta dv
					    INNER JOIN venta v ON v.idventa = dv.idventa
					    INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
					    INNER JOIN producto p ON p.idproducto = pg.idproducto
					    INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
					    INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
					    INNER JOIN categoria c ON c.idcategoria = p.idcategoria
					    WHERE ventacredito = 'No' 
							AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado') 
							AND DATE(fecha_hora)>='$fecha_inicio'
							AND DATE(fecha_hora)<='$fecha_fin'
					)AS subquery";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT ROUND(IFNULL(SUM(utilidad),0), 2) as total_utilidad
					FROM (
					    SELECT 
					        v.fecha_hora, 
					        p.nombre, 
					        pg.contenedor,
					        dv.cantidad_contenedor, 
					        (dv.cantidad * dv.precio_venta) as precio, ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as precioCompra, (dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as utilidad, pe.nombre as nombreVendedor, v.descuento as descuento   
					    FROM detalle_venta dv
					    INNER JOIN venta v ON v.idventa = dv.idventa
					    INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
					    INNER JOIN producto p ON p.idproducto = pg.idproducto
					    INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
					    INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
					    INNER JOIN categoria c ON c.idcategoria = p.idcategoria
					    WHERE ventacredito = 'No' 
							AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado') 
							AND DATE(fecha_hora)>='$fecha_inicio'
							AND DATE(fecha_hora)<='$fecha_fin'
							AND v.idPersonal='$idvendedor'
					)AS subquery";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT ROUND(IFNULL(SUM(utilidad),0), 2) as total_utilidad
					FROM (
					    SELECT 
					        v.fecha_hora, 
					        p.nombre, 
					        pg.contenedor,
					        dv.cantidad_contenedor, 
					        (dv.cantidad * dv.precio_venta) as precio, ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as precioCompra, (dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as utilidad, pe.nombre as nombreVendedor, v.descuento as descuento  
					    FROM detalle_venta dv
					    INNER JOIN venta v ON v.idventa = dv.idventa
					    INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
					    INNER JOIN producto p ON p.idproducto = pg.idproducto
					    INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
					    INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
					    INNER JOIN categoria c ON c.idcategoria = p.idcategoria
					    WHERE ventacredito = 'No' 
							AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado') 
							AND DATE(fecha_hora)>='$fecha_inicio'
							AND DATE(fecha_hora)<='$fecha_fin'
							AND dv.idsucursal='$idsucursal'
					)AS subquery";
		} else {
			$sql = "SELECT ROUND(IFNULL(SUM(utilidad),0), 2) as total_utilidad
					FROM (
					    SELECT 
					        v.fecha_hora, 
					        p.nombre, 
					        pg.contenedor,
					        dv.cantidad_contenedor, 
					        (dv.cantidad * dv.precio_venta) as precio, ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as precioCompra, (dv.cantidad* dv.precio_venta) - ((dv.cantidad*dv.cantidad_contenedor) * p.precio_compra) as utilidad, pe.nombre as nombreVendedor, v.descuento as descuento   
					    FROM detalle_venta dv
					    INNER JOIN venta v ON v.idventa = dv.idventa
					    INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
					    INNER JOIN producto p ON p.idproducto = pg.idproducto
					    INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
					    INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
					    INNER JOIN categoria c ON c.idcategoria = p.idcategoria
					    WHERE ventacredito = 'No' 
							AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado') 
							AND DATE(fecha_hora)>='$fecha_inicio'
							AND DATE(fecha_hora)<='$fecha_fin'
							AND dv.idsucursal='$idsucursal'
							AND v.idpersonal='$idvendedor'
					)AS subquery";
		}
		return ejecutarConsultaSimpleFila($sql);
	}



	public function totalventachoy($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal)
	{
		if ($idvendedor == "Todos" || $idvendedor == null and $idsucursal == "Todos" || $idsucursal == null) {
			$sql = "SELECT IFNULL(SUM(total_venta),0) as total_venta FROM venta WHERE ventacredito = 'Si' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor == "Todos" and $idsucursal != "Todos") {
			$sql = "SELECT IFNULL(SUM(total_venta),0) as total_venta FROM venta WHERE ventacredito = 'Si' AND idsucursal = '$idsucursal' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else if ($idvendedor != "Todos" and $idsucursal == "Todos") {
			$sql = "SELECT IFNULL(SUM(total_venta),0) as total_venta FROM venta WHERE ventacredito = 'Si' AND idpersonal = '$idvendedor' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		} else {
			$sql = "SELECT IFNULL(SUM(total_venta),0) as total_venta FROM venta WHERE ventacredito = 'Si' AND idpersonal = '$idvendedor' AND idsucursal = '$idsucursal' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin'";
		}
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalcategorias()
	{
		$sql = "SELECT COUNT(*) totalca FROM categoria WHERE condicion=1";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalproductos()
	{
		$sql = "SELECT COUNT(*) totalpro FROM producto WHERE condicion=1";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalusuariosr()
	{
		$sql = "SELECT IFNULL(count(idpersonal),0) as idpersonal FROM personal";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalproveedoresr()
	{
		$sql = "SELECT IFNULL(count(idpersona),0) as idpersona FROM persona WHERE tipo_persona='Proveedor'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function comprasultimos_10dias($idsucursal)
	{
		if($idsucursal == "Todos" || $idsucursal == null){
			$sql = "SELECT CONCAT(DAY(fecha_hora),'-',DATE_FORMAT(fecha_hora,'%M')) as fecha,SUM(total_compra) as total FROM compra WHERE tipo_c = 'Compra' AND estado != 'Anulado' GROUP by fecha_hora ORDER BY fecha_hora DESC limit 0,10";
		} else {
			$sql = "SELECT CONCAT(DAY(fecha_hora),'-',DATE_FORMAT(fecha_hora,'%M')) as fecha,SUM(total_compra) as total FROM compra WHERE tipo_c = 'Compra' AND estado != 'Anulado' AND idsucursal = '$idsucursal' GROUP by fecha_hora ORDER BY fecha_hora DESC limit 0,10";
		}
		return ejecutarConsulta($sql);
	}

	public function ventasultimos_12meses($idsucursal)
	{
		//Date format -> convertir fecha y hora en un formato de mes
		if($idsucursal == "Todos" || $idsucursal == null){
			$sql = "SELECT DATE_FORMAT(fecha_hora,'%M') as fecha,SUM(total_venta) as total FROM venta WHERE estado IN('Aceptado','Activado') GROUP by MONTH(fecha_hora) ORDER BY fecha_hora DESC limit 0,12";
		} else {
			$sql = "SELECT DATE_FORMAT(fecha_hora,'%M') as fecha,SUM(total_venta) as total FROM venta WHERE idsucursal = '$idsucursal' AND estado IN('Aceptado','Activado') GROUP by MONTH(fecha_hora) ORDER BY fecha_hora DESC limit 0,12";
		}
		return ejecutarConsulta($sql);
	}

	public function utilidadUltimos12Meses($idvendedor, $idsucursal)
{
    // Forzar meses en español
    ejecutarConsulta("SET lc_time_names = 'es_ES'");

    $filtroVendedor = (!empty($idvendedor) && $idvendedor !== "Todos") ? "AND v.idPersonal = " . intval($idvendedor) : "";
    $filtroSucursal = (!empty($idsucursal) && $idsucursal !== "Todos") ? "AND dv.idsucursal = " . intval($idsucursal) : "";

    $sql = "
        SELECT DATE_FORMAT(m.mes, '%M') AS mes,
               IFNULL(SUM((dv.cantidad * dv.precio_venta) - ((dv.cantidad * dv.cantidad_contenedor) * p.precio_compra)), 0) AS total_utilidad
        FROM (
            SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL seq MONTH), '%Y-%m-01') AS mes
            FROM (
                SELECT 0 AS seq UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3
                UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7
                UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11
            ) AS seqs
        ) AS m
        LEFT JOIN venta v
            ON DATE_FORMAT(v.fecha_hora, '%Y-%m-01') = m.mes
            AND v.ventacredito = 'No' 
            AND v.estado IN ('Aceptado','Por Enviar','Activado')
            $filtroVendedor
        LEFT JOIN detalle_venta dv
            ON dv.idventa = v.idventa
            $filtroSucursal
        LEFT JOIN producto_configuracion pg ON pg.id = dv.idproducto
        LEFT JOIN producto p ON p.idproducto = pg.idproducto
        GROUP BY m.mes
        ORDER BY m.mes ASC
    ";

    return ejecutarConsulta($sql);
}


	public function IngresosEgresosMesesDelAnio()
	{
	    ejecutarConsulta("SET lc_time_names = 'es_ES'");

	    $sql = "
	        SELECT 
	            DATE_FORMAT(mes, '%M') AS mes,
	            IFNULL(SUM(CASE WHEN m.tipo = 'Ingresos' THEN m.monto END), 0) AS ingresos,
	            IFNULL(SUM(CASE WHEN m.tipo = 'Egresos' THEN m.monto END), 0) AS egresos
	        FROM (
	            SELECT DATE_FORMAT(DATE_ADD(MAKEDATE(YEAR(CURDATE()), 1), INTERVAL n MONTH), '%Y-%m-01') AS mes
	            FROM (
	                SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
	                UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 
	                UNION SELECT 10 UNION SELECT 11
	            ) AS nums
	            WHERE DATE_ADD(MAKEDATE(YEAR(CURDATE()), 1), INTERVAL n MONTH) <= LAST_DAY(CURDATE())
	        ) AS meses
	        LEFT JOIN movimiento m 
	            ON DATE_FORMAT(m.fecha, '%Y-%m-01') = meses.mes
	        GROUP BY mes
	        ORDER BY mes ASC
	    ";

	    return ejecutarConsulta($sql);
	}



	public function productosmasvendidos()
	{
		//Date format -> convertir fecha y hora en un formato de mes
		$sql = "SELECT dv.idproducto,p.nombre as nombre,  SUM(dv.cantidad) AS cantidad
			FROM venta v
			INNER JOIN detalle_venta dv ON v.idventa = dv.idventa
			INNER JOIN producto_configuracion pg on dv.idproducto=pg.id
			INNER JOIN producto p on pg.idproducto = p.idproducto
			GROUP BY dv.idproducto, p.nombre
			ORDER BY SUM(dv.cantidad) DESC
			LIMIT 6";
		return ejecutarConsulta($sql);
	}

	public function stockproductosmasbajos()
	{
		$sql = "SELECT p.idproducto,p.nombre,c.nombre as categoria,p.stock
		FROM producto p
        INNER JOIN categoria c
        on p.idcategoria=c.idcategoria
        WHERE stock<=5 AND c.nombre != 'SERVICIO'
		GROUP BY idproducto
		LIMIT 0 , 5";
		return ejecutarConsulta($sql);
	}

	public function cantidadarticulos()
	{
		$sql = "SELECT COUNT(*) totalar FROM producto WHERE condicion=1";
		return ejecutarConsulta($sql);
	}
	public function totalstock()
	{
		$sql = "SELECT SUM(stock) AS totalstock FROM producto";
		return ejecutarConsulta($sql);
	}

	public function cantidadcategorias()
	{
		$sql = "SELECT COUNT(*) totalca FROM categoria WHERE condicion=1";
		return ejecutarConsulta($sql);
	}

	public function mostrarTotalBoletasCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{
	    // Filtros dinámicos
	    $filtroSucursal = $idsucursal != "Todos" ? "AND v.idsucursal = '$idsucursal'" : "";
	    $filtroVendedor = $idvendedor != "Todos" ? "AND v.idpersonal = '$idvendedor'" : "";

	    $sql = "SELECT 
	                IFNULL(
	                    SUM(
	                        CASE 
	                            WHEN v.ventacredito = 'no' THEN IFNULL(vp.monto,0)
	                            WHEN v.ventacredito = 'si' THEN IFNULL(v.montoPagado,0)
	                            ELSE 0
	                        END
	                    ), 0
	                ) AS total_venta
	            FROM venta v
	            INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
	            LEFT JOIN venta_pago vp ON v.idventa = vp.idventa AND vp.metodo_pago = 'Efectivo'
	            WHERE DATE(v.fecha_hora) >= '$fecha_inicio'
	              AND DATE(v.fecha_hora) <= '$fecha_fin'
	              AND v.tipo_comprobante = 'Boleta'
	              AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
	              $filtroSucursal
	              $filtroVendedor";

	    return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarTotalBoletasTCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{
	    // Filtros dinámicos
	    $filtroSucursal = $idsucursal != "Todos" ? "AND v.idsucursal = '$idsucursal'" : "";
	    $filtroVendedor = $idvendedor != "Todos" ? "AND v.idpersonal = '$idvendedor'" : "";

	    $sql = "SELECT 
	                IFNULL(
	                    (SELECT SUM(vp.monto) AS total_venta 
				         FROM venta v 
				         INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
	               		 INNER JOIN venta_pago vp ON v.idventa = vp.idventa 
				         WHERE DATE(v.fecha_hora) >= '$fecha_inicio' 
				           AND DATE(v.fecha_hora) <= '$fecha_fin' 
				           AND vp.metodo_pago != 'Efectivo'
	                       AND v.tipo_comprobante = 'Boleta'
	                       AND v.ventacredito = 'no'
	                       AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
	                       $filtroSucursal
	                       $filtroVendedor
	                    ), 0
	                ) AS total_venta";

	    return ejecutarConsultaSimpleFila($sql);
	}


	public function mostrarTotalFacturasCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{
	    // Filtros dinámicos
	    $filtroSucursal = $idsucursal != "Todos" ? "AND v.idsucursal = '$idsucursal'" : "";
	    $filtroVendedor = $idvendedor != "Todos" ? "AND v.idpersonal = '$idvendedor'" : "";

	    $sql = "SELECT 
	                IFNULL(
	                    SUM(
	                        CASE 
	                            WHEN v.ventacredito = 'no' THEN IFNULL(vp.monto,0)
	                            WHEN v.ventacredito = 'si' THEN IFNULL(v.montoPagado,0)
	                            ELSE 0
	                        END
	                    ), 0
	                ) AS total_venta
	            FROM venta v
	            INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
	            LEFT JOIN venta_pago vp ON v.idventa = vp.idventa AND vp.metodo_pago = 'Efectivo'
	            WHERE DATE(v.fecha_hora) >= '$fecha_inicio'
	              AND DATE(v.fecha_hora) <= '$fecha_fin'
	              AND v.tipo_comprobante = 'Factura'
	              AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
	              $filtroSucursal
	              $filtroVendedor";

	    return ejecutarConsultaSimpleFila($sql);
	}


	public function mostrarTotalFacturasTCaja($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor)
	{

		// Filtros dinámicos
	    $filtroSucursal = $idsucursal != "Todos" ? "AND v.idsucursal = '$idsucursal'" : "";
	    $filtroVendedor = $idvendedor != "Todos" ? "AND v.idpersonal = '$idvendedor'" : "";

	    $sql = "SELECT 
	                IFNULL(
	                    (SELECT SUM(vp.monto) AS total_venta 
				         FROM venta v 
				         INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
	               		 INNER JOIN venta_pago vp ON v.idventa = vp.idventa 
				         WHERE DATE(v.fecha_hora) >= '$fecha_inicio' 
				           AND DATE(v.fecha_hora) <= '$fecha_fin' 
				           AND vp.metodo_pago != 'Efectivo'
	                       AND v.tipo_comprobante = 'Factura'
	                       AND v.ventacredito = 'no'
	                       AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
	                       $filtroSucursal
	                       $filtroVendedor
	                    ), 0
	                ) AS total_venta";

	    return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarTotalNotasVentaCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor) {
    
    // Filtro base
    $filtroSucursal = $idsucursal != "Todos" ? "AND v.idsucursal = '$idsucursal'" : "";
    $filtroVendedor = $idvendedor != "Todos" ? "AND v.idpersonal = '$idvendedor'" : "";

    $sql = "SELECT 
                IFNULL(
                    SUM(
                        CASE 
                            WHEN v.ventacredito = 'no' THEN IFNULL(vp.monto,0)
                            WHEN v.ventacredito = 'si' THEN IFNULL(v.montoPagado,0)
                            ELSE 0
                        END
                    ), 0
                ) AS total_venta
            FROM venta v
            INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
            LEFT JOIN venta_pago vp ON v.idventa = vp.idventa AND vp.metodo_pago = 'Efectivo'
            WHERE DATE(v.fecha_hora) >= '$fecha_inicio'
              AND DATE(v.fecha_hora) <= '$fecha_fin'
              AND v.tipo_comprobante = 'Nota de Venta'
              AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
              $filtroSucursal
              $filtroVendedor";

    return ejecutarConsultaSimpleFila($sql);
}


	public function mostrarTotalNotasVetnaTCaja($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {
        $sql = "SELECT 
			    IFNULL(
			        (SELECT SUM(vp.monto) AS total_venta 
			         FROM venta v 
			         INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
               INNER JOIN venta_pago vp ON v.idventa = vp.idventa 
			         WHERE DATE(v.fecha_hora) >= '$fecha_inicio' 
			           AND DATE(v.fecha_hora) <= '$fecha_fin' 
			           AND vp.metodo_pago != 'Efectivo' 
			           AND v.tipo_comprobante = 'Nota de Venta' 
			           AND v.ventacredito = 'no' 
			           AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')), 0
			    ) AS total_venta";
    } 
    // Solo filtrando por sucursal
    else if ($idsucursal != "Todos" && $idvendedor == "Todos") {
        $sql = "SELECT 
			    IFNULL(
			        (SELECT SUM(vp.monto) AS total_venta 
			         FROM venta v 
			         INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
               INNER JOIN venta_pago vp ON v.idventa = vp.idventa 
			         WHERE DATE(v.fecha_hora) >= '$fecha_inicio' 
			           AND DATE(v.fecha_hora) <= '$fecha_fin' 
			           AND vp.metodo_pago != 'Efectivo' 
			           AND v.tipo_comprobante = 'Nota de Venta' 
			           AND v.ventacredito = 'no' 
			           AND v.idsucursal = '$idsucursal' 
			           AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
			        ), 0
			    ) AS total_venta";
    } 
    // Solo filtrando por vendedor
    else if ($idsucursal == "Todos" && $idvendedor != "Todos") {
        $sql = "SELECT 
			    IFNULL(
			        (SELECT SUM(vp.monto) AS total_venta 
			         FROM venta v 
			         INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
               INNER JOIN venta_pago vp ON v.idventa = vp.idventa 
			         WHERE DATE(v.fecha_hora) >= '$fecha_inicio' 
			           AND DATE(v.fecha_hora) <= '$fecha_fin' 
			           AND vp.metodo_pago != 'Efectivo' 
			           AND v.tipo_comprobante = 'Nota de Venta' 
			           AND v.ventacredito = 'no'  
			           AND v.idpersonal = '$idvendedor' 
			           AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
			        ), 0
			    ) AS total_venta";
    } 
    // Filtrando por sucursal y vendedor
    else {
        $sql = "SELECT 
			    IFNULL(
			        (SELECT SUM(vp.monto) AS total_venta 
			         FROM venta v 
			         INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
               INNER JOIN venta_pago vp ON v.idventa = vp.idventa 
			         WHERE DATE(v.fecha_hora) >= '$fecha_inicio' 
			           AND DATE(v.fecha_hora) <= '$fecha_fin' 
			           AND vp.metodo_pago != 'Efectivo' 
			           AND v.tipo_comprobante = 'Nota de Venta' 
			           AND v.ventacredito = 'no'  
			           AND v.idsucursal = '$idsucursal' 
			           AND v.idpersonal = '$idvendedor' 
			           AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')
			        ), 0
			    ) AS total_venta";
    }

    return ejecutarConsultaSimpleFila($sql);
}

	public function mostrarTotalCuentasCobrarVentaCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (SELECT sum(dcc.montopagado) as total_venta FROM detalle_cuentas_por_cobrar dcc 
				INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc 
				INNER JOIN venta v ON v.idventa = cc.idventa
				INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
				WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin'  ), 0) as total_venta";
		} else if($idsucursal != "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (SELECT sum(dcc.montopagado) as total_venta FROM detalle_cuentas_por_cobrar dcc 
				INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc 
				INNER JOIN venta v ON v.idventa = cc.idventa
				INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
				WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin'  AND v.idsucursal='$idsucursal'), 0) as total_venta";
		} else if($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql = "SELECT IFNULL( (SELECT sum(dcc.montopagado) as total_venta FROM detalle_cuentas_por_cobrar dcc 
				INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc 
				INNER JOIN venta v ON v.idventa = cc.idventa
				INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
				WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin'  AND v.idPersonal='$idvendedor'), 0) as total_venta";
		} else{
			$sql = "SELECT IFNULL( (SELECT sum(dcc.montopagado) as total_venta FROM detalle_cuentas_por_cobrar dcc 
				INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc 
				INNER JOIN venta v ON v.idventa = cc.idventa
				INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
				WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin'  AND v.idPersonal='$idvendedor' AND v.idsucursal='$idsucursal'), 0) as total_venta";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarTotalCuentasCobrarVentaTCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (SELECT sum(dcc.montotarjeta) as total_venta FROM detalle_cuentas_por_cobrar dcc 
				INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc 
				INNER JOIN venta v ON v.idventa = cc.idventa
				INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
				WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' ), 0) as total_venta";
		} else if($idsucursal != "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (SELECT sum(dcc.montotarjeta) as total_venta FROM detalle_cuentas_por_cobrar dcc 
				INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc 
				INNER JOIN venta v ON v.idventa = cc.idventa
				INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
				WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND v.idsucursal='$idsucursal'), 0) as total_venta";
		} else if($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql = "SELECT IFNULL( (SELECT sum(dcc.montotarjeta) as total_venta FROM detalle_cuentas_por_cobrar dcc 
				INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc 
				INNER JOIN venta v ON v.idventa = cc.idventa
				INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
				WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND v.idPersonal='$idvendedor'), 0) as total_venta";
		} else{
			$sql = "SELECT IFNULL( (SELECT sum(dcc.montotarjeta) as total_venta FROM detalle_cuentas_por_cobrar dcc 
				INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc 
				INNER JOIN venta v ON v.idventa = cc.idventa
				INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
				WHERE DATE(dcc.fechapago)>='$fecha_inicio' AND DATE(dcc.fechapago)<='$fecha_fin' AND dcc.formapago != 'Efectivo' AND v.idPersonal='$idvendedor' AND v.idsucursal='$idsucursal'), 0) as total_venta";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalEfectivo($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{
	    // Filtros dinámicos
	    $filtroSucursal = $idsucursal !== "Todos" ? "AND v.idsucursal='$idsucursal'" : "";
	    $filtroVendedor = $idvendedor !== "Todos" ? "AND v.idPersonal='$idvendedor'" : "";
	    $filtroEstado = "AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')";

	    // Tipos de comprobante
	    $tiposComprobante = ["Boleta", "Factura", "Nota de Venta"];
	    $subConsultas = [];

	    foreach ($tiposComprobante as $tipo) {
	        $subConsultas[] = "(
	            SELECT IFNULL(SUM(
	                CASE 
	                    WHEN v.ventacredito='no' THEN IFNULL(vp.monto,0)
	                    WHEN v.ventacredito='si' THEN IFNULL(v.montoPagado,0)
	                    ELSE 0
	                END
	            ),0)
	            FROM venta v
	            LEFT JOIN venta_pago vp ON v.idventa = vp.idventa AND vp.metodo_pago='Efectivo'
	            INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
	            WHERE DATE(v.fecha_hora) >= '$fecha_inicio'
	              AND DATE(v.fecha_hora) <= '$fecha_fin'
	              AND v.tipo_comprobante='$tipo'
	              $filtroEstado
	              $filtroSucursal
	              $filtroVendedor
	        )";
	    }

	    // Subconsulta para cuentas por cobrar
	    $subCuentas = "(
	        SELECT IFNULL(SUM(dcc.montopagado),0)
	        FROM detalle_cuentas_por_cobrar dcc
	        INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc
	        INNER JOIN venta v ON v.idventa = cc.idventa
	        INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
	        WHERE DATE(dcc.fechapago) >= '$fecha_inicio'
	          AND DATE(dcc.fechapago) <= '$fecha_fin'
	          $filtroSucursal
	          $filtroVendedor
	    )";

	    // Construir consulta final sumando todas las subconsultas
	    $sql = "SELECT (" . implode(" + ", $subConsultas) . " + $subCuentas) AS total_venta";

	    return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarTotalTransferencia($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{
	    // Inicializar condiciones adicionales
	    $condSucursal = ($idsucursal != "Todos") ? " AND v.idsucursal='$idsucursal'" : "";
	    $condVendedor = ($idvendedor != "Todos") ? " AND v.idPersonal='$idvendedor'" : "";

	    // Condición base para ventas
	    $condBase = "DATE(v.fecha_hora) >= '$fecha_inicio' AND DATE(v.fecha_hora) <= '$fecha_fin' 
	                 AND vp.metodo_pago != 'Efectivo' 
	                 AND v.ventacredito = 'no' 
	                 AND v.estado IN ('Aceptado', 'Por Enviar', 'Activado')";

	    // Condición base para pagos con tarjeta
	    $condTarjeta = "DATE(dcc.fechapago) >= '$fecha_inicio' AND DATE(dcc.fechapago) <= '$fecha_fin' 
	                    AND dcc.formapago != 'Efectivo'";

	    // Construir SQL unificado
	    $sql = "
	        SELECT (
	            (SELECT IFNULL(SUM(vp.monto),0) 
		         FROM venta v
		         INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
		         INNER JOIN venta_pago vp ON v.idventa = vp.idventa
	             WHERE $condBase AND v.tipo_comprobante = 'Boleta' $condSucursal $condVendedor) +
	            
	            (SELECT IFNULL(SUM(vp.monto),0) 
		         FROM venta v
		         INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
		         INNER JOIN venta_pago vp ON v.idventa = vp.idventa
	             WHERE $condBase AND v.tipo_comprobante = 'Factura' $condSucursal $condVendedor) +
	            
	            (SELECT IFNULL(SUM(vp.monto),0) 
		         FROM venta v
		         INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
		         INNER JOIN venta_pago vp ON v.idventa = vp.idventa
	             WHERE $condBase AND v.tipo_comprobante = 'Nota de Venta' $condSucursal $condVendedor) +
	            
	            (SELECT IFNULL(SUM(dcc.montotarjeta), 0) 
	             FROM detalle_cuentas_por_cobrar dcc 
	             INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc
	             INNER JOIN venta v ON v.idventa = cc.idventa
	             INNER JOIN personal pe ON pe.idpersonal = v.idPersonal
	             WHERE $condTarjeta $condSucursal $condVendedor)
	        ) AS total_venta
	    ";

	    return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarTotalIngresos($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor)
	{
		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (SELECT sum(monto) as totalIngresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Ingresos'), 0) as totalIngresos";
		} else if ($idsucursal != "Todos" && $idvendedor == "Todos"){

			$sql = "SELECT IFNULL( (SELECT sum(monto) as totalIngresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Ingresos' AND idsucursal='$idsucursal'), 0) as totalIngresos";

		} else if($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql = "SELECT IFNULL( (SELECT sum(monto) as totalIngresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Ingresos' AND idpersonal='$idvendedor'), 0) as totalIngresos";
		} else {
			$sql = "SELECT IFNULL( (SELECT sum(monto) as totalIngresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Ingresos' AND idpersonal='$idvendedor' AND idsucursal='$idsucursal'), 0) as totalIngresos";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarTotalEgresos($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{

		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (SELECT sum(monto) as totalEgresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Egresos'), 0) as totalEgresos";
		} else if ($idsucursal != "Todos" && $idvendedor == "Todos"){

			$sql = "SELECT IFNULL( (SELECT sum(monto) as totalEgresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Egresos' AND idsucursal='$idsucursal'), 0) as totalEgresos";

		} else if($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql = "SELECT IFNULL( (SELECT sum(monto) as totalEgresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Egresos' AND idpersonal='$idvendedor'), 0) as totalEgresos";
		} else {
			$sql = "SELECT IFNULL( (SELECT sum(monto) as totalEgresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Egresos' AND idpersonal='$idvendedor' AND idsucursal='$idsucursal'), 0) as totalEgresos";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarTotalIngresosTar($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor)
	{
		if ($idsucursal == "Todos" && $idvendedor == "Todos") {

			$sql = "SELECT IFNULL( (SELECT sum(totaldeposito) as totalIngresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Ingresos'), 0) as totalIngresos";
		} else if ($idsucursal != "Todos" && $idvendedor == "Todos"){

			$sql = "SELECT IFNULL( (SELECT sum(totaldeposito) as totalIngresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Ingresos' AND idsucursal='$idsucursal'), 0) as totalIngresos";

		} else if($idsucursal == "Todos" && $idvendedor != "Todos"){
			$sql = "SELECT IFNULL( (SELECT sum(totaldeposito) as totalIngresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Ingresos' AND idpersonal='$idvendedor'), 0) as totalIngresos";
		} else {
			$sql = "SELECT IFNULL( (SELECT sum(totaldeposito) as totalIngresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Ingresos' AND idpersonal='$idvendedor' AND idsucursal='$idsucursal'), 0) as totalIngresos";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalFacturas($fecha_inicio, $fecha_fin, $idsucursal)
	{

		if ($idsucursal == "Todos" || $idsucursal == null) {

			$sql = "SELECT IFNULL(count(idventa),0) as totalcuentaventa FROM venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Factura' AND estado IN ('Aceptado', 'Por Enviar', 'Activado')";
		} else {

			$sql = "SELECT IFNULL(count(idventa),0) as totalcuentaventa FROM venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Factura' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND idsucursal='$idsucursal'";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalBoletas($fecha_inicio, $fecha_fin, $idsucursal)
	{

		if ($idsucursal == "Todos" || $idsucursal == null) {

			$sql = "SELECT IFNULL(count(idventa),0) as totalcuentaventa FROM venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Boleta' AND estado IN ('Aceptado', 'Por Enviar', 'Activado')";
		} else {

			$sql = "SELECT IFNULL(count(idventa),0) as totalcuentaventa FROM venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Boleta' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND idsucursal='$idsucursal'";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalNotas($fecha_inicio, $fecha_fin, $idsucursal)
	{

		if ($idsucursal == "Todos" || $idsucursal == null) {

			$sql = "SELECT IFNULL(count(idventa),0) as totalcuentaventa FROM venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Nota de Venta' AND estado IN ('Aceptado', 'Por Enviar', 'Activado')";
		} else {

			$sql = "SELECT IFNULL(count(idventa),0) as totalcuentaventa FROM venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND tipo_comprobante = 'Nota de Venta' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND idsucursal='$idsucursal'";
		}

		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalCuentas($fecha_inicio, $fecha_fin, $idsucursal)
	{

		if ($idsucursal == "Todos" || $idsucursal == null) {

			$sql = "SELECT IFNULL(count(idcpc),0) as totalcuentacobrar FROM cuentas_por_cobrar WHERE condicion = 1 AND DATE(fecharegistro)>='$fecha_inicio' AND DATE(fecharegistro)<='$fecha_fin'";
		} else {

			$sql = "SELECT IFNULL(count(idcpc),0) as totalcuentacobrar FROM cuentas_por_cobrar WHERE condicion = 1 AND DATE(fecharegistro)>='$fecha_inicio' AND DATE(fecharegistro)<='$fecha_fin'";
		}


		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalT($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor)
	{
	    // Filtros dinámicos
	    $filtroSucursal = $idsucursal !== "Todos" ? "AND v.idsucursal='$idsucursal'" : "";
	    $filtroVendedor = $idvendedor !== "Todos" ? "AND v.idPersonal='$idvendedor'" : "";
	    $filtroEstado = "AND v.estado IN ('Aceptado','Por Enviar','Activado')";

	    // Tipos de comprobante
	    $tiposComprobante = ["Boleta","Factura","Nota de Venta"];
	    $subConsultas = [];

	    foreach ($tiposComprobante as $tipo) {
	        // Ventas en efectivo y anticipos de crédito
	        $subConsultas[] = "(
	            SELECT IFNULL(SUM(
	                CASE
	                    WHEN v.ventacredito='no' AND v.formapago='Efectivo' THEN IFNULL(v.total_venta - v.descuento,0)
	                    WHEN v.ventacredito='si' THEN IFNULL(v.montoPagado,0)
	                    ELSE 0
	                END
	            ),0)
	            FROM venta v
	            WHERE DATE(v.fecha_hora) >= '$fecha_inicio'
	              AND DATE(v.fecha_hora) <= '$fecha_fin'
	              AND v.tipo_comprobante='$tipo'
	              $filtroEstado
	              $filtroSucursal
	              $filtroVendedor
	        )";

	        // Ventas que no son en efectivo
	        $subConsultas[] = "(
	            SELECT IFNULL(SUM(v.total_venta - IFNULL(v.descuento,0)),0)
	            FROM venta v
	            WHERE DATE(v.fecha_hora) >= '$fecha_inicio'
	              AND DATE(v.fecha_hora) <= '$fecha_fin'
	              AND v.tipo_comprobante='$tipo'
	              AND v.formapago != 'Efectivo'
	              $filtroEstado
	              $filtroSucursal
	              $filtroVendedor
	        )";
	    }

	    // Subconsulta cuentas por cobrar
	    $subCuentasEfectivo = "(
	        SELECT IFNULL(SUM(dcc.montopagado),0)
	        FROM detalle_cuentas_por_cobrar dcc
	        INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc
	        INNER JOIN venta v ON v.idventa = cc.idventa
	        WHERE DATE(dcc.fechapago) >= '$fecha_inicio'
	          AND DATE(dcc.fechapago) <= '$fecha_fin'
	          $filtroSucursal
	          $filtroVendedor
	    )";

	    $subCuentasNoEfectivo = "(
	        SELECT IFNULL(SUM(dcc.montotarjeta),0)
	        FROM detalle_cuentas_por_cobrar dcc
	        INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc
	        INNER JOIN venta v ON v.idventa = cc.idventa
	        WHERE DATE(dcc.fechapago) >= '$fecha_inicio'
	          AND DATE(dcc.fechapago) <= '$fecha_fin'
	          AND dcc.formapago != 'Efectivo'
	          $filtroSucursal
	          $filtroVendedor
	    )";

	    // Construir consulta final sumando todo
	    $sql = "SELECT (" . implode(" + ", $subConsultas) . " + $subCuentasEfectivo + $subCuentasNoEfectivo) AS totalI";

	    return ejecutarConsultaSimpleFila($sql);
	}

	public function totalEC($fecha_inicio, $fecha_fin, $idsucursal)
	{

		if ($idsucursal == "Todos" || $idsucursal == null) {

			$sql = "SELECT (IFNULL( (SELECT sum(monto) as totalIngresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Ingresos'), 0) + 
			((select ifnull(sum(total_venta-descuento),0) from venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago = 'Efectivo' AND tipo_comprobante = 'Boleta' AND ventacredito= 'no' AND estado IN ('Aceptado', 'Por Enviar', 'Activado')) + 
			(select ifnull(sum(total_venta-descuento),0) from venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago = 'Efectivo' AND tipo_comprobante = 'Factura' AND ventacredito= 'no' AND estado IN ('Aceptado', 'Por Enviar', 'Activado')) +
		   (select ifnull(sum(total_venta-descuento),0) from venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago = 'Efectivo' AND tipo_comprobante = 'Nota de Venta' AND ventacredito= 'no' AND estado IN ('Aceptado', 'Por Enviar', 'Activado')) +
		   (select ifnull(sum(dcc.montopagado),0) from detalle_cuentas_por_cobrar dcc INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc WHERE DATE(fechapago)>='$fecha_inicio' AND DATE(fechapago)<='$fecha_fin' AND formapago = 'Efectivo' AND cc.condicion = 0)
		   ) + ((select ifnull(sum(total_venta-descuento),0) from venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Boleta' AND ventacredito= 'no' AND estado IN ('Aceptado', 'Por Enviar', 'Activado')) + 
        (select ifnull(sum(total_venta-descuento),0) from venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Factura' AND ventacredito= 'no' AND estado IN ('Aceptado', 'Por Enviar', 'Activado')) +
       (select ifnull(sum(total_venta-descuento),0) from venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Nota de Venta' AND ventacredito= 'no' AND estado IN ('Aceptado', 'Por Enviar', 'Activado')) +
       (select ifnull(sum(montopagado),0) from detalle_cuentas_por_cobrar WHERE DATE(fechapago)>='$fecha_inicio' AND DATE(fechapago)<='$fecha_fin' AND formapago != 'Efectivo')
       )) - IFNULL( (SELECT sum(monto) as totalIngresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Egresos'), 0) AS totalEC";
		} else {

			$sql = "SELECT (IFNULL( (SELECT sum(monto) as totalIngresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Ingresos'), 0) + ((select ifnull(sum(total_venta),0) from venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago = 'Efectivo' AND tipo_comprobante = 'Boleta' AND ventacredito= 'no' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND idsucursal='$idsucursal') + 
			(select ifnull(sum(total_venta-descuento),0) from venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago = 'Efectivo' AND tipo_comprobante = 'Factura' AND ventacredito= 'no' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND idsucursal='$idsucursal') +
		   (select ifnull(sum(total_venta-descuento),0) from venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago = 'Efectivo' AND tipo_comprobante = 'Nota de Venta' AND ventacredito= 'no' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND idsucursal='$idsucursal') +
		   (select ifnull(sum(dcc.montopagado),0) from detalle_cuentas_por_cobrar dcc INNER JOIN cuentas_por_cobrar cc ON cc.idcpc = dcc.idcpc WHERE DATE(fechapago)>='$fecha_inicio' AND DATE(fechapago)<='$fecha_fin' AND formapago = 'Efectivo' AND cc.condicion = 0)
		   ) + ((select ifnull(sum(total_venta),0) from venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Boleta' AND ventacredito= 'no' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND idsucursal='$idsucursal') + 
        (select ifnull(sum(total_venta-descuento),0) from venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Factura' AND ventacredito= 'no' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND idsucursal='$idsucursal') +
       (select ifnull(sum(total_venta-descuento),0) from venta WHERE DATE(fecha_hora)>='$fecha_inicio' AND DATE(fecha_hora)<='$fecha_fin' AND formapago != 'Efectivo' AND tipo_comprobante = 'Nota de Venta' AND ventacredito= 'no' AND estado IN ('Aceptado', 'Por Enviar', 'Activado') AND idsucursal='$idsucursal') +
       (select ifnull(sum(montopagado),0) from detalle_cuentas_por_cobrar WHERE DATE(fechapago)>='$fecha_inicio' AND DATE(fechapago)<='$fecha_fin' AND formapago != 'Efectivo')
       )) - IFNULL( (SELECT sum(monto) as totalIngresos FROM movimiento WHERE DATE(fecha)>='$fecha_inicio' AND DATE(fecha)<='$fecha_fin' AND tipo = 'Egresos'), 0) AS totalEC";
		}


		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalStocksBajos()
	{

		$sql = "SELECT IFNULL(count(p.idproducto),0) as totalstocksbajos 
		FROM producto p
		INNER JOIN categoria c
		ON c.idcategoria = p.idcategoria
		WHERE stock>=0 AND stock<='5' AND c.nombre != 'SERVICIO'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalCreditoPendientes()
	{

		$sql = "SELECT IFNULL(count(idcpc),0) as totalcreditospendientes FROM cuentas_por_cobrar WHERE deudatotal != abonototal AND condicion=1";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalDocumentosPendientes()
	{

		$sql = "SELECT IFNULL(count(idventa),0) as totaldocumentospendientes FROM venta WHERE estado = 'Por Enviar' AND tipo_comprobante!='Nota de Venta'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function totalDocumentosPendientes2($idsucursal)
	{

		$sql = "SELECT IFNULL(count(idproducto),0) as totaldocumentospendientes 
		FROM producto 
		where idsucursal = '$idsucursal'
		and fecha between curdate() 
		and date_add(curdate(), 
		interval (SELECT diasVencer from datos_negocio) day)";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarCreditosPendientes()
	{
		$sql = "SELECT cc.idcpc,DATE(cc.fecharegistro) as fecharegistro, v.tipo_comprobante, c.nombre, c.num_documento, v.serie_comprobante, v.num_comprobante, cc.deudatotal, cc.abonototal, cc.fechavencimiento 
				FROM venta v 
				INNER JOIN cuentas_por_cobrar cc
		        ON v.idventa = cc.idventa
		        INNER JOIN persona c
		        ON c.idpersona = v.idcliente
                WHERE cc.deudatotal != cc.abonototal AND condicion=1
                ORDER BY cc.idcpc desc";
		return ejecutarConsulta($sql);
	}

	public function listarDocumentosPendientes()
	{
		$sql = "SELECT v.idventa,v.tipo_comprobante, v.serie_comprobante, v.num_comprobante 
				FROM venta v 
				WHERE estado = 'Por Enviar' AND tipo_comprobante!='Nota de Venta'
                ORDER BY v.idventa desc";
		return ejecutarConsulta($sql);
	}

	public function listarDocumentosPendientes2($idsucursal)
	{
		$sql = "select p.*, um.nombre as unidadmedida, c.nombre as categoria from producto p INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida INNER JOIN categoria c ON p.idcategoria = c.idcategoria where idsucursal = '$idsucursal' and fecha between curdate() and date_add(curdate(), interval (SELECT diasVencer from datos_negocio) day)";
		return ejecutarConsulta($sql);
	}
}
