<?php
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";
class OrdenTrabajo extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }


    public function selectPersonal($idsucursal)
    {
        $page = (int) ($_GET['page'] ?? 1);
        $limit = (int) ($_GET['limit'] ?? 10);
        $search = trim($_GET['search'] ?? '');

        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('personal')
            ->search($search, ['nombre', 'num_documento'])
            ->paginate(
                $page,
                $limit
            );

        return Response::json($data);
    }


    public function listarOrdenesTrabajo($idsucursal, $fecha_inicio, $fecha_fin)
    {
        $page = (int) ($_GET['page'] ?? 1);
        $limit = (int) ($_GET['limit'] ?? 10);
        $search = trim($_GET['search'] ?? '');

        $query = (new DBQuery($this->pdo))
            ->select('ot.*, p.nombre as producto_nombre')
            ->from('orden_trabajo ot')
            ->leftJoin('producto p', 'ot.idproducto = p.idproducto')
            ->where('ot.idsucursal', '=', $idsucursal)
            ->search($search, ['ot.numero', 'ot.estado']);

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $query->whereBetween('DATE(ot.fecha_inicio)', $fecha_inicio, $fecha_fin);
        }

        $data = $query->paginate(
            $page,
            $limit
        );

        return Response::json($data);
    }


    public function guardarOrdenTrabajo(
        $idusuario,
        $idsucursal,
        $vehiculoId,
        $costosObservaciones,
        $documentoRelacionado,
        $estado,
        $fecha,
        $fechaCompromiso,
        $lavado,
        $otrosGastos,
        $pintura,
        $prioridad,
        $referencia,
        $serviciosExternos,
        $tipoOrden,
        $transporte,
        $mecanicos,
        $repuestos
    ) {
        try {
            $this->pdo->beginTransaction();
            $ultimoNumero = (new DBQuery($this->pdo))
                ->select(['numero'])
                ->from('orden_trabajo')
                ->where('idsucursal', '=', $idsucursal)
                ->orderBy('idorden', 'DESC')
                ->first();

            $siguienteNumero = 1;

            if ($ultimoNumero && !empty($ultimoNumero['numero'])) {
                $siguienteNumero = (int) $ultimoNumero['numero'] + 1;
            }

            $numero = str_pad($siguienteNumero, 8, '0', STR_PAD_LEFT);

            // Estado por defecto
            $estado = $estado ?: 'PENDIENTE';

            $idOrdenTrabajo = (new FluentSaver($this->pdo))
                ->table('orden_trabajo')
                ->cast([
                    'idsucursal' => 'int',
                    'idproducto' => 'int',
                    'lavado' => 'float',
                    'otros_gastos' => 'float',
                    'pintura' => 'float',
                    'servicios_externos' => 'float',
                    'transporte' => 'float'
                ])
                ->data([
                    'idsucursal' => $idsucursal,
                    'idproducto' => $vehiculoId ?: null,
                    'numero' => $numero,
                    'observaciones_costos' => $costosObservaciones,
                    'estado' => $estado,
                    'fecha_inicio' => $fecha ?: date('Y-m-d H:i:s'),
                    'fecha_fin' => $fechaCompromiso ?: null,
                    'lavado' => $lavado,
                    'otros_gastos' => $otrosGastos,
                    'pintura' => $pintura,
                    'servicios_externos' => $serviciosExternos,
                    'tipo' => $tipoOrden,
                    'transporte' => $transporte,
                    'idusuario' => $idusuario,
                ])
                ->save();

            // Guardar mecánicos
            foreach ($mecanicos as $mecanico) {
                (new FluentSaver($this->pdo))
                    ->table('orden_trabajo_mecanico')
                    ->cast([
                        'idorden' => 'int',
                        'idpersonal' => 'int'
                    ])
                    ->data([
                        'idorden' => $idOrdenTrabajo,
                        'idpersonal' => $mecanico['id'],
                        'rol' => 'PRINCIPAL', // Puedes ajustar esto según tu lógica
                        'fecha_inicio' => $fecha ?: date('Y-m-d H:i:s'),
                        'fecha_fin' => $fechaCompromiso ?: null,
                    ])
                    ->save();
            }

            // Guardar repuestos
            foreach ($repuestos as $repuesto) {
                (new FluentSaver($this->pdo))
                    ->table('orden_trabajo_detalle')
                    ->cast([
                        'idorden' => 'int',
                        'idproducto' => 'int'
                    ])
                    ->data([
                        'idorden' => $idOrdenTrabajo,
                        'idproducto' => $repuesto['idproducto'],
                        'cantidad' => $repuesto['cantidad'],
                        'precio_unitario' => $repuesto['precio'],
                        'subtotal' => $repuesto['subtotal']
                    ])
                    ->save();
            }

            $this->pdo->commit();
            return Response::json([
                'success' => true,
                'message' => 'Orden de trabajo guardada correctamente',
                'numero' => $numero
            ]);
        } catch (\Throwable $th) {
            $this->pdo->rollback();
            Response::error($th->getMessage());
        }
    }


    public function actualizarOrdenTrabajo($idOrdenTrabajo, $idusuario, $idsucursal, $vehiculoId, $costosObservaciones, $documentoRelacionado, $estado, $fecha, $fechaCompromiso, $lavado, $otrosGastos, $pintura, $prioridad, $referencia, $serviciosExternos, $tipoOrden, $transporte, $mecanicos, $repuestos)
    {
        try {
            $this->pdo->beginTransaction();

            (new FluentSaver($this->pdo))
                ->table('orden_trabajo')
                ->cast([
                    'idsucursal' => 'int',
                    'idproducto' => 'int',
                    'lavado' => 'float',
                    'otros_gastos' => 'float',
                    'pintura' => 'float',
                    'servicios_externos' =>
                        'float',
                    'transporte' => 'float'
                ])
                ->data([
                    'idsucursal' => $idsucursal,
                    'idproducto' => $vehiculoId ?: null,
                    'observaciones_costos' => $costosObservaciones,
                    'estado' => $estado ?: 'PENDIENTE',
                    'fecha_fin' => $fechaCompromiso ?: null,
                    'lavado' => $lavado,
                    'otros_gastos' => $otrosGastos,
                    'pintura' => $pintura,
                    'servicios_externos' => $serviciosExternos,
                    'tipo' => $tipoOrden,
                    'transporte' => $transporte,
                    'idusuario' => $idusuario
                ])
                ->where('idorden', '=', $idOrdenTrabajo)
                ->update();


            (new FluentSaver($this->pdo))
                ->table('orden_trabajo_mecanico')
                ->where('idorden', '=', $idOrdenTrabajo)
                ->data([
                    'deleted_at' => date('Y-m-d H:i:s')
                ])
                ->update();

            foreach ($mecanicos as $mecanico) {
                $idMecanico = empty($mecanico['id']) ? null : (int) $mecanico['id'];
                $idpersonal = (int) $mecanico['idpersonal'];
                if ($idMecanico) {
                    (new FluentSaver($this->pdo))
                        ->table('orden_trabajo_mecanico')
                        ->primaryKey('id')
                        ->data([
                            'id' => $idMecanico,
                            'idpersonal' => $idpersonal,
                            'rol' => 'PRINCIPAL',
                            'fecha_inicio' => $fecha ?: date('Y-m-d H:i:s'),
                            'fecha_fin' => $fechaCompromiso ?: null,
                            'deleted_at' => null
                        ])
                        ->update();
                } else {
                    (new FluentSaver($this->pdo))
                        ->table('orden_trabajo_mecanico')
                        ->cast([
                            'idorden' => 'int',
                            'idpersonal' => 'int'
                        ])
                        ->data([
                            'idorden' => $idOrdenTrabajo,
                            'idpersonal' => $idpersonal,
                            'rol' => 'PRINCIPAL',
                            'fecha_inicio' => $fecha ?: date('Y-m-d H:i:s'),
                            'fecha_fin' => $fechaCompromiso ?: null
                        ])
                        ->save();
                }
            }

            (new FluentSaver($this->pdo))
                ->table('orden_trabajo_detalle')
                ->where('idorden', '=', $idOrdenTrabajo)
                ->data([
                    'deleted_at' => date('Y-m-d H:i:s')
                ])
                ->update();


            foreach ($repuestos as $repuesto) {
                $iddetalle = empty($repuesto['iddetalle']) ? null : (int) $repuesto['iddetalle'];
                $idproducto = (int) $repuesto['idproducto'];
                $cantidad = $repuesto['cantidad'] ?? 1;
                $precioUnitario = $repuesto['precio'] ?? $repuesto['precio_unitario'] ?? 0;
                $subtotal = $repuesto['subtotal'] ?? 0;
                if ($iddetalle) {
                    (new FluentSaver($this->pdo))
                        ->table('orden_trabajo_detalle')
                        ->primaryKey('iddetalle')
                        ->cast([
                            'idproducto' => 'int',
                            'cantidad' => 'float',
                            'precio_unitario' => 'float',
                            'subtotal' => 'float'
                        ])
                        ->data([
                            'iddetalle' => $iddetalle,
                            'idproducto' => $idproducto,
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precioUnitario,
                            'subtotal' => $subtotal,
                            'deleted_at' => null
                        ])
                        ->update();
                } else {
                    (new FluentSaver($this->pdo))
                        ->table('orden_trabajo_detalle')
                        ->cast([
                            'idorden' => 'int',
                            'idproducto' => 'int',
                            'cantidad' => 'float',
                            'precio_unitario' => 'float',
                            'subtotal' => 'float'
                        ])
                        ->data([
                            'idorden' => $idOrdenTrabajo,
                            'idproducto' => $idproducto,
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precioUnitario,
                            'subtotal' => $subtotal
                        ])
                        ->save();
                }
            }
            $this->pdo->commit();
            return Response::json([
                'success' => true,
                'message' => 'Orden de trabajo actualizada correctamente',
                'idOrdenTrabajo' => $idOrdenTrabajo
            ]);
        } catch (\Throwable $th) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollback();
            }
            return Response::error($th->getMessage());
        }
    }


    public function mostrarOrdenTrabajo($idOrdenTrabajo)
    {
        $ordenTrabajo = (new DBQuery($this->pdo))
            ->select('*')
            ->from('orden_trabajo')
            ->where('idorden', '=', $idOrdenTrabajo)
            ->first();

        if (!$ordenTrabajo) {
            return Response::error('Orden de trabajo no encontrada');
        }

        // obtener producto relacionado si existe
        if ($ordenTrabajo['idproducto']) {
            $productoRelacionado = (new DBQuery($this->pdo))
                ->select('p.*, numero_serie, ps.numero_motor, ps.placa, ps.color, ps.anio_fabricacion, ps.estado, um.nombre as unidadmedida')
                ->from('producto p')
                ->join('unidad_medida um', 'p.idunidad_medida = um.idunidad_medida')
                ->join('producto_serie ps', 'ps.idproducto=p.idproducto')
                ->where('p.idproducto', '=', $ordenTrabajo['idproducto'])
                ->first();
            $ordenTrabajo['productoRelacionado'] = $productoRelacionado;
        } else {
            $ordenTrabajo['productoRelacionado'] = null;
        }

        // Obtener mecánicos asociados
        $mecanicos = (new DBQuery($this->pdo))
            ->select('m.*, per.nombre as nombre_personal')
            ->from('orden_trabajo_mecanico m')
            ->leftJoin('personal per', 'm.idpersonal = per.idpersonal')
            ->where('m.idorden', '=', $idOrdenTrabajo)
            ->get();

        // Obtener repuestos asociados
        $repuestos = (new DBQuery($this->pdo))
            ->select('d.*, p.nombre as nombre_producto, p.codigo')
            ->from('orden_trabajo_detalle d')
            ->leftJoin('producto p', 'd.idproducto = p.idproducto')
            ->where('d.idorden', '=', $idOrdenTrabajo)
            ->get();

        return Response::json([
            'ordenTrabajo' => $ordenTrabajo,
            'mecanicos' => $mecanicos,
            'repuestos' => $repuestos
        ]);
    }


    public function dataOrdenTrabajo($idOrdenTrabajo)
    {
        $ordenTrabajo = (new DBQuery($this->pdo))
            ->select('*')
            ->from('orden_trabajo')
            ->where('idorden', '=', $idOrdenTrabajo)
            ->first();

        if (!$ordenTrabajo) {
            return Response::error('Orden de trabajo no encontrada');
        }

        // obtener producto relacionado si existe
        if ($ordenTrabajo['idproducto']) {
            $productoRelacionado = (new DBQuery($this->pdo))
                ->select('p.*, numero_serie, ps.numero_motor, ps.placa, ps.color, ps.anio_fabricacion, ps.estado, um.nombre as unidadmedida')
                ->from('producto p')
                ->join('unidad_medida um', 'p.idunidad_medida = um.idunidad_medida')
                ->join('producto_serie ps', 'ps.idproducto=p.idproducto')
                ->where('p.idproducto', '=', $ordenTrabajo['idproducto'])
                ->first();
            $ordenTrabajo['productoRelacionado'] = $productoRelacionado;
        } else {
            $ordenTrabajo['productoRelacionado'] = null;
        }

        // Obtener mecánicos asociados
        $mecanicos = (new DBQuery($this->pdo))
            ->select('m.*, per.nombre as nombre_personal')
            ->from('orden_trabajo_mecanico m')
            ->leftJoin('personal per', 'm.idpersonal = per.idpersonal')
            ->where('m.idorden', '=', $idOrdenTrabajo)
            ->get();

        // Obtener repuestos asociados
        $repuestos = (new DBQuery($this->pdo))
            ->select('d.*, prod.nombre as nombre_producto')
            ->from('orden_trabajo_detalle d')
            ->leftJoin('producto prod', 'd.idproducto = prod.idproducto')
            ->where('d.idorden', '=', $idOrdenTrabajo)
            ->get();

        return [
            'ordenTrabajo' => $ordenTrabajo,
            'mecanicos' => $mecanicos,
            'repuestos' => $repuestos
        ];
    }
}