<?php
require "../configuraciones/Conexion.php";
date_default_timezone_set('America/Lima');
require_once __DIR__ . "/../configuraciones/ConexionPdo.php";
require_once __DIR__ . "/../core/Paginanation.php";
require_once __DIR__ . "/../core/FluentSave.php";
require_once __DIR__ . "/Helpers.php";

class Traslado extends Helpers
{
    public PDO $pdo;
    //Implementamos nuestro constructor
    public function __construct()
    {
        $this->pdo = Conexion::conectar();
    }

    /**
     * Insertar traslado con:
     * - validaciones de stock en origen
     * - creación automática en destino si producto no existe
     * - transacción (START/COMMIT/ROLLBACK)
     * Devuelve un string con el mensaje (éxito o error).
     */
    public function insertar($idorigen, $iddestino, $fecha, $productos_json, $idusuario)
    {
        try {
            if ($idorigen == $iddestino) {
                throw new Exception("El almacén destino debe ser distinto al origen.");
            }

            $productos = json_decode($productos_json, true);
            if (!is_array($productos) || count($productos) === 0) {
                throw new Exception("No se enviaron productos para traslado.");
            }

            $this->pdo->beginTransaction();

            // Validar stock en origen
            foreach ($productos as $p) {
                $idproducto = intval($p["idproducto"]);
                $cantidad = floatval($p["cantidad"]);
                $sql = "SELECT i.*,  p.precio FROM inventario_producto i INNER JOIN producto p ON p.idproducto=i.idproducto 
                WHERE i.idproducto=:idproducto AND i.idsucursal=:idsucursal FOR UPDATE";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'idproducto' => $idproducto,
                    'idsucursal' => $idorigen
                ]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$row) {
                    throw new Exception("Producto {$idproducto} no existe en almacén origen.");
                }
                if ($row['stock'] < $cantidad) {
                    throw new Exception("Stock insuficiente de {$idproducto} en almacén origen.");
                }
            }

            // Insertar cabecera traslado como REALIZADO
            $traslado = (new FluentSaver($this->pdo))
                ->table('traslado')
                ->nullable([
                    'idsolicitud_origen',
                    'fecha_aceptacion',
                    'idusuario_acepta'
                ])
                ->data([
                    'correlativo' => Helpers::correlativoTraslado($this->pdo, $idorigen, 'traslado'),
                    'idorigen' => $idorigen,
                    'iddestino' => $iddestino,
                    'fecha' => $fecha,
                    'estado' => 'en_transito', // estado inicial
                    'idusuario' => $idusuario,
                    'tipo' => 'traslado'
                ])
                ->save();
            if (!$traslado) {
                throw new Exception("Error al registrar cabecera de traslado.");
            }

            // Insertar detalle y registrar salida en origen
            foreach ($productos as $p) {
                $idproducto = intval($p["idproducto"]);
                $idserie = intval($p["idserie"]);
                $cantidad = floatval($p["cantidad"]);

                // Insertar detalle
                $traslado_detalle = (new FluentSaver($this->pdo))
                    ->table('traslado_detalle')
                    ->nullable([
                        'idserie',
                        'cantidad_recibida',
                    ])
                    ->data([
                        'idtraslado' => $traslado,
                        'idproducto' => $idproducto,
                        'idserie' => $idserie,
                        'cantidad_enviada' => $cantidad,
                        'estado_detalle' => 'pendiente',
                        'observacion' => ''
                    ])
                    ->save();
                // Insertar detalle con estado pendiente y sin observación
                if (!$traslado_detalle) {
                    throw new Exception("Error al registrar detalle del producto $idproducto.");
                }     
            }

            $this->pdo->commit();
            return json_encode([
                'success' => true,
                'message' => 'Se ha creado el traslado correctamente'
            ]);
           
        } catch (Throwable $e) {
            if (isset($this->pdo) && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return json_encode(["success" => false, "message" => "Error al guardar los datos: " . $e->getMessage()]);
        }
    }


    public function movimientoIngreso(
        $rowProduct,
        $iddestino,
        $idserie,
        $cantidad,
        $motivo = ''
    ) {

        // Obtener la serie original
        $stmt = $this->pdo->prepare("
                SELECT *
                FROM producto_serie
                WHERE idserie = :idserie
            ");

        $stmt->execute([
            'idserie' => $idserie
        ]);

        $serie = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$serie) {
            throw new Exception("No se encontró la serie.");
        }

        // 1. Crear nuevo producto
        $nuevoProducto = $rowProduct;

        unset(
            $nuevoProducto['idproducto'],
            $nuevoProducto['created_at'],
            $nuevoProducto['updated_at']
        );

        $nuevoProducto['idsucursal'] = $iddestino;

        $idproductoDestino = (new FluentSaver($this->pdo))
            ->table('producto')
            ->data($nuevoProducto)
            ->save();

        if (!$idproductoDestino) {
            throw new Exception("No se pudo crear el producto destino.");
        }

        // 2. Inventario
        // Obtener inventario del origen
        $stmt = $this->pdo->prepare("
                SELECT *
                FROM inventario_producto
                WHERE idproducto = :idproducto
                AND idsucursal = :idsucursal
            ");

        $stmt->execute([
            'idproducto' => $rowProduct['idproducto'],
            'idsucursal' => $rowProduct['idsucursal']
        ]);

        $inventarioOrigen = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$inventarioOrigen) {
            throw new Exception("No existe inventario del producto en el almacén origen.");
        }

        (new FluentSaver($this->pdo))
            ->table('inventario_producto')
            ->data([
                'idproducto' => $idproductoDestino,
                'idsucursal' => $iddestino,
                'stock' => $cantidad,
                'stock_minimo' => $inventarioOrigen['stock_minimo'],
                'stock_maximo' => $inventarioOrigen['stock_maximo'],
                'precio_compra' => $inventarioOrigen['precio_compra']
            ])
            ->save();

        // 3. Configuración
        $config = $this->pdo->prepare("
            SELECT *
            FROM producto_configuracion
            WHERE idproducto = :idproducto
        ");

        $config->execute([
            'idproducto' => $rowProduct['idproducto']
        ]);

        $rowConfiguracion = $config->fetch(PDO::FETCH_ASSOC);

        unset($rowConfiguracion['idproducto_configuracion']);

        $rowConfiguracion['idproducto'] = $idproductoDestino;

        $saveConfig = (new FluentSaver($this->pdo))
            ->table('producto_configuracion')
            ->data($rowConfiguracion)
            ->save();

        // 4. Serie
        unset($serie['idserie']);
        $serie['idproducto'] = $idproductoDestino;
        $serie['idsucursal'] = $iddestino;
        $serie['estado'] = 'DISPONIBLE';

        (new FluentSaver($this->pdo))
            ->table('producto_serie')
            ->data($serie)
            ->save();

        // Actualizar kardex si es necesario
        $ingreso = 1;
        if ($nuevoProducto['controla_stock'] === 'Si') {
            Helpers::updateKardexSucursal(
                $this->pdo,
                $iddestino,
                $idproductoDestino,
                $saveConfig,
                $cantidad,
                $cantidad * $rowConfiguracion['cantidad_contenedor'],
                $nuevoProducto['precio'],
                0,
                $ingreso,
                'Ingreso por transferencia',
                $motivo
            );
        }
        return [
            'success' => true,
            'message' => ''
        ];
    }

    public function movimientoSalida(
        $rowProduct,
        $idsucursal,
        $idserie,
        $cantidad,
        $motivo = ''
    ) {

        // 1. Cambiar estado de la serie
        (new FluentSaver($this->pdo))
            ->table('producto_serie')
            ->primaryKey('idserie')
            ->data([
                'idserie' => $idserie,
                'estado' => 'TRASLADO'
            ])
            ->save();

        // 2. Obtener inventario de la sucursal
        $sql = "SELECT *
            FROM inventario_producto
            WHERE idproducto = :idproducto
              AND idsucursal = :idsucursal
            FOR UPDATE";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'idproducto' => $rowProduct['idproducto'],
            'idsucursal' => $idsucursal
        ]);

        $inventario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$inventario) {
            throw new Exception("No existe inventario del producto.");
        }

        if ($inventario['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente.");
        }

        // 3. Actualizar stock
        (new FluentSaver($this->pdo))
            ->table('inventario_producto')
            ->primaryKey('idinventario')
            ->data([
                'idinventario' => $inventario['idinventario'],
                'stock' => $inventario['stock'] - $cantidad
            ])
            ->save();

        // Actualizar kardex si es necesario
        $config = $this->pdo->prepare("
            SELECT *
            FROM producto_configuracion
            WHERE idproducto = :idproducto
        ");
        $config->execute([
            'idproducto' => $rowProduct['idproducto']
        ]);

        $rowConfiguracion = $config->fetch(PDO::FETCH_ASSOC);
        $salida = 0;
        if ($rowProduct['controla_stock'] === 'Si') {
            Helpers::updateKardexSucursal(
                $this->pdo,
                $idsucursal,
                $rowProduct['idproducto'],
                $rowConfiguracion['idproducto_configuracion'],
                $cantidad,
                $cantidad * $rowConfiguracion['cantidad_contenedor'],
                $rowProduct['precio'],
                0,
                $salida,
                'Salida por transferencia',
                $motivo
            );
        }

        return [
            "success" => true,
            "message" => ""
        ];
    }

    public function guardarSolicitud($idorigen, $iddestino, $productos, $idusuario){
        try {
            $this->pdo->beginTransaction();
            $fecha = date("Y-m-d H:i:s");
            // Insertar cabecera traslado como PENDIENTE
            $traslado = (new FluentSaver($this->pdo))
                ->table('traslado')
                ->nullable([
                    'idsolicitud_origen',
                    'fecha_aceptacion',
                    'idusuario_acepta'
                ])
                ->data([
                    'correlativo' => Helpers::correlativoTraslado($this->pdo, $idorigen, 'solicitud'),
                    'idorigen' => $idorigen,
                    'iddestino' => $iddestino,
                    'fecha' => $fecha,
                    'estado' => 'pendiente', // estado inicial
                    'idusuario' => $idusuario,
                    'tipo' => 'solicitud'
                ])
                ->save();
            if (!$traslado) {
                throw new Exception("Error al registrar cabecera de traslado.");
            }

            // Insertar detalle y registrar salida en origen
            $productos = json_decode($productos, true);
            foreach ($productos as $p) {
                $idproducto = intval($p["idproducto"]);
                $idserie = intval($p["idserie"]);
                $cantidad = floatval($p["cantidad"]);

                // Insertar detalle
                $traslado_detalle = (new FluentSaver($this->pdo))
                    ->table('traslado_detalle')
                    ->nullable([
                        'idserie',
                        'cantidad_recibida',
                    ])
                    ->data([
                        'idtraslado' => $traslado,
                        'idproducto' => $idproducto,
                        'idserie' => $idserie,
                        'cantidad_enviada' => $cantidad,
                        'estado_detalle' => 'pendiente',
                        'observacion' => ''
                    ])
                    ->save();
                // Insertar detalle con estado pendiente y sin observación
                if (!$traslado_detalle) {
                    throw new Exception("Error al registrar detalle del producto $idproducto.");
                }
            }
            $nombreOrigen = $_SESSION['nombre_sucursal'] ?? "Almacén $idorigen";
            $mensaje = "Nueva solicitud pendiente desde el almacén {$nombreOrigen} con ID $traslado";
            (new FluentSaver($this->pdo))
                ->table('notificaciones')
                ->data([
                    'idsucursal' => $iddestino,
                    'idtraslado' => $traslado,
                    'mensaje' => $mensaje
                ])
                ->save();

            $this->pdo->commit();
            return json_encode([
                'success' => true,
                'message' => 'Se ha creado el traslado correctamente'
            ]);
            
        } catch (Throwable $e) {
            if (isset($this->pdo) && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return json_encode(["success" => false, "message" => "Error al guardar los datos: " . $e->getMessage()]);
        }
		
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

        $idorigen = intval($traslado['idorigen']);
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
            $cantidad = floatval($reg->cantidad);

            // 🔹 Buscar producto en origen
            $origen = ejecutarConsultaSimpleFila("SELECT * FROM producto WHERE idproducto='$idproducto' AND idsucursal='$idorigen'");
            if (!$origen) {
                ejecutarConsulta("ROLLBACK");
                return "Producto $idproducto no encontrado en almacén origen.";
            }

            // 🔹 Preparar datos
            $nombre = addslashes($origen['nombre'] ?? '');
            $codigo = trim($origen['codigo'] ?? '');
            $descripcion = addslashes($origen['descripcion'] ?? '');
            $idcategoria = $origen['idcategoria'] ?? "NULL";
            $idunidad = $origen['idunidad_medida'] ?? 1;
            $idrubro = $origen['idrubro'] ?? 14;
            $idcondicion = $origen['idcondicionventa'] ?? 4;
            $precio = floatval($origen['precio'] ?? 0);
            $precioB = floatval($origen['precioB'] ?? 0);

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

    // public function movimientoEntradaSalidaT(
    //     $idproducto,
    //     $idsucursal,
    //     $tipo_movimiento,
    //     $cantidad,
    //     $motivo,
    //     $cantidad_contenedor = 1,
    //     $precio_unitario = null,
    //     $idproducto_origen = null
    // ) {
    //     date_default_timezone_set('America/Lima');

    //     $idproducto = intval($idproducto);
    //     $idsucursal = intval($idsucursal);
    //     $cantidad = floatval(str_replace(',', '.', $cantidad));
    //     $cantidad_contenedor = floatval(str_replace(',', '.', $cantidad_contenedor));
    //     if ($cantidad_contenedor <= 0)
    //         $cantidad_contenedor = 1;

    //     $total_unidades = round($cantidad * $cantidad_contenedor, 2);
    //     if ($total_unidades <= 0) {
    //         return ['status' => 0, 'message' => 'La cantidad total no puede ser cero o negativa'];
    //     }

    //     $intentos = 0;
    //     $max_intentos = 3;

    //     while ($intentos < $max_intentos) {
    //         try {
    //             $intentos++;
    //             ejecutarConsulta("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
    //             ejecutarConsulta("BEGIN");

    //             $sql_producto = "SELECT idproducto, stock, precio 
    //                          FROM producto 
    //                          WHERE idproducto = '$idproducto' 
    //                            AND idsucursal = '$idsucursal'
    //                          FOR UPDATE";
    //             $res = ejecutarConsulta($sql_producto);
    //             $producto = $res ? $res->fetch_object() : null;

    //             $existe = ($producto !== null);
    //             $stock_actual = $existe ? floatval($producto->stock) : 0;

    //             // Determinar precio
    //             if ($tipo_movimiento == 1) {
    //                 $precio = ($precio_unitario !== null)
    //                     ? floatval($precio_unitario)
    //                     : floatval($producto->precio ?? 0);
    //             } else {
    //                 if ($existe) {
    //                     $precio = ($precio_unitario !== null)
    //                         ? floatval($precio_unitario)
    //                         : floatval($producto->precio);
    //                 } else {
    //                     $precio = 0;
    //                     if ($idproducto_origen) {
    //                         $sql_origen = "SELECT precio FROM producto WHERE idproducto = '$idproducto_origen'";
    //                         $res_origen = ejecutarConsultaSimpleFila($sql_origen);
    //                         if ($res_origen && isset($res_origen['precio'])) {
    //                             $precio = floatval($res_origen['precio']);
    //                         }
    //                     }
    //                     if ($precio <= 0 && $precio_unitario !== null) {
    //                         $precio = floatval($precio_unitario);
    //                     }
    //                 }
    //             }

    //             $fecha_kardex = date('Y-m-d H:i:s');

    //             //  Procesar movimiento
    //             if ($tipo_movimiento == 0) {
    //                 // ENTRADA - Registrar en stock_fifo
    //                 $nuevo_stock = round($stock_actual + $total_unidades, 2);
    //                 $type = "Entrada de almacén por Traslado";

    //                 // Insertar en stock_fifo
    //                 $sql_fifo = "INSERT INTO stock_fifo 
    //                 (idsucursal, idproducto, producto_configuracion_id, origen, referencia_id,
    //                  cantidad_ingreso, cantidad_restante, precio_compra, precio_venta, 
    //                  fecha_ingreso, estado, fvencimiento)
    //                 VALUES (
    //                     '$idsucursal',
    //                     '$idproducto',
    //                     NULL,
    //                     'ALMACEN',
    //                     NULL,
    //                     '$total_unidades',
    //                     '$total_unidades',
    //                     '$precio',
    //                     '$precio',
    //                     '$fecha_kardex',
    //                     1,
    //                     NULL
    //                 )";

    //                 if (!ejecutarConsulta($sql_fifo)) {
    //                     throw new Exception("Error al registrar entrada en stock_fifo");
    //                 }

    //             } else {
    //                 //  SALIDA - Consumir de stock_fifo usando FIFO
    //                 $type = "Salida de almacén por Traslado";

    //                 if ($stock_actual < $total_unidades) {
    //                     ejecutarConsulta("ROLLBACK");
    //                     return ['status' => 0, 'message' => "Stock insuficiente (actual: $stock_actual, salida: $total_unidades)"];
    //                 }

    //                 // Consumir stock usando FIFO
    //                 $cantidad_restante_salida = $total_unidades;

    //                 $sql_lotes = "SELECT idfifo, cantidad_restante, precio_compra 
    //                           FROM stock_fifo 
    //                           WHERE idsucursal = '$idsucursal' 
    //                             AND idproducto = '$idproducto' 
    //                             AND cantidad_restante > 0 
    //                             AND estado = 1
    //                           ORDER BY fecha_ingreso ASC, idfifo ASC
    //                           FOR UPDATE";

    //                 $res_lotes = ejecutarConsulta($sql_lotes);

    //                 if (!$res_lotes || $res_lotes->num_rows == 0) {
    //                     throw new Exception("No hay lotes FIFO disponibles para la salida");
    //                 }

    //                 while ($lote = $res_lotes->fetch_object()) {
    //                     if ($cantidad_restante_salida <= 0)
    //                         break;

    //                     $idfifo = $lote->idfifo;
    //                     $cant_disp = floatval($lote->cantidad_restante);

    //                     if ($cant_disp >= $cantidad_restante_salida) {
    //                         // Este lote cubre todo
    //                         $nueva_cant = $cant_disp - $cantidad_restante_salida;
    //                         $sql_upd = "UPDATE stock_fifo 
    //                                 SET cantidad_restante = '$nueva_cant' 
    //                                 WHERE idfifo = '$idfifo'";
    //                         ejecutarConsulta($sql_upd);
    //                         $cantidad_restante_salida = 0;
    //                     } else {
    //                         // Agotar este lote
    //                         $sql_upd = "UPDATE stock_fifo 
    //                                 SET cantidad_restante = 0 
    //                                 WHERE idfifo = '$idfifo'";
    //                         ejecutarConsulta($sql_upd);
    //                         $cantidad_restante_salida -= $cant_disp;
    //                     }
    //                 }

    //                 if ($cantidad_restante_salida > 0) {
    //                     throw new Exception("No se pudo descontar toda la cantidad de stock_fifo");
    //                 }

    //                 $nuevo_stock = round($stock_actual - $total_unidades, 2);
    //             }

    //             // Actualizar stock del producto
    //             $sql_update = "UPDATE producto SET stock = '$nuevo_stock' 
    //                        WHERE idproducto = '$idproducto' 
    //                          AND idsucursal = '$idsucursal'";
    //             if (!ejecutarConsulta($sql_update)) {
    //                 throw new Exception("Error al actualizar stock del producto");
    //             }

    //             // Registrar en kardex
    //             $sql_kardex = "INSERT INTO kardex 
    //             (idsucursal, idproducto, cantidad, cantidad_contenedor, precio_unitario, 
    //              stock_actual, tipo_movimiento, motivo, descripcion, fecha_kardex)
    //             VALUES (
    //                 '$idsucursal',
    //                 '$idproducto',
    //                 '$total_unidades',
    //                 '$cantidad_contenedor',
    //                 '$precio',
    //                 '$nuevo_stock',
    //                 '$tipo_movimiento',
    //                 '$type',
    //                 '$motivo',
    //                 '$fecha_kardex'
    //             )";
    //             if (!ejecutarConsulta($sql_kardex)) {
    //                 throw new Exception("Error al registrar movimiento en kardex");
    //             }

    //             ejecutarConsulta("COMMIT");

    //             return [
    //                 'status' => 1,
    //                 'message' => "Movimiento registrado correctamente ($type)",
    //                 'stock_anterior' => $stock_actual,
    //                 'stock_nuevo' => $nuevo_stock,
    //                 'precio_usado' => $precio,
    //                 'fecha' => $fecha_kardex
    //             ];

    //         } catch (Exception $e) {
    //             ejecutarConsulta("ROLLBACK");

    //             if (
    //                 stripos($e->getMessage(), 'deadlock') !== false ||
    //                 stripos($e->getMessage(), 'lock wait timeout') !== false
    //             ) {
    //                 if ($intentos < $max_intentos) {
    //                     usleep(200000);
    //                     continue;
    //                 }
    //             }

    //             return ['status' => 0, 'message' => 'Error: ' . $e->getMessage()];
    //         }
    //     }

    //     return ['status' => 0, 'message' => 'No se pudo completar el movimiento tras varios intentos por concurrencia.'];
    // }


    // Función opcional para crear notificación al crear traslado
    public function crearNotificacionTraslado($idtraslado, $idsucursal, $mensaje)
    {
        $sql = "INSERT INTO notificaciones (idsucursal, mensaje, leido, fecha, idtraslado)
                VALUES ('$idsucursal', '$mensaje', 0, NOW(), '$idtraslado')";
        return ejecutarConsulta($sql);
    }


    // listar (igual que antes)
    public function listar($fecha_inicio, $fecha_fin, $estado, $idsucursal, $tipo, $origen = false)
    {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $search = $_GET['search'] ?? '';

        $paginator = (new FluentPaginator($this->pdo))
            ->query("
                SELECT
                    t.*,
                    s1.nombre AS origen,
                    s2.nombre AS destino,
                    CASE
                        WHEN t.estado = 'recibido' THEN 'Recibido'
                        WHEN t.estado = 'pendiente' THEN 'Pendiente'
                        WHEN t.estado = 'en_transito' THEN 'En Tránsito'
                        WHEN t.estado = 'cancelado' THEN 'Cancelado'
                        WHEN t.estado = 'rechazado' THEN 'Rechazado'
                        ELSE 'Pendiente'
                    END AS estado_str,
                    u1.login AS usuario_solicita,
                    u2.login AS usuario_acepta
                FROM traslado t
                INNER JOIN sucursal s1
                    ON s1.idsucursal = t.idorigen
                INNER JOIN sucursal s2
                    ON s2.idsucursal = t.iddestino
                INNER JOIN usuario u1
                    ON u1.idusuario = t.idusuario
                LEFT JOIN usuario u2
                    ON u2.idusuario = t.idusuario_acepta
            ")
            ->where("t.tipo", "=", $tipo);

        if ($tipo === 'solicitud') {

            if ($origen) {
                $paginator->where("t.idorigen", "=", $idsucursal);
            } else {
                $paginator->where("t.iddestino", "=", $idsucursal);
            }

        } else { // traslado
            $paginator->whereRaw(
    "(t.idorigen = :sucursal1 OR t.iddestino = :sucursal2)",
    [
        'sucursal1' => $idsucursal,
        'sucursal2' => $idsucursal
    ]
);
        }
        
        // Solo filtrar fechas si ambas existen
        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $paginator->where("DATE(t.fecha)", "BETWEEN", [
                $fecha_inicio,
                $fecha_fin
            ]);
        }

        // Solo filtrar estado cuando no sea "Todos"
        if (!empty($estado) && $estado != "Todos") {
            $paginator->where("t.estado", "=", $estado);
        }

        $response = $paginator
            ->search($search, [
                't.idtraslado',
                's1.nombre',
                's2.nombre'
            ])
            ->orderBy("t.idtraslado", "DESC")
            ->paginate(
                (int) $page,
                (int) $limit
            );

        return json_encode($response);
    }


    public function sucursales($idsucursal_origen)
    {
        $sql = "SELECT idsucursal, nombre FROM sucursal";
        return ejecutarConsulta($sql);
    }


    public function listarSucursales()
    {
        $sql = "SELECT * FROM sucursal";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($rows);
    }

    public function listarNotificaciones($idsucursal)
    {
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


    public function marcarLeida($idnotificacion)
    {
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
        try {
            if (!$idtraslado) {
                throw new Exception("No se ha seleccionado el traslado a procesar.");
            }

            $this->pdo->beginTransaction();

            $fecha = date('Y-m-d H:i:s');
            // obtenemos la solicitud
            $sqlSolicitud = "
                                SELECT *
                                FROM traslado
                                WHERE idtraslado = :idtraslado
                                FOR UPDATE
                                ";
            $stmtSolicitud = $this->pdo->prepare($sqlSolicitud);
            $stmtSolicitud->execute([
                'idtraslado' => $idtraslado
            ]);
            $rowSolicitud = $stmtSolicitud->fetch(PDO::FETCH_ASSOC);

            if (!$rowSolicitud) {
                throw new Exception("La solicitud no existe.");
            }

            if (in_array($rowSolicitud['estado'], ['1', '2'])) {
                throw new Exception("El traslado ya fue procesado.");
            }

            $idSolicitante = $rowSolicitud['idorigen'];
            $idProveedor = $rowSolicitud['iddestino'];
            $productosAceptados = [];
            // Validar stock en origen
            foreach ($productos as $p) {
                $idproducto = intval($p["idproducto"]);
                $cantidad = floatval($p["cantidad"]);
                $sql = "SELECT i.*,  p.precio FROM inventario_producto i INNER JOIN producto p ON p.idproducto=i.idproducto 
                WHERE i.idproducto=:idproducto AND i.idsucursal=:idsucursal FOR UPDATE";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'idproducto' => $idproducto,
                    'idsucursal' => $idProveedor
                ]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$row) {
                    throw new Exception("Producto {$idproducto} no existe en almacén origen.");
                }
                if ($row['stock'] < $cantidad) {
                    throw new Exception("Stock insuficiente de {$idproducto} en almacén origen.");
                }

                if ($p["estado_detalle"] === 'aceptado') {
                    $productosAceptados[] = $p;
                }
            }

            // Insertar cabecera traslado como REALIZADO
            if (!empty($productosAceptados)) {
                $traslado = (new FluentSaver($this->pdo))
                    ->table('traslado')
                    ->data([
                        'idorigen' => $idProveedor,
                        'iddestino' => $idSolicitante,
                        'fecha' => $fecha,
                        'estado' => '1',
                        'idusuario' => $idusuario,
                        'tipo' => 'traslado',
                        'fecha_aceptacion' => $fecha,
                        'idusuario_acepta' => $idusuario
                    ])
                    ->save();
                if (!$traslado) {
                    throw new Exception("Error al registrar cabecera de traslado.");
                }

                // Insertar detalle y registrar salida en origen
                foreach ($productosAceptados as $p) {
                    $idproducto = intval($p["idproducto"]);
                    $idserie = intval($p["idserie"]);
                    $cantidad = floatval($p["cantidad"]);
                    // Insertar detalle
                    $traslado_detalle = (new FluentSaver($this->pdo))
                        ->table('traslado_detalle')
                        ->nullable([
                            'idserie'
                        ])
                        ->data([
                            'idtraslado' => $traslado,
                            'idproducto' => $idproducto,
                            'idserie' => $idserie,
                            'cantidad' => $cantidad,
                            'estado_detalle' => 'aceptado',
                            'observacion' => ''
                        ])
                        ->save();

                    if (!$traslado_detalle) {
                        throw new Exception("Error al registrar detalle del producto $idproducto.");
                    }

                    $sqlProduct = "SELECT * FROM producto WHERE idproducto=:idproducto AND idsucursal=:idsucursal";
                    $stmtProduct = $this->pdo->prepare($sqlProduct);
                    $stmtProduct->execute([
                        'idproducto' => $idproducto,
                        'idsucursal' => $idProveedor
                    ]);
                    $rowProduct = $stmtProduct->fetch(PDO::FETCH_ASSOC);
                    if (!$rowProduct) {
                        throw new Exception("No se encontró información del producto $idproducto en origen.");
                    }

                    $precio = floatval($rowProduct['precio'] ?? 0);

                    // Salida de almacén origen
                    $salida = 1;
                    $motivo = "Traslado generado desde la solicitud #{$idtraslado}";
                    $resSalida = $this->movimientoSalida($rowProduct, $idProveedor, $idserie, $cantidad, $motivo);
                    if ($resSalida['success'] != true) {
                        throw new Exception("Error en kardex de salida: " . $resSalida['message']);
                    }

                    // ingreso de almace destino
                    $resSalida = $this->movimientoIngreso($rowProduct, $idSolicitante, $idserie, $cantidad, $motivo);
                    if ($resSalida['success'] != true) {
                        throw new Exception("Error en kardex de salida: " . $resSalida['message']);
                    }
                }
            }


            // actualizamos a procesada
            (new FluentSaver($this->pdo))
                ->table('traslado')
                ->primaryKey('idtraslado')
                ->data([
                    'idtraslado' => $rowSolicitud['idtraslado'],
                    'estado' => '2',
                    'fecha_aceptacion' => $fecha,
                    'idusuario_acepta' => $idusuario
                ])
                ->save();

            foreach ($productos as $p) {
                (new FluentSaver($this->pdo))
                    ->table('traslado_detalle')
                    ->primaryKey('iddetalle')
                    ->data([
                        'iddetalle' => $p["iddetalle"],
                        'estado_detalle' => $p["estado_detalle"],
                        'observacion' => $p["observacion"]
                    ])
                    ->save();
            }

            $this->pdo->commit();

            if (!empty($productosAceptados)) {
                $mensaje = "Solicitud aprobada y traslado generado correctamente.";
            } else {
                $mensaje = "Solicitud procesada. Todos los productos fueron rechazados.";
            }

            return json_encode([
                'success' => true,
                'message' => $mensaje
            ]);
        } catch (Throwable $e) {
            if (isset($this->pdo) && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return json_encode(["success" => false, "message" => "Error al guardar los datos: " . $e->getMessage()]);
        }

    }

    // public function aprobarSolicitud($idtraslado, $productos, $idusuario)
    // {
    //     ejecutarConsulta("START TRANSACTION");

    //     // Decodificar productos si vienen en JSON
    //     if (is_string($productos)) {
    //         $productos = json_decode($productos, true);
    //     }
    //     if (!is_array($productos)) {
    //         ejecutarConsulta("ROLLBACK");
    //         return "Error: formato de productos inválido.";
    //     }

    //     //  Obtener datos del traslado
    //     $info = ejecutarConsultaSimpleFila("SELECT * FROM traslado WHERE idtraslado='$idtraslado'");
    //     if (!$info) {
    //         ejecutarConsulta("ROLLBACK");
    //         return "Solicitud no encontrada.";
    //     }

    //     //  Definir sucursales
    //     $idsucursalPrincipal = $info['iddestino']; // almacén que envía stock
    //     $idsucursalSolicitante = $info['idorigen'];  // almacén que recibe

    //     $aprobados = 0;

    //     foreach ($productos as $p) {
    //         $idproductoSolicitante = intval($p["idproducto"]);
    //         $estado = strtolower(trim($p["estado"] ?? ''));
    //         $cantidad = floatval($p["cantidad"] ?? 0);
    //         $observacion = isset($p["observacion"]) ? limpiarCadena($p["observacion"]) : '';
    //         $nombreProducto = isset($p["nombre"]) ? limpiarCadena($p["nombre"]) : '';

    //         if (!in_array($estado, ['aceptado', 'rechazado']))
    //             continue;

    //         //  Actualizar detalle del traslado
    //         ejecutarConsulta("UPDATE traslado_detalle 
    //                       SET estado_detalle='$estado', 
    //                           observacion='$observacion', 
    //                           cantidad='$cantidad'
    //                       WHERE idtraslado='$idtraslado' AND idproducto='$idproductoSolicitante'");

    //         if ($estado === 'aceptado' && $cantidad > 0) {
    //             $aprobados++;

    //             //  Obtener producto en sucursal principal
    //             $productoPrincipal = ejecutarConsultaSimpleFila("
    //             SELECT * FROM producto
    //             WHERE nombre = '$nombreProducto' AND idsucursal = '$idsucursalPrincipal'
    //             LIMIT 1
    //         ");
    //             if (!$productoPrincipal) {
    //                 ejecutarConsulta("ROLLBACK");
    //                 return "No se encontró el producto '$nombreProducto' en la sucursal principal.";
    //             }
    //             $idproductoPrincipal = $productoPrincipal['idproducto'];

    //             // Verificar si el producto existe en la sucursal solicitante
    //             $productoSolicitante = ejecutarConsultaSimpleFila("
    //             SELECT idproducto FROM producto
    //             WHERE nombre = '$nombreProducto' AND idsucursal = '$idsucursalSolicitante'
    //             LIMIT 1
    //         ");

    //             if (!$productoSolicitante) {
    //                 // Crear producto en la sucursal solicitante copiando del principal
    //                 $sqlInsert = "INSERT INTO producto (
    //                 idsucursal, idcategoria, idunidad_medida, idrubro, idcondicionventa,
    //                 nombre, codigo, descripcion, precio, precioB, precioC, precioD, precioE,
    //                 preciocigv, precio_compra, proigv, stock_minimo, stock, imagen, condicion
    //             ) VALUES (
    //                 '{$idsucursalSolicitante}',
    //                 '{$productoPrincipal['idcategoria']}',
    //                 '{$productoPrincipal['idunidad_medida']}',
    //                 '{$productoPrincipal['idrubro']}',
    //                 '{$productoPrincipal['idcondicionventa']}',
    //                 '" . addslashes($productoPrincipal['nombre']) . "',
    //                 '" . addslashes($productoPrincipal['codigo']) . "',
    //                 '" . addslashes($productoPrincipal['descripcion']) . "',
    //                 '{$productoPrincipal['precio']}',
    //                 '{$productoPrincipal['precioB']}',
    //                 '{$productoPrincipal['precioC']}',
    //                 '{$productoPrincipal['precioD']}',
    //                 '{$productoPrincipal['precioE']}',
    //                 '{$productoPrincipal['preciocigv']}',
    //                 '{$productoPrincipal['precio_compra']}',
    //                 '" . addslashes($productoPrincipal['proigv']) . "',
    //                 '{$productoPrincipal['stock_minimo']}',
    //                 0,
    //                 '" . ($productoPrincipal['imagen'] ?? 'anonymous.png') . "',
    //                 1
    //             )";
    //                 $idNuevo = ejecutarConsulta_retornarID($sqlInsert);
    //                 if (!$idNuevo) {
    //                     ejecutarConsulta("ROLLBACK");
    //                     return "Error al crear producto '$nombreProducto' en la sucursal solicitante.";
    //                 }

    //                 // Copiar configuraciones del producto principal (si las tiene)
    //                 $configOrigen = ejecutarConsulta("SELECT * FROM producto_configuracion WHERE idproducto = '$idproductoPrincipal' AND deleted_at IS NULL");
    //                 while ($conf = $configOrigen->fetch_assoc()) {
    //                     $sql_conf = "INSERT INTO producto_configuracion
    //                     (codigo_extra, contenedor, cantidad_contenedor, precio_venta, precio_promocion, idproducto)
    //                     VALUES (
    //                         '" . addslashes($conf['codigo_extra']) . "',
    //                         '" . addslashes($conf['contenedor']) . "',
    //                         " . floatval($conf['cantidad_contenedor']) . ",
    //                         " . floatval($conf['precio_venta']) . ",
    //                         " . floatval($conf['precio_promocion']) . ",
    //                         '$idNuevo')";
    //                     $idConfNuevo = ejecutarConsulta_retornarID($sql_conf);

    //                     // Copiar precios asociados
    //                     $precios = ejecutarConsulta("SELECT * FROM producto_configuracion_precios WHERE producto_configuracion_id = {$conf['id']} AND estado = 1");
    //                     while ($pconf = $precios->fetch_assoc()) {
    //                         ejecutarConsulta("INSERT INTO producto_configuracion_precios
    //                         (producto_configuracion_id, idnombre_p, precio, estado)
    //                         VALUES (
    //                             '$idConfNuevo',
    //                             '" . addslashes($pconf['idnombre_p']) . "',
    //                             '" . floatval($pconf['precio']) . "',
    //                             1
    //                         )");
    //                     }
    //                 }

    //                 $idproductoSolicitante = $idNuevo;
    //             } else {
    //                 $idproductoSolicitante = $productoSolicitante['idproducto'];
    //             }

    //             // Registrar salida del principal
    //             $resSalida = $this->movimientoEntradaSalidaT_aprobacion(
    //                 $idproductoPrincipal,
    //                 $idsucursalPrincipal,
    //                 1, // salida
    //                 $cantidad,
    //                 "Aprobación de solicitud #$idtraslado (envío desde sucursal principal)",
    //                 $idusuario
    //             );
    //             if ($resSalida['status'] != 1) {
    //                 ejecutarConsulta("ROLLBACK");
    //                 return "Error en salida de stock (principal): " . $resSalida['message'];
    //             }

    //             // Registrar entrada en solicitante
    //             $resEntrada = $this->movimientoEntradaSalidaT_aprobacion(
    //                 $idproductoSolicitante,
    //                 $idsucursalSolicitante,
    //                 0, // entrada
    //                 $cantidad,
    //                 "Ingreso por solicitud aprobada #$idtraslado (recepción en sucursal solicitante)",
    //                 $idusuario
    //             );
    //             if ($resEntrada['status'] != 1) {
    //                 ejecutarConsulta("ROLLBACK");
    //                 return "Error en entrada de stock (solicitante): " . $resEntrada['message'];
    //             }
    //         }
    //     }

    //     //  Actualizar estado general del traslado
    //     $nuevoEstado = $aprobados > 0 ? '1' : '2';
    //     ejecutarConsulta("UPDATE traslado SET estado='$nuevoEstado' WHERE idtraslado='$idtraslado'");

    //     ejecutarConsulta("COMMIT");
    //     return "Solicitud procesada correctamente.";
    // }



    // public function movimientoEntradaSalidaT_aprobacion(
    //     $idproducto,
    //     $idsucursal,
    //     $tipo_movimiento,   // 0 = Entrada, 1 = Salida
    //     $cantidad,
    //     $motivo
    // ) {
    //     date_default_timezone_set('America/Lima');

    //     $idproducto = intval($idproducto);
    //     $idsucursal = intval($idsucursal);
    //     $cantidad = floatval(str_replace(',', '.', $cantidad));
    //     $cantidad_contenedor = 1;

    //     if ($cantidad <= 0) {
    //         return ['status' => 0, 'message' => 'La cantidad debe ser mayor que cero'];
    //     }

    //     $intentos = 0;
    //     $max_intentos = 3;

    //     while ($intentos < $max_intentos) {
    //         try {
    //             $intentos++;
    //             ejecutarConsulta("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
    //             ejecutarConsulta("BEGIN");

    //             //  Buscar producto exacto en esa sucursal
    //             $sql = "SELECT idproducto, stock, precio, nombre, codigo
    //                 FROM producto
    //                 WHERE idproducto = '$idproducto' AND idsucursal = '$idsucursal'
    //                 FOR UPDATE";
    //             $res = ejecutarConsulta($sql);

    //             $producto = $res ? $res->fetch_object() : null;

    //             //  Si no se encuentra, intentar buscar por nombre o código
    //             if (!$producto) {
    //                 $prodOrigen = ejecutarConsultaSimpleFila("SELECT nombre, codigo FROM producto WHERE idproducto='$idproducto' LIMIT 1");
    //                 if ($prodOrigen) {
    //                     $nombre = $prodOrigen['nombre'];
    //                     $codigo = $prodOrigen['codigo'];

    //                     $sqlAlt = "SELECT idproducto, stock, precio 
    //                            FROM producto 
    //                            WHERE (nombre = '" . addslashes($nombre) . "' OR codigo = '" . addslashes($codigo) . "') 
    //                              AND idsucursal = '$idsucursal'
    //                            LIMIT 1 FOR UPDATE";
    //                     $resAlt = ejecutarConsulta($sqlAlt);
    //                     $producto = $resAlt ? $resAlt->fetch_object() : null;
    //                 }
    //             }

    //             // Si aun así no se encuentra
    //             if (!$producto) {
    //                 ejecutarConsulta("ROLLBACK");
    //                 return [
    //                     'status' => 0,
    //                     'message' => "No se encontró producto equivalente (id:$idproducto) en la sucursal $idsucursal"
    //                 ];
    //             }

    //             $idproducto_real = intval($producto->idproducto);
    //             $stock_actual = floatval($producto->stock);
    //             $precio = floatval($producto->precio);
    //             $fecha_kardex = date('Y-m-d H:i:s');

    //             // 🔹 Procesar movimiento según tipo
    //             if ($tipo_movimiento == 0) {
    //                 //  ENTRADA - Registrar en stock_fifo
    //                 $nuevo_stock = round($stock_actual + $cantidad, 2);
    //                 $tipo_descripcion = "Entrada por aprobación de solicitud";

    //                 //  Insertar en stock_fifo
    //                 $sql_fifo = "INSERT INTO stock_fifo 
    //                 (idsucursal, idproducto, producto_configuracion_id, origen, referencia_id,
    //                  cantidad_ingreso, cantidad_restante, precio_compra, precio_venta, 
    //                  fecha_ingreso, estado, fvencimiento)
    //                 VALUES (
    //                     '$idsucursal',
    //                     '$idproducto_real',
    //                     NULL,
    //                     'ALMACEN',
    //                     NULL,
    //                     '$cantidad',
    //                     '$cantidad',
    //                     '$precio',
    //                     '$precio',
    //                     '$fecha_kardex',
    //                     1,
    //                     NULL
    //                 )";

    //                 if (!ejecutarConsulta($sql_fifo)) {
    //                     throw new Exception("Error al registrar entrada en stock_fifo");
    //                 }

    //             } else {
    //                 //  SALIDA - Consumir de stock_fifo usando FIFO
    //                 $tipo_descripcion = "Salida por aprobación de solicitud";

    //                 if ($stock_actual < $cantidad) {
    //                     ejecutarConsulta("ROLLBACK");
    //                     return [
    //                         'status' => 0,
    //                         'message' => "Stock insuficiente en sucursal (actual: $stock_actual, salida: $cantidad)"
    //                     ];
    //                 }

    //                 // Consumir stock usando FIFO
    //                 $cantidad_restante_salida = $cantidad;

    //                 $sql_lotes = "SELECT idfifo, cantidad_restante, precio_compra 
    //                           FROM stock_fifo 
    //                           WHERE idsucursal = '$idsucursal' 
    //                             AND idproducto = '$idproducto_real' 
    //                             AND cantidad_restante > 0 
    //                             AND estado = 1
    //                           ORDER BY fecha_ingreso ASC, idfifo ASC
    //                           FOR UPDATE";

    //                 $res_lotes = ejecutarConsulta($sql_lotes);

    //                 if (!$res_lotes || $res_lotes->num_rows == 0) {
    //                     throw new Exception("No hay lotes FIFO disponibles para la salida");
    //                 }

    //                 while ($lote = $res_lotes->fetch_object()) {
    //                     if ($cantidad_restante_salida <= 0)
    //                         break;

    //                     $idfifo = $lote->idfifo;
    //                     $cant_disp = floatval($lote->cantidad_restante);

    //                     if ($cant_disp >= $cantidad_restante_salida) {
    //                         // Este lote cubre todo
    //                         $nueva_cant = $cant_disp - $cantidad_restante_salida;
    //                         $sql_upd = "UPDATE stock_fifo 
    //                                 SET cantidad_restante = '$nueva_cant' 
    //                                 WHERE idfifo = '$idfifo'";
    //                         ejecutarConsulta($sql_upd);
    //                         $cantidad_restante_salida = 0;
    //                     } else {
    //                         // Agotar este lote
    //                         $sql_upd = "UPDATE stock_fifo 
    //                                 SET cantidad_restante = 0 
    //                                 WHERE idfifo = '$idfifo'";
    //                         ejecutarConsulta($sql_upd);
    //                         $cantidad_restante_salida -= $cant_disp;
    //                     }
    //                 }

    //                 if ($cantidad_restante_salida > 0) {
    //                     throw new Exception("No se pudo descontar toda la cantidad de stock_fifo");
    //                 }

    //                 $nuevo_stock = round($stock_actual - $cantidad, 2);
    //             }

    //             // Actualizar stock del producto
    //             $sql_update = "UPDATE producto 
    //                        SET stock = '$nuevo_stock' 
    //                        WHERE idproducto = '$idproducto_real' 
    //                          AND idsucursal = '$idsucursal'";
    //             if (!ejecutarConsulta($sql_update)) {
    //                 throw new Exception("Error al actualizar stock");
    //             }

    //             //  Registrar movimiento en kardex
    //             $sql_kardex = "INSERT INTO kardex 
    //             (idsucursal, idproducto, cantidad, cantidad_contenedor, precio_unitario, 
    //              stock_actual, tipo_movimiento, motivo, descripcion, fecha_kardex)
    //             VALUES (
    //                 '$idsucursal',
    //                 '$idproducto_real',
    //                 '$cantidad',
    //                 '$cantidad_contenedor',
    //                 '$precio',
    //                 '$nuevo_stock',
    //                 '$tipo_movimiento',
    //                 '$tipo_descripcion',
    //                 '$motivo',
    //                 '$fecha_kardex'
    //             )";
    //             if (!ejecutarConsulta($sql_kardex)) {
    //                 throw new Exception("Error al registrar movimiento en kardex");
    //             }

    //             ejecutarConsulta("COMMIT");

    //             return [
    //                 'status' => 1,
    //                 'message' => "Movimiento registrado correctamente ($tipo_descripcion)",
    //                 'stock_anterior' => $stock_actual,
    //                 'stock_nuevo' => $nuevo_stock,
    //                 'fecha' => $fecha_kardex
    //             ];

    //         } catch (Exception $e) {
    //             ejecutarConsulta("ROLLBACK");

    //             if (
    //                 stripos($e->getMessage(), 'deadlock') !== false ||
    //                 stripos($e->getMessage(), 'lock wait timeout') !== false
    //             ) {
    //                 if ($intentos < $max_intentos) {
    //                     usleep(200000);
    //                     continue;
    //                 }
    //             }

    //             return ['status' => 0, 'message' => 'Error: ' . $e->getMessage()];
    //         }
    //     }

    //     return [
    //         'status' => 0,
    //         'message' => 'No se pudo completar el movimiento tras varios intentos.'
    //     ];
    // }

    public function verProductosSolicitud($idtraslado, $idsucursal)
    {
        try{
            if (!$idtraslado) {
                throw new Exception("ID de traslado inválido.");
            }

            $sql = "SELECT t.idtraslado, t.idorigen, t.iddestino, t.estado, t.tipo
                    FROM traslado t
                    WHERE t.idtraslado = :idtraslado";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'idtraslado' => $idtraslado
            ]);
            $traslado = $stmt->fetch(PDO::FETCH_ASSOC);

            $sql = "SELECT 
                    td.*, 
                    p.nombre
                FROM traslado_detalle td
                INNER JOIN producto p ON td.idproducto = p.idproducto
                WHERE td.idtraslado = :idtraslado";
            $stmtSolicitud = $this->pdo->prepare($sql);
            $stmtSolicitud->execute([
                'idtraslado' => $idtraslado
            ]);
            $productos = $stmtSolicitud->fetchAll(PDO::FETCH_ASSOC);
            
            if ((int)$idsucursal === (int)$traslado['idorigen']) {
                // La sucursal que envía siempre consulta en modo lectura.
                $soloLectura = true;
            } else {
                // La sucursal destino solo puede editar mientras esté pendiente o en tránsito.
                $soloLectura = !in_array($traslado['estado'], ['pendiente', 'en_transito'], true);
            }
            return json_encode(['success' => true, 'productos' => $productos, 'soloLectura' => $soloLectura]);
		} catch (Throwable $e) {
            return json_encode(["success" => false, "message" => "Error al guardar los datos: " . $e->getMessage()]);
        }
    }



    public function obtenerSucursalOrigen($idtraslado)
    {
        $sql = "SELECT t.idorigen, t.iddestino, s1.nombre AS origen, s2.nombre AS destino
            FROM traslado t
            INNER JOIN sucursal s1 ON t.idorigen = s1.idsucursal
            INNER JOIN sucursal s2 ON t.iddestino = s2.idsucursal
            WHERE t.idtraslado = '$idtraslado'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function mostrarCabecera($idtraslado)
    {
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


    public function listarDetalleTicket($idtraslado)
    {
        $sql = "SELECT dt.cantidad, p.nombre AS producto, um.nombre AS unidad,dt.estado_detalle
            FROM traslado_detalle dt
            INNER JOIN producto p ON dt.idproducto = p.idproducto
            INNER JOIN unidad_medida um ON p.idunidad_medida=um.idunidad_medida
            WHERE dt.idtraslado = '$idtraslado'";
        return ejecutarConsulta($sql);
    }


}
?>