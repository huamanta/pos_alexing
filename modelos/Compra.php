<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";

date_default_timezone_set('America/Lima');

Class Compra
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	public function insertar(
    $idsucursal, $idproveedor, $idpersonal, $tipo_c, $tipo_comprobante, $serie_comprobante,
    $num_comprobante, $fecha_hora, $impuesto, $tipo_igv, $monto_gravado, $monto_exonerado, 
    $monto_igv, $total_compra, $formapago, $lugar_entrega,
    $motivo_compra, $documento, $nota, $comprobanteReferencia, $idproducto, $nombre_producto,
    $cantidad, $precio_compra, $precio_venta, $nlote, $fvencimiento, $tipopago,
    $fechaOperacion, $input_cuotas, $montoPagado, $montoDeuda, $fecha_pago,
    $totaldeposito, $noperacion, $totalrecibido, $fecha_deposito, $tipo_pago, $monto_pago, $operacion_pago) {
    try {
        // INICIO DE TRANSACCIÓN
        ejecutarConsulta("BEGIN");

        // Validación de sesión
        if (!isset($_SESSION['idpersonal']) || empty($_SESSION['idpersonal'])) {
            throw new Exception("La sesión del personal no es válida.");
        }
        $idpersonal = $_SESSION['idpersonal'];

        // Estado
        $estadoC = ($tipo_c == 'Orden Compra') ? 'POR APROBACIÓN' : 'REGISTRADO';

        // Si existe un comprobante de referencia → actualizarlo
        if (!empty($comprobanteReferencia)) {
            $sql3 = "UPDATE compra SET estadoC = 'COMPRADO' WHERE idcompra = '$comprobanteReferencia'";
            if (!ejecutarConsulta($sql3)) {
                throw new Exception("Error al actualizar la orden de compra de referencia.");
            }
        }

        $fechaActual = date('Y-m-d H:i:s');
        
        // =====================================================
        // OBTENER PRODUCTOS DESDE TABLA TEMPORAL (compra_tmp)
        // =====================================================
        $sqlTmp = "SELECT * FROM compra_tmp
                   WHERE idpersonal='$idpersonal'
                   AND idsucursal='$idsucursal'";
        $rsTmp = ejecutarConsulta($sqlTmp);

        if (!$rsTmp || $rsTmp->num_rows == 0) {
            throw new Exception("No hay productos agregados a la compra.");
        }

        // REINICIAMOS ARRAYS
        $idproducto = [];
        $nombre_producto = [];
        $cantidad = [];
        $precio_compra = [];
        $precio_venta = [];
        $nlote = [];
        $fvencimiento = [];

        while ($tmp = $rsTmp->fetch_object()) {
            $idproducto[]       = $tmp->idproducto;
            $nombre_producto[]  = $tmp->nombre_producto;
            $cantidad[]         = $tmp->cantidad;
            $precio_compra[]    = $tmp->precio_compra;
            $precio_venta[]     = $tmp->precio_venta;
            $nlote[]            = $tmp->nlote;
            $fvencimiento[]     = $tmp->fvencimiento;
        }

        // =====================================================
        // VALIDAR Y LIMPIAR DATOS DE PAGO
        // =====================================================
        $pagos_validos = [];
        
        if (is_array($tipo_pago) && is_array($monto_pago)) {
            for ($i = 0; $i < count($tipo_pago); $i++) {
                // Validar que exista el tipo de pago y el monto
                if (empty($tipo_pago[$i]) || !isset($monto_pago[$i])) {
                    continue;
                }
                
                // Limpiar y convertir el monto
                $monto_limpio = is_numeric($monto_pago[$i]) ? floatval($monto_pago[$i]) : 0;
                
                // Solo agregar si el monto es mayor a 0
                if ($monto_limpio > 0) {
                    $pagos_validos[] = [
                        'tipo' => trim($tipo_pago[$i]),
                        'monto' => $monto_limpio,
                        'operacion' => isset($operacion_pago[$i]) && !empty($operacion_pago[$i]) 
                                      ? trim($operacion_pago[$i]) 
                                      : null
                    ];
                }
            }
        }

        // Validar que la suma de pagos no exceda el total de la compra
        $suma_pagos = array_sum(array_column($pagos_validos, 'monto'));
        if ($suma_pagos > floatval($total_compra)) {
            throw new Exception("La suma de los pagos (" . number_format($suma_pagos, 2) . ") excede el total de la compra (" . number_format($total_compra, 2) . ")");
        }

        // Validar consistencia entre montoPagado y suma de pagos
        if (!empty($montoPagado) && floatval($montoPagado) > 0) {
            $diferencia = abs($suma_pagos - floatval($montoPagado));
            if ($diferencia > 0.01) { // Tolerancia de 1 centavo por redondeos
                throw new Exception("El monto total pagado no coincide con la suma de los pagos individuales");
            }
        }

        // 1. INSERTAR COMPRA CABECERA
        $sql = "INSERT INTO compra (idsucursal, idproveedor, idpersonal, tipo_c, tipo_comprobante,
                    serie_comprobante, num_comprobante, fecha_hora, impuesto, tipo_igv, monto_gravado, monto_exonerado, monto_igv, total_compra,
                    compracredito, motoPagado, formapago, lugar_entrega, motivo_compra, documento,
                    nota, estado, estadoC, documento_rel, totaldeposito, noperacion, totalrecibido,
                    fecha_deposito, fecha_kardex)
                VALUES ('$idsucursal','$idproveedor','$idpersonal','$tipo_c','$tipo_comprobante',
                    '$serie_comprobante','$num_comprobante','$fecha_hora','$impuesto','$tipo_igv','$monto_gravado','$monto_exonerado','$monto_igv', '$total_compra',
                    '$tipopago','$montoPagado','$formapago','$lugar_entrega','$motivo_compra',
                    '$documento','$nota','REGISTRADO','$estadoC','$comprobanteReferencia',
                    '$totaldeposito','$noperacion','$totalrecibido','$fecha_deposito','$fechaActual')";
        
        $idcompra_new = ejecutarConsulta_retornarID($sql);
        if (!$idcompra_new) {
            throw new Exception("No se pudo obtener el ID de la nueva compra.");
        }

        // 2. INSERTAR DETALLES Y ACTUALIZAR STOCK/KARDEX
        for ($i = 0; $i < count($idproducto); $i++) {
            if (empty($idproducto[$i]) || empty($cantidad[$i]) || $cantidad[$i] <= 0) {
                continue;
            }

            // A. Insertar detalle de la compra y el stock del lote
            $sql_detalle = "INSERT INTO detalle_compra(idsucursal, idcompra, idproducto, nombre_producto,
                            cantidad, precio_compra, precio_venta, nlote, fvencimiento, tipo_c, stock_lote)
                            VALUES ('$idsucursal', '$idcompra_new', '{$idproducto[$i]}', '{$nombre_producto[$i]}', '{$cantidad[$i]}',
                            '{$precio_compra[$i]}', '{$precio_venta[$i]}', '{$nlote[$i]}', '{$fvencimiento[$i]}', '$tipo_c', '{$cantidad[$i]}')";
            $iddetalle_compra = ejecutarConsulta_retornarID($sql_detalle);
            if (!$iddetalle_compra) {
                throw new Exception("No se pudo obtener iddetalle_compra para: " . $nombre_producto[$i]);
            }

            // B. Actualizar stock del producto
            $sqlUpdateStock = "UPDATE producto SET stock = stock + '{$cantidad[$i]}' WHERE idproducto = '{$idproducto[$i]}' AND idsucursal = '$idsucursal'";
            if (!ejecutarConsulta($sqlUpdateStock)) {
                throw new Exception("Error al actualizar el stock total del producto: " . $nombre_producto[$i]);
            }

            // C. Insertar en Kardex
            $sql_stock_actual = "SELECT stock FROM producto WHERE idproducto = '{$idproducto[$i]}' AND idsucursal = '$idsucursal'";
            $resultado_stock = ejecutarConsulta($sql_stock_actual);
            if (!$resultado_stock) {
                throw new Exception("Error al obtener el stock actual del producto: " . $nombre_producto[$i]);
            }
            $stock_actual_obj = $resultado_stock->fetch_object();
            $nuevo_stock = $stock_actual_obj->stock;

            $sqlK = "INSERT INTO kardex (idsucursal, idproducto, cantidad, precio_unitario,
                    stock_actual, tipo_movimiento, motivo, fecha_kardex)
                    VALUES ('$idsucursal', '{$idproducto[$i]}', '{$cantidad[$i]}', '{$precio_compra[$i]}', '$nuevo_stock', 0, 'Compra', '$fechaActual')";
            if (!ejecutarConsulta($sqlK)) {
                throw new Exception("Error al registrar el movimiento en el kardex para: " . $nombre_producto[$i]);
            }

            // D. Actualizar precios en la tabla producto
            $sqlUpdateProducto = "UPDATE producto SET precio_compra = '{$precio_compra[$i]}', precio = '{$precio_venta[$i]}' WHERE idproducto = '{$idproducto[$i]}' AND idsucursal = '$idsucursal'";
            if (!ejecutarConsulta($sqlUpdateProducto)) {
                throw new Exception("Error al actualizar los precios del producto: " . $nombre_producto[$i]);
            }

            // E. Actualizar precio de venta en la tabla producto_configuracion
            $sqlUpdatePrecioVentaConfig = "UPDATE producto_configuracion SET precio_venta = '{$precio_venta[$i]}' WHERE idproducto = '{$idproducto[$i]}'";
            if (!ejecutarConsulta($sqlUpdatePrecioVentaConfig)) {
                throw new Exception("Error al actualizar el precio de venta en la configuración del producto: " . $nombre_producto[$i]);
            }

            // F. FIFO DE COMPRA (LOTE REAL)
            $sqlFifoCompra = "INSERT INTO stock_fifo (
                idsucursal,
                idproducto,
                origen,
                referencia_id,
                cantidad_ingreso,
                cantidad_restante,
                precio_compra,
                precio_venta,
                fecha_ingreso,
                fvencimiento
            ) VALUES (
                '$idsucursal',
                '{$idproducto[$i]}',
                'COMPRA',
                '$iddetalle_compra',
                '{$cantidad[$i]}',
                '{$cantidad[$i]}',
                '{$precio_compra[$i]}',
                '{$precio_venta[$i]}',
                '$fechaActual',
                '{$fvencimiento[$i]}'
            )";

            if (!ejecutarConsulta($sqlFifoCompra)) {
                throw new Exception('Error al registrar FIFO de compra para: ' . $nombre_producto[$i]);
            }
        }

        // 3. GESTIONAR CUENTAS POR PAGAR (SI ES CRÉDITO)
        if ($tipopago == 'Si' && !empty($fecha_pago) && is_array($fecha_pago)) {
            $num_cuotas = count($fecha_pago);
            if ($num_cuotas > 0 && floatval($montoDeuda) > 0) {
                $montoCuota = round($montoDeuda / $num_cuotas, 2);
                $suma_cuotas = 0;
                
                foreach ($fecha_pago as $index => $fecha_vencimiento) {
                    // Para la última cuota, ajustar por diferencias de redondeo
                    $monto_actual = ($index == $num_cuotas - 1) 
                                   ? round($montoDeuda - $suma_cuotas, 2) 
                                   : $montoCuota;
                    
                    $sqlCPP = "INSERT INTO cuentas_por_pagar(idcompra, fecharegistro, deudatotal, fechavencimiento)
                               VALUES ('$idcompra_new', '$fecha_hora', '$monto_actual', '$fecha_vencimiento')";
                    if (!ejecutarConsulta($sqlCPP)) {
                        throw new Exception("Error al generar las cuentas por pagar.");
                    }
                    
                    $suma_cuotas += $monto_actual;
                }
            }
        }

        // =====================================================
        // REGISTRAR PAGOS DE LA COMPRA (SOLO PAGOS VÁLIDOS)
        // =====================================================
        if (count($pagos_validos) > 0) {
            foreach ($pagos_validos as $pago) {
                $nro_op = $pago['operacion'] !== null 
                         ? "'{$pago['operacion']}'" 
                         : "NULL";
                
                $sqlPago = "INSERT INTO compra_pago (
                                idcompra,
                                tipo_pago,
                                monto,
                                nro_operacion,
                                fecha_pago
                            ) VALUES (
                                '$idcompra_new',
                                '{$pago['tipo']}',
                                '{$pago['monto']}',
                                $nro_op,
                                '$fechaActual'
                            )";

                if (!ejecutarConsulta($sqlPago)) {
                    throw new Exception("Error al registrar pago de tipo {$pago['tipo']} por monto {$pago['monto']}");
                }
            }
        }

        // LIMPIAR COMPRA TEMPORAL (SOLO SI TODO SALIÓ BIEN)
        $sqlCleanTmp = "DELETE FROM compra_tmp
                        WHERE idpersonal='$idpersonal'
                        AND idsucursal='$idsucursal'";
        ejecutarConsulta($sqlCleanTmp);
        
        // TODO OK → confirmar transacción
        ejecutarConsulta("COMMIT");
        return $idcompra_new;

    } catch (Exception $e) {
        // Algo salió mal → revertir todo
        ejecutarConsulta("ROLLBACK");
        error_log("Error en Compra->insertar: " . $e->getMessage());
        return false;
    }
}

	public function agregarTmp(
        $idpersonal,
        $idsucursal,
        $idproducto,
        $nombre_producto,
        $cantidad,
        $precio_compra,
        $precio_venta,
        $unidadmedida,
        $nlote,
        $fvencimiento
    ) {
        $sql = "INSERT INTO compra_tmp(
                    idpersonal, idsucursal, idproducto, nombre_producto,
                    cantidad, precio_compra, precio_venta, unidadmedida, nlote, fvencimiento
                ) VALUES (
                    '$idpersonal', '$idsucursal', '$idproducto', '$nombre_producto',
                    '$cantidad', '$precio_compra', '$precio_venta', '$unidadmedida', '$nlote', '$fvencimiento'
                )";
        return ejecutarConsulta($sql);
    }

	public function listarTmp($idpersonal, $idsucursal) {
    $sql = "SELECT 
                idproducto,
                nombre_producto,
                cantidad,
                -- Eliminar ceros innecesarios
                CAST(precio_compra AS DECIMAL(18,8)) + 0 AS precio_compra,
                CAST(precio_venta AS DECIMAL(18,8)) + 0 AS precio_venta,
                unidadmedida,
                nlote,
                fvencimiento
            FROM compra_tmp
            WHERE idpersonal='$idpersonal'
            AND idsucursal='$idsucursal'";
    return ejecutarConsulta($sql);
}

	public function actualizarDetalleTemporal(
        $idpersonal,
        $idsucursal,
        $idproducto,
        $cantidad,
        $precio_compra,
        $precio_venta,
        $nlote,
        $fvencimiento
    ) {
        $sql = "UPDATE compra_tmp SET
                    cantidad = '$cantidad',
                    precio_compra = '$precio_compra',
                    precio_venta = '$precio_venta',
                    nlote = '$nlote',
                    fvencimiento = '$fvencimiento'
                WHERE idpersonal = '$idpersonal'
                  AND idsucursal = '$idsucursal'
                  AND idproducto = '$idproducto'";
        return ejecutarConsulta($sql);
    }

	public function eliminarDetalleTemporal($idpersonal, $idsucursal, $idproducto)
	{
		$sql = "DELETE FROM compra_tmp 
				WHERE idpersonal = '$idpersonal' AND idsucursal = '$idsucursal' AND idproducto = '$idproducto'";
		return ejecutarConsulta($sql);
	}

	

    public function editar(
    $idcompra, $idsucursal, $idproveedor, $tipo_comprobante, $serie_comprobante,
    $num_comprobante, $fecha_hora, $impuesto, $tipo_igv, $monto_gravado, 
    $monto_exonerado, $monto_igv, $total_compra, $formapago, $tipopago, 
    $lugar_entrega, $motivo_compra, $documento, $nota, $comprobanteReferencia, 
    $idproducto, $nombre_producto, $cantidad, $precio_compra, $precio_venta, 
    $nlote, $fvencimiento, $fechaOperacion, $input_cuotas, $montoPagado, 
    $montoDeuda, $fecha_pago, $tipo_c, $totaldeposito, $noperacion, 
    $totalrecibido, $fecha_deposito, $tipo_pago, $monto_pago, $operacion_pago) {
    
    try {
        // INICIO DE TRANSACCIÓN
        ejecutarConsulta("BEGIN");
        
        // Validación de sesión
        if (!isset($_SESSION['idpersonal']) || empty($_SESSION['idpersonal'])) {
            throw new Exception("La sesión del personal no es válida.");
        }
        $idpersonal = $_SESSION['idpersonal'];
        
        $fechaActual = date('Y-m-d H:i:s');
        
        // VALIDAR QUE LA COMPRA EXISTE Y ESTÁ EN ESTADO REGISTRADO
        $sqlValidar = "SELECT estado FROM compra WHERE idcompra = '$idcompra'";
        $rsValidar = ejecutarConsulta($sqlValidar);
        if (!$rsValidar || $rsValidar->num_rows == 0) {
            throw new Exception("La compra no existe.");
        }
        $compraActual = $rsValidar->fetch_object();
        if ($compraActual->estado != 'REGISTRADO') {
            throw new Exception("Solo se pueden editar compras en estado REGISTRADO.");
        }
        
        // =====================================================
        // PASO 1: REVERTIR STOCK, KARDEX Y FIFO DE DETALLES ANTERIORES
        // =====================================================
        $sqlDetallesAnteriores = "SELECT dc.*, p.nombre as nombre_producto
                                    FROM detalle_compra dc
                                    INNER JOIN producto p ON dc.idproducto = p.idproducto
                                    WHERE dc.idcompra = '$idcompra'";
        $rsDetallesAnteriores = ejecutarConsulta($sqlDetallesAnteriores);

        if (!$rsDetallesAnteriores) {
            throw new Exception("Error al obtener los detalles anteriores de la compra.");
        }

        // Crear array de detalles anteriores
        $detallesAnteriores = [];
        while ($detAnterior = $rsDetallesAnteriores->fetch_object()) {
            $detallesAnteriores[$detAnterior->idproducto] = $detAnterior;
        }
        // =====================================================
        // PASO 2: ELIMINAR DETALLES ANTERIORES
        // =====================================================
        foreach ($detallesAnteriores as $idprod => $detAnterior) {
            $sqlCheckFifo = "SELECT COALESCE(SUM(cantidad_restante), 0) as restante 
                             FROM stock_fifo 
                             WHERE origen = 'COMPRA' 
                             AND referencia_id = '{$detAnterior->iddetalle_compra}'";
            $rsFifo = ejecutarConsulta($sqlCheckFifo);
            $fifoRow = $rsFifo->fetch_object();

            $detallesAnteriores[$idprod]->fifo_disponible = floatval($fifoRow->restante) > 0;
            $detallesAnteriores[$idprod]->fifo_restante   = floatval($fifoRow->restante);
        }
        // =====================================================
        // PASO 3: ELIMINAR PAGOS ANTERIORES
        // =====================================================
        $sqlEliminarPagos = "DELETE FROM compra_pago WHERE idcompra = '$idcompra'";
        ejecutarConsulta($sqlEliminarPagos); // No falla si no hay pagos
        
        // =====================================================
        // PASO 4: ELIMINAR CUENTAS POR PAGAR ANTERIORES
        // =====================================================
        $sqlEliminarCPP = "DELETE FROM cuentas_por_pagar WHERE idcompra = '$idcompra'";
        ejecutarConsulta($sqlEliminarCPP); // No falla si no hay cuentas
        
        // =====================================================
        // PASO 5: ACTUALIZAR CABECERA DE COMPRA
        // =====================================================
        $sqlUpdateCompra = "UPDATE compra SET
                            idsucursal = '$idsucursal',
                            idproveedor = '$idproveedor',
                            tipo_comprobante = '$tipo_comprobante',
                            serie_comprobante = '$serie_comprobante',
                            num_comprobante = '$num_comprobante',
                            fecha_hora = '$fecha_hora',
                            impuesto = '$impuesto',
                            tipo_igv = '$tipo_igv',
                            monto_gravado = '$monto_gravado',
                            monto_exonerado = '$monto_exonerado',
                            monto_igv = '$monto_igv',
                            total_compra = '$total_compra',
                            compracredito = '$tipopago',
                            motoPagado = '$montoPagado',
                            formapago = '$formapago',
                            lugar_entrega = '$lugar_entrega',
                            motivo_compra = '$motivo_compra',
                            documento = '$documento',
                            nota = '$nota',
                            totaldeposito = '$totaldeposito',
                            noperacion = '$noperacion',
                            totalrecibido = '$totalrecibido',
                            fecha_deposito = '$fecha_deposito',
                            fecha_kardex = '$fechaActual'
                            WHERE idcompra = '$idcompra'";
        
        if (!ejecutarConsulta($sqlUpdateCompra)) {
            throw new Exception("Error al actualizar la cabecera de la compra.");
        }
        
        // =====================================================
        // PASO 6: OBTENER NUEVOS PRODUCTOS DESDE TABLA TEMPORAL
        // =====================================================
        $sqlTmp = "SELECT * FROM compra_tmp
                   WHERE idpersonal='$idpersonal'
                   AND idsucursal='$idsucursal'";
        $rsTmp = ejecutarConsulta($sqlTmp);
        
        if (!$rsTmp || $rsTmp->num_rows == 0) {
            throw new Exception("No hay productos agregados en la tabla temporal para actualizar.");
        }
        
        // REINICIAMOS ARRAYS
        $idproducto = [];
        $nombre_producto = [];
        $cantidad = [];
        $precio_compra = [];
        $precio_venta = [];
        $nlote = [];
        $fvencimiento = [];
        
        while ($tmp = $rsTmp->fetch_object()) {
            $idproducto[]       = $tmp->idproducto;
            $nombre_producto[]  = $tmp->nombre_producto;
            $cantidad[]         = $tmp->cantidad;
            $precio_compra[]    = $tmp->precio_compra;
            $precio_venta[]     = $tmp->precio_venta;
            $nlote[]            = $tmp->nlote;
            $fvencimiento[]     = $tmp->fvencimiento;
        }
        
        // =====================================================
        // PASO 7: VALIDAR Y LIMPIAR DATOS DE PAGO
        // =====================================================
        $pagos_validos = [];
        
        if (is_array($tipo_pago) && is_array($monto_pago)) {
            for ($i = 0; $i < count($tipo_pago); $i++) {
                if (empty($tipo_pago[$i]) || !isset($monto_pago[$i])) {
                    continue;
                }
                
                $monto_limpio = is_numeric($monto_pago[$i]) ? floatval($monto_pago[$i]) : 0;
                
                if ($monto_limpio > 0) {
                    $pagos_validos[] = [
                        'tipo' => trim($tipo_pago[$i]),
                        'monto' => $monto_limpio,
                        'operacion' => isset($operacion_pago[$i]) && !empty($operacion_pago[$i]) 
                                      ? trim($operacion_pago[$i]) 
                                      : null
                    ];
                }
            }
        }
        
        // Validar suma de pagos
        $suma_pagos = array_sum(array_column($pagos_validos, 'monto'));
        if ($suma_pagos > floatval($total_compra)) {
            throw new Exception("La suma de los pagos (" . number_format($suma_pagos, 2) . ") excede el total de la compra (" . number_format($total_compra, 2) . ")");
        }
        
        // Validar consistencia
        if (!empty($montoPagado) && floatval($montoPagado) > 0) {
            $diferencia = abs($suma_pagos - floatval($montoPagado));
            if ($diferencia > 0.01) {
                throw new Exception("El monto total pagado no coincide con la suma de los pagos individuales");
            }
        }
        
        // =====================================================
        // PASO 8: INSERTAR NUEVOS DETALLES Y ACTUALIZAR STOCK/KARDEX/FIFO
        // =====================================================
        for ($i = 0; $i < count($idproducto); $i++) {
            if (empty($idproducto[$i]) || empty($cantidad[$i]) || $cantidad[$i] <= 0) {
                continue;
            }

            // Si el producto ya fue vendido (FIFO consumido), no tocarlo
            if (isset($detallesAnteriores[$idproducto[$i]]) &&
                $detallesAnteriores[$idproducto[$i]]->fifo_disponible === false) {
                continue;
            }

            // Validar que la cantidad no sea menor a lo ya vendido
            if (isset($detallesAnteriores[$idproducto[$i]])) {
                $cantidadVendida = $detallesAnteriores[$idproducto[$i]]->cantidad
                                 - $detallesAnteriores[$idproducto[$i]]->fifo_restante;
                if (floatval($cantidad[$i]) < $cantidadVendida) {
                    throw new Exception(
                        "La cantidad de '{$nombre_producto[$i]}' no puede ser menor a $cantidadVendida " .
                        "porque ya se vendieron esas unidades."
                    );
                }
            }
            
            // A. CALCULAR DIFERENCIA CON CANTIDAD ANTERIOR
            $cantidadAnterior = 0;
            $precioCompraAnterior = 0;
            
            if (isset($detallesAnteriores[$idproducto[$i]])) {
                $cantidadAnterior = $detallesAnteriores[$idproducto[$i]]->cantidad;
                $precioCompraAnterior = $detallesAnteriores[$idproducto[$i]]->precio_compra;
            }
            
            $diferenciaCantidad = $cantidad[$i] - $cantidadAnterior;
            // B. INSERTAR O ACTUALIZAR DETALLE
            if (isset($detallesAnteriores[$idproducto[$i]])) {

                //  UPDATE
                $iddetalle_compra = $detallesAnteriores[$idproducto[$i]]->iddetalle_compra;

                $sql_detalle = "UPDATE detalle_compra SET
                                    cantidad = '{$cantidad[$i]}',
                                    precio_compra = '{$precio_compra[$i]}',
                                    precio_venta = '{$precio_venta[$i]}',
                                    nlote = '{$nlote[$i]}',
                                    fvencimiento = '{$fvencimiento[$i]}',
                                    stock_lote = '{$cantidad[$i]}'
                                WHERE iddetalle_compra = '$iddetalle_compra'";

                if (!ejecutarConsulta($sql_detalle)) {
                    throw new Exception("Error al actualizar detalle: {$nombre_producto[$i]}");
                }

            } else {

                //  INSERT
                $sql_detalle = "INSERT INTO detalle_compra(
                    idsucursal, idcompra, idproducto, nombre_producto,
                    cantidad, precio_compra, precio_venta, nlote, fvencimiento,
                    tipo_c, stock_lote
                ) VALUES (
                    '$idsucursal', '$idcompra', '{$idproducto[$i]}',
                    '{$nombre_producto[$i]}', '{$cantidad[$i]}',
                    '{$precio_compra[$i]}', '{$precio_venta[$i]}',
                    '{$nlote[$i]}', '{$fvencimiento[$i]}',
                    '$tipo_c', '{$cantidad[$i]}'
                )";

                $iddetalle_compra = ejecutarConsulta_retornarID($sql_detalle);

                if (!$iddetalle_compra) {
                    throw new Exception("No se pudo insertar el detalle para: {$nombre_producto[$i]}");
                }
            }
            // C. AJUSTAR STOCK SEGÚN DIFERENCIA
            if ($diferenciaCantidad != 0) {
                $operador = ($diferenciaCantidad > 0) ? '+' : '';
                $sqlUpdateStock = "UPDATE producto 
                                  SET stock = stock $operador $diferenciaCantidad 
                                  WHERE idproducto = '{$idproducto[$i]}' 
                                  AND idsucursal = '$idsucursal'";
                if (!ejecutarConsulta($sqlUpdateStock)) {
                    throw new Exception("Error al ajustar el stock del producto: {$nombre_producto[$i]}");
                }
                
                // D. OBTENER STOCK ACTUALIZADO
                $sql_stock_actual = "SELECT stock FROM producto 
                                    WHERE idproducto = '{$idproducto[$i]}' 
                                    AND idsucursal = '$idsucursal'";
                $resultado_stock = ejecutarConsulta($sql_stock_actual);
                if (!$resultado_stock) {
                    throw new Exception("Error al obtener stock actual de: {$nombre_producto[$i]}");
                }
                $stock_obj = $resultado_stock->fetch_object();
                $nuevo_stock = $stock_obj->stock;
                
                // E. REGISTRAR EN KARDEX SEGÚN TIPO DE AJUSTE
                if ($diferenciaCantidad > 0) {
                    // AUMENTO DE CANTIDAD
                    $motivo = "Edición de Compra #$idcompra (Incremento)";
                    $tipo_movimiento = 0; // Entrada
                    $cantidadKardex = $diferenciaCantidad;
                } else {
                    // REDUCCIÓN DE CANTIDAD
                    $motivo = "Edición de Compra #$idcompra (Reducción)";
                    $tipo_movimiento = 1; // Salida
                    $cantidadKardex = abs($diferenciaCantidad);
                }
                
                $sqlKardex = "INSERT INTO kardex (
                                idsucursal, idproducto, cantidad, precio_unitario,
                                stock_actual, tipo_movimiento, motivo, fecha_kardex
                             ) VALUES (
                                '$idsucursal', 
                                '{$idproducto[$i]}', 
                                '$cantidadKardex', 
                                '{$precio_compra[$i]}', 
                                '$nuevo_stock', 
                                $tipo_movimiento, 
                                '$motivo', 
                                '$fechaActual'
                             )";
                if (!ejecutarConsulta($sqlKardex)) {
                    throw new Exception("Error al registrar en kardex para: {$nombre_producto[$i]}");
                }
            }
            
            // F. ACTUALIZAR PRECIOS EN PRODUCTO
            $sqlUpdateProducto = "UPDATE producto 
                                 SET precio_compra = '{$precio_compra[$i]}', 
                                     precio = '{$precio_venta[$i]}' 
                                 WHERE idproducto = '{$idproducto[$i]}' 
                                 AND idsucursal = '$idsucursal'";
            if (!ejecutarConsulta($sqlUpdateProducto)) {
                throw new Exception("Error al actualizar precios de: {$nombre_producto[$i]}");
            }
            
            // G. ACTUALIZAR PRECIO EN CONFIGURACIÓN
            $sqlUpdatePrecioVentaConfig = "UPDATE producto_configuracion 
                                          SET precio_venta = '{$precio_venta[$i]}' 
                                          WHERE idproducto = '{$idproducto[$i]}'";
            if (!ejecutarConsulta($sqlUpdatePrecioVentaConfig)) {
                throw new Exception("Error al actualizar precio en configuración de: {$nombre_producto[$i]}");
            }
            
            // H. INSERTAR NUEVO REGISTRO FIFO
            if ($diferenciaCantidad > 0) {
                // Solo insertar FIFO por la cantidad adicional nueva
                $sqlFifoCompra = "INSERT INTO stock_fifo (
                                    idsucursal, idproducto, origen, referencia_id,
                                    cantidad_ingreso, cantidad_restante, precio_compra, precio_venta,
                                    fecha_ingreso, fvencimiento
                                 ) VALUES (
                                    '$idsucursal',
                                    '{$idproducto[$i]}',
                                    'COMPRA',
                                    '$iddetalle_compra',
                                    '$diferenciaCantidad',
                                    '$diferenciaCantidad',
                                    '{$precio_compra[$i]}',
                                    '{$precio_venta[$i]}',
                                    '$fechaActual',
                                    '{$fvencimiento[$i]}'
                                 )";

                if (!ejecutarConsulta($sqlFifoCompra)) {
                    throw new Exception("Error al registrar FIFO para: {$nombre_producto[$i]}");
                }

            } elseif ($diferenciaCantidad == 0) {
                // Cantidad no cambió — solo actualizar precio si el FIFO original aún tiene stock restante
                $sqlUpdateFifo = "UPDATE stock_fifo 
                                  SET precio_compra = '{$precio_compra[$i]}',
                                      precio_venta  = '{$precio_venta[$i]}'
                                  WHERE origen = 'COMPRA'
                                  AND referencia_id = '$iddetalle_compra'
                                  AND cantidad_restante > 0";
                ejecutarConsulta($sqlUpdateFifo); // No falla si no hay nada que actualizar

            } else {
                // $diferenciaCantidad < 0 — se redujo la cantidad
                // Reducir cantidad_restante del FIFO existente, sin pasarse de cero
                $reduccion = abs($diferenciaCantidad);
                $sqlReducirFifo = "UPDATE stock_fifo
                                   SET cantidad_restante = GREATEST(0, cantidad_restante - $reduccion)
                                   WHERE origen = 'COMPRA'
                                   AND referencia_id = '$iddetalle_compra'";
                ejecutarConsulta($sqlReducirFifo);
            }
        }
        // =====================================================
        // PASO 9: GESTIONAR NUEVAS CUENTAS POR PAGAR (SI ES CRÉDITO)
        // =====================================================
        if ($tipopago == 'Si' && !empty($fecha_pago) && is_array($fecha_pago)) {
            $num_cuotas = count($fecha_pago);
            if ($num_cuotas > 0 && floatval($montoDeuda) > 0) {
                $montoCuota = round($montoDeuda / $num_cuotas, 2);
                $suma_cuotas = 0;
                
                foreach ($fecha_pago as $index => $fecha_vencimiento) {
                    $monto_actual = ($index == $num_cuotas - 1) 
                                   ? round($montoDeuda - $suma_cuotas, 2) 
                                   : $montoCuota;
                    
                    $sqlCPP = "INSERT INTO cuentas_por_pagar(
                                  idcompra, fecharegistro, deudatotal, fechavencimiento
                               ) VALUES (
                                  '$idcompra', 
                                  '$fecha_hora', 
                                  '$monto_actual', 
                                  '$fecha_vencimiento'
                               )";
                    if (!ejecutarConsulta($sqlCPP)) {
                        throw new Exception("Error al generar las cuentas por pagar.");
                    }
                    
                    $suma_cuotas += $monto_actual;
                }
            }
        }
        
        // =====================================================
        // PASO 10: REGISTRAR NUEVOS PAGOS
        // =====================================================
        if (count($pagos_validos) > 0) {
            foreach ($pagos_validos as $pago) {
                $nro_op = $pago['operacion'] !== null 
                         ? "'{$pago['operacion']}'" 
                         : "NULL";
                
                $sqlPago = "INSERT INTO compra_pago (
                                idcompra, tipo_pago, monto, nro_operacion, fecha_pago
                            ) VALUES (
                                '$idcompra',
                                '{$pago['tipo']}',
                                '{$pago['monto']}',
                                $nro_op,
                                '$fechaActual'
                            )";
                
                if (!ejecutarConsulta($sqlPago)) {
                    throw new Exception("Error al registrar pago de tipo {$pago['tipo']}");
                }
            }
        }
        
        // =====================================================
        // PASO 11: LIMPIAR TABLA TEMPORAL
        // =====================================================
        $sqlCleanTmp = "DELETE FROM compra_tmp
                        WHERE idpersonal='$idpersonal'
                        AND idsucursal='$idsucursal'";
        ejecutarConsulta($sqlCleanTmp);
        
        // TODO OK → CONFIRMAR TRANSACCIÓN
        ejecutarConsulta("COMMIT");
        return true;
        
    } catch (Exception $e) {
        // ERROR → REVERTIR TODO
        ejecutarConsulta("ROLLBACK");
        error_log("Error en Compra->editar: " . $e->getMessage());
        return $e->getMessage(); // Devolver mensaje de error específico
    }
}

// MÉTODOS AUXILIARES PARA LA EDICIÓN

public function mostrarEditar($idcompra)
{
    $sql = "SELECT c.*, DATE_FORMAT(c.fecha_hora, '%Y-%m-%d') as fecha
            FROM compra c
            WHERE c.idcompra = '$idcompra'";
    return ejecutarConsultaSimpleFila($sql);
}

public function listarDetalleEdicion($idcompra)
{
    $sql = "SELECT 
                dc.*,
                p.idunidad_medida,
                CAST(dc.precio_compra AS DECIMAL(18,8)) + 0 AS precio_compra,
                CAST(dc.precio_venta AS DECIMAL(18,8)) + 0 AS precio_venta,
                COALESCE((
                    SELECT SUM(sf.cantidad_restante) 
                    FROM stock_fifo sf 
                    WHERE sf.origen = 'COMPRA' 
                    AND sf.referencia_id = dc.iddetalle_compra
                ), 0) AS fifo_restante,
                COALESCE((
                    SELECT SUM(sf.cantidad_ingreso - sf.cantidad_restante) 
                    FROM stock_fifo sf 
                    WHERE sf.origen = 'COMPRA' 
                    AND sf.referencia_id = dc.iddetalle_compra
                ), 0) AS cantidad_vendida
            FROM detalle_compra dc
            INNER JOIN producto p ON dc.idproducto = p.idproducto
            WHERE dc.idcompra = '$idcompra'
            ORDER BY dc.iddetalle_compra";
    return ejecutarConsulta($sql);
}

public function limpiarTemporal($idpersonal, $idsucursal)
{
    $sql = "DELETE FROM compra_tmp 
            WHERE idpersonal = '$idpersonal' 
            AND idsucursal = '$idsucursal'";
    return ejecutarConsulta($sql);
}

	public function subirImagen($idcompra,$imagen)
	{
		$sql="UPDATE compra SET imagen='$imagen', estado = 'REALIZADO' WHERE idcompra='$idcompra'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para anular categorías
	public function aprobar($idcompra)
	{
		$sql="UPDATE compra SET estadoC='APROBADO' WHERE idcompra='$idcompra'";
		return ejecutarConsulta($sql);
	}
    
	// Dentro de la clase Compra (método anular)
    public function anular($idcompra, $idcompraRef)
	{
	    // 0. Restaurar estado OC si aplica
	    if ($idcompraRef != '') {
	        ejecutarConsulta("UPDATE compra SET estadoC='APROBADO' WHERE idcompra='$idcompraRef'");
	    }

	    /* ===========================
	       1. VALIDAR STOCK
	    =========================== */
	    $sql_validacion = "
	        SELECT dc.idproducto, dc.cantidad, p.stock
	        FROM detalle_compra dc
	        INNER JOIN producto p ON dc.idproducto = p.idproducto
	        WHERE dc.idcompra='$idcompra'
	    ";
	    $res_validacion = ejecutarConsulta($sql_validacion);

	    while ($row = $res_validacion->fetch_object()) {
	        if ($row->stock < $row->cantidad) {
	            // Ya hubo ventas → no se puede anular
	            return false;
	        }
	    }

	    /* ===========================
	       2. ANULAR COMPRA
	    =========================== */
	    ejecutarConsulta("UPDATE compra SET estado='Anulado', estadoC='Anulado' WHERE idcompra='$idcompra'");
	    ejecutarConsulta("UPDATE cuentas_por_pagar SET condicion='0' WHERE idcompra='$idcompra'");

	    /* ===========================
	       3. 🔥 ANULAR FIFO DE LA COMPRA
	    =========================== */
	    $sql_fifo = "
	        UPDATE stock_fifo sf
	        INNER JOIN detalle_compra dc
	            ON sf.referencia_id = dc.iddetalle_compra
	        SET 
	            sf.estado = 0,
	            sf.cantidad_restante = 0
	        WHERE dc.idcompra = '$idcompra'
	          AND sf.origen = 'COMPRA'
	          AND sf.estado = 1
	    ";
	    ejecutarConsulta($sql_fifo);

	    /* ===========================
	       4. DEVOLVER STOCK + KARDEX
	    =========================== */
	    $sql_productos = "
	        SELECT dc.idproducto, dc.cantidad, p.stock, p.precio_compra, p.idsucursal
	        FROM detalle_compra dc
	        INNER JOIN producto p ON dc.idproducto = p.idproducto
	        WHERE dc.idcompra='$idcompra'
	    ";
	    $resultado = ejecutarConsulta($sql_productos);
	    $fechaActual = date('Y-m-d H:i:s');

	    while ($row = $resultado->fetch_object()) {
	        $nuevo_stock = $row->stock - $row->cantidad;

	        ejecutarConsulta("
	            UPDATE producto 
	            SET stock = '$nuevo_stock' 
	            WHERE idproducto = '{$row->idproducto}'
	        ");

	        ejecutarConsulta("
	            INSERT INTO kardex
	            (idsucursal, idproducto, cantidad, cantidad_contenedor, precio_unitario, stock_actual,
	             tipo_movimiento, motivo, descripcion, fecha_kardex)
	            VALUES
	            ('{$row->idsucursal}', '{$row->idproducto}', '{$row->cantidad}', 1,
	             '{$row->precio_compra}', '$nuevo_stock', 1, 'Compra anulada', '', '$fechaActual')
	        ");
	    }

	    return true;
	}


	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idcompra)
	{	
		$sql="SELECT i.idcompra,DATE(i.fecha_hora) as fecha,i.idproveedor,i.idsucursal,p.nombre as proveedor,u.idpersonal,u.nombre as personal,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.compracredito,i.formapago,i.total_compra,i.impuesto,i.estado 
		FROM compra i 
		INNER JOIN persona p ON i.idproveedor=p.idpersona 
		INNER JOIN personal u ON i.idpersonal=u.idpersonal
		INNER JOIN sucursal s ON i.idsucursal = s.idsucursal  
		WHERE i.idcompra='$idcompra'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrar2($idcompra)
	{	
		$sql="SELECT i.idcompra,i.idsucursal,DATE(i.fecha_hora) as fecha,i.idproveedor,p.nombre as proveedor,i.estadoC,u.idpersonal,u.nombre as personal,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado,i.formapago,i.lugar_entrega,i.motivo_compra,i.documento,i.nota FROM compra i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN personal u ON i.idpersonal=u.idpersonal WHERE i.idcompra='$idcompra'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function compradetalle($idcompra)
	{
		$sql="SELECT di.idcompra,di.idproducto,um.nombre as unidadmedida,a.nombre,di.cantidad,di.precio_compra,di.precio_venta 
			FROM detalle_compra di 
			inner join producto a 
			on di.idproducto=a.idproducto
			inner JOIN unidad_medida um
			on a.idunidad_medida = um.idunidad_medida where di.idcompra='$idcompra'";
		return ejecutarConsulta($sql);
	}

	public function listarDetalle($idcompra)
	{
		$sql="SELECT di.idcompra,di.idproducto,a.nombre,di.cantidad,di.precio_compra,di.precio_venta FROM detalle_compra di inner join producto a on di.idproducto=a.idproducto where di.idcompra='$idcompra'";
		return ejecutarConsulta($sql);
	}

	public function selectCompras($idsucursal)
	{

		$sql="SELECT i.idcompra,date_format(i.fecha_hora,'%d/%m/%y') as fecha,date_format(i.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,i.idproveedor,p.nombre as proveedor,u.idpersonal,u.nombre as personal,i.tipo_c,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado FROM compra i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN personal u ON i.idpersonal=u.idpersonal WHERE tipo_c = 'Orden Compra' AND estadoC = 'APROBADO' AND idsucursal = '$idsucursal' ORDER BY i.idcompra desc";
		return ejecutarConsulta($sql);

	}

	//Implementar un método para listar los registros
	public function listarReporte()
	{

		$sql="SELECT i.idcompra,date_format(i.fecha_hora,'%d/%m/%y') as fecha,date_format(i.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,i.idproveedor,p.nombre as proveedor,u.idpersonal,u.nombre as personal,i.tipo_c,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado FROM compra i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN personal u ON i.idpersonal=u.idpersonal WHERE tipo_c = 'Compra' ORDER BY i.idcompra desc";

		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros
	public function listar($fecha_inicio, $fecha_fin, $idsucursal)
	{

		if ($idsucursal == "Todos") {

			$sql="SELECT i.idcompra,date_format(i.fecha_hora,'%d/%m/%y') as fecha,date_format(i.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,i.idproveedor,p.nombre as proveedor,u.idpersonal,u.nombre as personal,i.tipo_c,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra, i.monto_gravado as gravadas, i.monto_exonerado as exoneradas, i.monto_igv as igv,i.impuesto,i.estado,i.imagen,i.documento_rel FROM compra i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN personal u ON i.idpersonal=u.idpersonal WHERE tipo_c = 'Compra' AND DATE(i.fecha_hora)>='$fecha_inicio' AND DATE(i.fecha_hora)<='$fecha_fin' ORDER BY i.idcompra desc";

		}else{

			$sql="SELECT i.idcompra,date_format(i.fecha_hora,'%d/%m/%y') as fecha,date_format(i.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,i.idproveedor,p.nombre as proveedor,u.idpersonal,u.nombre as personal,i.tipo_c,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.monto_gravado as gravadas, i.monto_exonerado as exoneradas, i.monto_igv as igv,i.impuesto,i.estado,i.imagen,i.documento_rel FROM compra i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN personal u ON i.idpersonal=u.idpersonal WHERE tipo_c = 'Compra' AND idsucursal = '$idsucursal' AND DATE(i.fecha_hora)>='$fecha_inicio' AND DATE(i.fecha_hora)<='$fecha_fin' ORDER BY i.idcompra desc";

		}

		return ejecutarConsulta($sql);		
	}

	public function listar2($fecha_inicio, $fecha_fin, $idsucursal)
	{
		if ($idsucursal == "Todos") {

			$sql="SELECT i.idcompra,date_format(i.fecha_hora,'%d/%m/%y') as fecha,date_format(i.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,i.idproveedor,p.nombre as proveedor,u.idpersonal,u.nombre as personal,i.tipo_c,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado,i.estadoC FROM compra i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN personal u ON i.idpersonal=u.idpersonal WHERE tipo_c != 'Compra' AND DATE(i.fecha_hora)>='$fecha_inicio' AND DATE(i.fecha_hora)<='$fecha_fin' ORDER BY i.idcompra desc";

		}else{

			$sql="SELECT i.idcompra,date_format(i.fecha_hora,'%d/%m/%y') as fecha,date_format(i.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,i.idproveedor,p.nombre as proveedor,u.idpersonal,u.nombre as personal,i.tipo_c,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado,i.estadoC FROM compra i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN personal u ON i.idpersonal=u.idpersonal WHERE tipo_c != 'Compra' AND DATE(i.fecha_hora)>='$fecha_inicio' AND DATE(i.fecha_hora)<='$fecha_fin' ORDER BY i.idcompra desc";

		}
		return ejecutarConsulta($sql);		
	}

	public function ingresocabecera($idcompra){
		$sql="SELECT i.idcompra,i.idproveedor,p.nombre as proveedor,p.direccion,p.tipo_documento,p.num_documento,p.email,p.telefono,i.idpersonal,u.nombre as personal,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,date(i.fecha_hora) as fecha,i.impuesto,i.total_compra FROM compra i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN personal u ON i.idpersonal=u.idpersonal WHERE i.idcompra='$idcompra'";
		return ejecutarConsulta($sql);
	}

	public function ingresodetalle($idcompra){
		$sql="SELECT a.nombre as producto,um.nombre as unidadmedida,CASE WHEN a.codigo = 'SIN CODIGO' THEN '-' ELSE a.codigo END as codigo,d.cantidad,d.precio_compra,d.precio_venta,(d.cantidad*d.precio_compra) as subtotal FROM detalle_compra d INNER JOIN producto a ON d.idproducto=a.idproducto INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida WHERE d.idcompra='$idcompra'";
		return ejecutarConsulta($sql);
	}

    public function exportarExcel($fecha_inicio, $fecha_fin, $estado, $idsucursal, $idproducto)
    {
        require_once '../vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // PROPIEDADES
        $spreadsheet->getProperties()
            ->setCreator("Sistema de Compras")
            ->setTitle("Reporte de Compras")
            ->setSubject("Reporte de Compras")
            ->setDescription("Reporte detallado de compras");

        // TITULO
        $sheet->mergeCells('A1:P1');
        $sheet->setCellValue('A1', 'REPORTE DE COMPRAS');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // FILTROS
        $filtrosTexto = "Período: " . date('d/m/Y', strtotime($fecha_inicio)) . " al " . date('d/m/Y', strtotime($fecha_fin));
        if (!empty($estado)) {
            $filtrosTexto .= " | Estado: " . $estado;
        }

        $sheet->mergeCells('A2:P2');
        $sheet->setCellValue('A2', $filtrosTexto);
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // CABECERA
        $headers = [
            'A4' => '#','B4' => 'FECHA','C4' => 'SUCURSAL','D4' => 'PROVEEDOR',
            'E4' => 'TIPO COMPROBANTE','F4' => 'SERIE','G4' => 'NÚMERO',
            'H4' => 'TIPO IGV','I4' => 'EXONERADO','J4' => 'GRAVADO',
            'K4' => 'IGV','L4' => 'TOTAL','M4' => 'FORMA PAGO',
            'N4' => 'ESTADO','O4' => 'USUARIO','P4' => 'OBSERVACIONES'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A4:P4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ]);

        // CONSULTA
        $sql = "SELECT c.idcompra, DATE_FORMAT(c.fecha_hora,'%d/%m/%Y %H:%i') fecha,
                s.nombre sucursal, p.nombre proveedor, c.tipo_comprobante,
                c.serie_comprobante, c.num_comprobante, c.tipo_igv,
                COALESCE(c.monto_exonerado,0) monto_exonerado,
                COALESCE(c.monto_gravado,0) monto_gravado,
                COALESCE(c.monto_igv,0) monto_igv,
                c.total_compra, c.formapago, c.estado,
                CONCAT(per.nombre) usuario, c.nota
                FROM compra c
                INNER JOIN sucursal s ON c.idsucursal=s.idsucursal
                INNER JOIN persona p ON c.idproveedor=p.idpersona
                INNER JOIN personal per ON c.idpersonal=per.idpersonal
                WHERE DATE(c.fecha_hora) BETWEEN '$fecha_inicio' AND '$fecha_fin' AND c.estado = 'REGISTRADO'";

        if (!empty($estado) && $estado !== 'undefined') {
            $sql .= " AND c.estado = '$estado'";
        }

        if (!empty($idsucursal)) {
            $sql .= " AND c.idsucursal = '$idsucursal'";
        }

        if (!empty($idproducto) && $idproducto !== 'undefined') {
            $sql .= " AND c.idcompra IN (
                SELECT DISTINCT idcompra 
                FROM detalle_compra 
                WHERE idproducto = '$idproducto'
            )";
        }

        $rs = ejecutarConsulta($sql);

        $fila = 5;
        $i = 1;

        $totalExonerado = 0;
        $totalGravado   = 0;
        $totalIGV       = 0;
        $totalGeneral   = 0;

        while ($r = $rs->fetch_object()) {
            $sheet->fromArray([
                $i++, $r->fecha, $r->sucursal, $r->proveedor,
                $r->tipo_comprobante, $r->serie_comprobante, $r->num_comprobante,
                $r->tipo_igv, $r->monto_exonerado, $r->monto_gravado,
                $r->monto_igv, $r->total_compra, $r->formapago,
                $r->estado, $r->usuario, $r->nota
            ], null, "A$fila");

            $totalExonerado += $r->monto_exonerado;
            $totalGravado   += $r->monto_gravado;
            $totalIGV       += $r->monto_igv;
            $totalGeneral   += $r->total_compra;
            $fila++;
        }

        // FILA DE TOTALES
        $sheet->mergeCells("A$fila:H$fila");
        $sheet->setCellValue("A$fila", "TOTALES");

        $sheet->setCellValue("I$fila", number_format($totalExonerado, 2));
        $sheet->setCellValue("J$fila", number_format($totalGravado, 2));
        $sheet->setCellValue("K$fila", number_format($totalIGV, 2));
        $sheet->setCellValue("L$fila", number_format($totalGeneral, 2));
        $sheet->getStyle("A$fila:P$fila")->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7E6E6']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
            ]
        ]);

        // Centrar el texto "TOTALES"
        $sheet->getStyle("A$fila")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // EXPORTAR
        while (ob_get_level()) { ob_end_clean(); }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte_Compras.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

}

?>