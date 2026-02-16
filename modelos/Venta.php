<?php
require "../configuraciones/Conexion.php";
date_default_timezone_set('America/Lima');
class Venta
{
	//implementamos nuestro constructor
	public function __construct()
	{
	}

	public function verificarCaja($idusuario, $idsucursal)
    {
        $sql = "SELECT ca.*
                FROM caja_apertura ca
                INNER JOIN cajas c ON c.idcaja = ca.idcaja
                WHERE ca.estado = 1 
                  AND ca.idusuario = '$idusuario'
                  AND ca.fecha_cierre IS NULL
                  AND c.idsucursal = '$idsucursal'
                LIMIT 1";

        $rpta = ejecutarConsultaSimpleFila($sql);
        if ($rpta) {
            return array('success' => true, 'idcaja' => $rpta['idcaja']);
        } else {
            return array('success' => false, 'idcaja' => 0);
        }
    }



	public function listarCajas($idsucursal)
	{
		$sql = "SELECT * FROM cajas WHERE estado = 1 AND idsucursal = '$idsucursal' AND deleted_at IS NULL";
		$cajas = ejecutarConsulta($sql);
		$data = array();
		while ($reg = $cajas->fetch_object()) {
			$data[] = $reg;
		}
		return $data;
	}


	public function aperturarCaja($idcaja, $monto_apertura, $idusuario)
	{
		$fechaActual = date('Y-m-d H:i:s');
		$sql = "INSERT INTO caja_apertura (idcaja, efectivo_apertura, idusuario, estado, fecha_apertura) 
		VALUES ('$idcaja', '$monto_apertura', '$idusuario', '1', '$fechaActual')";
		$rspta = ejecutarConsulta($sql);
		$data = array();
		if ($rspta) {
			$sqlupdate = "UPDATE cajas SET estado = '2' WHERE idcaja = '$idcaja'";
			ejecutarConsulta($sqlupdate);
			$data = array('success' => true, 'idcaja' => $idcaja);
		} else {
			$data = array('success' => false, 'idcaja' => 0);
		}
		return $data;
	}

    public function verPreciosItem($idproducto_configuracion, $idusuario)
    {
        $idproducto_configuracion = intval($idproducto_configuracion);
        $idusuario = intval($idusuario);

        $sql = "SELECT pcp.idnombre_p, np.descripcion, pcp.precio 
                FROM producto_configuracion_precios pcp
                INNER JOIN producto_configuracion pc ON pcp.producto_configuracion_id = pc.id
                INNER JOIN nombre_precios np ON np.idnombre_p = pcp.idnombre_p
                WHERE pcp.producto_configuracion_id = $idproducto_configuracion 
                AND np.estado = 1";

        // Si el usuario no es 1, se limita a nombre_precios con ID = 1
        if ($idusuario !== 1) {
            $sql .= " AND np.idnombre_p = 1";
        }

        $productos = ejecutarConsulta($sql);
        $data = array();
        while ($reg = $productos->fetch_object()) {
            $data[] = $reg;
        }

        return $data;
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

	public function insertar($idsucursal, $idcliente, $idpersonal, $idcaja, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto, $total_venta, $tipopago, $formapago, $nroOperacion, $fechaDepostivo, $porcentaje, $totalrecibido, $totaldeposito, $vuelto, $tipo, $banco, $idproducto, $nombre, $cantidad, $precio_venta, $descuento, $fechaOperacion, $montoDeuda, $montoPagado, $comprobanteReferencia, $idmotivo, $observaciones, $fecha_pago, $interes, $input_cuotas, $cantidad_contenedor, $contenedor, $idp, $check_precio, $id_fifo_lote, $idcategoria) { // Cambiado de id_detalle_compra_lote a id_fifo_lote
    global $conexion;

    // Validación sesión
    if (!isset($_SESSION['idpersonal']) || $_SESSION['idpersonal'] == null || $_SESSION['idpersonal'] == '') {
        header('Location: /salir');
        exit();
    }

    // Validar conexión a la BD
    if (!$conexion || $conexion->connect_errno) {
        error_log("Error de conexión a la base de datos: " . ($conexion ? $conexion->connect_error : 'Sin conexión'));
        return null;
    }

    // Validar conexión a internet
    $internetTest = @fsockopen("www.google.com", 80, $errno, $errstr, 2);
    if (!$internetTest) {
        error_log("No hay conexión a internet. Abortando venta.");
        return null;
    } else {
        fclose($internetTest);
    }

    if ($total_venta <= 0) {
        return null;
    }

    $dovEstado = "";
    if ($idcliente == "") { $idcliente = 6; }
    if ($tipo_comprobante == "Nota de Venta") {
        $estado = "Activado"; $dovEstado = "ACEPTADO";
    } else {
        $estado = "Por Enviar";
    }
    if ($serie_comprobante == "-" && $num_comprobante == "-") {
        $tipo_comprobante = "Anular";
    }

    // Generar número de comprobante si no hay
    if (empty($num_comprobante)) {
        $numc = "SELECT serie_comprobante, num_comprobante FROM venta WHERE tipo_comprobante = '$tipo_comprobante' AND idsucursal = '$idsucursal' ORDER BY idventa DESC LIMIT 1";
        $existeNum = ejecutarConsulta($numc);
        if (!$existeNum) { return null; }
        $v = 0;
        while ($regn = $existeNum->fetch_object()) {
            $c = $regn->serie_comprobante;
            $v = $regn->num_comprobante;
        }
        if (!empty($v)) {
            $serie_comprobante = $c;
            $num = $v + 1;
            $num_comprobante = str_pad($num, 7, "0", STR_PAD_LEFT);
        } else {
            $num_comprobante = '0000001';
        }
    }

    // Validar duplicados
    $existeComprobante = "SELECT * FROM venta WHERE serie_comprobante = '$serie_comprobante' AND num_comprobante = '$num_comprobante' AND idsucursal = '$idsucursal'";
    $existeCompro = ejecutarConsulta($existeComprobante);
    if (!$existeCompro) { return null; }
    if ($existeCompro->num_rows > 0) {
        $sqlUltimoC = "SELECT idventa, num_comprobante FROM venta WHERE tipo_comprobante = '$tipo_comprobante' AND idsucursal = '$idsucursal' ORDER BY idventa DESC LIMIT 1";
        $ultimoComprobante = ejecutarConsulta($sqlUltimoC);
        if (!$ultimoComprobante) { return null; }
        $var2 = 0;
        while ($reg = $ultimoComprobante->fetch_object()) {
            $var2 = $reg->num_comprobante;
        }
        if ($var2 > 0) {
            $num_comprobante = str_pad($var2 + 1, 7, "0", STR_PAD_LEFT);
        }
    }

    // Iniciar transacción
    $startTime = microtime(true);
    ejecutarConsulta("BEGIN");
    $formapagoVenta = $formapago;

    if (isset($_POST['metodo_pago']) && is_array($_POST['metodo_pago'])) {
        $metodos = array_filter($_POST['metodo_pago']);
        $metodos = array_values($metodos);
        if (count($metodos) > 1) {
            $formapagoVenta = "Mixto";
        } elseif (count($metodos) == 1) {
            $formapagoVenta = $metodos[0];
        }
    }
    
    $fechaActual = date('Y-m-d H:i:s');
    $sql = "INSERT INTO venta (idsucursal, idcaja, idcliente, idpersonal, idmotivo_nota, tipo_comprobante, serie_comprobante, num_comprobante, 
                fecha_hora, impuesto, total_venta, ventacredito, interes, formapago, meses, numoperacion, fechadeposito, 
                descuento, totalrecibido, totaldeposito, vuelto, banco, montoPagado, estado, documento_rel, dov_Estado, observacion, fecha_kardex) 
                VALUES ('$idsucursal','$idcaja','$idcliente','$idpersonal','$idmotivo','$tipo_comprobante','$serie_comprobante','$num_comprobante',
                        '$fecha_hora','$impuesto','$total_venta','$tipopago','$interes','$formapagoVenta','$input_cuotas','$nroOperacion','$fechaDepostivo',
                        '$porcentaje','$totalrecibido','$totaldeposito','$vuelto','$banco','$montoPagado','$estado','$comprobanteReferencia','$dovEstado',
                        '$observaciones','$fechaActual')";
    $idventanew = ejecutarConsulta_retornarID($sql);
    if (!$idventanew) { ejecutarConsulta("ROLLBACK"); return null; }

    // ========== INSERTAR PAGOS MIXTOS ==========
    // IMPORTANTE: No sobrescribir $tipopago, usar variable diferente
    $ventaCredito = $_POST['tipopago'] ?? 'No';
    
    // Solo insertar pagos si NO es crédito
    if($ventaCredito !== "Si"){
        if(isset($_POST['metodo_pago']) && is_array($_POST['metodo_pago'])){
            $metodos = $_POST['metodo_pago'];
            $montosReales = $_POST['monto_real_pago'] ?? [];
            $nros = $_POST['nroOperacion_pago'] ?? [];
            $bancos = $_POST['banco_pago'] ?? [];
            $fechas = $_POST['fecha_deposito_pago'] ?? [];

            // Validar que haya datos antes de insertar
            if(count($metodos) > 0 && count($montosReales) > 0){
                for($i = 0; $i < count($metodos); $i++){
                    // Validar que el método y monto existan
                    if(!empty($metodos[$i]) && isset($montosReales[$i])){
                        $montoPago = floatval($montosReales[$i]);
                        $metodoPago = mysqli_real_escape_string($conexion, $metodos[$i]);
                        $nroOp = isset($nros[$i]) && !empty($nros[$i]) ? mysqli_real_escape_string($conexion, $nros[$i]) : NULL;
                        $bancoVal = isset($bancos[$i]) && !empty($bancos[$i]) ? mysqli_real_escape_string($conexion, $bancos[$i]) : NULL;
                        $fechaDep = isset($fechas[$i]) && !empty($fechas[$i]) ? mysqli_real_escape_string($conexion, $fechas[$i]) : NULL;

                        // Construir query con NULL correctos
                        $nroOpSQL = $nroOp !== NULL ? "'$nroOp'" : "NULL";
                        $bancoSQL = $bancoVal !== NULL ? "'$bancoVal'" : "NULL";
                        $fechaDepSQL = $fechaDep !== NULL ? "'$fechaDep'" : "NULL";

                        $sqlPago = "INSERT INTO venta_pago (idventa, metodo_pago, monto, nroOperacion, banco, fechaDeposito) 
                                    VALUES ('$idventanew', '$metodoPago', '$montoPago', $nroOpSQL, $bancoSQL, $fechaDepSQL)";
                        
                        if(!ejecutarConsulta($sqlPago)){
                            error_log("Error al insertar pago mixto - Método: $metodoPago, Monto: $montoPago");
                            $sw = false;
                            break;
                        }
                    }
                }
            } else {
                error_log("Advertencia: No se recibieron datos de pagos mixtos en POST");
            }
        }
    }

    $num_elementos = 0;
    $sw = true;

    // ========== LÓGICA FIFO CORREGIDA ==========
    while ($num_elementos < count($idp)) {
        if ((microtime(true) - $startTime) > 15) {
            error_log("Timeout en venta - más de 15 segundos");
            $sw = false;
            break;
        }

        $id_producto_config = $idp[$num_elementos];
        $id_producto_real = $idproducto[$num_elementos];
        $cantidad_solicitada = $cantidad[$num_elementos];
        $id_fifo_sugerido = $id_fifo_lote[$num_elementos]; // El idfifo que viene del frontend
            // =====================================================
    //  SI ES SERVICIO → NO FIFO, NO STOCK, NO KARDEX
    // =====================================================
    if ($idcategoria[$num_elementos] == 1) {

        $sql_detalle_servicio = "INSERT INTO detalle_venta (
            idsucursal, idventa, idproducto, id_fifo, nombre_producto,
            cantidad, contenedor, cantidad_contenedor, precio_venta,
            descuento, tipo, check_precio
        ) VALUES (
            '$idsucursal', '$idventanew', '$id_producto_config', 0,
            '{$nombre[$num_elementos]}',
            '{$cantidad[$num_elementos]}',
            '{$contenedor[$num_elementos]}',
            1,
            '{$precio_venta[$num_elementos]}',
            '{$descuento[$num_elementos]}',
            '$tipo',
            '{$check_precio[$num_elementos]}'
        )";

        if (!ejecutarConsulta($sql_detalle_servicio)) {
            error_log("Error al insertar detalle de SERVICIO");
            $sw = false;
            break;
        }

        //  saltamos TODO el FIFO
        $num_elementos++;
        continue;
    }

        $factor_contenedor = $cantidad_contenedor[$num_elementos];
        $cantidad_total_unidades = $cantidad_solicitada * $factor_contenedor;
        $cantidad_restante_a_vender = $cantidad_total_unidades;

        // ========== OBTENER LOTES FIFO DISPONIBLES ==========
        $lotes_disponibles = null;
        
        // 1. Intentar usar el lote sugerido si tiene stock
        if ($id_fifo_sugerido != 0) {
            $sql_lote_especifico = "SELECT idfifo, cantidad_restante, precio_venta
                                    FROM stock_fifo
                                    WHERE idfifo = '$id_fifo_sugerido' 
                                      AND idproducto = '$id_producto_real'
                                      AND idsucursal = '$idsucursal'
                                      AND cantidad_restante > 0
                                      AND estado = 1
                                    LIMIT 1";
            $lotes_disponibles = ejecutarConsulta($sql_lote_especifico);
        }

        // 2. Si no hay lote específico o no tiene stock, usar FIFO normal
        if (!$lotes_disponibles || $lotes_disponibles->num_rows == 0 || $id_fifo_sugerido == 0) {
            $sql_fifo = "SELECT idfifo, cantidad_restante, precio_venta
                         FROM stock_fifo
                         WHERE idproducto = '$id_producto_real' 
                           AND idsucursal = '$idsucursal'
                           AND cantidad_restante > 0
                           AND estado = 1
                         ORDER BY fecha_ingreso ASC"; // FIFO: más antiguo primero
            $lotes_disponibles = ejecutarConsulta($sql_fifo);
        }

        if (!$lotes_disponibles) { 
            error_log("Error al consultar lotes FIFO para producto: $id_producto_real");
            $sw = false; 
            break; 
        }

        $stock_global_descontado = 0;

        // ========== DESCONTAR DE CADA LOTE FIFO ==========
        while ($lote = $lotes_disponibles->fetch_object() AND $cantidad_restante_a_vender > 0) {
            $cantidad_disponible_lote = floatval($lote->cantidad_restante);
            $id_lote_actual = $lote->idfifo;
            
            // CORRECCIÓN: Usar el precio enviado por el usuario, NO el del lote
            // $precio_venta_lote = $lote->precio_venta; // <--- ESTO ESTABA SOBRESCRIBIENDO TU SELECCIÓN
            $precio_venta_final = $precio_venta[$num_elementos]; 

            // Tomar lo que se pueda de este lote
            $cantidad_a_tomar = min($cantidad_restante_a_vender, $cantidad_disponible_lote);

            // ========== ACTUALIZAR STOCK_FIFO ==========
            $sql_update_fifo = "UPDATE stock_fifo 
                                SET cantidad_restante = cantidad_restante - '$cantidad_a_tomar' 
                                WHERE idfifo = '$id_lote_actual'";
            if (!ejecutarConsulta($sql_update_fifo)) { 
                error_log("Error al actualizar stock_fifo - idfifo: $id_lote_actual");
                $sw = false; 
                break 2; 
            }

            // ========== INSERTAR DETALLE_VENTA ==========
            $sql_detalle = "INSERT INTO detalle_venta (
                        idsucursal, idventa, idproducto, id_fifo, nombre_producto, 
                        cantidad, contenedor, cantidad_contenedor, precio_venta, 
                        descuento, tipo, check_precio
                    ) VALUES (
                        '$idsucursal', '$idventanew', '$id_producto_config', '$id_lote_actual',
                        '{$nombre[$num_elementos]}', '$cantidad_a_tomar', 
                        '{$contenedor[$num_elementos]}', '$factor_contenedor', 
                        '$precio_venta_final', '{$descuento[$num_elementos]}',  -- USAR LA VARIABLE CORREGIDA
                        '$tipo', '{$check_precio[$num_elementos]}'
                    )";
            if (!ejecutarConsulta($sql_detalle)) { 
                error_log("Error al insertar detalle_venta");
                $sw = false; 
                break 2; 
            }

            $cantidad_restante_a_vender -= $cantidad_a_tomar;
            $stock_global_descontado += $cantidad_a_tomar;
        }

        // ========== VALIDAR SI SE CUBRIÓ TODA LA CANTIDAD ==========
        if ($cantidad_restante_a_vender > 0) {
            error_log("Stock insuficiente en lotes FIFO para producto ID: {$id_producto_real}. Faltante: {$cantidad_restante_a_vender}");
            $sw = false;
            break;
        }

        // ========== ACTUALIZAR STOCK GLOBAL EN PRODUCTO ==========
        $sql_update_producto_stock = "UPDATE producto 
                                      SET stock = stock - '$stock_global_descontado' 
                                      WHERE idproducto = '$id_producto_real' 
                                        AND idsucursal = '$idsucursal'";
        if (!ejecutarConsulta($sql_update_producto_stock)) { 
            error_log("Error al actualizar stock global del producto: $id_producto_real");
            $sw = false; 
            break; 
        }

        // ========== INSERTAR EN KARDEX ==========
        $sql_kardex = "INSERT INTO kardex (
                            idsucursal, idproducto, cantidad, cantidad_contenedor, 
                            precio_unitario, stock_actual, tipo_movimiento, 
                            motivo, descripcion, fecha_kardex
                       ) VALUES (
                            '$idsucursal', '$id_producto_real', '$stock_global_descontado', 
                            '$factor_contenedor', '{$precio_venta[$num_elementos]}', 
                            (SELECT stock FROM producto WHERE idproducto = '$id_producto_real' AND idsucursal = '$idsucursal'), 
                            1, 'Venta', 'Venta #$num_comprobante', '$fechaActual'
                       )";
        if (!ejecutarConsulta($sql_kardex)) { 
            error_log("Error al insertar kardex");
            $sw = false; 
            break; 
        }

        $num_elementos++;
    }

    // ========== MANEJO DE CRÉDITO ==========
    if ($tipopago == 'Si' && $sw) {
        $cuotas = 0;
        $monto_cuota = round(($montoDeuda * ($interes / 100) + $montoDeuda) / $input_cuotas, 1);
        while ($cuotas < count($fecha_pago)) {
            if (!ejecutarConsulta("INSERT INTO cuentas_por_cobrar (idventa, fecharegistro, deudatotal, fechavencimiento, abonototal, deuda, interes) 
                                   VALUES ('$idventanew', '$fecha_hora', '$monto_cuota', '$fecha_pago[$cuotas]', 0, '$monto_cuota', 0)")) {
                $sw = false; break;
            }
            $cuotas++;
        }
    }

    // ========== ACTUALIZAR COTIZACIÓN SI APLICA ==========
    if ($comprobanteReferencia != '' && $tipo == 'venta' && $sw) {
        if (!ejecutarConsulta("UPDATE cotizacion SET estado = 'VENDIDO' WHERE idcotizacion = '$comprobanteReferencia'")) {
            $sw = false;
        }
    }

    // ========== COMMIT O ROLLBACK ==========
    if ($sw) {
        ejecutarConsulta("COMMIT");
        return $idventanew;
    } else {
        ejecutarConsulta("ROLLBACK");
        error_log("ROLLBACK ejecutado - venta cancelada");
        return null;
    }
}

public function editar($idventa, $idsucursal, $idcliente, $idpersonal, $idcaja, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto, $total_venta, $tipopago, $formapago, $nroOperacion, $fechaDepostivo, $porcentaje, $totalrecibido, $totaldeposito, $vuelto, $tipo, $banco, $idproducto, $nombre, $cantidad, $precio_venta, $descuento, $fechaOperacion, $montoDeuda, $montoPagado, $comprobanteReferencia, $idmotivo, $observaciones, $fecha_pago, $interes, $input_cuotas, $cantidad_contenedor, $contenedor, $idp, $check_precio) 
{
    $fechaActual = date('Y-m-d H:i:s');

    // Obtener productos ya existentes en el detalle
    $productosExistentes = [];
    $res = ejecutarConsulta("SELECT idproducto FROM detalle_venta WHERE idventa = '$idventa'");
    while ($row = $res->fetch_object()) {
        $productosExistentes[] = $row->idproducto;
    }

    // Procesar productos enviados desde el formulario
    for ($i = 0; $i < count($idproducto); $i++) {
        $existe = in_array($idp[$i], $productosExistentes);

        if (!$existe) {
            // INSERTAR nuevo producto
            $sql_detalle = "INSERT INTO detalle_venta (idsucursal, idventa, idproducto, nombre_producto, cantidad, contenedor, cantidad_contenedor, precio_venta, descuento, tipo, check_precio) 
                VALUES ('$idsucursal','$idventa','$idp[$i]','','$cantidad[$i]','$contenedor[$i]','$cantidad_contenedor[$i]','$precio_venta[$i]','$descuento[$i]','$tipo','{$check_precio[$i]}')";
            ejecutarConsulta($sql_detalle);

            // Stock y kardex
            $producto = ejecutarConsulta("SELECT * FROM producto WHERE idproducto = '$idproducto[$i]' AND idsucursal = '$idsucursal'")->fetch_object();
            $cantidad_vendida = $cantidad[$i] * $cantidad_contenedor[$i];
            $nuevo_stock = $producto->stock - $cantidad_vendida;

            ejecutarConsulta("INSERT INTO kardex (idsucursal, idproducto, cantidad, cantidad_contenedor, precio_unitario, stock_actual, tipo_movimiento, motivo, descripcion, fecha_kardex) 
                VALUES ('$idsucursal', '$idproducto[$i]', '$cantidad_vendida', '$cantidad_contenedor[$i]', '$precio_venta[$i]', '$nuevo_stock', 1, 'Venta Actualizada', '', '$fechaActual')");

            ejecutarConsulta("UPDATE producto SET stock = $nuevo_stock WHERE idproducto = '$idproducto[$i]'");
        } else {
            // ACTUALIZAR producto existente
            $this->verificarProducto(
                $idp[$i], // ID que ya estaba en detalle_venta
                $idproducto[$i], // ID del producto actual del form
                $idventa,
                $cantidad[$i],
                $precio_venta[$i],
                $cantidad_contenedor[$i],
                $contenedor[$i],
                $idsucursal,
                $descuento[$i]
            );
        }
    }

    // ELIMINAR productos que ya no están
    foreach ($productosExistentes as $prodExistente) {
        if (!in_array($prodExistente, $idp)) {
            $detalle = ejecutarConsulta("SELECT * FROM detalle_venta WHERE idventa = '$idventa' AND idproducto = '$prodExistente'")->fetch_object();
            $producto = ejecutarConsulta("SELECT * FROM producto WHERE idproducto = '$prodExistente' AND idsucursal = '$idsucursal'")->fetch_object();

            $cantidad_devuelta = $detalle->cantidad * $detalle->cantidad_contenedor;
            $nuevo_stock = $producto->stock + $cantidad_devuelta;

            ejecutarConsulta("INSERT INTO kardex (idsucursal, idproducto, cantidad, cantidad_contenedor, precio_unitario, stock_actual, tipo_movimiento, motivo, descripcion, fecha_kardex) 
                VALUES ('$idsucursal', '$prodExistente', '$cantidad_devuelta', '$detalle->cantidad_contenedor', '$detalle->precio_venta', '$nuevo_stock', 0, 'Detalle de venta eliminado', '', '$fechaActual')");

            ejecutarConsulta("UPDATE producto SET stock = $nuevo_stock WHERE idproducto = '$prodExistente'");
            ejecutarConsulta("DELETE FROM detalle_venta WHERE idventa = '$idventa' AND idproducto = '$prodExistente'");
        }
    }

    // Eliminar cuentas por cobrar existentes
    ejecutarConsulta("DELETE FROM cuentas_por_cobrar WHERE idventa = '$idventa'");

    // Registrar nuevas cuentas por cobrar si aplica
    if ($tipopago == 'Si') {
        // Usar interes como en insertar()
        $monto_cuota = round(($montoDeuda * ($interes / 100) + $montoDeuda) / $input_cuotas, 1);

        for ($c = 0; $c < count($fecha_pago); $c++) {
            $sql_cpc = "INSERT INTO cuentas_por_cobrar
                        (idventa, fecharegistro, deudatotal, fechavencimiento, abonototal, deuda, interes)
                        VALUES ('$idventa', '$fecha_hora', '$monto_cuota', '{$fecha_pago[$c]}', 0, '$monto_cuota', '$interes')";
            ejecutarConsulta($sql_cpc);
        }
    }
    // ------------------ Determinar formapago según metodos enviados (igual que insertar) ------------------
    $formapagoVenta = $formapago; // por defecto el que viene por parámetro

    if ($tipopago === "Si") {
        // 🔹 Si es crédito, forzar "Credito"
        $formapagoVenta = "Credito";
    } elseif (isset($_POST['metodo_pago']) && is_array($_POST['metodo_pago'])) {
        $metodos = $_POST['metodo_pago'];
        $montosReales = isset($_POST['monto_real_pago']) ? $_POST['monto_real_pago'] : array();

        // Contar sólo métodos con monto > 0
        $metodosValidos = [];
        for ($i = 0; $i < count($metodos); $i++) {
            $montoPagoTmp = isset($montosReales[$i]) ? floatval($montosReales[$i]) : 0;
            $metodoTmp = trim($metodos[$i]);
            if ($montoPagoTmp > 0 && $metodoTmp !== '') {
                $metodosValidos[] = $metodoTmp;
            }
        }

        if (count($metodosValidos) > 1) {
            $formapagoVenta = "Mixto";
        } elseif (count($metodosValidos) == 1) {
            $formapagoVenta = $metodosValidos[0];
        }
    }

    // ---------------------------------------------------------------------------------------------------------

    // Actualizar venta (usar formapago calculado)
    ejecutarConsulta("UPDATE venta SET 
        idcliente='$idcliente', 
        total_venta='$total_venta',
        ventacredito='$tipopago',
        formapago='$formapagoVenta',
        numoperacion='$nroOperacion',
        fechadeposito='$fechaDepostivo',
        totalrecibido='$totalrecibido',
        totaldeposito='$totaldeposito',
        meses='$input_cuotas',
        vuelto='$vuelto',
        fecha_hora='$fecha_hora'
        WHERE idventa='$idventa'");

    // --------- PAGOS: borrar los viejos y grabar los nuevos (venta_pago) ----------
    ejecutarConsulta("DELETE FROM venta_pago WHERE idventa = '$idventa'");

    if ($tipopago !== "Si") { // si NO es crédito, insertamos pagos
        if (isset($_POST['metodo_pago']) && is_array($_POST['metodo_pago'])) {
            $metodos = $_POST['metodo_pago'];
            $montosReales = isset($_POST['monto_real_pago']) ? $_POST['monto_real_pago'] : array();
            $nros = isset($_POST['nroOperacion_pago']) ? $_POST['nroOperacion_pago'] : array();
            $bancos = isset($_POST['banco_pago']) ? $_POST['banco_pago'] : array();
            $fechas = isset($_POST['fecha_deposito_pago']) ? $_POST['fecha_deposito_pago'] : array();

            for ($i = 0; $i < count($metodos); $i++) {
                $montoPago = floatval(isset($montosReales[$i]) ? $montosReales[$i] : 0);
                if ($montoPago <= 0) continue;

                $metodoPago = $metodos[$i];
                $nroOp = isset($nros[$i]) ? $nros[$i] : '';
                $ban = isset($bancos[$i]) ? $bancos[$i] : '';
                $fechaDep = isset($fechas[$i]) ? $fechas[$i] : '';

                $sqlPago = "INSERT INTO venta_pago (idventa, metodo_pago, monto, nroOperacion, banco, fechaDeposito) 
                            VALUES ('$idventa','$metodoPago','$montoPago','$nroOp','$ban','$fechaDep')";
                if (!ejecutarConsulta($sqlPago)) {
                    // si falla, puedes lanzar rollback aquí (si estás manejando transacciones)
                }
            }
        }
    }

    return $idventa;
}



	public function verificarProducto($idp, $idproducto, $idventa, $cantidad, $precio_venta, $cantidad_contenedor, $contenedor, $idsucursal, $descuento = 0, $check_precio = 0)
{
    $sql = "SELECT * FROM detalle_venta WHERE idventa = '$idventa' AND idproducto = '$idp'";
    $result = ejecutarConsulta($sql)->fetch_object();
    $fechaActual = date('Y-m-d H:i:s');

    if ($result) {
        $cambioCantidad = ($result->cantidad != $cantidad);
        $cambioPrecio   = ($result->precio_venta != $precio_venta);
        $cambioDesc     = ($result->descuento != $descuento);
        $cambioCheck    = (isset($result->check_precio) ? $result->check_precio != $check_precio : false);

        // Siempre actualizar detalle si cambia cantidad, precio o descuento
        if ($cambioCantidad || $cambioPrecio || $cambioDesc || $cambioCheck) {
            ejecutarConsulta("UPDATE detalle_venta 
                SET cantidad = '$cantidad',
                    precio_venta = '$precio_venta',
                    descuento = '$descuento',
                    contenedor = '$contenedor',
                    cantidad_contenedor = '$cantidad_contenedor'
                    " . (isset($result->check_precio) ? ", check_precio = '$check_precio'" : "") . "
                WHERE idventa = '$idventa' AND idproducto = '$idp'");
        }

        // Ajustar stock y kardex solo si cambia cantidad
        if ($cambioCantidad) {
            $producto = ejecutarConsulta("SELECT * FROM producto WHERE idproducto = '$idproducto' AND idsucursal = '$idsucursal'")->fetch_object();

            if ($result->cantidad > $cantidad) {
                // Venta reducida → aumentar stock
                $new_cantidad = $result->cantidad - $cantidad;
                $cantidad_vendida = $new_cantidad * $cantidad_contenedor;
                $nuevo_stock = $producto->stock + $cantidad_vendida;

                ejecutarConsulta("INSERT INTO kardex (idsucursal, idproducto, cantidad, cantidad_contenedor, precio_unitario, stock_actual, tipo_movimiento, motivo, descripcion, fecha_kardex) 
                    VALUES ('$idsucursal', '$idproducto', '$cantidad_vendida', '$cantidad_contenedor', '$precio_venta', '$nuevo_stock', 0, 'Venta Actualizada', '', '$fechaActual')");
                ejecutarConsulta("UPDATE producto SET stock = $nuevo_stock WHERE idproducto = '$idproducto'");
            } elseif ($result->cantidad < $cantidad) {
                // Venta aumentada → reducir stock
                $new_cantidad = $cantidad - $result->cantidad;
                $cantidad_vendida = $new_cantidad * $cantidad_contenedor;
                $nuevo_stock = $producto->stock - $cantidad_vendida;

                ejecutarConsulta("INSERT INTO kardex (idsucursal, idproducto, cantidad, cantidad_contenedor, precio_unitario, stock_actual, tipo_movimiento, motivo, descripcion, fecha_kardex) 
                    VALUES ('$idsucursal', '$idproducto', '$cantidad_vendida', '$cantidad_contenedor', '$precio_venta', '$nuevo_stock', 1, 'Venta Actualizada', '', '$fechaActual')");
                ejecutarConsulta("UPDATE producto SET stock = $nuevo_stock WHERE idproducto = '$idproducto'");
            }
        }
    }
}

	public function notaCredito($comprobanteReferencia, $idsucursal, $idmotivo)
    {
        if ($comprobanteReferencia == '') return false;

        $sx = true;
        $fechaActual = date('Y-m-d H:i:s');

        // =============================
        // OBTENER VENTA ORIGINAL
        // =============================
        $venta = ejecutarConsulta("SELECT * FROM venta WHERE idventa='$comprobanteReferencia'")->fetch_object();
        if (!$venta) return false;

        // =============================
        // DEVOLUCIÓN DE STOCK + KARDEX
        // =============================
        if ($idmotivo != 7) {
            // 🔹 DEVOLUCIÓN TOTAL (NO SE TOCA FUNCIONALIDAD EXISTENTE)
            $detalles = ejecutarConsulta("SELECT * FROM detalle_venta WHERE idventa='$venta->idventa'");
            while ($reg = $detalles->fetch_object()) {

                $producto = ejecutarConsulta("
                    SELECT p.*, pg.cantidad_contenedor
                    FROM producto_configuracion pg
                    INNER JOIN producto p ON p.idproducto = pg.idproducto
                    WHERE pg.id='$reg->idproducto'
                ")->fetch_object();

                $cantidad_real = $reg->cantidad * $producto->cantidad_contenedor;
                // devolver al FIFO correcto
                if (!empty($reg->id_fifo)) {
                    ejecutarConsulta("
                        UPDATE stock_fifo
                        SET cantidad_restante = cantidad_restante + $cantidad_real
                        WHERE idfifo = '$reg->id_fifo'
                          AND estado = 1
                    ");
                }
                $nuevo_stock = $producto->stock + $cantidad_real;

                ejecutarConsulta("UPDATE producto SET stock='$nuevo_stock' WHERE idproducto='$producto->idproducto'");

                ejecutarConsulta("
                    INSERT INTO kardex
                    (idsucursal,idproducto,cantidad,precio_unitario,stock_actual,tipo_movimiento,motivo,descripcion,fecha_kardex)
                    VALUES
                    ('$idsucursal','$producto->idproducto','$cantidad_real','$producto->precio','$nuevo_stock',0,
                     'ANULACIÓN / DEVOLUCIÓN TOTAL','Nota de crédito','$fechaActual')
                ");
            }
        } else {
            // 🔹 DEVOLUCIÓN POR ÍTEM
            for ($i = 0; $i < count($_POST['idproducto']); $i++) {

                $idproducto = $_POST['idproducto'][$i];
                $cantidad = $_POST['cantidad'][$i];
                $cant_cont = $_POST['cantidad_contenedor'][$i];

                $producto = ejecutarConsulta("SELECT * FROM producto WHERE idproducto='$idproducto'")->fetch_object();
                if (!$producto) continue;

                $cantidad_real = $cantidad * $cant_cont;
                $nuevo_stock = $producto->stock + $cantidad_real;

                ejecutarConsulta("UPDATE producto SET stock='$nuevo_stock' WHERE idproducto='$idproducto'");

                ejecutarConsulta("
                    INSERT INTO kardex
                    (idsucursal,idproducto,cantidad,precio_unitario,stock_actual,tipo_movimiento,motivo,descripcion,fecha_kardex)
                    VALUES
                    ('$idsucursal','$idproducto','$cantidad_real','$producto->precio','$nuevo_stock',0,
                     'DEVOLUCIÓN POR ÍTEM','Nota de crédito','$fechaActual')
                ");
            }
        }

        // =============================
        // CUENTAS POR COBRAR
        // =============================
        ejecutarConsulta("UPDATE cuentas_por_cobrar SET condicion='0' WHERE idventa='$comprobanteReferencia'");

        // =============================
        // ESTADO VENTA ORIGINAL
        // =============================
        ejecutarConsulta("
            UPDATE venta SET estado=
            CASE 
                WHEN tipo_comprobante='Nota de Venta' THEN 'Anulado'
                ELSE IF('$idmotivo'=7,'Nota Credito','Nota Credito')
            END
            WHERE idventa='$comprobanteReferencia'
        ");

        // =============================
        // TIPO NC
        // =============================
        $tipoNC = ($venta->tipo_comprobante == 'Boleta') ? 'NCB' : 'NC';

        // =============================
        // SERIE / NÚMERO NC
        // =============================
        $ult = ejecutarConsulta("
            SELECT serie_comprobante,num_comprobante
            FROM venta
            WHERE tipo_comprobante='$tipoNC' AND idsucursal='$idsucursal'
            ORDER BY idventa DESC LIMIT 1
        ")->fetch_object();

        if ($ult) {
            $serie = $ult->serie_comprobante;
            $numero = str_pad($ult->num_comprobante + 1, 7, '0', STR_PAD_LEFT);
        } else {
            $serie = ($tipoNC == 'NCB') ? 'BN02' : 'FN02';
            $numero = '0000001';
        }

        // =============================
        // INSERTAR CABECERA NC
        // =============================
        $sqlNC = "
            INSERT INTO venta
            (idsucursal,idcaja,idcliente,idPersonal,idmotivo_nota,tipo_comprobante,
             serie_comprobante,num_comprobante,fecha_hora,impuesto,total_venta,
             estado,documento_rel,fecha_kardex)
            VALUES
            ('$venta->idsucursal','$venta->idcaja','$venta->idcliente','$venta->idPersonal','$idmotivo',
             '$tipoNC','$serie','$numero','$fechaActual','$venta->impuesto','$venta->total_venta',
             'Por Enviar','$venta->idventa','$fechaActual')
        ";

        $idNC = ejecutarConsulta_retornarID($sqlNC);

        // =============================
        // DETALLE NC
        // =============================
        if ($idmotivo != 7) {
            $detalles = ejecutarConsulta("SELECT * FROM detalle_venta WHERE idventa='$venta->idventa'");
            while ($reg = $detalles->fetch_object()) {
                ejecutarConsulta("
                    INSERT INTO detalle_venta
                    (idsucursal,idventa,idproducto,nombre_producto,cantidad,contenedor,
                     cantidad_contenedor,precio_venta,descuento,tipo,id_fifo)
                    VALUES
                    ('$idsucursal','$idNC','$reg->idproducto','$reg->nombre_producto',
                     '$reg->cantidad','$reg->contenedor','$reg->cantidad_contenedor',
                     '$reg->precio_venta','$reg->descuento','NC','$reg->id_fifo')
                ");
            }
        } else {
            for ($i = 0; $i < count($_POST['idproducto']); $i++) {
                ejecutarConsulta("
                    INSERT INTO detalle_venta
                    (idsucursal,idventa,idproducto,nombre_producto,cantidad,contenedor,
                     cantidad_contenedor,precio_venta,descuento,tipo,id_fifo)
                    VALUES
                    ('$idsucursal','$idNC',
                     '{$_POST['idproducto'][$i]}',
                     '{$_POST['nombreProducto'][$i]}',
                     '{$_POST['cantidad'][$i]}',
                     '{$_POST['contenedor'][$i]}',
                     '{$_POST['cantidad_contenedor'][$i]}',
                     '{$_POST['precio_venta'][$i]}',
                     '{$_POST['descuento'][$i]}','NC'),
                     '{$_POST['id_fifo'][$i]}')
                ");
            }
        }

        ejecutarConsulta("UPDATE cotizacion SET estado='COMPRADO' WHERE idcotizacion='$comprobanteReferencia'");

        return $sx;
    }

	public function anular($idventa, $idsucursal)
    {
        $verifydata = "SELECT * FROM detalle_venta WHERE idventa='$idventa'";
        $list = ejecutarConsulta($verifydata);
        if (!$list) return false;

        $fechaActual = date('Y-m-d H:i:s');

        while ($reg = $list->fetch_object()) {

            $verifyproduct = "
                SELECT p.*, pg.cantidad_contenedor
                FROM producto_configuracion pg
                INNER JOIN producto p ON p.idproducto = pg.idproducto
                WHERE pg.id ='$reg->idproducto'
            ";
            $producto = ejecutarConsulta($verifyproduct)->fetch_object();
            if (!$producto) continue;

            $cantidad_real = $reg->cantidad * $producto->cantidad_contenedor;

            // =============================
            // DEVOLVER AL FIFO ORIGINAL
            // =============================
            if (!empty($reg->id_fifo)) {
                ejecutarConsulta("
                    UPDATE stock_fifo
                    SET cantidad_restante = cantidad_restante + $cantidad_real
                    WHERE idfifo = '$reg->id_fifo'
                      AND estado = 1
                ");
            }

            // =============================
            // ACTUALIZAR STOCK GENERAL
            // =============================
            $nuevo_stock = $producto->stock + $cantidad_real;

            ejecutarConsulta("
                UPDATE producto 
                SET stock = '$nuevo_stock' 
                WHERE idproducto = '$producto->idproducto'
            ");

            // =============================
            // KARDEX
            // =============================
            ejecutarConsulta("
                INSERT INTO kardex
                (idsucursal,idproducto,cantidad,cantidad_contenedor,precio_unitario,
                 stock_actual,tipo_movimiento,motivo,descripcion,fecha_kardex)
                VALUES
                ('$idsucursal','$producto->idproducto','$reg->cantidad',
                 '$producto->cantidad_contenedor','$producto->precio',
                 '$nuevo_stock',0,'Nota de Venta anulada','','$fechaActual')
            ");
        }

        // =============================
        // CUENTAS POR COBRAR
        // =============================
        ejecutarConsulta("
            UPDATE cuentas_por_cobrar 
            SET condicion='0' 
            WHERE idventa='$idventa'
        ");

        // =============================
        // ESTADO VENTA
        // =============================
        return ejecutarConsulta("
            UPDATE venta 
            SET estado='Anulado' 
            WHERE idventa='$idventa'
        ");
    }

	public function anularR($idventa, $idsucursal)
	{
		// Obtener los detalles de la venta
		$verifydata = "SELECT * FROM detalle_venta WHERE idventa='$idventa'";
		$list = ejecutarConsulta($verifydata);

		while ($reg = $list->fetch_object()) {
			// Obtener información del producto
			$verifyproduct = "SELECT * FROM producto_configuracion pg, producto p WHERE p.idproducto = pg.idproducto AND pg.id ='$reg->idproducto'";
			$producto = ejecutarConsulta($verifyproduct)->fetch_object();

			// Calcular el nuevo stock
			$nuevo_stock = $producto->stock + $reg->cantidad * $producto->cantidad_contenedor;

			// Actualizar el stock del producto
			$producto_update = "UPDATE producto SET stock = '$nuevo_stock' WHERE idproducto = '$producto->idproducto'";
			ejecutarConsulta($producto_update);

			// Registrar movimiento en el kardex
			$insert = "INSERT INTO kardex (idsucursal, idproducto, cantidad, precio_unitario, stock_actual, tipo_movimiento, motivo, descripcion) VALUES ('$idsucursal', '$producto->idproducto', '$reg->cantidad', '$producto->precio', '$nuevo_stock', 0, 'Venta rechazada', '')";
			ejecutarConsulta($insert);
		}

		// Actualizar la condición de cuentas por cobrar
		$sql1 = "UPDATE cuentas_por_cobrar SET condicion='0' WHERE idventa='$idventa'";
		ejecutarConsulta($sql1);

		// Actualizar el estado de la venta a 'Anulado'
		$sql = "UPDATE venta SET estado='Rechazado' WHERE idventa='$idventa'";
		ejecutarConsulta($sql);
	}


	public function cambiarEstado($idventa, $estado)
	{
		$sql = "UPDATE venta SET estadoS='$estado' WHERE idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	//implementar un metodopara mostrar los datos de unregistro a modificar
	public function mostrar($idventa)
	{
	    $sql = "SELECT v.idventa, DATE(v.fecha_hora) as fecha,DATE(v.fecha_kardex) as fechahora ,c.idcaja as caja, s.idsucursal as sucursal, v.idcliente, p.nombre as cliente, u.idpersonal, u.nombre as personal, p.telefono, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, v.total_venta, v.impuesto, v.ventacredito, v.formapago, v.meses,v.observacion, v.descuento, v.totalrecibido,cpc.deudatotal,SUM(dcpc.montopagado) as montopagado, v.vuelto, vp.nroOperacion, DATE(vp.fechaDeposito) as fechaDeposito, v.estado,vp.banco
	            FROM venta v 
	            INNER JOIN persona p ON v.idcliente = p.idpersona
                LEFT JOIN venta_pago vp ON v.idventa = vp.idventa 
	            INNER JOIN personal u ON v.idpersonal = u.idpersonal 
	            INNER JOIN cajas c ON v.idcaja = c.idcaja
	            INNER JOIN sucursal s ON v.idsucursal = s.idsucursal
				LEFT JOIN cuentas_por_cobrar cpc ON v.idventa = cpc.idventa
				LEFT JOIN detalle_cuentas_por_cobrar dcpc ON cpc.idcpc = dcpc.idcpc
	            WHERE v.idventa  = '$idventa'
	            GROUP BY v.idventa";
	    return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarEdit($idventa)
{
    // Traer datos principales de la venta (igual que antes, agrupando por venta)
    $sql = "SELECT v.idventa, DATE(v.fecha_hora) as fecha, DATE(v.fecha_kardex) as fechahora, c.idcaja as caja, s.idsucursal as sucursal,
                   v.idcliente, p.nombre as cliente, u.idpersonal, u.nombre as personal, p.telefono,
                   v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, v.total_venta, v.impuesto,
                   v.ventacredito, v.formapago, v.meses, v.observacion, v.descuento, v.totalrecibido,
                   cpc.deudatotal, SUM(dcpc.montopagado) as montopagado, v.vuelto, v.numoperacion,
                   DATE(v.fechadeposito) as fechadeposito, v.estado, cpc.fecharegistro, cpc.deudatotal as cpc_deudatotal
            FROM venta v
            INNER JOIN persona p ON v.idcliente = p.idpersona
            INNER JOIN personal u ON v.idpersonal = u.idpersonal
            INNER JOIN cajas c ON v.idcaja = c.idcaja
            INNER JOIN sucursal s ON v.idsucursal = s.idsucursal
            LEFT JOIN cuentas_por_cobrar cpc ON v.idventa = cpc.idventa
            LEFT JOIN detalle_cuentas_por_cobrar dcpc ON cpc.idcpc = dcpc.idcpc
            WHERE v.idventa = '$idventa'
            GROUP BY v.idventa";

    $venta = ejecutarConsultaSimpleFila($sql);

    // Ahora traer los pagos relacionados (venta_pago)
    $sqlPagos = "SELECT metodo_pago, monto, nroOperacion, banco, DATE(fechaDeposito) AS fechaDeposito
                 FROM venta_pago
                 WHERE idventa = '$idventa'
                 ORDER BY idventa";
    $rsPagos = ejecutarConsulta($sqlPagos);

    $pagos = array();
    if ($rsPagos) {
        while ($p = $rsPagos->fetch_object()) {
            $pagos[] = $p;
        }
    }

    // Adjuntar pagos al objeto/array venta para que el JS reciba data.pagos
    if (is_object($venta)) {
        $venta->pagos = $pagos;
    } elseif (is_array($venta)) {
        $venta['pagos'] = $pagos;
    } else {
        // si no hay venta, devolver al menos el array pagos vacío en un objeto
        $venta = new stdClass();
        $venta->pagos = $pagos;
    }

    return $venta;
}

	public function listarCuotas($idventa){
	    $sql = "SELECT cpc.idcpc, cpc.fecharegistro, cpc.deudatotal, cpc.fechavencimiento, dcpc.montopagado 
	            FROM cuentas_por_cobrar cpc 
	            LEFT JOIN detalle_cuentas_por_cobrar dcpc ON cpc.idcpc = dcpc.idcpc 
	            WHERE cpc.idventa = '$idventa'";
	    return ejecutarConsulta($sql);
	}


	public function mostrarPOS($idventa)
	{
	    $sql = "SELECT v.idventa, DATE(v.fecha_hora) as fecha,DATE(v.fecha_kardex) as fechahora ,c.idcaja as caja, s.idsucursal as sucursal, v.idcliente, p.nombre as cliente, u.idpersonal, u.nombre as personal, p.telefono, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, v.total_venta, v.impuesto, v.ventacredito, v.formapago, v.meses,v.observacion, v.descuento, v.totalrecibido,cpc.deudatotal,SUM(dcpc.montopagado) as montopagado, v.vuelto, v.numoperacion, DATE(v.fechadeposito) as fechadeposito, v.estado 
	            FROM venta v 
	            INNER JOIN persona p ON v.idcliente = p.idpersona 
	            INNER JOIN personal u ON v.idpersonal = u.idpersonal 
	            INNER JOIN cajas c ON v.idcaja = c.idcaja
	            INNER JOIN sucursal s ON v.idsucursal = s.idsucursal
				LEFT JOIN cuentas_por_cobrar cpc ON v.idventa = cpc.idventa
				LEFT JOIN detalle_cuentas_por_cobrar dcpc ON cpc.idcpc = dcpc.idcpc
	            WHERE v.idventa  = '$idventa'
	            GROUP BY v.idventa";
	    return ejecutarConsultaSimpleFila($sql);
	}


	public function mostrardetalle($idventa)
	{
		$sql = "SELECT dv.idventa,dv.idproducto,a.nombre,dv.cantidad,dv.precio_venta,dv.descuento,(dv.cantidad*dv.precio_venta-dv.descuento) as subtotal, v.total_venta, v.impuesto, p.nombre as cliente, v.num_comprobante 
		FROM detalle_venta dv 
		INNER JOIN producto a ON dv.idproducto=a.idproducto 
		INNER JOIN venta v ON v.idventa=dv.idventa 
		INNER JOIN persona p ON v.idcliente=p.idpersona WHERE dv.idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	public function mostrarUltimoCliente()
	{

		$sql = "SELECT * FROM persona order by idpersona desc limit 1";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarDetalle($idventa)
{
    $sql = "SELECT 
                dv.idventa,
                dv.idproducto,
                c.nombre AS categoria,
                dv.cantidad,
                dv.nombre_producto,
                pg.contenedor,
                pg.cantidad_contenedor,
                dv.precio_venta,
                v.descuento,
                (dv.cantidad * dv.precio_venta) AS subtotal,
                (v.total_venta - v.descuento) AS total_venta,
                v.impuesto,
                COALESCE((SELECT SUM(vp.monto) FROM venta_pago vp WHERE vp.idventa = dv.idventa AND vp.metodo_pago = 'Efectivo'),0) AS total_efectivo,
                COALESCE((SELECT SUM(vp.monto) FROM venta_pago vp WHERE vp.idventa = dv.idventa AND vp.metodo_pago <> 'Efectivo'),0) AS total_otro_pago,
                (SELECT GROUP_CONCAT(vp.nroOperacion SEPARATOR ' ') FROM venta_pago vp WHERE vp.idventa = dv.idventa) AS numoperacion,
                (SELECT GROUP_CONCAT(vp.banco SEPARATOR ' ') FROM venta_pago vp WHERE vp.idventa = dv.idventa) AS banco,
                (SELECT GROUP_CONCAT(vp.fechaDeposito SEPARATOR ' ') FROM venta_pago vp WHERE vp.idventa = dv.idventa) AS fechadeposito
            FROM detalle_venta dv
            INNER JOIN venta v ON v.idventa = dv.idventa
            LEFT JOIN producto_configuracion pg ON pg.id = dv.idproducto
            LEFT JOIN producto a ON a.idproducto = pg.idproducto
            LEFT JOIN categoria c ON c.idcategoria = a.idcategoria
            WHERE dv.idventa = '$idventa'";
    return ejecutarConsulta($sql);
}


	//Implementar un método para listar los registros 
	public function listarSucursal()
	{
		$sql = "SELECT * FROM sucursal";
		return ejecutarConsulta($sql);
	}

	public function listarSucursal2($idpersonal, $idsucursal)
{
    if ($idsucursal != 0 && isset($_SESSION['sucursales']) && is_array($_SESSION['sucursales'])) {

        // Si el usuario tiene varias sucursales asignadas en la sesión
        $ids = implode(",", $_SESSION['sucursales']); // Ej: "1,2,3"

        $sql = "SELECT s.idsucursal, s.nombre 
                FROM sucursal s 
                WHERE s.idsucursal IN ($ids)";
    } else {
        // Si el usuario tiene acceso total (administrador, etc.)
        $sql = "SELECT s.idsucursal, s.nombre FROM sucursal s";
    }

    return ejecutarConsulta($sql);
}


	//listar registros
public function listar($fecha_inicio, $fecha_fin, $estado, $idsucursal, $idproducto)
{
    $idpersonal_sesion = $_SESSION["idpersonal"]; // ID del usuario en sesión
    $cargo_sesion = $_SESSION["cargo"]; // Cargo del usuario en sesión

    // Base de la consulta SQL
    $sql = "SELECT v.idventa,DATE_FORMAT(v.fecha_hora, '%d/%m/%Y %H:%i:%s') as fecha,v.idsucursal, s.nombre as sucursal, date_format(v.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex,
            v.idcliente,p.nombre as cliente,p.num_documento,v.estadoS,u.idpersonal,u.nombre as personal, 
            v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.formapago,v.ventacredito,v.impuesto,
            v.dov_Nombre,v.estado 
            FROM venta v 
            INNER JOIN persona p ON v.idcliente=p.idpersona 
            INNER JOIN personal u ON v.idpersonal=u.idpersonal
            INNER JOIN sucursal s ON s.idsucursal = v.idsucursal 
            WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') 
            AND v.serie_comprobante != '-' 
            AND DATE(v.fecha_hora) >= '$fecha_inicio' 
            AND DATE(v.fecha_hora) <= '$fecha_fin'";

    // Si el usuario NO es administrador, filtrar solo sus ventas
    if ($cargo_sesion != "Administrador") {
        $sql .= " AND v.idpersonal = '$idpersonal_sesion'";
    }

    // Filtrado por sucursal (si aplica)
    if ($idsucursal != "Todos") {
        $sql .= " AND v.idsucursal = '$idsucursal'";
    }

    // Filtrado por estado (si aplica)
    if ($estado != "Todos") {
        $sql .= " AND v.estado = '$estado'";
    }
    // Filtrar por producto solo si NO ES "Todos"
    if ($idproducto != "" && $idproducto != null && $idproducto != "Todos") {
        $sql .= " AND v.idventa IN (
                SELECT dv.idventa
                FROM detalle_venta dv
                INNER JOIN producto_configuracion pc ON pc.id = dv.idproducto
                WHERE pc.idproducto = '$idproducto'
                )";
    }

    $sql .= " ORDER BY v.idventa DESC";

    return ejecutarConsulta($sql);
}


	//listar registros
	public function listar2($fecha_inicio, $fecha_fin, $estado, $idsucursal, $ids)
	{
		if ($estado == "Todos" and $ids == "Todos" || $estado == "Todos" and $ids == "") {

			$sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.fecha_kardex,v.idcliente,p.nombre as cliente,p.num_documento,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') AND v.serie_comprobante != '-' AND DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' ORDER BY v.idventa DESC";
		} else if ($estado == "Todos" and $ids != "Todos") {

			$sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.fecha_kardex,v.idcliente,p.nombre as cliente,p.num_documento,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') AND v.serie_comprobante != '-' AND DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.idsucursal = '$ids' ORDER BY v.idventa DESC";
		} else if ($estado == 'Aceptado' and $ids == "Todos") {

			$sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.fecha_kardex,v.idcliente,p.nombre as cliente,p.num_documento,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') AND v.serie_comprobante != '-' AND DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.estado = 'Aceptado' ORDER BY v.idventa DESC";
		} else if ($estado == "Por Enviar" and $ids == "Todos") {

			$sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.fecha_kardex,v.idcliente,p.nombre as cliente,p.num_documento,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') AND v.serie_comprobante != '-' AND DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.estado = 'Por Enviar' ORDER BY v.idventa DESC";
		} else if ($estado == "Nota Credito" and $ids == "Todos") {

			$sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.fecha_kardex,v.idcliente,p.nombre as cliente,p.num_documento,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') AND v.serie_comprobante != '-' AND DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.estado = 'Nota Credito' ORDER BY v.idventa DESC";
		} else {

			$sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.fecha_kardex,v.idcliente,p.nombre as cliente,p.num_documento,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') AND v.serie_comprobante != '-' AND DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.estado = 'Rechazado' AND v.idsucursal = '$ids' ORDER BY v.idventa DESC";
		}
		return ejecutarConsulta($sql);
	}

	//listar registros
	public function listarTodo()
	{

		$sql = "SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(v.total_venta-v.descuento) as total_venta,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE v.tipo_comprobante IN ('Boleta','Factura','Nota de Venta') AND v.serie_comprobante != '-' ORDER BY v.idventa DESC";

		return ejecutarConsulta($sql);
	}

	//listar registros
	public function listarNC($fecha_inicio, $fecha_fin, $estado, $idsucursal)
	{
		if ($estado == "Todos") {
			$sql = "SELECT v.idventa,s.nombre as sucursal,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(
        SELECT IFNULL(ROUND(SUM((dv.precio_venta - dv.descuento) * dv.cantidad),2),0)
        FROM detalle_venta dv
        WHERE dv.idventa = v.idventa
          AND dv.tipo = 'NC'
    ) AS total_anulado,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado 
            FROM venta v 
            INNER JOIN persona p ON v.idcliente=p.idpersona 
            INNER JOIN personal u ON v.idpersonal=u.idpersonal
            INNER JOIN sucursal s ON v.idsucursal = s.idsucursal
            WHERE v.tipo_comprobante IN ('NC', 'NCB') 
            AND v.serie_comprobante != '-' 
            AND DATE(v.fecha_hora)>='$fecha_inicio' 
            AND DATE(v.fecha_hora)<='$fecha_fin'
            AND v.idsucursal = '$idsucursal' 
            ORDER BY v.idventa DESC";
		} else if ($estado == 'Aceptado') {
			$sql = "SELECT v.idventa,s.nombre as sucursal,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(
        SELECT IFNULL(ROUND(SUM((dv.precio_venta - dv.descuento) * dv.cantidad),2),0)
        FROM detalle_venta dv
        WHERE dv.idventa = v.idventa
          AND dv.tipo = 'NC'
    ) AS total_anulado,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado 
            FROM venta v 
            INNER JOIN persona p ON v.idcliente=p.idpersona 
            INNER JOIN personal u ON v.idpersonal=u.idpersonal 
            INNER JOIN sucursal s ON v.idsucursal = s.idsucursal
            WHERE v.tipo_comprobante IN ('NC', 'NCB') 
            AND v.serie_comprobante != '-' 
            AND DATE(v.fecha_hora)>='$fecha_inicio' 
            AND DATE(v.fecha_hora)<='$fecha_fin'
            AND v.idsucursal = '$idsucursal'  
            AND v.estado = 'Aceptado' 
            ORDER BY v.idventa DESC";
		} else if ($estado == "Por Enviar") {
			$sql = "SELECT v.idventa,s.nombre as sucursal,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(
        SELECT IFNULL(ROUND(SUM((dv.precio_venta - dv.descuento) * dv.cantidad),2),0)
        FROM detalle_venta dv
        WHERE dv.idventa = v.idventa
          AND dv.tipo = 'NC'
    ) AS total_anulado,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado 
            FROM venta v 
            INNER JOIN persona p ON v.idcliente=p.idpersona 
            INNER JOIN personal u ON v.idpersonal=u.idpersonal 
            INNER JOIN sucursal s ON v.idsucursal = s.idsucursal
            WHERE v.tipo_comprobante IN ('NC', 'NCB') 
            AND v.serie_comprobante != '-' 
            AND DATE(v.fecha_hora)>='$fecha_inicio' 
            AND DATE(v.fecha_hora)<='$fecha_fin'
            AND v.idsucursal = '$idsucursal'  
            AND v.estado = 'Por Enviar' 
            ORDER BY v.idventa DESC";
		} else {

			$sql = "SELECT v.idventa,s.nombre as sucursal,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idpersonal,u.nombre as personal, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,(
        SELECT IFNULL(ROUND(SUM((dv.precio_venta - dv.descuento) * dv.cantidad),2),0)
        FROM detalle_venta dv
        WHERE dv.idventa = v.idventa
          AND dv.tipo = 'NC'
    ) AS total_anulado,v.ventacredito,v.impuesto,v.dov_Nombre,v.estado 
            FROM venta v 
            INNER JOIN persona p ON v.idcliente=p.idpersona 
            INNER JOIN personal u ON v.idpersonal=u.idpersonal 
            INNER JOIN sucursal s ON v.idsucursal = s.idsucursal
            WHERE v.tipo_comprobante IN ('NC', 'NCB') 
            AND v.serie_comprobante != '-' 
            AND DATE(v.fecha_hora)>='$fecha_inicio' 
            AND DATE(v.fecha_hora)<='$fecha_fin'
            AND v.idsucursal = '$idsucursal'  
            AND v.estado = 'Rechazado' 
            ORDER BY v.idventa DESC";
		}
		return ejecutarConsulta($sql);
	}


	public function ventacabecera($idventa)
	{
		$sql = "SELECT v.idventa,v.idsucursal, v.idcliente, p.nombre AS cliente, s.nombre as sucursal, p.direccion, p.tipo_documento, p.num_documento, p.email, p.telefono, v.idpersonal, v.montoPagado, v.formapago, v.numoperacion, date_format(v.fechadeposito,'%d/%m/%y') as fechadeposito, u.nombre AS personal, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, DATE(v.fecha_hora) AS fecha, date_format(v.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex, v.impuesto, v.total_venta, v.ventacredito, v.descuento, v.vuelto, v.observacion, v.totalrecibido
		FROM venta v 
		INNER JOIN persona p 
		ON v.idcliente=p.idpersona 
		INNER JOIN personal u 
		ON v.idpersonal=u.idpersonal
		INNER JOIN sucursal s
		ON s.idsucursal = v.idsucursal WHERE v.idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	public function ventadetalle($idventa)
{
    $sql = "SELECT 
                pg.id, 
                a.idproducto,
                a.idcategoria,
                pg.contenedor,
                pg.cantidad_contenedor, 
                a.nombre AS producto, 
                um.nombre AS unidadmedida, 
                a.codigo, 
                d.nombre_producto AS dproducto, 
                d.cantidad, 
                d.precio_venta, 
                a.precioB, 
                a.precioC, 
                a.precioD, 
                a.preciocigv, 
                v.descuento AS descuento, 
                CASE 
                    WHEN d.check_precio = 1 THEN d.precio_venta 
                    ELSE (d.cantidad * d.precio_venta - d.descuento)
                END AS subtotal,
                a.stock, 
                a.proigv,
                d.check_precio
            FROM detalle_venta d 
            LEFT JOIN producto_configuracion pg ON pg.id = d.idproducto
            INNER JOIN producto a ON pg.idproducto = a.idproducto 
            INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida
            INNER JOIN venta v ON v.idventa = d.idventa
            INNER JOIN categoria ca ON a.idcategoria = ca.idcategoria
            WHERE d.idventa = '$idventa'";
    
    return ejecutarConsulta($sql);
}


    public function pagosPorVenta($idventa) {
        $sql = "SELECT metodo_pago, monto 
                FROM venta_pago 
                WHERE idventa = '$idventa'";
        return ejecutarConsulta($sql);
    }

	public function ventadetallePDF($idventa)
	{
		$sql = "SELECT a.idproducto, a.nombre AS producto, um.nombre as unidadmedida, a.proigv, CASE WHEN a.codigo = 'SIN CODIGO' THEN '-' ELSE a.codigo END as codigo, d.cantidad, d.precio_venta, (d.descuento + v.descuento) AS descuento, (d.cantidad*d.precio_venta-d.descuento) AS subtotal, a.stock 
	FROM detalle_venta d 
	INNER JOIN producto a ON 
	d.idproducto=a.idproducto 
	INNER JOIN unidad_medida um 
	ON a.idunidad_medida = um.idunidad_medida
	INNER JOIN venta v
	ON v.idventa = d.idventa
	WHERE d.idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	//funcion para selecciolnar el numero de factura
	public function numero_venta($idsucursal)
	{

		$sql = "SELECT num_comprobante FROM venta WHERE tipo_comprobante='Factura' AND idsucursal = '$idsucursal' ORDER BY idventa DESC limit 1";
		return ejecutarConsulta($sql);
	}

	//funcion para seleccionar la serie de la factura
	public function numero_serie($idsucursal)
	{

		$sql = "SELECT REPLACE(serie_comprobante,'F','') AS serie_comprobante ,num_comprobante FROM venta WHERE tipo_comprobante='Factura'
			AND idsucursal = '$idsucursal'
			ORDER BY idventa DESC limit 1";

		return ejecutarConsulta($sql);
	}

	//funcion para selecciolnar el numero de boleta
	public function numero_venta_boleta($idsucursal)
	{

		$sql = "SELECT num_comprobante FROM venta WHERE tipo_comprobante='Boleta' 
			AND idsucursal = '$idsucursal'
			ORDER BY idventa DESC limit 1 ";
		return ejecutarConsulta($sql);
	}
	//funcion para seleccionar la serie de la boleta
	public function numero_serie_boleta($idsucursal)
	{

		$sql = "SELECT REPLACE(serie_comprobante,'B','') AS serie_comprobante, num_comprobante FROM venta WHERE tipo_comprobante='Boleta' AND idsucursal = '$idsucursal' ORDER BY idventa DESC limit 1";

		return ejecutarConsulta($sql);
	}

	//funcion para seleccionar la serie de la boleta
	public function numero_serie_nc($idsucursal)
	{

		$sql = "SELECT REPLACE(serie_comprobante,'FN','') AS serie_comprobante, num_comprobante FROM venta WHERE tipo_comprobante='NC' 
	AND idsucursal = '$idsucursal'
	ORDER BY idventa DESC limit 1";

		return ejecutarConsulta($sql);
	}

	//funcion para seleccionar la serie de la boleta
	public function numero_serie_ncb($idsucursal)
	{

		$sql = "SELECT REPLACE(serie_comprobante,'BN','') AS serie_comprobante, num_comprobante FROM venta WHERE tipo_comprobante='NCB' 
	AND idsucursal = '$idsucursal'
	ORDER BY idventa DESC limit 1";

		return ejecutarConsulta($sql);
	}

	//funcion para selecciolnar el numero de nota de crédito
	public function numero_venta_nc($idsucursal)
	{

		$sql = "SELECT num_comprobante FROM venta WHERE tipo_comprobante='NC' 
			AND idsucursal = '$idsucursal'
			ORDER BY idventa DESC limit 1";
		return ejecutarConsulta($sql);
	}

	//funcion para selecciolnar el numero de nota de crédito
	public function numero_venta_ncb($idsucursal)
	{

		$sql = "SELECT num_comprobante FROM venta WHERE tipo_comprobante='NCB' 
	AND idsucursal = '$idsucursal'
	ORDER BY idventa DESC limit 1";
		return ejecutarConsulta($sql);
	}

	//funcion para selecciolnar el numero de ticket
	public function numero_venta_ticket($idsucursal)
	{

		$sql = "SELECT num_comprobante FROM venta WHERE tipo_comprobante='Nota de Venta' 
			AND idsucursal = '$idsucursal'
			ORDER BY idventa DESC limit 1";
		return ejecutarConsulta($sql);
	}
	//funcion para seleccionar la serie de la ticket
	public function numero_serie_ticket($idsucursal)
	{

		$sql = "SELECT REPLACE(serie_comprobante,'P','') AS serie_comprobante, num_comprobante 
		FROM venta WHERE tipo_comprobante='Nota de Venta' AND idsucursal = '$idsucursal' 
		ORDER BY idventa DESC limit 1";

		return ejecutarConsulta($sql);
	}

	public function buscarProducto($codigo)
	{
		$sql = "SELECT p.*, um.nombre as unidadmedida FROM producto p INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida WHERE codigo='$codigo'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function updateNV($idventa)
	{
		$sql = "UPDATE venta SET estado='Nota de Venta Editida' WHERE idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	public function updateBoleta($idventa)
	{
		$sql = "UPDATE venta SET estado='Boleta Emitida' WHERE idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	public function updateFactura($idventa)
	{
		$sql = "UPDATE venta SET estado='Factura Emitida' WHERE idventa='$idventa'";
		return ejecutarConsulta($sql);
	}

	public function comprobantesPendientes() {
	    $sql = "SELECT COUNT(*) as total FROM venta WHERE estado = 'Por Enviar'";
	    return ejecutarConsultaSimpleFila($sql);
	}

public function listarHistorialCliente($idcliente,$fecha_inicio,$fecha_fin)
    {
        $sql = "SELECT * FROM venta v WHERE v.idcliente = '$idcliente' AND DATE(v.fecha_hora)>='$fecha_inicio' AND DATE(v.fecha_hora)<='$fecha_fin' AND v.estado IN ('Activado','Por Enviar','Aceptado')";
        //echo  $sql;
        $ventas = ejecutarConsulta($sql);
        $data = array();
        $list = array();
        while ($reg = $ventas->fetch_object()) {

            if ($reg->ventacredito == 'Si') {
                $sql1 = "SELECT * FROM detalle_venta  WHERE idventa = '$reg->idventa'";
                $detalles = ejecutarConsulta($sql1);
                $detalle = array();
                while ($reg2 = $detalles->fetch_object()) {
                    $detalle[] = array(
                        "idproducto" => $reg2->idproducto,
                        "nombre_producto" => $reg2->nombre_producto,
                        "cantidad" => $reg2->cantidad.' Unid.',
                        "precio_venta" => 'S/. '.$reg2->precio_venta,

                    ); # code...
                }

                $data[] = array(
                    "idventa" => $reg->idventa,
                    "tipo_comprobante" => $reg->tipo_comprobante,
                    "serie_comprobante" => $reg->serie_comprobante . '-' . $reg->num_comprobante,
                    "fecha_hora" => $reg->fecha_hora,
                    "interes" => ($reg->total_venta * $reg->interes) / 100,
                    "totalrecibido" => ($reg->totalrecibido == 0) ? $reg->montoPagado : $reg->totalrecibido,
                    "total_venta" => $reg->total_venta + ($reg->total_venta * $reg->interes) / 100,
                    "meses" => $reg->meses,
                    "detalle" => $detalle,
                );
            }


            $sql2 = "SELECT * FROM cuentas_por_cobrar cc 
             WHERE cc.idventa = '$reg->idventa' 
             AND cc.condicion = 1
             AND DATE(cc.fecharegistro)>='$fecha_inicio' 
             AND DATE(cc.fecharegistro)<='$fecha_fin'";

            $cuentasxcobrar = ejecutarConsulta($sql2);
            $datacuentasxcobrar = array();
            while ($reg3 = $cuentasxcobrar->fetch_object()) {

                if ($reg3->condicion == 1) {
                    $sql3 = "SELECT * FROM detalle_cuentas_por_cobrar WHERE idcpc = '$reg3->idcpc'";
                    $detallecuentasxcobrar = ejecutarConsulta($sql3);
                    $datadetallecuentasxcobrar = array();
                    while ($reg4 = $detallecuentasxcobrar->fetch_object()) {
                        $datadetallecuentasxcobrar[] = array(
                            "tipo" => 'AMORTIZACION DE CUENTA',
                            "montopagado" => $reg4->montopagado,
                        );
                    }
                    $list[] = array(
                        "fecha_hora" => $reg->fecha_hora,
                        "tipo" => 'CUENTA POR COBRAR -' . $reg->serie_comprobante . '-' . $reg->num_comprobante,
                        "deudatotal" => $reg3->deudatotal,
                        "interes" => $reg3->interes,
                        "abonototal" => $reg3->abonototal,
                        "detalle" => $datadetallecuentasxcobrar,
                    );
                }
            }
        }

        $sql3 = "SELECT * FROM compra c WHERE idproveedor = '$idcliente' AND DATE(c.fecha_hora)>='$fecha_inicio' AND DATE(c.fecha_hora)<='$fecha_fin'";
        $compras = ejecutarConsulta($sql3);
        $data3 = array();
        $list3 = array();
        while ($reg3 = $compras->fetch_object()) {

            if ($reg3->compracredito == 'Si') {
                $sql4 = "SELECT * FROM detalle_compra WHERE idcompra = '$reg3->idcompra'";
                $detalles4 = ejecutarConsulta($sql4);
                $detalle4 = array();
                while ($reg4 = $detalles4->fetch_object()) {
                    $detalle4[] = array(
                        "idproducto" => $reg4->idproducto,
                        "nombre_producto" => $reg4->nombre_producto,
                        "cantidad" => $reg4->cantidad.' Unid.',
                        "precio_venta" => 'S/. '.$reg4->precio_venta,

                    ); # code...
                }

                $data3[] = array(
                    "idventa" => $reg3->idcompra,
                    "tipo_comprobante" => $reg3->tipo_comprobante,
                    "serie_comprobante" => $reg3->serie_comprobante . '-' . $reg3->num_comprobante,
                    "totalrecibido" => $reg3->motoPagado,
                    "interes" => '0',
                    "fecha_hora" => $reg3->fecha_hora,
                    "total_venta" => $reg3->total_compra,
                    "meses" => '0',
                    "detalle" => $detalle4,
                );
            }


            $sql5 = "SELECT * FROM cuentas_por_pagar cp WHERE cp.idcompra = '$reg3->idcompra' AND cp.condicion = 1 AND DATE(cp.fecharegistro)>='$fecha_inicio' AND DATE(cp.fecharegistro)<='$fecha_fin'";
            $cuentasxpagar = ejecutarConsulta($sql5);
            $datacuentasxpagar = array();
            while ($reg4 = $cuentasxpagar->fetch_object()) {

                if ($reg4->condicion == 1) {
                    $sql5 = "SELECT * FROM detalle_cuentas_por_pagar WHERE idcpp = '$reg4->idcpp'";
                    $datacuentasxpagar = ejecutarConsulta($sql5);
                    $datadetallecuentasxpagar = array();
                    while ($reg5 = $datacuentasxpagar->fetch_object()) {
                        $datadetallecuentasxpagar[] = array(
                            "tipo" => 'AMORTIZACION DE PAGO',
                            "montopagado" => $reg5->montopagado,
                        );
                    }
                    $list3[] = array(
                        "fecha_hora" => $reg4->fecha_hora,
                        "tipo" => 'CUENTA POR PAGAR -' . $reg3->serie_comprobante . '-' . $reg3->num_comprobante,
                        "deudatotal" => $reg4->deudatotal,
                        "abonototal" => $reg4->abonototal,
                        "interes" => '0',
                        "detalle" => $datadetallecuentasxpagar,
                    );
                }
            }
        }

        return array('ventas' => $data, 'cuentasxcobrar' => $list, 'compras' => $data3, 'cuentasxpagar' => $list3);
    }

   

   public function exportarExcel($fecha_inicio, $fecha_fin, $estado, $idsucursal, $idproducto)
{
    // Cargar PHPSpreadsheet
    $autoload = realpath(__DIR__ . '/../vendor/autoload.php');
    if ($autoload) require $autoload;

    // Datos del negocio
    require_once "Negocio.php";
    $negocio = new Negocio();
    $datos = $negocio->mostrarNombreNegocio();

    // Buscar nombre de sucursal
    $nombre_sucursal = "Todas";
    if ($idsucursal && $idsucursal != "Todas") {
        $sql = "SELECT nombre FROM sucursal WHERE idsucursal = '$idsucursal' LIMIT 1";
        $res = ejecutarConsultaSimpleFila($sql);
        if ($res && isset($res['nombre'])) {
            $nombre_sucursal = $res['nombre'];
        }
    }

    // Crear Excel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Ventas");

    // ENCABEZADO
    $sheet->mergeCells("A1:H1");
    $sheet->mergeCells("A2:H2");
    $sheet->mergeCells("A3:H3");

    $sheet->setCellValue("A1", $datos['nombre'] ?? '');
    $sheet->setCellValue("A2", "RUC: " . ($datos['documento'] ?? ''));
    $sheet->setCellValue("A3", $datos['direccion'] ?? '');

    $sheet->getStyle("A1")->getFont()->setSize(16)->setBold(true);
    $sheet->getStyle("A1:A3")->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // SUBTÍTULO
    $sheet->mergeCells("A5:H5");
    $sheet->setCellValue("A5", "REPORTE DE VENTAS");
    $sheet->getStyle("A5")->getFont()->setBold(true)->setSize(14);

    // FILTROS
    $sheet->setCellValue("A7", "Fecha Inicio:");
    $sheet->setCellValue("B7", $fecha_inicio ?: "-");

    $sheet->setCellValue("A8", "Fecha Fin:");
    $sheet->setCellValue("B8", $fecha_fin ?: "-");

    $sheet->setCellValue("A9", "Estado:");
    $sheet->setCellValue("B9", $estado ?: "Todos");

    $sheet->setCellValue("A10", "Sucursal:");
    $sheet->setCellValue("B10", $nombre_sucursal);

    $sheet->setCellValue("A11", "Producto:");
    $sheet->setCellValue("B11", $idproducto ?: "Todos");

    $sheet->getStyle("A7:A11")->getFont()->setBold(true);

    // ENCABEZADOS TABLA
    $filaEnc = 14;
    $titulos = ["Fecha","Tipo Doc","Serie","Número","Cliente","Documento","Sucursal","Total Venta"];
    $col = "A";

    foreach ($titulos as $t) {
        $sheet->setCellValue($col.$filaEnc, $t);
        $col++;
    }

    // ESTILO ENCABEZADO
    $sheet->getStyle("A{$filaEnc}:H{$filaEnc}")->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4F81BD']
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ]
    ]);

    // OBTENER VENTAS
    $rs = $this->listar($fecha_inicio, $fecha_fin, $estado, $idsucursal, $idproducto);

    $fila = $filaEnc + 1;
    $total = 0;

    // LLENAR FILAS
    while ($r = $rs->fetch_object()) {
        $sheet->setCellValue("A{$fila}", $r->fecha);
        $sheet->setCellValue("B{$fila}", $r->tipo_comprobante);
        $sheet->setCellValue("C{$fila}", $r->serie_comprobante);
        $sheet->setCellValue("D{$fila}", $r->num_comprobante);
        $sheet->setCellValue("E{$fila}", $r->cliente);
        $sheet->setCellValue("F{$fila}", $r->num_documento);
        $sheet->setCellValue("G{$fila}", $r->sucursal);
        $sheet->setCellValue("H{$fila}", $r->total_venta);

        $total += $r->total_venta;
        $fila++;
    }

    $ultima = $fila - 1;

    // APLICAR UN SOLO ESTILO A LA TABLA (evita corrupción)
    $sheet->getStyle("A{$filaEnc}:H{$ultima}")->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            ]
        ],
        'alignment' => [
            'wrapText' => true,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
        ]
    ]);

    // TOTAL GENERAL
    $sheet->setCellValue("G{$fila}", "TOTAL GENERAL:");
    $sheet->setCellValue("H{$fila}", $total);

    $sheet->getStyle("G{$fila}:H{$fila}")->applyFromArray([
        'font' => ['bold' => true]
    ]);

    // ANCHOS FIJOS (autosize genera styles.xml corrupto)
    $anchos = [12, 12, 10, 12, 35, 15, 20, 14];
    $col = "A";
    foreach ($anchos as $w) {
        $sheet->getColumnDimension($col)->setWidth($w);
        $col++;
    }

    // EXPORTACIÓN LIMPIA
    while (ob_get_level()) { @ob_end_clean(); }

    $filename = "Ventas_" . date("Ymd_His") . ".xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"{$filename}\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

public function getVentaData($idventa)
{
    $sql = "SELECT v.idcliente, p.nombre as cliente, v.fecha_hora as fecha, p.direccion as punto_llegada 
            FROM venta v
            INNER JOIN persona p ON v.idcliente = p.idpersona
            WHERE v.idventa = '$idventa'";
    return ejecutarConsultaSimpleFila($sql);
}

public function getVentaDetalles($idventa)
{
    $sql = "SELECT dv.idproducto, p.codigo, dv.nombre_producto, dv.cantidad, um.nombre as unidad, p.stock as peso, 1 as bultos, '' as lotes
            FROM detalle_venta dv
            INNER JOIN producto p ON dv.idproducto = p.idproducto
            INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
            WHERE dv.idventa = '$idventa'";
    return ejecutarConsulta($sql);
}

public function cambiarComprobante($idventa, $nuevo_tipo, $idsucursal)
{
    // --- 1. DEFINIR PREFIJO ---
    $prefijo = ($nuevo_tipo == "Factura") ? "F" :
               (($nuevo_tipo == "Boleta") ? "B" : "P");

    // --- 2. OBTENER SERIE DESDE comp_pago ---
    $sqlSerie = "
        SELECT serie_comprobante
        FROM comp_pago
        WHERE nombre = '$nuevo_tipo'
        AND idsucursal = '$idsucursal'
        LIMIT 1
    ";
    $rowSerie = ejecutarConsultaSimpleFila($sqlSerie);

    if (!$rowSerie || $rowSerie['serie_comprobante'] === "") {
        return "Error: No existe serie configurada en comp_pago.";
    }

    // convertir serie de BD (000,001,002...) a entero y sumar 1
    $serie_base = intval($rowSerie['serie_comprobante']); // 0,1,2...
    $serie_num = $serie_base + 1; // 1,2,3...

    // construir serie final real F001, B002, P003
    $serie_final = $prefijo . str_pad($serie_num, 3, "0", STR_PAD_LEFT);

    // --- 3. OBTENER CORRELATIVO ---
    $sqlNumero = "
        SELECT num_comprobante
        FROM venta
        WHERE tipo_comprobante = '$nuevo_tipo'
        AND serie_comprobante = '$serie_final'
        AND idsucursal = '$idsucursal'
        ORDER BY idventa DESC
        LIMIT 1
    ";

    $rowNum = ejecutarConsultaSimpleFila($sqlNumero);

    if ($rowNum && $rowNum['num_comprobante'] != "") {
        $nuevo_num = intval($rowNum['num_comprobante']) + 1;
    } else {
        $nuevo_num = 1;
    }

    $num_final = str_pad($nuevo_num, 7, "0", STR_PAD_LEFT);

    // --- 4. ACTUALIZAR LA VENTA ---
    $sqlUpdate = "
        UPDATE venta SET 
            tipo_comprobante = '$nuevo_tipo',
            serie_comprobante = '$serie_final',
            num_comprobante = '$num_final',
            estado = 'Por Enviar'
        WHERE idventa = '$idventa'
    ";

    return ejecutarConsulta($sqlUpdate) ? "ok" : "Error al actualizar";
}
public function obtenerPagos($idventa)
{
    $sql = "SELECT metodo_pago, monto 
            FROM venta_pago 
            WHERE idventa = '$idventa'";

    // Usando tu función de consulta general
    $res = ejecutarConsulta($sql);

    $pagos = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $pagos[] = $row;
        }
    }

    return $pagos; // Retorna array vacío si no hay pagos
}

// Agregamos $idsucursal como parámetro
public function listarUltimosProductosCliente($idcliente, $idsucursal, $ids_carrito = [])
{
    // Preparamos los IDs para la prioridad
    $ids_string = "0"; 
    if (!empty($ids_carrito)) {
        $ids_seguros = array_map('intval', $ids_carrito);
        $ids_string = implode(',', $ids_seguros);
    }

    $sql = "SELECT 
                dv.nombre_producto,
                dv.cantidad,
                pg.contenedor, 
                dv.precio_venta,
                dv.descuento,  
                ((dv.cantidad * dv.precio_venta) - dv.descuento) as subtotal,
                DATE_FORMAT(v.fecha_hora, '%d/%m/%Y %H:%i') as fecha,
                v.tipo_comprobante,
                v.serie_comprobante,
                v.num_comprobante,
                pg.idproducto as id_real, 
                (CASE WHEN pg.idproducto IN ($ids_string) THEN 0 ELSE 1 END) as prioridad
                
            FROM detalle_venta dv
            INNER JOIN venta v ON dv.idventa = v.idventa
            INNER JOIN producto_configuracion pg ON pg.id = dv.idproducto
            
            WHERE v.idcliente = '$idcliente' 
            AND v.idsucursal = '$idsucursal' -- <<--- AQUÍ ESTÁ EL FILTRO CLAVE
            AND v.estado IN ('Aceptado', 'Activado', 'Por Enviar', 'En Resumen')
            
            ORDER BY prioridad ASC, v.fecha_hora DESC
            LIMIT 100";
            
    return ejecutarConsulta($sql);
}

}