<?php
require_once __DIR__ . "/../modelos/Helpers.php";
require_once __DIR__ . "/../core/FluentQuery.php";
require_once __DIR__ . '/../core/Response.php';

class Rutas extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function obtener(string $url): ?array
    {
        foreach ($this->listaRutas() as $modulo) {
            if (($modulo['ruta'] ?? null) === $url) {
                return $modulo;
            }

            foreach ($modulo['childrens'] ?? [] as $hijo) {
                if (($hijo['ruta'] ?? null) === $url) {
                    $hijo['submodulo'] = true;
                    $hijo['modulo'] = $modulo['nombre'];

                    return $hijo;
                }
            }
        }

        return null;
    }

    public function verificarPermiso(int $id): bool
    {
        $idusuario = (int) ($_SESSION['idusuario'] ?? 0);

        if (Helpers::esSuperusuario($idusuario)) {
            return true;
        }

        $resultado = (new DBQuery($this->pdo))
            ->query("
                    SELECT EXISTS (
                        SELECT 1
                        FROM permisos p
                        WHERE p.modulo_id = :modulo_id
                            AND p.codigo LIKE '%.ver'
                            AND (
                                EXISTS (
                                    SELECT 1
                                    FROM rol_permisos rp
                                    INNER JOIN usuario_roles ur
                                        ON ur.rol_id = rp.rol_id
                                    WHERE rp.permiso_id = p.id
                                        AND ur.idusuario = :usuario_rol
                                )
                                OR EXISTS (
                                    SELECT 1
                                    FROM usuario_permisos up
                                    WHERE up.permiso_id = p.id
                                        AND up.idusuario = :usuario_directo
                                )
                            )
                    ) AS autorizado
                ", 
                [
                'modulo_id' => $id,
                'usuario_rol' => $idusuario,
                'usuario_directo' => $idusuario,
            ])
            ->first();

        return (bool) ($resultado['autorizado'] ?? false);
    }

    public function listaRutas(): array
    {
        $modulos = (new DBQuery($this->pdo))
            ->select('*')
            ->from('modulos')
            ->where('menu', '=', 1)
            ->orderBy('orden', 'ASC')
            ->get();

        $resultado = [];
        $padres = [];
        $autorizados = [];

        foreach ($modulos as $modulo) {
            $id = (int) $modulo['id'];

            $autorizados[$id] = $this->verificarPermiso($id);

            $resultado[$id] = [
                'id' => $id,
                'nombre' => $modulo['nombre'],
                'ruta' => $modulo['ruta'],
                'icono' => $modulo['icono'],
                'childrens' => [],
            ];

            $padres[$id] = !empty($modulo['modulo_padre_id'])
                ? (int) $modulo['modulo_padre_id']
                : null;
        }

        foreach (array_keys($resultado) as $id) {
            $padreId = $padres[$id];

            if ($padreId !== null && isset($resultado[$padreId])) {
                $resultado[$padreId]['childrens'][] = $resultado[$id];
            }
        }

        $filtrar = function (array $modulo) use (&$filtrar, $autorizados): ?array {
            $hijosAutorizados = [];

            foreach ($modulo['childrens'] as $hijo) {
                $hijoFiltrado = $filtrar($hijo);

                if ($hijoFiltrado !== null) {
                    $hijosAutorizados[] = $hijoFiltrado;
                }
            }

            $id = (int) $modulo['id'];

            // Mantener el padre si tiene permiso propio o hijos autorizados.
            if (!($autorizados[$id] ?? false) && empty($hijosAutorizados)) {
                return null;
            }

            $modulo['childrens'] = $hijosAutorizados;

            return $modulo;
        };

        $rutas = [];

        foreach ($resultado as $id => $modulo) {
            if ($padres[$id] !== null) {
                continue;
            }

            $moduloFiltrado = $filtrar($modulo);

            if ($moduloFiltrado !== null) {
                $rutas[] = $moduloFiltrado;
            }
        }

        return $rutas;
    }

    // public function listaRutas()
    // {
    //     return [
    //         [
    //             'nombre' => 'Inicio',
    //             'ruta' => 'inicio',
    //             'icono' => 'fas fa-home',
    //             'permiso' => 'inicio',
    //             'childrens' => [],
    //         ],
    //         [
    //             'nombre' => 'Clientes',
    //             'ruta' => 'cliente',
    //             'icono' => 'fas fa-users',
    //             'permiso' => 'Clientes',
    //             'childrens' => [],
    //         ],
    //         [
    //             'nombre' => 'Ventas',
    //             'ruta' => null,
    //             'icono' => 'fas fa-shopping-bag',
    //             'permiso' => null,
    //             'childrens' => [
    //                 ['nombre' => 'Contratos', 'ruta' => 'contrato', 'icono' => 'fas fa-file-contract', 'permiso' => 'Contratos'],
    //                 ['nombre' => 'Solicitudes', 'ruta' => 'solicitudes', 'icono' => 'fas fa-file-signature', 'permiso' => 'Solicitudes'],
    //                 ['nombre' => 'Punto de venta', 'ruta' => 'venta-pos', 'icono' => 'fas fa-cash-register', 'permiso' => 'Punto de venta'],
    //                 ['nombre' => 'Guía de remisión', 'ruta' => 'guia', 'icono' => 'fas fa-truck', 'permiso' => 'Guia de remision'],
    //                 ['nombre' => 'Cotizaciones', 'ruta' => 'cotizacion', 'icono' => 'fas fa-file-invoice-dollar', 'permiso' => 'Cotizaciones'],
    //                 ['nombre' => 'Notas de crédito', 'ruta' => 'nota-credito', 'icono' => 'fas fa-file-invoice', 'permiso' => 'Notas de credito'],
    //             ],
    //         ],
    //         [
    //             'nombre' => 'Resumen caja',
    //             'ruta' => 'caja-chica',
    //             'icono' => 'fas fa-wallet',
    //             'permiso' => 'Resumen caja',
    //             'childrens' => [],
    //         ],
    //         [
    //             'nombre' => 'Facturacion y cajas',
    //             'ruta' => null,
    //             'icono' => 'fas fa-store-alt',
    //             'permiso' => null,
    //             'childrens' => [
    //                 ['nombre' => 'Comprobantes', 'ruta' => 'venta', 'icono' => 'fas fa-file-invoice', 'permiso' => 'Comprobantes'],
    //                 ['nombre' => 'Resumen diario', 'ruta' => 'resumen', 'icono' => 'fas fa-calendar-day', 'permiso' => 'Resumen diario'],
    //                 ['nombre' => 'Bancos', 'ruta' => 'bancos', 'icono' => 'fas fa-university', 'permiso' => 'Bancos'],
    //                 ['nombre' => 'Conceptos', 'ruta' => 'conceptos', 'icono' => 'fas fa-list', 'permiso' => 'Conceptos'],
    //                 ['nombre' => 'Movimientos', 'ruta' => 'movimientos', 'icono' => 'fas fa-exchange-alt', 'permiso' => 'Movimientos'],
    //                 ['nombre' => 'Cajas', 'ruta' => 'cajas', 'icono' => 'fas fa-cash-register', 'permiso' => 'Cajas'],
    //             ],
    //         ],
    //         [
    //             'nombre' => 'Almacen',
    //             'ruta' => null,
    //             'icono' => 'fas fa-boxes',
    //             'permiso' => null,
    //             'childrens' => [
    //                 ['nombre' => 'Productos', 'ruta' => 'producto', 'icono' => 'fas fa-box', 'permiso' => 'Productos'],
    //                 ['nombre' => 'Servicios', 'ruta' => 'servicio', 'icono' => 'fas fa-concierge-bell', 'permiso' => 'Servicios'],
    //                 ['nombre' => 'Nombres Precios', 'ruta' => 'nombres-precios', 'icono' => 'fas fa-tags', 'permiso' => 'Nombres Precios'],
    //                 ['nombre' => 'Categorias', 'ruta' => 'categoria', 'icono' => 'fas fa-sitemap', 'permiso' => 'Categorias'],
    //                 ['nombre' => 'Marcas', 'ruta' => 'marca', 'icono' => 'fas fa-copyright', 'permiso' => 'Marcas'],
    //                 ['nombre' => 'Modelos', 'ruta' => 'modelo', 'icono' => 'fas fa-cubes', 'permiso' => 'Modelos'],
    //                 ['nombre' => 'Lineas', 'ruta' => 'rubro', 'icono' => 'fas fa-layer-group', 'permiso' => 'Lineas'],
    //                 ['nombre' => 'Condicion de venta', 'ruta' => 'condicionventa', 'icono' => 'fas fa-handshake', 'permiso' => 'Condicion de venta'],
    //                 ['nombre' => 'Unidad de medida', 'ruta' => 'unidad-medida', 'icono' => 'fas fa-ruler-combined', 'permiso' => 'Unidad de medida'],
    //                 ['nombre' => 'Traslados', 'ruta' => 'traslado', 'icono' => 'fas fa-truck-loading', 'permiso' => 'Traslados'],
    //                 ['nombre' => 'Reportes', 'ruta' => 'reportes-digemid', 'icono' => 'fas fa-chart-pie', 'permiso' => 'Reportes'],
    //                 ['nombre' => 'Vencimientos', 'ruta' => 'reportes-vencimiento', 'icono' => 'fas fa-calendar-times', 'permiso' => 'Vencimientos'],
    //             ],
    //         ],
    //         [
    //             'nombre' => 'Inventario',
    //             'ruta' => null,
    //             'icono' => 'fas fa-clipboard-list',
    //             'permiso' => null,
    //             'childrens' => [
    //                 ['nombre' => 'Kardex', 'ruta' => 'kardex', 'icono' => 'fas fa-book', 'permiso' => 'Kardex'],
    //                 ['nombre' => 'Toma de inventario', 'ruta' => 'toma-inventario', 'icono' => 'fas fa-clipboard-check', 'permiso' => 'Toma de inventario'],
    //                 ['nombre' => 'Ajuste de inventario', 'ruta' => 'ajuste-inventario', 'icono' => 'fas fa-sliders-h', 'permiso' => 'Ajuste de inventario'],
    //             ],
    //         ],
    //         [
    //             'nombre' => 'Compras',
    //             'ruta' => null,
    //             'icono' => 'fas fa-dolly',
    //             'permiso' => null,
    //             'childrens' => [
    //                 ['nombre' => 'Crear compras', 'ruta' => 'compra', 'icono' => 'fas fa-cart-plus', 'permiso' => 'Crear compras'],
    //                 ['nombre' => 'Proveedores', 'ruta' => 'proveedor', 'icono' => 'fas fa-truck', 'permiso' => 'Proveedores'],
    //                 ['nombre' => 'Cuentas por pagar', 'ruta' => 'cuentasxpagar', 'icono' => 'fas fa-file-invoice-dollar', 'permiso' => 'Cuentas por pagar'],
    //             ],
    //         ],
    //         [
    //             'nombre' => 'Cobros',
    //             'ruta' => null,
    //             'icono' => 'fas fa-hand-holding-usd',
    //             'permiso' => null,
    //             'childrens' => [
    //                 ['nombre' => 'Cuentas por cobrar', 'ruta' => 'cuentas-cobrar', 'icono' => 'fas fa-money-bill-wave', 'permiso' => 'Cuentas por cobrar'],
    //                 ['nombre' => 'Refinanciar creditos', 'ruta' => 'refinanciamientos', 'icono' => 'fas fa-sync-alt', 'permiso' => 'Refinanciar creditos'],
    //                 ['nombre' => 'Recuperacion de vehiculos', 'ruta' => 'recuperacion-vehiculos', 'icono' => 'fas fa-car', 'permiso' => 'Recuperacion de vehiculos'],
    //             ],
    //         ],
    //         [
    //             'nombre' => 'Taller',
    //             'ruta' => null,
    //             'icono' => 'fas fa-tools',
    //             'permiso' => null,
    //             'childrens' => [
    //                 ['nombre' => 'Orden de trabajo', 'ruta' => 'orden-trabajo', 'icono' => 'fas fa-tools', 'permiso' => 'Orden de trabajo'],
    //             ],
    //         ],
    //         [
    //             'nombre' => 'Personal',
    //             'ruta' => null,
    //             'icono' => 'fas fa-users-cog',
    //             'permiso' => null,
    //             'childrens' => [
    //                 ['nombre' => 'Asistencia', 'ruta' => 'asistencia', 'icono' => 'fas fa-user-clock', 'permiso' => 'Asistencia'],
    //                 ['nombre' => 'Personal', 'ruta' => 'personal', 'icono' => 'fas fa-users', 'permiso' => 'Personal'],
    //                 ['nombre' => 'Usuarios', 'ruta' => 'usuario', 'icono' => 'fas fa-user-cog', 'permiso' => 'Usuarios'],
    //                 ['nombre' => 'Permisos', 'ruta' => 'permiso', 'icono' => 'fas fa-user-shield', 'permiso' => 'Permisos'],
    //             ],
    //         ],
    //         [
    //             'nombre' => 'Configuracion',
    //             'ruta' => null,
    //             'icono' => 'fas fa-cog',
    //             'permiso' => null,
    //             'childrens' => [
    //                 ['nombre' => 'Datos generales', 'ruta' => 'negocio', 'icono' => 'fas fa-building', 'permiso' => 'Datos generales'],
    //                 ['nombre' => 'Facturadores', 'ruta' => 'empresas', 'icono' => 'fas fa-file-signature', 'permiso' => 'Facturadores'],
    //                 ['nombre' => 'Sucursales', 'ruta' => 'sucursal', 'icono' => 'fas fa-store', 'permiso' => 'Sucursales'],
    //             ],
    //         ],
    //         [
    //             'nombre' => 'Consulta compras',
    //             'ruta' => null,
    //             'icono' => 'fas fa-shopping-cart',
    //             'permiso' => null,
    //             'childrens' => [
    //                 ['nombre' => 'Compras', 'ruta' => 'compras-fecha', 'icono' => 'fas fa-search-dollar', 'permiso' => 'Compras'],
    //                 ['nombre' => 'Compras por proveedor', 'ruta' => 'compras-proveedor', 'icono' => 'fas fa-truck-loading', 'permiso' => 'Compras por proveedor'],
    //             ],
    //         ],
    //         [
    //             'nombre' => 'Consultar ventas',
    //             'ruta' => null,
    //             'icono' => 'fas fa-chart-line',
    //             'permiso' => null,
    //             'childrens' => [
    //                 ['nombre' => 'Ventas por cliente', 'ruta' => 'ventas-cliente', 'icono' => 'fas fa-user-tag', 'permiso' => 'Ventas por cliente'],
    //                 ['nombre' => 'Ventas por vendedor', 'ruta' => 'ventas-vendedor', 'icono' => 'fas fa-user-tie', 'permiso' => 'Ventas por vendedor'],
    //                 ['nombre' => 'Ventas - utilidades', 'ruta' => 'ventas-producto', 'icono' => 'fas fa-chart-line', 'permiso' => 'Ventas - utilidades'],
    //                 ['nombre' => 'Creditos - utilidades', 'ruta' => 'ventas-credito', 'icono' => 'fas fa-credit-card', 'permiso' => 'Creditos - utilidades'],
    //                 ['nombre' => 'Reporte consolidado', 'ruta' => 'reporte', 'icono' => 'fas fa-chart-bar', 'permiso' => 'Reporte consolidado'],
    //                 ['nombre' => 'Ventas por servicio', 'ruta' => 'ventas-servicio', 'icono' => 'fas fa-concierge-bell', 'permiso' => 'Ventas por servicio'],
    //                 ['nombre' => 'Ventas detalle', 'ruta' => 'detalle-venta-comprobante', 'icono' => 'fas fa-list-alt', 'permiso' => 'Ventas detalle'],
    //             ],
    //         ],

    //         // RUTAS DEL SISTEMA

    //         [
    //             'nombre' => null,
    //             'ruta' => 'recuperar',
    //             'icono' => null,
    //             'permiso' => null,
    //             'childrens' => []
    //         ],
    //         [
    //             'nombre' => null,
    //             'ruta' => 'reset',
    //             'icono' => null,
    //             'permiso' => null,
    //             'childrens' => []
    //         ],
    //         [
    //             'nombre' => null,
    //             'ruta' => 'salirsucursal',
    //             'icono' => null,
    //             'permiso' => null,
    //             'childrens' => []
    //         ],
    //         [
    //             'nombre' => null,
    //             'ruta' => 'salir',
    //             'icono' => null,
    //             'permiso' => null,
    //             'childrens' => []
    //         ],
    //         [
    //             'nombre' => null,
    //             'ruta' => 'restaurant',
    //             'icono' => null,
    //             'permiso' => null,
    //             'childrens' => []
    //         ],
    //         [
    //             'nombre' => null,
    //             'ruta' => 'pos',
    //             'icono' => null,
    //             'permiso' => null,
    //             'childrens' => []
    //         ],
    //         [
    //             'nombre' => null,
    //             'ruta' => 'service',
    //             'icono' => null,
    //             'permiso' => null,
    //             'childrens' => []
    //         ],
    //         [
    //             'nombre' => null,
    //             'ruta' => 'caja-chica2',
    //             'icono' => null,
    //             'permiso' => null,
    //             'childrens' => []
    //         ],
    //         [
    //             'nombre' => null,
    //             'ruta' => 'configuracion',
    //             'icono' => null,
    //             'permiso' => 'Configuracion',
    //             'childrens' => []
    //         ]
    //     ];
    // }
}