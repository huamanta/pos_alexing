<?php
require_once __DIR__ . "/Helpers.php";

use Carbon\Carbon;

class HistorialPagos extends Helpers
{

    public function __construct()
    {
        parent::__construct();
    }

    public function listarHistorialPagos($fecha_inicio = null, $fecha_fin = null)
    {
        $page = (int) ($_GET['page'] ?? 1);
        $limit = (int) ($_GET['limit'] ?? 20);
        $search = $_GET['search'] ?? '';

        $sql = (new DBQuery($this->pdo))
            ->select('
                dcpc.*,
                p.nombre AS cliente,
                p.num_documento,
                CONCAT(
                    "Corresponde a la venta ",
                    v.serie_comprobante, "-",
                    v.num_comprobante,
                    " - Cuota N° ",
                    (
                        SELECT COUNT(*)
                        FROM cuentas_por_cobrar cpc2
                        WHERE cpc2.idventa = cpc.idventa
                        AND cpc2.idcpc <= cpc.idcpc
                    )
                ) AS descripcion,
                b.nombre AS banco
            ')
            ->from('detalle_cuentas_por_cobrar dcpc')
            ->join('cuentas_por_cobrar cpc', 'dcpc.idcpc = cpc.idcpc')
            ->join('venta v', 'v.idventa = cpc.idventa')
            ->join('persona p', 'p.idpersona = v.idcliente')
            ->leftJoin('bancos b', 'b.idbanco = dcpc.idbanco')
            ->orderBy('dcpc.created_at', 'DESC')
            ->search($search, [
                'p.nombre',
                'p.num_documento',
                'v.serie_comprobante',
                'v.num_comprobante',
                'b.nombre'
            ]);

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $sql->whereBetween('dcpc.created_at', $fecha_inicio, $fecha_fin);
        }

        $data = $sql->paginate($page, $limit);

        foreach ($data['data'] as &$item) {
            // Formatear fechas
            $item['fechapago_str'] = $item['fechapago'] ? Carbon::parse($item['fechapago'])->format('d-m-Y h:i A') : null;
            $item['created_at_str'] = $item['created_at'] ? Carbon::parse($item['created_at'])->format('d-m-Y h:i A') : null;

            // Formatear montos a formato de moneda
            $item['montopagado_str'] = Helpers::get_currency_symbol($item['montopagado']);
            $item['montotarjeta_str'] = Helpers::get_currency_symbol($item['montotarjeta']);
            $item['total_pagado_str'] = Helpers::get_currency_symbol((float) $item['montopagado'] + (float) $item['montotarjeta']);
        }
        unset($item);

        return Response::json($data);
    }
}
