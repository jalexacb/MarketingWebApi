-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 03-03-2021 a las 22:47:56
-- Versión del servidor: 5.7.31
-- Versión de PHP: 7.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `marketingweb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campania`
--

DROP TABLE IF EXISTS `campania`;
CREATE TABLE IF NOT EXISTS `campania` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` enum('P','E','A') NOT NULL DEFAULT 'A' COMMENT 'P: pendiente E: ejecutada A: archivada',
  `nombre` varchar(100) NOT NULL,
  `descripcion` int(150) NOT NULL,
  `mensaje` longtext NOT NULL,
  `url` varchar(250) NOT NULL,
  `url_media` varchar(250) NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `campania`
--
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campania_canal`
--

DROP TABLE IF EXISTS `campania_canal`;
CREATE TABLE IF NOT EXISTS `campania_canal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campania_id` int(11) UNSIGNED NOT NULL,
  `canal_id` int(11) UNSIGNED NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `campania_canal`
--



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campania_contacto`
--

DROP TABLE IF EXISTS `campania_contacto`;
CREATE TABLE IF NOT EXISTS `campania_contacto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campania_id` int(11) UNSIGNED NOT NULL,
  `contacto_id` int(11) UNSIGNED NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `campania_contacto`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campania_interes`
--

DROP TABLE IF EXISTS `campania_interes`;
CREATE TABLE IF NOT EXISTS `campania_interes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campania_id` int(11) UNSIGNED NOT NULL,
  `interes_id` int(11) UNSIGNED NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `campania_interes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campania_objetivo`
--

DROP TABLE IF EXISTS `campania_objetivo`;
CREATE TABLE IF NOT EXISTS `campania_objetivo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campania_id` int(11) UNSIGNED NOT NULL,
  `objetivo_id` int(11) UNSIGNED NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `campania_objetivo`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canal`
--

DROP TABLE IF EXISTS `canal`;
CREATE TABLE IF NOT EXISTS `canal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `canal`
--

INSERT INTO `canal` (`id`, `nombre`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES
(1, 'SMS', 'A', '2021-02-19 15:05:12', 5, NULL, NULL, NULL, NULL),
(2, 'Whatsapp', 'A', '2021-02-19 15:07:07', 5, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configusuario`
--

DROP TABLE IF EXISTS `configusuario`;
CREATE TABLE IF NOT EXISTS `configusuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(250) NOT NULL,
  `intento_login` int(11) NOT NULL DEFAULT '0',
  `fecha_bloqueado` datetime DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `status` enum('A','I','E') NOT NULL,
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `configusuario`
--

INSERT INTO `configusuario` (`id`, `token`, `intento_login`, `fecha_bloqueado`, `usuario_id`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES
(1, '', 1, NULL, 5, 'A', '2021-01-11 01:53:14', 1, NULL, NULL, NULL, NULL),
(2, '', 1, NULL, 13, 'A', '2021-01-27 21:38:29', 1, NULL, NULL, NULL, NULL),
(3, '', 1, NULL, 14, 'A', '2021-01-30 21:53:26', 1, NULL, NULL, NULL, NULL),
(4, '', 1, NULL, 15, 'A', '2021-01-30 22:47:08', 1, NULL, NULL, NULL, NULL),
(5, '', 1, NULL, 16, 'A', '2021-02-03 20:17:51', 1, NULL, NULL, NULL, NULL),
(6, '', 1, NULL, 17, 'A', '2021-02-03 20:25:46', 1, NULL, NULL, NULL, NULL),
(7, '', 1, NULL, 18, 'A', '2021-02-04 23:36:17', 1, NULL, NULL, NULL, NULL),
(8, '', 1, NULL, 19, 'A', '2021-02-10 20:48:26', 1, NULL, NULL, NULL, NULL),
(9, '', 1, NULL, 20, 'A', '2021-02-17 00:48:35', 1, NULL, NULL, NULL, NULL),
(10, '', 1, NULL, 21, 'A', '2021-02-26 18:58:43', 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto`
--

DROP TABLE IF EXISTS `contacto`;
CREATE TABLE IF NOT EXISTS `contacto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(150) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `celular` varchar(13) NOT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `status` enum('A','I') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `contacto`
--
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto_interes`
--

DROP TABLE IF EXISTS `contacto_interes`;
CREATE TABLE IF NOT EXISTS `contacto_interes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contacto_id` int(11) UNSIGNED NOT NULL,
  `interes_id` int(11) UNSIGNED NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `contacto_interes`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_campania`
--

DROP TABLE IF EXISTS `evento_campania`;
CREATE TABLE IF NOT EXISTS `evento_campania` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `campania_id` int(11) UNSIGNED NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `evento_campania`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `interes`
--

DROP TABLE IF EXISTS `interes`;
CREATE TABLE IF NOT EXISTS `interes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `interes`
--

INSERT INTO `interes` (`id`, `nombre`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES
(1, 'E-commerce', 'A', '2021-02-19 15:33:26', 5, NULL, NULL, NULL, NULL),
(2, 'Soporte ténico', 'A', '2021-02-19 15:34:07', 5, NULL, NULL, NULL, NULL),
(3, 'Computadores', 'A', '2021-02-19 15:35:56', 5, NULL, NULL, NULL, NULL),
(4, 'Cloud computing', 'A', '2021-02-24 14:32:19', 5, NULL, NULL, NULL, NULL),
(5, 'Software', 'A', '2021-02-24 14:34:39', 5, NULL, NULL, NULL, NULL),
(6, 'Desarrollo aplicaciones', 'A', '2021-02-24 14:34:56', 5, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

DROP TABLE IF EXISTS `menu`;
CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `type` enum('item','collapse','group') NOT NULL DEFAULT 'group',
  `icon` varchar(150) DEFAULT NULL,
  `padre_id` int(11) UNSIGNED DEFAULT NULL,
  `orden` int(11) NOT NULL,
  `url` varchar(150) DEFAULT NULL,
  `target` tinyint(4) DEFAULT NULL,
  `breadcrumbs` tinyint(1) DEFAULT NULL,
  `status` enum('A','I') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_fecha_modifica` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`id`, `title`, `type`, `icon`, `padre_id`, `orden`, `url`, `target`, `breadcrumbs`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_fecha_modifica`, `fecha_elimina`, `usuario_elimina_id`) VALUES
(1, 'Seguridad', 'group', 'feather icon-layers', NULL, 1, NULL, NULL, NULL, 'A', '2021-01-26 00:39:39', 1, NULL, NULL, NULL, NULL),
(2, 'Configuración', 'collapse', 'feather icon-lock', 1, 1, NULL, NULL, NULL, 'A', '2021-01-26 00:39:39', 1, NULL, NULL, NULL, NULL),
(3, 'Usuarios', 'item', NULL, 2, 1, '/seguridad/usuario/list', NULL, 1, 'A', '2021-01-26 00:39:39', 1, NULL, NULL, NULL, NULL),
(4, 'Permisos', 'item', NULL, 2, 2, '/seguridad/permiso/list', NULL, 1, 'A', '2021-01-26 00:39:39', 1, NULL, NULL, NULL, NULL),
(5, 'Roles', 'item', NULL, 2, 3, '/seguridad/rol/list', NULL, 1, 'A', '2021-01-26 00:39:39', 1, NULL, NULL, NULL, NULL),
(6, 'Registro', 'collapse', 'feather icon-user', 1, 2, NULL, NULL, NULL, 'A', '2021-01-26 00:39:39', 1, NULL, NULL, NULL, NULL),
(7, 'Bloqueos por inicio', 'item', NULL, 2, 2, '/seguridad/bloqueos', NULL, NULL, 'A', '2021-01-26 00:39:39', 1, NULL, NULL, NULL, NULL),
(30, 'Logs', 'item', NULL, 6, 1, '/seguridad/logs', NULL, 1, 'A', '2021-02-28 13:11:05', 1, NULL, NULL, NULL, NULL),
(8, 'Prueba', 'item', NULL, 15, 3, '/campanias/pruebas', NULL, NULL, 'I', '2021-01-26 13:35:31', 1, NULL, NULL, NULL, NULL),
(13, 'Grupo de intereses', 'item', 'feather icon-grid', 11, 2, '/contactos/grupo-interes', NULL, 1, 'I', '2021-02-03 20:01:49', 1, NULL, NULL, NULL, NULL),
(10, 'Contactos', 'group', 'feather icon-layers', NULL, 2, NULL, NULL, NULL, 'A', '2021-01-31 22:42:11', 1, NULL, NULL, NULL, NULL),
(11, 'Libro de contactos', 'collapse', 'feather icon-book', 10, 1, NULL, NULL, NULL, 'A', '2021-01-31 22:42:11', 1, NULL, NULL, NULL, NULL),
(12, 'Clientes', 'item', 'feather icon-users', 11, 1, '/contactos/cliente-list', NULL, 1, 'A', '2021-01-31 22:42:11', 1, NULL, NULL, NULL, NULL),
(14, 'Campañas', 'group', 'feather icon-layers', NULL, 3, NULL, NULL, NULL, 'A', '2021-02-09 02:46:00', 1, NULL, NULL, NULL, NULL),
(15, 'Marketing', 'collapse', 'feather icon-codepen', 14, 1, NULL, NULL, NULL, 'A', '2021-02-09 11:57:26', 1, NULL, NULL, NULL, NULL),
(17, 'Lista de Campañas', 'item', 'feather icon-folder', 15, 1, '/campanias', NULL, 1, 'A', '2021-02-09 11:57:43', 1, NULL, NULL, NULL, NULL),
(18, 'Planificación', 'item', 'feather icon-calendar', 15, 2, '/campanias/programacion', NULL, NULL, 'A', '2021-02-09 11:57:43', 1, NULL, NULL, NULL, NULL),
(31, 'Objetivos', 'item', NULL, 21, 4, '/parametrizacion/objetivo/list', NULL, 1, 'A', '2021-02-28 22:16:53', 1, NULL, NULL, NULL, NULL),
(20, 'Parametrización', 'group', 'feather icon-layers', NULL, 4, NULL, NULL, NULL, 'A', '2021-02-19 00:34:31', 1, NULL, NULL, NULL, NULL),
(21, 'Configuración', 'collapse', 'feather icon-settings', 20, 1, NULL, NULL, NULL, 'A', '2021-02-19 00:34:31', 1, NULL, NULL, NULL, NULL),
(22, 'Canales', 'item', '', 21, 1, '/parametrizacion/canal/list', NULL, NULL, 'A', '2021-02-19 00:34:31', 1, NULL, NULL, NULL, NULL),
(23, 'Parámetros', 'item', '', 21, 2, '/parametrizacion/parametro/list', NULL, NULL, 'A', '2021-02-19 00:34:31', 1, NULL, NULL, NULL, NULL),
(24, 'Intereses', 'item', NULL, 21, 3, '/parametrizacion/interes/list', NULL, NULL, 'A', '2021-02-19 11:57:45', 1, NULL, NULL, NULL, NULL),
(25, 'Reportería', 'group', 'feather icon-layers', NULL, 5, NULL, NULL, NULL, 'A', '2021-02-24 13:03:01', 1, NULL, NULL, NULL, NULL),
(26, 'Reportes', 'collapse', 'feather icon-activity', 25, 1, NULL, NULL, NULL, 'A', '2021-02-24 13:03:01', 1, NULL, NULL, NULL, NULL),
(27, 'Seguimiento de campañas', 'item', '', 26, 1, '/reporteria/seguimiento/list', NULL, 1, 'A', '2021-02-24 13:03:01', 1, NULL, NULL, NULL, NULL),
(28, 'Tablero', 'item', NULL, 26, 2, '/reporteria/tablero', NULL, NULL, 'A', '2021-02-24 18:15:40', 1, NULL, NULL, NULL, NULL),
(29, 'Comportamiento de clientes', 'item', NULL, 26, 3, '/reporteria/comportamiento-cliente/list', NULL, NULL, 'A', '2021-02-28 00:35:16', 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `objetivo`
--

DROP TABLE IF EXISTS `objetivo`;
CREATE TABLE IF NOT EXISTS `objetivo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `objetivo`
--

INSERT INTO `objetivo` (`id`, `nombre`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES
(1, 'Fidelización de clientes', 'A', '2021-03-01 00:36:01', 1, NULL, NULL, NULL, NULL),
(2, 'Reconocimiento de la marca', 'A', '2021-03-01 02:09:23', 5, NULL, NULL, NULL, NULL),
(3, 'Lanzar un producto nuevo', 'A', '2021-03-01 02:09:57', 5, NULL, NULL, NULL, NULL),
(4, 'Aumentar las ventas', 'A', '2021-03-01 02:10:24', 5, NULL, NULL, NULL, NULL),
(5, 'Generar tráfico', 'A', '2021-03-01 02:12:11', 5, NULL, NULL, NULL, NULL),
(6, 'Generación de clientes potenciales', 'A', '2021-03-01 02:14:12', 5, NULL, NULL, NULL, NULL),
(7, 'Ventas del catálogo', 'A', '2021-03-01 02:14:50', 5, NULL, NULL, NULL, NULL),
(8, 'Aumentar clientes', 'I', '2021-03-03 05:34:02', 5, NULL, NULL, NULL, NULL),
(9, 'Segmentación de contactos', 'A', '2021-03-03 06:01:58', 5, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametro`
--

DROP TABLE IF EXISTS `parametro`;
CREATE TABLE IF NOT EXISTS `parametro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `valor` varchar(50) NOT NULL,
  `status` enum('A','I') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `parametro`
--

INSERT INTO `parametro` (`id`, `nombre`, `valor`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES
(1, 'max_intentos_login', '2', 'A', '2021-01-11 01:59:23', 1, NULL, NULL, NULL, NULL),
(2, 'tiempo_espera_login', '3', 'A', '2021-01-11 01:59:23', 1, NULL, NULL, NULL, NULL),
(3, 'numero_telefono', '+16892158900', 'A', '2021-02-19 16:55:27', 5, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

DROP TABLE IF EXISTS `permiso`;
CREATE TABLE IF NOT EXISTS `permiso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_id` int(11) UNSIGNED DEFAULT NULL,
  `usuario_id` int(11) UNSIGNED DEFAULT NULL,
  `menu_id` int(11) UNSIGNED DEFAULT NULL,
  `ver` tinyint(1) DEFAULT NULL,
  `crear` tinyint(1) DEFAULT NULL,
  `editar` tinyint(1) DEFAULT NULL,
  `eliminar` tinyint(1) DEFAULT NULL,
  `status` enum('A','I') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=652 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `permiso`
--

INSERT INTO `permiso` (`id`, `rol_id`, `usuario_id`, `menu_id`, `ver`, `crear`, `editar`, `eliminar`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES
(52, 0, 13, 1, 1, 0, 0, 0, 'A', '2021-01-27 21:39:54', 5, NULL, NULL, NULL, NULL),
(53, 0, 13, 2, 1, 0, 0, 0, 'A', '2021-01-27 21:39:54', 5, NULL, NULL, NULL, NULL),
(54, 0, 13, 3, 1, 1, 1, 1, 'A', '2021-01-27 21:39:54', 5, NULL, NULL, NULL, NULL),
(60, 0, 14, 1, 1, 0, 0, 0, 'A', '2021-01-30 21:54:25', 5, NULL, NULL, NULL, NULL),
(61, 0, 14, 2, 1, 0, 0, 0, 'A', '2021-01-30 21:54:26', 5, NULL, NULL, NULL, NULL),
(62, 0, 14, 3, 1, 1, 0, 0, 'A', '2021-01-30 21:54:26', 5, NULL, NULL, NULL, NULL),
(292, 1, NULL, 1, NULL, 0, 0, 0, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(293, 1, NULL, 2, NULL, 0, 0, 0, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(294, 1, NULL, 3, NULL, NULL, NULL, NULL, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(295, 1, NULL, 4, 1, 1, 1, 0, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(296, 1, NULL, 5, 1, 1, 1, 1, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(297, 1, NULL, 6, NULL, 0, 0, 0, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(298, 1, NULL, 7, 1, 1, 1, 1, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(299, 1, NULL, 10, NULL, 0, 0, 0, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(300, 1, NULL, 11, NULL, 0, 0, 0, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(301, 1, NULL, 13, 1, 1, 1, 1, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(302, 1, NULL, 12, 1, 1, 1, 1, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(303, 1, NULL, 14, NULL, 0, 0, 0, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(304, 1, NULL, 15, NULL, 0, 0, 0, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(305, 1, NULL, 8, 1, 1, 1, 1, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(306, 1, NULL, 17, 1, 1, 1, 1, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(307, 1, NULL, 18, 1, 1, 1, 1, 'A', '2021-02-17 02:03:58', 5, NULL, NULL, NULL, NULL),
(528, NULL, 21, 1, 0, 0, 0, 0, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(529, NULL, 21, 2, 0, 0, 0, 0, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(530, NULL, 21, 3, 1, 1, 1, 1, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(531, NULL, 21, 4, 1, 1, 1, 1, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(532, NULL, 21, 5, 1, 1, 1, 1, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(533, NULL, 21, 6, 0, 0, 0, 0, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(534, NULL, 21, 7, 1, 1, 1, 1, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(535, NULL, 21, 10, 0, 0, 0, 0, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(536, NULL, 21, 11, 0, 0, 0, 0, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(537, NULL, 21, 12, 1, 1, 1, 1, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(538, NULL, 21, 14, 0, 0, 0, 0, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(539, NULL, 21, 15, 0, 0, 0, 0, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(540, NULL, 21, 17, 1, 1, 1, 1, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(541, NULL, 21, 18, 1, 1, 1, 1, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(542, NULL, 21, 20, 0, 0, 0, 0, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(543, NULL, 21, 21, 0, 0, 0, 0, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(544, NULL, 21, 22, 1, 1, 1, 1, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(545, NULL, 21, 23, 1, 1, 1, 1, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(546, NULL, 21, 24, 1, 1, 1, 1, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(547, NULL, 21, 25, 0, 0, 0, 0, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(548, NULL, 21, 26, 0, 0, 0, 0, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(549, NULL, 21, 27, 1, 1, 1, 1, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(550, NULL, 21, 28, 1, 1, 1, 1, 'A', '2021-02-26 21:09:47', 5, NULL, NULL, NULL, NULL),
(626, NULL, 5, 1, 0, 0, 0, 0, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(627, NULL, 5, 2, 0, 0, 0, 0, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(628, NULL, 5, 3, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(629, NULL, 5, 4, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(630, NULL, 5, 5, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(631, NULL, 5, 7, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(632, NULL, 5, 6, 0, 0, 0, 0, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(633, NULL, 5, 30, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(634, NULL, 5, 10, 0, 0, 0, 0, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(635, NULL, 5, 11, 0, 0, 0, 0, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(636, NULL, 5, 12, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(637, NULL, 5, 14, 0, 0, 0, 0, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(638, NULL, 5, 15, 0, 0, 0, 0, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(639, NULL, 5, 17, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(640, NULL, 5, 18, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(641, NULL, 5, 20, NULL, NULL, NULL, NULL, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(642, NULL, 5, 21, NULL, NULL, NULL, NULL, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(643, NULL, 5, 31, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(644, NULL, 5, 22, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(645, NULL, 5, 23, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(646, NULL, 5, 24, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(647, NULL, 5, 25, NULL, NULL, NULL, NULL, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(648, NULL, 5, 26, NULL, NULL, NULL, NULL, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(649, NULL, 5, 27, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(650, NULL, 5, 28, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL),
(651, NULL, 5, 29, 1, 1, 1, 1, 'A', '2021-03-01 02:03:17', 5, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro`
--

DROP TABLE IF EXISTS `registro`;
CREATE TABLE IF NOT EXISTS `registro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` enum('G','M','E','A') NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `menu_id` int(11) UNSIGNED NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `registro`
--

INSERT INTO `registro` (`id`, `tipo`, `nombre`, `menu_id`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES
(12, 'G', 'Se ha guardado un rol.', 1, 'A', '2021-03-01 12:57:53', 5, NULL, NULL, NULL, NULL),
(22, 'E', 'Se ha eliminado un canal.', 20, 'A', '2021-03-02 23:13:26', 5, NULL, NULL, NULL, NULL),
(23, 'M', 'Se ha modificado un objetivo.', 20, 'A', '2021-03-03 05:24:19', 5, NULL, NULL, NULL, NULL),
(24, 'E', 'Se ha eliminado un objetivo.', 20, 'A', '2021-03-03 05:37:03', 5, NULL, NULL, NULL, NULL),
(25, 'A', 'Se ha activado un objetivo.', 20, 'A', '2021-03-03 05:44:25', 5, NULL, NULL, NULL, NULL),
(26, 'E', 'Se ha eliminado un objetivo.', 20, 'A', '2021-03-03 05:45:10', 5, NULL, NULL, NULL, NULL),
(27, 'A', 'Se ha activado un canal.', 20, 'A', '2021-03-03 05:46:02', 5, NULL, NULL, NULL, NULL),
(28, 'E', 'Se ha eliminado un canal.', 20, 'A', '2021-03-03 06:26:09', 5, NULL, NULL, NULL, NULL),
(29, 'A', 'Se ha activado un canal.', 20, 'A', '2021-03-03 06:30:04', 5, NULL, NULL, NULL, NULL),
(30, 'E', 'Se ha eliminado un canal.', 20, 'A', '2021-03-03 06:30:55', 5, NULL, NULL, NULL, NULL),
(31, 'A', 'Se ha activado un parametro.', 1, 'A', '2021-03-03 06:36:41', 0, NULL, NULL, NULL, NULL),
(32, 'E', 'Se ha eliminado un parametro.', 1, 'A', '2021-03-03 06:59:20', 0, NULL, NULL, NULL, NULL),
(33, 'A', 'Se ha activado un parametro.', 1, 'A', '2021-03-03 07:01:43', 0, NULL, NULL, NULL, NULL),
(34, 'E', 'Se ha eliminado un parametro.', 1, 'A', '2021-03-03 07:02:54', 0, NULL, NULL, NULL, NULL),
(35, 'A', 'Se ha activado un parametro.', 1, 'A', '2021-03-03 07:04:46', 0, NULL, NULL, NULL, NULL),
(36, 'E', 'Se ha eliminado un parametro.', 1, 'A', '2021-03-03 07:05:32', 0, NULL, NULL, NULL, NULL),
(37, 'E', 'Se ha eliminado un interés.', 20, 'A', '2021-03-03 07:43:15', 5, NULL, NULL, NULL, NULL),
(38, 'A', 'Se ha activado un interés.', 20, 'A', '2021-03-03 07:49:38', 5, NULL, NULL, NULL, NULL),
(39, 'E', 'Se ha eliminado un parametro.', 1, 'A', '2021-03-03 11:08:34', 5, NULL, NULL, NULL, NULL),
(40, 'E', 'Se ha eliminado un parametro.', 1, 'A', '2021-03-03 11:10:43', 5, NULL, NULL, NULL, NULL),
(41, 'A', 'Se ha activado un parametro.', 1, 'A', '2021-03-03 11:10:52', 5, NULL, NULL, NULL, NULL),
(42, 'M', 'Se ha modificado un parámetro.', 20, 'A', '2021-03-03 11:13:48', 5, NULL, NULL, NULL, NULL),
(43, 'E', 'Se ha eliminado un parametro.', 1, 'A', '2021-03-03 11:15:24', 5, NULL, NULL, NULL, NULL),
(44, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 15:30:13', 5, NULL, NULL, NULL, NULL),
(45, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 15:54:49', 5, NULL, NULL, NULL, NULL),
(46, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 17:48:13', 5, NULL, NULL, NULL, NULL),
(47, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 18:53:16', 5, NULL, NULL, NULL, NULL),
(50, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 19:06:27', 5, NULL, NULL, NULL, NULL),
(51, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 19:06:27', 5, NULL, NULL, NULL, NULL),
(52, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 19:21:53', 5, NULL, NULL, NULL, NULL),
(53, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 19:28:38', 5, NULL, NULL, NULL, NULL),
(54, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 19:31:17', 5, NULL, NULL, NULL, NULL),
(55, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 19:33:23', 5, NULL, NULL, NULL, NULL),
(56, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 19:39:16', 5, NULL, NULL, NULL, NULL),
(57, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 19:39:17', 5, NULL, NULL, NULL, NULL),
(58, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 19:39:18', 5, NULL, NULL, NULL, NULL),
(59, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 19:48:05', 5, NULL, NULL, NULL, NULL),
(60, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 19:48:06', 5, NULL, NULL, NULL, NULL),
(61, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 19:48:07', 5, NULL, NULL, NULL, NULL),
(62, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 19:50:37', 5, NULL, NULL, NULL, NULL),
(63, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 19:50:37', 5, NULL, NULL, NULL, NULL),
(64, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 19:50:38', 5, NULL, NULL, NULL, NULL),
(65, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 19:52:19', 5, NULL, NULL, NULL, NULL),
(66, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 19:52:19', 5, NULL, NULL, NULL, NULL),
(67, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 19:52:20', 5, NULL, NULL, NULL, NULL),
(68, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 19:54:32', 5, NULL, NULL, NULL, NULL),
(69, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 19:54:32', 5, NULL, NULL, NULL, NULL),
(70, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 19:54:33', 5, NULL, NULL, NULL, NULL),
(71, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 19:57:28', 5, NULL, NULL, NULL, NULL),
(73, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 19:58:43', 5, NULL, NULL, NULL, NULL),
(75, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 20:00:01', 5, NULL, NULL, NULL, NULL),
(76, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 20:00:01', 5, NULL, NULL, NULL, NULL),
(77, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 20:00:03', 5, NULL, NULL, NULL, NULL),
(78, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 20:03:04', 5, NULL, NULL, NULL, NULL),
(79, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 20:03:04', 5, NULL, NULL, NULL, NULL),
(80, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 20:03:05', 5, NULL, NULL, NULL, NULL),
(81, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 20:04:38', 5, NULL, NULL, NULL, NULL),
(82, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 20:04:38', 5, NULL, NULL, NULL, NULL),
(83, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 20:04:39', 5, NULL, NULL, NULL, NULL),
(84, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 20:07:07', 5, NULL, NULL, NULL, NULL),
(85, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 20:07:07', 5, NULL, NULL, NULL, NULL),
(86, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 20:07:08', 5, NULL, NULL, NULL, NULL),
(87, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 20:08:37', 5, NULL, NULL, NULL, NULL),
(88, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 20:08:37', 5, NULL, NULL, NULL, NULL),
(89, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 20:08:39', 5, NULL, NULL, NULL, NULL),
(90, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 20:14:03', 5, NULL, NULL, NULL, NULL),
(91, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 20:14:03', 5, NULL, NULL, NULL, NULL),
(92, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 20:14:05', 5, NULL, NULL, NULL, NULL),
(93, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 20:16:03', 5, NULL, NULL, NULL, NULL),
(94, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 20:16:03', 5, NULL, NULL, NULL, NULL),
(95, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 20:16:04', 5, NULL, NULL, NULL, NULL),
(96, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 20:22:20', 5, NULL, NULL, NULL, NULL),
(97, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 20:22:20', 5, NULL, NULL, NULL, NULL),
(98, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 20:22:23', 5, NULL, NULL, NULL, NULL),
(99, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 20:38:44', 5, NULL, NULL, NULL, NULL),
(100, 'G', 'Se ha creado una planificación de campaña.', 14, 'A', '2021-03-03 20:39:17', 5, NULL, NULL, NULL, NULL),
(101, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 22:19:04', 5, NULL, NULL, NULL, NULL),
(102, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 22:19:04', 5, NULL, NULL, NULL, NULL),
(103, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 22:19:05', 5, NULL, NULL, NULL, NULL),
(104, 'G', 'Se ha guardado una campaña.', 14, 'A', '2021-03-03 22:20:19', 5, NULL, NULL, NULL, NULL),
(105, 'G', 'Se ha guardado un seguimiento de campaña', 14, 'A', '2021-03-03 22:20:19', 5, NULL, NULL, NULL, NULL),
(106, 'M', 'Se ha ejecutado una campaña.', 14, 'A', '2021-03-03 22:20:20', 5, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

DROP TABLE IF EXISTS `rol`;
CREATE TABLE IF NOT EXISTS `rol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `status` enum('A','I') NOT NULL,
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `nombre`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES
(1, 'administrador', 'A', '2020-12-23 20:26:48', 1, NULL, NULL, NULL, NULL),
(4, 'Usuario general', 'A', '2021-01-20 18:52:05', 5, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_campania`
--

DROP TABLE IF EXISTS `seguimiento_campania`;
CREATE TABLE IF NOT EXISTS `seguimiento_campania` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_inicio_seguimiento` datetime NOT NULL,
  `fecha_fin_seguimiento` datetime DEFAULT NULL,
  `campania_id` int(11) UNSIGNED NOT NULL,
  `usuarios_interesados` int(11) NOT NULL DEFAULT '0',
  `mensajes_enviados` int(11) NOT NULL DEFAULT '0',
  `mensajes_entregados` int(11) NOT NULL DEFAULT '0',
  `mensajes_rebotados` int(11) NOT NULL DEFAULT '0',
  `mensajes_leidos` int(11) NOT NULL DEFAULT '0',
  `mensajes_respondidos` int(11) NOT NULL DEFAULT '0',
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `seguimiento_campania`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_campania_detalle`
--

DROP TABLE IF EXISTS `seguimiento_campania_detalle`;
CREATE TABLE IF NOT EXISTS `seguimiento_campania_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seguimiento_campania_id` int(11) UNSIGNED NOT NULL,
  `campania_canal_id` int(11) UNSIGNED NOT NULL,
  `campania_contacto_id` int(10) UNSIGNED NOT NULL,
  `estado_mensaje` varchar(50) NOT NULL,
  `is_enviado` tinyint(1) DEFAULT NULL,
  `is_interesado` tinyint(1) DEFAULT NULL,
  `is_entregado` tinyint(4) DEFAULT NULL,
  `is_leido` tinyint(4) DEFAULT NULL,
  `is_respondido` tinyint(4) DEFAULT NULL,
  `is_rebotado` tinyint(4) DEFAULT NULL,
  `message_id` varchar(250) NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `seguimiento_campania_detalle`
--
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `sexo` enum('M','F') NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `nacionalidad` varchar(50) NOT NULL,
  `path_logo` varchar(250) NOT NULL,
  `empresa_id` int(10) UNSIGNED DEFAULT NULL,
  `rol_id` int(11) UNSIGNED NOT NULL,
  `status` enum('A','I','E') NOT NULL DEFAULT 'A',
  `fecha_ingresa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_ingresa_id` int(11) NOT NULL,
  `fecha_modifica` datetime DEFAULT NULL,
  `usuario_modifica_id` int(11) DEFAULT NULL,
  `fecha_elimina` datetime DEFAULT NULL,
  `usuario_elimina_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `usuario`, `password`, `nombres`, `apellidos`, `sexo`, `fecha_nacimiento`, `nacionalidad`, `path_logo`, `empresa_id`, `rol_id`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES
(1, 'sadministrador', '123456', 'Super', 'Administrador', 'F', '2011-06-01', 'Ecuatoriana', '', 1, 1, 'A', '2020-12-23 20:25:01', 5, NULL, NULL, NULL, NULL),
(5, 'jcab', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'Johanna Alejandra', 'Cabrera Borbor', 'F', '1997-09-01', 'ecuatoriana', 'usuario.jpg', NULL, 1, 'A', '2020-12-26 16:11:24', 0, NULL, NULL, NULL, NULL),
(6, 'johanna', '123', 'abc', 'def', 'M', '2021-01-04', 'ec', '', 1, 1, 'A', '2021-01-10 23:14:00', 1, NULL, NULL, NULL, NULL),
(7, 'joha', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'Alejandra', 'Cabrera', 'F', '0000-00-00', '', '', NULL, 1, 'I', '2021-01-19 22:59:04', 1, NULL, NULL, NULL, NULL),
(8, 'charlie', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'Carlos', 'Sánchez', 'M', '0000-00-00', '', '', NULL, 1, 'I', '2021-01-19 23:07:02', 0, NULL, NULL, NULL, NULL),
(9, 'arturo', '114bd151f8fb0c58642d2170da4ae7d7c57977260ac2cc8905306cab6b2acabc', 'Arturo', 'Castro', 'M', '0000-00-00', '', '', NULL, 1, 'I', '2021-01-19 23:08:42', 0, NULL, NULL, NULL, NULL),
(10, 'munir', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'Munir', 'Hidalgo', 'M', '0000-00-00', '', '', NULL, 1, 'I', '2021-01-19 23:09:22', 5, NULL, NULL, NULL, NULL),
(12, 'Char', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'Carlos', 'Castillo', 'M', '0000-00-00', '', '', NULL, 1, 'I', '2021-01-27 21:33:49', 5, NULL, NULL, NULL, NULL),
(13, 'jalexa', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'Johanna', 'Cabrera', 'F', '0000-00-00', '', '', NULL, 1, 'I', '2021-01-27 21:38:29', 5, NULL, NULL, NULL, NULL),
(14, 'joh', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'Joha', 'Nna', 'F', '0000-00-00', '', '', NULL, 4, 'I', '2021-01-30 21:53:26', 5, NULL, NULL, NULL, NULL),
(15, 'usuario1', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'Usuario', 'User', 'F', '0000-00-00', '', '', NULL, 1, 'A', '2021-01-30 22:47:08', 5, NULL, NULL, NULL, NULL),
(16, 'jcabrera', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'Johanna Cabrera', 'Cabrera Borbor', 'F', '0000-00-00', '', '', NULL, 1, 'A', '2021-02-03 20:17:51', 5, NULL, NULL, NULL, NULL),
(17, 'mcabrera', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'Mariuxi Anabel', 'Cabrera Borbor', 'F', '0000-00-00', '', '', NULL, 4, 'A', '2021-02-03 20:25:46', 5, NULL, NULL, NULL, NULL),
(18, 'acarvaca', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'Andrés', 'Carvaca', 'M', '0000-00-00', '', '', NULL, 4, 'A', '2021-02-04 23:36:17', 5, NULL, NULL, NULL, NULL),
(19, 'ayanz', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'Angela', 'Yanz', 'F', '0000-00-00', '', '', NULL, 1, 'I', '2021-02-10 20:48:26', 5, NULL, NULL, NULL, NULL),
(20, 'lcabrera', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'Luis Javier', 'Cabrera Borbor', 'M', '0000-00-00', '', '', NULL, 1, 'A', '2021-02-17 00:48:35', 5, NULL, NULL, NULL, NULL),
(21, 'csanz', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'Charlie Valentin', 'Sanz', 'M', '0000-00-00', '', '', NULL, 1, 'A', '2021-02-26 18:58:43', 5, NULL, NULL, NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
