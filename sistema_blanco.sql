-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-07-2026 a las 05:21:39
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_blanco`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accion_permiso`
--

CREATE TABLE `accion_permiso` (
  `idaccion_permiso` int(11) NOT NULL,
  `idsubpermiso` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `accion_permiso`
--

INSERT INTO `accion_permiso` (`idaccion_permiso`, `idsubpermiso`, `nombre`, `descripcion`) VALUES
(2, 13, 'Editar productos', ''),
(3, 13, 'Movimientos productos', ''),
(4, 13, 'Configurar productos', ''),
(6, 13, 'Listar vencimientos', ''),
(7, 13, 'Desactivar productos', ''),
(8, 13, 'Eliminar productos', ''),
(9, 3, 'Editar', ''),
(10, 3, 'Eliminar', ''),
(11, 29, 'Amortizar deuda', ''),
(12, 29, 'Crear abono', ''),
(13, 29, 'Programar visita', ''),
(14, 29, 'Programar compromiso de pago', ''),
(15, 29, 'Ver abonos', ''),
(16, 8, 'Puede ver calendario', ''),
(17, 16, 'Crear categoria', ''),
(18, 20, 'Crear condicion venta', ''),
(19, 8, 'Editar interes credito', ''),
(20, 17, 'Crear marca', ''),
(21, 18, 'Crear modelo', ''),
(22, 15, 'Crear precio', ''),
(23, 3, 'Crear nota de venta', ''),
(24, 3, 'Crear boleta', ''),
(25, 3, 'Crear factura', ''),
(26, 13, 'Crear producto', ''),
(27, 13, 'Catalago', ''),
(28, 13, 'Traslados', ''),
(29, 13, 'Empaque', ''),
(30, 13, 'Inversion por producto', ''),
(31, 13, 'Filtrar Stock', ''),
(32, 13, 'Consultar producto sucursal', ''),
(33, 12, 'Cerrar caja', ''),
(34, 12, 'Aperturar caja', ''),
(35, 12, 'Crear caja', ''),
(36, 2, 'Crear solicitud', ''),
(37, 2, 'Puede realizar evaluación Inicial', ''),
(38, 2, 'puede realizar validacion documentaria', ''),
(39, 2, 'Puede realizar verificacion domiciliaria', ''),
(40, 2, 'Puede realizar comité de crédito', ''),
(41, 2, 'Puede realizar aprobación final', ''),
(42, 49, 'Agregar cliente', ''),
(43, 49, 'Editar cliente', ''),
(44, 49, 'Historial cliente', ''),
(45, 49, 'Puntuacion cliente', ''),
(46, 49, 'Eliminar cliente', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ajuste_inventario`
--

CREATE TABLE `ajuste_inventario` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_ajuste` datetime NOT NULL,
  `numero` varchar(10) NOT NULL,
  `serie` varchar(10) NOT NULL,
  `observacion` varchar(200) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `idasistencia` int(11) NOT NULL,
  `idpersonal` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_entrada` time NOT NULL,
  `hora_salida` time NOT NULL,
  `estado` enum('asistio','falto') NOT NULL DEFAULT 'asistio',
  `tardanza` time DEFAULT NULL,
  `permiso` enum('si','no') DEFAULT 'no',
  `vacaciones` enum('si','no') DEFAULT 'no',
  `estado_pago` tinyint(1) DEFAULT 0,
  `monto` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

CREATE TABLE `cajas` (
  `idcaja` int(11) NOT NULL,
  `cretaed_at` datetime NOT NULL DEFAULT current_timestamp(),
  `nombre` varchar(100) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `estado` varchar(1) NOT NULL DEFAULT '1',
  `idsucursal` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `cajas`
--

INSERT INTO `cajas` (`idcaja`, `cretaed_at`, `nombre`, `numero`, `estado`, `idsucursal`, `deleted_at`) VALUES
(1, '2026-07-01 23:21:58', 'Caja 1', '1', '2', 1, NULL),
(2, '2026-07-15 10:09:55', 'Caja Principal', '1', '2', 2, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_apertura`
--

CREATE TABLE `caja_apertura` (
  `aperturacajaid` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_apertura` datetime NOT NULL,
  `efectivo_apertura` double NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `efectivo_cierre` double DEFAULT NULL,
  `efectivo_cierre_real` decimal(10,2) DEFAULT NULL,
  `estado` varchar(1) NOT NULL DEFAULT '1',
  `idcaja` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idusuario_cierre` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `caja_apertura`
--

INSERT INTO `caja_apertura` (`aperturacajaid`, `created_at`, `fecha_apertura`, `efectivo_apertura`, `fecha_cierre`, `efectivo_cierre`, `efectivo_cierre_real`, `estado`, `idcaja`, `idsucursal`, `idusuario`, `idusuario_cierre`, `deleted_at`) VALUES
(1, '2026-07-01 23:22:11', '2026-07-01 23:22:11', 100, '2026-07-08 20:53:11', 7942.09, 7942.09, '0', 1, 1, 1, 1, NULL),
(2, '2026-07-08 20:53:32', '2026-07-08 20:53:32', 50, NULL, NULL, NULL, '1', 1, 1, 1, NULL, NULL),
(3, '2026-07-15 10:10:21', '2026-07-15 10:10:21', 100, NULL, NULL, NULL, '1', 2, 2, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogo_imagen`
--

CREATE TABLE `catalogo_imagen` (
  `idcatalogo_imagen` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `nombre_imagen` varchar(255) NOT NULL,
  `orden` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `idcategoria` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `condicion` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`idcategoria`, `nombre`, `condicion`) VALUES
(1, 'MOTOS', 1),
(2, 'VEHICULO', 1),
(3, 'REPUESTOS', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `idcompra` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `idproveedor` int(11) NOT NULL,
  `idpersonal` int(11) NOT NULL,
  `tipo_comprobante` varchar(20) NOT NULL,
  `serie_comprobante` varchar(7) DEFAULT NULL,
  `num_comprobante` varchar(10) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `impuesto` decimal(4,2) NOT NULL,
  `tipo_igv` varchar(20) DEFAULT 'EXONERADA',
  `monto_gravado` decimal(11,2) DEFAULT 0.00,
  `monto_exonerado` decimal(11,2) DEFAULT 0.00,
  `monto_igv` decimal(11,2) DEFAULT 0.00,
  `total_compra` decimal(11,2) NOT NULL,
  `compracredito` varchar(10) NOT NULL,
  `motoPagado` float NOT NULL,
  `totaldeposito` double NOT NULL,
  `totalrecibido` float NOT NULL,
  `noperacion` varchar(20) NOT NULL,
  `fecha_deposito` datetime DEFAULT NULL,
  `estado` varchar(20) NOT NULL,
  `fecha_kardex` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_c` varchar(250) DEFAULT NULL,
  `estadoC` varchar(50) DEFAULT NULL,
  `documento_rel` varchar(20) DEFAULT NULL,
  `formapago` varchar(250) DEFAULT NULL,
  `lugar_entrega` varchar(250) DEFAULT NULL,
  `motivo_compra` varchar(250) DEFAULT NULL,
  `documento` varchar(250) DEFAULT NULL,
  `nota` text DEFAULT NULL,
  `imagen` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `compra`
--

INSERT INTO `compra` (`idcompra`, `idsucursal`, `idproveedor`, `idpersonal`, `tipo_comprobante`, `serie_comprobante`, `num_comprobante`, `fecha_hora`, `impuesto`, `tipo_igv`, `monto_gravado`, `monto_exonerado`, `monto_igv`, `total_compra`, `compracredito`, `motoPagado`, `totaldeposito`, `totalrecibido`, `noperacion`, `fecha_deposito`, `estado`, `fecha_kardex`, `tipo_c`, `estadoC`, `documento_rel`, `formapago`, `lugar_entrega`, `motivo_compra`, `documento`, `nota`, `imagen`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-23 23:45:50', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 18:45:50', '2026-07-23 18:45:50', NULL),
(2, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-23 23:47:49', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 18:47:49', '2026-07-23 18:47:49', NULL),
(3, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-23 23:48:27', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 18:48:27', '2026-07-23 18:48:27', NULL),
(4, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-23 23:48:34', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 18:48:34', '2026-07-23 18:48:34', NULL),
(5, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-23 23:50:51', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 18:50:51', '2026-07-23 18:50:51', NULL),
(6, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-23 23:50:52', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 18:50:52', '2026-07-23 18:50:52', NULL),
(7, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-23 23:50:54', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 18:50:54', '2026-07-23 18:50:54', NULL),
(8, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-23 23:53:35', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 18:53:35', '2026-07-23 18:53:35', NULL),
(9, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-23 23:55:48', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 18:55:48', '2026-07-23 18:55:48', NULL),
(14, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-24 00:09:31', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 19:09:31', '2026-07-23 19:09:31', NULL),
(15, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-24 00:13:16', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 19:13:16', '2026-07-23 19:13:16', NULL),
(16, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-24 00:15:15', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 19:15:15', '2026-07-23 19:15:15', NULL),
(17, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-24 00:16:32', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 19:16:32', '2026-07-23 19:16:32', NULL),
(18, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-24 00:18:12', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 19:18:12', '2026-07-23 19:18:12', NULL),
(19, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-24 00:19:38', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 19:19:38', '2026-07-23 19:19:38', NULL),
(20, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-24 00:21:18', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 19:21:18', '2026-07-23 19:21:18', NULL),
(21, 1, 17, 1, 'Boleta', 'B001', '00076', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-24 00:22:23', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 19:22:23', '2026-07-23 19:22:23', NULL),
(22, 1, 17, 1, 'Boleta', 'B001', '00043', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 5.00, 0.00, 5.00, 'No', 0, 0, 5, '', NULL, 'REGISTRADO', '2026-07-24 00:24:46', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 19:24:46', '2026-07-23 19:24:46', NULL),
(23, 1, 17, 1, 'Boleta', 'B001', '00056', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 50.00, 0.00, 50.00, 'Si', 0, 0, 50, '', NULL, 'REGISTRADO', '2026-07-24 04:42:03', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 23:42:03', '2026-07-23 23:42:03', NULL),
(24, 1, 17, 1, 'Boleta', 'B001', '00056', '2026-07-23 00:00:00', 0.00, 'EXONERADA', 0.00, 50.00, 0.00, 50.00, 'Si', 0, 0, 50, '', NULL, 'REGISTRADO', '2026-07-24 04:43:04', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-23 23:43:04', '2026-07-23 23:43:04', NULL),
(25, 1, 17, 1, 'Boleta', 'B002', '00045', '2026-07-27 00:00:00', 0.00, 'EXONERADA', 0.00, 4005.00, 0.00, 4005.00, 'No', 0, 0, 4005, '', NULL, 'REGISTRADO', '2026-07-28 02:40:40', 'Compra', 'REGISTRADO', '', '', '', '', '', '', NULL, '2026-07-27 21:40:40', '2026-07-27 21:40:40', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_pago`
--

CREATE TABLE `compra_pago` (
  `idpago` int(11) NOT NULL,
  `idcompra` int(11) NOT NULL,
  `tipo_pago` varchar(50) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `nro_operacion` varchar(50) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT current_timestamp(),
  `banco` varchar(50) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `compra_pago`
--

INSERT INTO `compra_pago` (`idpago`, `idcompra`, `tipo_pago`, `monto`, `nro_operacion`, `fecha_pago`, `banco`, `observacion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 21, 'Efectivo', 5.00, NULL, '2026-07-23 19:22:23', NULL, NULL, '2026-07-23 19:22:23', '2026-07-23 19:22:23', NULL),
(2, 22, 'Efectivo', 5.00, NULL, '2026-07-23 19:24:46', NULL, NULL, '2026-07-23 19:24:46', '2026-07-23 19:24:46', NULL),
(3, 24, 'Efectivo', 50.00, NULL, '2026-07-23 23:43:04', NULL, NULL, '2026-07-23 23:43:04', '2026-07-23 23:43:04', NULL),
(4, 25, 'Efectivo', 4005.00, NULL, '2026-07-27 21:40:40', NULL, NULL, '2026-07-27 21:40:40', '2026-07-27 21:40:40', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compromiso_pago`
--

CREATE TABLE `compromiso_pago` (
  `idcompromiso_pago` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `idcpc` int(11) NOT NULL,
  `fecha_compromiso` date NOT NULL,
  `detalle` varchar(500) DEFAULT NULL,
  `monto` double NOT NULL,
  `fecha_cumplimiento` datetime DEFAULT NULL,
  `observacion` varchar(255) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `compromiso_pago`
--

INSERT INTO `compromiso_pago` (`idcompromiso_pago`, `created_at`, `updated_at`, `idcpc`, `fecha_compromiso`, `detalle`, `monto`, `fecha_cumplimiento`, `observacion`, `idusuario`, `deleted_at`) VALUES
(1, '2026-07-18 08:57:31', NULL, 20, '2026-07-20', NULL, 470, NULL, 'Cliente se compromete a pagar mañana', 1, NULL),
(2, '2026-07-18 09:07:12', NULL, 20, '2026-07-22', 'Compromiso de pago de la cuota 2 del cliente  para el 22/07/2026 por un monto de S/ 470.00.', 470, NULL, 'sagshsh', 1, NULL),
(3, '2026-07-18 09:07:19', NULL, 20, '2026-07-22', 'Compromiso de pago de la cuota 2 del cliente  para el 22/07/2026 por un monto de S/ 470.00.', 470, NULL, 'sagshsh', 1, NULL),
(4, '2026-07-18 09:14:13', NULL, 20, '2026-07-22', 'Compromiso de pago de la cuota 2 del cliente ALIS HUAMANTA EDQUEN para el 22/07/2026 por un monto de S/ 470.00.', 470, '2026-07-02 09:43:16', 'sagshsh', 1, NULL),
(5, '2026-07-19 09:50:29', NULL, 20, '2026-07-31', 'Compromiso de pago de la cuota 2 del cliente ALIS HUAMANTA EDQUEN para el 31/07/2026 por un monto de S/ 470.00.', 470, NULL, 'Saaaaa', 1, '2026-07-19 15:41:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comp_pago`
--

CREATE TABLE `comp_pago` (
  `id_comp_pago` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `serie_comprobante` varchar(4) NOT NULL,
  `num_comprobante` varchar(7) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `condicion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `comp_pago`
--

INSERT INTO `comp_pago` (`id_comp_pago`, `nombre`, `serie_comprobante`, `num_comprobante`, `idempresa`, `condicion`) VALUES
(1, 'Nota de Venta', 'NV00', '0', 1, 1),
(2, 'Factura', 'F001', '0', 1, 1),
(3, 'Boleta', 'B001', '0', 1, 1),
(4, 'Nota de Crédito', 'NC01', '0', 1, 1),
(5, 'Nota de Débito', 'ND01', '0', 1, 1),
(6, 'Cotización', 'COT0', '0', 1, 1),
(7, 'Orden de Compra', 'OC01', '0', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `concepto_ajuste`
--

CREATE TABLE `concepto_ajuste` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('entrada','salida') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `concepto_movimiento`
--

CREATE TABLE `concepto_movimiento` (
  `idconcepto_movimiento` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `descripcion` varchar(255) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `categoria_concepto` varchar(100) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `concepto_movimiento`
--

INSERT INTO `concepto_movimiento` (`idconcepto_movimiento`, `created_at`, `updated_at`, `descripcion`, `tipo`, `estado`, `categoria_concepto`, `deleted_at`) VALUES
(1, '2026-07-19 09:16:14', '2026-07-19 09:16:14', 'Adelanto de sueldo', 'egresos', 1, 'personal', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `condicionventa`
--

CREATE TABLE `condicionventa` (
  `idcondicionventa` int(11) NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `condicion` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `condicionventa`
--

INSERT INTO `condicionventa` (`idcondicionventa`, `nombre`, `condicion`) VALUES
(1, 'NUEVO', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `clave` varchar(255) NOT NULL,
  `valor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion`
--

CREATE TABLE `cotizacion` (
  `idcotizacion` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `idPersonal` int(11) NOT NULL,
  `tipo_comprobante` varchar(20) NOT NULL,
  `serie_comprobante` varchar(7) DEFAULT NULL,
  `num_comprobante` varchar(10) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `total_venta` decimal(11,2) NOT NULL,
  `descuento` double DEFAULT NULL,
  `condicion` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_h` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_aprobacion` datetime DEFAULT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `nota` varchar(350) DEFAULT NULL,
  `saludo` varchar(350) DEFAULT NULL,
  `formapago` varchar(250) DEFAULT NULL,
  `Inicial` varchar(20) DEFAULT NULL,
  `frecuencia` varchar(20) DEFAULT NULL,
  `meses` int(11) DEFAULT NULL,
  `interes` int(11) DEFAULT NULL,
  `tiempo_pro` varchar(250) DEFAULT NULL,
  `igv` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cotizacion`
--

INSERT INTO `cotizacion` (`idcotizacion`, `idsucursal`, `idcliente`, `idPersonal`, `tipo_comprobante`, `serie_comprobante`, `num_comprobante`, `fecha_hora`, `total_venta`, `descuento`, `condicion`, `fecha_h`, `fecha_aprobacion`, `titulo`, `nota`, `saludo`, `formapago`, `Inicial`, `frecuencia`, `meses`, `interes`, `tiempo_pro`, `igv`, `estado`, `observacion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 3, 1, 'Cotización', '001', '000001', '2026-07-01 00:00:00', 7000.00, NULL, 1, '2026-07-01 23:20:52', '2026-07-02 06:27:10', '', '15 Días calendario', '', 'Si', '500', '4', 18, 10, '', '', 'VENDIDO', '', '2026-07-15 07:57:53', NULL, NULL),
(2, 1, 3, 1, 'Cotización', '001', '000002', '2026-07-05 00:00:00', 7000.00, NULL, 1, '2026-07-05 23:31:13', '2026-07-09 04:43:41', '', '7 Días calendario', '', 'Si', '0', '3', 5, 10, '', '', 'VENDIDO', '', '2026-07-15 07:57:53', NULL, NULL),
(7, 2, 15, 1, '', '001', '000001', '2026-07-15 00:00:00', 4000.00, NULL, 1, '2026-07-15 09:05:02', '2026-07-15 17:05:00', '', '7', '', 'Si', '0', '4', 12, 10, '', '', 'VENDIDO', '', '2026-07-15 09:05:02', '2026-07-15 14:35:29', NULL),
(8, 2, 1, 1, '', '001', '000002', '2026-07-15 00:00:00', 9000.00, NULL, 1, '2026-07-15 09:41:53', NULL, '', '7', '', 'No', NULL, NULL, NULL, 10, '', '', 'EN ESPERA', '', '2026-07-15 09:41:53', '2026-07-15 09:41:53', NULL),
(12, 1, 19, 1, 'Cotización', '001', '000003', '2026-07-28 00:00:00', 50.00, NULL, 1, '2026-07-28 09:04:48', NULL, '', '7', '', 'No', NULL, NULL, NULL, 10, '', '', 'EN ESPERA', '', '2026-07-28 09:04:48', '2026-07-28 09:04:48', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_por_cobrar`
--

CREATE TABLE `cuentas_por_cobrar` (
  `idcpc` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `fecharegistro` datetime NOT NULL,
  `deudatotal` double NOT NULL,
  `deuda_base` decimal(12,2) NOT NULL DEFAULT 0.00,
  `mora` decimal(12,2) NOT NULL DEFAULT 0.00,
  `mora_pagada` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fecha_update_mora` date DEFAULT NULL,
  `fechavencimiento` date NOT NULL,
  `deuda` double DEFAULT NULL,
  `interes` double DEFAULT NULL,
  `descuento` decimal(10,2) DEFAULT NULL,
  `abonototal` decimal(11,2) NOT NULL,
  `condicion` tinyint(4) NOT NULL DEFAULT 1,
  `nota` varchar(1000) DEFAULT NULL,
  `fecha_hora` timestamp NULL DEFAULT NULL,
  `estado_pago` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = pendiente, 0 = pagado',
  `idrefinanciamiento` int(11) DEFAULT NULL,
  `estado_plan` tinyint(4) DEFAULT 1,
  `idrefinanciamiento_origen` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cuentas_por_cobrar`
--

INSERT INTO `cuentas_por_cobrar` (`idcpc`, `idventa`, `fecharegistro`, `deudatotal`, `deuda_base`, `mora`, `mora_pagada`, `fecha_update_mora`, `fechavencimiento`, `deuda`, `interes`, `descuento`, `abonototal`, `condicion`, `nota`, `fecha_hora`, `estado_pago`, `idrefinanciamiento`, `estado_plan`, `idrefinanciamiento_origen`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2026-07-30', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', '2026-07-28 22:14:43', NULL),
(2, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2026-08-30', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(3, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2026-09-30', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(4, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 15.89, '2026-07-04', '2026-06-30', 0, 36.11, 0.00, 413.11, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(5, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 139.04, '2026-07-04', '2026-05-30', 0, 36.11, 0.00, 536.26, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(6, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2026-12-30', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(7, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2027-01-30', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(8, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2027-03-02', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(9, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2027-04-02', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(10, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2027-05-02', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(11, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2027-06-02', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(12, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2027-07-02', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(13, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2027-08-02', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(14, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2027-09-02', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(15, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2027-10-02', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(16, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2027-11-02', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(17, 1, '2026-07-01 23:27:36', 397.26, 361.11, 0.00, 0.00, NULL, '2027-12-02', 0, 36.11, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(18, 1, '2026-07-01 23:27:36', 397.26, 361.13, 0.00, 0.00, NULL, '2028-01-02', 0, 36.13, 3.97, 393.29, 1, NULL, NULL, 0, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(19, 2, '2026-07-08 21:50:13', 770, 700.00, 0.00, 0.00, NULL, '2025-07-22', 0, 70, 6.60, 763.40, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:27:55', '2026-07-17 21:38:04', NULL),
(20, 2, '2026-07-08 21:50:13', 770, 700.00, 0.00, 1673.20, '2026-07-28', '2025-08-06', 0, 70, 0.00, 770.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:27:55', '2026-07-28 22:14:43', NULL),
(21, 2, '2026-07-08 21:50:13', 770, 700.00, 2525.70, 100.00, '2026-07-28', '2025-08-21', 770, 70, 0.00, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:27:55', '2026-07-28 22:10:11', NULL),
(22, 2, '2026-07-08 21:50:13', 770, 700.00, 0.00, 0.00, NULL, '2025-09-05', 770, 70, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(23, 2, '2026-07-08 21:50:13', 770, 700.00, 0.00, 0.00, NULL, '2025-09-20', 770, 70, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(24, 2, '2026-07-08 21:50:13', 770, 700.00, 0.00, 0.00, NULL, '2025-10-05', 770, 70, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(25, 2, '2026-07-08 21:50:13', 770, 700.00, 0.00, 0.00, NULL, '2025-10-20', 770, 70, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(26, 2, '2026-07-08 21:50:13', 770, 700.00, 0.00, 0.00, NULL, '2026-11-04', 770, 70, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(27, 2, '2026-07-08 21:50:13', 770, 700.00, 0.00, 0.00, NULL, '2026-11-19', 770, 70, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(28, 2, '2026-07-08 21:50:13', 770, 700.00, 0.00, 0.00, NULL, '2026-12-04', 770, 70, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:27:55', NULL, NULL),
(125, 23, '2026-07-15 14:35:29', 366.66, 333.33, 0.00, 0.00, NULL, '2026-08-14', 366.66, 33.33, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(126, 23, '2026-07-15 14:35:29', 366.66, 333.33, 0.00, 0.00, NULL, '2026-09-14', 366.66, 33.33, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(127, 23, '2026-07-15 14:35:29', 366.66, 333.33, 0.00, 0.00, NULL, '2026-10-14', 366.66, 33.33, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(128, 23, '2026-07-15 14:35:29', 366.66, 333.33, 0.00, 0.00, NULL, '2026-11-14', 366.66, 33.33, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(129, 23, '2026-07-15 14:35:29', 366.66, 333.33, 0.00, 0.00, NULL, '2026-12-14', 366.66, 33.33, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(130, 23, '2026-07-15 14:35:29', 366.66, 333.33, 0.00, 0.00, NULL, '2027-01-14', 366.66, 33.33, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(131, 23, '2026-07-15 14:35:29', 366.66, 333.33, 0.00, 0.00, NULL, '2027-02-14', 366.66, 33.33, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(132, 23, '2026-07-15 14:35:29', 366.66, 333.33, 0.00, 0.00, NULL, '2027-03-14', 366.66, 33.33, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(133, 23, '2026-07-15 14:35:29', 366.66, 333.33, 0.00, 0.00, NULL, '2027-04-14', 366.66, 33.33, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(134, 23, '2026-07-15 14:35:29', 366.66, 333.33, 0.00, 0.00, NULL, '2027-05-14', 366.66, 33.33, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(135, 23, '2026-07-15 14:35:29', 366.66, 333.33, 0.00, 0.00, NULL, '2027-06-14', 366.66, 33.33, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(136, 23, '2026-07-15 14:35:29', 366.74, 333.37, 0.00, 0.00, NULL, '2027-07-14', 366.74, 33.37, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(137, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2025-08-10', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(138, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2025-08-25', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(139, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2025-09-09', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(140, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2025-09-24', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(141, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2025-10-09', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(142, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2025-10-24', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(143, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2025-11-08', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(144, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2025-11-23', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(145, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2025-12-08', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(146, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2025-12-23', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(147, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-01-07', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(148, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-01-22', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(149, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-02-06', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(150, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-02-21', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(151, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-03-08', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(152, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-03-23', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(153, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-04-07', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(154, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-04-22', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(155, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-05-07', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(156, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-05-22', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(157, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-06-06', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(158, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-06-21', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(159, 62, '2025-07-27 17:42:54', 291.67, 291.67, 0.00, 0.00, NULL, '2026-07-06', 291.67, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL),
(160, 62, '2025-07-27 17:42:54', 291.59, 291.59, 0.00, 0.00, NULL, '2026-07-21', 291.59, 0, NULL, 0.00, 1, NULL, NULL, 1, NULL, 1, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_por_pagar`
--

CREATE TABLE `cuentas_por_pagar` (
  `idcpp` int(11) NOT NULL,
  `idcompra` int(11) NOT NULL,
  `fecharegistro` datetime NOT NULL,
  `deudatotal` double NOT NULL,
  `fechavencimiento` date NOT NULL,
  `abonototal` decimal(11,2) NOT NULL DEFAULT 0.00,
  `condicion` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cuentas_por_pagar`
--

INSERT INTO `cuentas_por_pagar` (`idcpp`, `idcompra`, `fecharegistro`, `deudatotal`, `fechavencimiento`, `abonototal`, `condicion`, `fecha_hora`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 24, '2026-07-23 00:00:00', 25, '2026-08-23', 0.00, 1, '2026-07-23 23:43:04', '2026-07-23 23:43:04', '2026-07-23 23:43:04', NULL),
(2, 24, '2026-07-23 00:00:00', 25, '2026-09-23', 0.00, 1, '2026-07-23 23:43:04', '2026-07-23 23:43:04', '2026-07-23 23:43:04', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_negocio`
--

CREATE TABLE `datos_negocio` (
  `id_negocio` int(11) NOT NULL,
  `nombre` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ndocumento` varchar(20) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `direccion` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `logo` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `pais` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `ciudad` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `nombre_impuesto` varchar(10) NOT NULL,
  `monto_impuesto` float(4,2) NOT NULL,
  `moneda` varchar(10) NOT NULL,
  `simbolo` varchar(10) NOT NULL,
  `diasVencer` int(11) DEFAULT NULL,
  `validezcoti` char(3) DEFAULT NULL,
  `usuario_sol` varchar(30) DEFAULT NULL,
  `clave_sol` varchar(30) DEFAULT NULL,
  `estado_certificado` varchar(10) NOT NULL DEFAULT 'BETA',
  `ruta_certificado` varchar(100) DEFAULT NULL,
  `clave_certificado` varchar(50) DEFAULT NULL,
  `condicion` tinyint(4) NOT NULL DEFAULT 1,
  `client_id` varchar(255) DEFAULT NULL,
  `client_secret` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ajuste_inventario`
--

CREATE TABLE `detalle_ajuste_inventario` (
  `id` int(11) NOT NULL,
  `ajuste_inventario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_ajustada` decimal(10,2) NOT NULL,
  `stock_anterior` decimal(10,2) NOT NULL,
  `stock_nuevo` decimal(10,2) NOT NULL,
  `tipo_ajuste` int(11) NOT NULL,
  `concepto_ajuste_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `iddetalle_compra` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `idcompra` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `producto_configuracion_id` int(11) DEFAULT NULL,
  `cantidad` decimal(11,2) NOT NULL,
  `precio_compra` decimal(20,8) NOT NULL,
  `precio_venta` decimal(20,8) NOT NULL,
  `nlote` varchar(20) DEFAULT NULL,
  `fvencimiento` date DEFAULT NULL,
  `tipo_c` varchar(250) DEFAULT NULL,
  `nombre_producto` varchar(250) DEFAULT NULL,
  `stock_lote` decimal(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `detalle_compra`
--

INSERT INTO `detalle_compra` (`iddetalle_compra`, `idsucursal`, `idcompra`, `idproducto`, `producto_configuracion_id`, `cantidad`, `precio_compra`, `precio_venta`, `nlote`, `fvencimiento`, `tipo_c`, `nombre_producto`, `stock_lote`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 3, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 18:48:27', '2026-07-23 18:48:27', NULL),
(2, 1, 4, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 18:48:34', '2026-07-23 18:48:34', NULL),
(3, 1, 5, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 18:50:51', '2026-07-23 18:50:51', NULL),
(4, 1, 6, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 18:50:52', '2026-07-23 18:50:52', NULL),
(5, 1, 7, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 18:50:54', '2026-07-23 18:50:54', NULL),
(6, 1, 8, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 18:53:35', '2026-07-23 18:53:35', NULL),
(7, 1, 9, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 18:55:48', '2026-07-23 18:55:48', NULL),
(12, 1, 14, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 19:09:31', '2026-07-23 19:09:31', NULL),
(13, 1, 15, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 19:13:16', '2026-07-23 19:13:16', NULL),
(14, 1, 16, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 19:15:15', '2026-07-23 19:15:15', NULL),
(15, 1, 17, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 19:16:32', '2026-07-23 19:16:32', NULL),
(16, 1, 18, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 19:18:12', '2026-07-23 19:18:12', NULL),
(17, 1, 19, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 19:19:38', '2026-07-23 19:19:38', NULL),
(18, 1, 20, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 19:21:18', '2026-07-23 19:21:18', NULL),
(19, 1, 21, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 19:22:23', '2026-07-23 19:22:23', NULL),
(20, 1, 22, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-23 19:24:46', '2026-07-23 19:24:46', NULL),
(21, 1, 23, 31, NULL, 10.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 10.00, '2026-07-23 23:42:03', '2026-07-23 23:42:03', NULL),
(22, 1, 24, 31, NULL, 10.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 10.00, '2026-07-23 23:43:04', '2026-07-23 23:43:04', NULL),
(23, 1, 25, 31, NULL, 1.00, 5.00000000, 10.00000000, '', NULL, 'Compra', 'Camara para m300 x undefined', 1.00, '2026-07-27 21:40:40', '2026-07-27 21:40:40', NULL),
(24, 1, 25, 26, NULL, 1.00, 4000.00000000, 6000.00000000, '', NULL, 'Compra', 'GL-125 REACH x undefined', 1.00, '2026-07-27 21:40:40', '2026-07-27 21:40:40', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_cotizacion`
--

CREATE TABLE `detalle_cotizacion` (
  `iddetalle_cotizacion` int(11) NOT NULL,
  `idcotizacion` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `contenedor` varchar(100) NOT NULL,
  `cantidad_contenedor` int(11) NOT NULL,
  `precio_venta` decimal(11,2) NOT NULL,
  `descuento` decimal(11,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `detalle_cotizacion`
--

INSERT INTO `detalle_cotizacion` (`iddetalle_cotizacion`, `idcotizacion`, `idproducto`, `cantidad`, `contenedor`, `cantidad_contenedor`, `precio_venta`, `descuento`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 'UNIDAD', 1, 7000.00, 0.00, '2026-07-15 07:58:28', NULL, NULL),
(2, 2, 1, 1, 'UNIDAD', 1, 7000.00, 0.00, '2026-07-15 07:58:28', NULL, NULL),
(7, 7, 22, 1, 'UNIDAD', 1, 4000.00, 0.00, '2026-07-15 09:05:02', '2026-07-15 09:05:02', NULL),
(8, 8, 14, 1, 'UNIDAD', 1, 9000.00, 0.00, '2026-07-15 09:41:53', '2026-07-15 09:41:53', NULL),
(9, 12, 31, 5, 'UNIDAD', 1, 10.00, 0.00, '2026-07-28 09:04:48', '2026-07-28 09:04:48', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_cuentas_por_cobrar`
--

CREATE TABLE `detalle_cuentas_por_cobrar` (
  `iddcpc` int(11) NOT NULL,
  `idcpc` int(11) NOT NULL,
  `idcaja` int(11) NOT NULL,
  `idpersonal` int(11) NOT NULL,
  `montopagado` decimal(11,2) NOT NULL,
  `montotarjeta` decimal(11,2) NOT NULL,
  `banco` varchar(250) DEFAULT NULL,
  `op` varchar(250) DEFAULT NULL,
  `fechapago` datetime DEFAULT current_timestamp(),
  `formapago` varchar(50) NOT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `detalle_cuentas_por_cobrar`
--

INSERT INTO `detalle_cuentas_por_cobrar` (`iddcpc`, `idcpc`, `idcaja`, `idpersonal`, `montopagado`, `montotarjeta`, `banco`, `op`, `fechapago`, `formapago`, `observacion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(68, 5, 1, 1, 536.30, 0.00, '', 'guardaryeditar', '2026-07-04 23:03:58', 'Efectivo', '', '2026-07-17 21:25:12', NULL, NULL),
(69, 4, 1, 1, 15.89, 0.00, '', 'guardaryeditar', '2026-07-04 23:03:58', 'Efectivo', '', '2026-07-17 21:25:12', NULL, NULL),
(70, 4, 1, 1, 397.26, 0.00, '', 'guardaryeditar', '2026-07-04 23:03:58', 'Efectivo', '', '2026-07-17 21:25:12', NULL, NULL),
(122, 1, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(123, 2, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(124, 3, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(125, 6, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(126, 7, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(127, 8, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(128, 9, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(129, 10, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(130, 11, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(131, 12, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(132, 13, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(133, 14, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(134, 15, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(135, 16, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(136, 17, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(137, 18, 1, 1, 393.29, 0.00, '', '', '2026-07-04 23:51:22', 'Efectivo', 'AMORTIZACION CREDITO', '2026-07-17 21:25:12', NULL, NULL),
(144, 19, 1, 1, 100.00, 10.00, 'BCP', 'guardaryeditar', '2026-07-17 21:11:33', 'Yape', NULL, '2026-07-17 21:25:18', '2026-07-17 21:25:18', NULL),
(145, 19, 1, 1, 653.40, 0.00, NULL, 'guardaryeditar', '2026-07-17 21:37:31', 'Efectivo', NULL, '2026-07-17 21:38:04', '2026-07-17 21:38:04', NULL),
(146, 20, 1, 1, 100.00, 0.00, NULL, 'guardaryeditar', '2026-07-17 23:00:00', 'Efectivo', NULL, '2026-07-17 23:00:13', '2026-07-17 23:00:13', NULL),
(147, 20, 1, 1, 100.00, 0.00, NULL, 'guardaryeditar', '2026-07-17 23:04:03', 'Efectivo', NULL, '2026-07-17 23:04:17', '2026-07-17 23:04:17', NULL),
(148, 20, 1, 1, 100.00, 0.00, NULL, 'guardaryeditar', '2026-07-17 23:06:16', 'Efectivo', NULL, '2026-07-17 23:06:35', '2026-07-17 23:06:35', NULL),
(149, 20, 1, 1, 0.00, 2143.20, 'INTERBANK', 'guardaryeditar', '2026-07-28 21:55:16', 'Plin', NULL, '2026-07-28 21:57:12', '2026-07-28 21:57:12', NULL),
(150, 21, 1, 1, 0.00, 100.00, NULL, 'guardaryeditar', '2026-07-28 22:09:32', 'Yape', NULL, '2026-07-28 22:10:11', '2026-07-28 22:10:11', NULL),
(151, 20, 1, 1, 0.00, 100.00, 'INTERBANK', 'guardaryeditar', '2026-07-28 22:09:32', 'Transferencia', NULL, '2026-07-28 22:14:22', '2026-07-28 22:14:22', NULL),
(152, 20, 1, 1, 0.00, 100.00, 'BCP', 'guardaryeditar', '2026-07-28 22:09:32', 'Transferencia', NULL, '2026-07-28 22:14:43', '2026-07-28 22:14:43', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_cuentas_por_pagar`
--

CREATE TABLE `detalle_cuentas_por_pagar` (
  `iddcpp` int(11) NOT NULL,
  `idcpp` int(11) NOT NULL,
  `idcaja` int(11) NOT NULL,
  `idpersonal` int(11) NOT NULL,
  `montopagado` decimal(11,2) NOT NULL,
  `montotarjeta` decimal(11,2) NOT NULL,
  `banco` varchar(250) DEFAULT NULL,
  `op` varchar(250) DEFAULT NULL,
  `fechapago` datetime DEFAULT current_timestamp(),
  `formapago` varchar(50) NOT NULL,
  `observacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_guia`
--

CREATE TABLE `detalle_guia` (
  `iddetalle` int(11) NOT NULL,
  `idguia` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `nombre_producto` varchar(255) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `unidad` varchar(20) DEFAULT NULL,
  `peso` decimal(10,2) DEFAULT NULL,
  `bultos` int(11) DEFAULT NULL,
  `lotes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_servicio`
--

CREATE TABLE `detalle_servicio` (
  `iddetalle_servicio` int(11) NOT NULL,
  `idservicio` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `cantidad` int(11) DEFAULT 1,
  `precio` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `iddetalle_venta` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `idserie` int(11) DEFAULT NULL,
  `pf` varchar(20) DEFAULT NULL,
  `ph` varchar(20) DEFAULT NULL,
  `ce` varchar(20) DEFAULT NULL,
  `se` varchar(20) DEFAULT NULL,
  `m` varchar(50) DEFAULT NULL,
  `t` varchar(50) DEFAULT NULL,
  `a` varchar(50) DEFAULT NULL,
  `ae` varchar(50) DEFAULT NULL,
  `ton` varchar(50) DEFAULT NULL,
  `r` varchar(50) DEFAULT NULL,
  `k` varchar(50) DEFAULT NULL,
  `mz` varchar(50) DEFAULT NULL,
  `nombre_producto` varchar(250) DEFAULT NULL,
  `cantidad` decimal(11,2) NOT NULL,
  `contenedor` varchar(100) NOT NULL,
  `cantidad_contenedor` decimal(11,2) NOT NULL,
  `precio_venta` decimal(11,2) NOT NULL,
  `descuento` decimal(11,2) NOT NULL,
  `tipo` varchar(150) NOT NULL,
  `check_precio` tinyint(1) DEFAULT 0,
  `id_detalle_compra` int(11) DEFAULT NULL,
  `id_fifo` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`iddetalle_venta`, `idsucursal`, `idventa`, `idproducto`, `idserie`, `pf`, `ph`, `ce`, `se`, `m`, `t`, `a`, `ae`, `ton`, `r`, `k`, `mz`, `nombre_producto`, `cantidad`, `contenedor`, `cantidad_contenedor`, `precio_venta`, `descuento`, `tipo`, `check_precio`, `id_detalle_compra`, `id_fifo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MOTOCICLETA LINEAL HONDA GL 125 (UNIDAD)', 1.00, 'UNIDAD', 1.00, 7000.00, 0.00, 'venta', 0, NULL, 0, '2026-07-15 14:13:59', NULL, NULL),
(2, 1, 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MOTOCICLETA LINEAL HONDA GL 125 (UNIDAD)', 1.00, 'UNIDAD', 1.00, 7000.00, 0.00, 'venta', 0, NULL, 0, '2026-07-15 14:13:59', NULL, NULL),
(14, 2, 23, 22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MOTO WAVE 110 (UNIDAD)', 1.00, 'UNIDAD', 1.00, 4000.00, 0.00, 'venta', 0, NULL, 16, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(32, 1, 49, 24, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MOTOCICLETA LINEAL HONDA GL 125', 1.00, 'UNIDAD', 1.00, 7000.00, 0.00, 'venta', 0, NULL, NULL, '2026-07-16 09:01:35', '2026-07-16 09:01:35', NULL),
(36, 1, 53, 25, 19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MOTO NS 200 BAJAJ', 1.00, 'UNIDAD', 1.00, 11000.00, 0.00, 'venta', 0, NULL, NULL, '2026-07-16 09:15:31', '2026-07-16 09:15:31', NULL),
(40, 1, 57, 26, 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'GL-125 REACH', 1.00, 'UNIDAD', 1.00, 6000.00, 0.00, 'venta', 0, NULL, NULL, '2026-07-16 22:18:01', '2026-07-16 22:18:01', NULL),
(41, 1, 58, 27, 21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MOTO LINEAL NUEV', 1.00, 'UNIDAD', 1.00, 6000.00, 0.00, 'venta', 0, NULL, NULL, '2026-07-16 22:24:49', '2026-07-16 22:24:49', NULL),
(42, 1, 59, 29, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MOTO WAVE 110', 1.00, 'UNIDAD', 1.00, 4000.00, 0.00, 'venta', 0, NULL, NULL, '2026-07-17 19:01:27', '2026-07-17 19:01:27', NULL),
(43, 1, 60, 28, 22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MOTO XR190', 1.00, 'UNIDAD', 1.00, 9000.00, 0.00, 'venta', 0, NULL, NULL, '2026-07-17 19:04:45', '2026-07-17 19:04:45', NULL),
(44, 1, 62, 32, 25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MOTO LINEAL HIUNDAT 200', 1.00, 'UNIDAD', 1.00, 7000.00, 0.00, 'venta', 0, NULL, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentacion`
--

CREATE TABLE `documentacion` (
  `iddocumento` int(11) NOT NULL,
  `fecha_contrato` datetime NOT NULL DEFAULT current_timestamp(),
  `tipo` varchar(5) NOT NULL,
  `correlativo` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `idventa` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `documentacion`
--

INSERT INTO `documentacion` (`iddocumento`, `fecha_contrato`, `tipo`, `correlativo`, `estado`, `idventa`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2026-07-01 23:27:36', '1', 1, 2, 1, '2026-07-15 00:00:00', NULL, NULL),
(2, '2026-07-08 21:50:14', '1', 2, 1, 2, '2026-07-15 00:00:00', NULL, NULL),
(5, '2026-07-15 14:35:29', '1', 3, 1, 23, '2026-07-15 00:00:00', '2026-07-15 14:35:29', NULL),
(6, '2026-07-27 17:42:54', '1', 4, 1, 62, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `idempresa` int(11) NOT NULL,
  `ruc` varchar(11) NOT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `usuario_sol` varchar(50) DEFAULT NULL,
  `clave_sol` varchar(100) DEFAULT NULL,
  `ruta_certificado` varchar(255) DEFAULT NULL,
  `clave_certificado` varchar(100) DEFAULT NULL,
  `estado_certificado` varchar(20) DEFAULT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `client_secret` varchar(255) DEFAULT NULL,
  `nombre_impuesto` varchar(10) DEFAULT NULL,
  `monto_impuesto` float DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`idempresa`, `ruc`, `razon_social`, `usuario_sol`, `clave_sol`, `ruta_certificado`, `clave_certificado`, `estado_certificado`, `client_id`, `client_secret`, `nombre_impuesto`, `monto_impuesto`, `estado`) VALUES
(1, '20152458654', 'Multiservicion conan', '71845256', '1sunTSUmen2', '', '', '', '', '', 'IGV', 18, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guia_remision`
--

CREATE TABLE `guia_remision` (
  `idguia` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `idpersonal` int(11) NOT NULL,
  `serie_comprobante` varchar(10) NOT NULL,
  `num_comprobante` varchar(10) NOT NULL,
  `fecha_emision` datetime NOT NULL,
  `fecha_traslado` date NOT NULL,
  `factura_ref` varchar(20) DEFAULT NULL,
  `fecha_factura_ref` date DEFAULT NULL,
  `tipo_transporte` tinyint(1) DEFAULT 0,
  `idtransportista` int(11) DEFAULT NULL,
  `peso` decimal(10,2) DEFAULT NULL,
  `estado` enum('Por Enviar','Aceptado','Nota Credito','Rechazado') DEFAULT 'Por Enviar',
  `punto_partida` varchar(255) DEFAULT NULL,
  `ubigeo_partida` varchar(6) DEFAULT NULL,
  `punto_llegada` varchar(255) DEFAULT NULL,
  `ubigeo_llegada` varchar(6) DEFAULT NULL,
  `atencion` varchar(100) DEFAULT NULL,
  `referencia` varchar(50) DEFAULT NULL,
  `idtrabajador` int(11) DEFAULT NULL,
  `idmotivo` int(11) DEFAULT NULL,
  `ord_compra` varchar(50) DEFAULT NULL,
  `ord_pedido` varchar(50) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hero_images`
--

CREATE TABLE `hero_images` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `fill_effect` varchar(255) DEFAULT 'none',
  `title` varchar(255) DEFAULT '',
  `subtitle` text DEFAULT NULL,
  `title_animation` varchar(255) DEFAULT 'none',
  `subtitle_animation` varchar(255) DEFAULT 'none',
  `font_title` varchar(100) DEFAULT NULL,
  `font_subtitle` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventarios`
--

CREATE TABLE `inventarios` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_apertura` datetime NOT NULL,
  `observacion_apertura` varchar(200) NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `observacion_cierre` varchar(200) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_detalles`
--

CREATE TABLE `inventario_detalles` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` double NOT NULL,
  `cantidad_real` double NOT NULL,
  `diferencia` double NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 0,
  `fecha_registro` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_producto`
--

CREATE TABLE `inventario_producto` (
  `idinventario` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock_minimo` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock_maximo` decimal(12,2) NOT NULL DEFAULT 0.00,
  `precio_compra` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `inventario_producto`
--

INSERT INTO `inventario_producto` (`idinventario`, `idproducto`, `idsucursal`, `stock`, `stock_minimo`, `stock_maximo`, `precio_compra`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 0.00, 1.00, 10.00, 5000.00, '2026-07-10 22:45:32', '2026-07-11 22:06:38', NULL),
(3, 4, 1, 0.00, 1.00, 10.00, 7000.00, NULL, '2026-07-12 00:25:12', NULL),
(4, 5, 1, 0.00, 1.00, 11.00, 6000.00, NULL, '2026-07-12 23:37:03', NULL),
(7, 8, 1, 0.00, 1.00, 1.00, 6000.00, NULL, '2026-07-12 09:02:33', NULL),
(11, 12, 2, 0.00, 1.00, 10.00, 5000.00, '2026-07-11 22:06:38', '2026-07-12 23:37:51', NULL),
(12, 13, 2, 1.00, 1.00, 10.00, 7000.00, '2026-07-12 00:25:12', '2026-07-12 00:25:12', NULL),
(13, 14, 2, 0.00, 1.00, 1.00, 6000.00, '2026-07-12 09:02:33', '2026-07-16 23:03:41', NULL),
(14, 15, 2, 1.00, 1.00, 11.00, 6000.00, '2026-07-12 23:37:03', '2026-07-12 23:37:03', NULL),
(15, 16, 1, 0.00, 1.00, 10.00, 5000.00, '2026-07-12 23:37:51', '2026-07-13 11:45:23', NULL),
(16, 17, 1, 0.00, 1.00, 10.00, 2500.00, NULL, '2026-07-12 23:55:19', NULL),
(17, 18, 2, 0.00, 1.00, 10.00, 2500.00, '2026-07-12 23:55:19', '2026-07-13 00:00:51', NULL),
(18, 19, 1, 0.00, 1.00, 10.00, 2500.00, '2026-07-13 00:00:51', '2026-07-13 00:10:37', NULL),
(21, 22, 2, 0.00, 1.00, 10.00, 2500.00, '2026-07-13 00:10:37', '2026-07-16 23:03:41', NULL),
(22, 23, 2, 0.00, 1.00, 10.00, 5000.00, '2026-07-13 11:45:23', '2026-07-13 13:24:27', NULL),
(23, 24, 1, 0.00, 1.00, 10.00, 5000.00, '2026-07-13 13:24:28', '2026-07-16 09:01:35', NULL),
(24, 25, 1, 0.00, 1.00, 10.00, 8000.00, NULL, '2026-07-16 09:15:31', NULL),
(25, 26, 1, 1.00, 1.00, 10.00, 4000.00, NULL, '2026-07-16 22:18:01', NULL),
(26, 27, 1, 0.00, 1.00, 10.00, 5000.00, NULL, '2026-07-16 22:24:49', NULL),
(27, 28, 1, 0.00, 1.00, 1.00, 6000.00, '2026-07-16 23:03:41', '2026-07-17 19:04:45', NULL),
(28, 29, 1, 0.00, 1.00, 10.00, 2500.00, '2026-07-16 23:03:41', '2026-07-17 19:01:27', NULL),
(30, 31, 1, 30.00, 10.00, 100.00, 5.00, NULL, NULL, NULL),
(31, 32, 1, 1.00, 1.00, 1.00, 5000.00, NULL, '2026-07-27 17:42:54', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_seleccionados`
--

CREATE TABLE `inventario_seleccionados` (
  `id` int(11) NOT NULL,
  `idinventario` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kardex`
--

CREATE TABLE `kardex` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `idsucursal` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `idproducto_configuracion` int(11) DEFAULT NULL,
  `cantidad` decimal(11,3) NOT NULL,
  `cantidad_contenedor` decimal(11,2) NOT NULL DEFAULT 1.00,
  `precio_unitario` double NOT NULL,
  `stock_actual` decimal(11,3) NOT NULL,
  `tipo_movimiento` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `fecha_kardex` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `kardex`
--

INSERT INTO `kardex` (`id`, `created_at`, `updated_at`, `idsucursal`, `idproducto`, `idproducto_configuracion`, `cantidad`, `cantidad_contenedor`, `precio_unitario`, `stock_actual`, `tipo_movimiento`, `motivo`, `descripcion`, `fecha_kardex`, `deleted_at`) VALUES
(1, '2026-07-12 23:55:19', '2026-07-12 23:55:19', 1, 17, 8, 1.000, 1.00, 4000, 0.000, 0, 'Salida por transferencia', 'Traslado a almacén 1 (pendiente)', '2026-07-13 04:55:19', NULL),
(2, '2026-07-12 23:55:19', '2026-07-12 23:55:19', 2, 18, 8, 1.000, 1.00, 4000, 0.000, 1, 'Ingreso por transferencia', 'Traslado a almacén 1 (pendiente)', '2026-07-13 05:00:15', NULL),
(3, '2026-07-13 00:00:51', '2026-07-13 00:00:51', 2, 18, 9, 1.000, 1.00, 4000, 0.000, 0, 'Salida por transferencia', 'Traslado a almacén 2 (pendiente)', '2026-07-13 05:00:51', NULL),
(4, '2026-07-13 00:00:51', '2026-07-13 00:00:51', 1, 19, 9, 1.000, 1.00, 4000, 0.000, 1, 'Ingreso por transferencia', 'Traslado a almacén 2 (pendiente)', '2026-07-13 05:03:31', NULL),
(7, '2026-07-13 00:10:37', '2026-07-13 00:10:37', 1, 19, 11, 1.000, 1.00, 4000, 0.000, 0, 'Salida por transferencia', 'Traslado a almacén 1 (pendiente)', '2026-07-13 05:14:39', NULL),
(8, '2026-07-13 00:10:37', '2026-07-13 00:10:37', 2, 22, 11, 1.000, 1.00, 4000, 0.000, 1, 'Ingreso por transferencia', 'Traslado a almacén 1 (pendiente)', '2026-07-13 05:13:37', NULL),
(9, '2026-07-16 23:03:41', '2026-07-16 23:03:41', 2, 22, 11, 1.000, 1.00, 4000, 0.000, 0, 'Salida por transferencia', 'Traslado generado desde la solicitud #50', '2026-07-17 04:03:41', NULL),
(10, '2026-07-16 23:03:41', '2026-07-16 23:03:41', 1, 29, 18, 1.000, 1.00, 4000, 0.000, 1, 'Ingreso por transferencia', 'Traslado generado desde la solicitud #50', '2026-07-17 04:03:41', NULL),
(11, '2026-07-17 19:01:27', '2026-07-17 19:01:27', 1, 29, 18, 1.000, 1.00, 4000, 0.000, 0, 'Salida por transferencia', 'Salida generada por la venta #59', '2026-07-18 00:01:27', NULL),
(12, '2026-07-23 19:13:16', '2026-07-23 19:13:16', 1, 31, NULL, 1.000, 1.00, 5, 2.000, 0, 'Compra', NULL, '2026-07-24 00:13:16', NULL),
(13, '2026-07-23 19:15:15', '2026-07-23 19:15:15', 1, 31, NULL, 1.000, 1.00, 5, 3.000, 0, 'Compra', NULL, '2026-07-24 00:15:15', NULL),
(14, '2026-07-23 19:16:32', '2026-07-23 19:16:32', 1, 31, NULL, 1.000, 1.00, 5, 4.000, 0, 'Compra', NULL, '2026-07-24 00:16:32', NULL),
(15, '2026-07-23 19:18:12', '2026-07-23 19:18:12', 1, 31, NULL, 1.000, 1.00, 5, 5.000, 0, 'Compra', NULL, '2026-07-24 00:18:12', NULL),
(16, '2026-07-23 19:19:38', '2026-07-23 19:19:38', 1, 31, NULL, 1.000, 1.00, 5, 6.000, 0, 'Compra', NULL, '2026-07-24 00:19:38', NULL),
(17, '2026-07-23 19:21:18', '2026-07-23 19:21:18', 1, 31, NULL, 1.000, 1.00, 5, 7.000, 0, 'Compra', NULL, '2026-07-24 00:21:18', NULL),
(18, '2026-07-23 19:22:23', '2026-07-23 19:22:23', 1, 31, NULL, 1.000, 1.00, 5, 8.000, 0, 'Compra', NULL, '2026-07-24 00:22:23', NULL),
(19, '2026-07-23 19:24:46', '2026-07-23 19:24:46', 1, 31, NULL, 1.000, 1.00, 5, 9.000, 1, 'Compra', NULL, '2026-07-24 00:24:46', NULL),
(20, '2026-07-23 23:42:03', '2026-07-23 23:42:03', 1, 31, NULL, 10.000, 1.00, 5, 19.000, 1, 'Compra', NULL, '2026-07-24 04:42:03', NULL),
(21, '2026-07-23 23:43:04', '2026-07-23 23:43:04', 1, 31, NULL, 10.000, 1.00, 5, 29.000, 1, 'Compra', NULL, '2026-07-24 04:43:04', NULL),
(22, '2026-07-27 21:40:40', '2026-07-27 21:40:40', 1, 31, NULL, 1.000, 1.00, 5, 30.000, 1, 'Compra', NULL, '2026-07-28 02:40:40', NULL),
(23, '2026-07-27 21:40:40', '2026-07-27 21:40:40', 1, 26, NULL, 1.000, 1.00, 4000, 1.000, 1, 'Compra', NULL, '2026-07-28 02:40:40', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_historial`
--

CREATE TABLE `login_historial` (
  `idhistorial` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `exito` tinyint(1) NOT NULL,
  `logout` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `login_historial`
--

INSERT INTO `login_historial` (`idhistorial`, `idusuario`, `ip`, `user_agent`, `fecha`, `exito`, `logout`) VALUES
(1, 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 12:52:02', 0, NULL),
(2, 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 12:52:06', 0, NULL),
(3, 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 12:53:15', 0, NULL),
(4, 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 12:53:16', 0, NULL),
(5, 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 12:53:22', 0, NULL),
(6, 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 12:53:28', 0, NULL),
(7, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 12:54:23', 0, '2026-07-01 17:54:39'),
(8, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 12:54:49', 0, '2026-07-01 17:55:06'),
(9, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 12:55:21', 0, '2026-07-01 17:55:42'),
(10, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 12:56:36', 0, '2026-07-01 17:58:06'),
(11, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 12:58:17', 0, '2026-07-01 17:59:37'),
(12, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 12:59:51', 0, '2026-07-01 18:00:45'),
(13, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-01 13:00:58', 0, '2026-07-04 08:47:57'),
(14, 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-04 03:47:59', 1, NULL),
(15, 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 15:13:13', 1, NULL),
(16, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-09 11:44:35', 0, '2026-07-12 00:47:29'),
(17, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-11 19:47:33', 0, '2026-07-12 18:47:51'),
(18, 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-12 13:47:54', 1, NULL),
(19, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 03:19:42', 0, '2026-07-14 09:48:46'),
(20, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 04:48:49', 0, '2026-07-14 10:10:41'),
(21, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 05:10:43', 0, '2026-07-14 11:53:28'),
(22, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 06:53:30', 0, '2026-07-14 11:57:07'),
(23, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 07:06:53', 0, '2026-07-14 12:36:44'),
(24, 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 07:36:51', 0, NULL),
(25, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 07:36:57', 0, '2026-07-14 12:37:27'),
(26, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 07:39:39', 0, '2026-07-14 12:42:51'),
(27, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 07:42:54', 0, '2026-07-14 12:54:02'),
(28, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 07:54:05', 0, '2026-07-14 13:11:33'),
(29, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 08:11:40', 0, '2026-07-14 13:12:25'),
(30, 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 08:12:27', 1, NULL),
(31, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-15 03:00:29', 0, '2026-07-16 20:51:20'),
(32, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 15:51:28', 0, '2026-07-16 20:51:35'),
(33, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 15:51:38', 0, '2026-07-16 21:46:52'),
(34, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 16:46:58', 0, '2026-07-18 18:52:36'),
(35, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-18 13:52:38', 0, '2026-07-19 00:11:31'),
(36, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-18 19:11:34', 0, '2026-07-20 17:52:40'),
(37, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-20 13:12:43', 0, '2026-07-20 18:20:34'),
(38, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-20 13:21:44', 0, '2026-07-20 18:23:25'),
(39, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-20 13:25:22', 0, '2026-07-21 16:54:32'),
(40, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 11:54:40', 0, '2026-07-21 16:57:39'),
(41, 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 11:57:43', 1, NULL),
(42, 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-26 03:32:32', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `idmarca` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `marca`
--

INSERT INTO `marca` (`idmarca`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'HONDA', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modelo`
--

CREATE TABLE `modelo` (
  `idmodelo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `modelo`
--

INSERT INTO `modelo` (`idmodelo`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'GL-125', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `motivos_nota`
--

CREATE TABLE `motivos_nota` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `condicion` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento`
--

CREATE TABLE `movimiento` (
  `idmovimiento` int(11) NOT NULL,
  `idcaja` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `tipo` varchar(25) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `idpersonal` int(11) DEFAULT NULL,
  `totalefectivo` decimal(11,2) NOT NULL,
  `formapago` varchar(50) NOT NULL,
  `totaldeposito` decimal(10,2) NOT NULL,
  `noperacion` varchar(50) NOT NULL,
  `descripcion` text NOT NULL,
  `idconcepto_movimiento` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `movimiento`
--

INSERT INTO `movimiento` (`idmovimiento`, `idcaja`, `fecha`, `tipo`, `idsucursal`, `idpersonal`, `totalefectivo`, `formapago`, `totaldeposito`, `noperacion`, `descripcion`, `idconcepto_movimiento`) VALUES
(1, 1, '2026-07-07 23:25:05', 'Ingresos', 1, 1, 100.00, 'YAPE', 100.00, '68679', '', 1),
(2, 1, '2026-07-07 23:29:19', 'Egresos', 1, 1, 0.00, 'YAPE', 100.00, '68679', '', NULL),
(3, 1, '2026-07-19 09:20:51', 'Egresos', 1, 2, 100.00, 'Efectivo', 0.00, '', 'Adelanto de sueldo', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nombre_precios`
--

CREATE TABLE `nombre_precios` (
  `idnombre_p` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nombre_precios`
--

INSERT INTO `nombre_precios` (`idnombre_p`, `descripcion`, `estado`) VALUES
(1, 'Precio oferta', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `idnotificacion` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `idtraslado` int(11) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) DEFAULT 0,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `idcliente` int(11) DEFAULT NULL,
  `idcpc` int(11) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `canal` enum('SISTEMA','WHATSAPP') DEFAULT 'SISTEMA',
  `estado` enum('PENDIENTE','ENVIADO','ERROR') DEFAULT 'PENDIENTE',
  `respuesta_api` text DEFAULT NULL,
  `fecha_envio` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`idnotificacion`, `idsucursal`, `idtraslado`, `mensaje`, `leido`, `fecha`, `idcliente`, `idcpc`, `telefono`, `canal`, `estado`, `respuesta_api`, `fecha_envio`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 1, 4, 'Nueva solicitud pendiente desde el almacén 2 con ID 4', 1, '2026-07-11 15:13:46', NULL, NULL, NULL, 'SISTEMA', 'PENDIENTE', NULL, NULL, '2026-07-13 12:14:50', NULL, NULL),
(4, 1, 6, 'Nueva solicitud pendiente desde el almacén 2 con ID 6', 1, '2026-07-11 16:57:52', NULL, NULL, NULL, 'SISTEMA', 'PENDIENTE', NULL, NULL, '2026-07-13 12:14:50', NULL, NULL),
(5, 1, 7, 'Nueva solicitud pendiente desde el almacén 2 con ID 7', 1, '2026-07-11 16:59:01', NULL, NULL, NULL, 'SISTEMA', 'PENDIENTE', NULL, NULL, '2026-07-13 12:14:50', NULL, NULL),
(6, 2, 44, 'Nueva solicitud pendiente desde el almacén JIMENEZ 2 con ID 44', 1, '2026-07-13 17:15:15', NULL, NULL, NULL, 'SISTEMA', 'PENDIENTE', NULL, NULL, '2026-07-13 12:15:15', '2026-07-13 12:15:15', NULL),
(7, 1, 46, 'Nueva solicitud pendiente desde el almacén Nueva cajamarca con ID 46', 0, '2026-07-13 18:26:02', NULL, NULL, NULL, 'SISTEMA', 'PENDIENTE', NULL, NULL, '2026-07-13 13:26:02', '2026-07-13 13:26:02', NULL),
(8, 1, 47, 'Nueva solicitud pendiente desde el almacén Nueva cajamarca con ID 47', 0, '2026-07-13 18:26:03', NULL, NULL, NULL, 'SISTEMA', 'PENDIENTE', NULL, NULL, '2026-07-13 13:26:03', '2026-07-13 13:26:03', NULL),
(9, 1, 48, 'Nueva solicitud pendiente desde el almacén Nueva cajamarca con ID 48', 0, '2026-07-13 18:26:04', NULL, NULL, NULL, 'SISTEMA', 'PENDIENTE', NULL, NULL, '2026-07-13 13:26:04', '2026-07-13 13:26:04', NULL),
(10, 1, 49, 'Nueva solicitud pendiente desde el almacén Nueva cajamarca con ID 49', 0, '2026-07-13 18:26:12', NULL, NULL, NULL, 'SISTEMA', 'PENDIENTE', NULL, NULL, '2026-07-13 13:26:12', '2026-07-13 13:26:12', NULL),
(11, 2, 51, 'Nueva solicitud pendiente desde el almacén JIMENEZ 2 con ID 51', 1, '2026-07-13 19:48:14', NULL, NULL, NULL, 'SISTEMA', 'PENDIENTE', NULL, NULL, '2026-07-13 14:48:14', '2026-07-13 14:48:14', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_trabajo`
--

CREATE TABLE `orden_trabajo` (
  `idorden` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `tipo` enum('MANTENIMIENTO','REPARACION','ENSAMBLAJE','DESARME','RESTAURACION') NOT NULL,
  `estado` enum('PENDIENTE','EN_PROCESO','FINALIZADO','CANCELADO') NOT NULL DEFAULT 'PENDIENTE',
  `fecha_inicio` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_fin` datetime DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `costo_materiales` decimal(12,2) NOT NULL DEFAULT 0.00,
  `costo_servicios` decimal(12,2) NOT NULL DEFAULT 0.00,
  `costo_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `idusuario` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_trabajo_detalle`
--

CREATE TABLE `orden_trabajo_detalle` (
  `iddetalle` int(11) NOT NULL,
  `idorden` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `tipo` enum('MATERIAL','SERVICIO') NOT NULL DEFAULT 'MATERIAL',
  `cantidad` decimal(10,2) NOT NULL DEFAULT 1.00,
  `precio_unitario` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `observacion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_asistencia`
--

CREATE TABLE `pagos_asistencia` (
  `idpago` int(11) NOT NULL,
  `idasistencia` int(11) NOT NULL,
  `idpersonal` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `monto_pago` decimal(10,2) NOT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `idpermiso` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `permiso`
--

INSERT INTO `permiso` (`idpermiso`, `nombre`) VALUES
(1, 'Ventas'),
(2, 'Configuracion general'),
(3, 'Clientes'),
(4, 'Facturacion y cajas'),
(5, 'Almacen'),
(6, 'Inventario'),
(7, 'Compras'),
(8, 'Caja chica'),
(9, 'Cobros'),
(10, 'Cuentas por pagar'),
(11, 'Kardex'),
(12, 'Personal'),
(13, 'Configuracion'),
(14, 'Consultar compras'),
(15, 'Consultar ventas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `idpersona` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `tipo_persona` varchar(20) NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `tipo_documento` varchar(20) DEFAULT NULL,
  `num_documento` varchar(20) DEFAULT NULL,
  `direccion` varchar(70) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `latitude` varchar(20) DEFAULT NULL,
  `longitude` varchar(20) DEFAULT NULL,
  `medida_derecha` varchar(50) DEFAULT NULL,
  `medida_izquierda` varchar(50) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `isproveedor` tinyint(1) DEFAULT 0,
  `dipc` varchar(250) DEFAULT NULL,
  `addc` varchar(250) DEFAULT NULL,
  `productoc` varchar(250) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`idpersona`, `created_at`, `updated_at`, `tipo_persona`, `nombre`, `tipo_documento`, `num_documento`, `direccion`, `telefono`, `email`, `fecha`, `latitude`, `longitude`, `medida_derecha`, `medida_izquierda`, `fecha_registro`, `isproveedor`, `dipc`, `addc`, `productoc`, `deleted_at`) VALUES
(1, '2026-07-09 16:13:30', NULL, 'Cliente', 'Publico general', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-01 00:00:00', 0, NULL, NULL, NULL, NULL),
(2, '2026-07-09 16:13:30', NULL, 'Cliente', 'Alis Huamanta Edquen', 'DNI', '71845256', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-01 00:00:00', 0, NULL, NULL, NULL, NULL),
(3, '2026-07-09 16:13:30', NULL, 'Cliente', 'ALIS HUAMANTA EDQUEN', 'DNI', '71845256', 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', '956856625', 'alexhe406@gmail.com', NULL, '-6.480185084346602', '-76.3749584665141', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(4, '2026-07-10 01:25:22', '2026-07-10 01:25:22', 'Cliente', 'ALIS HUAMANTA EDQUEN', 'DNI', '71845256', 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', '956856625', 'alexhe406@gmail.com', NULL, '-6.480185084346602', '-76.3749584665141', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(5, '2026-07-10 01:29:33', '2026-07-10 01:29:33', 'Cliente', 'ALIS HUAMANTA EDQUEN', 'DNI', '71845256', 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', '956856625', 'alexhe406@gmail.com', NULL, '-6.480185084346602', '-76.3749584665141', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(6, '2026-07-10 01:32:14', '2026-07-10 01:32:14', 'Cliente', 'ALIS HUAMANTA EDQUEN', 'DNI', '71845256', 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', '956856625', 'alexhe406@gmail.com', NULL, '-6.480185084346602', '-76.3749584665141', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(7, '2026-07-10 01:32:39', '2026-07-10 01:32:39', 'Cliente', 'ALIS HUAMANTA EDQUEN', 'DNI', '71845256', 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', '956856625', 'alexhe406@gmail.com', NULL, '-6.480185084346602', '-76.3749584665141', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(8, '2026-07-10 01:33:31', '2026-07-10 01:33:31', 'Cliente', 'ALIS HUAMANTA EDQUENN', 'DNI', '71845256', 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', '956856625', 'alexhe406@gmail.com', NULL, '-6.480185084346602', '-76.3749584665141', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(9, '2026-07-10 01:34:31', '2026-07-10 01:34:31', 'Cliente', 'Alis Huamanta Edquen', 'DNI', '71845256', 'Plaza de Armas de Tarapoto, Pl. Mayor 453, Tarapoto 22202, Peru', NULL, NULL, NULL, '-6.487595468705555', '-76.3601303100586', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(10, '2026-07-10 01:36:46', '2026-07-10 06:07:30', 'Cliente', 'Alis Huamanta Edquen', 'DNI', '71845256', 'Plaza de Armas de Tarapoto, Pl. Mayor 453, Tarapoto 22202, Peru', NULL, NULL, NULL, '-6.487595468705555', '-76.3601303100586', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(11, '2026-07-10 01:36:53', '2026-07-10 01:36:53', 'Cliente', 'ALIS HUAMANTA EDQUEN', 'DNI', '71845256', 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', '956856625', 'alexhe406@gmail.com', NULL, '-6.480185084346602', '-76.3749584665141', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(12, '2026-07-10 01:37:02', '2026-07-10 01:37:02', 'Cliente', 'ALIS HUAMANTA EDQUEN', 'DNI', '71845256', 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', '956856625', 'alexhe406@gmail.com', NULL, '-6.480185084346602', '-76.3749584665141', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(13, '2026-07-10 01:38:07', '2026-07-10 01:50:41', 'Cliente', 'FLOR EEDITH MARRUFO VASQUEZ', 'DNI', '71845223', 'FMX2+MX8, Tarapoto 22202, Peru', '993598356', 'qwqeqeqwe@gmail.com', NULL, '-6.500762691370645', '-76.34742737340275', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(14, '2026-07-10 01:39:18', '2026-07-10 06:06:59', 'Cliente', 'FLORR EDITH MARRUFO VASQUEZ', 'DNI', '71845223', 'FMX2+MX8, Tarapoto 22202, Peru', '993598356', 'qwqeqeqwe@gmail.com', NULL, '-6.500762691370645', '-76.34742737340275', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(15, '2026-07-14 15:17:45', '2026-07-14 22:17:45', 'Cliente', 'FLORR EDITH MARRUFO VASQUEZ', 'DNI', '71845223', 'FMX2+MX8, Tarapoto 22202, Peru', '993598356', 'qwqeqeqwe@gmail.com', NULL, '-6.500762691370645', '-76.34742737340275', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(16, '2026-07-14 15:18:02', '2026-07-24 00:11:49', 'Cliente', 'Alis Huamanta Edquen', 'DNI', '71845256', 'Plaza de Armas de Tarapoto, Pl. Mayor 453, Tarapoto 22202, Peru', '999', NULL, NULL, '-6.487595468705555', '-76.3601303100586', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(17, '2026-07-23 15:44:23', '2026-07-23 16:09:55', 'Proveedor', 'HUAMANTA EDQUEN ALIS SAC', 'RUC', '10718452568', 'Plaza de Nueva cajamarca Pl. Mayor 453, Tarapoto 22202, Peru', '956856625', 'alexhe406@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(18, '2026-07-23 16:15:01', '2026-07-27 23:28:36', 'Proveedor', 'COCA COLA COMPANY', 'RUC', '20232565895', 'Plaza de Nueva cajamarca Pl. Mayor 453, Tarapoto 22202, Peru', '933216752', 'wegerg@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(19, '2026-07-27 17:40:13', '2026-07-27 17:40:13', 'Cliente', 'VICENTE FERNANDEZ', 'DNI', '70256589', 'Plaza de Nueva cajamarca Pl. Mayor 453, Tarapoto 22202, Peru', '937485661', 'fsdfsf@gmail.com', '2026-07-27', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `idpersonal` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo_documento` varchar(20) NOT NULL,
  `num_documento` varchar(20) NOT NULL,
  `direccion` varchar(70) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `cargo` varchar(20) DEFAULT NULL,
  `imagen` varchar(50) DEFAULT NULL,
  `porcentaje` decimal(11,2) DEFAULT NULL,
  `salario` decimal(11,2) DEFAULT NULL,
  `condicion` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`created_at`, `updated_at`, `idpersonal`, `nombre`, `tipo_documento`, `num_documento`, `direccion`, `telefono`, `email`, `cargo`, `imagen`, `porcentaje`, `salario`, `condicion`, `deleted_at`) VALUES
('2026-07-10 22:17:51', '2026-07-11 03:19:41', 1, 'Alis Huamanta', 'DNI', '71845256', NULL, NULL, NULL, 'Administrador', NULL, 0.00, NULL, 1, NULL),
('2026-07-11 03:18:25', '2026-07-11 03:18:59', 2, 'Juan Diego Rodriguez Dias', 'DNI', '75485625', 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', '956585656', 'wwgwg@gmail.com', 'Vendedor', 'user.png', 0.00, 1500.00, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `idproducto` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `idcategoria` int(11) DEFAULT NULL,
  `idunidad_medida` int(11) NOT NULL,
  `idrubro` int(11) DEFAULT NULL,
  `idcondicionventa` int(11) DEFAULT NULL,
  `idmarca` int(11) DEFAULT NULL,
  `idmodelo` int(11) DEFAULT NULL,
  `registrosan` varchar(50) DEFAULT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `nombre` varchar(250) NOT NULL,
  `precio` decimal(11,2) DEFAULT NULL,
  `precioB` decimal(11,2) DEFAULT NULL,
  `precioC` decimal(11,2) DEFAULT NULL,
  `precioD` decimal(11,2) DEFAULT NULL,
  `precioE` decimal(11,2) DEFAULT NULL,
  `margenpubl` decimal(11,2) DEFAULT NULL,
  `margendes` decimal(11,2) DEFAULT NULL,
  `margenp1` decimal(11,2) DEFAULT NULL,
  `margenp2` decimal(11,2) DEFAULT NULL,
  `margendist` decimal(11,2) DEFAULT NULL,
  `utilprecio` decimal(11,2) DEFAULT NULL,
  `utilprecioB` decimal(11,2) DEFAULT NULL,
  `utilprecioC` decimal(11,2) DEFAULT NULL,
  `utilprecioD` decimal(11,2) DEFAULT NULL,
  `utilprecioE` decimal(11,2) DEFAULT NULL,
  `preciocigv` decimal(11,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `descripcion` varchar(256) DEFAULT NULL,
  `imagen` varchar(50) DEFAULT 'anonymous.png',
  `condicion` tinyint(1) NOT NULL DEFAULT 1,
  `controla_stock` enum('Si','No') NOT NULL DEFAULT 'Si',
  `requiere_serie` tinyint(1) NOT NULL DEFAULT 0,
  `alerta_stock` enum('Si','No') NOT NULL DEFAULT 'Si',
  `proigv` varchar(50) DEFAULT NULL,
  `percha` varchar(100) DEFAULT NULL,
  `comisionV` decimal(11,2) DEFAULT NULL,
  `fechac` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`created_at`, `updated_at`, `idproducto`, `idsucursal`, `idcategoria`, `idunidad_medida`, `idrubro`, `idcondicionventa`, `idmarca`, `idmodelo`, `registrosan`, `codigo`, `nombre`, `precio`, `precioB`, `precioC`, `precioD`, `precioE`, `margenpubl`, `margendes`, `margenp1`, `margenp2`, `margendist`, `utilprecio`, `utilprecioB`, `utilprecioC`, `utilprecioD`, `utilprecioE`, `preciocigv`, `fecha`, `descripcion`, `imagen`, `condicion`, `controla_stock`, `requiere_serie`, `alerta_stock`, `proigv`, `percha`, `comisionV`, `fechac`, `deleted_at`) VALUES
('2026-07-10 22:43:19', '2026-07-11 07:59:46', 1, 1, 1, 1, 1, 1, 1, 1, '', '2026001', 'MOTOCICLETA LINEAL HONDA GL 125', 7000.00, 8000.00, 0.00, 0.00, 0.00, 40.00, 60.00, -100.00, -100.00, -100.00, 2000.00, 3000.00, -5000.00, -5000.00, -5000.00, 0.00, NULL, 'FJFGJ', 'anonymous.png', 1, 'No', 0, 'Si', 'Gravada', NULL, 0.00, '2026-07-02 03:04:40', NULL),
('2026-07-10 23:59:19', '2026-07-11 07:59:50', 4, 1, 2, 1, 1, 1, 1, 1, '', '2026002', 'NUEVA CAJAMARCA', 9000.00, 1.00, 0.00, 0.00, 0.00, 28.57, 32.86, -100.00, -100.00, -100.00, 2000.00, 2300.00, -7000.00, -7000.00, -7000.00, 0.00, NULL, 'Plato norteño', 'anonymous.png', 1, 'No', 0, 'No', 'No Gravada', NULL, 0.00, '2026-07-11 04:59:19', NULL),
('2026-07-11 00:05:59', '2026-07-11 08:06:46', 5, 1, 1, 1, 1, 1, 1, 1, '1', '2026007', 'Moto CB 150 XR', 9000.00, 1.00, 1.00, 1.00, 1.00, 50.00, 50.00, -99.98, -99.98, -99.98, 3000.00, 3000.00, -5999.00, -5999.00, -5999.00, 0.00, NULL, 'DFNDMGM', 'DFNDMGM', 1, 'No', 0, 'No', '', NULL, 1.00, '2026-07-11 05:05:59', NULL),
('2026-07-11 00:06:34', '2026-07-11 08:08:48', 8, 1, 2, 1, 1, 1, 1, 1, '', '2026003', 'MOTO XR190', 9000.00, 1.00, 1.00, 1.00, 1.00, 50.00, -99.98, -100.00, -100.00, -100.00, 3000.00, -5999.00, -6000.00, -6000.00, -6000.00, 0.00, NULL, 'DFNDMGM', 'anonymous.png', 1, 'No', 0, 'Si', 'No Gravada', NULL, 1.00, '2026-07-11 05:06:34', NULL),
('2026-07-11 22:06:38', '2026-07-11 22:06:38', 12, 2, 1, 1, 1, 1, 1, 1, '', '2026001', 'MOTOCICLETA LINEAL HONDA GL 125', 7000.00, 8000.00, 0.00, 0.00, 0.00, 40.00, 60.00, -100.00, -100.00, -100.00, 2000.00, 3000.00, -5000.00, -5000.00, -5000.00, 0.00, NULL, 'FJFGJ', 'anonymous.png', 1, 'No', 0, 'Si', 'Gravada', NULL, 0.00, '2026-07-02 03:04:40', NULL),
('2026-07-12 00:25:12', '2026-07-12 00:25:12', 13, 2, 2, 1, 1, 1, 1, 1, '', '2026002', 'NUEVA CAJAMARCA', 9000.00, 1.00, 0.00, 0.00, 0.00, 28.57, 32.86, -100.00, -100.00, -100.00, 2000.00, 2300.00, -7000.00, -7000.00, -7000.00, 0.00, NULL, 'Plato norteño', 'anonymous.png', 1, 'No', 0, 'No', 'No Gravada', NULL, 0.00, '2026-07-11 04:59:19', NULL),
('2026-07-12 09:02:33', '2026-07-12 09:02:33', 14, 2, 2, 1, 1, 1, 1, 1, '', '2026003', 'MOTO XR190', 9000.00, 1.00, 1.00, 1.00, 1.00, 50.00, -99.98, -100.00, -100.00, -100.00, 3000.00, -5999.00, -6000.00, -6000.00, -6000.00, 0.00, NULL, 'DFNDMGM', 'anonymous.png', 1, 'No', 0, 'Si', 'No Gravada', NULL, 1.00, '2026-07-11 05:06:34', NULL),
('2026-07-12 23:37:03', '2026-07-12 23:37:03', 15, 2, 1, 1, 1, 1, 1, 1, '1', '2026007', 'Moto CB 150 XR', 9000.00, 1.00, 1.00, 1.00, 1.00, 50.00, 50.00, -99.98, -99.98, -99.98, 3000.00, 3000.00, -5999.00, -5999.00, -5999.00, 0.00, NULL, 'DFNDMGM', 'DFNDMGM', 1, 'No', 0, 'No', '', NULL, 1.00, '2026-07-11 05:05:59', NULL),
('2026-07-12 23:37:51', '2026-07-12 23:37:51', 16, 1, 1, 1, 1, 1, 1, 1, '', '2026001', 'MOTOCICLETA LINEAL HONDA GL 125', 7000.00, 8000.00, 0.00, 0.00, 0.00, 40.00, 60.00, -100.00, -100.00, -100.00, 2000.00, 3000.00, -5000.00, -5000.00, -5000.00, 0.00, NULL, 'FJFGJ', 'anonymous.png', 1, 'No', 0, 'Si', 'Gravada', NULL, 0.00, '2026-07-02 03:04:40', NULL),
('2026-07-12 23:40:57', '2026-07-12 23:40:57', 17, 1, 1, 1, 1, 1, 1, 1, '', '2026008', 'MOTO WAVE 110', 4000.00, 1.00, 0.00, 0.00, 0.00, 60.00, 60.00, -100.00, -100.00, -100.00, 1500.00, 1500.00, -2500.00, -2500.00, -2500.00, 0.00, NULL, 'fgjfdjdtktyk', 'anonymous.png', 1, 'Si', 0, 'Si', 'No Gravada', NULL, 0.00, '2026-07-13 04:40:57', NULL),
('2026-07-12 23:55:19', '2026-07-12 23:55:19', 18, 2, 1, 1, 1, 1, 1, 1, '', '2026008', 'MOTO WAVE 110', 4000.00, 1.00, 0.00, 0.00, 0.00, 60.00, 60.00, -100.00, -100.00, -100.00, 1500.00, 1500.00, -2500.00, -2500.00, -2500.00, 0.00, NULL, 'fgjfdjdtktyk', 'anonymous.png', 1, 'Si', 0, 'Si', 'No Gravada', NULL, 0.00, '2026-07-13 04:40:57', NULL),
('2026-07-13 00:00:51', '2026-07-13 00:00:51', 19, 1, 1, 1, 1, 1, 1, 1, '', '2026008', 'MOTO WAVE 110', 4000.00, 1.00, 0.00, 0.00, 0.00, 60.00, 60.00, -100.00, -100.00, -100.00, 1500.00, 1500.00, -2500.00, -2500.00, -2500.00, 0.00, NULL, 'fgjfdjdtktyk', 'anonymous.png', 1, 'Si', 0, 'Si', 'No Gravada', NULL, 0.00, '2026-07-13 04:40:57', NULL),
('2026-07-13 00:10:37', '2026-07-13 00:10:37', 22, 2, 1, 1, 1, 1, 1, 1, '', '2026008', 'MOTO WAVE 110', 4000.00, 1.00, 0.00, 0.00, 0.00, 60.00, 60.00, -100.00, -100.00, -100.00, 1500.00, 1500.00, -2500.00, -2500.00, -2500.00, 0.00, NULL, 'fgjfdjdtktyk', 'anonymous.png', 1, 'Si', 0, 'Si', 'No Gravada', NULL, 0.00, '2026-07-13 04:40:57', NULL),
('2026-07-13 11:45:23', '2026-07-13 11:45:23', 23, 2, 1, 1, 1, 1, 1, 1, '', '2026001', 'MOTOCICLETA LINEAL HONDA GL 125', 7000.00, 8000.00, 0.00, 0.00, 0.00, 40.00, 60.00, -100.00, -100.00, -100.00, 2000.00, 3000.00, -5000.00, -5000.00, -5000.00, 0.00, NULL, 'FJFGJ', 'anonymous.png', 1, 'No', 0, 'Si', 'Gravada', NULL, 0.00, '2026-07-02 03:04:40', NULL),
('2026-07-13 13:24:27', '2026-07-13 13:24:27', 24, 1, 1, 1, 1, 1, 1, 1, '', '2026001', 'MOTOCICLETA LINEAL HONDA GL 125', 7000.00, 8000.00, 0.00, 0.00, 0.00, 40.00, 60.00, -100.00, -100.00, -100.00, 2000.00, 3000.00, -5000.00, -5000.00, -5000.00, 0.00, NULL, 'FJFGJ', 'anonymous.png', 1, 'No', 0, 'Si', 'Gravada', NULL, 0.00, '2026-07-02 03:04:40', NULL),
('2026-07-16 09:06:39', '2026-07-16 09:06:39', 25, 1, 1, 1, 1, 1, 1, 1, '', '2026009', 'MOTO NS 200 BAJAJ', 11000.00, 1.00, 0.00, 0.00, 0.00, 37.50, 43.75, -100.00, -100.00, -100.00, 3000.00, 3500.00, -8000.00, -8000.00, -8000.00, 0.00, NULL, 'JTDJ', 'anonymous.png', 1, 'No', 0, 'No', 'Gravada', NULL, 0.00, '2026-07-16 14:06:39', NULL),
('2026-07-16 10:20:56', '2026-07-16 10:20:56', 26, 1, 1, 1, 1, 4, 1, 1, '', '2026010', 'GL-125 REACH', 6000.00, 1.00, 0.00, 0.00, 0.00, 50.00, 50.00, -100.00, -100.00, -100.00, 2000.00, 2000.00, -4000.00, -4000.00, -4000.00, 0.00, NULL, 'DFNDMGM', 'anonymous.png', 1, 'No', 0, 'No', 'No Gravada', NULL, 0.00, '2026-07-16 15:20:56', NULL),
('2026-07-16 22:24:01', '2026-07-16 22:24:01', 27, 1, 1, 1, 1, 4, 1, 1, '', '2026011', 'MOTO LINEAL NUEV', 6000.00, 1.00, 0.00, 0.00, 0.00, 20.00, 20.00, -100.00, -100.00, -100.00, 1000.00, 1000.00, -5000.00, -5000.00, -5000.00, 0.00, NULL, 'erg', 'anonymous.png', 1, 'No', 0, 'No', 'Gravada', NULL, 0.00, '2026-07-17 03:24:01', NULL),
('2026-07-16 23:03:41', '2026-07-16 23:03:41', 28, 1, 2, 1, 1, 1, 1, 1, '', '2026003', 'MOTO XR190', 9000.00, 1.00, 1.00, 1.00, 1.00, 50.00, -99.98, -100.00, -100.00, -100.00, 3000.00, -5999.00, -6000.00, -6000.00, -6000.00, 0.00, NULL, 'DFNDMGM', 'anonymous.png', 1, 'No', 0, 'Si', 'No Gravada', NULL, 1.00, '2026-07-11 05:06:34', NULL),
('2026-07-16 23:03:41', '2026-07-16 23:03:41', 29, 1, 1, 1, 1, 1, 1, 1, '', '2026008', 'MOTO WAVE 110', 4000.00, 1.00, 0.00, 0.00, 0.00, 60.00, 60.00, -100.00, -100.00, -100.00, 1500.00, 1500.00, -2500.00, -2500.00, -2500.00, 0.00, NULL, 'fgjfdjdtktyk', 'anonymous.png', 1, 'Si', 0, 'Si', 'No Gravada', NULL, 0.00, '2026-07-13 04:40:57', NULL),
('2026-07-23 10:26:20', '2026-07-23 10:26:40', 31, 1, 1, 1, 2, 4, 1, 1, '', '2026012', 'Camara para m300', 10.00, 1.00, 1.00, 1.00, 1.00, 100.00, 100.00, -100.00, -100.00, -100.00, 5.00, 5.00, -5.00, -5.00, -5.00, 0.00, NULL, 'ergerh', 'anonymous.png', 1, 'Si', 0, 'Si', 'No Gravada', NULL, 1.00, '2026-07-23 15:26:20', NULL),
('2026-07-27 17:21:00', '2026-07-27 17:21:00', 32, 1, 2, 1, 1, 1, 1, 1, '', '2026013', 'MOTO LINEAL HIUNDAT 200', 7000.00, 1.00, 0.00, 0.00, 0.00, 40.00, 40.00, -100.00, -100.00, -100.00, 2000.00, 2000.00, -5000.00, -5000.00, -5000.00, 0.00, NULL, 'owejfpwjefpioew', 'anonymous.png', 1, 'No', 0, 'No', 'Gravada', NULL, 0.00, '2026-07-27 22:21:00', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_configuracion`
--

CREATE TABLE `producto_configuracion` (
  `idproducto_configuracion` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `codigo_extra` varchar(50) NOT NULL,
  `contenedor` varchar(200) NOT NULL,
  `cantidad_contenedor` decimal(11,2) NOT NULL,
  `precio_venta` double NOT NULL DEFAULT 0,
  `idfifo_origen` int(11) DEFAULT 0,
  `precio_promocion` double NOT NULL DEFAULT 0,
  `estado` int(11) NOT NULL DEFAULT 1,
  `idproducto` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `producto_configuracion`
--

INSERT INTO `producto_configuracion` (`idproducto_configuracion`, `created_at`, `updated_at`, `codigo_extra`, `contenedor`, `cantidad_contenedor`, `precio_venta`, `idfifo_origen`, `precio_promocion`, `estado`, `idproducto`, `deleted_at`) VALUES
(1, '2026-07-01 22:04:40', '2026-07-11 22:06:38', '2026001', 'UNIDAD', 1.00, 7000, 0, 80000, 1, 16, NULL),
(3, '2026-07-10 23:59:19', '2026-07-12 00:25:12', '2026002', 'UNIDAD', 1.00, 9000, 0, 9300, 1, 13, NULL),
(4, '2026-07-11 00:05:59', '2026-07-12 23:37:03', '2026003', 'UNIDAD', 1.00, 9000, 0, 9500, 1, 15, NULL),
(7, '2026-07-11 00:06:34', '2026-07-12 09:02:33', '2026003', 'UNIDAD', 1.00, 9000, 0, 9500, 1, 14, NULL),
(8, '2026-07-12 23:40:57', NULL, '2026008', 'UNIDAD', 1.00, 4000, 0, 4000, 1, 17, NULL),
(9, '2026-07-12 23:40:57', '2026-07-12 23:55:19', '2026008', 'UNIDAD', 1.00, 4000, 0, 4000, 1, 18, NULL),
(10, '2026-07-12 23:40:57', '2026-07-12 23:55:19', '2026008', 'UNIDAD', 1.00, 4000, 0, 4000, 1, 19, NULL),
(11, '2026-07-12 23:40:57', '2026-07-12 23:55:19', '2026008', 'UNIDAD', 1.00, 4000, 0, 4000, 1, 22, NULL),
(12, '2026-07-01 22:04:40', '2026-07-11 22:06:38', '2026001', 'UNIDAD', 1.00, 7000, 0, 80000, 1, 23, NULL),
(13, '2026-07-01 22:04:40', '2026-07-11 22:06:38', '2026001', 'UNIDAD', 1.00, 7000, 0, 80000, 1, 24, NULL),
(14, '2026-07-16 09:06:39', NULL, '2026009', 'UNIDAD', 1.00, 11000, 0, 11500, 1, 25, NULL),
(15, '2026-07-16 10:20:56', NULL, '2026010', 'UNIDAD', 1.00, 6000, 0, 6000, 1, 26, NULL),
(16, '2026-07-16 22:24:01', NULL, '2026011', 'UNIDAD', 1.00, 6000, 0, 6000, 1, 27, NULL),
(17, '2026-07-11 00:06:34', '2026-07-12 09:02:33', '2026003', 'UNIDAD', 1.00, 9000, 0, 9500, 1, 28, NULL),
(18, '2026-07-12 23:40:57', '2026-07-12 23:55:19', '2026008', 'UNIDAD', 1.00, 4000, 0, 4000, 1, 29, NULL),
(20, '2026-07-23 10:26:20', NULL, '2026012', 'UNIDAD', 1.00, 10, 0, 10, 1, 31, NULL),
(21, '2026-07-27 17:21:00', NULL, '2026013', 'UNIDAD', 1.00, 7000, 0, 7000, 1, 32, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_configuracion_precios`
--

CREATE TABLE `producto_configuracion_precios` (
  `id` int(11) NOT NULL,
  `producto_configuracion_id` int(11) NOT NULL,
  `precio` double NOT NULL,
  `margen_utilidad` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `idnombre_p` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_serie`
--

CREATE TABLE `producto_serie` (
  `idserie` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `numero_serie` varchar(150) DEFAULT NULL,
  `numero_motor` varchar(150) DEFAULT NULL,
  `placa` varchar(30) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `anio_fabricacion` smallint(6) DEFAULT NULL,
  `permiso_circulacion` varchar(150) DEFAULT NULL,
  `tipo_vehiculo` varchar(100) DEFAULT NULL,
  `clase_vehiculo` varchar(100) DEFAULT NULL,
  `propietario_vehiculo` varchar(200) DEFAULT NULL,
  `estado` enum('DISPONIBLE','RESERVADO','VENDIDO','ANULADO','TRASLADO','MANTENIMIENTO') DEFAULT 'DISPONIBLE',
  `observacion` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `producto_serie`
--

INSERT INTO `producto_serie` (`idserie`, `idproducto`, `idsucursal`, `numero_serie`, `numero_motor`, `placa`, `color`, `anio_fabricacion`, `permiso_circulacion`, `tipo_vehiculo`, `clase_vehiculo`, `propietario_vehiculo`, `estado`, `observacion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '5876867GHKGH', '86786786786', 'AP9-545', 'Rojo', 2026, 'sgerherh564', 'MOTO', 'MOTOCICLETA', 'EMPRESA', 'TRASLADO', NULL, '2026-07-10 22:47:15', '2026-07-11 22:06:37', NULL),
(2, 4, 1, 'FGJT65H55H6', '7867867678676', '23423-SGSG', 'NEGRO', 2022, 'sgerger', 'LINEAL', 'L3', 'Empresa', 'TRASLADO', NULL, NULL, '2026-07-12 00:25:12', NULL),
(3, 5, 1, '56H56H566HRTH', 'NEGRO', 'egerger3443', '23423-SGSG', 2022, '34t34g34g', '2025', 'LINEAL', '', 'TRASLADO', NULL, NULL, '2026-07-12 23:37:03', NULL),
(6, 8, 1, 'G54HI776JGJGH', 'sdgsdg', 'sdgfwt345345', 'NEGRO', 2025, 'ASGAGSD', 'LINEAL', 'L3', 'Cliente', 'TRASLADO', NULL, NULL, '2026-07-12 09:02:33', NULL),
(8, 12, 2, '5876867GHKGH', '86786786786', 'AP9-545', 'Rojo', 2026, 'sgerherh564', 'MOTO', 'MOTOCICLETA', 'EMPRESA', 'TRASLADO', NULL, '2026-07-10 22:47:15', '2026-07-12 23:37:51', NULL),
(9, 13, 2, 'FGJT65H55H6', '7867867678676', '23423-SGSG', 'NEGRO', 2022, 'sgerger', 'LINEAL', 'L3', 'Empresa', 'DISPONIBLE', NULL, '2026-07-12 00:25:12', '2026-07-12 00:25:12', NULL),
(10, 14, 2, 'G54HI776JGJGH', 'sdgsdg', 'sdgfwt345345', 'NEGRO', 2025, 'ASGAGSD', 'LINEAL', 'L3', 'Cliente', 'TRASLADO', NULL, '2026-07-12 09:02:33', '2026-07-16 23:03:41', NULL),
(11, 15, 2, '56H56H566HRTH', 'NEGRO', 'egerger3443', '23423-SGSG', 2022, '34t34g34g', '2025', 'LINEAL', '', 'VENDIDO', NULL, '2026-07-12 23:37:03', '2026-07-15 14:57:00', NULL),
(12, 16, 1, '5876867GHKGH', '86786786786', 'AP9-545', 'Rojo', 2026, 'sgerherh564', 'MOTO', 'MOTOCICLETA', 'EMPRESA', 'TRASLADO', NULL, '2026-07-10 22:47:15', '2026-07-13 11:45:23', NULL),
(13, 17, 1, 'FHDY43T4335', 'SG34534543654', 'ASD-454', 'AZUL', NULL, NULL, 'LINEAL', 'L3', 'Empresa', 'TRASLADO', NULL, NULL, '2026-07-12 23:55:19', NULL),
(14, 18, 2, 'FHDY43T4335', 'SG34534543654', 'ASD-454', 'AZUL', NULL, NULL, 'LINEAL', 'L3', 'Empresa', 'TRASLADO', NULL, '2026-07-12 23:55:19', '2026-07-13 00:00:51', NULL),
(15, 19, 1, 'FHDY43T4335', 'SG34534543654', 'ASD-454', 'AZUL', NULL, NULL, 'LINEAL', 'L3', 'Empresa', 'TRASLADO', NULL, '2026-07-12 23:55:19', '2026-07-13 00:10:37', NULL),
(16, 22, 2, 'FHDY43T4335', 'SG34534543654', 'ASD-454', 'AZUL', NULL, NULL, 'LINEAL', 'L3', 'Empresa', 'TRASLADO', NULL, '2026-07-12 23:55:19', '2026-07-16 23:03:41', NULL),
(17, 23, 2, '5876867GHKGH', '86786786786', 'AP9-545', 'Rojo', 2026, 'sgerherh564', 'MOTO', 'MOTOCICLETA', 'EMPRESA', 'TRASLADO', NULL, '2026-07-10 22:47:15', '2026-07-13 13:24:27', NULL),
(18, 24, 1, '5876867GHKGH', '86786786786', 'AP9-545', 'Rojo', 2026, 'sgerherh564', 'MOTO', 'MOTOCICLETA', 'EMPRESA', 'VENDIDO', NULL, '2026-07-10 22:47:15', '2026-07-16 09:01:35', NULL),
(19, 25, 1, 'LYUIYO57RGRE48G', 'DGRHSR5498RH8R', NULL, 'NEGRO', 2025, 'ASGAGSD', 'LINEAL', 'L3', 'Empresa', 'VENDIDO', NULL, NULL, '2026-07-16 09:15:31', NULL),
(20, 26, 1, '8342358676', '23536374747', NULL, 'NEGRO', 2025, 'sgerger', 'LINEAL', 'LINEAL', 'Empresa', 'VENDIDO', NULL, NULL, '2026-07-16 22:18:01', NULL),
(21, 27, 1, 'ergrhrertjhrtj', 'rtjrtjr', NULL, 'NEGRO', 2026, 'jrtjrtj', 'LINEAL', 'L3', 'Empresa', 'VENDIDO', NULL, NULL, '2026-07-16 22:24:49', NULL),
(22, 28, 1, 'G54HI776JGJGH', 'sdgsdg', 'sdgfwt345345', 'NEGRO', 2025, 'ASGAGSD', 'LINEAL', 'L3', 'Cliente', 'VENDIDO', NULL, '2026-07-12 09:02:33', '2026-07-17 19:04:45', NULL),
(23, 29, 1, 'FHDY43T4335', 'SG34534543654', 'ASD-454', 'AZUL', NULL, NULL, 'LINEAL', 'L3', 'Empresa', 'VENDIDO', NULL, '2026-07-12 23:55:19', '2026-07-17 19:01:27', NULL),
(24, 31, 1, NULL, NULL, NULL, 'NEGRO', 0, '', '', '', '', 'DISPONIBLE', NULL, NULL, NULL, NULL),
(25, 32, 1, '912839HIRIIWE23', 'WEKF39030FHUIF', NULL, 'ROJO/VERDE', 2022, 'EWFOWEFJO', 'Lineal', 'L5', 'Empresa', 'MANTENIMIENTO', NULL, NULL, '2026-07-27 17:42:54', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_variaciones`
--

CREATE TABLE `producto_variaciones` (
  `id_variacion` int(11) NOT NULL,
  `idproducto` int(11) DEFAULT NULL,
  `color_nombre` varchar(255) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorio_envios`
--

CREATE TABLE `recordatorio_envios` (
  `id` int(11) NOT NULL,
  `idcpc` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `fecha_envio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `recordatorio_envios`
--

INSERT INTO `recordatorio_envios` (`id`, `idcpc`, `idcliente`, `fecha_envio`) VALUES
(1, 4, 3, '2026-07-04 16:02:19'),
(2, 5, 3, '2026-07-04 16:02:19'),
(3, 20, 3, '2026-07-18 08:19:52'),
(4, 19, 3, '2026-07-18 23:50:50'),
(5, 21, 3, '2026-07-18 23:50:50'),
(6, 22, 3, '2026-07-18 23:50:50'),
(7, 23, 3, '2026-07-18 23:50:50'),
(8, 24, 3, '2026-07-18 23:50:50'),
(9, 25, 3, '2026-07-18 23:50:50'),
(10, 137, 19, '2026-07-28 10:02:56'),
(11, 138, 19, '2026-07-28 10:02:56'),
(12, 139, 19, '2026-07-28 10:02:56'),
(13, 140, 19, '2026-07-28 10:02:56'),
(14, 141, 19, '2026-07-28 10:02:56'),
(15, 142, 19, '2026-07-28 10:02:56'),
(16, 143, 19, '2026-07-28 10:02:56'),
(17, 144, 19, '2026-07-28 10:02:56'),
(18, 145, 19, '2026-07-28 10:02:56'),
(19, 146, 19, '2026-07-28 10:02:56'),
(20, 147, 19, '2026-07-28 10:02:56'),
(21, 148, 19, '2026-07-28 10:02:56'),
(22, 149, 19, '2026-07-28 10:02:56'),
(23, 150, 19, '2026-07-28 10:02:56'),
(24, 151, 19, '2026-07-28 10:02:56'),
(25, 152, 19, '2026-07-28 10:02:56'),
(26, 153, 19, '2026-07-28 10:02:56'),
(27, 154, 19, '2026-07-28 10:02:56'),
(28, 155, 19, '2026-07-28 10:02:56'),
(29, 156, 19, '2026-07-28 10:02:56'),
(30, 157, 19, '2026-07-28 10:02:56'),
(31, 158, 19, '2026-07-28 10:02:56'),
(32, 159, 19, '2026-07-28 10:02:56'),
(33, 160, 19, '2026-07-28 10:02:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacion_documento`
--

CREATE TABLE `recuperacion_documento` (
  `iddocumento` int(11) NOT NULL,
  `idrecuperacion` int(11) NOT NULL,
  `tipo` enum('NOTIFICACION','CARTA_NOTARIAL','ACTA_VISITA','ACTA_ENTREGA','DENUNCIA','PODER','CONTRATO','FOTO','OTRO') NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `archivo` varchar(255) NOT NULL,
  `nombre_original` varchar(255) DEFAULT NULL,
  `idusuario` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `recuperacion_documento`
--

INSERT INTO `recuperacion_documento` (`iddocumento`, `idrecuperacion`, `tipo`, `descripcion`, `archivo`, `nombre_original`, `idusuario`, `fecha_registro`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'DENUNCIA', 'jrtjrtjrkryk', '6a61187d902e5_COTIZACIÓN_PAN (1).docx', 'COTIZACIÓN_PAN (1).docx', 1, '2026-07-22 14:22:37', '2026-07-22 19:22:37', '2026-07-22 19:22:37', NULL),
(2, 1, 'ACTA_VISITA', 'jrsjdj', '6a622be5469ad_Plan de Trabajo.docx', 'Plan de Trabajo.docx', 1, '2026-07-23 09:57:41', '2026-07-23 14:57:41', '2026-07-23 14:57:41', NULL),
(3, 1, 'NOTIFICACION', 'rjrtkr', '6a622c8af057c_Plan de Trabajo.docx', 'Plan de Trabajo.docx', 1, '2026-07-23 10:00:26', '2026-07-23 15:00:26', '2026-07-23 15:00:26', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacion_vehiculo`
--

CREATE TABLE `recuperacion_vehiculo` (
  `idrecuperacion` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `idpersona` int(11) NOT NULL,
  `idserie` int(11) DEFAULT NULL,
  `fecha_registro` date,
  `dias_mora` int(11) DEFAULT NULL,
  `deuda_vencida` decimal(10,2) DEFAULT NULL,
  `nivel_riesgo` enum('BAJO','MEDIO','ALTO','CRITICO') DEFAULT NULL,
  `estado` enum('PENDIENTE','CONTACTADO','NEGOCIACION','VISITA_PROGRAMADA','RECUPERADO','CERRADO') DEFAULT 'PENDIENTE',
  `observacion` text DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `fecha_contacto` date DEFAULT NULL,
  `fecha_ultima_gestion` datetime DEFAULT NULL,
  `resultado_gestion` text DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `recuperacion_vehiculo`
--

INSERT INTO `recuperacion_vehiculo` (`idrecuperacion`, `idventa`, `idpersona`, `idserie`, `fecha_registro`, `dias_mora`, `deuda_vencida`, `nivel_riesgo`, `estado`, `observacion`, `idusuario`, `created_at`, `fecha_contacto`, `fecha_ultima_gestion`, `resultado_gestion`, `updated_at`, `deleted_at`) VALUES
(1, 2, 3, 1, '2026-07-19', 362, 6630.00, 'CRITICO', 'PENDIENTE', 'kdk', 1, '2026-07-19 11:44:59', NULL, NULL, NULL, '2026-07-27 23:34:59', NULL),
(2, 62, 19, 25, '2026-07-27', 351, 7000.00, 'CRITICO', 'RECUPERADO', '', NULL, '2026-07-28 05:53:02', NULL, NULL, NULL, '2026-07-28 04:28:34', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `refinanciamientos`
--

CREATE TABLE `refinanciamientos` (
  `idrefinanciamiento` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `saldo_original` decimal(10,2) DEFAULT NULL,
  `interes` decimal(10,2) DEFAULT NULL,
  `inicial` decimal(10,2) NOT NULL,
  `total_refinanciado` decimal(10,2) NOT NULL,
  `cuotas` int(11) NOT NULL,
  `frecuencia` int(11) NOT NULL,
  `observacion` text DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas`
--

CREATE TABLE `resenas` (
  `id_resena` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `calificacion` int(11) NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resumen_diario`
--

CREATE TABLE `resumen_diario` (
  `idresumen` int(11) NOT NULL,
  `fecha_generacion` date NOT NULL,
  `fecha_envio` datetime NOT NULL,
  `correlativo` int(11) NOT NULL,
  `ticket` varchar(50) DEFAULT NULL,
  `nombre_xml` varchar(100) DEFAULT NULL,
  `idsucursal` int(11) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `respuesta_sunat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resumen_diario_detalle`
--

CREATE TABLE `resumen_diario_detalle` (
  `iddetalle` int(11) NOT NULL,
  `idresumen` int(11) NOT NULL,
  `idventa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retenciones`
--

CREATE TABLE `retenciones` (
  `idretencion` int(11) NOT NULL,
  `motivo` varchar(500) NOT NULL,
  `fecha` datetime NOT NULL,
  `idventa` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `retenciones`
--

INSERT INTO `retenciones` (`idretencion`, `motivo`, `fecha`, `idventa`, `estado`, `created_at`, `updated_at`, `deleted_at`) VALUES
(9, 'cxdf', '2026-07-28 14:21:13', 2, 0, '2026-07-28 14:21:13', '2026-07-28 14:21:13', NULL),
(10, 'cxdfkjhjhk', '2026-07-28 14:22:14', 2, 0, '2026-07-28 14:22:14', '2026-07-28 14:24:00', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rubro`
--

CREATE TABLE `rubro` (
  `idrubro` int(11) NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `condicion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `rubro`
--

INSERT INTO `rubro` (`idrubro`, `nombre`, `condicion`) VALUES
(1, 'MOTOCICLETA', 1),
(2, 'Motores', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_adjuntos`
--

CREATE TABLE `seguimiento_adjuntos` (
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `idadjunto` int(11) NOT NULL,
  `idseguimiento` int(11) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `nombre_original` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `seguimiento_adjuntos`
--

INSERT INTO `seguimiento_adjuntos` (`created_at`, `updated_at`, `idadjunto`, `idseguimiento`, `archivo`, `nombre_original`, `fecha_registro`, `deleted_at`) VALUES
('2026-07-10 01:58:44', NULL, 1, 1, '20260710012056_6a508f48d870f.jpeg', 'WhatsApp Image 2026-03-13 at 4.09.53 PM.jpeg', '2026-07-10 01:20:56', NULL),
('2026-07-18 09:29:07', '2026-07-18 09:29:07', 2, 3, '20260718092907_6a5b8db330203.jpeg', 'WhatsApp Image 2026-03-13 at 3.50.17 PM (1).jpeg', '2026-07-18 09:29:07', NULL),
('2026-07-18 09:29:44', NULL, 3, 3, '20260718092944_6a5b8dd8ae9ac.jpeg', 'WhatsApp Image 2026-05-14 at 11.13.00 AM.jpeg', '2026-07-18 09:29:44', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_clientes`
--

CREATE TABLE `seguimiento_clientes` (
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `idseguimiento` int(11) NOT NULL,
  `idventa` int(11) DEFAULT NULL,
  `idrecuperacion` int(11) DEFAULT NULL,
  `iddocumento` int(11) DEFAULT NULL,
  `idcpc` int(11) DEFAULT NULL,
  `idcliente` int(11) DEFAULT NULL,
  `idpersonal` int(11) NOT NULL,
  `tipo` enum('LLAMADA','VISITA','WHATSAPP','CORREO','COBRANZA','SUSTENTO','OTRO') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `fecha_proxima` datetime DEFAULT NULL,
  `fecha_final` datetime DEFAULT NULL,
  `idusuario` int(11) NOT NULL,
  `estado` enum('PENDIENTE','REALIZADO','NO_RESPONDE','REPROGRAMADO') DEFAULT 'REALIZADO',
  `prioridad` varchar(20) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `seguimiento_clientes`
--

INSERT INTO `seguimiento_clientes` (`created_at`, `updated_at`, `idseguimiento`, `idventa`, `idrecuperacion`, `iddocumento`, `idcpc`, `idcliente`, `idpersonal`, `tipo`, `descripcion`, `fecha_registro`, `fecha_proxima`, `fecha_final`, `idusuario`, `estado`, `prioridad`, `direccion`, `deleted_at`) VALUES
('2026-07-10 01:59:48', NULL, 1, 2, NULL, 2, 19, 3, 1, 'COBRANZA', '', '2026-07-10 01:20:56', '2026-07-10 01:20:00', '2026-07-10 01:20:00', 1, 'REALIZADO', 'MEDIA', 'Plaza de Nueva cajamarca Pl. Mayor 453, Tarapoto 22202, Peru', NULL),
('2026-07-10 01:59:52', '2026-07-10 01:59:52', 2, 2, NULL, 2, 19, 3, 1, 'LLAMADA', '', '2026-07-10 01:59:52', '2026-07-10 01:52:00', '2026-07-10 12:52:00', 1, 'REALIZADO', 'MEDIA', 'Plaza de Nueva cajamarca Pl. Mayor 453, Tarapoto 22202, Peru', NULL),
('2026-07-18 09:29:07', '2026-07-18 09:29:07', 3, 2, NULL, 2, 20, 3, 1, 'COBRANZA', 'No respondio el cliente', '2026-07-18 09:29:07', '2026-07-18 12:28:00', '2026-07-18 13:28:00', 1, 'REALIZADO', 'MEDIA', 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', NULL),
('2026-07-21 17:53:10', '2026-07-21 17:53:10', 4, 2, 1, 2, NULL, 3, 2, 'COBRANZA', 'Cliente no responde por 5ta vez', '2026-07-21 17:53:10', '2026-07-22 17:52:00', NULL, 1, 'REALIZADO', 'MEDIA', 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `idservicio` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `tipo_comprobante` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `serie_comprobante` varchar(7) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `num_comprobante` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `idcliente` int(11) NOT NULL,
  `equipo` varchar(100) DEFAULT NULL,
  `idtecnico` int(11) NOT NULL,
  `fecha_ingreso` datetime DEFAULT NULL,
  `fecha_reparacion` datetime DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `total` decimal(11,2) NOT NULL,
  `descripcion_problema` text DEFAULT NULL,
  `descripcion_solucion` text DEFAULT NULL,
  `estado` enum('Recibido','En proceso','Terminado','Entregado') DEFAULT 'Recibido'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_credito`
--

CREATE TABLE `solicitud_credito` (
  `idsolicitud` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `codigo` varchar(20) DEFAULT NULL,
  `idcliente` int(11) NOT NULL,
  `idcotizacion` int(11) NOT NULL,
  `score` int(11) DEFAULT 0,
  `riesgo` enum('BAJO','MEDIO','ALTO','CRITICO') DEFAULT 'MEDIO',
  `estado` enum('BORRADOR','EN_PROCESO','OBSERVADO','PENDIENTE_DOCUMENTOS','APROBADO','RECHAZADO','ANULADO') DEFAULT 'BORRADOR',
  `paso_actual` int(11) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL,
  `idusuario` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `solicitud_credito`
--

INSERT INTO `solicitud_credito` (`idsolicitud`, `idsucursal`, `created_at`, `codigo`, `idcliente`, `idcotizacion`, `score`, `riesgo`, `estado`, `paso_actual`, `fecha_registro`, `fecha_actualizacion`, `idusuario`, `deleted_at`) VALUES
(2, 1, '2026-07-01 23:25:04', 'SOL-20260702062504', 3, 1, 0, '', 'APROBADO', 5, '2026-07-01 23:25:04', '2026-07-01 23:27:10', 1, NULL),
(3, 1, '2026-07-05 23:32:29', 'SOL-20260706063229', 3, 2, 20, 'BAJO', 'APROBADO', 5, '2026-07-05 23:32:29', '2026-07-08 21:43:41', 1, NULL),
(4, 2, '2026-07-15 10:04:08', 'SOL-20260715170408', 15, 7, 0, '', 'APROBADO', 5, '2026-07-15 10:04:08', '2026-07-15 10:05:00', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_documento`
--

CREATE TABLE `solicitud_documento` (
  `iddocumento` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `tipo_documento` varchar(100) DEFAULT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `nombre_original` varchar(255) DEFAULT NULL,
  `descripcion` varchar(200) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `solicitud_documento`
--

INSERT INTO `solicitud_documento` (`iddocumento`, `idsolicitud`, `tipo_documento`, `archivo`, `nombre_original`, `descripcion`, `fecha_registro`) VALUES
(1, 2, 'Documento de crédito', 'sol_2_1782966404_6555.js', 'solicitudes.js', 'Copia dni', '2026-07-01 23:26:44'),
(2, 3, 'Documento de crédito', 'sol_3_1783564980_9034.docx', 'COTIZACIÓN_PAN (1).docx', 'Copia de DNI', '2026-07-08 21:43:00'),
(3, 4, 'Documento de crédito', 'sol_4_1784127876_9259.png', '81d9c978-ce93-4f7e-9533-a1ae86c884a3.png', 'Copid de dni', '2026-07-15 10:04:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_evaluacion`
--

CREATE TABLE `solicitud_evaluacion` (
  `idevaluacion` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `ingreso_mensual` decimal(12,2) DEFAULT NULL,
  `egreso_mensual` decimal(12,2) DEFAULT NULL,
  `capacidad_pago` decimal(12,2) DEFAULT NULL,
  `inicial_validada` decimal(12,2) DEFAULT NULL,
  `score_manual` int(11) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `notas_comite` varchar(200) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `solicitud_evaluacion`
--

INSERT INTO `solicitud_evaluacion` (`idevaluacion`, `idsolicitud`, `ingreso_mensual`, `egreso_mensual`, `capacidad_pago`, `inicial_validada`, `score_manual`, `observacion`, `notas_comite`, `fecha_registro`) VALUES
(1, 2, 3000.00, 0.00, 3000.00, 0.00, 0, 'Primer credito', 'Aprobado por unanimidad', '2026-07-01 23:25:04'),
(2, 3, 2000.00, 0.00, 2000.00, 0.00, 20, '', '', '2026-07-05 23:32:29'),
(3, 4, 3000.00, 0.00, 3000.00, 0.00, 0, '', 'Completo', '2026-07-15 10:04:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_workflow`
--

CREATE TABLE `solicitud_workflow` (
  `idworkflow` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `idpaso` int(11) NOT NULL,
  `fecha_inicio` datetime DEFAULT current_timestamp(),
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('PENDIENTE','EN_PROCESO','APROBADO','OBSERVADO','RECHAZADO') DEFAULT 'PENDIENTE',
  `observacion` text DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `solicitud_workflow`
--

INSERT INTO `solicitud_workflow` (`idworkflow`, `idsolicitud`, `idpaso`, `fecha_inicio`, `fecha_fin`, `estado`, `observacion`, `idusuario`) VALUES
(2, 2, 1, '2026-07-01 23:25:04', '2026-07-01 23:26:21', 'APROBADO', 'Solicitud creada', 1),
(3, 2, 2, '2026-07-01 23:26:21', '2026-07-01 23:26:46', 'APROBADO', 'Documentación cargada', 1),
(4, 2, 3, '2026-07-01 23:26:46', '2026-07-01 23:26:55', 'APROBADO', 'Documentación aprobada', 1),
(5, 2, 4, '2026-07-01 23:26:55', '2026-07-01 23:27:10', 'APROBADO', 'Verificacion domiciliaria: CONFORME. ', 1),
(6, 2, 5, '2026-07-01 23:27:10', '0000-00-00 00:00:00', 'APROBADO', 'Enviado a aprobación final', 1),
(7, 3, 1, '2026-07-05 23:32:29', '2026-07-08 21:42:33', 'APROBADO', 'Solicitud creada', 1),
(8, 3, 2, '2026-07-08 21:42:33', '2026-07-08 21:43:20', 'APROBADO', 'Documentación cargada', 1),
(9, 3, 3, '2026-07-08 21:43:20', '2026-07-08 21:43:29', 'APROBADO', 'Documentación aprobada', 1),
(10, 3, 4, '2026-07-08 21:43:29', '2026-07-08 21:43:41', 'APROBADO', 'Verificacion domiciliaria: CONFORME. ', 1),
(11, 3, 5, '2026-07-08 21:43:41', '0000-00-00 00:00:00', 'APROBADO', 'Enviado a aprobación final', 1),
(12, 4, 1, '2026-07-15 10:04:08', '2026-07-15 10:04:16', 'APROBADO', 'Solicitud creada', 1),
(13, 4, 2, '2026-07-15 10:04:16', '2026-07-15 10:04:39', 'APROBADO', 'Documentación cargada', 1),
(14, 4, 3, '2026-07-15 10:04:39', '2026-07-15 10:04:47', 'APROBADO', 'Documentación aprobada', 1),
(15, 4, 4, '2026-07-15 10:04:47', '2026-07-15 10:05:00', 'APROBADO', 'Verificacion domiciliaria: CONFORME. rthrhrth', 1),
(16, 4, 5, '2026-07-15 10:05:00', '0000-00-00 00:00:00', 'APROBADO', 'Enviado a aprobación final', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_fifo`
--

CREATE TABLE `stock_fifo` (
  `idfifo` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `producto_configuracion_id` int(11) DEFAULT NULL,
  `origen` enum('ALMACEN','COMPRA') NOT NULL,
  `referencia_id` int(11) DEFAULT NULL,
  `cantidad_ingreso` decimal(11,2) NOT NULL,
  `cantidad_restante` decimal(11,2) NOT NULL,
  `precio_compra` decimal(11,2) NOT NULL,
  `precio_venta` decimal(11,2) NOT NULL,
  `fecha_ingreso` datetime NOT NULL,
  `estado` tinyint(4) DEFAULT 1,
  `fvencimiento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subpermiso`
--

CREATE TABLE `subpermiso` (
  `idsubpermiso` int(11) NOT NULL,
  `idpermiso` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `subpermiso`
--

INSERT INTO `subpermiso` (`idsubpermiso`, `idpermiso`, `nombre`) VALUES
(1, 1, 'Contratos'),
(2, 1, 'Solicitudes'),
(3, 1, 'Punto de venta'),
(4, 1, 'Guia de remision'),
(5, 1, 'Cotizaciones'),
(6, 1, 'Notas de credito'),
(7, 2, 'Mora'),
(8, 2, 'Credito'),
(9, 2, 'Refinanciamiento'),
(10, 4, 'Comprobantes'),
(11, 4, 'Resumen diario'),
(12, 4, 'Cajas'),
(13, 5, 'Productos'),
(14, 5, 'Servicios'),
(15, 5, 'Nombres precios'),
(16, 5, 'Categorias'),
(17, 5, 'Marcas'),
(18, 5, 'Modelos'),
(19, 5, 'Lineas'),
(20, 5, 'Condicion de venta'),
(21, 5, 'Unidad de medida'),
(22, 5, 'Traslados'),
(23, 5, 'Reportes'),
(24, 5, 'Vencimientos'),
(25, 6, 'Toma de nventario'),
(26, 6, 'Ajuste de nventario'),
(27, 7, 'Crear compras'),
(28, 7, 'Proveedores'),
(29, 9, 'Cuentas por cobrar'),
(30, 9, 'Refinanciar creditos'),
(31, 12, 'Asistencia'),
(32, 12, 'Personal'),
(33, 12, 'Usuarios'),
(34, 12, 'Permisos'),
(35, 13, 'Datos generales'),
(36, 13, 'Facturadores'),
(37, 13, 'Sucursales'),
(38, 14, 'Compras'),
(39, 14, 'Compras por proveedor'),
(40, 15, 'Ventas por cliente'),
(41, 15, 'Ventas por vendedor'),
(44, 15, 'Creditos - utilidades'),
(45, 15, 'Reporte consolidado'),
(46, 15, 'Ventas - utilidades'),
(47, 15, 'Ventas por servicio'),
(48, 15, 'Ventas detalle'),
(49, 3, 'Clientes'),
(50, 13, 'Configuracion general'),
(51, 13, 'Configuracion facturacion'),
(52, 13, 'Configuracion mora'),
(54, 13, 'Configuracion credito'),
(55, 13, 'Configuracion refinanciamiento');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `idsucursal` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `direccion` varchar(250) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `departamento` varchar(60) DEFAULT NULL,
  `provincia` varchar(60) DEFAULT NULL,
  `distrito` varchar(60) DEFAULT NULL,
  `ubigeo` char(10) DEFAULT NULL,
  `moneda` varchar(10) DEFAULT NULL,
  `simbolo` varchar(10) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `idempresa` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sucursal`
--

INSERT INTO `sucursal` (`idsucursal`, `nombre`, `direccion`, `telefono`, `email`, `departamento`, `provincia`, `distrito`, `ubigeo`, `moneda`, `simbolo`, `logo`, `idempresa`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'JIMENEZ 2', 'jr jimenez pimentel 345', '933216752', 'alexhe406@gmail.com', 'San Martín', 'San Martín ', 'Morales', '220910', 'PEN', 'S/', NULL, 1, '2026-07-14 12:40:21', '2026-07-18 04:58:08', NULL),
(2, 'Nueva cajamarca', 'S/N', '985652456', '', '', '', '', NULL, 'PEN', 'S/', NULL, 1, '2026-07-14 12:40:21', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal_configuracion`
--

CREATE TABLE `sucursal_configuracion` (
  `idsucursal_configuracion` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `is_refinanciamiento` tinyint(1) NOT NULL DEFAULT 0,
  `maximo_refinanciamientos` int(11) NOT NULL DEFAULT 1,
  `is_mora_credito` tinyint(1) DEFAULT 0,
  `valor_mora_credito` decimal(10,2) NOT NULL,
  `is_notificacion` tinyint(1) DEFAULT 0,
  `dias_gracia` int(11) DEFAULT NULL,
  `interes_defecto` decimal(12,2) DEFAULT NULL,
  `is_descuento_anticipado` tinyint(1) DEFAULT 0,
  `valor_descuento_anticipado` decimal(10,2) DEFAULT NULL,
  `dias_anticipacion` int(11) DEFAULT 0,
  `is_send_sunat` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `sucursal_configuracion`
--

INSERT INTO `sucursal_configuracion` (`idsucursal_configuracion`, `idsucursal`, `is_refinanciamiento`, `maximo_refinanciamientos`, `is_mora_credito`, `valor_mora_credito`, `is_notificacion`, `dias_gracia`, `interes_defecto`, `is_descuento_anticipado`, `valor_descuento_anticipado`, `dias_anticipacion`, `is_send_sunat`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 1, 1.00, 1, 0, 10.00, 1, 1.00, 2, 0, '2026-07-16 15:59:49', '2026-07-17 03:09:05', NULL),
(2, 2, 0, 1, 0, 0.00, 0, 0, 10.00, 0, 1.00, 0, 0, '2026-07-16 15:59:49', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temp_detalle_venta`
--

CREATE TABLE `temp_detalle_venta` (
  `token` varchar(100) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `producto` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `contenedor` varchar(100) NOT NULL,
  `cantidad_contenedor` int(11) NOT NULL,
  `cantidad` decimal(11,2) NOT NULL,
  `precio` double NOT NULL,
  `id_fifo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoacompanante`
--

CREATE TABLE `tipoacompanante` (
  `idtipoacompanante` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `abeviacion` varchar(5) DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traslado`
--

CREATE TABLE `traslado` (
  `idtraslado` int(11) NOT NULL,
  `idorigen` int(11) NOT NULL,
  `iddestino` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `correlativo` varchar(50) NOT NULL,
  `estado` enum('pendiente','en_transito','rechazado','recibido','cancelado') NOT NULL DEFAULT 'pendiente',
  `fecha_aceptacion` datetime DEFAULT NULL,
  `idusuario_acepta` int(11) DEFAULT NULL,
  `tipo` enum('solicitud','traslado') DEFAULT 'traslado',
  `idsolicitud_origen` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `traslado`
--

INSERT INTO `traslado` (`idtraslado`, `idorigen`, `iddestino`, `idusuario`, `fecha`, `correlativo`, `estado`, `fecha_aceptacion`, `idusuario_acepta`, `tipo`, `idsolicitud_origen`, `created_at`, `updated_at`, `deleted_at`) VALUES
(50, 2, 1, 1, '2026-07-13 13:32:20', 'PDO-TR-0000001', 'recibido', '2026-07-16 23:03:41', 1, 'traslado', NULL, '2026-07-13 13:32:20', '2026-07-16 23:03:41', NULL),
(51, 1, 2, 1, '2026-07-13 14:48:14', 'PDO-SL-0000001', 'pendiente', NULL, NULL, 'solicitud', NULL, '2026-07-13 14:48:14', '2026-07-13 14:48:14', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traslado_detalle`
--

CREATE TABLE `traslado_detalle` (
  `iddetalle` int(11) NOT NULL,
  `idtraslado` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `idserie` int(11) DEFAULT NULL,
  `cantidad_enviada` decimal(10,2) DEFAULT NULL,
  `cantidad_recibida` decimal(10,2) DEFAULT NULL,
  `estado_detalle` enum('pendiente','aceptado','rechazado') DEFAULT 'pendiente',
  `observacion` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `traslado_detalle`
--

INSERT INTO `traslado_detalle` (`iddetalle`, `idtraslado`, `idproducto`, `idserie`, `cantidad_enviada`, `cantidad_recibida`, `estado_detalle`, `observacion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(45, 50, 14, 10, 1.00, 1.00, 'aceptado', '', '2026-07-13 13:32:20', '2026-07-16 23:03:41', NULL),
(46, 50, 22, 16, 1.00, 1.00, 'aceptado', '', '2026-07-13 13:32:20', '2026-07-16 23:03:41', NULL),
(47, 51, 22, 16, 1.00, NULL, 'pendiente', '', '2026-07-13 14:48:14', '2026-07-13 14:48:14', NULL),
(48, 51, 14, 10, 1.00, NULL, 'pendiente', '', '2026-07-13 14:48:14', '2026-07-13 14:48:14', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubigeo_peru_departments`
--

CREATE TABLE `ubigeo_peru_departments` (
  `id` varchar(2) NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `ubigeo_peru_departments`
--

INSERT INTO `ubigeo_peru_departments` (`id`, `name`) VALUES
('01', 'Amazonas'),
('02', 'Áncash'),
('03', 'Apurímac'),
('04', 'Arequipa'),
('05', 'Ayacucho'),
('06', 'Cajamarca'),
('07', 'Callao'),
('08', 'Cusco'),
('09', 'Huancavelica'),
('10', 'Huánuco'),
('11', 'Ica'),
('12', 'Junín'),
('13', 'La Libertad'),
('14', 'Lambayeque'),
('15', 'Lima'),
('16', 'Loreto'),
('17', 'Madre de Dios'),
('18', 'Moquegua'),
('19', 'Pasco'),
('20', 'Piura'),
('21', 'Puno'),
('22', 'San Martín'),
('23', 'Tacna'),
('24', 'Tumbes'),
('25', 'Ucayali');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubigeo_peru_districts`
--

CREATE TABLE `ubigeo_peru_districts` (
  `id` varchar(6) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `province_id` varchar(4) DEFAULT NULL,
  `department_id` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `ubigeo_peru_districts`
--

INSERT INTO `ubigeo_peru_districts` (`id`, `name`, `province_id`, `department_id`) VALUES
('010101', 'Chachapoyas', '0101', '01'),
('010102', 'Asunción', '0101', '01'),
('010103', 'Balsas', '0101', '01'),
('010104', 'Cheto', '0101', '01'),
('010105', 'Chiliquin', '0101', '01'),
('010106', 'Chuquibamba', '0101', '01'),
('010107', 'Granada', '0101', '01'),
('010108', 'Huancas', '0101', '01'),
('010109', 'La Jalca', '0101', '01'),
('010110', 'Leimebamba', '0101', '01'),
('010111', 'Levanto', '0101', '01'),
('010112', 'Magdalena', '0101', '01'),
('010113', 'Mariscal Castilla', '0101', '01'),
('010114', 'Molinopampa', '0101', '01'),
('010115', 'Montevideo', '0101', '01'),
('010116', 'Olleros', '0101', '01'),
('010117', 'Quinjalca', '0101', '01'),
('010118', 'San Francisco de Daguas', '0101', '01'),
('010119', 'San Isidro de Maino', '0101', '01'),
('010120', 'Soloco', '0101', '01'),
('010121', 'Sonche', '0101', '01'),
('010201', 'Bagua', '0102', '01'),
('010202', 'Aramango', '0102', '01'),
('010203', 'Copallin', '0102', '01'),
('010204', 'El Parco', '0102', '01'),
('010205', 'Imaza', '0102', '01'),
('010206', 'La Peca', '0102', '01'),
('010301', 'Jumbilla', '0103', '01'),
('010302', 'Chisquilla', '0103', '01'),
('010303', 'Churuja', '0103', '01'),
('010304', 'Corosha', '0103', '01'),
('010305', 'Cuispes', '0103', '01'),
('010306', 'Florida', '0103', '01'),
('010307', 'Jazan', '0103', '01'),
('010308', 'Recta', '0103', '01'),
('010309', 'San Carlos', '0103', '01'),
('010310', 'Shipasbamba', '0103', '01'),
('010311', 'Valera', '0103', '01'),
('010312', 'Yambrasbamba', '0103', '01'),
('010401', 'Nieva', '0104', '01'),
('010402', 'El Cenepa', '0104', '01'),
('010403', 'Río Santiago', '0104', '01'),
('010501', 'Lamud', '0105', '01'),
('010502', 'Camporredondo', '0105', '01'),
('010503', 'Cocabamba', '0105', '01'),
('010504', 'Colcamar', '0105', '01'),
('010505', 'Conila', '0105', '01'),
('010506', 'Inguilpata', '0105', '01'),
('010507', 'Longuita', '0105', '01'),
('010508', 'Lonya Chico', '0105', '01'),
('010509', 'Luya', '0105', '01'),
('010510', 'Luya Viejo', '0105', '01'),
('010511', 'María', '0105', '01'),
('010512', 'Ocalli', '0105', '01'),
('010513', 'Ocumal', '0105', '01'),
('010514', 'Pisuquia', '0105', '01'),
('010515', 'Providencia', '0105', '01'),
('010516', 'San Cristóbal', '0105', '01'),
('010517', 'San Francisco del Yeso', '0105', '01'),
('010518', 'San Jerónimo', '0105', '01'),
('010519', 'San Juan de Lopecancha', '0105', '01'),
('010520', 'Santa Catalina', '0105', '01'),
('010521', 'Santo Tomas', '0105', '01'),
('010522', 'Tingo', '0105', '01'),
('010523', 'Trita', '0105', '01'),
('010601', 'San Nicolás', '0106', '01'),
('010602', 'Chirimoto', '0106', '01'),
('010603', 'Cochamal', '0106', '01'),
('010604', 'Huambo', '0106', '01'),
('010605', 'Limabamba', '0106', '01'),
('010606', 'Longar', '0106', '01'),
('010607', 'Mariscal Benavides', '0106', '01'),
('010608', 'Milpuc', '0106', '01'),
('010609', 'Omia', '0106', '01'),
('010610', 'Santa Rosa', '0106', '01'),
('010611', 'Totora', '0106', '01'),
('010612', 'Vista Alegre', '0106', '01'),
('010701', 'Bagua Grande', '0107', '01'),
('010702', 'Cajaruro', '0107', '01'),
('010703', 'Cumba', '0107', '01'),
('010704', 'El Milagro', '0107', '01'),
('010705', 'Jamalca', '0107', '01'),
('010706', 'Lonya Grande', '0107', '01'),
('010707', 'Yamon', '0107', '01'),
('020101', 'Huaraz', '0201', '02'),
('020102', 'Cochabamba', '0201', '02'),
('020103', 'Colcabamba', '0201', '02'),
('020104', 'Huanchay', '0201', '02'),
('020105', 'Independencia', '0201', '02'),
('020106', 'Jangas', '0201', '02'),
('020107', 'La Libertad', '0201', '02'),
('020108', 'Olleros', '0201', '02'),
('020109', 'Pampas Grande', '0201', '02'),
('020110', 'Pariacoto', '0201', '02'),
('020111', 'Pira', '0201', '02'),
('020112', 'Tarica', '0201', '02'),
('020201', 'Aija', '0202', '02'),
('020202', 'Coris', '0202', '02'),
('020203', 'Huacllan', '0202', '02'),
('020204', 'La Merced', '0202', '02'),
('020205', 'Succha', '0202', '02'),
('020301', 'Llamellin', '0203', '02'),
('020302', 'Aczo', '0203', '02'),
('020303', 'Chaccho', '0203', '02'),
('020304', 'Chingas', '0203', '02'),
('020305', 'Mirgas', '0203', '02'),
('020306', 'San Juan de Rontoy', '0203', '02'),
('020401', 'Chacas', '0204', '02'),
('020402', 'Acochaca', '0204', '02'),
('020501', 'Chiquian', '0205', '02'),
('020502', 'Abelardo Pardo Lezameta', '0205', '02'),
('020503', 'Antonio Raymondi', '0205', '02'),
('020504', 'Aquia', '0205', '02'),
('020505', 'Cajacay', '0205', '02'),
('020506', 'Canis', '0205', '02'),
('020507', 'Colquioc', '0205', '02'),
('020508', 'Huallanca', '0205', '02'),
('020509', 'Huasta', '0205', '02'),
('020510', 'Huayllacayan', '0205', '02'),
('020511', 'La Primavera', '0205', '02'),
('020512', 'Mangas', '0205', '02'),
('020513', 'Pacllon', '0205', '02'),
('020514', 'San Miguel de Corpanqui', '0205', '02'),
('020515', 'Ticllos', '0205', '02'),
('020601', 'Carhuaz', '0206', '02'),
('020602', 'Acopampa', '0206', '02'),
('020603', 'Amashca', '0206', '02'),
('020604', 'Anta', '0206', '02'),
('020605', 'Ataquero', '0206', '02'),
('020606', 'Marcara', '0206', '02'),
('020607', 'Pariahuanca', '0206', '02'),
('020608', 'San Miguel de Aco', '0206', '02'),
('020609', 'Shilla', '0206', '02'),
('020610', 'Tinco', '0206', '02'),
('020611', 'Yungar', '0206', '02'),
('020701', 'San Luis', '0207', '02'),
('020702', 'San Nicolás', '0207', '02'),
('020703', 'Yauya', '0207', '02'),
('020801', 'Casma', '0208', '02'),
('020802', 'Buena Vista Alta', '0208', '02'),
('020803', 'Comandante Noel', '0208', '02'),
('020804', 'Yautan', '0208', '02'),
('020901', 'Corongo', '0209', '02'),
('020902', 'Aco', '0209', '02'),
('020903', 'Bambas', '0209', '02'),
('020904', 'Cusca', '0209', '02'),
('020905', 'La Pampa', '0209', '02'),
('020906', 'Yanac', '0209', '02'),
('020907', 'Yupan', '0209', '02'),
('021001', 'Huari', '0210', '02'),
('021002', 'Anra', '0210', '02'),
('021003', 'Cajay', '0210', '02'),
('021004', 'Chavin de Huantar', '0210', '02'),
('021005', 'Huacachi', '0210', '02'),
('021006', 'Huacchis', '0210', '02'),
('021007', 'Huachis', '0210', '02'),
('021008', 'Huantar', '0210', '02'),
('021009', 'Masin', '0210', '02'),
('021010', 'Paucas', '0210', '02'),
('021011', 'Ponto', '0210', '02'),
('021012', 'Rahuapampa', '0210', '02'),
('021013', 'Rapayan', '0210', '02'),
('021014', 'San Marcos', '0210', '02'),
('021015', 'San Pedro de Chana', '0210', '02'),
('021016', 'Uco', '0210', '02'),
('021101', 'Huarmey', '0211', '02'),
('021102', 'Cochapeti', '0211', '02'),
('021103', 'Culebras', '0211', '02'),
('021104', 'Huayan', '0211', '02'),
('021105', 'Malvas', '0211', '02'),
('021201', 'Caraz', '0212', '02'),
('021202', 'Huallanca', '0212', '02'),
('021203', 'Huata', '0212', '02'),
('021204', 'Huaylas', '0212', '02'),
('021205', 'Mato', '0212', '02'),
('021206', 'Pamparomas', '0212', '02'),
('021207', 'Pueblo Libre', '0212', '02'),
('021208', 'Santa Cruz', '0212', '02'),
('021209', 'Santo Toribio', '0212', '02'),
('021210', 'Yuracmarca', '0212', '02'),
('021301', 'Piscobamba', '0213', '02'),
('021302', 'Casca', '0213', '02'),
('021303', 'Eleazar Guzmán Barron', '0213', '02'),
('021304', 'Fidel Olivas Escudero', '0213', '02'),
('021305', 'Llama', '0213', '02'),
('021306', 'Llumpa', '0213', '02'),
('021307', 'Lucma', '0213', '02'),
('021308', 'Musga', '0213', '02'),
('021401', 'Ocros', '0214', '02'),
('021402', 'Acas', '0214', '02'),
('021403', 'Cajamarquilla', '0214', '02'),
('021404', 'Carhuapampa', '0214', '02'),
('021405', 'Cochas', '0214', '02'),
('021406', 'Congas', '0214', '02'),
('021407', 'Llipa', '0214', '02'),
('021408', 'San Cristóbal de Rajan', '0214', '02'),
('021409', 'San Pedro', '0214', '02'),
('021410', 'Santiago de Chilcas', '0214', '02'),
('021501', 'Cabana', '0215', '02'),
('021502', 'Bolognesi', '0215', '02'),
('021503', 'Conchucos', '0215', '02'),
('021504', 'Huacaschuque', '0215', '02'),
('021505', 'Huandoval', '0215', '02'),
('021506', 'Lacabamba', '0215', '02'),
('021507', 'Llapo', '0215', '02'),
('021508', 'Pallasca', '0215', '02'),
('021509', 'Pampas', '0215', '02'),
('021510', 'Santa Rosa', '0215', '02'),
('021511', 'Tauca', '0215', '02'),
('021601', 'Pomabamba', '0216', '02'),
('021602', 'Huayllan', '0216', '02'),
('021603', 'Parobamba', '0216', '02'),
('021604', 'Quinuabamba', '0216', '02'),
('021701', 'Recuay', '0217', '02'),
('021702', 'Catac', '0217', '02'),
('021703', 'Cotaparaco', '0217', '02'),
('021704', 'Huayllapampa', '0217', '02'),
('021705', 'Llacllin', '0217', '02'),
('021706', 'Marca', '0217', '02'),
('021707', 'Pampas Chico', '0217', '02'),
('021708', 'Pararin', '0217', '02'),
('021709', 'Tapacocha', '0217', '02'),
('021710', 'Ticapampa', '0217', '02'),
('021801', 'Chimbote', '0218', '02'),
('021802', 'Cáceres del Perú', '0218', '02'),
('021803', 'Coishco', '0218', '02'),
('021804', 'Macate', '0218', '02'),
('021805', 'Moro', '0218', '02'),
('021806', 'Nepeña', '0218', '02'),
('021807', 'Samanco', '0218', '02'),
('021808', 'Santa', '0218', '02'),
('021809', 'Nuevo Chimbote', '0218', '02'),
('021901', 'Sihuas', '0219', '02'),
('021902', 'Acobamba', '0219', '02'),
('021903', 'Alfonso Ugarte', '0219', '02'),
('021904', 'Cashapampa', '0219', '02'),
('021905', 'Chingalpo', '0219', '02'),
('021906', 'Huayllabamba', '0219', '02'),
('021907', 'Quiches', '0219', '02'),
('021908', 'Ragash', '0219', '02'),
('021909', 'San Juan', '0219', '02'),
('021910', 'Sicsibamba', '0219', '02'),
('022001', 'Yungay', '0220', '02'),
('022002', 'Cascapara', '0220', '02'),
('022003', 'Mancos', '0220', '02'),
('022004', 'Matacoto', '0220', '02'),
('022005', 'Quillo', '0220', '02'),
('022006', 'Ranrahirca', '0220', '02'),
('022007', 'Shupluy', '0220', '02'),
('022008', 'Yanama', '0220', '02'),
('030101', 'Abancay', '0301', '03'),
('030102', 'Chacoche', '0301', '03'),
('030103', 'Circa', '0301', '03'),
('030104', 'Curahuasi', '0301', '03'),
('030105', 'Huanipaca', '0301', '03'),
('030106', 'Lambrama', '0301', '03'),
('030107', 'Pichirhua', '0301', '03'),
('030108', 'San Pedro de Cachora', '0301', '03'),
('030109', 'Tamburco', '0301', '03'),
('030201', 'Andahuaylas', '0302', '03'),
('030202', 'Andarapa', '0302', '03'),
('030203', 'Chiara', '0302', '03'),
('030204', 'Huancarama', '0302', '03'),
('030205', 'Huancaray', '0302', '03'),
('030206', 'Huayana', '0302', '03'),
('030207', 'Kishuara', '0302', '03'),
('030208', 'Pacobamba', '0302', '03'),
('030209', 'Pacucha', '0302', '03'),
('030210', 'Pampachiri', '0302', '03'),
('030211', 'Pomacocha', '0302', '03'),
('030212', 'San Antonio de Cachi', '0302', '03'),
('030213', 'San Jerónimo', '0302', '03'),
('030214', 'San Miguel de Chaccrampa', '0302', '03'),
('030215', 'Santa María de Chicmo', '0302', '03'),
('030216', 'Talavera', '0302', '03'),
('030217', 'Tumay Huaraca', '0302', '03'),
('030218', 'Turpo', '0302', '03'),
('030219', 'Kaquiabamba', '0302', '03'),
('030220', 'José María Arguedas', '0302', '03'),
('030301', 'Antabamba', '0303', '03'),
('030302', 'El Oro', '0303', '03'),
('030303', 'Huaquirca', '0303', '03'),
('030304', 'Juan Espinoza Medrano', '0303', '03'),
('030305', 'Oropesa', '0303', '03'),
('030306', 'Pachaconas', '0303', '03'),
('030307', 'Sabaino', '0303', '03'),
('030401', 'Chalhuanca', '0304', '03'),
('030402', 'Capaya', '0304', '03'),
('030403', 'Caraybamba', '0304', '03'),
('030404', 'Chapimarca', '0304', '03'),
('030405', 'Colcabamba', '0304', '03'),
('030406', 'Cotaruse', '0304', '03'),
('030407', 'Ihuayllo', '0304', '03'),
('030408', 'Justo Apu Sahuaraura', '0304', '03'),
('030409', 'Lucre', '0304', '03'),
('030410', 'Pocohuanca', '0304', '03'),
('030411', 'San Juan de Chacña', '0304', '03'),
('030412', 'Sañayca', '0304', '03'),
('030413', 'Soraya', '0304', '03'),
('030414', 'Tapairihua', '0304', '03'),
('030415', 'Tintay', '0304', '03'),
('030416', 'Toraya', '0304', '03'),
('030417', 'Yanaca', '0304', '03'),
('030501', 'Tambobamba', '0305', '03'),
('030502', 'Cotabambas', '0305', '03'),
('030503', 'Coyllurqui', '0305', '03'),
('030504', 'Haquira', '0305', '03'),
('030505', 'Mara', '0305', '03'),
('030506', 'Challhuahuacho', '0305', '03'),
('030601', 'Chincheros', '0306', '03'),
('030602', 'Anco_Huallo', '0306', '03'),
('030603', 'Cocharcas', '0306', '03'),
('030604', 'Huaccana', '0306', '03'),
('030605', 'Ocobamba', '0306', '03'),
('030606', 'Ongoy', '0306', '03'),
('030607', 'Uranmarca', '0306', '03'),
('030608', 'Ranracancha', '0306', '03'),
('030609', 'Rocchacc', '0306', '03'),
('030610', 'El Porvenir', '0306', '03'),
('030611', 'Los Chankas', '0306', '03'),
('030701', 'Chuquibambilla', '0307', '03'),
('030702', 'Curpahuasi', '0307', '03'),
('030703', 'Gamarra', '0307', '03'),
('030704', 'Huayllati', '0307', '03'),
('030705', 'Mamara', '0307', '03'),
('030706', 'Micaela Bastidas', '0307', '03'),
('030707', 'Pataypampa', '0307', '03'),
('030708', 'Progreso', '0307', '03'),
('030709', 'San Antonio', '0307', '03'),
('030710', 'Santa Rosa', '0307', '03'),
('030711', 'Turpay', '0307', '03'),
('030712', 'Vilcabamba', '0307', '03'),
('030713', 'Virundo', '0307', '03'),
('030714', 'Curasco', '0307', '03'),
('040101', 'Arequipa', '0401', '04'),
('040102', 'Alto Selva Alegre', '0401', '04'),
('040103', 'Cayma', '0401', '04'),
('040104', 'Cerro Colorado', '0401', '04'),
('040105', 'Characato', '0401', '04'),
('040106', 'Chiguata', '0401', '04'),
('040107', 'Jacobo Hunter', '0401', '04'),
('040108', 'La Joya', '0401', '04'),
('040109', 'Mariano Melgar', '0401', '04'),
('040110', 'Miraflores', '0401', '04'),
('040111', 'Mollebaya', '0401', '04'),
('040112', 'Paucarpata', '0401', '04'),
('040113', 'Pocsi', '0401', '04'),
('040114', 'Polobaya', '0401', '04'),
('040115', 'Quequeña', '0401', '04'),
('040116', 'Sabandia', '0401', '04'),
('040117', 'Sachaca', '0401', '04'),
('040118', 'San Juan de Siguas', '0401', '04'),
('040119', 'San Juan de Tarucani', '0401', '04'),
('040120', 'Santa Isabel de Siguas', '0401', '04'),
('040121', 'Santa Rita de Siguas', '0401', '04'),
('040122', 'Socabaya', '0401', '04'),
('040123', 'Tiabaya', '0401', '04'),
('040124', 'Uchumayo', '0401', '04'),
('040125', 'Vitor', '0401', '04'),
('040126', 'Yanahuara', '0401', '04'),
('040127', 'Yarabamba', '0401', '04'),
('040128', 'Yura', '0401', '04'),
('040129', 'José Luis Bustamante Y Rivero', '0401', '04'),
('040201', 'Camaná', '0402', '04'),
('040202', 'José María Quimper', '0402', '04'),
('040203', 'Mariano Nicolás Valcárcel', '0402', '04'),
('040204', 'Mariscal Cáceres', '0402', '04'),
('040205', 'Nicolás de Pierola', '0402', '04'),
('040206', 'Ocoña', '0402', '04'),
('040207', 'Quilca', '0402', '04'),
('040208', 'Samuel Pastor', '0402', '04'),
('040301', 'Caravelí', '0403', '04'),
('040302', 'Acarí', '0403', '04'),
('040303', 'Atico', '0403', '04'),
('040304', 'Atiquipa', '0403', '04'),
('040305', 'Bella Unión', '0403', '04'),
('040306', 'Cahuacho', '0403', '04'),
('040307', 'Chala', '0403', '04'),
('040308', 'Chaparra', '0403', '04'),
('040309', 'Huanuhuanu', '0403', '04'),
('040310', 'Jaqui', '0403', '04'),
('040311', 'Lomas', '0403', '04'),
('040312', 'Quicacha', '0403', '04'),
('040313', 'Yauca', '0403', '04'),
('040401', 'Aplao', '0404', '04'),
('040402', 'Andagua', '0404', '04'),
('040403', 'Ayo', '0404', '04'),
('040404', 'Chachas', '0404', '04'),
('040405', 'Chilcaymarca', '0404', '04'),
('040406', 'Choco', '0404', '04'),
('040407', 'Huancarqui', '0404', '04'),
('040408', 'Machaguay', '0404', '04'),
('040409', 'Orcopampa', '0404', '04'),
('040410', 'Pampacolca', '0404', '04'),
('040411', 'Tipan', '0404', '04'),
('040412', 'Uñon', '0404', '04'),
('040413', 'Uraca', '0404', '04'),
('040414', 'Viraco', '0404', '04'),
('040501', 'Chivay', '0405', '04'),
('040502', 'Achoma', '0405', '04'),
('040503', 'Cabanaconde', '0405', '04'),
('040504', 'Callalli', '0405', '04'),
('040505', 'Caylloma', '0405', '04'),
('040506', 'Coporaque', '0405', '04'),
('040507', 'Huambo', '0405', '04'),
('040508', 'Huanca', '0405', '04'),
('040509', 'Ichupampa', '0405', '04'),
('040510', 'Lari', '0405', '04'),
('040511', 'Lluta', '0405', '04'),
('040512', 'Maca', '0405', '04'),
('040513', 'Madrigal', '0405', '04'),
('040514', 'San Antonio de Chuca', '0405', '04'),
('040515', 'Sibayo', '0405', '04'),
('040516', 'Tapay', '0405', '04'),
('040517', 'Callalli', '0405', '04'),
('040518', 'Tuti', '0405', '04'),
('040519', 'Yanque', '0405', '04'),
('040520', 'Majes', '0405', '04'),
('040601', 'Chuquibamba', '0406', '04'),
('040602', 'Andaray', '0406', '04'),
('040603', 'Cayarani', '0406', '04'),
('040604', 'Chichas', '0406', '04'),
('040605', 'Iray', '0406', '04'),
('040606', 'Río Grande', '0406', '04'),
('040607', 'Salamanca', '0406', '04'),
('040608', 'Yanaquihua', '0406', '04'),
('040701', 'Mollendo', '0407', '04'),
('040702', 'Cocachacra', '0407', '04'),
('040703', 'Dean Valdivia', '0407', '04'),
('040704', 'Islay', '0407', '04'),
('040705', 'Mejia', '0407', '04'),
('040706', 'Punta de Bombón', '0407', '04'),
('040801', 'Cotahuasi', '0408', '04'),
('040802', 'Alca', '0408', '04'),
('040803', 'Charcana', '0408', '04'),
('040804', 'Huaynacotas', '0408', '04'),
('040805', 'Pampamarca', '0408', '04'),
('040806', 'Puyca', '0408', '04'),
('040807', 'Quechualla', '0408', '04'),
('040808', 'Sayla', '0408', '04'),
('040809', 'Tauria', '0408', '04'),
('040810', 'Tomepampa', '0408', '04'),
('040811', 'Toro', '0408', '04'),
('050101', 'Ayacucho', '0501', '05'),
('050102', 'Acocro', '0501', '05'),
('050103', 'Acos Vinchos', '0501', '05'),
('050104', 'Carmen Alto', '0501', '05'),
('050105', 'Chiara', '0501', '05'),
('050106', 'Ocros', '0501', '05'),
('050107', 'Pacaycasa', '0501', '05'),
('050108', 'Quinua', '0501', '05'),
('050109', 'San José de Ticllas', '0501', '05'),
('050110', 'San Juan Bautista', '0501', '05'),
('050111', 'Santiago de Pischa', '0501', '05'),
('050112', 'Socos', '0501', '05'),
('050113', 'Tambillo', '0501', '05'),
('050114', 'Vinchos', '0501', '05'),
('050115', 'Jesús Nazareno', '0501', '05'),
('050116', 'Andrés Avelino Cáceres Dorregaray', '0501', '05'),
('050201', 'Cangallo', '0502', '05'),
('050202', 'Chuschi', '0502', '05'),
('050203', 'Los Morochucos', '0502', '05'),
('050204', 'María Parado de Bellido', '0502', '05'),
('050205', 'Paras', '0502', '05'),
('050206', 'Totos', '0502', '05'),
('050301', 'Sancos', '0503', '05'),
('050302', 'Carapo', '0503', '05'),
('050303', 'Sacsamarca', '0503', '05'),
('050304', 'Santiago de Lucanamarca', '0503', '05'),
('050401', 'Huanta', '0504', '05'),
('050402', 'Ayahuanco', '0504', '05'),
('050403', 'Huamanguilla', '0504', '05'),
('050404', 'Iguain', '0504', '05'),
('050405', 'Luricocha', '0504', '05'),
('050406', 'Santillana', '0504', '05'),
('050407', 'Sivia', '0504', '05'),
('050408', 'Llochegua', '0504', '05'),
('050409', 'Canayre', '0504', '05'),
('050410', 'Uchuraccay', '0504', '05'),
('050411', 'Pucacolpa', '0504', '05'),
('050412', 'Chaca', '0504', '05'),
('050501', 'San Miguel', '0505', '05'),
('050502', 'Anco', '0505', '05'),
('050503', 'Ayna', '0505', '05'),
('050504', 'Chilcas', '0505', '05'),
('050505', 'Chungui', '0505', '05'),
('050506', 'Luis Carranza', '0505', '05'),
('050507', 'Santa Rosa', '0505', '05'),
('050508', 'Tambo', '0505', '05'),
('050509', 'Samugari', '0505', '05'),
('050510', 'Anchihuay', '0505', '05'),
('050511', 'Oronccoy', '0505', '05'),
('050601', 'Puquio', '0506', '05'),
('050602', 'Aucara', '0506', '05'),
('050603', 'Cabana', '0506', '05'),
('050604', 'Carmen Salcedo', '0506', '05'),
('050605', 'Chaviña', '0506', '05'),
('050606', 'Chipao', '0506', '05'),
('050607', 'Huac-Huas', '0506', '05'),
('050608', 'Laramate', '0506', '05'),
('050609', 'Leoncio Prado', '0506', '05'),
('050610', 'Llauta', '0506', '05'),
('050611', 'Lucanas', '0506', '05'),
('050612', 'Ocaña', '0506', '05'),
('050613', 'Otoca', '0506', '05'),
('050614', 'Saisa', '0506', '05'),
('050615', 'San Cristóbal', '0506', '05'),
('050616', 'San Juan', '0506', '05'),
('050617', 'San Pedro', '0506', '05'),
('050618', 'San Pedro de Palco', '0506', '05'),
('050619', 'Sancos', '0506', '05'),
('050620', 'Santa Ana de Huaycahuacho', '0506', '05'),
('050621', 'Santa Lucia', '0506', '05'),
('050701', 'Coracora', '0507', '05'),
('050702', 'Chumpi', '0507', '05'),
('050703', 'Coronel Castañeda', '0507', '05'),
('050704', 'Pacapausa', '0507', '05'),
('050705', 'Pullo', '0507', '05'),
('050706', 'Puyusca', '0507', '05'),
('050707', 'San Francisco de Ravacayco', '0507', '05'),
('050708', 'Upahuacho', '0507', '05'),
('050801', 'Pausa', '0508', '05'),
('050802', 'Colta', '0508', '05'),
('050803', 'Corculla', '0508', '05'),
('050804', 'Lampa', '0508', '05'),
('050805', 'Marcabamba', '0508', '05'),
('050806', 'Oyolo', '0508', '05'),
('050807', 'Pararca', '0508', '05'),
('050808', 'San Javier de Alpabamba', '0508', '05'),
('050809', 'San José de Ushua', '0508', '05'),
('050810', 'Sara Sara', '0508', '05'),
('050901', 'Querobamba', '0509', '05'),
('050902', 'Belén', '0509', '05'),
('050903', 'Chalcos', '0509', '05'),
('050904', 'Chilcayoc', '0509', '05'),
('050905', 'Huacaña', '0509', '05'),
('050906', 'Morcolla', '0509', '05'),
('050907', 'Paico', '0509', '05'),
('050908', 'San Pedro de Larcay', '0509', '05'),
('050909', 'San Salvador de Quije', '0509', '05'),
('050910', 'Santiago de Paucaray', '0509', '05'),
('050911', 'Soras', '0509', '05'),
('051001', 'Huancapi', '0510', '05'),
('051002', 'Alcamenca', '0510', '05'),
('051003', 'Apongo', '0510', '05'),
('051004', 'Asquipata', '0510', '05'),
('051005', 'Canaria', '0510', '05'),
('051006', 'Cayara', '0510', '05'),
('051007', 'Colca', '0510', '05'),
('051008', 'Huamanquiquia', '0510', '05'),
('051009', 'Huancaraylla', '0510', '05'),
('051010', 'Hualla', '0510', '05'),
('051011', 'Sarhua', '0510', '05'),
('051012', 'Vilcanchos', '0510', '05'),
('051101', 'Vilcas Huaman', '0511', '05'),
('051102', 'Accomarca', '0511', '05'),
('051103', 'Carhuanca', '0511', '05'),
('051104', 'Concepción', '0511', '05'),
('051105', 'Huambalpa', '0511', '05'),
('051106', 'Independencia', '0511', '05'),
('051107', 'Saurama', '0511', '05'),
('051108', 'Vischongo', '0511', '05'),
('060101', 'Cajamarca', '0601', '06'),
('060102', 'Asunción', '0601', '06'),
('060103', 'Chetilla', '0601', '06'),
('060104', 'Cospan', '0601', '06'),
('060105', 'Encañada', '0601', '06'),
('060106', 'Jesús', '0601', '06'),
('060107', 'Llacanora', '0601', '06'),
('060108', 'Los Baños del Inca', '0601', '06'),
('060109', 'Magdalena', '0601', '06'),
('060110', 'Matara', '0601', '06'),
('060111', 'Namora', '0601', '06'),
('060112', 'San Juan', '0601', '06'),
('060201', 'Cajabamba', '0602', '06'),
('060202', 'Cachachi', '0602', '06'),
('060203', 'Condebamba', '0602', '06'),
('060204', 'Sitacocha', '0602', '06'),
('060301', 'Celendín', '0603', '06'),
('060302', 'Chumuch', '0603', '06'),
('060303', 'Cortegana', '0603', '06'),
('060304', 'Huasmin', '0603', '06'),
('060305', 'Jorge Chávez', '0603', '06'),
('060306', 'José Gálvez', '0603', '06'),
('060307', 'Miguel Iglesias', '0603', '06'),
('060308', 'Oxamarca', '0603', '06'),
('060309', 'Sorochuco', '0603', '06'),
('060310', 'Sucre', '0603', '06'),
('060311', 'Utco', '0603', '06'),
('060312', 'La Libertad de Pallan', '0603', '06'),
('060401', 'Chota', '0604', '06'),
('060402', 'Anguia', '0604', '06'),
('060403', 'Chadin', '0604', '06'),
('060404', 'Chiguirip', '0604', '06'),
('060405', 'Chimban', '0604', '06'),
('060406', 'Choropampa', '0604', '06'),
('060407', 'Cochabamba', '0604', '06'),
('060408', 'Conchan', '0604', '06'),
('060409', 'Huambos', '0604', '06'),
('060410', 'Lajas', '0604', '06'),
('060411', 'Llama', '0604', '06'),
('060412', 'Miracosta', '0604', '06'),
('060413', 'Paccha', '0604', '06'),
('060414', 'Pion', '0604', '06'),
('060415', 'Querocoto', '0604', '06'),
('060416', 'San Juan de Licupis', '0604', '06'),
('060417', 'Tacabamba', '0604', '06'),
('060418', 'Tocmoche', '0604', '06'),
('060419', 'Chalamarca', '0604', '06'),
('060501', 'Contumaza', '0605', '06'),
('060502', 'Chilete', '0605', '06'),
('060503', 'Cupisnique', '0605', '06'),
('060504', 'Guzmango', '0605', '06'),
('060505', 'San Benito', '0605', '06'),
('060506', 'Santa Cruz de Toledo', '0605', '06'),
('060507', 'Tantarica', '0605', '06'),
('060508', 'Yonan', '0605', '06'),
('060601', 'Cutervo', '0606', '06'),
('060602', 'Callayuc', '0606', '06'),
('060603', 'Choros', '0606', '06'),
('060604', 'Cujillo', '0606', '06'),
('060605', 'La Ramada', '0606', '06'),
('060606', 'Pimpingos', '0606', '06'),
('060607', 'Querocotillo', '0606', '06'),
('060608', 'San Andrés de Cutervo', '0606', '06'),
('060609', 'San Juan de Cutervo', '0606', '06'),
('060610', 'San Luis de Lucma', '0606', '06'),
('060611', 'Santa Cruz', '0606', '06'),
('060612', 'Santo Domingo de la Capilla', '0606', '06'),
('060613', 'Santo Tomas', '0606', '06'),
('060614', 'Socota', '0606', '06'),
('060615', 'Toribio Casanova', '0606', '06'),
('060701', 'Bambamarca', '0607', '06'),
('060702', 'Chugur', '0607', '06'),
('060703', 'Hualgayoc', '0607', '06'),
('060801', 'Jaén', '0608', '06'),
('060802', 'Bellavista', '0608', '06'),
('060803', 'Chontali', '0608', '06'),
('060804', 'Colasay', '0608', '06'),
('060805', 'Huabal', '0608', '06'),
('060806', 'Las Pirias', '0608', '06'),
('060807', 'Pomahuaca', '0608', '06'),
('060808', 'Pucara', '0608', '06'),
('060809', 'Sallique', '0608', '06'),
('060810', 'San Felipe', '0608', '06'),
('060811', 'San José del Alto', '0608', '06'),
('060812', 'Santa Rosa', '0608', '06'),
('060901', 'San Ignacio', '0609', '06'),
('060902', 'Chirinos', '0609', '06'),
('060903', 'Huarango', '0609', '06'),
('060904', 'La Coipa', '0609', '06'),
('060905', 'Namballe', '0609', '06'),
('060906', 'San José de Lourdes', '0609', '06'),
('060907', 'Tabaconas', '0609', '06'),
('061001', 'Pedro Gálvez', '0610', '06'),
('061002', 'Chancay', '0610', '06'),
('061003', 'Eduardo Villanueva', '0610', '06'),
('061004', 'Gregorio Pita', '0610', '06'),
('061005', 'Ichocan', '0610', '06'),
('061006', 'José Manuel Quiroz', '0610', '06'),
('061007', 'José Sabogal', '0610', '06'),
('061101', 'San Miguel', '0611', '06'),
('061102', 'Bolívar', '0611', '06'),
('061103', 'Calquis', '0611', '06'),
('061104', 'Catilluc', '0611', '06'),
('061105', 'El Prado', '0611', '06'),
('061106', 'La Florida', '0611', '06'),
('061107', 'Llapa', '0611', '06'),
('061108', 'Nanchoc', '0611', '06'),
('061109', 'Niepos', '0611', '06'),
('061110', 'San Gregorio', '0611', '06'),
('061111', 'San Silvestre de Cochan', '0611', '06'),
('061112', 'Tongod', '0611', '06'),
('061113', 'Unión Agua Blanca', '0611', '06'),
('061201', 'San Pablo', '0612', '06'),
('061202', 'San Bernardino', '0612', '06'),
('061203', 'San Luis', '0612', '06'),
('061204', 'Tumbaden', '0612', '06'),
('061301', 'Santa Cruz', '0613', '06'),
('061302', 'Andabamba', '0613', '06'),
('061303', 'Catache', '0613', '06'),
('061304', 'Chancaybaños', '0613', '06'),
('061305', 'La Esperanza', '0613', '06'),
('061306', 'Ninabamba', '0613', '06'),
('061307', 'Pulan', '0613', '06'),
('061308', 'Saucepampa', '0613', '06'),
('061309', 'Sexi', '0613', '06'),
('061310', 'Uticyacu', '0613', '06'),
('061311', 'Yauyucan', '0613', '06'),
('070101', 'Callao', '0701', '07'),
('070102', 'Bellavista', '0701', '07'),
('070103', 'Carmen de la Legua Reynoso', '0701', '07'),
('070104', 'La Perla', '0701', '07'),
('070105', 'La Punta', '0701', '07'),
('070106', 'Ventanilla', '0701', '07'),
('070107', 'Mi Perú', '0701', '07'),
('080101', 'Cusco', '0801', '08'),
('080102', 'Ccorca', '0801', '08'),
('080103', 'Poroy', '0801', '08'),
('080104', 'San Jerónimo', '0801', '08'),
('080105', 'San Sebastian', '0801', '08'),
('080106', 'Santiago', '0801', '08'),
('080107', 'Saylla', '0801', '08'),
('080108', 'Wanchaq', '0801', '08'),
('080201', 'Acomayo', '0802', '08'),
('080202', 'Acopia', '0802', '08'),
('080203', 'Acos', '0802', '08'),
('080204', 'Mosoc Llacta', '0802', '08'),
('080205', 'Pomacanchi', '0802', '08'),
('080206', 'Rondocan', '0802', '08'),
('080207', 'Sangarara', '0802', '08'),
('080301', 'Anta', '0803', '08'),
('080302', 'Ancahuasi', '0803', '08'),
('080303', 'Cachimayo', '0803', '08'),
('080304', 'Chinchaypujio', '0803', '08'),
('080305', 'Huarocondo', '0803', '08'),
('080306', 'Limatambo', '0803', '08'),
('080307', 'Mollepata', '0803', '08'),
('080308', 'Pucyura', '0803', '08'),
('080309', 'Zurite', '0803', '08'),
('080401', 'Calca', '0804', '08'),
('080402', 'Coya', '0804', '08'),
('080403', 'Lamay', '0804', '08'),
('080404', 'Lares', '0804', '08'),
('080405', 'Pisac', '0804', '08'),
('080406', 'San Salvador', '0804', '08'),
('080407', 'Taray', '0804', '08'),
('080408', 'Yanatile', '0804', '08'),
('080501', 'Yanaoca', '0805', '08'),
('080502', 'Checca', '0805', '08'),
('080503', 'Kunturkanki', '0805', '08'),
('080504', 'Langui', '0805', '08'),
('080505', 'Layo', '0805', '08'),
('080506', 'Pampamarca', '0805', '08'),
('080507', 'Quehue', '0805', '08'),
('080508', 'Tupac Amaru', '0805', '08'),
('080601', 'Sicuani', '0806', '08'),
('080602', 'Checacupe', '0806', '08'),
('080603', 'Combapata', '0806', '08'),
('080604', 'Marangani', '0806', '08'),
('080605', 'Pitumarca', '0806', '08'),
('080606', 'San Pablo', '0806', '08'),
('080607', 'San Pedro', '0806', '08'),
('080608', 'Tinta', '0806', '08'),
('080701', 'Santo Tomas', '0807', '08'),
('080702', 'Capacmarca', '0807', '08'),
('080703', 'Chamaca', '0807', '08'),
('080704', 'Colquemarca', '0807', '08'),
('080705', 'Livitaca', '0807', '08'),
('080706', 'Llusco', '0807', '08'),
('080707', 'Quiñota', '0807', '08'),
('080708', 'Velille', '0807', '08'),
('080801', 'Espinar', '0808', '08'),
('080802', 'Condoroma', '0808', '08'),
('080803', 'Coporaque', '0808', '08'),
('080804', 'Ocoruro', '0808', '08'),
('080805', 'Pallpata', '0808', '08'),
('080806', 'Pichigua', '0808', '08'),
('080807', 'Suyckutambo', '0808', '08'),
('080808', 'Alto Pichigua', '0808', '08'),
('080901', 'Santa Ana', '0809', '08'),
('080902', 'Echarate', '0809', '08'),
('080903', 'Huayopata', '0809', '08'),
('080904', 'Maranura', '0809', '08'),
('080905', 'Ocobamba', '0809', '08'),
('080906', 'Quellouno', '0809', '08'),
('080907', 'Kimbiri', '0809', '08'),
('080908', 'Santa Teresa', '0809', '08'),
('080909', 'Vilcabamba', '0809', '08'),
('080910', 'Pichari', '0809', '08'),
('080911', 'Inkawasi', '0809', '08'),
('080912', 'Villa Virgen', '0809', '08'),
('080913', 'Villa Kintiarina', '0809', '08'),
('080914', 'Megantoni', '0809', '08'),
('081001', 'Paruro', '0810', '08'),
('081002', 'Accha', '0810', '08'),
('081003', 'Ccapi', '0810', '08'),
('081004', 'Colcha', '0810', '08'),
('081005', 'Huanoquite', '0810', '08'),
('081006', 'Omachaç', '0810', '08'),
('081007', 'Paccaritambo', '0810', '08'),
('081008', 'Pillpinto', '0810', '08'),
('081009', 'Yaurisque', '0810', '08'),
('081101', 'Paucartambo', '0811', '08'),
('081102', 'Caicay', '0811', '08'),
('081103', 'Challabamba', '0811', '08'),
('081104', 'Colquepata', '0811', '08'),
('081105', 'Huancarani', '0811', '08'),
('081106', 'Kosñipata', '0811', '08'),
('081201', 'Urcos', '0812', '08'),
('081202', 'Andahuaylillas', '0812', '08'),
('081203', 'Camanti', '0812', '08'),
('081204', 'Ccarhuayo', '0812', '08'),
('081205', 'Ccatca', '0812', '08'),
('081206', 'Cusipata', '0812', '08'),
('081207', 'Huaro', '0812', '08'),
('081208', 'Lucre', '0812', '08'),
('081209', 'Marcapata', '0812', '08'),
('081210', 'Ocongate', '0812', '08'),
('081211', 'Oropesa', '0812', '08'),
('081212', 'Quiquijana', '0812', '08'),
('081301', 'Urubamba', '0813', '08'),
('081302', 'Chinchero', '0813', '08'),
('081303', 'Huayllabamba', '0813', '08'),
('081304', 'Machupicchu', '0813', '08'),
('081305', 'Maras', '0813', '08'),
('081306', 'Ollantaytambo', '0813', '08'),
('081307', 'Yucay', '0813', '08'),
('090101', 'Huancavelica', '0901', '09'),
('090102', 'Acobambilla', '0901', '09'),
('090103', 'Acoria', '0901', '09'),
('090104', 'Conayca', '0901', '09'),
('090105', 'Cuenca', '0901', '09'),
('090106', 'Huachocolpa', '0901', '09'),
('090107', 'Huayllahuara', '0901', '09'),
('090108', 'Izcuchaca', '0901', '09'),
('090109', 'Laria', '0901', '09'),
('090110', 'Manta', '0901', '09'),
('090111', 'Mariscal Cáceres', '0901', '09'),
('090112', 'Moya', '0901', '09'),
('090113', 'Nuevo Occoro', '0901', '09'),
('090114', 'Palca', '0901', '09'),
('090115', 'Pilchaca', '0901', '09'),
('090116', 'Vilca', '0901', '09'),
('090117', 'Yauli', '0901', '09'),
('090118', 'Ascensión', '0901', '09'),
('090119', 'Huando', '0901', '09'),
('090201', 'Acobamba', '0902', '09'),
('090202', 'Andabamba', '0902', '09'),
('090203', 'Anta', '0902', '09'),
('090204', 'Caja', '0902', '09'),
('090205', 'Marcas', '0902', '09'),
('090206', 'Paucara', '0902', '09'),
('090207', 'Pomacocha', '0902', '09'),
('090208', 'Rosario', '0902', '09'),
('090301', 'Lircay', '0903', '09'),
('090302', 'Anchonga', '0903', '09'),
('090303', 'Callanmarca', '0903', '09'),
('090304', 'Ccochaccasa', '0903', '09'),
('090305', 'Chincho', '0903', '09'),
('090306', 'Congalla', '0903', '09'),
('090307', 'Huanca-Huanca', '0903', '09'),
('090308', 'Huayllay Grande', '0903', '09'),
('090309', 'Julcamarca', '0903', '09'),
('090310', 'San Antonio de Antaparco', '0903', '09'),
('090311', 'Santo Tomas de Pata', '0903', '09'),
('090312', 'Secclla', '0903', '09'),
('090401', 'Castrovirreyna', '0904', '09'),
('090402', 'Arma', '0904', '09'),
('090403', 'Aurahua', '0904', '09'),
('090404', 'Capillas', '0904', '09'),
('090405', 'Chupamarca', '0904', '09'),
('090406', 'Cocas', '0904', '09'),
('090407', 'Huachos', '0904', '09'),
('090408', 'Huamatambo', '0904', '09'),
('090409', 'Mollepampa', '0904', '09'),
('090410', 'San Juan', '0904', '09'),
('090411', 'Santa Ana', '0904', '09'),
('090412', 'Tantara', '0904', '09'),
('090413', 'Ticrapo', '0904', '09'),
('090501', 'Churcampa', '0905', '09'),
('090502', 'Anco', '0905', '09'),
('090503', 'Chinchihuasi', '0905', '09'),
('090504', 'El Carmen', '0905', '09'),
('090505', 'La Merced', '0905', '09'),
('090506', 'Locroja', '0905', '09'),
('090507', 'Paucarbamba', '0905', '09'),
('090508', 'San Miguel de Mayocc', '0905', '09'),
('090509', 'San Pedro de Coris', '0905', '09'),
('090510', 'Pachamarca', '0905', '09'),
('090511', 'Cosme', '0905', '09'),
('090601', 'Huaytara', '0906', '09'),
('090602', 'Ayavi', '0906', '09'),
('090603', 'Córdova', '0906', '09'),
('090604', 'Huayacundo Arma', '0906', '09'),
('090605', 'Laramarca', '0906', '09'),
('090606', 'Ocoyo', '0906', '09'),
('090607', 'Pilpichaca', '0906', '09'),
('090608', 'Querco', '0906', '09'),
('090609', 'Quito-Arma', '0906', '09'),
('090610', 'San Antonio de Cusicancha', '0906', '09'),
('090611', 'San Francisco de Sangayaico', '0906', '09'),
('090612', 'San Isidro', '0906', '09'),
('090613', 'Santiago de Chocorvos', '0906', '09'),
('090614', 'Santiago de Quirahuara', '0906', '09'),
('090615', 'Santo Domingo de Capillas', '0906', '09'),
('090616', 'Tambo', '0906', '09'),
('090701', 'Pampas', '0907', '09'),
('090702', 'Acostambo', '0907', '09'),
('090703', 'Acraquia', '0907', '09'),
('090704', 'Ahuaycha', '0907', '09'),
('090705', 'Colcabamba', '0907', '09'),
('090706', 'Daniel Hernández', '0907', '09'),
('090707', 'Huachocolpa', '0907', '09'),
('090709', 'Huaribamba', '0907', '09'),
('090710', 'Ñahuimpuquio', '0907', '09'),
('090711', 'Pazos', '0907', '09'),
('090713', 'Quishuar', '0907', '09'),
('090714', 'Salcabamba', '0907', '09'),
('090715', 'Salcahuasi', '0907', '09'),
('090716', 'San Marcos de Rocchac', '0907', '09'),
('090717', 'Surcubamba', '0907', '09'),
('090718', 'Tintay Puncu', '0907', '09'),
('090719', 'Quichuas', '0907', '09'),
('090720', 'Andaymarca', '0907', '09'),
('090721', 'Roble', '0907', '09'),
('090722', 'Pichos', '0907', '09'),
('090723', 'Santiago de Tucuma', '0907', '09'),
('100101', 'Huanuco', '1001', '10'),
('100102', 'Amarilis', '1001', '10'),
('100103', 'Chinchao', '1001', '10'),
('100104', 'Churubamba', '1001', '10'),
('100105', 'Margos', '1001', '10'),
('100106', 'Quisqui (Kichki)', '1001', '10'),
('100107', 'San Francisco de Cayran', '1001', '10'),
('100108', 'San Pedro de Chaulan', '1001', '10'),
('100109', 'Santa María del Valle', '1001', '10'),
('100110', 'Yarumayo', '1001', '10'),
('100111', 'Pillco Marca', '1001', '10'),
('100112', 'Yacus', '1001', '10'),
('100113', 'San Pablo de Pillao', '1001', '10'),
('100201', 'Ambo', '1002', '10'),
('100202', 'Cayna', '1002', '10'),
('100203', 'Colpas', '1002', '10'),
('100204', 'Conchamarca', '1002', '10'),
('100205', 'Huacar', '1002', '10'),
('100206', 'San Francisco', '1002', '10'),
('100207', 'San Rafael', '1002', '10'),
('100208', 'Tomay Kichwa', '1002', '10'),
('100301', 'La Unión', '1003', '10'),
('100307', 'Chuquis', '1003', '10'),
('100311', 'Marías', '1003', '10'),
('100313', 'Pachas', '1003', '10'),
('100316', 'Quivilla', '1003', '10'),
('100317', 'Ripan', '1003', '10'),
('100321', 'Shunqui', '1003', '10'),
('100322', 'Sillapata', '1003', '10'),
('100323', 'Yanas', '1003', '10'),
('100401', 'Huacaybamba', '1004', '10'),
('100402', 'Canchabamba', '1004', '10'),
('100403', 'Cochabamba', '1004', '10'),
('100404', 'Pinra', '1004', '10'),
('100501', 'Llata', '1005', '10'),
('100502', 'Arancay', '1005', '10'),
('100503', 'Chavín de Pariarca', '1005', '10'),
('100504', 'Jacas Grande', '1005', '10'),
('100505', 'Jircan', '1005', '10'),
('100506', 'Miraflores', '1005', '10'),
('100507', 'Monzón', '1005', '10'),
('100508', 'Punchao', '1005', '10'),
('100509', 'Puños', '1005', '10'),
('100510', 'Singa', '1005', '10'),
('100511', 'Tantamayo', '1005', '10'),
('100601', 'Rupa-Rupa', '1006', '10'),
('100602', 'Daniel Alomía Robles', '1006', '10'),
('100603', 'Hermílio Valdizan', '1006', '10'),
('100604', 'José Crespo y Castillo', '1006', '10'),
('100605', 'Luyando', '1006', '10'),
('100606', 'Mariano Damaso Beraun', '1006', '10'),
('100607', 'Pucayacu', '1006', '10'),
('100608', 'Castillo Grande', '1006', '10'),
('100609', 'Pueblo Nuevo', '1006', '10'),
('100610', 'Santo Domingo de Anda', '1006', '10'),
('100701', 'Huacrachuco', '1007', '10'),
('100702', 'Cholon', '1007', '10'),
('100703', 'San Buenaventura', '1007', '10'),
('100704', 'La Morada', '1007', '10'),
('100705', 'Santa Rosa de Alto Yanajanca', '1007', '10'),
('100801', 'Panao', '1008', '10'),
('100802', 'Chaglla', '1008', '10'),
('100803', 'Molino', '1008', '10'),
('100804', 'Umari', '1008', '10'),
('100901', 'Puerto Inca', '1009', '10'),
('100902', 'Codo del Pozuzo', '1009', '10'),
('100903', 'Honoria', '1009', '10'),
('100904', 'Tournavista', '1009', '10'),
('100905', 'Yuyapichis', '1009', '10'),
('101001', 'Jesús', '1010', '10'),
('101002', 'Baños', '1010', '10'),
('101003', 'Jivia', '1010', '10'),
('101004', 'Queropalca', '1010', '10'),
('101005', 'Rondos', '1010', '10'),
('101006', 'San Francisco de Asís', '1010', '10'),
('101007', 'San Miguel de Cauri', '1010', '10'),
('101101', 'Chavinillo', '1011', '10'),
('101102', 'Cahuac', '1011', '10'),
('101103', 'Chacabamba', '1011', '10'),
('101104', 'Aparicio Pomares', '1011', '10'),
('101105', 'Jacas Chico', '1011', '10'),
('101106', 'Obas', '1011', '10'),
('101107', 'Pampamarca', '1011', '10'),
('101108', 'Choras', '1011', '10'),
('110101', 'Ica', '1101', '11'),
('110102', 'La Tinguiña', '1101', '11'),
('110103', 'Los Aquijes', '1101', '11'),
('110104', 'Ocucaje', '1101', '11'),
('110105', 'Pachacutec', '1101', '11'),
('110106', 'Parcona', '1101', '11'),
('110107', 'Pueblo Nuevo', '1101', '11'),
('110108', 'Salas', '1101', '11'),
('110109', 'San José de Los Molinos', '1101', '11'),
('110110', 'San Juan Bautista', '1101', '11'),
('110111', 'Santiago', '1101', '11'),
('110112', 'Subtanjalla', '1101', '11'),
('110113', 'Tate', '1101', '11'),
('110114', 'Yauca del Rosario', '1101', '11'),
('110201', 'Chincha Alta', '1102', '11'),
('110202', 'Alto Laran', '1102', '11'),
('110203', 'Chavin', '1102', '11'),
('110204', 'Chincha Baja', '1102', '11'),
('110205', 'El Carmen', '1102', '11'),
('110206', 'Grocio Prado', '1102', '11'),
('110207', 'Pueblo Nuevo', '1102', '11'),
('110208', 'San Juan de Yanac', '1102', '11'),
('110209', 'San Pedro de Huacarpana', '1102', '11'),
('110210', 'Sunampe', '1102', '11'),
('110211', 'Tambo de Mora', '1102', '11'),
('110301', 'Nasca', '1103', '11'),
('110302', 'Changuillo', '1103', '11'),
('110303', 'El Ingenio', '1103', '11'),
('110304', 'Marcona', '1103', '11'),
('110305', 'Vista Alegre', '1103', '11'),
('110401', 'Palpa', '1104', '11'),
('110402', 'Llipata', '1104', '11'),
('110403', 'Río Grande', '1104', '11'),
('110404', 'Santa Cruz', '1104', '11'),
('110405', 'Tibillo', '1104', '11'),
('110501', 'Pisco', '1105', '11'),
('110502', 'Huancano', '1105', '11'),
('110503', 'Humay', '1105', '11'),
('110504', 'Independencia', '1105', '11'),
('110505', 'Paracas', '1105', '11'),
('110506', 'San Andrés', '1105', '11'),
('110507', 'San Clemente', '1105', '11'),
('110508', 'Tupac Amaru Inca', '1105', '11'),
('120101', 'Huancayo', '1201', '12'),
('120104', 'Carhuacallanga', '1201', '12'),
('120105', 'Chacapampa', '1201', '12'),
('120106', 'Chicche', '1201', '12'),
('120107', 'Chilca', '1201', '12'),
('120108', 'Chongos Alto', '1201', '12'),
('120111', 'Chupuro', '1201', '12'),
('120112', 'Colca', '1201', '12'),
('120113', 'Cullhuas', '1201', '12'),
('120114', 'El Tambo', '1201', '12'),
('120116', 'Huacrapuquio', '1201', '12'),
('120117', 'Hualhuas', '1201', '12'),
('120119', 'Huancan', '1201', '12'),
('120120', 'Huasicancha', '1201', '12'),
('120121', 'Huayucachi', '1201', '12'),
('120122', 'Ingenio', '1201', '12'),
('120124', 'Pariahuanca', '1201', '12'),
('120125', 'Pilcomayo', '1201', '12'),
('120126', 'Pucara', '1201', '12'),
('120127', 'Quichuay', '1201', '12'),
('120128', 'Quilcas', '1201', '12'),
('120129', 'San Agustín', '1201', '12'),
('120130', 'San Jerónimo de Tunan', '1201', '12'),
('120132', 'Saño', '1201', '12'),
('120133', 'Sapallanga', '1201', '12'),
('120134', 'Sicaya', '1201', '12'),
('120135', 'Santo Domingo de Acobamba', '1201', '12'),
('120136', 'Viques', '1201', '12'),
('120201', 'Concepción', '1202', '12'),
('120202', 'Aco', '1202', '12'),
('120203', 'Andamarca', '1202', '12'),
('120204', 'Chambara', '1202', '12'),
('120205', 'Cochas', '1202', '12'),
('120206', 'Comas', '1202', '12'),
('120207', 'Heroínas Toledo', '1202', '12'),
('120208', 'Manzanares', '1202', '12'),
('120209', 'Mariscal Castilla', '1202', '12'),
('120210', 'Matahuasi', '1202', '12'),
('120211', 'Mito', '1202', '12'),
('120212', 'Nueve de Julio', '1202', '12'),
('120213', 'Orcotuna', '1202', '12'),
('120214', 'San José de Quero', '1202', '12'),
('120215', 'Santa Rosa de Ocopa', '1202', '12'),
('120301', 'Chanchamayo', '1203', '12'),
('120302', 'Perene', '1203', '12'),
('120303', 'Pichanaqui', '1203', '12'),
('120304', 'San Luis de Shuaro', '1203', '12'),
('120305', 'San Ramón', '1203', '12'),
('120306', 'Vitoc', '1203', '12'),
('120401', 'Jauja', '1204', '12'),
('120402', 'Acolla', '1204', '12'),
('120403', 'Apata', '1204', '12'),
('120404', 'Ataura', '1204', '12'),
('120405', 'Canchayllo', '1204', '12'),
('120406', 'Curicaca', '1204', '12'),
('120407', 'El Mantaro', '1204', '12'),
('120408', 'Huamali', '1204', '12'),
('120409', 'Huaripampa', '1204', '12'),
('120410', 'Huertas', '1204', '12'),
('120411', 'Janjaillo', '1204', '12'),
('120412', 'Julcán', '1204', '12'),
('120413', 'Leonor Ordóñez', '1204', '12'),
('120414', 'Llocllapampa', '1204', '12'),
('120415', 'Marco', '1204', '12'),
('120416', 'Masma', '1204', '12'),
('120417', 'Masma Chicche', '1204', '12'),
('120418', 'Molinos', '1204', '12'),
('120419', 'Monobamba', '1204', '12'),
('120420', 'Muqui', '1204', '12'),
('120421', 'Muquiyauyo', '1204', '12'),
('120422', 'Paca', '1204', '12'),
('120423', 'Paccha', '1204', '12'),
('120424', 'Pancan', '1204', '12'),
('120425', 'Parco', '1204', '12'),
('120426', 'Pomacancha', '1204', '12'),
('120427', 'Ricran', '1204', '12'),
('120428', 'San Lorenzo', '1204', '12'),
('120429', 'San Pedro de Chunan', '1204', '12'),
('120430', 'Sausa', '1204', '12'),
('120431', 'Sincos', '1204', '12'),
('120432', 'Tunan Marca', '1204', '12'),
('120433', 'Yauli', '1204', '12'),
('120434', 'Yauyos', '1204', '12'),
('120501', 'Junin', '1205', '12'),
('120502', 'Carhuamayo', '1205', '12'),
('120503', 'Ondores', '1205', '12'),
('120504', 'Ulcumayo', '1205', '12'),
('120601', 'Satipo', '1206', '12'),
('120602', 'Coviriali', '1206', '12'),
('120603', 'Llaylla', '1206', '12'),
('120604', 'Mazamari', '1206', '12'),
('120605', 'Pampa Hermosa', '1206', '12'),
('120606', 'Pangoa', '1206', '12'),
('120607', 'Río Negro', '1206', '12'),
('120608', 'Río Tambo', '1206', '12'),
('120609', 'Vizcatan del Ene', '1206', '12'),
('120701', 'Tarma', '1207', '12'),
('120702', 'Acobamba', '1207', '12'),
('120703', 'Huaricolca', '1207', '12'),
('120704', 'Huasahuasi', '1207', '12'),
('120705', 'La Unión', '1207', '12'),
('120706', 'Palca', '1207', '12'),
('120707', 'Palcamayo', '1207', '12'),
('120708', 'San Pedro de Cajas', '1207', '12'),
('120709', 'Tapo', '1207', '12'),
('120801', 'La Oroya', '1208', '12'),
('120802', 'Chacapalpa', '1208', '12'),
('120803', 'Huay-Huay', '1208', '12'),
('120804', 'Marcapomacocha', '1208', '12'),
('120805', 'Morococha', '1208', '12'),
('120806', 'Paccha', '1208', '12'),
('120807', 'Santa Bárbara de Carhuacayan', '1208', '12'),
('120808', 'Santa Rosa de Sacco', '1208', '12'),
('120809', 'Suitucancha', '1208', '12'),
('120810', 'Yauli', '1208', '12'),
('120901', 'Chupaca', '1209', '12'),
('120902', 'Ahuac', '1209', '12'),
('120903', 'Chongos Bajo', '1209', '12'),
('120904', 'Huachac', '1209', '12'),
('120905', 'Huamancaca Chico', '1209', '12'),
('120906', 'San Juan de Iscos', '1209', '12'),
('120907', 'San Juan de Jarpa', '1209', '12'),
('120908', 'Tres de Diciembre', '1209', '12'),
('120909', 'Yanacancha', '1209', '12'),
('130101', 'Trujillo', '1301', '13'),
('130102', 'El Porvenir', '1301', '13'),
('130103', 'Florencia de Mora', '1301', '13'),
('130104', 'Huanchaco', '1301', '13'),
('130105', 'La Esperanza', '1301', '13'),
('130106', 'Laredo', '1301', '13'),
('130107', 'Moche', '1301', '13'),
('130108', 'Poroto', '1301', '13'),
('130109', 'Salaverry', '1301', '13'),
('130110', 'Simbal', '1301', '13'),
('130111', 'Victor Larco Herrera', '1301', '13'),
('130201', 'Ascope', '1302', '13'),
('130202', 'Chicama', '1302', '13'),
('130203', 'Chocope', '1302', '13'),
('130204', 'Magdalena de Cao', '1302', '13'),
('130205', 'Paijan', '1302', '13'),
('130206', 'Rázuri', '1302', '13'),
('130207', 'Santiago de Cao', '1302', '13'),
('130208', 'Casa Grande', '1302', '13'),
('130301', 'Bolívar', '1303', '13'),
('130302', 'Bambamarca', '1303', '13'),
('130303', 'Condormarca', '1303', '13'),
('130304', 'Longotea', '1303', '13'),
('130305', 'Uchumarca', '1303', '13'),
('130306', 'Ucuncha', '1303', '13'),
('130401', 'Chepen', '1304', '13'),
('130402', 'Pacanga', '1304', '13'),
('130403', 'Pueblo Nuevo', '1304', '13'),
('130501', 'Julcan', '1305', '13'),
('130502', 'Calamarca', '1305', '13'),
('130503', 'Carabamba', '1305', '13'),
('130504', 'Huaso', '1305', '13'),
('130601', 'Otuzco', '1306', '13'),
('130602', 'Agallpampa', '1306', '13'),
('130604', 'Charat', '1306', '13'),
('130605', 'Huaranchal', '1306', '13'),
('130606', 'La Cuesta', '1306', '13'),
('130608', 'Mache', '1306', '13'),
('130610', 'Paranday', '1306', '13'),
('130611', 'Salpo', '1306', '13'),
('130613', 'Sinsicap', '1306', '13'),
('130614', 'Usquil', '1306', '13'),
('130701', 'San Pedro de Lloc', '1307', '13'),
('130702', 'Guadalupe', '1307', '13'),
('130703', 'Jequetepeque', '1307', '13'),
('130704', 'Pacasmayo', '1307', '13'),
('130705', 'San José', '1307', '13'),
('130801', 'Tayabamba', '1308', '13'),
('130802', 'Buldibuyo', '1308', '13'),
('130803', 'Chillia', '1308', '13'),
('130804', 'Huancaspata', '1308', '13'),
('130805', 'Huaylillas', '1308', '13'),
('130806', 'Huayo', '1308', '13'),
('130807', 'Ongon', '1308', '13'),
('130808', 'Parcoy', '1308', '13'),
('130809', 'Pataz', '1308', '13'),
('130810', 'Pias', '1308', '13'),
('130811', 'Santiago de Challas', '1308', '13'),
('130812', 'Taurija', '1308', '13'),
('130813', 'Urpay', '1308', '13'),
('130901', 'Huamachuco', '1309', '13'),
('130902', 'Chugay', '1309', '13'),
('130903', 'Cochorco', '1309', '13'),
('130904', 'Curgos', '1309', '13'),
('130905', 'Marcabal', '1309', '13'),
('130906', 'Sanagoran', '1309', '13'),
('130907', 'Sarin', '1309', '13'),
('130908', 'Sartimbamba', '1309', '13'),
('131001', 'Santiago de Chuco', '1310', '13'),
('131002', 'Angasmarca', '1310', '13'),
('131003', 'Cachicadan', '1310', '13'),
('131004', 'Mollebamba', '1310', '13'),
('131005', 'Mollepata', '1310', '13'),
('131006', 'Quiruvilca', '1310', '13'),
('131007', 'Santa Cruz de Chuca', '1310', '13'),
('131008', 'Sitabamba', '1310', '13'),
('131101', 'Cascas', '1311', '13'),
('131102', 'Lucma', '1311', '13'),
('131103', 'Marmot', '1311', '13'),
('131104', 'Sayapullo', '1311', '13'),
('131201', 'Viru', '1312', '13'),
('131202', 'Chao', '1312', '13'),
('131203', 'Guadalupito', '1312', '13'),
('140101', 'Chiclayo', '1401', '14'),
('140102', 'Chongoyape', '1401', '14'),
('140103', 'Eten', '1401', '14'),
('140104', 'Eten Puerto', '1401', '14'),
('140105', 'José Leonardo Ortiz', '1401', '14'),
('140106', 'La Victoria', '1401', '14'),
('140107', 'Lagunas', '1401', '14'),
('140108', 'Monsefu', '1401', '14'),
('140109', 'Nueva Arica', '1401', '14'),
('140110', 'Oyotun', '1401', '14'),
('140111', 'Picsi', '1401', '14'),
('140112', 'Pimentel', '1401', '14'),
('140113', 'Reque', '1401', '14'),
('140114', 'Santa Rosa', '1401', '14'),
('140115', 'Saña', '1401', '14'),
('140116', 'Cayalti', '1401', '14'),
('140117', 'Patapo', '1401', '14'),
('140118', 'Pomalca', '1401', '14'),
('140119', 'Pucala', '1401', '14'),
('140120', 'Tuman', '1401', '14'),
('140201', 'Ferreñafe', '1402', '14'),
('140202', 'Cañaris', '1402', '14'),
('140203', 'Incahuasi', '1402', '14'),
('140204', 'Manuel Antonio Mesones Muro', '1402', '14'),
('140205', 'Pitipo', '1402', '14'),
('140206', 'Pueblo Nuevo', '1402', '14'),
('140301', 'Lambayeque', '1403', '14'),
('140302', 'Chochope', '1403', '14'),
('140303', 'Illimo', '1403', '14'),
('140304', 'Jayanca', '1403', '14'),
('140305', 'Mochumi', '1403', '14'),
('140306', 'Morrope', '1403', '14'),
('140307', 'Motupe', '1403', '14'),
('140308', 'Olmos', '1403', '14'),
('140309', 'Pacora', '1403', '14'),
('140310', 'Salas', '1403', '14'),
('140311', 'San José', '1403', '14'),
('140312', 'Tucume', '1403', '14'),
('150101', 'Lima', '1501', '15'),
('150102', 'Ancón', '1501', '15'),
('150103', 'Ate', '1501', '15'),
('150104', 'Barranco', '1501', '15'),
('150105', 'Breña', '1501', '15'),
('150106', 'Carabayllo', '1501', '15'),
('150107', 'Chaclacayo', '1501', '15'),
('150108', 'Chorrillos', '1501', '15'),
('150109', 'Cieneguilla', '1501', '15'),
('150110', 'Comas', '1501', '15'),
('150111', 'El Agustino', '1501', '15'),
('150112', 'Independencia', '1501', '15'),
('150113', 'Jesús María', '1501', '15'),
('150114', 'La Molina', '1501', '15'),
('150115', 'La Victoria', '1501', '15'),
('150116', 'Lince', '1501', '15'),
('150117', 'Los Olivos', '1501', '15'),
('150118', 'Lurigancho', '1501', '15'),
('150119', 'Lurin', '1501', '15'),
('150120', 'Magdalena del Mar', '1501', '15'),
('150121', 'Pueblo Libre', '1501', '15'),
('150122', 'Miraflores', '1501', '15'),
('150123', 'Pachacamac', '1501', '15'),
('150124', 'Pucusana', '1501', '15'),
('150125', 'Puente Piedra', '1501', '15'),
('150126', 'Punta Hermosa', '1501', '15'),
('150127', 'Punta Negra', '1501', '15'),
('150128', 'Rímac', '1501', '15'),
('150129', 'San Bartolo', '1501', '15'),
('150130', 'San Borja', '1501', '15'),
('150131', 'San Isidro', '1501', '15'),
('150132', 'San Juan de Lurigancho', '1501', '15'),
('150133', 'San Juan de Miraflores', '1501', '15'),
('150134', 'San Luis', '1501', '15'),
('150135', 'San Martín de Porres', '1501', '15'),
('150136', 'San Miguel', '1501', '15'),
('150137', 'Santa Anita', '1501', '15'),
('150138', 'Santa María del Mar', '1501', '15'),
('150139', 'Santa Rosa', '1501', '15'),
('150140', 'Santiago de Surco', '1501', '15'),
('150141', 'Surquillo', '1501', '15'),
('150142', 'Villa El Salvador', '1501', '15'),
('150143', 'Villa María del Triunfo', '1501', '15'),
('150201', 'Barranca', '1502', '15'),
('150202', 'Paramonga', '1502', '15'),
('150203', 'Pativilca', '1502', '15'),
('150204', 'Supe', '1502', '15'),
('150205', 'Supe Puerto', '1502', '15'),
('150301', 'Cajatambo', '1503', '15'),
('150302', 'Copa', '1503', '15'),
('150303', 'Gorgor', '1503', '15'),
('150304', 'Huancapon', '1503', '15'),
('150305', 'Manas', '1503', '15'),
('150401', 'Canta', '1504', '15'),
('150402', 'Arahuay', '1504', '15'),
('150403', 'Huamantanga', '1504', '15'),
('150404', 'Huaros', '1504', '15'),
('150405', 'Lachaqui', '1504', '15'),
('150406', 'San Buenaventura', '1504', '15'),
('150407', 'Santa Rosa de Quives', '1504', '15');
INSERT INTO `ubigeo_peru_districts` (`id`, `name`, `province_id`, `department_id`) VALUES
('150501', 'San Vicente de Cañete', '1505', '15'),
('150502', 'Asia', '1505', '15'),
('150503', 'Calango', '1505', '15'),
('150504', 'Cerro Azul', '1505', '15'),
('150505', 'Chilca', '1505', '15'),
('150506', 'Coayllo', '1505', '15'),
('150507', 'Imperial', '1505', '15'),
('150508', 'Lunahuana', '1505', '15'),
('150509', 'Mala', '1505', '15'),
('150510', 'Nuevo Imperial', '1505', '15'),
('150511', 'Pacaran', '1505', '15'),
('150512', 'Quilmana', '1505', '15'),
('150513', 'San Antonio', '1505', '15'),
('150514', 'San Luis', '1505', '15'),
('150515', 'Santa Cruz de Flores', '1505', '15'),
('150516', 'Zúñiga', '1505', '15'),
('150601', 'Huaral', '1506', '15'),
('150602', 'Atavillos Alto', '1506', '15'),
('150603', 'Atavillos Bajo', '1506', '15'),
('150604', 'Aucallama', '1506', '15'),
('150605', 'Chancay', '1506', '15'),
('150606', 'Ihuari', '1506', '15'),
('150607', 'Lampian', '1506', '15'),
('150608', 'Pacaraos', '1506', '15'),
('150609', 'San Miguel de Acos', '1506', '15'),
('150610', 'Santa Cruz de Andamarca', '1506', '15'),
('150611', 'Sumbilca', '1506', '15'),
('150612', 'Veintisiete de Noviembre', '1506', '15'),
('150701', 'Matucana', '1507', '15'),
('150702', 'Antioquia', '1507', '15'),
('150703', 'Callahuanca', '1507', '15'),
('150704', 'Carampoma', '1507', '15'),
('150705', 'Chicla', '1507', '15'),
('150706', 'Cuenca', '1507', '15'),
('150707', 'Huachupampa', '1507', '15'),
('150708', 'Huanza', '1507', '15'),
('150709', 'Huarochiri', '1507', '15'),
('150710', 'Lahuaytambo', '1507', '15'),
('150711', 'Langa', '1507', '15'),
('150712', 'Laraos', '1507', '15'),
('150713', 'Mariatana', '1507', '15'),
('150714', 'Ricardo Palma', '1507', '15'),
('150715', 'San Andrés de Tupicocha', '1507', '15'),
('150716', 'San Antonio', '1507', '15'),
('150717', 'San Bartolomé', '1507', '15'),
('150718', 'San Damian', '1507', '15'),
('150719', 'San Juan de Iris', '1507', '15'),
('150720', 'San Juan de Tantaranche', '1507', '15'),
('150721', 'San Lorenzo de Quinti', '1507', '15'),
('150722', 'San Mateo', '1507', '15'),
('150723', 'San Mateo de Otao', '1507', '15'),
('150724', 'San Pedro de Casta', '1507', '15'),
('150725', 'San Pedro de Huancayre', '1507', '15'),
('150726', 'Sangallaya', '1507', '15'),
('150727', 'Santa Cruz de Cocachacra', '1507', '15'),
('150728', 'Santa Eulalia', '1507', '15'),
('150729', 'Santiago de Anchucaya', '1507', '15'),
('150730', 'Santiago de Tuna', '1507', '15'),
('150731', 'Santo Domingo de Los Olleros', '1507', '15'),
('150732', 'Surco', '1507', '15'),
('150801', 'Huacho', '1508', '15'),
('150802', 'Ambar', '1508', '15'),
('150803', 'Caleta de Carquin', '1508', '15'),
('150804', 'Checras', '1508', '15'),
('150805', 'Hualmay', '1508', '15'),
('150806', 'Huaura', '1508', '15'),
('150807', 'Leoncio Prado', '1508', '15'),
('150808', 'Paccho', '1508', '15'),
('150809', 'Santa Leonor', '1508', '15'),
('150810', 'Santa María', '1508', '15'),
('150811', 'Sayan', '1508', '15'),
('150812', 'Vegueta', '1508', '15'),
('150901', 'Oyon', '1509', '15'),
('150902', 'Andajes', '1509', '15'),
('150903', 'Caujul', '1509', '15'),
('150904', 'Cochamarca', '1509', '15'),
('150905', 'Navan', '1509', '15'),
('150906', 'Pachangara', '1509', '15'),
('151001', 'Yauyos', '1510', '15'),
('151002', 'Alis', '1510', '15'),
('151003', 'Allauca', '1510', '15'),
('151004', 'Ayaviri', '1510', '15'),
('151005', 'Azángaro', '1510', '15'),
('151006', 'Cacra', '1510', '15'),
('151007', 'Carania', '1510', '15'),
('151008', 'Catahuasi', '1510', '15'),
('151009', 'Chocos', '1510', '15'),
('151010', 'Cochas', '1510', '15'),
('151011', 'Colonia', '1510', '15'),
('151012', 'Hongos', '1510', '15'),
('151013', 'Huampara', '1510', '15'),
('151014', 'Huancaya', '1510', '15'),
('151015', 'Huangascar', '1510', '15'),
('151016', 'Huantan', '1510', '15'),
('151017', 'Huañec', '1510', '15'),
('151018', 'Laraos', '1510', '15'),
('151019', 'Lincha', '1510', '15'),
('151020', 'Madean', '1510', '15'),
('151021', 'Miraflores', '1510', '15'),
('151022', 'Omas', '1510', '15'),
('151023', 'Putinza', '1510', '15'),
('151024', 'Quinches', '1510', '15'),
('151025', 'Quinocay', '1510', '15'),
('151026', 'San Joaquín', '1510', '15'),
('151027', 'San Pedro de Pilas', '1510', '15'),
('151028', 'Tanta', '1510', '15'),
('151029', 'Tauripampa', '1510', '15'),
('151030', 'Tomas', '1510', '15'),
('151031', 'Tupe', '1510', '15'),
('151032', 'Viñac', '1510', '15'),
('151033', 'Vitis', '1510', '15'),
('160101', 'Iquitos', '1601', '16'),
('160102', 'Alto Nanay', '1601', '16'),
('160103', 'Fernando Lores', '1601', '16'),
('160104', 'Indiana', '1601', '16'),
('160105', 'Las Amazonas', '1601', '16'),
('160106', 'Mazan', '1601', '16'),
('160107', 'Napo', '1601', '16'),
('160108', 'Punchana', '1601', '16'),
('160110', 'Torres Causana', '1601', '16'),
('160112', 'Belén', '1601', '16'),
('160113', 'San Juan Bautista', '1601', '16'),
('160201', 'Yurimaguas', '1602', '16'),
('160202', 'Balsapuerto', '1602', '16'),
('160205', 'Jeberos', '1602', '16'),
('160206', 'Lagunas', '1602', '16'),
('160210', 'Santa Cruz', '1602', '16'),
('160211', 'Teniente Cesar López Rojas', '1602', '16'),
('160301', 'Nauta', '1603', '16'),
('160302', 'Parinari', '1603', '16'),
('160303', 'Tigre', '1603', '16'),
('160304', 'Trompeteros', '1603', '16'),
('160305', 'Urarinas', '1603', '16'),
('160401', 'Ramón Castilla', '1604', '16'),
('160402', 'Pebas', '1604', '16'),
('160403', 'Yavari', '1604', '16'),
('160404', 'San Pablo', '1604', '16'),
('160501', 'Requena', '1605', '16'),
('160502', 'Alto Tapiche', '1605', '16'),
('160503', 'Capelo', '1605', '16'),
('160504', 'Emilio San Martín', '1605', '16'),
('160505', 'Maquia', '1605', '16'),
('160506', 'Puinahua', '1605', '16'),
('160507', 'Saquena', '1605', '16'),
('160508', 'Soplin', '1605', '16'),
('160509', 'Tapiche', '1605', '16'),
('160510', 'Jenaro Herrera', '1605', '16'),
('160511', 'Yaquerana', '1605', '16'),
('160601', 'Contamana', '1606', '16'),
('160602', 'Inahuaya', '1606', '16'),
('160603', 'Padre Márquez', '1606', '16'),
('160604', 'Pampa Hermosa', '1606', '16'),
('160605', 'Sarayacu', '1606', '16'),
('160606', 'Vargas Guerra', '1606', '16'),
('160701', 'Barranca', '1607', '16'),
('160702', 'Cahuapanas', '1607', '16'),
('160703', 'Manseriche', '1607', '16'),
('160704', 'Morona', '1607', '16'),
('160705', 'Pastaza', '1607', '16'),
('160706', 'Andoas', '1607', '16'),
('160801', 'Putumayo', '1608', '16'),
('160802', 'Rosa Panduro', '1608', '16'),
('160803', 'Teniente Manuel Clavero', '1608', '16'),
('160804', 'Yaguas', '1608', '16'),
('170101', 'Tambopata', '1701', '17'),
('170102', 'Inambari', '1701', '17'),
('170103', 'Las Piedras', '1701', '17'),
('170104', 'Laberinto', '1701', '17'),
('170201', 'Manu', '1702', '17'),
('170202', 'Fitzcarrald', '1702', '17'),
('170203', 'Madre de Dios', '1702', '17'),
('170204', 'Huepetuhe', '1702', '17'),
('170301', 'Iñapari', '1703', '17'),
('170302', 'Iberia', '1703', '17'),
('170303', 'Tahuamanu', '1703', '17'),
('180101', 'Moquegua', '1801', '18'),
('180102', 'Carumas', '1801', '18'),
('180103', 'Cuchumbaya', '1801', '18'),
('180104', 'Samegua', '1801', '18'),
('180105', 'San Cristóbal', '1801', '18'),
('180106', 'Torata', '1801', '18'),
('180201', 'Omate', '1802', '18'),
('180202', 'Chojata', '1802', '18'),
('180203', 'Coalaque', '1802', '18'),
('180204', 'Ichuña', '1802', '18'),
('180205', 'La Capilla', '1802', '18'),
('180206', 'Lloque', '1802', '18'),
('180207', 'Matalaque', '1802', '18'),
('180208', 'Puquina', '1802', '18'),
('180209', 'Quinistaquillas', '1802', '18'),
('180210', 'Ubinas', '1802', '18'),
('180211', 'Yunga', '1802', '18'),
('180301', 'Ilo', '1803', '18'),
('180302', 'El Algarrobal', '1803', '18'),
('180303', 'Pacocha', '1803', '18'),
('190101', 'Chaupimarca', '1901', '19'),
('190102', 'Huachon', '1901', '19'),
('190103', 'Huariaca', '1901', '19'),
('190104', 'Huayllay', '1901', '19'),
('190105', 'Ninacaca', '1901', '19'),
('190106', 'Pallanchacra', '1901', '19'),
('190107', 'Paucartambo', '1901', '19'),
('190108', 'San Francisco de Asís de Yarusyacan', '1901', '19'),
('190109', 'Simon Bolívar', '1901', '19'),
('190110', 'Ticlacayan', '1901', '19'),
('190111', 'Tinyahuarco', '1901', '19'),
('190112', 'Vicco', '1901', '19'),
('190113', 'Yanacancha', '1901', '19'),
('190201', 'Yanahuanca', '1902', '19'),
('190202', 'Chacayan', '1902', '19'),
('190203', 'Goyllarisquizga', '1902', '19'),
('190204', 'Paucar', '1902', '19'),
('190205', 'San Pedro de Pillao', '1902', '19'),
('190206', 'Santa Ana de Tusi', '1902', '19'),
('190207', 'Tapuc', '1902', '19'),
('190208', 'Vilcabamba', '1902', '19'),
('190301', 'Oxapampa', '1903', '19'),
('190302', 'Chontabamba', '1903', '19'),
('190303', 'Huancabamba', '1903', '19'),
('190304', 'Palcazu', '1903', '19'),
('190305', 'Pozuzo', '1903', '19'),
('190306', 'Puerto Bermúdez', '1903', '19'),
('190307', 'Villa Rica', '1903', '19'),
('190308', 'Constitución', '1903', '19'),
('200101', 'Piura', '2001', '20'),
('200104', 'Castilla', '2001', '20'),
('200105', 'Catacaos', '2001', '20'),
('200107', 'Cura Mori', '2001', '20'),
('200108', 'El Tallan', '2001', '20'),
('200109', 'La Arena', '2001', '20'),
('200110', 'La Unión', '2001', '20'),
('200111', 'Las Lomas', '2001', '20'),
('200114', 'Tambo Grande', '2001', '20'),
('200115', 'Veintiseis de Octubre', '2001', '20'),
('200201', 'Ayabaca', '2002', '20'),
('200202', 'Frias', '2002', '20'),
('200203', 'Jilili', '2002', '20'),
('200204', 'Lagunas', '2002', '20'),
('200205', 'Montero', '2002', '20'),
('200206', 'Pacaipampa', '2002', '20'),
('200207', 'Paimas', '2002', '20'),
('200208', 'Sapillica', '2002', '20'),
('200209', 'Sicchez', '2002', '20'),
('200210', 'Suyo', '2002', '20'),
('200301', 'Huancabamba', '2003', '20'),
('200302', 'Canchaque', '2003', '20'),
('200303', 'El Carmen de la Frontera', '2003', '20'),
('200304', 'Huarmaca', '2003', '20'),
('200305', 'Lalaquiz', '2003', '20'),
('200306', 'San Miguel de El Faique', '2003', '20'),
('200307', 'Sondor', '2003', '20'),
('200308', 'Sondorillo', '2003', '20'),
('200401', 'Chulucanas', '2004', '20'),
('200402', 'Buenos Aires', '2004', '20'),
('200403', 'Chalaco', '2004', '20'),
('200404', 'La Matanza', '2004', '20'),
('200405', 'Morropon', '2004', '20'),
('200406', 'Salitral', '2004', '20'),
('200407', 'San Juan de Bigote', '2004', '20'),
('200408', 'Santa Catalina de Mossa', '2004', '20'),
('200409', 'Santo Domingo', '2004', '20'),
('200410', 'Yamango', '2004', '20'),
('200501', 'Paita', '2005', '20'),
('200502', 'Amotape', '2005', '20'),
('200503', 'Arenal', '2005', '20'),
('200504', 'Colan', '2005', '20'),
('200505', 'La Huaca', '2005', '20'),
('200506', 'Tamarindo', '2005', '20'),
('200507', 'Vichayal', '2005', '20'),
('200601', 'Sullana', '2006', '20'),
('200602', 'Bellavista', '2006', '20'),
('200603', 'Ignacio Escudero', '2006', '20'),
('200604', 'Lancones', '2006', '20'),
('200605', 'Marcavelica', '2006', '20'),
('200606', 'Miguel Checa', '2006', '20'),
('200607', 'Querecotillo', '2006', '20'),
('200608', 'Salitral', '2006', '20'),
('200701', 'Pariñas', '2007', '20'),
('200702', 'El Alto', '2007', '20'),
('200703', 'La Brea', '2007', '20'),
('200704', 'Lobitos', '2007', '20'),
('200705', 'Los Organos', '2007', '20'),
('200706', 'Mancora', '2007', '20'),
('200801', 'Sechura', '2008', '20'),
('200802', 'Bellavista de la Unión', '2008', '20'),
('200803', 'Bernal', '2008', '20'),
('200804', 'Cristo Nos Valga', '2008', '20'),
('200805', 'Vice', '2008', '20'),
('200806', 'Rinconada Llicuar', '2008', '20'),
('210101', 'Puno', '2101', '21'),
('210102', 'Acora', '2101', '21'),
('210103', 'Amantani', '2101', '21'),
('210104', 'Atuncolla', '2101', '21'),
('210105', 'Capachica', '2101', '21'),
('210106', 'Chucuito', '2101', '21'),
('210107', 'Coata', '2101', '21'),
('210108', 'Huata', '2101', '21'),
('210109', 'Mañazo', '2101', '21'),
('210110', 'Paucarcolla', '2101', '21'),
('210111', 'Pichacani', '2101', '21'),
('210112', 'Plateria', '2101', '21'),
('210113', 'San Antonio', '2101', '21'),
('210114', 'Tiquillaca', '2101', '21'),
('210115', 'Vilque', '2101', '21'),
('210201', 'Azángaro', '2102', '21'),
('210202', 'Achaya', '2102', '21'),
('210203', 'Arapa', '2102', '21'),
('210204', 'Asillo', '2102', '21'),
('210205', 'Caminaca', '2102', '21'),
('210206', 'Chupa', '2102', '21'),
('210207', 'José Domingo Choquehuanca', '2102', '21'),
('210208', 'Muñani', '2102', '21'),
('210209', 'Potoni', '2102', '21'),
('210210', 'Saman', '2102', '21'),
('210211', 'San Anton', '2102', '21'),
('210212', 'San José', '2102', '21'),
('210213', 'San Juan de Salinas', '2102', '21'),
('210214', 'Santiago de Pupuja', '2102', '21'),
('210215', 'Tirapata', '2102', '21'),
('210301', 'Macusani', '2103', '21'),
('210302', 'Ajoyani', '2103', '21'),
('210303', 'Ayapata', '2103', '21'),
('210304', 'Coasa', '2103', '21'),
('210305', 'Corani', '2103', '21'),
('210306', 'Crucero', '2103', '21'),
('210307', 'Ituata', '2103', '21'),
('210308', 'Ollachea', '2103', '21'),
('210309', 'San Gaban', '2103', '21'),
('210310', 'Usicayos', '2103', '21'),
('210401', 'Juli', '2104', '21'),
('210402', 'Desaguadero', '2104', '21'),
('210403', 'Huacullani', '2104', '21'),
('210404', 'Kelluyo', '2104', '21'),
('210405', 'Pisacoma', '2104', '21'),
('210406', 'Pomata', '2104', '21'),
('210407', 'Zepita', '2104', '21'),
('210501', 'Ilave', '2105', '21'),
('210502', 'Capazo', '2105', '21'),
('210503', 'Pilcuyo', '2105', '21'),
('210504', 'Santa Rosa', '2105', '21'),
('210505', 'Conduriri', '2105', '21'),
('210601', 'Huancane', '2106', '21'),
('210602', 'Cojata', '2106', '21'),
('210603', 'Huatasani', '2106', '21'),
('210604', 'Inchupalla', '2106', '21'),
('210605', 'Pusi', '2106', '21'),
('210606', 'Rosaspata', '2106', '21'),
('210607', 'Taraco', '2106', '21'),
('210608', 'Vilque Chico', '2106', '21'),
('210701', 'Lampa', '2107', '21'),
('210702', 'Cabanilla', '2107', '21'),
('210703', 'Calapuja', '2107', '21'),
('210704', 'Nicasio', '2107', '21'),
('210705', 'Ocuviri', '2107', '21'),
('210706', 'Palca', '2107', '21'),
('210707', 'Paratia', '2107', '21'),
('210708', 'Pucara', '2107', '21'),
('210709', 'Santa Lucia', '2107', '21'),
('210710', 'Vilavila', '2107', '21'),
('210801', 'Ayaviri', '2108', '21'),
('210802', 'Antauta', '2108', '21'),
('210803', 'Cupi', '2108', '21'),
('210804', 'Llalli', '2108', '21'),
('210805', 'Macari', '2108', '21'),
('210806', 'Nuñoa', '2108', '21'),
('210807', 'Orurillo', '2108', '21'),
('210808', 'Santa Rosa', '2108', '21'),
('210809', 'Umachiri', '2108', '21'),
('210901', 'Moho', '2109', '21'),
('210902', 'Conima', '2109', '21'),
('210903', 'Huayrapata', '2109', '21'),
('210904', 'Tilali', '2109', '21'),
('211001', 'Putina', '2110', '21'),
('211002', 'Ananea', '2110', '21'),
('211003', 'Pedro Vilca Apaza', '2110', '21'),
('211004', 'Quilcapuncu', '2110', '21'),
('211005', 'Sina', '2110', '21'),
('211101', 'Juliaca', '2111', '21'),
('211102', 'Cabana', '2111', '21'),
('211103', 'Cabanillas', '2111', '21'),
('211104', 'Caracoto', '2111', '21'),
('211105', 'San Miguel', '2111', '21'),
('211201', 'Sandia', '2112', '21'),
('211202', 'Cuyocuyo', '2112', '21'),
('211203', 'Limbani', '2112', '21'),
('211204', 'Patambuco', '2112', '21'),
('211205', 'Phara', '2112', '21'),
('211206', 'Quiaca', '2112', '21'),
('211207', 'San Juan del Oro', '2112', '21'),
('211208', 'Yanahuaya', '2112', '21'),
('211209', 'Alto Inambari', '2112', '21'),
('211210', 'San Pedro de Putina Punco', '2112', '21'),
('211301', 'Yunguyo', '2113', '21'),
('211302', 'Anapia', '2113', '21'),
('211303', 'Copani', '2113', '21'),
('211304', 'Cuturapi', '2113', '21'),
('211305', 'Ollaraya', '2113', '21'),
('211306', 'Tinicachi', '2113', '21'),
('211307', 'Unicachi', '2113', '21'),
('220101', 'Moyobamba', '2201', '22'),
('220102', 'Calzada', '2201', '22'),
('220103', 'Habana', '2201', '22'),
('220104', 'Jepelacio', '2201', '22'),
('220105', 'Soritor', '2201', '22'),
('220106', 'Yantalo', '2201', '22'),
('220201', 'Bellavista', '2202', '22'),
('220202', 'Alto Biavo', '2202', '22'),
('220203', 'Bajo Biavo', '2202', '22'),
('220204', 'Huallaga', '2202', '22'),
('220205', 'San Pablo', '2202', '22'),
('220206', 'San Rafael', '2202', '22'),
('220301', 'San José de Sisa', '2203', '22'),
('220302', 'Agua Blanca', '2203', '22'),
('220303', 'San Martín', '2203', '22'),
('220304', 'Santa Rosa', '2203', '22'),
('220305', 'Shatoja', '2203', '22'),
('220401', 'Saposoa', '2204', '22'),
('220402', 'Alto Saposoa', '2204', '22'),
('220403', 'El Eslabón', '2204', '22'),
('220404', 'Piscoyacu', '2204', '22'),
('220405', 'Sacanche', '2204', '22'),
('220406', 'Tingo de Saposoa', '2204', '22'),
('220501', 'Lamas', '2205', '22'),
('220502', 'Alonso de Alvarado', '2205', '22'),
('220503', 'Barranquita', '2205', '22'),
('220504', 'Caynarachi', '2205', '22'),
('220505', 'Cuñumbuqui', '2205', '22'),
('220506', 'Pinto Recodo', '2205', '22'),
('220507', 'Rumisapa', '2205', '22'),
('220508', 'San Roque de Cumbaza', '2205', '22'),
('220509', 'Shanao', '2205', '22'),
('220510', 'Tabalosos', '2205', '22'),
('220511', 'Zapatero', '2205', '22'),
('220601', 'Juanjuí', '2206', '22'),
('220602', 'Campanilla', '2206', '22'),
('220603', 'Huicungo', '2206', '22'),
('220604', 'Pachiza', '2206', '22'),
('220605', 'Pajarillo', '2206', '22'),
('220701', 'Picota', '2207', '22'),
('220702', 'Buenos Aires', '2207', '22'),
('220703', 'Caspisapa', '2207', '22'),
('220704', 'Pilluana', '2207', '22'),
('220705', 'Pucacaca', '2207', '22'),
('220706', 'San Cristóbal', '2207', '22'),
('220707', 'San Hilarión', '2207', '22'),
('220708', 'Shamboyacu', '2207', '22'),
('220709', 'Tingo de Ponasa', '2207', '22'),
('220710', 'Tres Unidos', '2207', '22'),
('220801', 'Rioja', '2208', '22'),
('220802', 'Awajun', '2208', '22'),
('220803', 'Elías Soplin Vargas', '2208', '22'),
('220804', 'Nueva Cajamarca', '2208', '22'),
('220805', 'Pardo Miguel', '2208', '22'),
('220806', 'Posic', '2208', '22'),
('220807', 'San Fernando', '2208', '22'),
('220808', 'Yorongos', '2208', '22'),
('220809', 'Yuracyacu', '2208', '22'),
('220901', 'Tarapoto', '2209', '22'),
('220902', 'Alberto Leveau', '2209', '22'),
('220903', 'Cacatachi', '2209', '22'),
('220904', 'Chazuta', '2209', '22'),
('220905', 'Chipurana', '2209', '22'),
('220906', 'El Porvenir', '2209', '22'),
('220907', 'Huimbayoc', '2209', '22'),
('220908', 'Juan Guerra', '2209', '22'),
('220909', 'La Banda de Shilcayo', '2209', '22'),
('220910', 'Morales', '2209', '22'),
('220911', 'Papaplaya', '2209', '22'),
('220912', 'San Antonio', '2209', '22'),
('220913', 'Sauce', '2209', '22'),
('220914', 'Shapaja', '2209', '22'),
('221001', 'Tocache', '2210', '22'),
('221002', 'Nuevo Progreso', '2210', '22'),
('221003', 'Polvora', '2210', '22'),
('221004', 'Shunte', '2210', '22'),
('221005', 'Uchiza', '2210', '22'),
('230101', 'Tacna', '2301', '23'),
('230102', 'Alto de la Alianza', '2301', '23'),
('230103', 'Calana', '2301', '23'),
('230104', 'Ciudad Nueva', '2301', '23'),
('230105', 'Inclan', '2301', '23'),
('230106', 'Pachia', '2301', '23'),
('230107', 'Palca', '2301', '23'),
('230108', 'Pocollay', '2301', '23'),
('230109', 'Sama', '2301', '23'),
('230110', 'Coronel Gregorio Albarracín Lanchipa', '2301', '23'),
('230111', 'La Yarada los Palos', '2301', '23'),
('230201', 'Candarave', '2302', '23'),
('230202', 'Cairani', '2302', '23'),
('230203', 'Camilaca', '2302', '23'),
('230204', 'Curibaya', '2302', '23'),
('230205', 'Huanuara', '2302', '23'),
('230206', 'Quilahuani', '2302', '23'),
('230301', 'Locumba', '2303', '23'),
('230302', 'Ilabaya', '2303', '23'),
('230303', 'Ite', '2303', '23'),
('230401', 'Tarata', '2304', '23'),
('230402', 'Héroes Albarracín', '2304', '23'),
('230403', 'Estique', '2304', '23'),
('230404', 'Estique-Pampa', '2304', '23'),
('230405', 'Sitajara', '2304', '23'),
('230406', 'Susapaya', '2304', '23'),
('230407', 'Tarucachi', '2304', '23'),
('230408', 'Ticaco', '2304', '23'),
('240101', 'Tumbes', '2401', '24'),
('240102', 'Corrales', '2401', '24'),
('240103', 'La Cruz', '2401', '24'),
('240104', 'Pampas de Hospital', '2401', '24'),
('240105', 'San Jacinto', '2401', '24'),
('240106', 'San Juan de la Virgen', '2401', '24'),
('240201', 'Zorritos', '2402', '24'),
('240202', 'Casitas', '2402', '24'),
('240203', 'Canoas de Punta Sal', '2402', '24'),
('240301', 'Zarumilla', '2403', '24'),
('240302', 'Aguas Verdes', '2403', '24'),
('240303', 'Matapalo', '2403', '24'),
('240304', 'Papayal', '2403', '24'),
('250101', 'Calleria', '2501', '25'),
('250102', 'Campoverde', '2501', '25'),
('250103', 'Iparia', '2501', '25'),
('250104', 'Masisea', '2501', '25'),
('250105', 'Yarinacocha', '2501', '25'),
('250106', 'Nueva Requena', '2501', '25'),
('250107', 'Manantay', '2501', '25'),
('250201', 'Raymondi', '2502', '25'),
('250202', 'Sepahua', '2502', '25'),
('250203', 'Tahuania', '2502', '25'),
('250204', 'Yurua', '2502', '25'),
('250301', 'Padre Abad', '2503', '25'),
('250302', 'Irazola', '2503', '25'),
('250303', 'Curimana', '2503', '25'),
('250304', 'Neshuya', '2503', '25'),
('250305', 'Alexander Von Humboldt', '2503', '25'),
('250401', 'Purus', '2504', '25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubigeo_peru_provinces`
--

CREATE TABLE `ubigeo_peru_provinces` (
  `id` varchar(4) NOT NULL,
  `name` varchar(45) NOT NULL,
  `department_id` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `ubigeo_peru_provinces`
--

INSERT INTO `ubigeo_peru_provinces` (`id`, `name`, `department_id`) VALUES
('0101', 'Chachapoyas', '01'),
('0102', 'Bagua', '01'),
('0103', 'Bongará', '01'),
('0104', 'Condorcanqui', '01'),
('0105', 'Luya', '01'),
('0106', 'Rodríguez de Mendoza', '01'),
('0107', 'Utcubamba', '01'),
('0201', 'Huaraz', '02'),
('0202', 'Aija', '02'),
('0203', 'Antonio Raymondi', '02'),
('0204', 'Asunción', '02'),
('0205', 'Bolognesi', '02'),
('0206', 'Carhuaz', '02'),
('0207', 'Carlos Fermín Fitzcarrald', '02'),
('0208', 'Casma', '02'),
('0209', 'Corongo', '02'),
('0210', 'Huari', '02'),
('0211', 'Huarmey', '02'),
('0212', 'Huaylas', '02'),
('0213', 'Mariscal Luzuriaga', '02'),
('0214', 'Ocros', '02'),
('0215', 'Pallasca', '02'),
('0216', 'Pomabamba', '02'),
('0217', 'Recuay', '02'),
('0218', 'Santa', '02'),
('0219', 'Sihuas', '02'),
('0220', 'Yungay', '02'),
('0301', 'Abancay', '03'),
('0302', 'Andahuaylas', '03'),
('0303', 'Antabamba', '03'),
('0304', 'Aymaraes', '03'),
('0305', 'Cotabambas', '03'),
('0306', 'Chincheros', '03'),
('0307', 'Grau', '03'),
('0401', 'Arequipa', '04'),
('0402', 'Camaná', '04'),
('0403', 'Caravelí', '04'),
('0404', 'Castilla', '04'),
('0405', 'Caylloma', '04'),
('0406', 'Condesuyos', '04'),
('0407', 'Islay', '04'),
('0408', 'La Uniòn', '04'),
('0501', 'Huamanga', '05'),
('0502', 'Cangallo', '05'),
('0503', 'Huanca Sancos', '05'),
('0504', 'Huanta', '05'),
('0505', 'La Mar', '05'),
('0506', 'Lucanas', '05'),
('0507', 'Parinacochas', '05'),
('0508', 'Pàucar del Sara Sara', '05'),
('0509', 'Sucre', '05'),
('0510', 'Víctor Fajardo', '05'),
('0511', 'Vilcas Huamán', '05'),
('0601', 'Cajamarca', '06'),
('0602', 'Cajabamba', '06'),
('0603', 'Celendín', '06'),
('0604', 'Chota', '06'),
('0605', 'Contumazá', '06'),
('0606', 'Cutervo', '06'),
('0607', 'Hualgayoc', '06'),
('0608', 'Jaén', '06'),
('0609', 'San Ignacio', '06'),
('0610', 'San Marcos', '06'),
('0611', 'San Miguel', '06'),
('0612', 'San Pablo', '06'),
('0613', 'Santa Cruz', '06'),
('0701', 'Prov. Const. del Callao', '07'),
('0801', 'Cusco', '08'),
('0802', 'Acomayo', '08'),
('0803', 'Anta', '08'),
('0804', 'Calca', '08'),
('0805', 'Canas', '08'),
('0806', 'Canchis', '08'),
('0807', 'Chumbivilcas', '08'),
('0808', 'Espinar', '08'),
('0809', 'La Convención', '08'),
('0810', 'Paruro', '08'),
('0811', 'Paucartambo', '08'),
('0812', 'Quispicanchi', '08'),
('0813', 'Urubamba', '08'),
('0901', 'Huancavelica', '09'),
('0902', 'Acobamba', '09'),
('0903', 'Angaraes', '09'),
('0904', 'Castrovirreyna', '09'),
('0905', 'Churcampa', '09'),
('0906', 'Huaytará', '09'),
('0907', 'Tayacaja', '09'),
('1001', 'Huánuco', '10'),
('1002', 'Ambo', '10'),
('1003', 'Dos de Mayo', '10'),
('1004', 'Huacaybamba', '10'),
('1005', 'Huamalíes', '10'),
('1006', 'Leoncio Prado', '10'),
('1007', 'Marañón', '10'),
('1008', 'Pachitea', '10'),
('1009', 'Puerto Inca', '10'),
('1010', 'Lauricocha ', '10'),
('1011', 'Yarowilca ', '10'),
('1101', 'Ica ', '11'),
('1102', 'Chincha ', '11'),
('1103', 'Nasca ', '11'),
('1104', 'Palpa ', '11'),
('1105', 'Pisco ', '11'),
('1201', 'Huancayo ', '12'),
('1202', 'Concepción ', '12'),
('1203', 'Chanchamayo ', '12'),
('1204', 'Jauja ', '12'),
('1205', 'Junín ', '12'),
('1206', 'Satipo ', '12'),
('1207', 'Tarma ', '12'),
('1208', 'Yauli ', '12'),
('1209', 'Chupaca ', '12'),
('1301', 'Trujillo ', '13'),
('1302', 'Ascope ', '13'),
('1303', 'Bolívar ', '13'),
('1304', 'Chepén ', '13'),
('1305', 'Julcán ', '13'),
('1306', 'Otuzco ', '13'),
('1307', 'Pacasmayo ', '13'),
('1308', 'Pataz ', '13'),
('1309', 'Sánchez Carrión ', '13'),
('1310', 'Santiago de Chuco ', '13'),
('1311', 'Gran Chimú ', '13'),
('1312', 'Virú ', '13'),
('1401', 'Chiclayo ', '14'),
('1402', 'Ferreñafe ', '14'),
('1403', 'Lambayeque ', '14'),
('1501', 'Lima ', '15'),
('1502', 'Barranca ', '15'),
('1503', 'Cajatambo ', '15'),
('1504', 'Canta ', '15'),
('1505', 'Cañete ', '15'),
('1506', 'Huaral ', '15'),
('1507', 'Huarochirí ', '15'),
('1508', 'Huaura ', '15'),
('1509', 'Oyón ', '15'),
('1510', 'Yauyos ', '15'),
('1601', 'Maynas ', '16'),
('1602', 'Alto Amazonas ', '16'),
('1603', 'Loreto ', '16'),
('1604', 'Mariscal Ramón Castilla ', '16'),
('1605', 'Requena ', '16'),
('1606', 'Ucayali ', '16'),
('1607', 'Datem del Marañón ', '16'),
('1608', 'Putumayo', '16'),
('1701', 'Tambopata ', '17'),
('1702', 'Manu ', '17'),
('1703', 'Tahuamanu ', '17'),
('1801', 'Mariscal Nieto ', '18'),
('1802', 'General Sánchez Cerro ', '18'),
('1803', 'Ilo ', '18'),
('1901', 'Pasco ', '19'),
('1902', 'Daniel Alcides Carrión ', '19'),
('1903', 'Oxapampa ', '19'),
('2001', 'Piura ', '20'),
('2002', 'Ayabaca ', '20'),
('2003', 'Huancabamba ', '20'),
('2004', 'Morropón ', '20'),
('2005', 'Paita ', '20'),
('2006', 'Sullana ', '20'),
('2007', 'Talara ', '20'),
('2008', 'Sechura ', '20'),
('2101', 'Puno ', '21'),
('2102', 'Azángaro ', '21'),
('2103', 'Carabaya ', '21'),
('2104', 'Chucuito ', '21'),
('2105', 'El Collao ', '21'),
('2106', 'Huancané ', '21'),
('2107', 'Lampa ', '21'),
('2108', 'Melgar ', '21'),
('2109', 'Moho ', '21'),
('2110', 'San Antonio de Putina ', '21'),
('2111', 'San Román ', '21'),
('2112', 'Sandia ', '21'),
('2113', 'Yunguyo ', '21'),
('2201', 'Moyobamba ', '22'),
('2202', 'Bellavista ', '22'),
('2203', 'El Dorado ', '22'),
('2204', 'Huallaga ', '22'),
('2205', 'Lamas ', '22'),
('2206', 'Mariscal Cáceres ', '22'),
('2207', 'Picota ', '22'),
('2208', 'Rioja ', '22'),
('2209', 'San Martín ', '22'),
('2210', 'Tocache ', '22'),
('2301', 'Tacna ', '23'),
('2302', 'Candarave ', '23'),
('2303', 'Jorge Basadre ', '23'),
('2304', 'Tarata ', '23'),
('2401', 'Tumbes ', '24'),
('2402', 'Contralmirante Villar ', '24'),
('2403', 'Zarumilla ', '24'),
('2501', 'Coronel Portillo ', '25'),
('2502', 'Atalaya ', '25'),
('2503', 'Padre Abad ', '25'),
('2504', 'Purús', '25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `idunidad_medida` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `condicion` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `unidad_medida`
--

INSERT INTO `unidad_medida` (`idunidad_medida`, `nombre`, `condicion`) VALUES
(1, 'UNIDAD', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `idpersonal` int(11) NOT NULL,
  `login` varchar(20) NOT NULL,
  `clave` varchar(64) NOT NULL,
  `superusuario` tinyint(1) NOT NULL DEFAULT 0,
  `idsucursal` int(11) DEFAULT NULL,
  `condicion` tinyint(1) NOT NULL DEFAULT 1,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `idpersonal`, `login`, `clave`, `superusuario`, `idsucursal`, `condicion`, `reset_token`, `reset_expira`) VALUES
(1, 1, 'admin', '7676aaafb027c825bd9abab78b234070e702752f625b752e55e55b48e607e358', 1, NULL, 1, NULL, NULL),
(2, 2, 'juan', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 0, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_accion`
--

CREATE TABLE `usuario_accion` (
  `idusuario` int(11) NOT NULL,
  `idaccion_permiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario_accion`
--

INSERT INTO `usuario_accion` (`idusuario`, `idaccion_permiso`) VALUES
(1, 33),
(1, 37),
(1, 39),
(1, 42),
(1, 43),
(2, 36),
(2, 37),
(2, 38),
(2, 42),
(2, 43),
(2, 44),
(2, 45),
(2, 46);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_permiso`
--

CREATE TABLE `usuario_permiso` (
  `idusuario_permiso` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idpermiso` int(11) DEFAULT NULL,
  `idsubpermiso` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuario_permiso`
--

INSERT INTO `usuario_permiso` (`idusuario_permiso`, `idusuario`, `idpermiso`, `idsubpermiso`) VALUES
(54, 2, 1, NULL),
(55, 2, 3, NULL),
(56, 2, 1, 1),
(57, 2, 1, 2),
(58, 2, 3, 49),
(71, 1, 1, NULL),
(72, 1, 2, NULL),
(73, 1, 3, NULL),
(74, 1, 4, NULL),
(75, 1, 1, 1),
(76, 1, 1, 2),
(77, 1, 1, 5),
(78, 1, 2, 7),
(79, 1, 3, 49),
(80, 1, 4, 12),
(81, 1, 13, 35),
(82, 1, 13, 36),
(83, 1, 13, 37),
(84, 1, 13, 50),
(85, 1, 13, 51);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_sucursal`
--

CREATE TABLE `usuario_sucursal` (
  `idusuario_sucursal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario_sucursal`
--

INSERT INTO `usuario_sucursal` (`idusuario_sucursal`, `idusuario`, `idsucursal`, `created_at`, `updated_at`, `deleted_at`) VALUES
(9, 2, 1, '2026-07-14 12:41:38', NULL, NULL),
(12, 1, 1, '2026-07-16 21:49:03', NULL, NULL),
(13, 1, 2, '2026-07-16 21:49:03', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `idventa` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `idcaja` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `idpersonal` int(11) NOT NULL,
  `idmotivo_nota` int(11) DEFAULT NULL,
  `tipo_comprobante` varchar(20) NOT NULL,
  `serie_comprobante` varchar(7) DEFAULT NULL,
  `num_comprobante` varchar(10) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `impuesto` decimal(12,2) NOT NULL,
  `total_venta` decimal(11,2) NOT NULL,
  `ventacredito` varchar(20) NOT NULL,
  `interes` double DEFAULT NULL,
  `frecuencia` int(11) DEFAULT NULL,
  `meses` int(11) DEFAULT NULL,
  `formapago` varchar(50) DEFAULT NULL,
  `numoperacion` varchar(100) DEFAULT NULL,
  `fechadeposito` datetime DEFAULT NULL,
  `descuento` double DEFAULT NULL,
  `totalrecibido` double DEFAULT NULL,
  `totaldeposito` double DEFAULT NULL,
  `comisionV` decimal(11,2) DEFAULT NULL,
  `vuelto` double DEFAULT NULL,
  `nomCliente` varchar(250) DEFAULT NULL,
  `documento_rel` varchar(20) DEFAULT NULL,
  `dov_Estado` varchar(15) DEFAULT NULL,
  `dov_Nombre` varchar(100) DEFAULT NULL,
  `dov_IdEmpleado` int(11) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `estado` varchar(20) NOT NULL,
  `fecha_kardex` timestamp NOT NULL DEFAULT current_timestamp(),
  `banco` varchar(250) DEFAULT NULL,
  `montoPagado` decimal(11,2) DEFAULT NULL,
  `estadoS` varchar(50) NOT NULL DEFAULT 'TERMINADO',
  `estado_venta` int(11) NOT NULL DEFAULT 1,
  `nota` varchar(1000) DEFAULT NULL,
  `idgarante` int(11) DEFAULT NULL,
  `idtipoacompanante` int(11) DEFAULT NULL,
  `idacompanante` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `venta`
--

INSERT INTO `venta` (`idventa`, `idsucursal`, `idcaja`, `idcliente`, `idpersonal`, `idmotivo_nota`, `tipo_comprobante`, `serie_comprobante`, `num_comprobante`, `fecha_hora`, `impuesto`, `total_venta`, `ventacredito`, `interes`, `frecuencia`, `meses`, `formapago`, `numoperacion`, `fechadeposito`, `descuento`, `totalrecibido`, `totaldeposito`, `comisionV`, `vuelto`, `nomCliente`, `documento_rel`, `dov_Estado`, `dov_Nombre`, `dov_IdEmpleado`, `observacion`, `mensaje`, `estado`, `fecha_kardex`, `banco`, `montoPagado`, `estadoS`, `estado_venta`, `nota`, `idgarante`, `idtipoacompanante`, `idacompanante`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 3, 1, 0, 'Boleta', 'B001', '0000001', '2026-07-01 23:27:36', 0.00, 7000.00, 'Si', 10, 4, 18, 'Efectivo', '', '2026-07-01 23:27:36', 0, 500, 0, NULL, 0, NULL, '1', 'ACEPTADO', '20152458654-03-B001-0000001', 1, '', NULL, 'Aceptado', '2026-07-02 04:27:36', 'BCP', 500.00, 'TERMINADO', 1, NULL, 0, 0, 0, '2026-07-15 13:38:47', NULL, NULL),
(2, 1, 1, 3, 1, 0, 'Nota de Venta', 'P001', '0000001', '2026-07-08 21:50:13', 0.00, 7000.00, 'Si', 10, 3, 10, 'Efectivo', '', '2026-07-08 21:50:14', 0, 7000, 0, NULL, 0, NULL, '2', 'ACEPTADO', NULL, NULL, '', NULL, 'Activado', '2026-07-09 02:50:14', 'BCP', 0.00, 'TERMINADO', 1, NULL, 0, 0, 0, '2026-07-15 13:38:47', NULL, NULL),
(23, 2, 2, 15, 1, 0, 'Nota de Venta', 'P001', '0000002', '2026-07-15 14:35:29', 0.00, 4000.00, 'Si', 10, 4, 12, 'Efectivo', '', '2026-07-15 14:35:29', 0, 0, 0, NULL, 0, NULL, '7', 'ACEPTADO', NULL, NULL, '', NULL, 'Activado', '2026-07-15 19:35:29', 'BCP', 0.00, 'TERMINADO', 1, NULL, NULL, NULL, NULL, '2026-07-15 14:35:29', '2026-07-15 14:35:29', NULL),
(28, 2, 2, 1, 1, 0, 'Nota de Venta', 'P001', '0000003', '2026-07-15 14:57:00', 0.00, 9000.00, 'No', 0, 0, 0, 'Efectivo', '', '2026-07-15 14:57:00', 0, 9000, 0, NULL, 0, NULL, '', 'ACEPTADO', NULL, NULL, '', NULL, 'Activado', '2026-07-15 19:57:00', 'BCP', 0.00, 'TERMINADO', 1, NULL, NULL, NULL, NULL, '2026-07-15 14:57:00', '2026-07-15 14:57:00', NULL),
(29, 1, 1, 1, 1, 0, 'Nota de Venta', 'P001', '0000004', '2026-07-16 08:09:31', 1067.80, 7000.00, 'No', 0, 0, 0, 'Efectivo', '', '2026-07-16 08:09:31', 0, 7000, 0, NULL, 0, NULL, '', 'ACEPTADO', NULL, NULL, '', NULL, 'Activado', '2026-07-16 13:09:31', 'BCP', 0.00, 'TERMINADO', 1, NULL, NULL, NULL, NULL, '2026-07-16 08:09:31', '2026-07-16 08:09:31', NULL),
(49, 1, 1, 1, 1, 0, 'Nota de Venta', 'P001', '0000005', '2026-07-16 09:01:35', 1067.80, 7000.00, 'No', 0, 0, 0, 'Efectivo', '', '2026-07-16 09:01:35', 0, 7000, 0, NULL, 0, NULL, '', 'ACEPTADO', NULL, NULL, '', NULL, 'Activado', '2026-07-16 14:01:35', 'BCP', 0.00, 'TERMINADO', 1, NULL, NULL, NULL, NULL, '2026-07-16 09:01:35', '2026-07-16 09:01:35', NULL),
(53, 1, 1, 1, 1, 0, 'Nota de Venta', 'P001', '0000006', '2026-07-16 09:15:31', 1677.97, 11000.00, 'No', 0, 0, 0, 'Efectivo', '', '2026-07-16 09:15:31', 0, 11000, 0, NULL, 0, NULL, '', 'ACEPTADO', NULL, NULL, '', NULL, 'Activado', '2026-07-16 14:15:31', 'BCP', 0.00, 'TERMINADO', 1, NULL, NULL, NULL, NULL, '2026-07-16 09:15:31', '2026-07-16 09:15:31', NULL),
(57, 1, 1, 1, 1, 0, 'Nota de Venta', 'P001', '0000007', '2026-07-16 22:18:01', 0.00, 6000.00, 'No', 0, 0, 0, 'Efectivo', '', '2026-07-16 22:18:01', 0, 6000, 0, NULL, 0, NULL, '', 'ACEPTADO', NULL, NULL, '', NULL, 'Activado', '2026-07-17 03:18:01', 'BCP', 0.00, 'TERMINADO', 1, NULL, NULL, NULL, NULL, '2026-07-16 22:18:01', '2026-07-16 22:18:01', NULL),
(58, 1, 1, 1, 1, 0, 'Boleta', 'B001', '0000002', '2026-07-16 22:24:49', 915.25, 6000.00, 'No', 0, 0, 0, 'Efectivo', '', '2026-07-16 22:24:49', 0, 6000, 0, NULL, 0, NULL, '', '', NULL, NULL, '', NULL, 'Por Enviar', '2026-07-17 03:24:49', 'BCP', 0.00, 'TERMINADO', 1, NULL, NULL, NULL, NULL, '2026-07-16 22:24:49', '2026-07-16 22:24:49', NULL),
(59, 1, 1, 1, 1, 0, 'Nota de Venta', 'P001', '0000008', '2026-07-17 19:01:27', 0.00, 4000.00, 'No', 0, 0, 0, 'Efectivo', '', '2026-07-17 19:01:27', 0, 4000, 0, NULL, 0, NULL, '', 'ACEPTADO', NULL, NULL, '', NULL, 'Activado', '2026-07-18 00:01:27', 'BCP', 0.00, 'TERMINADO', 1, NULL, NULL, NULL, NULL, '2026-07-17 19:01:27', '2026-07-17 19:01:27', NULL),
(60, 1, 1, 1, 1, 0, 'Boleta', 'B001', '0000009', '2026-07-17 19:04:45', 0.00, 9000.00, 'No', 0, 0, 0, 'Efectivo', '', '2026-07-17 19:04:45', 0, 9000, 0, NULL, 0, NULL, '', 'ACEPTADO', '20152458654-03-B001-0000009', 1, '', NULL, 'Aceptado', '2026-07-18 00:04:45', 'BCP', 0.00, 'TERMINADO', 1, NULL, NULL, NULL, NULL, '2026-07-17 19:04:45', '2026-07-17 19:04:45', NULL),
(62, 1, 1, 19, 1, 0, 'Boleta', 'B001', '0000009', '2026-07-27 17:42:54', 1067.80, 7000.00, 'Si', 0, 3, 24, 'Efectivo', '', '2026-07-27 17:42:54', 0, 0, 0, NULL, 0, NULL, '', '', NULL, NULL, '', NULL, 'Por Enviar', '2026-07-27 22:42:54', 'BCP', 0.00, 'TERMINADO', 1, NULL, NULL, NULL, NULL, '2026-07-27 17:42:54', '2026-07-27 17:42:54', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_pago`
--

CREATE TABLE `venta_pago` (
  `idventapago` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `nroOperacion` varchar(50) DEFAULT NULL,
  `fechaDeposito` date DEFAULT NULL,
  `banco` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `venta_pago`
--

INSERT INTO `venta_pago` (`idventapago`, `idventa`, `metodo_pago`, `monto`, `nroOperacion`, `fechaDeposito`, `banco`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Efectivo', 500.00, NULL, NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(2, 2, 'Efectivo', 7000.00, NULL, NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(11, 28, 'Efectivo', 9000.00, NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(12, 29, 'Efectivo', 7000.00, NULL, NULL, NULL, '2026-07-16 08:09:31', '2026-07-16 08:09:31', NULL),
(32, 49, 'Efectivo', 7000.00, NULL, NULL, NULL, '2026-07-16 09:01:35', '2026-07-16 09:01:35', NULL),
(36, 53, 'Efectivo', 11000.00, NULL, NULL, NULL, '2026-07-16 09:15:31', '2026-07-16 09:15:31', NULL),
(40, 57, 'Efectivo', 6000.00, NULL, NULL, NULL, '2026-07-16 22:18:01', '2026-07-16 22:18:01', NULL),
(41, 58, 'Efectivo', 6000.00, NULL, NULL, NULL, '2026-07-16 22:24:49', '2026-07-16 22:24:49', NULL),
(42, 59, 'Efectivo', 4000.00, NULL, NULL, NULL, '2026-07-17 19:01:27', '2026-07-17 19:01:27', NULL),
(43, 60, 'Efectivo', 9000.00, NULL, NULL, NULL, '2026-07-17 19:04:45', '2026-07-17 19:04:45', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `verificaciones_domiciliarias`
--

CREATE TABLE `verificaciones_domiciliarias` (
  `idverificacion` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `direccion_registrada` text DEFAULT NULL,
  `resultado_verificacion` enum('CONFORME','NO_CONFORME','NO_UBICADO','PENDIENTE') NOT NULL,
  `comentarios` text DEFAULT NULL,
  `fecha_verificacion` datetime DEFAULT current_timestamp(),
  `idusuario` int(11) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `verificaciones_domiciliarias`
--

INSERT INTO `verificaciones_domiciliarias` (`idverificacion`, `idsolicitud`, `idcliente`, `direccion_registrada`, `resultado_verificacion`, `comentarios`, `fecha_verificacion`, `idusuario`, `estado`) VALUES
(1, 2, 3, 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', 'CONFORME', '', '2026-07-01 23:26:55', 1, 1),
(2, 3, 3, 'Ir. Lorenzo Morales c1, Tarapoto 22202, Peru', 'CONFORME', '', '2026-07-08 21:43:29', 1, 1),
(3, 4, 15, 'FMX2+MX8, Tarapoto 22202, Peru', 'CONFORME', 'rthrhrth', '2026-07-15 10:04:47', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_paso`
--

CREATE TABLE `workflow_paso` (
  `idpaso` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden_paso` int(11) NOT NULL,
  `obligatorio` tinyint(4) DEFAULT 1,
  `activo` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_spanish_ci;

--
-- Volcado de datos para la tabla `workflow_paso`
--

INSERT INTO `workflow_paso` (`idpaso`, `nombre`, `descripcion`, `orden_paso`, `obligatorio`, `activo`) VALUES
(1, 'Evaluación Inicial', NULL, 1, 1, 1),
(2, 'Validación Documentaria', NULL, 2, 1, 1),
(3, 'Verificación Domiciliaria', NULL, 3, 1, 1),
(4, 'Comité de Crédito', NULL, 4, 1, 1),
(5, 'Aprobación Final', NULL, 5, 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accion_permiso`
--
ALTER TABLE `accion_permiso`
  ADD PRIMARY KEY (`idaccion_permiso`),
  ADD KEY `idsubpermiso` (`idsubpermiso`);

--
-- Indices de la tabla `ajuste_inventario`
--
ALTER TABLE `ajuste_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `sucursal_id` (`sucursal_id`),
  ADD KEY `inventario_id` (`inventario_id`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`idasistencia`),
  ADD KEY `idpersonal` (`idpersonal`);

--
-- Indices de la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`idcaja`),
  ADD KEY `idsucursal` (`idsucursal`);

--
-- Indices de la tabla `caja_apertura`
--
ALTER TABLE `caja_apertura`
  ADD PRIMARY KEY (`aperturacajaid`),
  ADD KEY `caja_id` (`idcaja`),
  ADD KEY `user_id` (`idusuario`),
  ADD KEY `idsucursal` (`idsucursal`),
  ADD KEY `idusuario_cierre` (`idusuario_cierre`);

--
-- Indices de la tabla `catalogo_imagen`
--
ALTER TABLE `catalogo_imagen`
  ADD PRIMARY KEY (`idcatalogo_imagen`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`idcategoria`),
  ADD UNIQUE KEY `nombre_UNIQUE` (`nombre`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`idcompra`),
  ADD KEY `fk_ingreso_persona_idx` (`idproveedor`),
  ADD KEY `fk_compra_personal1_idx` (`idpersonal`);

--
-- Indices de la tabla `compra_pago`
--
ALTER TABLE `compra_pago`
  ADD PRIMARY KEY (`idpago`),
  ADD KEY `idcompra` (`idcompra`);

--
-- Indices de la tabla `compromiso_pago`
--
ALTER TABLE `compromiso_pago`
  ADD PRIMARY KEY (`idcompromiso_pago`),
  ADD KEY `iscpc` (`idcpc`),
  ADD KEY `id_usuario` (`idusuario`);

--
-- Indices de la tabla `comp_pago`
--
ALTER TABLE `comp_pago`
  ADD PRIMARY KEY (`id_comp_pago`);

--
-- Indices de la tabla `concepto_ajuste`
--
ALTER TABLE `concepto_ajuste`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `concepto_movimiento`
--
ALTER TABLE `concepto_movimiento`
  ADD PRIMARY KEY (`idconcepto_movimiento`);

--
-- Indices de la tabla `condicionventa`
--
ALTER TABLE `condicionventa`
  ADD PRIMARY KEY (`idcondicionventa`);

--
-- Indices de la tabla `cotizacion`
--
ALTER TABLE `cotizacion`
  ADD PRIMARY KEY (`idcotizacion`),
  ADD KEY `idcliente` (`idcliente`),
  ADD KEY `idPersonal` (`idPersonal`),
  ADD KEY `idsucursal` (`idsucursal`);

--
-- Indices de la tabla `cuentas_por_cobrar`
--
ALTER TABLE `cuentas_por_cobrar`
  ADD PRIMARY KEY (`idcpc`),
  ADD KEY `idrefinanciamiento_origen` (`idrefinanciamiento_origen`),
  ADD KEY `idventa` (`idventa`),
  ADD KEY `idrefinanciamiento` (`idrefinanciamiento`);

--
-- Indices de la tabla `cuentas_por_pagar`
--
ALTER TABLE `cuentas_por_pagar`
  ADD PRIMARY KEY (`idcpp`),
  ADD KEY `idcompra` (`idcompra`);

--
-- Indices de la tabla `datos_negocio`
--
ALTER TABLE `datos_negocio`
  ADD PRIMARY KEY (`id_negocio`);

--
-- Indices de la tabla `detalle_ajuste_inventario`
--
ALTER TABLE `detalle_ajuste_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ajuste_inventario_id` (`ajuste_inventario_id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `concepto_ajuste_id` (`concepto_ajuste_id`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`iddetalle_compra`),
  ADD KEY `fk_detalle_ingreso_ingreso_idx` (`idcompra`),
  ADD KEY `fk_detalle_compra_producto1_idx` (`idproducto`),
  ADD KEY `fk_compra_sucursal` (`idsucursal`);

--
-- Indices de la tabla `detalle_cotizacion`
--
ALTER TABLE `detalle_cotizacion`
  ADD PRIMARY KEY (`iddetalle_cotizacion`),
  ADD KEY `idcotizacion` (`idcotizacion`),
  ADD KEY `idproducto` (`idproducto`);

--
-- Indices de la tabla `detalle_cuentas_por_cobrar`
--
ALTER TABLE `detalle_cuentas_por_cobrar`
  ADD PRIMARY KEY (`iddcpc`),
  ADD KEY `idcpc` (`idcpc`),
  ADD KEY `idcaja` (`idcaja`),
  ADD KEY `idpersonal` (`idpersonal`);

--
-- Indices de la tabla `detalle_cuentas_por_pagar`
--
ALTER TABLE `detalle_cuentas_por_pagar`
  ADD PRIMARY KEY (`iddcpp`),
  ADD KEY `idcaja` (`idcaja`),
  ADD KEY `idpersonal` (`idpersonal`);

--
-- Indices de la tabla `detalle_guia`
--
ALTER TABLE `detalle_guia`
  ADD PRIMARY KEY (`iddetalle`),
  ADD KEY `idguia` (`idguia`);

--
-- Indices de la tabla `detalle_servicio`
--
ALTER TABLE `detalle_servicio`
  ADD PRIMARY KEY (`iddetalle_servicio`),
  ADD KEY `idservicio` (`idservicio`),
  ADD KEY `idproducto` (`idproducto`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`iddetalle_venta`),
  ADD KEY `fk_venta_detalle` (`idventa`),
  ADD KEY `fk_sucursal_detalle` (`idsucursal`),
  ADD KEY `idproducto` (`idproducto`),
  ADD KEY `fk_detalle_serie` (`idserie`);

--
-- Indices de la tabla `documentacion`
--
ALTER TABLE `documentacion`
  ADD PRIMARY KEY (`iddocumento`),
  ADD KEY `idventa` (`idventa`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`idempresa`),
  ADD UNIQUE KEY `ruc` (`ruc`);

--
-- Indices de la tabla `guia_remision`
--
ALTER TABLE `guia_remision`
  ADD PRIMARY KEY (`idguia`);

--
-- Indices de la tabla `inventarios`
--
ALTER TABLE `inventarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sucursal_id` (`sucursal_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `inventario_detalles`
--
ALTER TABLE `inventario_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `inventario_id` (`inventario_id`);

--
-- Indices de la tabla `inventario_producto`
--
ALTER TABLE `inventario_producto`
  ADD PRIMARY KEY (`idinventario`),
  ADD UNIQUE KEY `uk_producto_sucursal` (`idproducto`,`idsucursal`),
  ADD KEY `fk_inv_sucursal` (`idsucursal`);

--
-- Indices de la tabla `inventario_seleccionados`
--
ALTER TABLE `inventario_seleccionados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idinventario` (`idinventario`,`idproducto`);

--
-- Indices de la tabla `kardex`
--
ALTER TABLE `kardex`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_producto` (`idproducto`),
  ADD KEY `fk_sucursal` (`idsucursal`),
  ADD KEY `idproducto_configuracion` (`idproducto_configuracion`);

--
-- Indices de la tabla `login_historial`
--
ALTER TABLE `login_historial`
  ADD PRIMARY KEY (`idhistorial`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`idmarca`);

--
-- Indices de la tabla `modelo`
--
ALTER TABLE `modelo`
  ADD KEY `idmodelo` (`idmodelo`);

--
-- Indices de la tabla `motivos_nota`
--
ALTER TABLE `motivos_nota`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD PRIMARY KEY (`idmovimiento`),
  ADD KEY `idmotivo_movimiento` (`idconcepto_movimiento`),
  ADD KEY `idx_idpersonal` (`idpersonal`),
  ADD KEY `idx_idsucursal` (`idsucursal`),
  ADD KEY `idx_idcaja` (`idcaja`);

--
-- Indices de la tabla `nombre_precios`
--
ALTER TABLE `nombre_precios`
  ADD PRIMARY KEY (`idnombre_p`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`idnotificacion`);

--
-- Indices de la tabla `orden_trabajo`
--
ALTER TABLE `orden_trabajo`
  ADD PRIMARY KEY (`idorden`),
  ADD KEY `fk_ot_producto` (`idproducto`),
  ADD KEY `fk_ot_usuario` (`idusuario`);

--
-- Indices de la tabla `orden_trabajo_detalle`
--
ALTER TABLE `orden_trabajo_detalle`
  ADD PRIMARY KEY (`iddetalle`),
  ADD KEY `fk_otd_orden` (`idorden`),
  ADD KEY `fk_otd_producto` (`idproducto`);

--
-- Indices de la tabla `pagos_asistencia`
--
ALTER TABLE `pagos_asistencia`
  ADD PRIMARY KEY (`idpago`),
  ADD KEY `idasistencia` (`idasistencia`),
  ADD KEY `idpersonal` (`idpersonal`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`idpermiso`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`idpersona`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`idpersonal`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idproducto`),
  ADD KEY `fk_producto_categoria1_idx` (`idcategoria`),
  ADD KEY `idunidad_medida` (`idunidad_medida`),
  ADD KEY `idrubro` (`idrubro`),
  ADD KEY `idcondicionventa` (`idcondicionventa`),
  ADD KEY `fk_sucursal_producto` (`idsucursal`),
  ADD KEY `idx_producto_nombre` (`nombre`,`condicion`,`idsucursal`),
  ADD KEY `idx_producto_codigo` (`codigo`),
  ADD KEY `idx_producto_listado` (`idsucursal`,`idcategoria`,`fechac`),
  ADD KEY `idmarca` (`idmarca`),
  ADD KEY `idmodelo` (`idmodelo`);

--
-- Indices de la tabla `producto_configuracion`
--
ALTER TABLE `producto_configuracion`
  ADD PRIMARY KEY (`idproducto_configuracion`),
  ADD KEY `producto_id` (`idproducto`);

--
-- Indices de la tabla `producto_configuracion_precios`
--
ALTER TABLE `producto_configuracion_precios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_configuracion_id` (`producto_configuracion_id`),
  ADD KEY `idnombre_p` (`idnombre_p`);

--
-- Indices de la tabla `producto_serie`
--
ALTER TABLE `producto_serie`
  ADD PRIMARY KEY (`idserie`),
  ADD KEY `fk_serie_producto` (`idproducto`),
  ADD KEY `fk_serie_sucursal` (`idsucursal`);

--
-- Indices de la tabla `recordatorio_envios`
--
ALTER TABLE `recordatorio_envios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `recuperacion_documento`
--
ALTER TABLE `recuperacion_documento`
  ADD PRIMARY KEY (`iddocumento`),
  ADD KEY `idrecuperacion` (`idrecuperacion`);

--
-- Indices de la tabla `recuperacion_vehiculo`
--
ALTER TABLE `recuperacion_vehiculo`
  ADD PRIMARY KEY (`idrecuperacion`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idventa` (`idventa`),
  ADD KEY `idpersona` (`idpersona`),
  ADD KEY `idserie` (`idserie`);

--
-- Indices de la tabla `refinanciamientos`
--
ALTER TABLE `refinanciamientos`
  ADD PRIMARY KEY (`idrefinanciamiento`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idventa` (`idventa`);

--
-- Indices de la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD PRIMARY KEY (`id_resena`),
  ADD KEY `fk_resenas_producto` (`idproducto`);

--
-- Indices de la tabla `resumen_diario`
--
ALTER TABLE `resumen_diario`
  ADD PRIMARY KEY (`idresumen`),
  ADD KEY `fk_sucursal_resumen` (`idsucursal`);

--
-- Indices de la tabla `resumen_diario_detalle`
--
ALTER TABLE `resumen_diario_detalle`
  ADD PRIMARY KEY (`iddetalle`),
  ADD KEY `fk_resumen` (`idresumen`),
  ADD KEY `fk_venta_resumen` (`idventa`);

--
-- Indices de la tabla `retenciones`
--
ALTER TABLE `retenciones`
  ADD PRIMARY KEY (`idretencion`),
  ADD KEY `idventa` (`idventa`);

--
-- Indices de la tabla `rubro`
--
ALTER TABLE `rubro`
  ADD PRIMARY KEY (`idrubro`) USING BTREE;

--
-- Indices de la tabla `seguimiento_adjuntos`
--
ALTER TABLE `seguimiento_adjuntos`
  ADD PRIMARY KEY (`idadjunto`),
  ADD KEY `idseguimiento` (`idseguimiento`);

--
-- Indices de la tabla `seguimiento_clientes`
--
ALTER TABLE `seguimiento_clientes`
  ADD PRIMARY KEY (`idseguimiento`),
  ADD KEY `iddocumento` (`iddocumento`),
  ADD KEY `idcpc` (`idcpc`),
  ADD KEY `idventa` (`idventa`),
  ADD KEY `idcliente` (`idcliente`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idpersonal` (`idpersonal`),
  ADD KEY `idx_idrecuperacion` (`idrecuperacion`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`idservicio`),
  ADD UNIQUE KEY `unique_comprobante` (`serie_comprobante`,`num_comprobante`,`idsucursal`);

--
-- Indices de la tabla `solicitud_credito`
--
ALTER TABLE `solicitud_credito`
  ADD PRIMARY KEY (`idsolicitud`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idcliente` (`idcliente`),
  ADD KEY `idcotizacion` (`idcotizacion`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idsucursal` (`idsucursal`);

--
-- Indices de la tabla `solicitud_documento`
--
ALTER TABLE `solicitud_documento`
  ADD PRIMARY KEY (`iddocumento`),
  ADD KEY `idsolicitud` (`idsolicitud`);

--
-- Indices de la tabla `solicitud_evaluacion`
--
ALTER TABLE `solicitud_evaluacion`
  ADD PRIMARY KEY (`idevaluacion`),
  ADD KEY `idsolicitud` (`idsolicitud`);

--
-- Indices de la tabla `solicitud_workflow`
--
ALTER TABLE `solicitud_workflow`
  ADD PRIMARY KEY (`idworkflow`),
  ADD KEY `idsolicitud` (`idsolicitud`),
  ADD KEY `idpaso` (`idpaso`),
  ADD KEY `idusuario` (`idusuario`);

--
-- Indices de la tabla `stock_fifo`
--
ALTER TABLE `stock_fifo`
  ADD PRIMARY KEY (`idfifo`),
  ADD KEY `idx_producto_config` (`producto_configuracion_id`),
  ADD KEY `idx_fifo_busqueda` (`idproducto`,`idsucursal`,`cantidad_restante`,`estado`,`fecha_ingreso`),
  ADD KEY `idx_calculo_fifo` (`idproducto`,`estado`,`cantidad_restante`,`idfifo`);

--
-- Indices de la tabla `subpermiso`
--
ALTER TABLE `subpermiso`
  ADD PRIMARY KEY (`idsubpermiso`),
  ADD KEY `idpermiso` (`idpermiso`);

--
-- Indices de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`idsucursal`),
  ADD KEY `idempresa` (`idempresa`);

--
-- Indices de la tabla `sucursal_configuracion`
--
ALTER TABLE `sucursal_configuracion`
  ADD PRIMARY KEY (`idsucursal_configuracion`),
  ADD KEY `idsucursal` (`idsucursal`);

--
-- Indices de la tabla `tipoacompanante`
--
ALTER TABLE `tipoacompanante`
  ADD PRIMARY KEY (`idtipoacompanante`);

--
-- Indices de la tabla `traslado`
--
ALTER TABLE `traslado`
  ADD PRIMARY KEY (`idtraslado`),
  ADD KEY `idorigen` (`idorigen`),
  ADD KEY `iddestino` (`iddestino`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idusuario_acepta` (`idusuario_acepta`),
  ADD KEY `id_solicitud_origen` (`idsolicitud_origen`);

--
-- Indices de la tabla `traslado_detalle`
--
ALTER TABLE `traslado_detalle`
  ADD PRIMARY KEY (`iddetalle`),
  ADD KEY `idserie` (`idserie`),
  ADD KEY `idtraslado` (`idtraslado`),
  ADD KEY `idproducto` (`idproducto`);

--
-- Indices de la tabla `ubigeo_peru_departments`
--
ALTER TABLE `ubigeo_peru_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ubigeo_peru_districts`
--
ALTER TABLE `ubigeo_peru_districts`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ubigeo_peru_provinces`
--
ALTER TABLE `ubigeo_peru_provinces`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD PRIMARY KEY (`idunidad_medida`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`),
  ADD UNIQUE KEY `login_UNIQUE` (`login`),
  ADD KEY `fk_usuario_personal1_idx` (`idpersonal`),
  ADD KEY `idx_usuario_personal` (`idpersonal`,`idusuario`),
  ADD KEY `idsucursal` (`idsucursal`);

--
-- Indices de la tabla `usuario_accion`
--
ALTER TABLE `usuario_accion`
  ADD PRIMARY KEY (`idusuario`,`idaccion_permiso`),
  ADD KEY `idaccion_permiso` (`idaccion_permiso`),
  ADD KEY `idx_usuario_accion_sucursal` (`idusuario`);

--
-- Indices de la tabla `usuario_permiso`
--
ALTER TABLE `usuario_permiso`
  ADD PRIMARY KEY (`idusuario_permiso`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idpermiso` (`idpermiso`),
  ADD KEY `idsubpermiso` (`idsubpermiso`),
  ADD KEY `idx_usuario_permiso_sucursal` (`idusuario`);

--
-- Indices de la tabla `usuario_sucursal`
--
ALTER TABLE `usuario_sucursal`
  ADD PRIMARY KEY (`idusuario_sucursal`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idsucursal` (`idsucursal`),
  ADD KEY `idx_usuario_sucursal_per` (`idsucursal`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`idventa`),
  ADD KEY `fk_venta_persona_idx` (`idcliente`),
  ADD KEY `fk_venta_Personal1_idx` (`idpersonal`),
  ADD KEY `idmotivo_nota` (`idmotivo_nota`),
  ADD KEY `idcaja` (`idcaja`),
  ADD KEY `fk_sucursal_venta` (`idsucursal`),
  ADD KEY `idgarante` (`idgarante`),
  ADD KEY `idtipoacompanante` (`idtipoacompanante`),
  ADD KEY `idacompanante` (`idacompanante`);

--
-- Indices de la tabla `venta_pago`
--
ALTER TABLE `venta_pago`
  ADD PRIMARY KEY (`idventapago`),
  ADD KEY `idventa` (`idventa`);

--
-- Indices de la tabla `verificaciones_domiciliarias`
--
ALTER TABLE `verificaciones_domiciliarias`
  ADD PRIMARY KEY (`idverificacion`),
  ADD KEY `idcliente` (`idcliente`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idsolicitud` (`idsolicitud`);

--
-- Indices de la tabla `workflow_paso`
--
ALTER TABLE `workflow_paso`
  ADD PRIMARY KEY (`idpaso`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accion_permiso`
--
ALTER TABLE `accion_permiso`
  MODIFY `idaccion_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `ajuste_inventario`
--
ALTER TABLE `ajuste_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `idasistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cajas`
--
ALTER TABLE `cajas`
  MODIFY `idcaja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `caja_apertura`
--
ALTER TABLE `caja_apertura`
  MODIFY `aperturacajaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `catalogo_imagen`
--
ALTER TABLE `catalogo_imagen`
  MODIFY `idcatalogo_imagen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `idcategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `idcompra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `compra_pago`
--
ALTER TABLE `compra_pago`
  MODIFY `idpago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `compromiso_pago`
--
ALTER TABLE `compromiso_pago`
  MODIFY `idcompromiso_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `comp_pago`
--
ALTER TABLE `comp_pago`
  MODIFY `id_comp_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `concepto_ajuste`
--
ALTER TABLE `concepto_ajuste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `concepto_movimiento`
--
ALTER TABLE `concepto_movimiento`
  MODIFY `idconcepto_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `condicionventa`
--
ALTER TABLE `condicionventa`
  MODIFY `idcondicionventa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cotizacion`
--
ALTER TABLE `cotizacion`
  MODIFY `idcotizacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `cuentas_por_cobrar`
--
ALTER TABLE `cuentas_por_cobrar`
  MODIFY `idcpc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT de la tabla `cuentas_por_pagar`
--
ALTER TABLE `cuentas_por_pagar`
  MODIFY `idcpp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `datos_negocio`
--
ALTER TABLE `datos_negocio`
  MODIFY `id_negocio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_ajuste_inventario`
--
ALTER TABLE `detalle_ajuste_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `iddetalle_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `detalle_cotizacion`
--
ALTER TABLE `detalle_cotizacion`
  MODIFY `iddetalle_cotizacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `detalle_cuentas_por_cobrar`
--
ALTER TABLE `detalle_cuentas_por_cobrar`
  MODIFY `iddcpc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT de la tabla `detalle_cuentas_por_pagar`
--
ALTER TABLE `detalle_cuentas_por_pagar`
  MODIFY `iddcpp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_guia`
--
ALTER TABLE `detalle_guia`
  MODIFY `iddetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_servicio`
--
ALTER TABLE `detalle_servicio`
  MODIFY `iddetalle_servicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `iddetalle_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `documentacion`
--
ALTER TABLE `documentacion`
  MODIFY `iddocumento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `idempresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `guia_remision`
--
ALTER TABLE `guia_remision`
  MODIFY `idguia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventarios`
--
ALTER TABLE `inventarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario_detalles`
--
ALTER TABLE `inventario_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario_producto`
--
ALTER TABLE `inventario_producto`
  MODIFY `idinventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `inventario_seleccionados`
--
ALTER TABLE `inventario_seleccionados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `kardex`
--
ALTER TABLE `kardex`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `login_historial`
--
ALTER TABLE `login_historial`
  MODIFY `idhistorial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `idmarca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `modelo`
--
ALTER TABLE `modelo`
  MODIFY `idmodelo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `motivos_nota`
--
ALTER TABLE `motivos_nota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  MODIFY `idmovimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `nombre_precios`
--
ALTER TABLE `nombre_precios`
  MODIFY `idnombre_p` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `idnotificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `orden_trabajo`
--
ALTER TABLE `orden_trabajo`
  MODIFY `idorden` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `orden_trabajo_detalle`
--
ALTER TABLE `orden_trabajo_detalle`
  MODIFY `iddetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos_asistencia`
--
ALTER TABLE `pagos_asistencia`
  MODIFY `idpago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `idpermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `idpersona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `idpersonal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idproducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `producto_configuracion`
--
ALTER TABLE `producto_configuracion`
  MODIFY `idproducto_configuracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `producto_configuracion_precios`
--
ALTER TABLE `producto_configuracion_precios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto_serie`
--
ALTER TABLE `producto_serie`
  MODIFY `idserie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `recordatorio_envios`
--
ALTER TABLE `recordatorio_envios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `recuperacion_documento`
--
ALTER TABLE `recuperacion_documento`
  MODIFY `iddocumento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `recuperacion_vehiculo`
--
ALTER TABLE `recuperacion_vehiculo`
  MODIFY `idrecuperacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `refinanciamientos`
--
ALTER TABLE `refinanciamientos`
  MODIFY `idrefinanciamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resenas`
--
ALTER TABLE `resenas`
  MODIFY `id_resena` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resumen_diario`
--
ALTER TABLE `resumen_diario`
  MODIFY `idresumen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resumen_diario_detalle`
--
ALTER TABLE `resumen_diario_detalle`
  MODIFY `iddetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `retenciones`
--
ALTER TABLE `retenciones`
  MODIFY `idretencion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `rubro`
--
ALTER TABLE `rubro`
  MODIFY `idrubro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `seguimiento_adjuntos`
--
ALTER TABLE `seguimiento_adjuntos`
  MODIFY `idadjunto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `seguimiento_clientes`
--
ALTER TABLE `seguimiento_clientes`
  MODIFY `idseguimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `idservicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitud_credito`
--
ALTER TABLE `solicitud_credito`
  MODIFY `idsolicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitud_documento`
--
ALTER TABLE `solicitud_documento`
  MODIFY `iddocumento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `solicitud_evaluacion`
--
ALTER TABLE `solicitud_evaluacion`
  MODIFY `idevaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `solicitud_workflow`
--
ALTER TABLE `solicitud_workflow`
  MODIFY `idworkflow` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `stock_fifo`
--
ALTER TABLE `stock_fifo`
  MODIFY `idfifo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `subpermiso`
--
ALTER TABLE `subpermiso`
  MODIFY `idsubpermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `idsucursal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sucursal_configuracion`
--
ALTER TABLE `sucursal_configuracion`
  MODIFY `idsucursal_configuracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipoacompanante`
--
ALTER TABLE `tipoacompanante`
  MODIFY `idtipoacompanante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `traslado`
--
ALTER TABLE `traslado`
  MODIFY `idtraslado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `traslado_detalle`
--
ALTER TABLE `traslado_detalle`
  MODIFY `iddetalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `idunidad_medida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario_permiso`
--
ALTER TABLE `usuario_permiso`
  MODIFY `idusuario_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT de la tabla `usuario_sucursal`
--
ALTER TABLE `usuario_sucursal`
  MODIFY `idusuario_sucursal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `idventa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT de la tabla `venta_pago`
--
ALTER TABLE `venta_pago`
  MODIFY `idventapago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `verificaciones_domiciliarias`
--
ALTER TABLE `verificaciones_domiciliarias`
  MODIFY `idverificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `workflow_paso`
--
ALTER TABLE `workflow_paso`
  MODIFY `idpaso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accion_permiso`
--
ALTER TABLE `accion_permiso`
  ADD CONSTRAINT `accion_permiso_ibfk_1` FOREIGN KEY (`idsubpermiso`) REFERENCES `subpermiso` (`idsubpermiso`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ajuste_inventario`
--
ALTER TABLE `ajuste_inventario`
  ADD CONSTRAINT `ajuste_inventario_ibfk_1` FOREIGN KEY (`inventario_id`) REFERENCES `inventarios` (`id`),
  ADD CONSTRAINT `ajuste_inventario_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`),
  ADD CONSTRAINT `ajuste_inventario_ibfk_4` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`idsucursal`);

--
-- Filtros para la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD CONSTRAINT `cajas_ibfk_1` FOREIGN KEY (`idsucursal`) REFERENCES `sucursal` (`idsucursal`);

--
-- Filtros para la tabla `caja_apertura`
--
ALTER TABLE `caja_apertura`
  ADD CONSTRAINT `caja_apertura_ibfk_1` FOREIGN KEY (`idsucursal`) REFERENCES `sucursal` (`idsucursal`),
  ADD CONSTRAINT `caja_apertura_ibfk_2` FOREIGN KEY (`idcaja`) REFERENCES `cajas` (`idcaja`),
  ADD CONSTRAINT `caja_apertura_ibfk_3` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `compromiso_pago`
--
ALTER TABLE `compromiso_pago`
  ADD CONSTRAINT `compromiso_pago_ibfk_1` FOREIGN KEY (`idcpc`) REFERENCES `cuentas_por_cobrar` (`idcpc`),
  ADD CONSTRAINT `compromiso_pago_ibfk_2` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `cotizacion`
--
ALTER TABLE `cotizacion`
  ADD CONSTRAINT `cotizacion_ibfk_1` FOREIGN KEY (`idsucursal`) REFERENCES `sucursal` (`idsucursal`),
  ADD CONSTRAINT `cotizacion_ibfk_2` FOREIGN KEY (`idcliente`) REFERENCES `persona` (`idpersona`),
  ADD CONSTRAINT `cotizacion_ibfk_3` FOREIGN KEY (`idPersonal`) REFERENCES `personal` (`idpersonal`);

--
-- Filtros para la tabla `cuentas_por_cobrar`
--
ALTER TABLE `cuentas_por_cobrar`
  ADD CONSTRAINT `cuentas_por_cobrar_ibfk_1` FOREIGN KEY (`idventa`) REFERENCES `venta` (`idventa`),
  ADD CONSTRAINT `cuentas_por_cobrar_ibfk_2` FOREIGN KEY (`idrefinanciamiento_origen`) REFERENCES `refinanciamientos` (`idrefinanciamiento`),
  ADD CONSTRAINT `cuentas_por_cobrar_ibfk_3` FOREIGN KEY (`idrefinanciamiento`) REFERENCES `refinanciamientos` (`idrefinanciamiento`);

--
-- Filtros para la tabla `detalle_cotizacion`
--
ALTER TABLE `detalle_cotizacion`
  ADD CONSTRAINT `detalle_cotizacion_ibfk_1` FOREIGN KEY (`idcotizacion`) REFERENCES `cotizacion` (`idcotizacion`),
  ADD CONSTRAINT `detalle_cotizacion_ibfk_2` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`idproducto`);

--
-- Filtros para la tabla `detalle_cuentas_por_cobrar`
--
ALTER TABLE `detalle_cuentas_por_cobrar`
  ADD CONSTRAINT `detalle_cuentas_por_cobrar_ibfk_1` FOREIGN KEY (`idcpc`) REFERENCES `cuentas_por_cobrar` (`idcpc`),
  ADD CONSTRAINT `detalle_cuentas_por_cobrar_ibfk_2` FOREIGN KEY (`idpersonal`) REFERENCES `personal` (`idpersonal`),
  ADD CONSTRAINT `detalle_cuentas_por_cobrar_ibfk_3` FOREIGN KEY (`idcaja`) REFERENCES `cajas` (`idcaja`);

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`idsucursal`) REFERENCES `sucursal` (`idsucursal`),
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`idventa`) REFERENCES `venta` (`idventa`),
  ADD CONSTRAINT `detalle_venta_ibfk_3` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`idproducto`),
  ADD CONSTRAINT `fk_detalle_serie` FOREIGN KEY (`idserie`) REFERENCES `producto_serie` (`idserie`);

--
-- Filtros para la tabla `documentacion`
--
ALTER TABLE `documentacion`
  ADD CONSTRAINT `documentacion_ibfk_1` FOREIGN KEY (`idventa`) REFERENCES `venta` (`idventa`);

--
-- Filtros para la tabla `inventario_producto`
--
ALTER TABLE `inventario_producto`
  ADD CONSTRAINT `fk_inv_producto` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`idproducto`),
  ADD CONSTRAINT `fk_inv_sucursal` FOREIGN KEY (`idsucursal`) REFERENCES `sucursal` (`idsucursal`);

--
-- Filtros para la tabla `orden_trabajo`
--
ALTER TABLE `orden_trabajo`
  ADD CONSTRAINT `fk_ot_producto` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`idproducto`),
  ADD CONSTRAINT `fk_ot_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `orden_trabajo_detalle`
--
ALTER TABLE `orden_trabajo_detalle`
  ADD CONSTRAINT `fk_otd_orden` FOREIGN KEY (`idorden`) REFERENCES `orden_trabajo` (`idorden`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_otd_producto` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`idproducto`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`idsucursal`) REFERENCES `sucursal` (`idsucursal`);

--
-- Filtros para la tabla `producto_serie`
--
ALTER TABLE `producto_serie`
  ADD CONSTRAINT `fk_serie_producto` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`idproducto`),
  ADD CONSTRAINT `fk_serie_sucursal` FOREIGN KEY (`idsucursal`) REFERENCES `sucursal` (`idsucursal`);

--
-- Filtros para la tabla `recuperacion_documento`
--
ALTER TABLE `recuperacion_documento`
  ADD CONSTRAINT `recuperacion_documento_ibfk_1` FOREIGN KEY (`idrecuperacion`) REFERENCES `recuperacion_vehiculo` (`idrecuperacion`);

--
-- Filtros para la tabla `recuperacion_vehiculo`
--
ALTER TABLE `recuperacion_vehiculo`
  ADD CONSTRAINT `recuperacion_vehiculo_ibfk_1` FOREIGN KEY (`idventa`) REFERENCES `venta` (`idventa`),
  ADD CONSTRAINT `recuperacion_vehiculo_ibfk_2` FOREIGN KEY (`idpersona`) REFERENCES `persona` (`idpersona`),
  ADD CONSTRAINT `recuperacion_vehiculo_ibfk_3` FOREIGN KEY (`idserie`) REFERENCES `producto_serie` (`idserie`),
  ADD CONSTRAINT `recuperacion_vehiculo_ibfk_4` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `refinanciamientos`
--
ALTER TABLE `refinanciamientos`
  ADD CONSTRAINT `refinanciamientos_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`),
  ADD CONSTRAINT `refinanciamientos_ibfk_2` FOREIGN KEY (`idventa`) REFERENCES `venta` (`idventa`);

--
-- Filtros para la tabla `seguimiento_adjuntos`
--
ALTER TABLE `seguimiento_adjuntos`
  ADD CONSTRAINT `seguimiento_adjuntos_ibfk_1` FOREIGN KEY (`idseguimiento`) REFERENCES `seguimiento_clientes` (`idseguimiento`);

--
-- Filtros para la tabla `seguimiento_clientes`
--
ALTER TABLE `seguimiento_clientes`
  ADD CONSTRAINT `seguimiento_clientes_ibfk_1` FOREIGN KEY (`iddocumento`) REFERENCES `documentacion` (`iddocumento`),
  ADD CONSTRAINT `seguimiento_clientes_ibfk_2` FOREIGN KEY (`idcpc`) REFERENCES `cuentas_por_cobrar` (`idcpc`),
  ADD CONSTRAINT `seguimiento_clientes_ibfk_3` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`),
  ADD CONSTRAINT `seguimiento_clientes_ibfk_4` FOREIGN KEY (`idventa`) REFERENCES `venta` (`idventa`),
  ADD CONSTRAINT `seguimiento_clientes_ibfk_5` FOREIGN KEY (`idcliente`) REFERENCES `persona` (`idpersona`),
  ADD CONSTRAINT `seguimiento_clientes_ibfk_6` FOREIGN KEY (`idpersonal`) REFERENCES `personal` (`idpersonal`),
  ADD CONSTRAINT `seguimiento_clientes_ibfk_7` FOREIGN KEY (`idrecuperacion`) REFERENCES `recuperacion_vehiculo` (`idrecuperacion`);

--
-- Filtros para la tabla `solicitud_credito`
--
ALTER TABLE `solicitud_credito`
  ADD CONSTRAINT `solicitud_credito_ibfk_1` FOREIGN KEY (`idcotizacion`) REFERENCES `cotizacion` (`idcotizacion`),
  ADD CONSTRAINT `solicitud_credito_ibfk_2` FOREIGN KEY (`idsucursal`) REFERENCES `sucursal` (`idsucursal`),
  ADD CONSTRAINT `solicitud_credito_ibfk_3` FOREIGN KEY (`idcliente`) REFERENCES `persona` (`idpersona`);

--
-- Filtros para la tabla `solicitud_documento`
--
ALTER TABLE `solicitud_documento`
  ADD CONSTRAINT `solicitud_documento_ibfk_1` FOREIGN KEY (`idsolicitud`) REFERENCES `solicitud_credito` (`idsolicitud`);

--
-- Filtros para la tabla `solicitud_evaluacion`
--
ALTER TABLE `solicitud_evaluacion`
  ADD CONSTRAINT `solicitud_evaluacion_ibfk_1` FOREIGN KEY (`idsolicitud`) REFERENCES `solicitud_credito` (`idsolicitud`);

--
-- Filtros para la tabla `solicitud_workflow`
--
ALTER TABLE `solicitud_workflow`
  ADD CONSTRAINT `solicitud_workflow_ibfk_1` FOREIGN KEY (`idsolicitud`) REFERENCES `solicitud_credito` (`idsolicitud`),
  ADD CONSTRAINT `solicitud_workflow_ibfk_2` FOREIGN KEY (`idpaso`) REFERENCES `workflow_paso` (`idpaso`),
  ADD CONSTRAINT `solicitud_workflow_ibfk_3` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `subpermiso`
--
ALTER TABLE `subpermiso`
  ADD CONSTRAINT `subpermiso_ibfk_1` FOREIGN KEY (`idpermiso`) REFERENCES `permiso` (`idpermiso`);

--
-- Filtros para la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD CONSTRAINT `sucursal_ibfk_1` FOREIGN KEY (`idempresa`) REFERENCES `empresas` (`idempresa`);

--
-- Filtros para la tabla `sucursal_configuracion`
--
ALTER TABLE `sucursal_configuracion`
  ADD CONSTRAINT `sucursal_configuracion_ibfk_1` FOREIGN KEY (`idsucursal`) REFERENCES `sucursal` (`idsucursal`);

--
-- Filtros para la tabla `traslado`
--
ALTER TABLE `traslado`
  ADD CONSTRAINT `traslado_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`),
  ADD CONSTRAINT `traslado_ibfk_2` FOREIGN KEY (`idorigen`) REFERENCES `sucursal` (`idsucursal`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `traslado_ibfk_3` FOREIGN KEY (`iddestino`) REFERENCES `sucursal` (`idsucursal`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `traslado_ibfk_4` FOREIGN KEY (`idusuario_acepta`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `traslado_detalle`
--
ALTER TABLE `traslado_detalle`
  ADD CONSTRAINT `traslado_detalle_ibfk_1` FOREIGN KEY (`idtraslado`) REFERENCES `traslado` (`idtraslado`),
  ADD CONSTRAINT `traslado_detalle_ibfk_2` FOREIGN KEY (`idserie`) REFERENCES `producto_serie` (`idserie`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `traslado_detalle_ibfk_3` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`idproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`idpersonal`) REFERENCES `personal` (`idpersonal`),
  ADD CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`idsucursal`) REFERENCES `sucursal` (`idsucursal`);

--
-- Filtros para la tabla `usuario_accion`
--
ALTER TABLE `usuario_accion`
  ADD CONSTRAINT `usuario_accion_ibfk_1` FOREIGN KEY (`idaccion_permiso`) REFERENCES `accion_permiso` (`idaccion_permiso`),
  ADD CONSTRAINT `usuario_accion_ibfk_2` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `usuario_permiso`
--
ALTER TABLE `usuario_permiso`
  ADD CONSTRAINT `usuario_permiso_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`),
  ADD CONSTRAINT `usuario_permiso_ibfk_2` FOREIGN KEY (`idsubpermiso`) REFERENCES `subpermiso` (`idsubpermiso`),
  ADD CONSTRAINT `usuario_permiso_ibfk_3` FOREIGN KEY (`idpermiso`) REFERENCES `permiso` (`idpermiso`);

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `venta_ibfk_2` FOREIGN KEY (`idsucursal`) REFERENCES `sucursal` (`idsucursal`),
  ADD CONSTRAINT `venta_ibfk_3` FOREIGN KEY (`idpersonal`) REFERENCES `personal` (`idpersonal`),
  ADD CONSTRAINT `venta_ibfk_4` FOREIGN KEY (`idcliente`) REFERENCES `persona` (`idpersona`),
  ADD CONSTRAINT `venta_ibfk_5` FOREIGN KEY (`idcaja`) REFERENCES `cajas` (`idcaja`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
