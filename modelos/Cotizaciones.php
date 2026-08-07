<?php
//incluir la conexion de base de datos
require "../configuraciones/Conexion.php";
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/config/Constants.php";
require_once __DIR__ . "/../core/Response.php";

date_default_timezone_set('America/Lima');

class Cotizacion extends Helpers
{


    //implementamos nuestro constructor
    public function __construct()
    {
        parent::__construct();
    }

    //metodo insertar registro
    public function insertar(
        $idsucursal,
        $idcliente,
        $idpersonal,
        $idtipo_comprobante,
        $fecha_hora,
        $total_venta,
        $titulo,
        $saludo,
        $nota,
        $igv,
        $formapago,
        $observaciones,
        $tiempoproduccion,
        $idproducto,
        $cantidad,
        $precio_venta,
        $descuento,
        $contenedor,
        $cantidad_contenedor,
        $idp,
        $inicial,
        $frecuencia,
        $meses,
        $interes,
        $idserie
    ) {
        try {

            $this->pdo->beginTransaction();

            $idcliente = Helpers::clienteDefault($idcliente);
            if ($idcliente == Constants::CLIENTE_DEFAULT) {
                throw new Exception("Debe seleccionar un cliente válido.");
            }

            $comprobante = Helpers::actualizarCorrelativo($idtipo_comprobante, $idsucursal);

            $idcotizacion = (new FluentSaver($this->pdo))
                ->table('cotizacion')
                ->nullable([
                    'inicial',
                    'frecuencia',
                    'meses',
                    'interes'
                ])
                ->data([
                    'idsucursal' => $idsucursal,
                    'idcliente' => $idcliente,
                    'idpersonal' => $idpersonal,
                    'idcomprobante_pago' => $idtipo_comprobante,
                    'serie_comprobante' => $comprobante['serie_comprobante'],
                    'num_comprobante' => $comprobante['num_comprobante'],
                    'fecha_hora' => $fecha_hora,
                    'total_venta' => $total_venta,
                    'titulo' => $titulo,
                    'saludo' => $saludo,
                    'nota' => $nota,
                    'igv' => $igv,
                    'formapago' => $formapago,
                    'observacion' => $observaciones,
                    'tiempo_pro' => $tiempoproduccion,
                    'estado' => 'EN ESPERA',
                    'inicial' => $inicial,
                    'frecuencia' => $frecuencia,
                    'meses' => $meses,
                    'interes' => $interes
                ])
                ->save();

            if (!$idcotizacion) {
                throw new Exception("No se pudo registrar la cotización.");
            }

            foreach ($idp as $i => $producto) {

                (new FluentSaver($this->pdo))
                    ->table('detalle_cotizacion')
                    ->data([
                        'idcotizacion' => $idcotizacion,
                        'idproducto' => $idproducto[$i],
                        'idserie' => $idserie[$i],
                        'cantidad' => $cantidad[$i],
                        'contenedor' => $contenedor[$i],
                        'cantidad_contenedor' => $cantidad_contenedor[$i],
                        'precio_venta' => $precio_venta[$i],
                        'descuento' => $descuento[$i]
                    ])
                    ->save();
            }

            $this->pdo->commit();

            return Response::json([
                'success' => true,
                'message' => 'Cotizacion registrado correctamente.'
            ]);

        } catch (Throwable $e) {
            $this->pdo->rollBack();
            return Response::error($e->getMessage());
        }
    }


    public function editar(
        $idcotizacion,
        $idsucursal,
        $idcliente,
        $idpersonal,
        $idtipo_comprobante,
        $fecha_hora,
        $total_venta,
        $titulo,
        $saludo,
        $nota,
        $igv,
        $formapago,
        $observaciones,
        $tiempoproduccion,
        $idproducto,
        $cantidad,
        $precio_venta,
        $descuento,
        $contenedor,
        $cantidad_contenedor,
        $idp,
        $inicial,
        $frecuencia,
        $meses,
        $interes,
        $isderie
    ) {
        try {

            $this->pdo->beginTransaction();

            $idcliente = Helpers::clienteDefault($idcliente);

            (new FluentSaver($this->pdo))
                ->table("cotizacion")
                ->primaryKey("idcotizacion")
                ->nullable([
                    "inicial",
                    "frecuencia",
                    "meses",
                    "interes"
                ])
                ->data([
                    "idcotizacion" => $idcotizacion,
                    "idsucursal" => $idsucursal,
                    "idcliente" => $idcliente,
                    "idpersonal" => $idpersonal,
                    "fecha_hora" => $fecha_hora,
                    "total_venta" => $total_venta,
                    "titulo" => $titulo,
                    "saludo" => $saludo,
                    "nota" => $nota,
                    "igv" => $igv,
                    "formapago" => $formapago,
                    "observacion" => $observaciones,
                    "tiempo_pro" => $tiempoproduccion,
                    "inicial" => $inicial,
                    "frecuencia" => $frecuencia,
                    "meses" => $meses,
                    "interes" => $interes
                ])
                ->update();

            // Eliminar detalle anterior
            (new FluentSaver($this->pdo))
                ->table("detalle_cotizacion")
                ->where("idcotizacion", "=", $idcotizacion)
                ->deleteWhere();

            // Registrar nuevo detalle
            foreach ($idp as $i => $producto) {

                (new FluentSaver($this->pdo))
                    ->table("detalle_cotizacion")
                    ->data([
                        "idcotizacion" => $idcotizacion,
                        "idproducto" => $idproducto[$i],
                        "idserie" => $idserie[$i],
                        "cantidad" => $cantidad[$i],
                        "contenedor" => $contenedor[$i],
                        "cantidad_contenedor" => $cantidad_contenedor[$i],
                        "precio_venta" => $precio_venta[$i],
                        "descuento" => $descuento[$i]
                    ])
                    ->save();
            }

            $this->pdo->commit();

            return Response::json([
                "success" => true,
                "message" => "Cotización actualizada correctamente."
            ]);

        } catch (Throwable $e) {

            $this->pdo->rollBack();

            return Response::json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    //Implementamos un método para desactivar categorías
    public function eliminar($idcotizacion)
    {
        $sql = "UPDATE cotizacion SET condicion='0' WHERE idcotizacion='$idcotizacion'";
        return ejecutarConsulta($sql);
    }

    //implementar un metodopara mostrar los datos de unregistro a modificar
    public function mostrar($idcotizacion)
{
    $data = (new DBQuery($this->pdo))
        ->select([
            "c.idcotizacion",
            "DATE(c.fecha_hora) AS fecha",
            "c.idcliente",
            "p.nombre AS cliente",
            "p.num_documento",
            "c.titulo",
            "c.nota",
            "c.igv",
            "u.idpersonal",
            "u.nombre AS personal",
            "p.telefono",
            "c.idcomprobante_pago",
            "c.serie_comprobante",
            "c.num_comprobante",
            "c.formapago",
            "c.fecha_h",
            "IFNULL(SUM((dc.cantidad * dc.precio_venta) - dc.descuento), 0) AS total_venta",
            "c.tiempo_pro",
            "c.inicial",
            "c.frecuencia",
            "c.meses",
            "c.interes",
            "c.observacion"
        ])
        ->from("cotizacion c")
        ->join(
            "persona p",
            "c.idcliente = p.idpersona"
        )
        ->join(
            "personal u",
            "c.idPersonal = u.idpersonal"
        )
        ->leftJoin(
            "detalle_cotizacion dc",
            "c.idcotizacion = dc.idcotizacion"
        )
        ->where("c.idcotizacion", "=", $idcotizacion)
        ->groupBy("c.idcotizacion")
        ->first();

        return Response::json($data);
}

    public function mostrardetalle($idcotizacion)
    {
        $sql = "SELECT dv.idcotizacion,dv.idproducto,a.nombre,dv.cantidad,dv.precio_venta,dv.descuento,(dv.cantidad*dv.precio_venta-dv.descuento) as subtotal, v.total_venta, p.nombre as cliente, v.num_comprobante FROM detalle_cotizacion dv INNER JOIN producto a ON dv.idproducto=a.idproducto INNER JOIN cotizacion v ON v.idcotizacion=dv.idcotizacion INNER JOIN persona p ON v.idcliente=p.idpersona WHERE dv.idcotizacion='$idcotizacion'";
        return ejecutarConsulta($sql);
    }

    public function listarDetalle($idcotizacion)
    {
        $sql = "SELECT dv.idcotizacion,dv.idproducto,a.nombre,dv.cantidad_contenedor,dv.contenedor,dv.cantidad,dv.precio_venta,dv.descuento,(dv.cantidad*dv.precio_venta-dv.descuento) as subtotal, v.total_venta 
		FROM detalle_cotizacion dv
		INNER JOIN producto a ON dv.idproducto=a.idproducto 
		INNER JOIN producto_configuracion pg ON a.idproducto=pg.idproducto
		INNER JOIN producto_serie ps ON ps.idproducto=a.idproducto 
		INNER JOIN inventario_producto ip ON ip.idproducto=a.idproducto 
		INNER JOIN cotizacion v ON v.idcotizacion=dv.idcotizacion 
		WHERE dv.idcotizacion='$idcotizacion'";
        return ejecutarConsulta($sql);
    }

    public function desistir($idcotizacion)
    {
        $sql = "UPDATE COTIZACION SET estado = 'DESISTIO' where idcotizacion = '$idcotizacion'";
        return ejecutarConsulta($sql);
    }

    //listar registros
    public function listar($fecha_inicio, $fecha_fin, $idsucursal)
    {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 20;
        $search = trim($_GET['search'] ?? '');

        $paginator = (new DBQuery($this->pdo))
            ->select('c.idcotizacion, DATE(c.fecha_h) as fecha_hora, date_format(c.fecha_h,"%d/%m/%y | %H:%i:%s %p") as fecha, c.idcliente,p.nombre as cliente,u.idpersonal,u.nombre as personal, cp.nombre as tipo_comprobante,c.serie_comprobante,c.num_comprobante,c.total_venta,c.estado, c.nota')
            ->from('cotizacion c')
            ->join('persona p', 'c.idcliente=p.idpersona')
            ->join('personal u', 'c.idpersonal=u.idpersonal')
            ->join('comp_pago cp', 'c.idcomprobante_pago=cp.idcomprobante_pago')
            ->where('c.condicion', '=', '1')
            ->where('c.idsucursal', '=', $idsucursal);

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $paginator->whereBetween(
                'DATE(c.fecha_h)',
                $fecha_inicio,
                $fecha_fin
            );
        }

        if ($search !== '') {
            $paginator->search($search, [
                'p.nombre',
                'u.nombre',
                'c.serie_comprobante',
                'c.num_comprobante'
            ]);
        }

        $response = $paginator
            ->orderBy('c.idcotizacion', 'DESC')
            ->paginate($page, $limit);

        return Response::json($response);
    }

    public function listar2($idsucursal, $is_aprobated = false)
    {

        $sql = "SELECT
            c.idcotizacion,
            DATE(c.fecha_hora) AS fecha,
            c.idcliente,
            p.nombre AS cliente,
            u.idpersonal,
            u.nombre AS personal,
            c.idcomprobante_pago,
            cp.nombre AS tipo_comprobante,
            c.serie_comprobante,
            c.num_comprobante,
            c.total_venta,
            c.formapago,
            c.estado
        FROM cotizacion c
        INNER JOIN persona p ON c.idcliente = p.idpersona
        INNER JOIN personal u ON c.idPersonal = u.idpersonal
        INNER JOIN comp_pago cp ON c.idcomprobante_pago = cp.idcomprobante_pago
        WHERE c.idsucursal = '$idsucursal'
        AND c.condicion = 1
        AND DATE_ADD(c.fecha_hora, INTERVAL c.nota DAY) >= CURDATE()
        AND (
            (
                c.formapago = 'Si'
                AND c.estado = 'APROBADO'
                AND c.fecha_aprobacion IS NOT NULL
            )
            OR
            (
                c.formapago = 'No'
                AND c.estado = 'EN ESPERA'
            )
        )
        ORDER BY c.idcotizacion DESC";

        return ejecutarConsulta($sql);
    }


    public function ventacabecera($idcotizacion)
    {
        $sql = "SELECT v.idcotizacion, v.idcliente, p.nombre AS cliente, v.titulo, v.nota, v.saludo, v.fecha_h, p.direccion, p.tipo_documento, p.num_documento, p.email, p.telefono, v.idpersonal, u.nombre AS personal, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, DATE(v.fecha_hora) AS fecha, v.total_venta FROM cotizacion v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE v.idcotizacion='$idcotizacion'";
        return ejecutarConsulta($sql);
    }

    public function listarDetalleCotizacion($idcotizacion)
    {
        $data = (new DBQuery($this->pdo))
            ->select([
                "d.iddetalle_cotizacion",
                "p.*",
                "pg.idproducto_configuracion",
                "pg.precio_venta",
                "pg.precio_credito",
                "pg.contenedor",
                "pg.cantidad_contenedor",
                "i.stock",
                "ps.idserie",
                "ps.numero_serie",
                "ps.numero_motor",
                "ps.placa",
                "ps.color",
                "ps.anio_fabricacion",
                "ps.estado AS estado_serie",
                "um.nombre AS unidadmedida",
                "d.cantidad",
                "d.precio_venta",
                "d.descuento",
                "(d.cantidad * d.precio_venta - d.descuento) AS subtotal"
            ])
            ->from("detalle_cotizacion d")
            ->join(
                "producto p",
                "p.idproducto = d.idproducto"
            )
            ->join(
                "producto_configuracion pg",
                "pg.idproducto = p.idproducto"
            )
            ->join(
                "unidad_medida um",
                "um.idunidad_medida = p.idunidad_medida"
            )
            ->leftJoin(
                "inventario_producto i",
                "i.idproducto = p.idproducto"
            )
            ->leftJoin(
                "producto_serie ps",
                "ps.idproducto = p.idproducto
             AND ps.estado = 'DISPONIBLE'"
            )
            ->where("d.idcotizacion", "=", $idcotizacion)
            ->get();
            return Response::json($data);
    }

    /*public function ventadetalle($idcotizacion)
        {
            // $sql="SELECT a.idproducto,a.nombre AS producto, CASE WHEN a.codigo = 'SIN CODIGO' THEN '-' ELSE a.codigo END as codigo, d.cantidad, d.precio_venta, d.descuento, (d.cantidad*d.precio_venta-d.descuento) AS subtotal, a.stock FROM detalle_venta d INNER JOIN producto a ON d.idproducto=a.idproducto WHERE d.idventa='$idventa'";
            //         return ejecutarConsulta($sql);

            $sql = "SELECT pg.id, a.idproducto, pg.contenedor,pg.cantidad_contenedor, a.nombre AS producto, um.nombre as unidadmedida, a.codigo, d.nombre_producto, d.cantidad, d.precio_venta, a.precioB, a.precioC, a.precioD, a.preciocigv, (d.descuento + v.descuento) AS descuento, (d.cantidad*d.precio_venta-d.descuento) AS subtotal, a.stock, a.proigv
            FROM detalle_venta d 
            LEFT JOIN producto_configuracion pg 
            ON pg.id=d.idproducto
            INNER JOIN producto a 
            ON pg.idproducto=a.idproducto 
            INNER JOIN unidad_medida um 
            ON a.idunidad_medida = um.idunidad_medida
            INNER JOIN venta v
            ON v.idventa = d.idventa
            WHERE d.idventa='$idventa'";
            return ejecutarConsulta($sql);
        }*/


    //funcion para selecciolnar el numero de factura
    // public function numero_venta()
    // {

    //     $sql = "SELECT num_comprobante FROM venta WHERE tipo_comprobante='Factura' ORDER BY idventa DESC limit 1 ";
    //     return ejecutarConsulta($sql);

    // }

    //funcion para seleccionar la serie de la factura
    // public function numero_serie()
    // {

    //     $sql = "SELECT serie_comprobante ,num_comprobante FROM venta WHERE tipo_comprobante='Factura' ORDER BY idventa DESC limit 1";

    //     return ejecutarConsulta($sql);
    // }

    //funcion para selecciolnar el numero de boleta
    // public function numero_venta_boleta()
    // {

    //     $sql = "SELECT num_comprobante FROM venta WHERE tipo_comprobante='Boleta' ORDER BY idventa DESC limit 1 ";
    //     return ejecutarConsulta($sql);

    // }
    //funcion para seleccionar la serie de la boleta
    // public function numero_serie_boleta()
    // {

    //     $sql = "SELECT serie_comprobante ,num_comprobante FROM venta WHERE tipo_comprobante='Boleta' ORDER BY idventa DESC limit 1";

    //     return ejecutarConsulta($sql);
    // }

    //funcion para selecciolnar el numero de ticket
    // public function numero_venta_ticket()
    // {

    //     $sql = "SELECT num_comprobante FROM venta WHERE tipo_comprobante='Ticket' ORDER BY idventa DESC limit 1 ";
    //     return ejecutarConsulta($sql);

    // }
    //funcion para seleccionar la serie de la ticket
    // public function numero_serie_ticket()
    // {

    //     $sql = "SELECT serie_comprobante ,num_comprobante FROM venta WHERE tipo_comprobante='Ticket' ORDER BY idventa DESC limit 1";

    //     return ejecutarConsulta($sql);
    // }

    //funcion para selecciolnar el numero de ticket
    // public function numero_venta_cotizacion($idsucursal)
    // {

    //     $sql = "SELECT num_comprobante FROM cotizacion WHERE tipo_comprobante='Cotización' AND idsucursal = '$idsucursal' ORDER BY idcotizacion DESC limit 1";
    //     return ejecutarConsulta($sql);

    // }

    //funcion para seleccionar la serie de la ticket
    // public function numero_serie_cotizacion($idsucursal)
    // {

    //     $sql = "SELECT serie_comprobante ,num_comprobante FROM cotizacion WHERE tipo_comprobante='Cotización' AND idsucursal = '$idsucursal' ORDER BY idcotizacion DESC limit 1";

    //     return ejecutarConsulta($sql);
    // }

    public function buscarProducto($codigo)
    {
        $sql = "SELECT * FROM producto WHERE codigo='$codigo'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // ==================== TABLA TEMPORAL ====================

    // Agregar producto temporal
    public function agregarTemporal($idusuario, $idproducto, $cantidad, $precio_venta, $descuento, $contenedor, $cantidad_contenedor, $idp)
    {
        $sql = "INSERT INTO cotizacion_tmp (idusuario, idproducto, cantidad, precio_venta, descuento, contenedor, cantidad_contenedor, idp)
            VALUES ('$idusuario', '$idproducto', '$cantidad', '$precio_venta', '$descuento', '$contenedor', '$cantidad_contenedor', '$idp')";
        return ejecutarConsulta_retornarID($sql);
    }

    // Actualizar un producto temporal
    public function actualizarTemporal($idtmp, $cantidad, $precio_venta)
    {
        $sql = "UPDATE cotizacion_tmp SET cantidad='$cantidad', precio_venta='$precio_venta' WHERE idtmp='$idtmp'";
        return ejecutarConsulta($sql);
    }

    // Listar productos temporales del usuario
    public function listarTmp($idusuario)
    {
        $sql = "SELECT 
                ct.idtmp,
                ct.idproducto,
                ct.idp,
                pr.nombre,
                conf.contenedor,
                conf.cantidad_contenedor,
                ct.cantidad,
                ct.precio_venta
            FROM cotizacion_tmp ct
            INNER JOIN producto_configuracion conf ON ct.idproducto = conf.id
            INNER JOIN producto pr ON conf.idproducto = pr.idproducto
            WHERE ct.idusuario = '$idusuario'
            ORDER BY ct.idtmp ASC";
        return ejecutarConsulta($sql);
    }



    public function eliminarTemporal($idtmp, $idusuario)
    {
        $idtmp = intval($idtmp);
        $idusuario = intval($idusuario);

        // Eliminamos el registro
        $sql = "DELETE FROM cotizacion_tmp WHERE idtmp = '$idtmp' AND idusuario = '$idusuario'";
        $result = ejecutarConsulta($sql);

        // Verificamos si se eliminó realmente
        $verificar = "SELECT COUNT(*) AS total FROM cotizacion_tmp WHERE idtmp = '$idtmp' AND idusuario = '$idusuario'";
        $row = ejecutarConsultaSimpleFila($verificar);

        if (isset($row['total']) && $row['total'] == 0) {
            return true; // eliminado correctamente
        } else {
            return false; // no se eliminó o no existía
        }
    }



    // Limpiar carrito temporal del usuario (al guardar la cotización)
    public function limpiarTemporales($idusuario)
    {
        $sql = "DELETE FROM cotizacion_tmp WHERE idusuario = '$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function cotizacionesCliente($idsucursal, $idcliente, $is_aprobated = false)
    {
        $sql = "SELECT
                idcotizacion,
                serie_comprobante,
                num_comprobante
            FROM cotizacion
            WHERE idsucursal = '$idsucursal' AND idcliente = '$idcliente'
            AND DATE_ADD(fecha_hora, INTERVAL nota DAY) >= CURDATE()";

        if ($is_aprobated) {
            $sql .= " AND fecha_aprobacion IS NOT NULL";
        } else {
            $sql .= " AND fecha_aprobacion IS NULL";
        }

        return ejecutarConsulta($sql);
    }

}
