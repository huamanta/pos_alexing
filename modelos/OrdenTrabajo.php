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
            ->join('producto p', 'ot.idproducto = p.idproducto')
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
                    'idsucursal'           => $idsucursal,
                    'idproducto'           => $vehiculoId ?: null,
                    'numero'               => $numero,
                    'observaciones_costos' => $costosObservaciones,
                    'estado'               => $estado,
                    'fecha_inicio'         => $fecha ?: date('Y-m-d H:i:s'),
                    'fecha_fin'            => $fechaCompromiso ?: null,
                    'lavado'               => $lavado,
                    'otros_gastos'         => $otrosGastos,
                    'pintura'              => $pintura,
                    'servicios_externos'  => $serviciosExternos,
                    'tipo'                 => $tipoOrden,
                    'transporte'           => $transporte,
                    'idusuario'            => $idusuario,
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
            return Response::json(['success' => true, 'message' => 'Orden de trabajo guardada correctamente', 'numero' => $numero]);
        } catch (\Throwable $th) {
            $this->pdo->rollback();
            Response::error($th->getMessage());
        }
    }
}