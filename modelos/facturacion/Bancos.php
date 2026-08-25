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
                'm.totalefectivo AS efectivo',
                'm.totaldeposito AS deposito',
                'p.nombre AS responsable',
                "'movimiento' AS origen",
            ])
            ->from('movimiento m')
            ->join(
                'usuario u',
                'u.idusuario=m.idusuario'
            )
            ->join(
                'personal p',
                'p.idpersonal=u.idpersonal'
            )
            ->where(
                'm.idbanco',
                '=',
                $idbanco
            );

        $cuentas = (new DBQuery($this->pdo))
            ->select([
                'dcc.fechapago AS fecha',
                "'Ingresos' AS tipo",
                'dcc.montopagado AS efectivo',
                'dcc.montotarjeta AS deposito',
                'p.nombre AS responsable',
                "'cuenta_por_cobrar' AS origen",
            ])
            ->from('detalle_cuentas_por_cobrar dcc')
            ->join(
                'personal p',
                'p.idpersonal=dcc.idpersonal'
            )
            ->where(
                'dcc.idbanco',
                '=',
                $idbanco
            );

        $resultado = $movimientos
            ->unionAll($cuentas)
            ->orderBy('fecha', 'DESC')
            ->paginate(
                $page,
                $limit
            );

        return Response::json($resultado);
    }
}