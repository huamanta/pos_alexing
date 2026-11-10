<?php
require_once __DIR__ . "/../core/Response.php";
require_once __DIR__ . "/Helpers.php";

class Cajas extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }

    //Implementamos un mÃ©todo para insertar registros
    public function insertar($nombre, $numero, $idsucursal)
    {
        try {
            $insertar = (new FluentSaver($this->pdo))
                ->table('cajas')
                ->data([
                    'nombre' => $nombre,
                    'numero' => $numero,
                    'idsucursal' => $idsucursal,
                ])
                ->save();

            if (!$insertar) {
                throw new Exception("Error al guardar la caja");
            }

            return Response::json([
                'success' => true,
                'message' => 'La caja se ha creado correctamente'
            ]);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }

    //Implementamos un método para editar registros
    public function editar($idcaja, $nombre, $numero)
    {
        try {
            $editar = (new FluentSaver($this->pdo))
                ->table('cajas')
                ->primaryKey('idcaja')
                ->data([
                    'idcaja' => $idcaja,
                    'nombre' => $nombre,
                    'numero' => $numero,
                ])
                ->update();

            if (!$editar) {
                throw new Exception("Error al editar la caja");
            }

            return Response::json([
                'success' => true,
                'message' => 'La caja se ha editado correctamente'
            ]);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }

    public function listar(int $idsucursal)
    {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 20;
        $search = trim($_GET['search'] ?? '');
        $abierto = 2;

        $response = (new DBQuery($this->pdo))
            ->select([
                'c.*',
                "CASE WHEN c.estado = {$abierto} THEN pe.nombre ELSE '' END AS personal",
                's.nombre AS almacen'
            ])
            ->from('cajas c')
            ->leftJoin(
                '(
                SELECT ca1.*
                FROM caja_apertura ca1
                INNER JOIN (
                    SELECT idcaja, MAX(aperturacajaid) AS ultima_apertura
                    FROM caja_apertura
                    GROUP BY idcaja
                ) ca2 ON ca1.aperturacajaid = ca2.ultima_apertura
            ) ca',
                'ca.idcaja = c.idcaja'
            )
            ->leftJoin('usuario u', 'u.idusuario = ca.idusuario')
            ->leftJoin('personal pe', 'pe.idpersonal = u.idpersonal')
            ->leftJoin('sucursal s', 's.idsucursal = c.idsucursal')
            ->where('c.idsucursal', '=', $idsucursal)
            ->whereNull('c.deleted_at')
            ->paginate($page, $limit);

        return Response::json($response);
    }

    //Implementamos un método para desactivar 
    public function desactivar(int $idcaja)
    {
        try {

            $verificarAperturaCaja = Helpers::verificarAperturaCaja($idcaja);
            
            if ($verificarAperturaCaja) {
                throw new Exception("La caja esta abierta no se puede descativar.");
            }

            $update = (new FluentSaver($this->pdo))
                ->table('cajas')
                ->primaryKey('idcaja')
                ->data([
                    'idcaja' => $idcaja,
                    'estado' => 0
                ])
                ->update();

            if (!$update) {
                throw new Exception("No se pudo desactivar la caja.");
            }

            return Response::json([
                'success' => true,
                'message' => 'La caja se desactivo con exito'
            ]);
        } catch (Exception $e) {
            return Response::error($e->getMessage());
        }
    }

    //Implementamos un método para activar 
    public function activar(int $idcaja)
    {
        try {
            $update = (new FluentSaver($this->pdo))
                ->table('cajas')
                ->primaryKey('idcaja')
                ->data([
                    'idcaja' => $idcaja,
                    'estado' => 1
                ])
                ->update();

            if (!$update) {
                throw new Exception("No se pudo activar la caja.");
            }
            return Response::json([
                'success' => true,
                'message' => 'La caja se activo con exito'
            ]);
        } catch (Exception $e) {
            return Response::error($e->getMessage());
        }
    }

    //Implementar un método para mostrar los datos de un registro a modificar
    public function mostrar(int $idcaja)
    {
        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('cajas')
            ->where('idcaja', '=', $idcaja)
            ->first();

        return Response::json($data);
    }

    public function historialCajas(int $idcaja, int $idusuario)
    {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 20;
        $search = trim($_GET['search'] ?? '');

        $response = (new DBQuery($this->pdo))
            ->select([
                'ca.aperturacajaid',
                'ca.idcaja',
                'ca.idusuario',
                'ca.fecha_apertura',
                'ca.fecha_cierre',
                'ca.efectivo_apertura',
                'ca.efectivo_cierre',
                'c.numero',
                'c.nombre',
                'pe.nombre AS personal',
                '(
                SELECT COUNT(*)
                FROM venta v
                WHERE v.idcaja = ca.idcaja
                AND v.fecha_hora BETWEEN ca.fecha_apertura
                    AND IFNULL(ca.fecha_cierre, NOW())
            ) AS cantventas',
                '(
                SELECT IFNULL(SUM(v.total_venta), 0)
                FROM venta v
                WHERE v.idcaja = ca.idcaja
                AND v.fecha_hora BETWEEN ca.fecha_apertura
                    AND IFNULL(ca.fecha_cierre, NOW())
            ) AS totalventas'
            ])
            ->from('caja_apertura ca')
            ->join('cajas c', 'c.idcaja=ca.idcaja')
            ->join('usuario u', 'u.idusuario=ca.idusuario')
            ->join('personal pe', 'pe.idpersonal=u.idpersonal')
            ->where('ca.idcaja', '=', $idcaja)
            ->search($search, ['pe.nombre', 'c.numero', 'c.nombre'])
            ->orderBy('ca.aperturacajaid', 'DESC')
            ->paginate($page, $limit);

        foreach ($response['data'] as &$row) {
            $row['efectivo_apertura_str'] = Helpers::get_currency_symbol($row['efectivo_apertura']);
            $row['efectivo_cierre_str'] = Helpers::get_currency_symbol($row['efectivo_cierre']);
            $row['totalventas_str'] = Helpers::get_currency_symbol($row['totalventas']);
        }
        unset($row);

        $response['permissions'] = [
            'superusuario' => Helpers::esSuperusuario($idusuario),
            'cerrar_caja' => Helpers::verificarPermisoCodigo('cajas.cerrarcaja')
        ];

        return Response::json($response);
    }


    public function listarPorApertura(int $aperturacajaid)
    {
        $caja = Helpers::verDataAperturaCaja($aperturacajaid);
        if (!$caja)
            return Response::json([
                "sEcho" => 1,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => []
            ]);

        $inicio = $caja['fecha_apertura'];
        $fin = !empty($caja['fecha_cierre']) ? $caja['fecha_cierre'] : date('Y-m-d H:i:s');
        $idcaja = $caja['idcaja'];

        $movimientos = (new DBQuery($this->pdo))
            ->select([
                'm.*',
                'm.fecha AS fecha',
                'm.tipo AS tipo',
                'm.totaldeposito AS monto',
                'p.nombre AS responsable',
                "'Movimientos' AS origen",
                "CONCAT(pe.nombre, ' <span class=\"badge badge-primary\">Personal</span>') AS cliente",
                "CONCAT('Movimiento por ', m.descripcion) AS descripcion",
            ])
            ->from('movimiento m')
            ->join('usuario u', 'u.idusuario = m.idusuario')
            ->join('personal p', 'p.idpersonal = u.idpersonal')
            ->join('personal pe', 'pe.idpersonal = m.idpersonal')
            ->where('m.idcaja', '=', $idcaja)
            ->where('m.totalefectivo', '>', 0)
            ->orderBy('m.idmovimiento', 'DESC')
            ->get();

        $data = array();

        foreach ($movimientos as $reg) {
            $data[] = array(
                "0" => $reg['fecha'],
                "1" => $reg['responsable'],
                "2" => $reg['descripcion'],
                "3" => ($reg['tipo'] == 'Egresos') ? '<span class="badge bg-red">EGRESO</span>' : '<span class="badge bg-green">INGRESO</span>',
                "4" => $reg['cliente'],
                "5" => $reg['tipo'] === Constants::INGRESOS ? Helpers::get_currency_symbol($reg['totalefectivo']) : '-',
                "6" => $reg['tipo'] === Constants::EGRESOS ? Helpers::get_currency_symbol($reg['totalefectivo']) : '-',
            );
        }

        return Response::json([
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ]);
    }

    public function listarCobrosPorApertura(int $aperturacajaid)
    {
        $caja = Helpers::verDataAperturaCaja($aperturacajaid);
        if (!$caja)
            return Response::json([
                "sEcho" => 1,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => []
            ]);

        $inicio = $caja['fecha_apertura'];
        $fin = !empty($caja['fecha_cierre']) ? $caja['fecha_cierre'] : date('Y-m-d H:i:s');
        $idcaja = $caja['idcaja'];

        $cuentas = (new DBQuery($this->pdo))
            ->select("dcc.*,
                CONCAT(
                    'Pago aplicado a cuota N° ',
                    (
                        SELECT COUNT(*)
                        FROM cuentas_por_cobrar cc2
                        WHERE cc2.idventa = cc.idventa
                        AND cc2.idcpc <= cc.idcpc
                    ),
                    ' de la venta N° ',
                    v.serie_comprobante, '-', v.num_comprobante
                ) AS descripcion")
            ->from('detalle_cuentas_por_cobrar dcc')
            ->join('cuentas_por_cobrar cc', 'cc.idcpc = dcc.idcpc')
            ->join('venta v', 'v.idventa = cc.idventa')
            ->where('dcc.montopagado', '>', 0)
            ->whereBetween('dcc.created_at', $inicio, $fin)
            ->where('dcc.idcaja ', '=', $idcaja)
            ->orderBy('dcc.iddcpc', 'DESC')
            ->get();
        $data = array();
        foreach ($cuentas as $reg) {
            $data[] = array(
                "0" => $reg['fechapago'],
                "1" => $this->getClienteVenta($reg['idcpc']),
                "2" => $reg['formapago'],
                "3" => $reg['descripcion'],
                "4" => Helpers::get_currency_symbol($reg['montopagado']),
            );
        }

        return Response::json(
            [
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            ]
        );
    }


    public function getProveedorCompra(int $idcpp)
    {
        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('cuentas_por_pagar cp')
            ->join('compra c', 'c.idventa = cc.idventa')
            ->join('persona p', 'c.idproveedor = p.idpersona')
            ->where('cp.idcpp', '=', $idcpp)
            ->first();

        return $data['nombre'];
    }

    public function getClienteVenta($idcpc)
    {
        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('cuentas_por_cobrar cc')
            ->join('venta v', 'v.idventa = cc.idventa')
            ->join('persona p', 'v.idcliente = p.idpersona')
            ->where('cc.idcpc', '=', $idcpc)
            ->first();

        return $data['nombre'];
    }

    public function listarPagosPorApertura($aperturacajaid)
    {
        $aperturacaja = Helpers::verDataAperturaCaja($aperturacajaid);

        if (!$aperturacaja)
            return [];

        $inicio = $aperturacaja['fecha_apertura'];
        $fin = !empty($aperturacaja['fecha_cierre']) ? $aperturacaja['fecha_cierre'] : date('Y-m-d H:i:s');
        $idcaja = $aperturacaja['idcaja'];

        $cuentas = (new DBQuery($this->pdo))
            ->select("dcc.*,
                CONCAT(
                    'Pago aplicado a cuota N° ',
                    (
                        SELECT COUNT(*)
                        FROM cuentas_por_pagar cc2
                        WHERE cc2.idcompra = cc.idcompra
                        AND cc2.idcpp <= cc.idcpp
                    ),
                    ' de la compra N° ',
                    v.serie_comprobante, '-', v.num_comprobante
                ) AS descripcion")
            ->from('detalle_cuentas_por_pagar dcc')
            ->join('cuentas_por_pagar cc', 'cc.idcpp = dcc.idcpp')
            ->join('compra v', 'v.idcompra = cc.idcompra')
            ->where('dcc.montopagado', '>', 0)
            ->whereBetween('dcc.created_at', $inicio, $fin)
            ->where('dcc.idcaja ', '=', $idcaja)
            ->orderBy('dcc.iddcpp', 'DESC')
            ->get();
        $data = array();
        foreach ($cuentas as $reg) {
            $data[] = array(
                "0" => $reg['fechapago'],
                "1" => self::getProveedorCompra($reg['idcpc']),
                "2" => $reg['formapago'],
                "3" => $reg['descripcion'],
                "4" => Helpers::get_currency_symbol($reg['montopagado']),
            );
        }

        return Response::json(
            [
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            ]
        );
    }
}
