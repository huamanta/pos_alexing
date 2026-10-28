<?php
require_once __DIR__ . "/../Helpers.php";
require_once __DIR__ . '/../../core/Response.php';
use Carbon\Carbon;

class Bancos extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listar()
    {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 20;
        $search = trim($_GET['search'] ?? '');

        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('bancos')
            ->softDeletes()
            ->search($search, [
                'nombre',
                'descripcion'
            ])
            ->orderBy('nombre')
            ->paginate($page, $limit);

        $data['permissions'] = [
            'editar' => Helpers::verificarPermisoCodigo('bancos.editar'),
            'detalle' => Helpers::verificarPermisoCodigo('bancos.detalle'),
            'eliminar' => Helpers::verificarPermisoCodigo('bancos.eliminar')
        ];

        return Response::json($data);
    }

    public function listarMovimientos(int $idbanco)
    {
        $page = (int) ($_GET['page'] ?? 1);
        $limit = (int) ($_GET['limit'] ?? 20);

        $movimientos = (new DBQuery($this->pdo))
            ->select([
                'm.fecha AS fecha',
                'CONVERT(m.tipo USING utf8mb4) COLLATE utf8mb4_spanish_ci AS tipo',
                'm.totaldeposito AS monto',
                'CONVERT(p.nombre USING utf8mb4) COLLATE utf8mb4_spanish_ci AS responsable',
                "CONVERT('Movimientos' USING utf8mb4) COLLATE utf8mb4_spanish_ci AS origen",
                "CONVERT(CONCAT(pe.nombre, ' <span class=\"badge badge-primary\">Personal</span>') USING utf8mb4) COLLATE utf8mb4_spanish_ci AS cliente",
                "CONVERT(CONCAT('Movimiento por ', m.descripcion) USING utf8mb4) COLLATE utf8mb4_spanish_ci AS descripcion",
            ])
            ->from('movimiento m')
            ->join('usuario u', 'u.idusuario = m.idusuario')
            ->join('personal p', 'p.idpersonal = u.idpersonal')
            ->join('personal pe', 'pe.idpersonal = m.idpersonal')
            ->where('m.idbanco', '=', $idbanco)
            ->where('m.totaldeposito', '>', 0);

        $cuentas = (new DBQuery($this->pdo))
            ->select([
                'dcc.fechapago AS fecha',
                "CONVERT('Ingresos' USING utf8mb4) COLLATE utf8mb4_spanish_ci AS tipo",
                'dcc.montotarjeta AS monto',
                'CONVERT(p.nombre USING utf8mb4) COLLATE utf8mb4_spanish_ci AS responsable',
                "CONVERT('Cuentas por cobrar' USING utf8mb4) COLLATE utf8mb4_spanish_ci AS origen",
                'CONVERT(pe.nombre USING utf8mb4) COLLATE utf8mb4_spanish_ci AS cliente',
                "CONVERT(CONCAT(
                    'Deposito de pago aplicado a cuota N° ',
                    (
                        SELECT COUNT(*)
                        FROM cuentas_por_cobrar cc2
                        WHERE cc2.idventa = cc.idventa
                        AND cc2.idcpc <= cc.idcpc
                    ),
                    ' de la venta N° ',
                    v.serie_comprobante, '-', v.num_comprobante
                ) USING utf8mb4) COLLATE utf8mb4_spanish_ci AS descripcion",
            ])
            ->from('detalle_cuentas_por_cobrar dcc')
            ->join('personal p', 'p.idpersonal = dcc.idpersonal')
            ->join('cuentas_por_cobrar cc', 'cc.idcpc = dcc.idcpc')
            ->join('venta v', 'v.idventa = cc.idventa')
            ->leftJoin('persona pe', 'pe.idpersona = v.idcliente')
            ->where('dcc.idbanco', '=', $idbanco)
            ->where('dcc.montotarjeta', '>', 0);

        $venta_pago = (new DBQuery($this->pdo))
            ->select([
                'vp.created_at AS fecha',
                "CONVERT('Ingresos' USING utf8mb4) COLLATE utf8mb4_spanish_ci AS tipo",
                'vp.monto AS monto',
                'CONVERT(p.nombre USING utf8mb4) COLLATE utf8mb4_spanish_ci AS responsable',
                "CONVERT('Ventas' USING utf8mb4) COLLATE utf8mb4_spanish_ci AS origen",
                'CONVERT(pe.nombre USING utf8mb4) COLLATE utf8mb4_spanish_ci AS cliente',
                "CONVERT(CONCAT(
                    'Deposito de pago por el inicial de la venta N° ',
                    v.serie_comprobante, '-', v.num_comprobante
                ) USING utf8mb4) COLLATE utf8mb4_spanish_ci AS descripcion",
            ])
            ->from('venta_pago vp')
            ->join('venta v', 'v.idventa = vp.idventa')
            ->join('usuario u', 'u.idusuario = vp.idusuario')
            ->join('personal p', 'p.idpersonal = u.idpersonal')
            ->leftJoin('persona pe', 'pe.idpersona = v.idcliente')
            ->where('vp.idbanco', '=', $idbanco)
            ->where('vp.monto', '>', 0);

        $resultado = $movimientos
            ->unionAll($cuentas)
            ->unionAll($venta_pago)
            ->orderBy('fecha', 'DESC')
            ->paginate($page, $limit);

        return Response::json($resultado);
    }

    public function insertar($data)
    {
        try {
            $save = (new FluentSaver($this->pdo))
                ->table('bancos')
                ->nullable([
                    'cuenta',
                    'cci'
                ])
                ->data([
                    'nombre' => $data['nombre'],
                    'descripcion' => $data['descripcion'],
                    'cuenta' => $data['cuenta'],
                    'cci' => $data['cci'],
                    'saldo' => 0
                ])
                ->save();

            if (!$save) {
                throw new Exception("Banco no se pudo guardar");
            }

            return Response::json([
                'success' => true,
                'message' => 'Banco registrado correctamente'
            ]);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }


    public function editar($idbanco, $data)
    {
        try {
            $update = (new FluentSaver($this->pdo))
                ->table('bancos')
                ->nullable([
                    'cuenta',
                    'cci'
                ])
                ->primaryKey('idbanco')
                ->data([
                    'idbanco' => $idbanco,
                    'nombre' => $data['nombre'],
                    'descripcion' => $data['descripcion'],
                    'cuenta' => $data['cuenta'],
                    'cci' => $data['cci'],
                ])
                ->update();

            if (!$update) {
                throw new Exception("Banco no se pudo actualizar");
            }

            return Response::json([
                'success' => true,
                'message' => 'Banco actualizado correctamente'
            ]);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }


    public function mostrar($idbanco)
    {
        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('bancos')
            ->where('idbanco', '=', $idbanco)
            ->first();

        return Response::json($data);
    }


    public function eliminar($idbanco)
    {
        try {
            $deleted = (new FluentSaver($this->pdo))
                ->table('bancos')
                ->primaryKey('idbanco')
                ->softDelete($idbanco);

            if (!$deleted) {
                throw new Exception("No se pudo eliminar el registro");
            }

            return Response::json([
                "success" => true,
                "message" => "Registro eliminado correctamente"
            ]);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }
}