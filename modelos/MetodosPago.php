<?php
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/../core/Response.php';
class MetodosPago extends Helpers
{

    public function __construct()
    {
        parent::__construct();
    }

    public function listarMetodosPago()
    {
        $metodos = [
            ['id' => 1, 'nombre' => 'Efectivo'],
            ['id' => 2, 'nombre' => 'Transferencia'],
            ['id' => 3, 'nombre' => 'Yape'],
            ['id' => 4, 'nombre' => 'Plin'],
            ['id' => 5, 'nombre' => 'Deposito'],
        ];



        return Response::json($metodos);
    }

    public function listarDetalleMetodo($metodo)
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = max(1, (int) ($_GET['limit'] ?? 10));
        $search = trim($_GET['search'] ?? '');

        $movimientos = (new DBQuery($this->pdo))
            ->select([
                'm.fecha AS fecha',
                'CONVERT(m.tipo USING utf8mb4) COLLATE utf8mb4_spanish_ci AS tipo',
                ($metodo === 'Efectivo' ? 'm.totalefectivo' : 'm.totaldeposito') . ' AS monto',
                'CONVERT(p.nombre USING utf8mb4) COLLATE utf8mb4_spanish_ci AS responsable',
                "CONVERT('Movimientos' USING utf8mb4) COLLATE utf8mb4_spanish_ci AS origen",
                "CONVERT(CONCAT(pe.nombre, ' <span class=\"badge badge-primary\">Personal</span>') USING utf8mb4) COLLATE utf8mb4_spanish_ci AS cliente",
                "CONVERT(CONCAT('Movimiento por ', m.descripcion) USING utf8mb4) COLLATE utf8mb4_spanish_ci AS descripcion",
            ])
            ->from('movimiento m')
            ->join('usuario u', 'u.idusuario = m.idusuario')
            ->join('personal p', 'p.idpersonal = u.idpersonal')
            ->join('personal pe', 'pe.idpersonal = m.idpersonal');

        if ($metodo === 'Efectivo') {
            $movimientos
                ->where('m.totalefectivo', '>', 0);
        } else {
            $movimientos
                ->where('m.formapago', '=', $metodo)
                ->where('m.totaldeposito', '>', 0);
        }

        $cuentascobrar = (new DBQuery($this->pdo))
            ->select([
                'dcc.fechapago AS fecha',
                "CONVERT('Ingresos' USING utf8mb4) COLLATE utf8mb4_spanish_ci AS tipo",
                ($metodo === 'Efectivo' ? 'dcc.montopagado' : 'dcc.montotarjeta') . ' AS monto',
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
            ->leftJoin('persona pe', 'pe.idpersona = v.idcliente');

        if ($metodo === 'Efectivo') {
            $cuentascobrar
                ->where('dcc.montopagado', '>', 0);
        } else {
            $cuentascobrar
                ->where('dcc.formapago', '=', $metodo)
                ->where('dcc.montotarjeta', '>', 0);
        }

        $cuentaspagar = (new DBQuery($this->pdo))
            ->select([
                'dcp.fechapago AS fecha',
                "CONVERT('Egresos' USING utf8mb4) COLLATE utf8mb4_spanish_ci AS tipo",
                ($metodo === 'Efectivo' ? 'dcp.montopagado' : 'dcp.montotarjeta') . ' AS monto',
                'CONVERT(p.nombre USING utf8mb4) COLLATE utf8mb4_spanish_ci AS responsable',
                "CONVERT('Cuentas por pagar' USING utf8mb4) COLLATE utf8mb4_spanish_ci AS origen",
                'CONVERT(pe.nombre USING utf8mb4) COLLATE utf8mb4_spanish_ci AS cliente',
                "CONVERT(CONCAT(
                    'Pago aplicado a compra N° ',
                    c.serie_comprobante, '-', c.num_comprobante
                ) USING utf8mb4) COLLATE utf8mb4_spanish_ci AS descripcion",
            ])
            ->from('detalle_cuentas_por_pagar dcp')
            ->join('personal p', 'p.idpersonal = dcp.idpersonal')
            ->join('cuentas_por_pagar cp', 'cp.idcpp = dcp.idcpp')
            ->join('compra c', 'c.idcompra = cp.idcompra')
            ->leftJoin('persona pe', 'pe.idpersona = c.idproveedor');

        if ($metodo === 'Efectivo') {
            $cuentaspagar
                ->where('dcp.montopagado', '>', 0);
        } else {
            $cuentaspagar
                ->where('dcp.formapago', '=', $metodo)
                ->where('dcp.montotarjeta', '>', 0);
        }

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
            ->where('vp.metodo_pago', '=', $metodo)
            ->where('vp.monto', '>', 0);

        $resultado = $movimientos
            ->unionAll($cuentascobrar)
            ->unionAll($cuentaspagar)
            ->unionAll($venta_pago)
            ->search($search, ['tipo', 'responsable', 'cliente', 'descripcion'])
            ->orderBy('fecha', 'DESC')
            ->paginate($page, $limit);

        $resultado['meta']['summary'] = $this->obtenerResumenMetodoPago($metodo);
        return Response::json($resultado);
    }

    private function obtenerResumenMetodoPago(string $metodo): array
    {
        $resumen = [
            'movimientos' => 0,
            'ingresos' => 0.0,
            'egresos' => 0.0,
            'neto' => 0.0,
        ];

        // MOVIMIENTOS
        $movimientos = (new DBQuery($this->pdo))
            ->select([
                'COUNT(*) AS movimientos',
                "COALESCE(SUM(CASE WHEN tipo = 'Ingresos' THEN " . ($metodo === 'Efectivo' ? 'totalefectivo' : 'totaldeposito') . " ELSE 0 END), 0) AS ingresos",
                "COALESCE(SUM(CASE WHEN tipo = 'Egresos' THEN " . ($metodo === 'Efectivo' ? 'totalefectivo' : 'totaldeposito') . " ELSE 0 END), 0) AS egresos",
            ])
            ->from('movimiento')
            ->where($metodo === 'Efectivo' ? 'totalefectivo' : 'totaldeposito', '>', 0);

        if ($metodo !== 'Efectivo') {
            $movimientos->where('formapago', '=', $metodo);
        }

        $movimientos = $movimientos->first();

        // CUENTAS POR COBRAR
        $cuentas = (new DBQuery($this->pdo))
            ->select([
                'COUNT(*) AS movimientos',
                'COALESCE(SUM(' . ($metodo === 'Efectivo' ? 'montopagado' : 'montotarjeta') . '), 0) AS ingresos',
                '0 AS egresos',
            ])
            ->from('detalle_cuentas_por_cobrar');

        if ($metodo === 'Efectivo') {
            $cuentas->where('montopagado', '>', 0);
        } else {
            $cuentas
                ->where('formapago', '=', $metodo)
                ->where('montotarjeta', '>', 0);
        }

        $cuentas = $cuentas->first();

        // CUENTAS POR PAGAR
        $cuentasPagar = (new DBQuery($this->pdo))
            ->select([
                'COUNT(*) AS movimientos',
                '0 AS ingresos',
                'COALESCE(SUM(' . ($metodo === 'Efectivo' ? 'montopagado' : 'montotarjeta') . '), 0) AS egresos',
            ])
            ->from('detalle_cuentas_por_pagar');

        if ($metodo === 'Efectivo') {
            $cuentasPagar->where('montopagado', '>', 0);
        } else {
            $cuentasPagar
                ->where('formapago', '=', $metodo)
                ->where('montotarjeta', '>', 0);
        }

        $cuentasPagar = $cuentasPagar->first();

        // PAGOS INICIALES DE VENTAS
        $ventas = (new DBQuery($this->pdo))
            ->select([
                'COUNT(*) AS movimientos',
                'COALESCE(SUM(monto), 0) AS ingresos',
                '0 AS egresos',
            ])
            ->from('venta_pago')
            ->where('metodo_pago', '=', $metodo)
            ->where('monto', '>', 0)
            ->first();

        foreach ([$movimientos, $cuentas, $cuentasPagar, $ventas] as $fila) {
            if (!$fila) {
                continue;
            }

            $resumen['movimientos'] += (int) ($fila['movimientos'] ?? 0);
            $resumen['ingresos'] += (float) ($fila['ingresos'] ?? 0);
            $resumen['egresos'] += (float) ($fila['egresos'] ?? 0);
        }

        $resumen['neto'] = $resumen['ingresos'] - $resumen['egresos'];

        return $resumen;
    }
}