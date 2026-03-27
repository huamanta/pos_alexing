<?php
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";

date_default_timezone_set('America/Lima');

final class Pos
{
    //Implementamos nuestro constructor
    public function __construct() {}

    public function listarVentas($idcaja, $idsucursal, $idusuario, $estado)
    {
        $sqlap = "SELECT * FROM caja_apertura WHERE idusuario = '$idusuario' AND estado = 1 AND idcaja = '$idcaja' AND fecha_cierre IS NULL";
        $apertura = ejecutarConsulta($sqlap)->fetch_object();
        $fecha_hora = $apertura->fecha_apertura;

        if ($estado == "Todos") {

            $sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,s.nombre as sucursal,date_format(v.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,
			v.idcliente,p.nombre as cliente,p.num_documento,v.estadoS,u.idpersonal,u.nombre as personal, 
			v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.ventacredito,
			v.impuesto,v.dov_Nombre,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona 
			INNER JOIN personal u ON v.idpersonal=u.idpersonal 
			INNER JOIN sucursal s
			ON s.idsucursal = v.idsucursal
			WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') 
            AND v.idcaja = '$idcaja' AND v.idsucursal = '$idsucursal' AND
			v.fecha_hora>='$fecha_hora' AND v.fecha_hora<= NOW()
			ORDER BY v.idventa DESC";
        } else if ($estado == 'Aceptado') {

            $sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,s.nombre as sucursal,date_format(v.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,
			v.idcliente,p.nombre as cliente,p.num_documento,v.estadoS,u.idpersonal,u.nombre as personal, 
			v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado 
			FROM venta v 
			INNER JOIN persona p 
			ON v.idcliente=p.idpersona 
			INNER JOIN personal u 
			ON v.idpersonal=u.idpersonal
			INNER JOIN sucursal s
			ON s.idsucursal = v.idsucursal 
			WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') 
			AND v.serie_comprobante != '-'  AND
            v.idcaja = '$idcaja' AND v.idsucursal = '$idsucursal' AND
			v.fecha_hora>='$fecha_hora' AND v.fecha_hora<= NOW()
			AND v.estado = 'Aceptado' 
			ORDER BY v.idventa DESC";
        } else if ($estado == "Por Enviar") {

            $sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,s.nombre as sucursal,date_format(v.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,
			v.idcliente,p.nombre as cliente,p.num_documento,v.estadoS,u.idpersonal,u.nombre as personal, 
			v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado 
			FROM venta v 
			INNER JOIN persona p 
			ON v.idcliente=p.idpersona 
			INNER JOIN personal u 
			ON v.idpersonal=u.idpersonal
			INNER JOIN sucursal s
			ON s.idsucursal = v.idsucursal 
			WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') 
			AND v.serie_comprobante != '-'  AND
            v.idcaja = '$idcaja' AND v.idsucursal = '$idsucursal' AND
			v.fecha_hora>='$fecha_hora' AND v.fecha_hora<= NOW() 
			AND v.estado = 'Por Enviar' 
			ORDER BY v.idventa DESC";
        } else if ($estado == "Nota Credito") {

            $sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,s.nombre as sucursal,date_format(v.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,v.idcliente,p.nombre as cliente,p.num_documento,v.estadoS,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal INNER JOIN sucursal s
			ON s.idsucursal = v.idsucursal WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') AND v.serie_comprobante != '-' AND 
            v.idcaja = '$idcaja' AND v.idsucursal = '$idsucursal' AND
			v.fecha_hora>='$fecha_hora' AND v.fecha_hora<= NOW()
            AND v.estado = 'Nota Credito' ORDER BY v.idventa DESC";
        } else {

            $sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,s.nombre as sucursal,date_format(v.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,v.idcliente,p.nombre as cliente,p.num_documento,v.estadoS,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal INNER JOIN sucursal s
			ON s.idsucursal = v.idsucursal WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') AND v.serie_comprobante != '-' AND 
            v.idcaja = '$idcaja' AND v.idsucursal = '$idsucursal' AND
			v.fecha_hora>='$fecha_hora' AND v.fecha_hora<= NOW() 
            AND v.estado = 'Rechazado' AND v.idsucursal = '$idsucursal' ORDER BY v.idventa DESC";
        }
        return ejecutarConsulta($sql);
    }

    public function listarVentas2($aperturacajaid)
    {
        $sqlap = "SELECT * FROM caja_apertura WHERE aperturacajaid = '$aperturacajaid'";
        $apertura = ejecutarConsulta($sqlap)->fetch_object();

        $fecha_apertura = $apertura->fecha_apertura;
        $fecha_cierre = $apertura->fecha_cierre;
        $idcaja = $apertura->idcaja;

        $sql = "
            SELECT 
                v.idventa,
                DATE(v.fecha_hora) as fecha,
                s.nombre as sucursal,
                DATE_FORMAT(v.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,
                v.idcliente,
                p.nombre as cliente,
                p.num_documento,
                v.estadoS,
                u.idpersonal,
                u.nombre as personal, 
                v.tipo_comprobante,
                v.serie_comprobante,
                v.num_comprobante,
                (v.total_venta - v.descuento) as total_venta,
                v.ventacredito,
                v.impuesto,
                v.dov_Nombre,
                v.estado,
                GROUP_CONCAT(CONCAT(vp.metodo_pago, ': S/. ', FORMAT(vp.monto,2)) SEPARATOR ' | ') as pagos
            FROM venta v
            INNER JOIN venta_pago vp ON v.idventa = vp.idventa 
            INNER JOIN persona p ON v.idcliente = p.idpersona 
            INNER JOIN personal u ON v.idpersonal = u.idpersonal 
            INNER JOIN sucursal s ON s.idsucursal = v.idsucursal
            WHERE v.tipo_comprobante IN ('Boleta', 'Factura', 'Nota de Venta') 
                AND v.idcaja = '$idcaja' 
                AND v.fecha_hora >= '$fecha_apertura' 
                AND v.fecha_hora <= '$fecha_cierre'
            GROUP BY v.idventa
            ORDER BY v.idventa DESC";

        return ejecutarConsulta($sql);
    }


    public function verificarCaja($idusurio, $idsucursal)
    {
        $sql = "SELECT * FROM caja_apertura WHERE idusuario = '$idusurio' AND idsucursal = '$idsucursal' AND estado = 1 AND fecha_cierre IS NULL";
        $apertura = ejecutarConsulta($sql)->fetch_object();
        return ($apertura) ? $apertura : array('idcaja' => 0);
    }

    public function listarCajas($idsucursal)
    {
        $sql = "SELECT * FROM cajas WHERE idsucursal = '$idsucursal' AND estado = 1 AND deleted_at IS NULL";
        $cajas = ejecutarConsulta($sql);
        $data = array();
        while ($reg = $cajas->fetch_object()) {
            $data[] = $reg;
        }
        return $data;
    }

    public function aperturarCaja($fecha_hora, $efectivo_apertura, $caja_apertura, $idusuario, $idsucursal)
    {
        if ($idusuario == null || $idusuario == '' || $idusuario == 0) {
            header('Location: /ingreso');
            exit();
        }
        $sql = "INSERT INTO caja_apertura (fecha_apertura, efectivo_apertura, idcaja, idusuario, idsucursal) VALUES ('$fecha_hora', '$efectivo_apertura','$caja_apertura','$idusuario','$idsucursal')";
        $update = "UPDATE cajas SET estado = 2 WHERE idcaja = '$caja_apertura'";
        ejecutarConsulta($update);
        return ejecutarConsulta($sql);
    }

    public function cerrarCaja($fecha_hora, $efectivo_cierre, $idcaja, $idusuario, $idsucursal)
    {
        $sql = "UPDATE caja_apertura SET fecha_cierre = '$fecha_hora', efectivo_cierre='$efectivo_cierre', estado='0'
        WHERE idcaja = '$idcaja' AND idusuario = '$idusuario' AND idsucursal = '$idsucursal' AND estado = '1' LIMIT 1";
        $update = "UPDATE cajas SET estado = 1 WHERE idcaja = '$idcaja'";
        ejecutarConsulta($update);
        return ejecutarConsulta($sql);
    }

  public function showResumenCaja($idcaja, $idsucursal, $idusuario)
{
    // 1. Obtener el idpersonal del usuario logueado
    $sql_personal = "SELECT idpersonal FROM usuario WHERE idusuario = '$idusuario' LIMIT 1";
    $row_personal = ejecutarConsulta($sql_personal)->fetch_object();
    $idpersonal = $row_personal ? $row_personal->idpersonal : 0;

    // 2. Obtener apertura de caja para la sucursal y usuario
    $sqlap = "SELECT ca.*, c.idsucursal
              FROM caja_apertura ca
              INNER JOIN cajas c ON ca.idcaja = c.idcaja
              WHERE ca.estado = 1
                AND c.idsucursal = '$idsucursal'
                AND ca.idusuario = '$idusuario'
                AND ca.fecha_cierre IS NULL
              LIMIT 1";
    $apertura = ejecutarConsulta($sqlap)->fetch_object();

    if (!$apertura) {
        // No hay caja abierta, devolvemos JSON seguro
        return json_encode([
            "error" => "No hay caja abierta en esta sucursal para este usuario",
            "efectivo_apertura" => 0,
            "ventas_efectivo" => 0,
            "cantidad_ventas_efectivo" => 0,
            "ventas_no_efectivo" => 0,
            "cantidad_ventas_no_efectivo" => 0,
            "ventas_credito" => 0,
            "cantidad_ventas_credito" => 0,
            "ingresos_efectivo" => 0,
            "ingresos_no_efectivo" => 0,
            "egresos_efectivo" => 0,
            "egresos_no_efectivo" => 0,
            "abonos_efectivo" => 0,
            "abonos_no_efectivo" => 0,
            "total_efectivo" => 0,
            "detalle_ventas" => []
        ]);
    }

    $efectivo_apertura = $apertura->efectivo_apertura;
    $fecha_hora = date("Y-m-d H:i:s", strtotime($apertura->fecha_apertura));
    $now = date("Y-m-d H:i:s");

    // 3. Total ventas (contado vs crédito)
    $sql_ventas = "SELECT vp.metodo_pago, v.ventacredito, 
                          SUM(vp.monto) AS total_ventas, 
                          COUNT(*) as cantidad
                   FROM venta v
                   INNER JOIN venta_pago vp ON v.idventa = vp.idventa
                   WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') 
                     AND v.idcaja = '$idcaja' 
                     AND v.idsucursal = '$idsucursal' 
                     AND v.fecha_hora >= '$fecha_hora' 
                     AND v.fecha_hora <= NOW()
                     AND v.estado NOT IN ('Nota Credito','Anulado')
                   GROUP BY vp.metodo_pago, v.ventacredito";
    $ventas_query = ejecutarConsulta($sql_ventas);

    $ventas_efectivo = 0;
    $ventas_no_efectivo = 0;
    $cantidad_ventas_efectivo = 0;
    $cantidad_ventas_no_efectivo = 0;
    $ventas_credito = 0;
    $cantidad_ventas_credito = 0;
    $detalle_ventas = [];

    while ($v = $ventas_query->fetch_object()) {
        $detalle_ventas[] = $v;
        if (strtolower($v->ventacredito) == 'si') {
            $ventas_credito += $v->total_ventas;
            $cantidad_ventas_credito += $v->cantidad;
        } else {
            if (strtolower($v->metodo_pago) == 'efectivo') {
                $ventas_efectivo += $v->total_ventas;
                $cantidad_ventas_efectivo += $v->cantidad;
            } else {
                $ventas_no_efectivo += $v->total_ventas;
                $cantidad_ventas_no_efectivo += $v->cantidad;
            }
        }
    }

    // 4. Movimientos de ingresos y egresos
    $sql_movs = "SELECT tipo, formapago, monto
                 FROM movimiento
                 WHERE idsucursal = '$idsucursal'
                   AND idcaja = '$idcaja'
                   AND fecha >= '$fecha_hora'
                   AND fecha <= '$now'";
    $movimientos_query = ejecutarConsulta($sql_movs);

    $ingresos_efectivo = 0;
    $ingresos_no_efectivo = 0;
    $egresos_efectivo = 0;
    $egresos_no_efectivo = 0;

    while ($m = $movimientos_query->fetch_object()) {
        $fp = strtolower($m->formapago);
        if ($m->tipo == 'Ingresos') {
            if ($fp == 'efectivo') $ingresos_efectivo += $m->monto;
            else $ingresos_no_efectivo += $m->monto;
        } elseif ($m->tipo == 'Egresos') {
            if ($fp == 'efectivo') $egresos_efectivo += $m->monto;
            else $egresos_no_efectivo += $m->monto;
        }
    }

    // 5. Abonos
    $sql_abonos = "SELECT SUM(montopagado) AS total_efectivo, 
                          SUM(montotarjeta) AS total_no_efectivo,
                          LOWER(TRIM(formapago)) AS formapago
                   FROM detalle_cuentas_por_cobrar
                   WHERE idcaja = '$idcaja'
                     AND idpersonal = '$idpersonal'
                     AND DATE(fechapago) BETWEEN DATE('$fecha_hora') AND DATE('$now')
                   GROUP BY iddcpc";
    $abonos_query = ejecutarConsulta($sql_abonos);

    $abonos_efectivo = 0;
    $abonos_no_efectivo = 0;
    $detalle_abonos = [];

    while ($a = $abonos_query->fetch_object()) {
        $abonos_efectivo += $a->total_efectivo;
        $abonos_no_efectivo += $a->total_no_efectivo;

        if ($a->total_efectivo > 0) $detalle_abonos[] = ["formapago" => "Efectivo", "monto" => $a->total_efectivo];
        if ($a->total_no_efectivo > 0) $detalle_abonos[] = ["formapago" => ucfirst($a->formapago), "monto" => $a->total_no_efectivo];
    }

    // 6. Calcular total efectivo esperado
    $total_efectivo = $efectivo_apertura + $ventas_efectivo + $ingresos_efectivo + $abonos_efectivo - $egresos_efectivo;

    // 7. Retornar datos como JSON limpio
    return json_encode([
        "efectivo_apertura" => $efectivo_apertura,
        "ventas_efectivo" => $ventas_efectivo,
        "cantidad_ventas_efectivo" => $cantidad_ventas_efectivo,
        "ventas_no_efectivo" => $ventas_no_efectivo,
        "cantidad_ventas_no_efectivo" => $cantidad_ventas_no_efectivo,
        "ventas_credito" => $ventas_credito,
        "cantidad_ventas_credito" => $cantidad_ventas_credito,
        "ingresos_efectivo" => $ingresos_efectivo,
        "ingresos_no_efectivo" => $ingresos_no_efectivo,
        "egresos_efectivo" => $egresos_efectivo,
        "egresos_no_efectivo" => $egresos_no_efectivo,
        "abonos_efectivo" => $abonos_efectivo,
        "abonos_no_efectivo" => $abonos_no_efectivo,
        "total_efectivo" => $total_efectivo,
        "detalle_ventas" => $detalle_ventas,
        "detalle_abonos" => $detalle_abonos
    ]);
}



    public function listarProductos($categoria)
    {
        if ($categoria == 'Todos') {
            $sql = "SELECT p.*, p.nombre as producto, um.nombre as unidad_medida, pg.*, um.* FROM producto p 
            INNER JOIN producto_configuracion pg ON p.idproducto = pg.idproducto 
            INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
            WHERE p.condicion=1  LIMIT 8";
        } else {
            $sql = "SELECT p.*, p.nombre as producto, um.nombre as unidad_medida, pg.*, um.* FROM producto p 
            INNER JOIN producto_configuracion pg ON p.idproducto = pg.idproducto 
            INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
            INNER JOIN categoria c ON c.idcategoria = p.idcategoria
            WHERE c.idcategoria = p.idcategoria AND p.idproducto = pg.idproducto AND p.idcategoria = $categoria AND p.condicion=1  LIMIT 8";
        }
        $productos = ejecutarConsulta($sql);
        $data = array();
        while ($reg = $productos->fetch_object()) {
            $data[] = $reg;
        }
        return $data;
    }


    public function searchProductos($producto, $modo = 1) // 1 = nombre, 2 = código
    {
        if ($modo === 1) {
            $sql = "SELECT p.*, p.nombre as producto, um.nombre as unidad_medida, pg.*, um.* FROM producto p 
                    INNER JOIN producto_configuracion pg ON p.idproducto = pg.idproducto 
                    INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
                    WHERE p.nombre LIKE '%$producto%' AND p.condicion='1'";
        } else {
            $sql = "SELECT p.*, p.nombre as producto, um.nombre as unidad_medida, pg.*, um.* FROM producto p 
                    INNER JOIN producto_configuracion pg ON p.idproducto = pg.idproducto 
                    INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
                    WHERE pg.codigo_extra = '$producto' AND p.condicion='1'";
        }

        $productos = ejecutarConsulta($sql);
        $data = array();
        while ($reg = $productos->fetch_object()) {
            $data[] = $reg;
        }
        return $data;
    }

    public function verPreciosItem($idproducto_configuracion, $idusuario)
{
    $idproducto_configuracion = intval($idproducto_configuracion);
    $idusuario = intval($idusuario);

    // Obtener el cargo del usuario desde personal
    $sqlCargo = "SELECT p.cargo 
                 FROM usuario u 
                 INNER JOIN personal p ON u.idpersonal = p.idpersonal 
                 WHERE u.idusuario = $idusuario 
                 LIMIT 1";
    $resCargo = ejecutarConsultaSimpleFila($sqlCargo);
    $cargo = $resCargo ? strtolower(trim($resCargo['cargo'])) : '';

    // Armar consulta base
    $sql = "SELECT 
                pcp.idnombre_p, 
                np.descripcion, 
                pcp.precio 
            FROM producto_configuracion_precios pcp
            INNER JOIN producto_configuracion pc ON pcp.producto_configuracion_id = pc.id
            INNER JOIN nombre_precios np ON np.idnombre_p = pcp.idnombre_p
            WHERE pcp.producto_configuracion_id = $idproducto_configuracion 
              AND np.estado = 1";

    // Solo restringir si el usuario NO es administrador ni vendedor
    if ($cargo !== 'administrador' && $cargo !== 'vendedor') {
        $sql .= " AND np.idnombre_p = 1";
    }

    // Ejecutar y retornar resultados
    $productos = ejecutarConsulta($sql);
    $data = array();
    while ($reg = $productos->fetch_object()) {
        $data[] = $reg;
    }    
    return $data;
}

    public function listarCategorias()
    {
        $sql = "SELECT * FROM categoria";
        $productos = ejecutarConsulta($sql);
        $data = array();
        while ($reg = $productos->fetch_object()) {
            $data[] = $reg;
        }
        return $data;
    }

 public function seleccionarProducto($idproducto, $producto, $nombre, $token, $precio, $contenedor, $cantidad_contenedor, $cantidad, $configuration, $stock_disponible, $id_fifo)
{
    $verificar = "SELECT * FROM temp_detalle_venta 
                  WHERE token = '$token' AND idproducto = '$configuration'";
    $existencia = ejecutarConsulta($verificar)->fetch_object();
    
    // 🔹 OBTENER STOCK DEL LOTE FIFO ESPECÍFICO
    $sqlStockFIFO = "SELECT cantidad_restante FROM stock_fifo WHERE idfifo = '$id_fifo'";
    $resStockFIFO = ejecutarConsulta($sqlStockFIFO)->fetch_object();
    $stockRealFIFO = $resStockFIFO ? floatval($resStockFIFO->cantidad_restante) : 0;
    
    // Cantidad ya en el carrito para este producto
    $cantidadEnCarrito = $existencia ? floatval($existencia->cantidad) : 0;
    
    // 🔹 VALIDACIÓN SEGÚN TIPO DE CONTENEDOR
    $cantidad_contenedor = floatval($cantidad_contenedor);
    $cantidad = floatval($cantidad);
    $stock_disponible = floatval($stock_disponible);
    
    if ($cantidad_contenedor > 1) {
        // Es CAJA o contenedor múltiple
        // Calcular cuántas cajas hay disponibles en el lote FIFO
        $cajasDisponibles = floor($stockRealFIFO / $cantidad_contenedor);
        
        // Validar que no se exceda el límite de cajas
        if (($cantidadEnCarrito + $cantidad) > $cajasDisponibles) {
            return json_encode([
                "status" => 0,
                "message" => "Stock insuficiente. Solo hay {$cajasDisponibles} {$contenedor}(s) disponibles en este lote"
            ]);
        }
    } else {
        // Es UNIDAD (puede ser fraccionado)
        // Validar contra stock del lote FIFO
        if (($cantidadEnCarrito + $cantidad) > $stockRealFIFO) {
            return json_encode([
                "status" => 0,
                "message" => "Stock insuficiente. Solo hay {$stockRealFIFO} unidad(es) disponibles en este lote"
            ]);
        }
    }
    
    // Recalcular precio si la cantidad es fraccionada
    $precioFinal = floatval($precio);
    if ($cantidad > 0 && $cantidad < 1) {
        $precioFinal = floatval($precio) * $cantidad;
    }
    
    // Limpiar el nombre del producto
    $nombre_limpio = html_entity_decode(stripslashes($nombre), ENT_QUOTES, 'UTF-8');
    
    // Agregar o actualizar en carrito
    if ($existencia) {
        $nueva_cantidad = $cantidadEnCarrito + $cantidad;
        $sql = "UPDATE temp_detalle_venta 
                SET cantidad = '$nueva_cantidad', 
                    precio = '$precioFinal',
                    id_fifo = '$id_fifo'
                WHERE token = '$token' AND idproducto = '$configuration'";
        $res = ejecutarConsulta($sql);
    } else {
        $sql = "INSERT INTO temp_detalle_venta 
                (token, idproducto, producto, nombre, contenedor, cantidad_contenedor, cantidad, precio, id_fifo) 
                VALUES (
                    '$token', 
                    '$configuration', 
                    '$idproducto', 
                    '$nombre_limpio', 
                    '$contenedor', 
                    '$cantidad_contenedor', 
                    '$cantidad', 
                    '$precioFinal', 
                    '$id_fifo'
                )";
        $res = ejecutarConsulta($sql);
    }
    
    return json_encode([
        "status" => $res ? 1 : 0,
        "message" => $res ? "Producto agregado" : "Error al agregar producto"
    ]);
}

    public function listarCarrito($token)
    {
        $sql = "SELECT 
                    tdc.idproducto, 
                    tdc.cantidad, 
                    tdc.nombre as producto, 
                    pg.contenedor, 
                    tdc.precio, 
                    tdc.id_fifo, /* <-- Added id_fifo */
                    p.comisionV ,
                    p.imagen, 
                    um.nombre as unidad_medida,
                    sf.cantidad_restante AS stock_lote_fifo /* <-- Added stock_lote_fifo */
                FROM temp_detalle_venta tdc
                INNER JOIN producto_configuracion pg ON pg.id = tdc.idproducto 
                INNER JOIN producto p ON p.idproducto = tdc.producto /* tdc.producto stores p.idproducto_real */
                INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida 
                LEFT JOIN stock_fifo sf ON sf.idfifo = tdc.id_fifo /* <-- Joined stock_fifo */
                WHERE tdc.token = '$token'";
        $productos = ejecutarConsulta($sql);
        $data = array();
        while ($reg = $productos->fetch_object()) {
            $data[] = $reg;
        }
        return $data;
    }

    public function eliminarProductoCarrito($idproducto, $token)
    {
        $sql = "DELETE FROM temp_detalle_venta WHERE token = '$token' AND idproducto = '$idproducto'";
        return ejecutarConsulta($sql);
    }

    public function sumarProductoCarrito($idproducto, $token)
    {

        $verificar = "SELECT * FROM temp_detalle_venta WHERE token = '$token' AND idproducto = $idproducto";
        $cantidadProducto = ejecutarConsulta($verificar);
        $data = $cantidadProducto->fetch_object();
        $producto = $data->producto;
        $verificarStock = "SELECT * FROM producto WHERE idproducto = $producto";
        $stockproducto = ejecutarConsulta($verificarStock);
        $data1 = $stockproducto->fetch_object();
        if (($data1->stock) > $data->cantidad) {
            if ($data->cantidad < 1000) {
                $cantidad = $data->cantidad + 1;
            } else {
                $cantidad = $data->cantidad;
            }
            $sql = "UPDATE temp_detalle_venta SET  cantidad = '$cantidad' WHERE token='$token'AND idproducto='$idproducto'";
            return ejecutarConsulta($sql);
        } else {
            return false;
        }
    }

    public function keyUpProductoCarrito($idproducto, $token, $cantidad)
    {

        $verificar = "SELECT * FROM temp_detalle_venta WHERE token = '$token' AND idproducto = $idproducto";
        $cantidadProducto = ejecutarConsulta($verificar);
        $data = $cantidadProducto->fetch_object();
        $producto = $data->producto;
        $verificarStock = "SELECT * FROM producto WHERE idproducto = $producto";
        $stockproducto = ejecutarConsulta($verificarStock);
        $data1 = $stockproducto->fetch_object();
        if (($data1->stock) >= $cantidad) {
            $sql = "UPDATE temp_detalle_venta SET  cantidad = '$cantidad' WHERE token='$token'AND idproducto='$idproducto'";
            return ejecutarConsulta($sql);
        } else {
            return false;
        }
    }

    public function restarProductoCarrito($idproducto, $token)
    {
        $verificar = "SELECT * FROM temp_detalle_venta WHERE token = '$token' AND idproducto = '$idproducto'";
        $existencia = ejecutarConsulta($verificar);
        $data = $existencia->fetch_object();
        if ($data->cantidad > 1) {
            $cantidad = $data->cantidad - 1;
        } else {
            $cantidad = $data->cantidad;
        }
        $sql = "UPDATE temp_detalle_venta SET  cantidad = '$cantidad' WHERE token='$token'AND idproducto='$idproducto'";
        return ejecutarConsulta($sql);
    }

    public function procesarVenta($tipo_comprobante, $serie_comprobante, $num_comprobante, $idcliente, $idpersonal,
    $idsucursal, $idcaja, $input_total_venta, $pagado_total, $totalrecibido, $totaldeposito, $tipopago, $total_comision,
     $token, $pagado, $idmotivo, $observaciones, $fecha_hora, $vuelto, $totalDescuento, $nombre, $metodos, $nroOperacionArr, 
    $bancoArr, $fechaDepositoArr) {
    if (empty($idpersonal)) {
        header('Location: /ingreso');
        exit();
    }

    $fechaActual = date('Y-m-d H:i:s');
    $impuesto = $input_total_venta * 0.18;
    if ($idcliente == "") $idcliente = 6; // cliente genérico

    $estado = ($tipo_comprobante == "Nota de Venta") ? "Activado" : "Por Enviar";
    $dovEstado = ($tipo_comprobante == "Nota de Venta") ? "ACEPTADO" : "";

    if ($serie_comprobante == "-" && $num_comprobante == "-") {
        $tipo_comprobante = "Anular";
    }
    ejecutarConsulta("BEGIN");

    try {
        // Determinar forma de pago real
        $formapago_real = $tipopago;
        if (!empty($pagado) && !empty($metodos)) {
            $pagos_activos = [];
            foreach ($pagado as $index => $monto) {
                if (floatval($monto) > 0) {
                    $pagos_activos[] = $metodos[$index] ?? 'Efectivo';
                }
            }
            if (count($pagos_activos) > 1) $formapago_real = 'Mixto';
            elseif (count($pagos_activos) == 1) $formapago_real = $metodos[0];
        }

        // Insert venta
$idmotivo_sql = (isset($idmotivo) && trim((string)$idmotivo) !== '') ? (int)$idmotivo : "NULL";

$sqlVenta = "INSERT INTO venta 
    (idsucursal, idcaja, idcliente, idpersonal, idmotivo_nota, tipo_comprobante, serie_comprobante, num_comprobante,
     fecha_hora, impuesto, total_venta, ventacredito, formapago, descuento, totalrecibido, totaldeposito,
     comisionV, vuelto, montoPagado, estado, dov_Estado, observacion, fecha_kardex)
    VALUES (
        '$idsucursal','$idcaja','$idcliente','$idpersonal', $idmotivo_sql,'$tipo_comprobante','$serie_comprobante','$num_comprobante',
        '$fecha_hora','$impuesto','$input_total_venta','No','$formapago_real','$totalDescuento','$totalrecibido','$totaldeposito',
        '$total_comision','$vuelto','$pagado_total','$estado','$dovEstado','$observaciones','$fechaActual'
    )";
        $idventanew = ejecutarConsulta_retornarID($sqlVenta);

        // Insertar pagos
        if ($idventanew && !empty($pagado)) {
            foreach ($pagado as $i => $montoRaw) {
                $monto = floatval($montoRaw);
                if ($monto <= 0) continue;
                $metodo = $metodos[$i] ?? 'Efectivo';
                $nroOp = $nroOperacionArr[$i] ?? null;
                $fechaDep = $fechaDepositoArr[$i] ?? null;
                $bancoPago = $bancoArr[$i] ?? null;

                $sqlPago = "INSERT INTO venta_pago (idventa, metodo_pago, monto, nroOperacion, fechaDeposito, banco)
                            VALUES (
                                '$idventanew', 
                                '$metodo',
                                '$monto',
                                " . ($nroOp ? "'$nroOp'" : "NULL") . ",
                                " . ($fechaDep ? "'$fechaDep'" : "NULL") . ",
                                " . ($bancoPago ? "'$bancoPago'" : "NULL") . "
                            )";
                ejecutarConsulta($sqlPago);
            }
        }

        // Procesar productos con lógica FIFO
        $detalle = ejecutarConsulta("SELECT * FROM temp_detalle_venta WHERE token = '$token'");
        while ($reg = $detalle->fetch_object()) {
            
            $id_producto_config = $reg->idproducto; // ID de la configuración del producto (ej. caja de 12)
            $id_producto_real = $reg->producto;   // ID del producto base (ej. botella individual)
            $cantidad_solicitada = $reg->cantidad; // Cantidad de contenedores (ej. 2 cajas)
            $factor_contenedor = $reg->cantidad_contenedor; // Unidades por contenedor (ej. 12)
            
            $cantidad_total_unidades = $cantidad_solicitada * $factor_contenedor;
            $cantidad_restante_a_vender = $cantidad_total_unidades;
            
            // Buscar lotes FIFO disponibles para el producto real
            $sql_fifo = "SELECT idfifo, cantidad_restante, precio_venta
                         FROM stock_fifo
                         WHERE idproducto = '$id_producto_real' 
                           AND idsucursal = '$idsucursal'
                           AND cantidad_restante > 0
                           AND estado = 1
                         ORDER BY fecha_ingreso ASC"; // Lógica FIFO
            
            $lotes_disponibles = ejecutarConsulta($sql_fifo);
            
            if (!$lotes_disponibles) {
                throw new Exception("Error al consultar lotes para el producto: $reg->nombre");
            }
            
            $stock_global_descontado = 0;

            // Iterar y descontar de cada lote
            while ($lote = $lotes_disponibles->fetch_object()) {
                if ($cantidad_restante_a_vender <= 0) break;

                $cantidad_disponible_lote = floatval($lote->cantidad_restante);
                $id_lote_actual = $lote->idfifo;
                $precio_venta_lote = $lote->precio_venta;

                $cantidad_a_tomar = min($cantidad_restante_a_vender, $cantidad_disponible_lote);

                // Actualizar la cantidad restante en el lote FIFO
                $sql_update_fifo = "UPDATE stock_fifo 
                                    SET cantidad_restante = cantidad_restante - '$cantidad_a_tomar' 
                                    WHERE idfifo = '$id_lote_actual'";
                if (!ejecutarConsulta($sql_update_fifo)) {
                    throw new Exception("Error al actualizar el stock del lote FIFO para el producto: $reg->nombre");
                }

                // Insertar el detalle de la venta por cada lote consumido
                $sqlDetalle = "INSERT INTO detalle_venta 
                    (idsucursal, idventa, idproducto, id_fifo, nombre_producto, cantidad, contenedor, cantidad_contenedor, precio_venta, descuento, tipo)
                    VALUES (
                        '$idsucursal', '$idventanew', '$id_producto_config', '$id_lote_actual', '$reg->nombre', 
                        '$cantidad_a_tomar', '$reg->contenedor', '$factor_contenedor', 
                        '$precio_venta_lote', 0, 'venta')";
                if (!ejecutarConsulta($sqlDetalle)) {
                    throw new Exception("Error al insertar el detalle de venta para el producto: $reg->nombre");
                }

                $cantidad_restante_a_vender -= $cantidad_a_tomar;
                $stock_global_descontado += $cantidad_a_tomar;
            }

            // Si después de recorrer todos los lotes aún falta cantidad, es un error de stock
            if ($cantidad_restante_a_vender > 0) {
                throw new Exception("Stock insuficiente en lotes para el producto: $reg->nombre. Faltan " . $cantidad_restante_a_vender . " unidades.");
            }

            // Actualizar el stock general en la tabla 'producto'
            $sqlUpd = "UPDATE producto 
                       SET stock = stock - '$stock_global_descontado' 
                       WHERE idproducto = '$id_producto_real' AND idsucursal = '$idsucursal'";
            if (!ejecutarConsulta($sqlUpd)) {
                throw new Exception("Error al actualizar el stock global para el producto: $reg->nombre");
            }
            
            // Registrar en kardex (un solo movimiento por el total de unidades vendidas del producto)
            $sqlKardex = "INSERT INTO kardex 
                (idsucursal, idproducto, cantidad, cantidad_contenedor, precio_unitario, stock_actual, tipo_movimiento, motivo, descripcion, fecha_kardex)
                VALUES (
                    '$idsucursal', '$id_producto_real', '$stock_global_descontado', '$factor_contenedor', '$reg->precio', 
                    (SELECT stock FROM producto WHERE idproducto = '$id_producto_real' AND idsucursal = '$idsucursal'), 
                    1, 'Venta', 'Venta POS #" . $num_comprobante . "', '$fechaActual')";
            if (!ejecutarConsulta($sqlKardex)) {
                throw new Exception("Error al registrar en el kardex para el producto: $reg->nombre");
            }
        }

        // Eliminar carrito temporal
        ejecutarConsulta("DELETE FROM temp_detalle_venta WHERE token = '$token'");

        // Confirmar venta
        ejecutarConsulta("COMMIT");
        return ['status' => 1, 'idventa' => $idventanew];

    } catch (Exception $e) {
        //  Revertir en caso de error
        ejecutarConsulta("ROLLBACK");
        return ['status' => 0, 'error' => $e->getMessage()];
    }
}


    public function actualizarStock($idproducto, $producto)
    {
        $sql = "SELECT cantidad_contenedor, cantidad FROM temp_detalle_venta WHERE idproducto = '$idproducto' AND producto = '$producto'";
        $resp = ejecutarConsulta($sql)->fetch_object();
        $cantidad_vendida = $resp->cantidad_contenedor * $resp->cantidad;
        $sql2 = "SELECT stock FROM producto WHERE idproducto = '$producto'";
        $resp2 = ejecutarConsulta($sql2)->fetch_object();
        $cantidad_habida = $resp2->stock;

        $totalstock = round($cantidad_habida - $cantidad_vendida, 2);
        $sql_stock = "UPDATE producto  SET stock = $totalstock WHERE idproducto = '$producto'";
        return ejecutarConsulta($sql_stock);
    }


    public function actualizarDataItem($idproducto, $campo, $value, $token)
{
    // Validar que solo se permiten ciertos campos (previene inyección SQL)
    $campos_permitidos = ['nombre', 'precio', 'cantidad'];
    if (!in_array($campo, $campos_permitidos)) {
        return ['error' => 'Campo no permitido'];
    }

    // Escapar el valor correctamente si no usas consultas preparadas
    $value_escaped = addslashes($value);

    // Si el campo es texto, rodearlo con comillas
    $sql = "UPDATE temp_detalle_venta SET $campo = '$value_escaped' WHERE token = '$token' AND idproducto = '$idproducto'";
   

    return ejecutarConsulta($sql);
}

    public function listarProductosActivosFIFO($idsucursal, $idcategoria = null)
{
    $idsucursal  = intval($idsucursal);
    $idcategoria = isset($idcategoria) && $idcategoria !== '' ? intval($idcategoria) : 0;

    // Filtro opcional
    $whereCategoria = ($idcategoria > 0)
        ? " AND c.idcategoria = $idcategoria "
        : "";

    $sql = "SELECT
                p.idproducto AS id_producto_real,
                p.nombre,
                p.imagen,
                p.codigo,
                p.stock AS stock_total,
                p.proigv,

                c.idcategoria,
                c.nombre AS categoria,
                um.nombre AS unidadmedida,

                pg.id AS id_producto_config,
                pg.cantidad_contenedor,
                pg.contenedor,
                pg.precio_venta AS precio_contenedor_guardado,
                pg.idfifo_origen,

                (SELECT idfifo
                 FROM stock_fifo
                 WHERE idproducto = p.idproducto
                   AND idsucursal = $idsucursal
                   AND cantidad_restante > 0
                   AND estado = 1
                 ORDER BY fecha_ingreso ASC
                 LIMIT 1) AS id_fifo,

                (SELECT precio_venta
                 FROM stock_fifo
                 WHERE idproducto = p.idproducto
                   AND idsucursal = $idsucursal
                   AND cantidad_restante > 0
                   AND estado = 1
                 ORDER BY fecha_ingreso ASC
                 LIMIT 1) AS precio_base_fifo,

                (SELECT cantidad_restante
                 FROM stock_fifo
                 WHERE idproducto = p.idproducto
                   AND idsucursal = $idsucursal
                   AND cantidad_restante > 0
                   AND estado = 1
                 ORDER BY fecha_ingreso ASC
                 LIMIT 1) AS stock_lote_fifo,

                (SELECT fecha_ingreso
                 FROM stock_fifo
                 WHERE idproducto = p.idproducto
                   AND idsucursal = $idsucursal
                   AND cantidad_restante > 0
                   AND estado = 1
                 ORDER BY fecha_ingreso ASC
                 LIMIT 1) AS fecha_ingreso,

                CASE
                    WHEN UPPER(TRIM(pg.contenedor)) = 'UNIDAD' THEN
                        COALESCE(
                            (SELECT precio_venta
                             FROM stock_fifo
                             WHERE idproducto = p.idproducto
                               AND idsucursal = $idsucursal
                               AND cantidad_restante > 0
                               AND estado = 1
                             ORDER BY fecha_ingreso ASC
                             LIMIT 1),
                            0
                        )
                    ELSE
                        COALESCE(NULLIF(pg.precio_venta, 0), 0)
                END AS precio_venta_fifo

            FROM producto p
            INNER JOIN producto_configuracion pg
                ON p.idproducto = pg.idproducto
                AND pg.deleted_at IS NULL
            INNER JOIN categoria c
                ON p.idcategoria = c.idcategoria
            INNER JOIN unidad_medida um
                ON p.idunidad_medida = um.idunidad_medida
            WHERE p.condicion = 1
              AND p.idsucursal = $idsucursal
              $whereCategoria
              AND c.nombre != 'SERVICIO'
            ORDER BY p.nombre ASC
            LIMIT 20";

    $productos = ejecutarConsulta($sql);
    $data = [];
    while ($reg = $productos->fetch_object()) {
        $data[] = $reg;
    }
    return $data;
}


public function searchProductosFIFO($idsucursal, $search = null, $type = null)
{
    $searching = "";
    if ($search) {
        $search_escaped = mysqli_real_escape_string($GLOBALS['conexion'], trim($search));
        if ($type == 2) { // Búsqueda por código
            $searching = "AND (p.codigo LIKE '%$search_escaped%' OR pg.codigo_extra LIKE '%$search_escaped%')";
        } else { // Búsqueda por nombre
            $palabras = explode(" ", $search_escaped);
            $condiciones = [];
            foreach ($palabras as $palabra) {
                $palabra = trim($palabra);
                if (strlen($palabra) > 0) {
                    $condiciones[] = "REPLACE(
                        REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(p.nombre),'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u'),'ñ','n') 
                        LIKE CONCAT('%', REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER('$palabra'),'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u'),'ñ','n'), '%')";
                }
            }
            if ($condiciones) {
                $searching = "AND (" . implode(" AND ", $condiciones) . ")";
            }
        }
    }

    $sql = "SELECT
                p.idproducto AS id_producto_real,
                p.nombre,
                p.imagen,
                p.codigo,
                p.stock AS stock_total,
                p.proigv,

                c.idcategoria,
                c.nombre AS categoria,
                um.nombre AS unidadmedida,

                pg.id AS id_producto_config,
                pg.cantidad_contenedor,
                pg.contenedor,
                pg.precio_venta AS precio_contenedor_guardado,
                pg.idfifo_origen,

                /* =========================
                   LOTE FIFO MÁS ANTIGUO CON STOCK
                ========================== */
                (SELECT idfifo 
                 FROM stock_fifo 
                 WHERE idproducto = p.idproducto
                   AND idsucursal = '$idsucursal'
                   AND cantidad_restante > 0
                   AND estado = 1
                 ORDER BY fecha_ingreso ASC
                 LIMIT 1) AS id_fifo,

                (SELECT precio_venta 
                 FROM stock_fifo 
                 WHERE idproducto = p.idproducto
                   AND idsucursal = '$idsucursal'
                   AND cantidad_restante > 0
                   AND estado = 1
                 ORDER BY fecha_ingreso ASC
                 LIMIT 1) AS precio_base_fifo,

                (SELECT cantidad_restante 
                 FROM stock_fifo 
                 WHERE idproducto = p.idproducto
                   AND idsucursal = '$idsucursal'
                   AND cantidad_restante > 0
                   AND estado = 1
                 ORDER BY fecha_ingreso ASC
                 LIMIT 1) AS stock_lote_fifo,

                (SELECT fecha_ingreso 
                 FROM stock_fifo 
                 WHERE idproducto = p.idproducto
                   AND idsucursal = '$idsucursal'
                   AND cantidad_restante > 0
                   AND estado = 1
                 ORDER BY fecha_ingreso ASC
                 LIMIT 1) AS fecha_ingreso,

                /* =========================
                   PRECIO FINAL DEL CONTENEDOR
                   - Si es UNIDAD: precio FIFO automático (se actualiza con cada lote)
                   - Si NO es UNIDAD: precio MANUAL de producto_configuracion (fijo hasta que el usuario lo cambie)
                ========================== */
                CASE 
                    WHEN UPPER(TRIM(pg.contenedor)) = 'UNIDAD' THEN
                        -- UNIDAD: Precio FIFO del lote más antiguo (automático)
                        COALESCE(
                            (SELECT precio_venta 
                             FROM stock_fifo 
                             WHERE idproducto = p.idproducto
                               AND idsucursal = '$idsucursal'
                               AND cantidad_restante > 0
                               AND estado = 1
                             ORDER BY fecha_ingreso ASC
                             LIMIT 1),
                            0
                        )
                    ELSE
                        -- Otros contenedores: Precio MANUAL guardado (NO se actualiza con lotes)
                        COALESCE(NULLIF(pg.precio_venta, 0), 0)
                END AS precio_venta_fifo

            FROM producto p
            INNER JOIN producto_configuracion pg 
                ON p.idproducto = pg.idproducto 
                AND pg.deleted_at IS NULL
            INNER JOIN categoria c 
                ON p.idcategoria = c.idcategoria
            INNER JOIN unidad_medida um 
                ON p.idunidad_medida = um.idunidad_medida
            WHERE p.condicion = 1
              AND p.idsucursal = '$idsucursal'
              AND c.nombre != 'SERVICIO'
              $searching
            ORDER BY p.nombre ASC
            LIMIT 20";

    $productos = ejecutarConsulta($sql);
    $data = array();
    while ($reg = $productos->fetch_object()) {
        $data[] = $reg;
    }
    return $data;
}
}