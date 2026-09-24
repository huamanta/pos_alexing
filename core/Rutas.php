<?php

class Rutas
{
    const MODULOS = [
        'Ventas' => 'fas fa-shopping-bag',
        'Facturacion y cajas' => 'fas fa-store-alt',
        'Almacen' => 'fas fa-boxes',
        'Inventario' => 'fas fa-clipboard-list',
        'Compras' => 'fas fa-dolly',
        'Cobros' => 'fas fa-hand-holding-usd',
        'Taller' => 'fas fa-tools',
        'Personal' => 'fas fa-users-cog',
        'Configuracion' => 'fas fa-cog',
        'Consulta compras' => 'fas fa-shopping-cart',
        'Consultar ventas' => 'fas fa-chart-line',
    ];

    const LISTA = [

        // INICIO
        [
            'url' => 'inicio',
            'modulo' => null,
            'submodulo' => 'Inicio',
            'permiso' => 'inicio',
            'icono' => 'fas fa-home',
            'menu' => true
        ],

        // PROCESOS INTERNOS
        [
            'url' => 'procesar',
            'modulo' => null,
            'submodulo' => null,
            'permiso' => 'procesar',
            'icono' => null,
            'menu' => false
        ],

        // VENTAS
        [
            'url' => 'contrato',
            'modulo' => 'Ventas',
            'submodulo' => 'Contratos',
            'permiso' => 'Contratos',
            'icono' => 'fas fa-file-contract',
            'menu' => true
        ],
        [
            'url' => 'solicitudes',
            'modulo' => 'Ventas',
            'submodulo' => 'Solicitudes',
            'permiso' => 'Solicitudes',
            'icono' => 'fas fa-file-signature',
            'menu' => true
        ],
        [
            'url' => 'venta-pos',
            'modulo' => 'Ventas',
            'submodulo' => 'Punto de venta',
            'permiso' => 'Punto de venta',
            'icono' => 'fas fa-cash-register',
            'menu' => true
        ],
        [
            'url' => 'guia',
            'modulo' => 'Ventas',
            'submodulo' => 'Guia de remision',
            'permiso' => 'Guia de remision',
            'icono' => 'fas fa-truck',
            'menu' => true
        ],
        [
            'url' => 'cotizacion',
            'modulo' => 'Ventas',
            'submodulo' => 'Cotizaciones',
            'permiso' => 'Cotizaciones',
            'icono' => 'fas fa-file-invoice-dollar',
            'menu' => true
        ],
        [
            'url' => 'nota-credito',
            'modulo' => 'Ventas',
            'submodulo' => 'Notas de credito',
            'permiso' => 'Notas de credito',
            'icono' => 'fas fa-file-invoice',
            'menu' => true
        ],

        // CLIENTES
        [
            'url' => 'cliente',
            'modulo' => null,
            'submodulo' => 'Clientes',
            'permiso' => 'Clientes',
            'icono' => 'fas fa-users',
            'menu' => true
        ],

        // FACTURACION Y CAJAS
        [
            'url' => 'venta',
            'modulo' => 'Facturacion y cajas',
            'submodulo' => 'Comprobantes',
            'permiso' => 'Comprobantes',
            'icono' => 'fas fa-file-invoice',
            'menu' => true
        ],
        [
            'url' => 'resumen',
            'modulo' => 'Facturacion y cajas',
            'submodulo' => 'Resumen diario',
            'permiso' => 'Resumen diario',
            'icono' => 'fas fa-calendar-day',
            'menu' => true
        ],
        [
            'url' => 'bancos',
            'modulo' => 'Facturacion y cajas',
            'submodulo' => 'Bancos',
            'permiso' => 'Bancos',
            'icono' => 'fas fa-university',
            'menu' => true
        ],
        [
            'url' => 'conceptos',
            'modulo' => 'Facturacion y cajas',
            'submodulo' => 'Conceptos',
            'permiso' => 'Conceptos',
            'icono' => 'fas fa-list',
            'menu' => true
        ],
        [
            'url' => 'movimientos',
            'modulo' => 'Facturacion y cajas',
            'submodulo' => 'Movimientos',
            'permiso' => 'Movimientos',
            'icono' => 'fas fa-exchange-alt',
            'menu' => true
        ],
        [
            'url' => 'cajas',
            'modulo' => 'Facturacion y cajas',
            'submodulo' => 'Cajas',
            'permiso' => 'Cajas',
            'icono' => 'fas fa-cash-register',
            'menu' => true
        ],

        // ALMACEN
        [
            'url' => 'producto',
            'modulo' => 'Almacen',
            'submodulo' => 'Productos',
            'permiso' => 'Productos',
            'icono' => 'fas fa-box',
            'menu' => true
        ],
        [
            'url' => 'servicio',
            'modulo' => 'Almacen',
            'submodulo' => 'Servicios',
            'permiso' => 'Servicios',
            'icono' => 'fas fa-concierge-bell',
            'menu' => true
        ],
        [
            'url' => 'nombres-precios',
            'modulo' => 'Almacen',
            'submodulo' => 'Nombres Precios',
            'permiso' => 'Nombres Precios',
            'icono' => 'fas fa-tags',
            'menu' => true
        ],
        [
            'url' => 'categoria',
            'modulo' => 'Almacen',
            'submodulo' => 'Categorias',
            'permiso' => 'Categorias',
            'icono' => 'fas fa-sitemap',
            'menu' => true
        ],
        [
            'url' => 'marca',
            'modulo' => 'Almacen',
            'submodulo' => 'Marcas',
            'permiso' => 'Marcas',
            'icono' => 'fas fa-copyright',
            'menu' => true
        ],
        [
            'url' => 'modelo',
            'modulo' => 'Almacen',
            'submodulo' => 'Modelos',
            'permiso' => 'Modelos',
            'icono' => 'fas fa-cubes',
            'menu' => true
        ],
        [
            'url' => 'rubro',
            'modulo' => 'Almacen',
            'submodulo' => 'Lineas',
            'permiso' => 'Lineas',
            'icono' => 'fas fa-layer-group',
            'menu' => true
        ],
        [
            'url' => 'condicionventa',
            'modulo' => 'Almacen',
            'submodulo' => 'Condicion de venta',
            'permiso' => 'Condicion de venta',
            'icono' => 'fas fa-handshake',
            'menu' => true
        ],
        [
            'url' => 'unidad-medida',
            'modulo' => 'Almacen',
            'submodulo' => 'Unidad de medida',
            'permiso' => 'Unidad de medida',
            'icono' => 'fas fa-ruler-combined',
            'menu' => true
        ],
        [
            'url' => 'traslado',
            'modulo' => 'Almacen',
            'submodulo' => 'Traslados',
            'permiso' => 'Traslados',
            'icono' => 'fas fa-truck-loading',
            'menu' => true
        ],
        [
            'url' => 'reportes-digemid',
            'modulo' => 'Almacen',
            'submodulo' => 'Reportes',
            'permiso' => 'Reportes',
            'icono' => 'fas fa-chart-pie',
            'menu' => true
        ],
        [
            'url' => 'reportes-vencimiento',
            'modulo' => 'Almacen',
            'submodulo' => 'Vencimientos',
            'permiso' => 'Vencimientos',
            'icono' => 'fas fa-calendar-times',
            'menu' => true
        ],

        // INVENTARIO
        [
            'url' => 'toma-inventario',
            'modulo' => 'Inventario',
            'submodulo' => 'Toma de inventario',
            'permiso' => 'Toma de inventario',
            'icono' => 'fas fa-clipboard-check',
            'menu' => true
        ],
        [
            'url' => 'ajuste-inventario',
            'modulo' => 'Inventario',
            'submodulo' => 'Ajuste de inventario',
            'permiso' => 'Ajuste de inventario',
            'icono' => 'fas fa-sliders-h',
            'menu' => true
        ],

        // COMPRAS
        [
            'url' => 'compra',
            'modulo' => 'Compras',
            'submodulo' => 'Crear compras',
            'permiso' => 'Crear compras',
            'icono' => 'fas fa-cart-plus',
            'menu' => true
        ],
        [
            'url' => 'proveedor',
            'modulo' => 'Compras',
            'submodulo' => 'Proveedores',
            'permiso' => 'Proveedores',
            'icono' => 'fas fa-truck',
            'menu' => true
        ],

        // CAJA CHICA
        [
            'url' => 'caja-chica',
            'modulo' => null,
            'submodulo' => 'Caja chica',
            'permiso' => 'Caja chica',
            'icono' => 'fas fa-wallet',
            'menu' => true
        ],

        // COBROS
        [
            'url' => 'cuentas-cobrar',
            'modulo' => 'Cobros',
            'submodulo' => 'Cuentas por cobrar',
            'permiso' => 'Cuentas por cobrar',
            'icono' => 'fas fa-money-bill-wave',
            'menu' => true
        ],
        [
            'url' => 'refinanciamientos',
            'modulo' => 'Cobros',
            'submodulo' => 'Refinanciar creditos',
            'permiso' => 'Refinanciar creditos',
            'icono' => 'fas fa-sync-alt',
            'menu' => true
        ],
        [
            'url' => 'recuperacion-vehiculos',
            'modulo' => 'Cobros',
            'submodulo' => 'Recuperacion de vehiculos',
            'permiso' => 'Recuperacion de vehiculos',
            'icono' => 'fas fa-car',
            'menu' => true
        ],

        // CUENTAS POR PAGAR
        [
            'url' => 'cuentasxpagar',
            'modulo' => null,
            'submodulo' => 'Cuentas por pagar',
            'permiso' => 'Cuentas por pagar',
            'icono' => 'fas fa-file-invoice-dollar',
            'menu' => true
        ],

        // KARDEX
        [
            'url' => 'kardex',
            'modulo' => null,
            'submodulo' => 'Kardex',
            'permiso' => 'Kardex',
            'icono' => 'fas fa-book',
            'menu' => true
        ],

        // TALLER
        [
            'url' => 'orden-trabajo',
            'modulo' => 'Taller',
            'submodulo' => 'Orden de trabajo',
            'permiso' => 'Orden de trabajo',
            'icono' => 'fas fa-tools',
            'menu' => true
        ],

        // PERSONAL
        [
            'url' => 'asistencia',
            'modulo' => 'Personal',
            'submodulo' => 'Asistencia',
            'permiso' => 'Asistencia',
            'icono' => 'fas fa-user-clock',
            'menu' => true
        ],
        [
            'url' => 'personal',
            'modulo' => 'Personal',
            'submodulo' => 'Personal',
            'permiso' => 'Personal',
            'icono' => 'fas fa-users',
            'menu' => true
        ],
        [
            'url' => 'usuario',
            'modulo' => 'Personal',
            'submodulo' => 'Usuarios',
            'permiso' => 'Usuarios',
            'icono' => 'fas fa-user-cog',
            'menu' => true
        ],
        [
            'url' => 'permiso',
            'modulo' => 'Personal',
            'submodulo' => 'Permisos',
            'permiso' => 'Permisos',
            'icono' => 'fas fa-user-shield',
            'menu' => true
        ],

        // CONFIGURACION
        [
            'url' => 'negocio',
            'modulo' => 'Configuracion',
            'submodulo' => 'Datos generales',
            'permiso' => 'Datos generales',
            'icono' => 'fas fa-building',
            'menu' => true
        ],
        [
            'url' => 'empresas',
            'modulo' => 'Configuracion',
            'submodulo' => 'Facturadores',
            'permiso' => 'Facturadores',
            'icono' => 'fas fa-file-signature',
            'menu' => true
        ],
        [
            'url' => 'sucursal',
            'modulo' => 'Configuracion',
            'submodulo' => 'Sucursales',
            'permiso' => 'Sucursales',
            'icono' => 'fas fa-store',
            'menu' => true
        ],

        // CONSULTA COMPRAS
        [
            'url' => 'compras-fecha',
            'modulo' => 'Consulta compras',
            'submodulo' => 'Compras',
            'permiso' => 'Compras',
            'icono' => 'fas fa-search-dollar',
            'menu' => true
        ],
        [
            'url' => 'compras-proveedor',
            'modulo' => 'Consulta compras',
            'submodulo' => 'Compras por proveedor',
            'permiso' => 'Compras por proveedor',
            'icono' => 'fas fa-truck-loading',
            'menu' => true
        ],

        // CONSULTA VENTAS
        [
            'url' => 'ventas-cliente',
            'modulo' => 'Consultar ventas',
            'submodulo' => 'Ventas por cliente',
            'permiso' => 'Ventas por cliente',
            'icono' => 'fas fa-user-tag',
            'menu' => true
        ],
        [
            'url' => 'ventas-vendedor',
            'modulo' => 'Consultar ventas',
            'submodulo' => 'Ventas por vendedor',
            'permiso' => 'Ventas por vendedor',
            'icono' => 'fas fa-user-tie',
            'menu' => true
        ],
        [
            'url' => 'ventas-producto',
            'modulo' => 'Consultar ventas',
            'submodulo' => 'Ventas - utilidades',
            'permiso' => 'Ventas - utilidades',
            'icono' => 'fas fa-chart-line',
            'menu' => true
        ],
        [
            'url' => 'ventas-credito',
            'modulo' => 'Consultar ventas',
            'submodulo' => 'Creditos - utilidades',
            'permiso' => 'Creditos - utilidades',
            'icono' => 'fas fa-credit-card',
            'menu' => true
        ],
        [
            'url' => 'reporte',
            'modulo' => 'Consultar ventas',
            'submodulo' => 'Reporte consolidado',
            'permiso' => 'Reporte consolidado',
            'icono' => 'fas fa-chart-bar',
            'menu' => true
        ],
        [
            'url' => 'ventas-servicio',
            'modulo' => 'Consultar ventas',
            'submodulo' => 'Ventas por servicio',
            'permiso' => 'Ventas por servicio',
            'icono' => 'fas fa-concierge-bell',
            'menu' => true
        ],
        [
            'url' => 'detalle-venta-comprobante',
            'modulo' => 'Consultar ventas',
            'submodulo' => 'Ventas detalle',
            'permiso' => 'Ventas detalle',
            'icono' => 'fas fa-list-alt',
            'menu' => true
        ],

        // RUTAS DEL SISTEMA
        [
            'url' => 'recuperar',
            'modulo' => null,
            'submodulo' => null,
            'permiso' => null,
            'icono' => null,
            'menu' => false
        ],
        [
            'url' => 'reset',
            'modulo' => null,
            'submodulo' => null,
            'permiso' => null,
            'icono' => null,
            'menu' => false
        ],
        [
            'url' => 'salirsucursal',
            'modulo' => null,
            'submodulo' => null,
            'permiso' => null,
            'icono' => null,
            'menu' => false
        ],
        [
            'url' => 'salir',
            'modulo' => null,
            'submodulo' => null,
            'permiso' => null,
            'icono' => null,
            'menu' => false
        ],
        [
            'url' => 'restaurant',
            'modulo' => null,
            'submodulo' => null,
            'permiso' => null,
            'icono' => null,
            'menu' => false
        ],
        [
            'url' => 'pos',
            'modulo' => null,
            'submodulo' => null,
            'permiso' => null,
            'icono' => null,
            'menu' => false
        ],
        [
            'url' => 'service',
            'modulo' => null,
            'submodulo' => null,
            'permiso' => null,
            'icono' => null,
            'menu' => false
        ],
        [
            'url' => 'caja-chica2',
            'modulo' => null,
            'submodulo' => null,
            'permiso' => null,
            'icono' => null,
            'menu' => false
        ],
        [
            'url' => 'configuracion',
            'modulo' => null,
            'submodulo' => null,
            'permiso' => 'Configuracion',
            'icono' => null,
            'menu' => false
        ],
    ];

    public static function obtener(string $url): ?array
    {
        foreach (self::LISTA as $ruta) {
            if ($ruta['url'] === $url) {
                return $ruta;
            }
        }

        return null;
    }

    public static function obtenerPorModulo(?string $modulo): array
    {
        return array_values(
            array_filter(
                self::LISTA,
                fn($ruta) =>
                    $ruta['menu'] === true &&
                    $ruta['modulo'] === $modulo
            )
        );
    }

    public static function obtenerRutasMenu(): array
    {
        return array_values(
            array_filter(
                self::LISTA,
                fn($ruta) => $ruta['menu'] === true
            )
        );
    }
}