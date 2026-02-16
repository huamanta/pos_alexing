<?php
require "../configuraciones/Conexion.php";
date_default_timezone_set('America/Lima');

class Traslado
{
	public function __construct() {}

	/**
	 * Insertar traslado con:
	 * - validaciones de stock en origen
	 * - creación automática en destino si producto no existe
	 * - transacción (START/COMMIT/ROLLBACK)
	 * Devuelve un string con el mensaje (éxito o error).
	 */
	public function insertar($idorigen, $iddestino, $fecha, $productos_json, $idusuario)
	{
	    if ($idorigen == $iddestino) return "El almacén destino debe ser distinto al origen.";

	    $productos = json_decode($productos_json, true);
	    if (!is_array($productos) || count($productos) === 0) return "No se enviaron productos para traslado.";

	    ejecutarConsulta("START TRANSACTION");

	    // 1️⃣ Validar stock en origen
	    foreach ($productos as $p) {
	        $idproducto = intval($p["idproducto"]);
	        $cantidad = floatval($p["cantidad"]);

	        $row = ejecutarConsultaSimpleFila("SELECT stock, nombre FROM producto WHERE idproducto='$idproducto' AND idsucursal='$idorigen'");
	        if (!$row) {
	            ejecutarConsulta("ROLLBACK");
	            return "Producto $idproducto no existe en almacén origen.";
	        }
	        if ($row['stock'] < $cantidad) {
	            ejecutarConsulta("ROLLBACK");
	            return "Stock insuficiente de {$row['nombre']} en almacén origen.";
	        }
	    }

	    // 2️⃣ Insertar cabecera traslado como PENDIENTE
	    $idtraslado = ejecutarConsulta_retornarID("INSERT INTO traslado (idorigen, iddestino, fecha, estado, idusuario, tipo) VALUES ('$idorigen','$iddestino','$fecha','0','$idusuario', 'traslado')");
	    if (!$idtraslado) {
	        ejecutarConsulta("ROLLBACK");
	        return "Error al registrar cabecera de traslado.";
	    }

	    // 3️⃣ Insertar detalle y registrar salida en origen
	    foreach ($productos as $p) {
	        $idproducto = intval($p["idproducto"]);
	        $cantidad = floatval($p["cantidad"]);

	        // Insertar detalle
	        // Insertar detalle con estado pendiente y sin observación
            $sqlDetalle = "INSERT INTO traslado_detalle 
                            (idtraslado, idproducto, cantidad, estado_detalle, observacion) 
                           VALUES 
                            ('$idtraslado','$idproducto','$cantidad','pendiente','')";
            if (!ejecutarConsulta($sqlDetalle)) {
                ejecutarConsulta("ROLLBACK");
                return "Error al registrar detalle del producto $idproducto.";
            }


	        // Obtener datos del producto en origen
	        $origen = ejecutarConsultaSimpleFila("SELECT * FROM producto WHERE idproducto='$idproducto' AND idsucursal='$idorigen'");
	        if (!$origen) {
	            ejecutarConsulta("ROLLBACK");
	            return "No se encontró información del producto $idproducto en origen.";
	        }

	        $precio = floatval($origen['precio'] ?? 0);

	        // 4️⃣ Salida de stock en almacén origen
	        $resSalida = $this->movimientoEntradaSalidaT($idproducto, $idorigen, 1, $cantidad, "Traslado a almacén $iddestino (pendiente)", 1, $precio);
	        if ($resSalida['status'] != 1) {
	            ejecutarConsulta("ROLLBACK");
	            return "Error en kardex de salida: " . $resSalida['message'];
	        }

	        // NOTA: no se registra entrada en destino todavía
	    }

	    // 5️⃣ Crear notificación para almacén destino
	   $origenFila = ejecutarConsultaSimpleFila("SELECT nombre FROM sucursal WHERE idsucursal='$idorigen'");
		$nombreOrigen = $origenFila ? $origenFila['nombre'] : "Almacén $idorigen";

		// Crear mensaje usando el nombre
		$mensaje = "Nuevo traslado pendiente desde el almacén $nombreOrigen con ID $idtraslado.";

		// Insertar notificación
		$sqlNotificacion = "INSERT INTO notificaciones (idsucursal, idtraslado, mensaje) 
		                    VALUES ('$iddestino', '$idtraslado', '$mensaje')";
		if (!ejecutarConsulta($sqlNotificacion)) {
		    ejecutarConsulta("ROLLBACK");
		    return "Error al generar notificación para el almacén de destino";
		}

	    ejecutarConsulta("COMMIT");
	    return " Traslado registrado correctamente como pendiente. La entrada de stock en destino se realizará al aceptar.";
	}

    // Función para aceptar traslado
   public function aceptarTraslado($idtraslado, $idusuario)
{
    ejecutarConsulta("START TRANSACTION");

    // 1️⃣ Verificar traslado pendiente
    $traslado = ejecutarConsultaSimpleFila("SELECT * FROM traslado WHERE idtraslado='$idtraslado' AND estado='0'");
    if (!$traslado) {
        ejecutarConsulta("ROLLBACK");
        return "El traslado ya fue aceptado o no existe.";
    }

    $idorigen  = intval($traslado['idorigen']);
    $iddestino = intval($traslado['iddestino']);

    // 2️⃣ Obtener productos del traslado
    $productos = ejecutarConsulta("SELECT idproducto, cantidad FROM traslado_detalle WHERE idtraslado='$idtraslado'");
    if (!$productos || $productos->num_rows == 0) {
        ejecutarConsulta("ROLLBACK");
        return "No hay productos en el traslado.";
    }

    // 3️⃣ Procesar productos
    while ($reg = $productos->fetch_object()) {
        $idproducto = intval($reg->idproducto);
        $cantidad   = floatval($reg->cantidad);

        // 🔹 Buscar producto en origen
        $origen = ejecutarConsultaSimpleFila("SELECT * FROM producto WHERE idproducto='$idproducto' AND idsucursal='$idorigen'");
        if (!$origen) {
            ejecutarConsulta("ROLLBACK");
            return "Producto $idproducto no encontrado en almacén origen.";
        }

        // 🔹 Preparar datos
        $nombre      = addslashes($origen['nombre'] ?? '');
        $codigo      = trim($origen['codigo'] ?? '');
        $descripcion = addslashes($origen['descripcion'] ?? '');
        $idcategoria = $origen['idcategoria'] ?? "NULL";
        $idunidad    = $origen['idunidad_medida'] ?? 1;
        $idrubro     = $origen['idrubro'] ?? 14;
        $idcondicion = $origen['idcondicionventa'] ?? 4;
        $precio      = floatval($origen['precio'] ?? 0);
        $precioB     = floatval($origen['precioB'] ?? 0);

        // 🔹 Buscar producto en destino
        $codigo_safe = !empty($codigo) ? addslashes($codigo) : '';
        $nombre_safe = !empty($nombre) ? addslashes($nombre) : '';
        $iddestino_safe = intval($iddestino);

        if (!empty($codigo_safe) && strtoupper($codigo_safe) !== 'SIN CODIGO') {
            $sql_destino = "SELECT idproducto, precio FROM producto WHERE codigo='$codigo_safe' AND idsucursal='$iddestino_safe' LIMIT 1";
        } else {
            $sql_destino = "SELECT idproducto, precio FROM producto WHERE nombre='$nombre_safe' AND idsucursal='$iddestino_safe' LIMIT 1";
        }

        $destino = ejecutarConsultaSimpleFila($sql_destino);
        if ($destino === false) {
            ejecutarConsulta("ROLLBACK");
            return "Error al buscar producto en destino.";
        }

        $producto_nuevo = false; // bandera

        if (!empty($destino) && isset($destino['idproducto'])) {
            // ✅ Producto ya existe en destino
            $iddest = intval($destino['idproducto']);
        } else {
            // 🆕 Crear producto en destino con precios del origen
            $producto_nuevo = true;
            $proigv_safe = isset($origen['proigv']) ? addslashes($origen['proigv']) : '';
            $stock_minimo_safe = isset($origen['stock_minimo']) ? floatval($origen['stock_minimo']) : 0;

            $sql_insert = "INSERT INTO producto (
                idsucursal, idcategoria, idunidad_medida, idrubro, idcondicionventa,
                nombre, codigo, descripcion, precio, precioB, precioC, precioD, precioE,
                preciocigv, precio_compra, proigv, stock_minimo, stock, imagen, condicion
            ) VALUES (
                '$iddestino_safe',
                " . ($idcategoria === 'NULL' ? 'NULL' : intval($idcategoria)) . ",
                $idunidad,
                $idrubro,
                $idcondicion,
                '$nombre_safe',
                " . (!empty($codigo_safe) ? "'$codigo_safe'" : "'SIN CODIGO'") . ",
                '$descripcion',
                $precio,
                $precioB,
                " . ($origen['precioC'] ?? 0) . ",
                " . ($origen['precioD'] ?? 0) . ",
                " . ($origen['precioE'] ?? 0) . ",
                " . ($origen['preciocigv'] ?? 0) . ",
                " . ($origen['precio_compra'] ?? 0) . ",
                '$proigv_safe',
                $stock_minimo_safe,
                0,
                'anonymous.png',
                1
            )";

            $iddest = ejecutarConsulta_retornarID($sql_insert);
            if (!$iddest) {
                ejecutarConsulta("ROLLBACK");
                return "Error al crear producto en destino: $nombre_safe";
            }

            // 🔄 Copiar configuraciones del producto origen
            $conf_origen = ejecutarConsulta("SELECT * FROM producto_configuracion WHERE idproducto = '$idproducto' AND deleted_at IS NULL");
            while ($conf = $conf_origen->fetch_assoc()) {
                $codigo_extra = addslashes($conf['codigo_extra']);
                $contenedor = addslashes($conf['contenedor']);
                $cantidad_contenedor = floatval($conf['cantidad_contenedor']);
                $precio_venta = floatval($conf['precio_venta']);
                $precio_promocion = floatval($conf['precio_promocion']);

                $sql_conf_dest = "INSERT INTO producto_configuracion
                    (codigo_extra, contenedor, cantidad_contenedor, precio_venta, precio_promocion, idproducto)
                    VALUES ('$codigo_extra','$contenedor',$cantidad_contenedor,$precio_venta,$precio_promocion,'$iddest')";
                $idconf_dest = ejecutarConsulta_retornarID($sql_conf_dest);

                // Copiar precios adicionales
                $precios_origen = ejecutarConsulta("SELECT * FROM producto_configuracion_precios WHERE producto_configuracion_id = {$conf['id']} AND estado = 1");
                while ($precio_ad = $precios_origen->fetch_assoc()) {
                    $idnombre_p = addslashes($precio_ad['idnombre_p']);
                    $precio_valor = floatval($precio_ad['precio']);
                    $sql_prec_dest = "INSERT INTO producto_configuracion_precios
                        (producto_configuracion_id, idnombre_p, precio, estado)
                        VALUES ($idconf_dest, '$idnombre_p', $precio_valor, 1)";
                    ejecutarConsulta($sql_prec_dest);
                }
            }
        }

        // 6️⃣ Registrar SALIDA en almacén origen (precio del origen)
        /*$resSalida = $this->movimientoEntradaSalidaT(
            $idproducto,
            $idorigen,
            1, // salida
            $cantidad,
            "Traslado hacia almacén $iddestino",
            1,
            $precio // usa precio del origen
        );
        if ($resSalida['status'] != 1) {
            ejecutarConsulta("ROLLBACK");
            return "Error en kardex de salida: " . $resSalida['message'];
        }*/

        // 7️⃣ Registrar ENTRADA en almacén destino
        $resEntrada = $this->movimientoEntradaSalidaT(
            $iddest,
            $iddestino_safe,
            0, // entrada
            $cantidad,
            "Traslado desde almacén $idorigen",
            1,
            null,              // que determine el precio según la lógica interna
            $idproducto        // idproducto de origen para tomar su precio si es nuevo
        );
        if ($resEntrada['status'] != 1) {
            ejecutarConsulta("ROLLBACK");
            return "Error en kardex de entrada: " . $resEntrada['message'];
        }
    }

    // 8️⃣ Actualizar estado del traslado
    $sqlUpdate = "UPDATE traslado 
                  SET estado='1', idusuario_acepta='$idusuario', fecha_aceptacion=NOW() 
                  WHERE idtraslado='$idtraslado'";
    if (!ejecutarConsulta($sqlUpdate)) {
        ejecutarConsulta("ROLLBACK");
        return "Error al actualizar estado del traslado.";
    }

    // 9️⃣ Marcar notificación como leída
    ejecutarConsulta("UPDATE notificaciones SET leido=1 WHERE idtraslado='$idtraslado'");

    ejecutarConsulta("COMMIT");
    return "Traslado aceptado correctamente y notificación marcada como leída.";
}



    // Función opcional para crear notificación al crear traslado
    public function crearNotificacionTraslado($idtraslado, $idsucursal, $mensaje) {
        $sql = "INSERT INTO notificaciones (idsucursal, mensaje, leido, fecha, idtraslado)
                VALUES ('$idsucursal', '$mensaje', 0, NOW(), '$idtraslado')";
        return ejecutarConsulta($sql);
    }



public function movimientoEntradaSalidaT(
    $idproducto,
    $idsucursal,
    $tipo_movimiento,
    $cantidad,
    $motivo,
    $cantidad_contenedor = 1,
    $precio_unitario = null,
    $idproducto_origen = null
) {
    date_default_timezone_set('America/Lima');

    $idproducto = intval($idproducto);
    $idsucursal = intval($idsucursal);
    $cantidad = floatval(str_replace(',', '.', $cantidad));
    $cantidad_contenedor = floatval(str_replace(',', '.', $cantidad_contenedor));
    if ($cantidad_contenedor <= 0) $cantidad_contenedor = 1;

    $total_unidades = round($cantidad * $cantidad_contenedor, 2);
    if ($total_unidades <= 0) {
        return ['status' => 0, 'message' => 'La cantidad total no puede ser cero o negativa'];
    }

    $intentos = 0;
    $max_intentos = 3;

    while ($intentos < $max_intentos) {
        try {
            $intentos++;
            ejecutarConsulta("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
            ejecutarConsulta("BEGIN");

            $sql_producto = "SELECT idproducto, stock, precio 
                             FROM producto 
                             WHERE idproducto = '$idproducto' 
                               AND idsucursal = '$idsucursal'
                             FOR UPDATE";
            $res = ejecutarConsulta($sql_producto);
            $producto = $res ? $res->fetch_object() : null;

            $existe = ($producto !== null);
            $stock_actual = $existe ? floatval($producto->stock) : 0;

            // Determinar precio
            if ($tipo_movimiento == 1) {
                $precio = ($precio_unitario !== null)
                    ? floatval($precio_unitario)
                    : floatval($producto->precio ?? 0);
            } else {
                if ($existe) {
                    $precio = ($precio_unitario !== null)
                        ? floatval($precio_unitario)
                        : floatval($producto->precio);
                } else {
                    $precio = 0;
                    if ($idproducto_origen) {
                        $sql_origen = "SELECT precio FROM producto WHERE idproducto = '$idproducto_origen'";
                        $res_origen = ejecutarConsultaSimpleFila($sql_origen);
                        if ($res_origen && isset($res_origen['precio'])) {
                            $precio = floatval($res_origen['precio']);
                        }
                    }
                    if ($precio <= 0 && $precio_unitario !== null) {
                        $precio = floatval($precio_unitario);
                    }
                }
            }

            $fecha_kardex = date('Y-m-d H:i:s');

            //  Procesar movimiento
            if ($tipo_movimiento == 0) {
                // ENTRADA - Registrar en stock_fifo
                $nuevo_stock = round($stock_actual + $total_unidades, 2);
                $type = "Entrada de almacén por Traslado";

                // Insertar en stock_fifo
                $sql_fifo = "INSERT INTO stock_fifo 
                    (idsucursal, idproducto, producto_configuracion_id, origen, referencia_id,
                     cantidad_ingreso, cantidad_restante, precio_compra, precio_venta, 
                     fecha_ingreso, estado, fvencimiento)
                    VALUES (
                        '$idsucursal',
                        '$idproducto',
                        NULL,
                        'ALMACEN',
                        NULL,
                        '$total_unidades',
                        '$total_unidades',
                        '$precio',
                        '$precio',
                        '$fecha_kardex',
                        1,
                        NULL
                    )";
                
                if (!ejecutarConsulta($sql_fifo)) {
                    throw new Exception("Error al registrar entrada en stock_fifo");
                }

            } else {
                //  SALIDA - Consumir de stock_fifo usando FIFO
                $type = "Salida de almacén por Traslado";
                
                if ($stock_actual < $total_unidades) {
                    ejecutarConsulta("ROLLBACK");
                    return ['status' => 0, 'message' => "Stock insuficiente (actual: $stock_actual, salida: $total_unidades)"];
                }

                // Consumir stock usando FIFO
                $cantidad_restante_salida = $total_unidades;
                
                $sql_lotes = "SELECT idfifo, cantidad_restante, precio_compra 
                              FROM stock_fifo 
                              WHERE idsucursal = '$idsucursal' 
                                AND idproducto = '$idproducto' 
                                AND cantidad_restante > 0 
                                AND estado = 1
                              ORDER BY fecha_ingreso ASC, idfifo ASC
                              FOR UPDATE";
                
                $res_lotes = ejecutarConsulta($sql_lotes);
                
                if (!$res_lotes || $res_lotes->num_rows == 0) {
                    throw new Exception("No hay lotes FIFO disponibles para la salida");
                }

                while ($lote = $res_lotes->fetch_object()) {
                    if ($cantidad_restante_salida <= 0) break;

                    $idfifo = $lote->idfifo;
                    $cant_disp = floatval($lote->cantidad_restante);
                    
                    if ($cant_disp >= $cantidad_restante_salida) {
                        // Este lote cubre todo
                        $nueva_cant = $cant_disp - $cantidad_restante_salida;
                        $sql_upd = "UPDATE stock_fifo 
                                    SET cantidad_restante = '$nueva_cant' 
                                    WHERE idfifo = '$idfifo'";
                        ejecutarConsulta($sql_upd);
                        $cantidad_restante_salida = 0;
                    } else {
                        // Agotar este lote
                        $sql_upd = "UPDATE stock_fifo 
                                    SET cantidad_restante = 0 
                                    WHERE idfifo = '$idfifo'";
                        ejecutarConsulta($sql_upd);
                        $cantidad_restante_salida -= $cant_disp;
                    }
                }

                if ($cantidad_restante_salida > 0) {
                    throw new Exception("No se pudo descontar toda la cantidad de stock_fifo");
                }

                $nuevo_stock = round($stock_actual - $total_unidades, 2);
            }

            // Actualizar stock del producto
            $sql_update = "UPDATE producto SET stock = '$nuevo_stock' 
                           WHERE idproducto = '$idproducto' 
                             AND idsucursal = '$idsucursal'";
            if (!ejecutarConsulta($sql_update)) {
                throw new Exception("Error al actualizar stock del producto");
            }

            // Registrar en kardex
            $sql_kardex = "INSERT INTO kardex 
                (idsucursal, idproducto, cantidad, cantidad_contenedor, precio_unitario, 
                 stock_actual, tipo_movimiento, motivo, descripcion, fecha_kardex)
                VALUES (
                    '$idsucursal',
                    '$idproducto',
                    '$total_unidades',
                    '$cantidad_contenedor',
                    '$precio',
                    '$nuevo_stock',
                    '$tipo_movimiento',
                    '$type',
                    '$motivo',
                    '$fecha_kardex'
                )";
            if (!ejecutarConsulta($sql_kardex)) {
                throw new Exception("Error al registrar movimiento en kardex");
            }

            ejecutarConsulta("COMMIT");

            return [
                'status' => 1,
                'message' => "Movimiento registrado correctamente ($type)",
                'stock_anterior' => $stock_actual,
                'stock_nuevo' => $nuevo_stock,
                'precio_usado' => $precio,
                'fecha' => $fecha_kardex
            ];

        } catch (Exception $e) {
            ejecutarConsulta("ROLLBACK");

            if (stripos($e->getMessage(), 'deadlock') !== false ||
                stripos($e->getMessage(), 'lock wait timeout') !== false) {
                if ($intentos < $max_intentos) {
                    usleep(200000);
                    continue;
                }
            }

            return ['status' => 0, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    return ['status' => 0, 'message' => 'No se pudo completar el movimiento tras varios intentos por concurrencia.'];
}

	// listar (igual que antes)
	public function listar($fecha_inicio, $fecha_fin, $estado, $idsucursal)
{
    $sql = "SELECT 
                t.idtraslado, 
                t.idorigen,
                t.iddestino,
                s1.nombre AS origen, 
                s2.nombre AS destino, 
                t.fecha,
                t.tipo,
                (CASE 
                    WHEN t.estado='1' THEN 'Aceptado' 
                    WHEN t.estado='0' THEN 'Pendiente'
                    ELSE 'Anulado' 
                END) AS estado
            FROM traslado t
            INNER JOIN sucursal s1 ON t.idorigen = s1.idsucursal
            INNER JOIN sucursal s2 ON t.iddestino = s2.idsucursal
            WHERE 
                (
                    (t.tipo = 'solicitud' AND (t.idorigen = '$idsucursal' OR t.iddestino = '$idsucursal'))
                    OR (t.tipo = 'traslado' AND t.idorigen = '$idsucursal')
                )
            AND DATE(t.fecha) BETWEEN '$fecha_inicio' AND '$fecha_fin'";

    if ($estado != "Todos") {
        $sql .= " AND t.estado = '$estado'";
    }

    $sql .= " ORDER BY t.idtraslado DESC";

    return ejecutarConsulta($sql);
}


	public function sucursales($idsucursal_origen)
	{
		$sql = "SELECT idsucursal, nombre FROM sucursal";
		return ejecutarConsulta($sql);
	}

	// Listar productos: ahora agrega data-stock en el checkbox
	public function listarProductos($idsucursal, $busqueda = '', $pagina = 1, $limite = 10, $iddestino = null, $tipo = 'traslado')
{
    $offset = ($pagina - 1) * $limite;
    $filtro = "";
    if ($busqueda != '') {
        $filtro = "AND (p.nombre LIKE '%$busqueda%' OR p.codigo LIKE '%$busqueda%')";
    }

    $sql = "SELECT p.idproducto, p.nombre, p.codigo, p.stock
            FROM producto p
            WHERE p.condicion = 1 AND p.idsucursal = '$idsucursal' $filtro
            LIMIT $offset, $limite";
    $result = ejecutarConsulta($sql);

    $existentes = [];

    // 🔹 Solo comprobar productos existentes en destino si es un traslado directo
    if (!empty($iddestino) && $tipo === 'traslado') {
        $sqlDestino = "SELECT nombre FROM producto WHERE idsucursal = '$iddestino' AND condicion = 1";
        $resDestino = ejecutarConsulta($sqlDestino);
        while ($row = $resDestino->fetch_assoc()) {
            $existentes[strtolower(trim($row['nombre']))] = true;
        }
    }

    $productos = "";
    while ($reg = $result->fetch_object()) {
        $yaExiste = isset($existentes[strtolower(trim($reg->nombre))]);
        $clase = $yaExiste ? 'bg-success text-white' : '';
        $etiqueta = $yaExiste ? '<small class="badge bg-warning text-dark">Ya existe en destino</small>' : '';

        $productos .= "
            <tr class='$clase'>
                <td>
                    <input type='checkbox' class='chkProducto' value='{$reg->idproducto}'
                        data-nombre='" . htmlspecialchars($reg->nombre) . "'
                        data-stock='{$reg->stock}'>
                </td>
                <td>{$reg->codigo}</td>
                <td>" . htmlspecialchars($reg->nombre) . " $etiqueta</td>
                <td>{$reg->stock}</td>
            </tr>";
    }

    $sqlTotal = "SELECT COUNT(*) AS total FROM producto p WHERE p.condicion=1 AND p.idsucursal='$idsucursal' $filtro";
    $totalRes = ejecutarConsultaSimpleFila($sqlTotal);
    $total = $totalRes['total'];
    $totalPaginas = ceil($total / $limite);

    $paginacion = '';
    for ($i = 1; $i <= $totalPaginas; $i++) {
        $active = ($i == $pagina) ? 'btn-primary' : 'btn-outline-primary';
        $paginacion .= "<button class='btn btn-sm $active m-1' onclick='cambiarPagina($i)'>$i</button>";
    }

    return ["html" => $productos, "paginacion" => $paginacion];
}


	public function listarNotificaciones($idsucursal) {
    $sql = "SELECT 
                n.idnotificacion,
                n.mensaje,
                n.leido,
                n.fecha,
                n.idtraslado,
                t.iddestino,
                t.tipo
            FROM notificaciones n
            LEFT JOIN traslado t ON n.idtraslado = t.idtraslado
            WHERE n.idsucursal = '$idsucursal'
            ORDER BY n.fecha DESC";
    return ejecutarConsulta($sql);
}


	public function marcarLeida($idnotificacion) {
	    $sql = "UPDATE notificaciones SET leido=1 WHERE idnotificacion='$idnotificacion'";
	    return ejecutarConsulta($sql);
	}

	public function listarDetalle($idtraslado)
	{
	    $sql = "SELECT td.cantidad, p.nombre AS producto, s2.nombre AS destino
	            FROM traslado_detalle td
	            INNER JOIN producto p ON td.idproducto = p.idproducto
	            INNER JOIN traslado t ON td.idtraslado = t.idtraslado
	            INNER JOIN sucursal s2 ON t.iddestino = s2.idsucursal
	            WHERE td.idtraslado = '$idtraslado'";
	    return ejecutarConsulta($sql);
	}

  public function aprobarSolicitud($idtraslado, $productos, $idusuario)
{
    ejecutarConsulta("START TRANSACTION");

    // Decodificar productos si vienen en JSON
    if (is_string($productos)) {
        $productos = json_decode($productos, true);
    }
    if (!is_array($productos)) {
        ejecutarConsulta("ROLLBACK");
        return "Error: formato de productos inválido.";
    }

    //  Obtener datos del traslado
    $info = ejecutarConsultaSimpleFila("SELECT * FROM traslado WHERE idtraslado='$idtraslado'");
    if (!$info) {
        ejecutarConsulta("ROLLBACK");
        return "Solicitud no encontrada.";
    }

    //  Definir sucursales
    $idsucursalPrincipal   = $info['iddestino']; // almacén que envía stock
    $idsucursalSolicitante = $info['idorigen'];  // almacén que recibe

    $aprobados = 0;

    foreach ($productos as $p) {
        $idproductoSolicitante = intval($p["idproducto"]);
        $estado                = strtolower(trim($p["estado"] ?? ''));
        $cantidad              = floatval($p["cantidad"] ?? 0);
        $observacion           = isset($p["observacion"]) ? limpiarCadena($p["observacion"]) : '';
        $nombreProducto        = isset($p["nombre"]) ? limpiarCadena($p["nombre"]) : '';

        if (!in_array($estado, ['aceptado', 'rechazado'])) continue;

        //  Actualizar detalle del traslado
        ejecutarConsulta("UPDATE traslado_detalle 
                          SET estado_detalle='$estado', 
                              observacion='$observacion', 
                              cantidad='$cantidad'
                          WHERE idtraslado='$idtraslado' AND idproducto='$idproductoSolicitante'");

        if ($estado === 'aceptado' && $cantidad > 0) {
            $aprobados++;

            //  Obtener producto en sucursal principal
            $productoPrincipal = ejecutarConsultaSimpleFila("
                SELECT * FROM producto
                WHERE nombre = '$nombreProducto' AND idsucursal = '$idsucursalPrincipal'
                LIMIT 1
            ");
            if (!$productoPrincipal) {
                ejecutarConsulta("ROLLBACK");
                return "No se encontró el producto '$nombreProducto' en la sucursal principal.";
            }
            $idproductoPrincipal = $productoPrincipal['idproducto'];

            // Verificar si el producto existe en la sucursal solicitante
            $productoSolicitante = ejecutarConsultaSimpleFila("
                SELECT idproducto FROM producto
                WHERE nombre = '$nombreProducto' AND idsucursal = '$idsucursalSolicitante'
                LIMIT 1
            ");

            if (!$productoSolicitante) {
                // Crear producto en la sucursal solicitante copiando del principal
                $sqlInsert = "INSERT INTO producto (
                    idsucursal, idcategoria, idunidad_medida, idrubro, idcondicionventa,
                    nombre, codigo, descripcion, precio, precioB, precioC, precioD, precioE,
                    preciocigv, precio_compra, proigv, stock_minimo, stock, imagen, condicion
                ) VALUES (
                    '{$idsucursalSolicitante}',
                    '{$productoPrincipal['idcategoria']}',
                    '{$productoPrincipal['idunidad_medida']}',
                    '{$productoPrincipal['idrubro']}',
                    '{$productoPrincipal['idcondicionventa']}',
                    '".addslashes($productoPrincipal['nombre'])."',
                    '".addslashes($productoPrincipal['codigo'])."',
                    '".addslashes($productoPrincipal['descripcion'])."',
                    '{$productoPrincipal['precio']}',
                    '{$productoPrincipal['precioB']}',
                    '{$productoPrincipal['precioC']}',
                    '{$productoPrincipal['precioD']}',
                    '{$productoPrincipal['precioE']}',
                    '{$productoPrincipal['preciocigv']}',
                    '{$productoPrincipal['precio_compra']}',
                    '".addslashes($productoPrincipal['proigv'])."',
                    '{$productoPrincipal['stock_minimo']}',
                    0,
                    '".($productoPrincipal['imagen'] ?? 'anonymous.png')."',
                    1
                )";
                $idNuevo = ejecutarConsulta_retornarID($sqlInsert);
                if (!$idNuevo) {
                    ejecutarConsulta("ROLLBACK");
                    return "Error al crear producto '$nombreProducto' en la sucursal solicitante.";
                }

                // Copiar configuraciones del producto principal (si las tiene)
                $configOrigen = ejecutarConsulta("SELECT * FROM producto_configuracion WHERE idproducto = '$idproductoPrincipal' AND deleted_at IS NULL");
                while ($conf = $configOrigen->fetch_assoc()) {
                    $sql_conf = "INSERT INTO producto_configuracion
                        (codigo_extra, contenedor, cantidad_contenedor, precio_venta, precio_promocion, idproducto)
                        VALUES (
                            '".addslashes($conf['codigo_extra'])."',
                            '".addslashes($conf['contenedor'])."',
                            ".floatval($conf['cantidad_contenedor']).",
                            ".floatval($conf['precio_venta']).",
                            ".floatval($conf['precio_promocion']).",
                            '$idNuevo')";
                    $idConfNuevo = ejecutarConsulta_retornarID($sql_conf);

                    // Copiar precios asociados
                    $precios = ejecutarConsulta("SELECT * FROM producto_configuracion_precios WHERE producto_configuracion_id = {$conf['id']} AND estado = 1");
                    while ($pconf = $precios->fetch_assoc()) {
                        ejecutarConsulta("INSERT INTO producto_configuracion_precios
                            (producto_configuracion_id, idnombre_p, precio, estado)
                            VALUES (
                                '$idConfNuevo',
                                '".addslashes($pconf['idnombre_p'])."',
                                '".floatval($pconf['precio'])."',
                                1
                            )");
                    }
                }

                $idproductoSolicitante = $idNuevo;
            } else {
                $idproductoSolicitante = $productoSolicitante['idproducto'];
            }

            // Registrar salida del principal
            $resSalida = $this->movimientoEntradaSalidaT_aprobacion(
                $idproductoPrincipal,
                $idsucursalPrincipal,
                1, // salida
                $cantidad,
                "Aprobación de solicitud #$idtraslado (envío desde sucursal principal)",
                $idusuario
            );
            if ($resSalida['status'] != 1) {
                ejecutarConsulta("ROLLBACK");
                return "Error en salida de stock (principal): ".$resSalida['message'];
            }

            // Registrar entrada en solicitante
            $resEntrada = $this->movimientoEntradaSalidaT_aprobacion(
                $idproductoSolicitante,
                $idsucursalSolicitante,
                0, // entrada
                $cantidad,
                "Ingreso por solicitud aprobada #$idtraslado (recepción en sucursal solicitante)",
                $idusuario
            );
            if ($resEntrada['status'] != 1) {
                ejecutarConsulta("ROLLBACK");
                return "Error en entrada de stock (solicitante): ".$resEntrada['message'];
            }
        }
    }

    //  Actualizar estado general del traslado
    $nuevoEstado = $aprobados > 0 ? '1' : '2';
    ejecutarConsulta("UPDATE traslado SET estado='$nuevoEstado' WHERE idtraslado='$idtraslado'");

    ejecutarConsulta("COMMIT");
    return "Solicitud procesada correctamente.";
}



public function movimientoEntradaSalidaT_aprobacion(
    $idproducto,
    $idsucursal,
    $tipo_movimiento,   // 0 = Entrada, 1 = Salida
    $cantidad,
    $motivo
) {
    date_default_timezone_set('America/Lima');

    $idproducto = intval($idproducto);
    $idsucursal = intval($idsucursal);
    $cantidad = floatval(str_replace(',', '.', $cantidad));
    $cantidad_contenedor = 1;

    if ($cantidad <= 0) {
        return ['status' => 0, 'message' => 'La cantidad debe ser mayor que cero'];
    }

    $intentos = 0;
    $max_intentos = 3;

    while ($intentos < $max_intentos) {
        try {
            $intentos++;
            ejecutarConsulta("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
            ejecutarConsulta("BEGIN");

            //  Buscar producto exacto en esa sucursal
            $sql = "SELECT idproducto, stock, precio, nombre, codigo
                    FROM producto
                    WHERE idproducto = '$idproducto' AND idsucursal = '$idsucursal'
                    FOR UPDATE";
            $res = ejecutarConsulta($sql);

            $producto = $res ? $res->fetch_object() : null;

            //  Si no se encuentra, intentar buscar por nombre o código
            if (!$producto) {
                $prodOrigen = ejecutarConsultaSimpleFila("SELECT nombre, codigo FROM producto WHERE idproducto='$idproducto' LIMIT 1");
                if ($prodOrigen) {
                    $nombre = $prodOrigen['nombre'];
                    $codigo = $prodOrigen['codigo'];

                    $sqlAlt = "SELECT idproducto, stock, precio 
                               FROM producto 
                               WHERE (nombre = '" . addslashes($nombre) . "' OR codigo = '" . addslashes($codigo) . "') 
                                 AND idsucursal = '$idsucursal'
                               LIMIT 1 FOR UPDATE";
                    $resAlt = ejecutarConsulta($sqlAlt);
                    $producto = $resAlt ? $resAlt->fetch_object() : null;
                }
            }

            // Si aun así no se encuentra
            if (!$producto) {
                ejecutarConsulta("ROLLBACK");
                return [
                    'status' => 0,
                    'message' => "No se encontró producto equivalente (id:$idproducto) en la sucursal $idsucursal"
                ];
            }

            $idproducto_real = intval($producto->idproducto);
            $stock_actual = floatval($producto->stock);
            $precio = floatval($producto->precio);
            $fecha_kardex = date('Y-m-d H:i:s');

            // 🔹 Procesar movimiento según tipo
            if ($tipo_movimiento == 0) {
                //  ENTRADA - Registrar en stock_fifo
                $nuevo_stock = round($stock_actual + $cantidad, 2);
                $tipo_descripcion = "Entrada por aprobación de solicitud";

                //  Insertar en stock_fifo
                $sql_fifo = "INSERT INTO stock_fifo 
                    (idsucursal, idproducto, producto_configuracion_id, origen, referencia_id,
                     cantidad_ingreso, cantidad_restante, precio_compra, precio_venta, 
                     fecha_ingreso, estado, fvencimiento)
                    VALUES (
                        '$idsucursal',
                        '$idproducto_real',
                        NULL,
                        'ALMACEN',
                        NULL,
                        '$cantidad',
                        '$cantidad',
                        '$precio',
                        '$precio',
                        '$fecha_kardex',
                        1,
                        NULL
                    )";
                
                if (!ejecutarConsulta($sql_fifo)) {
                    throw new Exception("Error al registrar entrada en stock_fifo");
                }

            } else {
                //  SALIDA - Consumir de stock_fifo usando FIFO
                $tipo_descripcion = "Salida por aprobación de solicitud";
                
                if ($stock_actual < $cantidad) {
                    ejecutarConsulta("ROLLBACK");
                    return [
                        'status' => 0,
                        'message' => "Stock insuficiente en sucursal (actual: $stock_actual, salida: $cantidad)"
                    ];
                }

                // Consumir stock usando FIFO
                $cantidad_restante_salida = $cantidad;
                
                $sql_lotes = "SELECT idfifo, cantidad_restante, precio_compra 
                              FROM stock_fifo 
                              WHERE idsucursal = '$idsucursal' 
                                AND idproducto = '$idproducto_real' 
                                AND cantidad_restante > 0 
                                AND estado = 1
                              ORDER BY fecha_ingreso ASC, idfifo ASC
                              FOR UPDATE";
                
                $res_lotes = ejecutarConsulta($sql_lotes);
                
                if (!$res_lotes || $res_lotes->num_rows == 0) {
                    throw new Exception("No hay lotes FIFO disponibles para la salida");
                }

                while ($lote = $res_lotes->fetch_object()) {
                    if ($cantidad_restante_salida <= 0) break;

                    $idfifo = $lote->idfifo;
                    $cant_disp = floatval($lote->cantidad_restante);
                    
                    if ($cant_disp >= $cantidad_restante_salida) {
                        // Este lote cubre todo
                        $nueva_cant = $cant_disp - $cantidad_restante_salida;
                        $sql_upd = "UPDATE stock_fifo 
                                    SET cantidad_restante = '$nueva_cant' 
                                    WHERE idfifo = '$idfifo'";
                        ejecutarConsulta($sql_upd);
                        $cantidad_restante_salida = 0;
                    } else {
                        // Agotar este lote
                        $sql_upd = "UPDATE stock_fifo 
                                    SET cantidad_restante = 0 
                                    WHERE idfifo = '$idfifo'";
                        ejecutarConsulta($sql_upd);
                        $cantidad_restante_salida -= $cant_disp;
                    }
                }

                if ($cantidad_restante_salida > 0) {
                    throw new Exception("No se pudo descontar toda la cantidad de stock_fifo");
                }

                $nuevo_stock = round($stock_actual - $cantidad, 2);
            }

            // Actualizar stock del producto
            $sql_update = "UPDATE producto 
                           SET stock = '$nuevo_stock' 
                           WHERE idproducto = '$idproducto_real' 
                             AND idsucursal = '$idsucursal'";
            if (!ejecutarConsulta($sql_update)) {
                throw new Exception("Error al actualizar stock");
            }

            //  Registrar movimiento en kardex
            $sql_kardex = "INSERT INTO kardex 
                (idsucursal, idproducto, cantidad, cantidad_contenedor, precio_unitario, 
                 stock_actual, tipo_movimiento, motivo, descripcion, fecha_kardex)
                VALUES (
                    '$idsucursal',
                    '$idproducto_real',
                    '$cantidad',
                    '$cantidad_contenedor',
                    '$precio',
                    '$nuevo_stock',
                    '$tipo_movimiento',
                    '$tipo_descripcion',
                    '$motivo',
                    '$fecha_kardex'
                )";
            if (!ejecutarConsulta($sql_kardex)) {
                throw new Exception("Error al registrar movimiento en kardex");
            }

            ejecutarConsulta("COMMIT");

            return [
                'status' => 1,
                'message' => "Movimiento registrado correctamente ($tipo_descripcion)",
                'stock_anterior' => $stock_actual,
                'stock_nuevo' => $nuevo_stock,
                'fecha' => $fecha_kardex
            ];

        } catch (Exception $e) {
            ejecutarConsulta("ROLLBACK");

            if (stripos($e->getMessage(), 'deadlock') !== false ||
                stripos($e->getMessage(), 'lock wait timeout') !== false) {
                if ($intentos < $max_intentos) {
                    usleep(200000);
                    continue;
                }
            }

            return ['status' => 0, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    return [
        'status' => 0,
        'message' => 'No se pudo completar el movimiento tras varios intentos.'
    ];
}

public function verProductosSolicitud($idtraslado)
{
    $sql = "SELECT 
                td.idproducto, 
                p.nombre, 
                td.cantidad,
                td.estado_detalle,
                td.observacion
            FROM traslado_detalle td
            INNER JOIN producto p ON td.idproducto = p.idproducto
            WHERE td.idtraslado = '$idtraslado'";
    return ejecutarConsulta($sql);
}



public function obtenerSucursalOrigen($idtraslado) {
    $sql = "SELECT s1.nombre AS origen
            FROM traslado t
            INNER JOIN sucursal s1 ON t.idorigen = s1.idsucursal
            WHERE t.idtraslado = '$idtraslado'";
    return ejecutarConsultaSimpleFila($sql);
}

public function mostrarCabecera($idtraslado) {
    $sql = "SELECT 
                t.idtraslado, 
                CONCAT(DATE_FORMAT(t.fecha, '%d/%m/%Y'), ' HORA: ', DATE_FORMAT(t.fecha, '%h:%i %p')) AS fecha, 
                so.nombre AS origen, 
                sd.nombre AS destino,
                t.idorigen, 
                t.iddestino,
                t.estado
            FROM traslado t
            INNER JOIN sucursal so ON t.idorigen = so.idsucursal
            INNER JOIN sucursal sd ON t.iddestino = sd.idsucursal
            WHERE t.idtraslado = '$idtraslado'";
    return ejecutarConsulta($sql);
}


public function listarDetalleTicket($idtraslado) {
    $sql = "SELECT dt.cantidad, p.nombre AS producto, um.nombre AS unidad,dt.estado_detalle
            FROM traslado_detalle dt
            INNER JOIN producto p ON dt.idproducto = p.idproducto
            INNER JOIN unidad_medida um ON p.idunidad_medida=um.idunidad_medida
            WHERE dt.idtraslado = '$idtraslado'";
    return ejecutarConsulta($sql);
}


}
?>
