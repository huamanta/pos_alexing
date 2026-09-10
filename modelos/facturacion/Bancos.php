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

        return Response::json($data);
    }


    public function listarMovimientos(
        int $idbanco,
    ) {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 20;
        $movimientos = (new DBQuery($this->pdo))
            ->select([
                'm.fecha AS fecha',
                'm.tipo AS tipo',
                'm.totaldeposito AS monto',
                'p.nombre AS responsable',
                "'movimiento' AS origen",
            ])
            ->from('movimiento m')
            ->join('usuario u', 'u.idusuario=m.idusuario')
            ->join('personal p', 'p.idpersonal=u.idpersonal')
            ->where('m.idbanco', '=', $idbanco);

        $cuentas = (new DBQuery($this->pdo))
            ->select([
                'dcc.fechapago AS fecha',
                "'Ingresos' AS tipo",
                'dcc.montotarjeta AS monto',
                'p.nombre AS responsable',
                "'cuenta_por_cobrar' AS origen",
            ])
            ->from('detalle_cuentas_por_cobrar dcc')
            ->join('personal p', 'p.idpersonal=dcc.idpersonal')
            ->where('dcc.idbanco', '=', $idbanco);

        $venta_pago = (new DBQuery($this->pdo))
            ->select([
                'vp.created_at AS fecha',
                "'Ingresos' as tipo",
                'vp.monto',
                'p.nombre AS responsable',
                "'venta' AS origen"
            ])
            ->from('venta_pago vp')
            ->join('usuario u', 'u.idusuario=vp.idusuario')
            ->join('personal p', 'p.idpersonal=u.idpersonal')
            ->where('vp.idbanco', '=', $idbanco);

        $resultado = $movimientos
            ->unionAll($cuentas)
            ->unionAll($venta_pago)
            ->orderBy('fecha', 'DESC')
            ->paginate($page, $limit);

        return Response::json($resultado);
    }


    public function insertar($data) {
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


    public function editar($idbanco, $data){
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


    public function mostrar($idbanco) {
        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('bancos')
            ->where('idbanco', '=', $idbanco)
            ->first();

        return Response::json($data);
    }


    public function eliminar($idbanco) {
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