<?php
require_once __DIR__ . "/../Helpers.php";
require_once __DIR__ . "/../config/Constants.php";
header('Content-Type: application/json; charset=utf-8');
class Recuperacion extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listarCandidatosRecuperacion($idsucursal, $diasMora = Constants::DIAS_MORA)
    {
        $pdo = Conexion::conectar();

        $page = (int) ($_GET['page'] ?? 1);
        $limit = (int) ($_GET['limit'] ?? 10);
        $search = trim($_GET['search'] ?? '');

        $query = (new DBQuery($pdo))
            ->select([
                'v.idventa',
                'p.idpersona',
                'p.nombre AS cliente',
                'p.num_documento',
                'p.telefono',
                'pr.idproducto',
                'pr.nombre AS vehiculo',
                'ps.idserie',
                'ps.numero_serie',
                'ps.numero_motor',
                'ps.placa',
                'COUNT(cpc.idcpc) AS cuotas_vencidas',
                'MIN(cpc.fechavencimiento) AS fecha_cuota_mas_antigua',
                'SUM(cpc.deuda) AS deuda_vencida',
                'SUM(cpc.deudatotal) AS saldo_pendiente',
                'DATEDIFF(CURDATE(), MIN(cpc.fechavencimiento)) AS dias_mora',
                "CASE
                WHEN DATEDIFF(CURDATE(), MIN(cpc.fechavencimiento)) >= " . Constants::RECUPERACION_CRITICA . "
                    AND COUNT(cpc.idcpc) >= " . Constants::MESES_RECUPERACION . "
                    THEN 'CRITICO'
                WHEN DATEDIFF(CURDATE(), MIN(cpc.fechavencimiento)) >= " . Constants::RECUPERACION_ALTO . "
                    OR COUNT(cpc.idcpc) >= " . Constants::MESES_RECUPERACION . "
                    THEN 'ALTO'
                WHEN DATEDIFF(CURDATE(), MIN(cpc.fechavencimiento)) >= " . Constants::RECUPERACION_MEDIA . "
                    THEN 'MEDIO'
                ELSE 'BAJO'
            END AS nivel_riesgo"
            ])
            ->from('venta v')
            ->join('detalle_venta dv', 'dv.idventa = v.idventa')
            ->join('persona p', 'p.idpersona = v.idcliente')
            ->join('producto pr', 'pr.idproducto = dv.idproducto')
            ->join('producto_serie ps', 'ps.idproducto = pr.idproducto')
            ->join('cuentas_por_cobrar cpc', 'cpc.idventa = v.idventa')
            ->leftJoin(
                'recuperacion_vehiculo rv',
                'rv.idventa = v.idventa AND rv.deleted_at IS NULL'
            )
            ->where('v.idsucursal', '=', $idsucursal)
            ->where('cpc.estado_pago', '=', 1)
            ->whereRaw('cpc.fechavencimiento < CURDATE()')
            ->whereNull('rv.idventa')
            ->groupBy([
                'v.idventa',
                'p.idpersona',
                'p.nombre',
                'p.num_documento',
                'p.telefono',
                'pr.idproducto',
                'pr.nombre',
                'ps.idserie',
                'ps.numero_serie',
                'ps.numero_motor',
                'ps.placa'
            ])
            ->havingRaw('DATEDIFF(CURDATE(), MIN(cpc.fechavencimiento)) >= ?', [$diasMora])
            ->search(
                $search,
                [
                    'p.nombre',
                    'p.num_documento',
                    'ps.placa',
                    'ps.numero_serie',
                    'pr.nombre'
                ]
            )
            ->orderBy('dias_mora', 'DESC');
        return json_encode(
            $query->paginate(
                $page,
                $limit
            )
        );
    }

    public function verCandidato($idventa)
    {
        $venta = (new DBQuery($this->pdo))
            ->select([
                'v.idventa',
                'v.fecha_hora',
                'v.total_venta',
                'v.tipo_comprobante',
                'v.serie_comprobante',
                'v.num_comprobante',

                'p.idpersona',
                'p.nombre AS cliente',
                'p.num_documento',
                'p.telefono',

                'pr.idproducto',
                'pr.nombre AS vehiculo',

                'ps.idserie',
                'ps.numero_serie',
                'ps.numero_motor',
                'ps.placa'
            ])
            ->from('venta v')

            ->join(
                'detalle_venta dv',
                'dv.idventa = v.idventa'
            )

            ->join(
                'persona p',
                'p.idpersona = v.idcliente'
            )

            ->join(
                'producto pr',
                'pr.idproducto = dv.idproducto'
            )

            ->join(
                'producto_serie ps',
                'ps.idproducto = pr.idproducto'
            )

            ->where(
                'v.idventa',
                '=',
                $idventa
            )

            ->first();



        if (!$venta) {

            return json_encode([
                'success' => false,
                'message' => 'No existe crédito'
            ]);

        }

        // CUENTAS POR COBRAR
        $cuentas = (new DBQuery($this->pdo))
            ->select([
                'idcpc',
                'fechavencimiento',

                'deudatotal',
                'deuda_base',

                'mora',
                'mora_pagada',

                'deuda',

                'interes',
                'descuento',

                'abonototal',

                'estado_pago',

                'GREATEST(DATEDIFF(CURDATE(), fechavencimiento),0) AS dias_vencido'
            ])
            ->from('cuentas_por_cobrar')
            ->where(
                'idventa',
                '=',
                $idventa
            )
            ->whereNull('deleted_at')
            ->orderBy(
                'fechavencimiento',
                'ASC'
            )
            ->get();

        // RESUMEN DEL CRÉDITO
        $resumen = (new DBQuery($this->pdo))

            ->select([
                'COUNT(idcpc) AS total_cuotas',

                'SUM(deudatotal) AS total_credito',

                'SUM(deuda) AS saldo_pendiente',

                'SUM(mora) AS mora_total',

                'SUM(abonototal) AS total_abonado'
            ])

            ->from('cuentas_por_cobrar')

            ->where(
                'idventa',
                '=',
                $idventa
            )

            ->whereNull(
                'deleted_at'
            )

            ->first();



        return json_encode([
            'success' => true,

            'venta' => $venta,

            'cuentas' => $cuentas,

            'resumen' => $resumen
        ]);
    }

    public function registrarRecuperacion(
        $idusuario,
        $idventa,
        $idpersona,
        $idserie
    ) {

        // obtener datos actuales
        $credito = (new DBQuery($this->pdo))
            ->select([
                "DATEDIFF(
                CURDATE(),
                MIN(cpc.fechavencimiento)
            ) AS dias_mora",

                "SUM(cpc.deuda) AS deuda_vencida"
            ])

            ->from("cuentas_por_cobrar cpc")

            ->where(
                "cpc.idventa",
                "=",
                $idventa
            )

            ->where(
                "cpc.estado_pago",
                "=",
                1
            )

            ->first();



        // validar duplicado
        $existe = $this->existeRecuperacion($idventa);

        if ($existe) {
            return [
                "success" => false,
                "message" => "Este crédito ya está en recuperación"
            ];

        }

        $nivel = "BAJO";

        if ($credito["dias_mora"] >= Constants::RECUPERACION_CRITICA) {
            $nivel = "CRITICO";
        } elseif ($credito["dias_mora"] >= Constants::RECUPERACION_ALTO) {
            $nivel = "ALTO";
        } elseif ($credito["dias_mora"] >= Constants::RECUPERACION_MEDIA) {
            $nivel = "MEDIO";
        }


        (new FluentSaver($this->pdo))
            ->table("recuperacion_vehiculo")
            ->data([

                "idventa" => $idventa,

                "idpersona" => $idpersona,

                "idserie" => $idserie,

                "dias_mora" => $credito["dias_mora"],

                "deuda_vencida" => $credito["deuda_vencida"],

                "nivel_riesgo" => $nivel,

                "estado" => "PENDIENTE",

                "idusuario" => $idusuario

            ])
            ->save();



        return json_encode([
            "success" => true
        ]);
    }

    public function existeRecuperacion($idventa)
    {
        $sql = "
    SELECT COUNT(*) total
    FROM recuperacion_vehiculo
    WHERE idventa=:idventa
    AND estado NOT IN ('CERRADO')
    ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':idventa' => $idventa
        ]);

        return $stmt->fetchColumn() > 0;
    }

    public function listarRecuperaciones($idsucursal, $estado)
    {

        $page = (int) ($_GET['page'] ?? 1);
        $limit = (int) ($_GET['limit'] ?? 10);
        $search = trim($_GET['search'] ?? '');

        $query = (new DBQuery($this->pdo))
            ->select([
                'rv.idrecuperacion',
                'rv.idventa',
                'rv.fecha_registro',
                'p.idpersona',
                'p.nombre AS cliente',
                'p.num_documento',
                'p.telefono',
                'p.direccion',
                'pr.nombre AS vehiculo',
                'ps.placa',
                'ps.numero_serie',
                'rv.dias_mora',
                'SUM(cpc.deuda) AS deuda_vencida',
                'SUM(cpc.mora) AS mora',
                'rv.nivel_riesgo',
                'rv.estado',
                'up.nombre AS gestor'
            ])
            ->from('recuperacion_vehiculo rv')
            ->join('venta v', 'v.idventa = rv.idventa')
            ->join('persona p', 'p.idpersona = rv.idpersona')
            ->join('detalle_venta dv', 'dv.idventa = v.idventa')
            ->join('producto pr', 'pr.idproducto = dv.idproducto')
            ->join('producto_serie ps', 'ps.idserie = rv.idserie')
            ->join('cuentas_por_cobrar cpc', 'cpc.idventa = rv.idventa')
            ->leftJoin('usuario u', 'u.idusuario = rv.idusuario')
            ->leftJoin('personal up', 'up.idpersonal = u.idpersonal')
            ->softDeletes("rv.deleted_at")
            ->where('v.idsucursal', '=', $idsucursal);

        if ($estado) {
            $query->where('rv.estado', '=', $estado);
        }

        $query->where('cpc.estado_pago', '=', 1)
            ->groupBy([
                'rv.idrecuperacion',
                'rv.idventa',
                'rv.fecha_registro',
                'p.nombre',
                'p.num_documento',
                'p.telefono',
                'pr.nombre',
                'ps.placa',
                'ps.numero_serie',
                'rv.dias_mora',
                'rv.nivel_riesgo',
                'rv.estado',
                'up.nombre'
            ])
            ->search(
                $search,
                [
                    'p.nombre',
                    'p.num_documento',
                    'ps.placa',
                    'ps.numero_serie',
                    'pr.nombre'
                ]
            )
            ->orderBy(
                'rv.fecha_registro',
                'DESC'
            );

        return json_encode(
            $query->paginate(
                $page,
                $limit
            )
        );
    }

    public function listarCompromisos($idsucursal)
    {
        $pdo = Conexion::conectar();

        $page = (int) ($_GET['page'] ?? 1);
        $limit = (int) ($_GET['limit'] ?? 10);
        $search = trim($_GET['search'] ?? '');

        $query = (new DBQuery($pdo))
            ->select([
                'cp.idcompromiso_pago',
                'cp.fecha_compromiso',
                'cp.fecha_cumplimiento',
                'cp.monto',
                'cp.detalle',
                'cp.observacion',
                'p.nombre AS cliente',
                'p.num_documento',
                'pr.nombre AS vehiculo',
                'ps.placa',
                'cpc.fechavencimiento',
                'cpc.deuda',
                'pe.nombre AS usuario',
                "CASE
                WHEN cp.fecha_cumplimiento IS NOT NULL
                    THEN 'CUMPLIDO'
                WHEN cp.fecha_compromiso < NOW()
                    THEN 'VENCIDO'
                ELSE 'PENDIENTE'
            END AS estado"
            ])
            ->from('compromiso_pago cp')
            ->join('cuentas_por_cobrar cpc', 'cpc.idcpc = cp.idcpc')
            ->join('venta v', 'v.idventa = cpc.idventa')
            ->join('persona p', 'p.idpersona = v.idcliente')
            ->join('detalle_venta dv', 'dv.idventa = v.idventa')
            ->join('producto pr', 'pr.idproducto = dv.idproducto')
            ->join('producto_serie ps', 'ps.idproducto = pr.idproducto')
            ->join('usuario u', 'u.idusuario = cp.idusuario')
            ->join('personal pe', 'pe.idpersonal = u.idpersonal')
            ->where('v.idsucursal', '=', $idsucursal)
            ->whereNull('cp.deleted_at')
            ->search(
                $search,
                [
                    'p.nombre',
                    'p.num_documento',
                    'ps.placa',
                    'pr.nombre'
                ]
            )
            ->orderBy(
                'cp.created_at',
                'DESC'
            );

        return json_encode(
            $query->paginate(
                $page,
                $limit
            )
        );
    }

    public function actualizarEstado(
        $idrecuperacion,
        $estado,
        $observacion = null
    ) {

        $sql = "
    UPDATE recuperacion_vehiculo
    SET
        estado=:estado,
        observacion=:observacion
    WHERE idrecuperacion=:id
    ";


        $stmt = $this->pdo->prepare($sql);


        return $stmt->execute([
            ':estado' => $estado,
            ':observacion' => $observacion,
            ':id' => $idrecuperacion
        ]);

    }

    public function registrarHistorial(
        $idrecuperacion,
        $accion,
        $comentario,
        $usuario
    ) {

        $sql = "
    INSERT INTO recuperacion_historial
    (
        idrecuperacion,
        accion,
        comentario,
        usuario
    )
    VALUES
    (
        :id,
        :accion,
        :comentario,
        :usuario
    )
    ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $idrecuperacion,
            ':accion' => $accion,
            ':comentario' => $comentario,
            ':usuario' => $usuario
        ]);

    }
    public function cumplirCompromiso($id)
    {
        try {
            (new FluentSaver($this->pdo))
                ->table("compromiso_pago")
                ->primaryKey("idcompromiso_pago")
                ->data([
                    "idcompromiso_pago" => $id,
                    "fecha_cumplimiento" => date("Y-m-d H:i:s")
                ])
                ->update();
            return json_encode([
                'status' => true,
                'message' => 'El compromiso de pago se actualizó correctamente.'
            ]);

        } catch (Throwable $e) {

            return json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function eliminarCompromiso($id)
    {
        try {
            (new FluentSaver($this->pdo))
                ->table("compromiso_pago")
                ->primaryKey("idcompromiso_pago")
                ->softDelete($id);

            return json_encode([
                'success' => true,
                'message' => 'El compromiso de pago se elimino correctamente.'
            ]);

        } catch (Throwable $e) {

            return json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function verCompromiso($id)
    {
        $data = (new DBQuery($this->pdo))

            ->select([

                'cp.*',

                'cpc.deuda',

                'p.nombre AS cliente',

                'p.num_documento',

                'pr.nombre AS vehiculo',

                'ps.placa',

                'pe.nombre AS usuario',

                "CASE

                WHEN cp.fecha_cumplimiento IS NOT NULL
                    THEN 'CUMPLIDO'

                WHEN cp.fecha_compromiso < NOW()
                    THEN 'VENCIDO'

                ELSE 'PENDIENTE'

            END AS estado"
            ])
            ->from('compromiso_pago cp')
            ->join(
                'cuentas_por_cobrar cpc',
                'cpc.idcpc=cp.idcpc'
            )
            ->join(
                'venta v',
                'v.idventa=cpc.idventa'
            )
            ->join(
                'persona p',
                'p.idpersona=v.idcliente'
            )
            ->join(
                'detalle_venta dv',
                'dv.idventa=v.idventa'
            )
            ->join(
                'producto pr',
                'pr.idproducto=dv.idproducto'
            )
            ->join(
                'producto_serie ps',
                'ps.idproducto=pr.idproducto'
            )
            ->join(
                'usuario u',
                'u.idusuario=cp.idusuario'
            )
            ->join(
                'personal pe',
                'pe.idpersonal=u.idpersonal'
            )
            ->where(
                'cp.idcompromiso_pago',
                '=',
                $id
            )
            ->first();

        return json_encode($data);
    }

    public function verRecuperacion($idsucursal, $idrecuperacion)
    {
        try {
            $expediente = (new DBQuery($this->pdo))
                ->select([
                    'rv.*',
                    'p.nombre AS cliente',
                    'p.num_documento',
                    'p.telefono',
                    'pr.nombre AS vehiculo',
                    'ps.placa',
                    'ps.numero_serie',
                    'ps.numero_motor',
                    'pe.nombre AS gestor'
                ])
                ->from('recuperacion_vehiculo rv')
                ->join('venta v', 'v.idventa = rv.idventa')
                ->join('persona p', 'p.idpersona = v.idcliente')
                ->join('detalle_venta dv', 'dv.idventa = v.idventa')
                ->join('producto pr', 'pr.idproducto = dv.idproducto')
                ->join('producto_serie ps', 'ps.idserie = rv.idserie')
                ->leftJoin('usuario u', 'u.idusuario = rv.idusuario')
                ->leftJoin('personal pe', 'pe.idpersonal = u.idpersonal')
                ->where('rv.idrecuperacion', '=', $idrecuperacion)
                ->first();

            if (!$expediente) {
                throw new Exception("Expediente no encontrado.");
            }

            $expediente['deuda_vencida_str'] = Helpers::get_currency_symbol($expediente['deuda_vencida']);

            // Mora total
            $moraActiva = Helpers::verificarMoraCredito($idsucursal);
            $expediente['mora'] = 0;
            $expediente['mora_str'] = Helpers::get_currency_symbol(0);
            if ($moraActiva['activo']) {
                $mora = (new DBQuery($this->pdo))
                    ->select([
                        "COALESCE(
                            SUM(
                                CASE
                                    WHEN estado_pago = 1
                                    AND fechavencimiento < CURDATE()
                                    THEN DATEDIFF(CURDATE(), fechavencimiento) * " . $moraActiva['valor'] . "
                                    ELSE 0
                                END
                            ),
                        0) AS mora"
                    ])
                    ->from('cuentas_por_cobrar')
                    ->where('idventa', '=', $expediente['idventa'])
                    ->first();

                $expediente['mora'] = $mora['mora'] ?? 0;
                $expediente['mora_str'] = Helpers::get_currency_symbol($mora['mora']);
            }

            // Seguimientos
            $seguimientos = (new DBQuery($this->pdo))
                ->select([
                    's.*',
                    'pe.nombre AS usuario'
                ])
                ->from('seguimiento_clientes s')
                ->join('usuario u', 'u.idusuario = s.idusuario')
                ->join('personal pe', 'pe.idpersonal = u.idpersonal')
                ->where('s.idventa', '=', $expediente['idventa'])
                ->latest('s.fecha_registro')
                ->get();

            // Compromisos
            $compromisos = (new DBQuery($this->pdo))
                ->select(['cp.*', 'pe.nombre AS usuario'])
                ->from('compromiso_pago cp')
                ->join('usuario u', 'u.idusuario = cp.idusuario')
                ->join('personal pe', 'pe.idpersonal = u.idpersonal')
                ->join('cuentas_por_cobrar cpc', 'cpc.idcpc = cp.idcpc')
                ->where('cpc.idventa', '=', $expediente['idventa'])
                ->latest('cp.fecha_compromiso')
                ->get();

            // Adjuntos
            $adjuntos = (new DBQuery($this->pdo))
                ->select(['a.*'])
                ->from('seguimiento_adjuntos a')
                ->join('seguimiento_clientes s', 's.idseguimiento = a.idseguimiento')
                ->where('s.idventa', '=', $expediente['idventa'])
                ->latest('a.fecha_registro')
                ->get();

            // Documentso legales
            $documentos = (new DBQuery($this->pdo))
                ->select(['rd.*'])
                ->from('recuperacion_documento rd')
                ->where('rd.idrecuperacion', '=', $idrecuperacion)
                ->latest('rd.fecha_registro')
                ->get();

            return json_encode([
                "success" => true,
                "data" => $expediente,
                "seguimientos" => $seguimientos,
                "compromisos" => $compromisos,
                "adjuntos" => $adjuntos,
                "documentos" => $documentos
            ]);
        } catch (Throwable $e) {
            return json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }
    }

    public function actualizarEstadoRecuperacion(
        $idsucursal,
        $idrecuperacion,
        $estado,
        $observacion = null
    ) {
        try {
            $this->pdo->beginTransaction();
            $estadosPermitidos = [
                'PENDIENTE',
                'CONTACTADO',
                'NEGOCIACION',
                'VISITA_PROGRAMADA',
                'RECUPERADO',
                'CERRADO'
            ];

            if (!in_array($estado, $estadosPermitidos)) {
                throw new Exception("Estado no válido.");
            }

            $data = [
                'idrecuperacion' => $idrecuperacion,
                'estado' => $estado,
            ];

            if ($observacion !== null) {
                $data['observacion'] = $observacion;
            }

            $updateRecuperacion = (new FluentSaver($this->pdo))
                ->table('recuperacion_vehiculo')
                ->primaryKey('idrecuperacion')
                ->data($data)
                ->update();

            if (!$updateRecuperacion) {
                throw new Exception("No se pudo actualizar la recuperacion de vehiculo.");
            }

            #Si es recuperado poner en mantenimiento vehiculo
            if ($estado == 'RECUPERADO') {
                $recuperacion = (new DBQuery($this->pdo))
                    ->from('recuperacion_vehiculo')
                    ->where('idrecuperacion', '=', $idrecuperacion)
                    ->first();

                $productos = (new DBQuery($this->pdo))
                    ->from('detalle_venta')
                    ->where('idventa', '=', $recuperacion['idventa'])
                    ->get();

                $this->actualizarStock($idsucursal, $productos);
            }

            $this->pdo->commit();

            return json_encode([
                "success" => true,
                "message" => "La recuperacion se actualizo correctamente."
            ]);
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            return json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }
    }


    private function actualizarStock(
        $idsucursal,
        array $productos
    ) {
        foreach ($productos as $producto) {
            $inventario = (new DBQuery($this->pdo))
                ->from("inventario_producto")
                ->where(
                    "idproducto",
                    "=",
                    $producto["idproducto"]
                )
                ->where(
                    "idsucursal",
                    "=",
                    $idsucursal
                )
                ->first();


            if (!$inventario) {
                throw new Exception("No existe inventario para actualizar");
            }


            $nuevoStock = $inventario["stock"] + $producto["cantidad"];


            (new FluentSaver($this->pdo))
                ->table("inventario_producto")
                ->where(
                    "idinventario",
                    "=",
                    $inventario["idinventario"]
                )
                ->data([
                    "stock" => $nuevoStock
                ])
                ->update();

            $serie = (new DBQuery($this->pdo))
                ->from("producto_serie")
                ->where(
                    "idproducto",
                    "=",
                    $producto["idproducto"]
                )
                ->where(
                    "idsucursal",
                    "=",
                    $idsucursal
                )
                ->first();

            if (!$serie) {
                throw new Exception("No existe la serie para actualizar");
            }

            (new FluentSaver($this->pdo))
                ->table("producto_serie")
                ->where(
                    "idserie",
                    "=",
                    $serie["idserie"]
                )
                ->data([
                    "estado" => "MANTENIMIENTO"
                ])
                ->update();
        }

        return true;
    }

    public function guardarDocumento($post, $file)
    {
        try {
            $ruta = "../files/recuperacion/";

            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }

            $nombre = uniqid() . "_" . basename($file["name"]);

            move_uploaded_file(
                $file["tmp_name"],
                $ruta . $nombre
            );

            (new FluentSaver($this->pdo))
                ->table("recuperacion_documento")
                ->data([
                    "idrecuperacion" => $post["idrecuperacion"],
                    "tipo" => $post["tipo"],
                    "descripcion" => $post["descripcion"],
                    "archivo" => $nombre,
                    "nombre_original" => $file["name"],
                    "idusuario" => $_SESSION["idusuario"]
                ])
                ->save();

            return json_encode([
                "success" => true,
                "message" => "Documento registrado correctamente."
            ]);
        } catch (Throwable $e) {
            return json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }
    }
}

