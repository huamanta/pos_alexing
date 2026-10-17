INSERT INTO modulos
(id, nombre, ruta, icono, modulo_padre_id, orden, menu, estado)
VALUES
-- MÓDULOS PRINCIPALES
(1, 'Inicio', 'inicio', 'fas fa-home', NULL, 1, 1, 1),
(2, 'Clientes', 'cliente', 'fas fa-users', NULL, 2, 1, 1),
(3, 'Ventas', NULL, 'fas fa-shopping-bag', NULL, 3, 1, 1),
(4, 'Resumen caja', 'caja-chica', 'fas fa-wallet', NULL, 4, 1, 1),
(5, 'Facturacion y cajas', NULL, 'fas fa-store-alt', NULL, 5, 1, 1),
(6, 'Almacen', NULL, 'fas fa-boxes', NULL, 6, 1, 1),
(7, 'Inventario', NULL, 'fas fa-clipboard-list', NULL, 7, 1, 1),
(8, 'Compras', NULL, 'fas fa-dolly', NULL, 8, 1, 1),
(9, 'Cobros', NULL, 'fas fa-hand-holding-usd', NULL, 9, 1, 1),
(10, 'Taller', NULL, 'fas fa-tools', NULL, 10, 1, 1),
(11, 'Personal', NULL, 'fas fa-users-cog', NULL, 11, 1, 1),
(12, 'Configuracion', NULL, 'fas fa-cog', NULL, 12, 1, 1),
(13, 'Consulta compras', NULL, 'fas fa-shopping-cart', NULL, 13, 1, 1),
(14, 'Consultar ventas', NULL, 'fas fa-chart-line', NULL, 14, 1, 1),

-- VENTAS: PADRE ID 3
(15, 'Contratos', 'contrato', 'fas fa-file-contract', 3, 1, 1, 1),
(16, 'Solicitudes', 'solicitudes', 'fas fa-file-signature', 3, 2, 1, 1),
(17, 'Punto de venta', 'venta-pos', 'fas fa-cash-register', 3, 3, 1, 1),
(18, 'Guía de remisión', 'guia', 'fas fa-truck', 3, 4, 1, 1),
(19, 'Cotizaciones', 'cotizacion', 'fas fa-file-invoice-dollar', 3, 5, 1, 1),
(20, 'Notas de crédito', 'nota-credito', 'fas fa-file-invoice', 3, 6, 1, 1),

-- FACTURACIÓN Y CAJAS: PADRE ID 5
(21, 'Comprobantes', 'venta', 'fas fa-file-invoice', 5, 1, 1, 1),
(22, 'Resumen diario', 'resumen', 'fas fa-calendar-day', 5, 2, 1, 1),
(23, 'Bancos', 'bancos', 'fas fa-university', 5, 3, 1, 1),
(24, 'Conceptos', 'conceptos', 'fas fa-list', 5, 4, 1, 1),
(25, 'Movimientos', 'movimientos', 'fas fa-exchange-alt', 5, 5, 1, 1),
(26, 'Cajas', 'cajas', 'fas fa-cash-register', 5, 6, 1, 1),

-- ALMACÉN: PADRE ID 6
(27, 'Productos', 'producto', 'fas fa-box', 6, 1, 1, 1),
(28, 'Servicios', 'servicio', 'fas fa-concierge-bell', 6, 2, 1, 1),
(29, 'Nombres Precios', 'nombres-precios', 'fas fa-tags', 6, 3, 1, 1),
(30, 'Categorias', 'categoria', 'fas fa-sitemap', 6, 4, 1, 1),
(31, 'Marcas', 'marca', 'fas fa-copyright', 6, 5, 1, 1),
(32, 'Modelos', 'modelo', 'fas fa-cubes', 6, 6, 1, 1),
(33, 'Lineas', 'rubro', 'fas fa-layer-group', 6, 7, 1, 1),
(34, 'Condicion de venta', 'condicionventa', 'fas fa-handshake', 6, 8, 1, 1),
(35, 'Unidad de medida', 'unidad-medida', 'fas fa-ruler-combined', 6, 9, 1, 1),
(36, 'Traslados', 'traslado', 'fas fa-truck-loading', 6, 10, 1, 1),
(37, 'Reportes', 'reportes-digemid', 'fas fa-chart-pie', 6, 11, 1, 1),
(38, 'Vencimientos', 'reportes-vencimiento', 'fas fa-calendar-times', 6, 12, 1, 1),

-- INVENTARIO: PADRE ID 7
(39, 'Kardex', 'kardex', 'fas fa-book', 7, 1, 1, 1),
(40, 'Toma de inventario', 'toma-inventario', 'fas fa-clipboard-check', 7, 2, 1, 1),
(41, 'Ajuste de inventario', 'ajuste-inventario', 'fas fa-sliders-h', 7, 3, 1, 1),

-- COMPRAS: PADRE ID 8
(42, 'Crear compras', 'compra', 'fas fa-cart-plus', 8, 1, 1, 1),
(43, 'Proveedores', 'proveedor', 'fas fa-truck', 8, 2, 1, 1),
(44, 'Cuentas por pagar', 'cuentasxpagar', 'fas fa-file-invoice-dollar', 8, 3, 1, 1),

-- COBROS: PADRE ID 9
(45, 'Cuentas por cobrar', 'cuentas-cobrar', 'fas fa-money-bill-wave', 9, 1, 1, 1),
(46, 'Refinanciar creditos', 'refinanciamientos', 'fas fa-sync-alt', 9, 2, 1, 1),
(47, 'Recuperacion de vehiculos', 'recuperacion-vehiculos', 'fas fa-car', 9, 3, 1, 1),

-- TALLER: PADRE ID 10
(48, 'Orden de trabajo', 'orden-trabajo', 'fas fa-tools', 10, 1, 1, 1),

-- PERSONAL: PADRE ID 11
(49, 'Asistencia', 'asistencia', 'fas fa-user-clock', 11, 1, 1, 1),
(50, 'Colaboradores', 'personal', 'fas fa-users', 11, 2, 1, 1),
(51, 'Usuarios', 'usuario', 'fas fa-user-cog', 11, 3, 1, 1),
(52, 'Permisos', 'permiso', 'fas fa-user-shield', 11, 4, 1, 1),
(53, 'Roles', 'roles', 'fas fa-user-tag', 11, 5, 1, 1),

-- CONFIGURACIÓN: PADRE ID 12
(54, 'Datos generales', 'negocio', 'fas fa-building', 12, 1, 1, 1),
(55, 'Facturadores', 'empresas', 'fas fa-file-signature', 12, 2, 1, 1),
(56, 'Sucursales', 'sucursal', 'fas fa-store', 12, 3, 1, 1),

-- CONSULTA COMPRAS: PADRE ID 13
(57, 'Compras', 'compras-fecha', 'fas fa-search-dollar', 13, 1, 1, 1),
(58, 'Compras por proveedor', 'compras-proveedor', 'fas fa-truck-loading', 13, 2, 1, 1),

-- CONSULTAR VENTAS: PADRE ID 14
(59, 'Ventas por cliente', 'ventas-cliente', 'fas fa-user-tag', 14, 1, 1, 1),
(60, 'Ventas por vendedor', 'ventas-vendedor', 'fas fa-user-tie', 14, 2, 1, 1),
(61, 'Ventas - utilidades', 'ventas-producto', 'fas fa-chart-line', 14, 3, 1, 1),
(62, 'Creditos - utilidades', 'ventas-credito', 'fas fa-credit-card', 14, 4, 1, 1),
(63, 'Reporte consolidado', 'reporte', 'fas fa-chart-bar', 14, 5, 1, 1),
(64, 'Ventas por servicio', 'ventas-servicio', 'fas fa-concierge-bell', 14, 6, 1, 1),
(65, 'Ventas detalle', 'detalle-venta-comprobante', 'fas fa-list-alt', 14, 7, 1, 1),

-- RUTAS INTERNAS DEL SISTEMA
(66, NULL, 'recuperar', NULL, NULL, 1, 0, 1),
(67, NULL, 'reset', NULL, NULL, 2, 0, 1),
(68, NULL, 'salirsucursal', NULL, NULL, 3, 0, 1),
(69, NULL, 'salir', NULL, NULL, 4, 0, 1),
(70, NULL, 'restaurant', NULL, NULL, 5, 0, 1),
(71, NULL, 'pos', NULL, NULL, 6, 0, 1),
(72, NULL, 'service', NULL, NULL, 7, 0, 1),
(73, NULL, 'caja-chica2', NULL, NULL, 8, 0, 1),
(74, 'Configuracion general', 'configuraciongeneral', NULL, NULL, 9, 0, 1);