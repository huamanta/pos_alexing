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
            ->where('m.formapago', '=', $metodo)
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
            ->where('dcc.formapago', '=', $metodo)
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
            ->where('vp.metodo_pago', '=', $metodo)
            ->where('vp.monto', '>', 0);

        $resultado = $movimientos
            ->unionAll($cuentas)
            ->unionAll($venta_pago)
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

        $movimientos = (new DBQuery($this->pdo))
            ->select([
                'COUNT(*) AS movimientos',
                "COALESCE(SUM(CASE WHEN tipo = 'Ingresos' THEN totaldeposito ELSE 0 END), 0) AS ingresos",
                "COALESCE(SUM(CASE WHEN tipo = 'Egresos' THEN totaldeposito ELSE 0 END), 0) AS egresos",
            ])
            ->from('movimiento')
            ->where('formapago', '=', $metodo)
            ->where('totaldeposito', '>', 0)
            ->first();

        $cuentas = (new DBQuery($this->pdo))
            ->select([
                'COUNT(*) AS movimientos',
                'COALESCE(SUM(montotarjeta), 0) AS ingresos',
                '0 AS egresos',
            ])
            ->from('detalle_cuentas_por_cobrar')
            ->where('formapago', '=', $metodo)
            ->where('montotarjeta', '>', 0)
            ->first();

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

        foreach ([$movimientos, $cuentas, $ventas] as $fila) {
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