<?php
require "../configuraciones/Conexion.php";
date_default_timezone_set('America/Lima');
require_once __DIR__ . "/../configuraciones/ConexionPdo.php";
require_once __DIR__ . "/../core/FluentQuery.php";
require_once __DIR__ . "/../core/FluentSave.php";
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";
use Carbon\Carbon;

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

    public function obtenerInventario(int $idproducto, int $idsucursal): array
    {
        return (new DBQuery($this->pdo))
            ->select('i.*,  p.precio, p.codigo, p.nombre, p.tipo_producto, p.controla_stock')
            ->from('inventario_producto i')
            ->join('producto p', 'p.idproducto=i.idproducto')
            ->where('i.idproducto', '=', $idproducto)
            ->where('i.idsucursal', '=', $idsucursal)
            ->forUpdate()
            ->first();
    }

    public function obtenerTraslado(int $idtraslado): array
    {
        return (new DBQuery($this->pdo))
            ->select('*')
            ->from('traslado')
            ->where('idtraslado', '=', $idtraslado)
            ->forUpdate()
            ->first();
    }

    public function obtenerConfiguracion($idproducto): array
    {
        return (new DBQuery($this->pdo))
            ->select('*')
            ->from('producto_configuracion')
            ->where('idproducto', '=', $idproducto)
            ->first();
    }

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
                $row = self::obtenerInventario($idproducto, $idorigen);
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
                    'correlativo' => Helpers::correlativoTraslado($idorigen, 'traslado'),
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

        // Buscar si el producto ya existe en el almacén destino
        $producto = (new DBQuery($this->pdo))
            ->select('idproducto')
            ->from('producto')
            ->where('codigo', '=', $rowProduct['codigo'])
            ->where('idsucursal', '=', $iddestino)
            ->first();

        // SI NO EXISTE EL PRODUCTO
        if (!$producto) {

            // Obtener la serie original
            $serie = (new DBQuery($this->pdo))
                ->select('*')
                ->from('producto_serie')
                ->where('idserie', '=', $idserie)
                ->first();

            if (!$serie) {
                throw new Exception("No se encontró la serie.");
            }

            // Crear producto
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

            // Inventario origen
            $inventarioOrigen = self::obtenerInventario($rowProduct['idproducto'], $rowProduct['idsucursal']);

            if (!$inventarioOrigen) {
                throw new Exception("No existe inventario del producto origen.");
            }

            // Crear inventario
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

            // Configuración
            $rowConfiguracion = self::obtenerConfiguracion($rowProduct['idproducto']);

            if (!$rowConfiguracion) {
                throw new Exception("No existe configuración del producto.");
            }

            unset($rowConfiguracion['idproducto_configuracion']);

            $rowConfiguracion['idproducto'] = $idproductoDestino;

            $saveConfig = (new FluentSaver($this->pdo))
                ->table('producto_configuracion')
                ->data($rowConfiguracion)
                ->save();

            // Crear serie
            unset($serie['idserie']);
            $serie['idproducto'] = $idproductoDestino;
            $serie['idsucursal'] = $iddestino;
            $serie['estado'] = 'DISPONIBLE';

            (new FluentSaver($this->pdo))
                ->table('producto_serie')
                ->data($serie)
                ->save();

            $stockActual = $cantidad;

        } else {
            // EL PRODUCTO YA EXISTE
            $nuevoProducto = $rowProduct;
            $idproductoDestino = $producto['idproducto'];
            // Actualizar stock
            $inventarioP = self::obtenerInventario($idproductoDestino, $iddestino);
            $saveConfig = (new FluentSaver($this->pdo))
                ->table('inventario_producto')
                ->primaryKey('idinventario')
                ->data(['idinventario' => $inventarioP['idinventario']])
                ->increment('stock', $cantidad);

            // Obtener el stock actualizado
            $inventario = self::obtenerInventario((int) $idproductoDestino, $iddestino);
            $stockActual = $inventario['stock'];
            // Obtener configuración
            $rowConfiguracion = self::obtenerConfiguracion($idproductoDestino);

            if (!$rowConfiguracion) {
                throw new Exception("No existe configuración del producto destino.");
            }

            $saveConfig = $rowConfiguracion['idproducto_configuracion'];
        }

        // Kardex
        if ($nuevoProducto['controla_stock'] === 'Si') {

            Helpers::updateKardexSucursal(
                $iddestino,
                $idproductoDestino,
                $saveConfig,
                $cantidad,
                $cantidad * $rowConfiguracion['cantidad_contenedor'],
                $nuevoProducto['precio'],
                $stockActual,
                1,
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
        if ($rowProduct['tipo_producto'] == "Vehiculo") {
            (new FluentSaver($this->pdo))
                ->table('producto_serie')
                ->primaryKey('idserie')
                ->data([
                    'idserie' => $idserie,
                    'estado' => 'TRASLADO'
                ])
                ->update();

        }
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
            ->update();

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
            $nuevo_stock = $rowProduct['stock'] - $cantidad;
            Helpers::updateKardexSucursal(
                $idsucursal,
                $rowProduct['idproducto'],
                $rowConfiguracion['idproducto_configuracion'],
                $cantidad,
                $cantidad * $rowConfiguracion['cantidad_contenedor'],
                $rowProduct['precio'],
                $nuevo_stock,
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

    public function guardarSolicitud($idorigen, $iddestino, $productos, $idusuario)
    {
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
                    'correlativo' => Helpers::correlativoTraslado($idorigen, 'solicitud'),
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


    // Función opcional para crear notificación al crear traslado
    public function crearNotificacionTraslado($idtraslado, $idsucursal, $mensaje)
    {
        return (new FluentSaver($this->pdo))
            ->table('notificaciones')
            ->data([
                'idsucursal' => $idsucursal,
                'mensaje' => $mensaje,
                'leido' => 0,
                'fecha' => date('Y-m-d H:i:s'),
                'idtraslado' => $idtraslado
            ])
            ->save();
    }


    // listar (igual que antes)
    public function listar($fecha_inicio, $fecha_fin, $estado, $idsucursal, $tipo, $origen = false)
    {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $search = $_GET['search'] ?? '';

        $paginator = (new DBQuery($this->pdo))
            ->query("
                SELECT
                    t.*,
                    CONCAT('<span class=''badge badge-primary''>', s1.nombre, '</span>') AS origen,
                    CONCAT('<span class=''badge badge-success''>', s2.nombre, '</span>') AS destino,
                    CASE
                        WHEN t.estado = 'aceptado' THEN '<span class=''badge badge-success''>Aceptado</span>'
                        WHEN t.estado = 'pendiente' THEN '<span class=''badge badge-warning''>Pendiente</span>'
                        WHEN t.estado = 'en_transito' THEN '<span class=''badge badge-info''>En Tránsito</span>'
                        WHEN t.estado = 'cancelado' THEN '<span class=''badge badge-danger''>Cancelado</span>'
                        WHEN t.estado = 'rechazado' THEN '<span class=''badge badge-secondary''>Rechazado</span>'
                        WHEN t.estado = 'recibido' THEN '<span class=''badge badge-primary''>Recibido</span>'
                        ELSE '<span class=''badge badge-light''>Desconocido</span>'
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

            $fecha = Carbon::now();
            // obtenemos la solicitud
            $rowSolicitud = self::obtenerTraslado($idtraslado);

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
                if ($p["estado"] === 'aceptado') {
                    $idproducto = intval($p["idproducto"]);
                    $cantidad = floatval($p["cantidad"]);
                    $row = self::obtenerInventario($idproducto, $idProveedor);

                    if (!$row) {
                        throw new Exception("Producto {$idproducto} no existe en almacén origen.");
                    }

                    if ($row['stock'] < $cantidad) {
                        throw new Exception("Stock insuficiente de {$idproducto} en almacén origen.");
                    }
                    $productosAceptados[] = $p;
                }
            }

            // Insertar cabecera traslado como EN TRANSITO
            if (!empty($productosAceptados)) {
                $traslado = (new FluentSaver($this->pdo))
                    ->table('traslado')
                    ->data([
                        'idorigen' => $idProveedor,
                        'correlativo' => Helpers::correlativoTraslado($idProveedor, 'traslado'),
                        'iddestino' => $idSolicitante,
                        'fecha' => $fecha,
                        'estado' => 'en_transito',
                        'idusuario' => $idusuario,
                        'tipo' => 'traslado',
                        'idsolicitud_origen' => $rowSolicitud['idtraslado']
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
                            'cantidad_enviada' => $cantidad,
                            'estado_detalle' => 'pendiente',
                            'observacion' => ''
                        ])
                        ->save();

                    if (!$traslado_detalle) {
                        throw new Exception("Error al registrar detalle del producto $idproducto.");
                    }

                }
            }


            // actualizamos a procesada
            (new FluentSaver($this->pdo))
                ->table('traslado')
                ->primaryKey('idtraslado')
                ->data([
                    'idtraslado' => $rowSolicitud['idtraslado'],
                    'estado' => 'aceptado',
                    'fecha_aceptacion' => $fecha,
                    'idusuario_acepta' => $idusuario
                ])
                ->update();

            foreach ($productos as $p) {
                (new FluentSaver($this->pdo))
                    ->table('traslado_detalle')
                    ->primaryKey('iddetalle')
                    ->data([
                        'iddetalle' => $p["iddetalle"],
                        'estado_detalle' => $p["estado"],
                        'observacion' => $p["observacion"]
                    ])
                    ->update();
            }

            $this->pdo->commit();

            if (!empty($productosAceptados)) {
                $mensaje = "Solicitud aprobada y traslado generado correctamente.";
            } else {
                $mensaje = "Solicitud procesada. Todos los productos fueron rechazados.";
            }

            return Response::json([
                'success' => true,
                'message' => $mensaje
            ]);
        } catch (Throwable $e) {
            if (isset($this->pdo) && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return Response::error($e->getMessage());
        }

    }



    public function procesarSolicitud($idtraslado, $productos, $idusuario)
    {
        try {
            if (!$idtraslado) {
                throw new Exception("No se ha seleccionado el traslado a procesar.");
            }

            $this->pdo->beginTransaction();

            $fecha = Carbon::now();
            $rowSolicitud = self::obtenerTraslado($idtraslado);

            if (!$rowSolicitud) {
                throw new Exception("La solicitud no existe.");
            }

            if (in_array($rowSolicitud['estado'], ['1', '2'])) {
                throw new Exception("El traslado ya fue procesado.");
            }
            $idSolicitante = $rowSolicitud['idorigen'];
            $idProveedor = $rowSolicitud['iddestino'];

            // actualizamos a procesada
            (new FluentSaver($this->pdo))
                ->table('traslado')
                ->primaryKey('idtraslado')
                ->data([
                    'idtraslado' => $rowSolicitud['idtraslado'],
                    'estado' => 'recibido',
                    'fecha_aceptacion' => $fecha,
                    'idusuario_acepta' => $idusuario
                ])
                ->update();

            foreach ($productos as $p) {
                (new FluentSaver($this->pdo))
                    ->table('traslado_detalle')
                    ->primaryKey('iddetalle')
                    ->data([
                        'iddetalle' => $p["iddetalle"],
                        'estado_detalle' => $p["estado"],
                        'cantidad_recibida' => $p["cantidad_recibida"],
                        'observacion' => $p["observacion"]
                    ])
                    ->update();

                $rowProduct = self::obtenerInventario($p['idproducto'], $idSolicitante);
                if (!$rowProduct) {
                    throw new Exception("No se encontró información del producto {$p['idproducto']} en origen.");
                }
                // Salida de almacén origen
                $motivo = "Traslado generado desde la solicitud #{$idtraslado}";
                $resSalida = $this->movimientoSalida($rowProduct, $idSolicitante, $p['idserie'], $p['cantidad'], $motivo);
                if ($resSalida['success'] != true) {
                    throw new Exception("Error en kardex de salida: " . $resSalida['message']);
                }
                // ingreso de almace destino
                $resSalida = $this->movimientoIngreso($rowProduct, $idProveedor, $p['idserie'], $p['cantidad'], $motivo);
                if ($resSalida['success'] != true) {
                    throw new Exception("Error en kardex de salida: " . $resSalida['message']);
                }
            }

            $this->pdo->commit();

            return Response::json([
                'success' => true,
                'message' => "Traslado recibido correctamente."
            ]);
        } catch (Throwable $e) {
            if (isset($this->pdo) && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return Response::error($e->getMessage());
        }

    }

    public function verProductosSolicitud($idtraslado, $idsucursal)
    {
        try {
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

            if ((int) $idsucursal === (int) $traslado['idorigen']) {
                // La sucursal que envía siempre consulta en modo lectura.
                $soloLectura = true;
            } else {
                // La sucursal destino solo puede editar mientras esté pendiente o en tránsito.
                if ($traslado['tipo'] === 'solicitud') {
                    $soloLectura = !in_array($traslado['estado'], ['pendiente'], true);
                } else {
                    $soloLectura = !in_array($traslado['estado'], ['pendiente', 'en_transito'], true);
                }
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
        $sql = "SELECT dt.cantidad_enviada, p.nombre AS producto, um.nombre AS unidad,dt.estado_detalle
            FROM traslado_detalle dt
            INNER JOIN producto p ON dt.idproducto = p.idproducto
            INNER JOIN unidad_medida um ON p.idunidad_medida=um.idunidad_medida
            WHERE dt.idtraslado = '$idtraslado'";
        return ejecutarConsulta($sql);
    }


    public function rechazarAnular($idtraslado, $idusuario, $estado)
    {
        try {
            if (!$idtraslado) {
                throw new Exception("ID de traslado inválido.");
            }
            $traslado = (new FluentSaver($this->pdo))
                ->table('traslado')
                ->primaryKey('idtraslado')
                ->data([
                    'idtraslado' => $idtraslado,
                    'idusuario_acepta' => $idusuario,
                    'estado' => $estado
                ])
                ->update();
            if (!$traslado) {
                throw new Exception("No se pudo actualizar el traslado.");
            }
            return json_encode(['success' => true, 'message' => 'Se actualizo corectamente']);
        } catch (Throwable $e) {
            return json_encode(["success" => false, "message" => "Error al guardar los datos: " . $e->getMessage()]);
        }
    }


}
?>