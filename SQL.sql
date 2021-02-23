CREATE TABLE `marketingweb`.`usuario` ( `id` INT NOT NULL AUTO_INCREMENT , `usuario` VARCHAR(50) NOT NULL , `password` VARCHAR(255) NOT NULL , `nombres` VARCHAR(50) NOT NULL , `apellidos` VARCHAR(50) NOT NULL , `sexo` ENUM('M','F') NOT NULL , `fecha_nacimiento` DATE NOT NULL , `nacionalidad` VARCHAR(50) NOT NULL , `empresa_id` INT UNSIGNED NULL DEFAULT NULL , `rol_id` INT NOT NULL , `status` ENUM('A','I','E') NOT NULL , `fecha_ingresa` TIMESTAMP NOT NULL , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `marketingweb`.`rol` ( `id` INT NOT NULL AUTO_INCREMENT , `nombre` VARCHAR(50) NOT NULL , `empresa_id` INT UNSIGNED NOT NULL , `status` ENUM('A','I') NOT NULL , `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `marketingweb`.`empresa` ( `id` INT NOT NULL AUTO_INCREMENT , `identificacion` INT NOT NULL , `razon_social` INT NOT NULL , `correo` INT NOT NULL , `celular` INT NOT NULL , `direccion` INT NOT NULL , `imagen_logo` INT NOT NULL , `tipo_identificacion` INT NOT NULL , `status` INT NOT NULL , `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_fecha_modifica` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `usuario` CHANGE `fecha_modifica` `fecha_modifica` DATETIME NULL, CHANGE `usuario_modifica_id` `usuario_modifica_id` INT(11) NULL, CHANGE `fecha_elimina` `fecha_elimina` DATETIME NULL, CHANGE `usuario_elimina_id` `usuario_elimina_id` INT(11) NULL;

ALTER TABLE `usuario` CHANGE `rol_id` `rol_id` INT(11) UNSIGNED NOT NULL;

ALTER TABLE `empresa` CHANGE `tipo_identificacion` `tipo_identificacion` ENUM('CED','RUC','PAS') NOT NULL;

ALTER TABLE `empresa` ADD `nombre_comercial` VARCHAR(150) NOT NULL AFTER `razon_social`;

ALTER TABLE `empresa` CHANGE `razon_social` `razon_social` VARCHAR(150) NOT NULL;

ALTER TABLE `empresa` CHANGE `identificacion` `identificacion` VARCHAR(13) NOT NULL;

ALTER TABLE `empresa` CHANGE `correo` `correo` VARCHAR(50) NOT NULL, CHANGE `celular` `celular` VARCHAR(10) NOT NULL, CHANGE `direccion` `direccion` VARCHAR(200) NOT NULL, CHANGE `imagen_logo` `imagen_logo` VARCHAR(50) NOT NULL, CHANGE `status` `status` ENUM('A','I','E') NOT NULL;

INSERT INTO `usuario` (`id`, `usuario`, `password`, `nombres`, `apellidos`, `sexo`, `fecha_nacimiento`, `nacionalidad`, `empresa_id`, `rol_id`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES (NULL, 'superadmin', '123456', 'Super', 'Admin', 'F', '2011-06-01', 'Ecuatoriana', '1', '1', 'A', '', '1', NULL, NULL, NULL, NULL);
ALTER TABLE `usuario` CHANGE `fecha_ingresa` `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;


INSERT INTO `rol` (`id`, `nombre`, `empresa_id`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES (NULL, 'superadmin', '1', 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL);

ALTER TABLE `empresa` CHANGE `status` `status` ENUM('A','I','E') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'A';

ALTER TABLE `usuario` CHANGE `status` `status` ENUM('A','I','E') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'A';

ALTER TABLE `rol` CHANGE `empresa_id` `usuario_id` INT(10) UNSIGNED NOT NULL; 

CREATE TABLE `marketingweb`.`configUsuario` ( `id` INT NOT NULL AUTO_INCREMENT , `token` VARCHAR(250) NOT NULL , `intento_login` INT(11) NOT NULL DEFAULT 0, `usuario_id` INT NOT NULL , `status` ENUM('A','I','E') NOT NULL , `fecha_ingresa` TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP, `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `marketingweb`.`parametro` ( `id` INT NOT NULL AUTO_INCREMENT , `nombre` VARCHAR(50) NOT NULL ,`valor` INT(11) NOT NULL, `status` ENUM('A','I','E') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

INSERT INTO `parametro` (`id`, `nombre`, `valor`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES (NULL, 'max_intentos_login', '5', 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, 'tiempo_espera_login', '30', 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL);

ALTER TABLE `configusuario` ADD `fecha_bloqueado` DATETIME NULL AFTER `intento_login`; 

-- 25 - 01 - 2021


INSERT INTO `menu` (`id`, `title`, `type`, `icon`, `padre_id`, `url`, `target`, `breadcrumbs`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_fecha_modifica`, `fecha_elimina`, `usuario_elimina_id`) VALUES (NULL, 'Seguridad', 'group', 'feather icon-layers', NULL, NULL, NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, 'Configuración', 'collapse', '', '1', NULL, NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, 'Usuarios', 'item', NULL, '2', '/seguridad/usuario/list', NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, 'Permisos', 'item', NULL, '2', '/seguridad/permiso/list', NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, 'Roles', 'item', NULL, '2', '/seguridad/rol/list', NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, 'Inicio de sesión', 'collapse', 'feather icon-user', '1', NULL, NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, 'Bloqueos por inicio', 'item', NULL, '6', '/seguridad/bloqueos', NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL);

CREATE TABLE `marketingweb`.`parametro` ( `id` INT NOT NULL AUTO_INCREMENT , `rol_id` INT(11) UNSIGNED NULL, `usuario_id` INT(11) UNSIGNED NULL ,`menu_id` INT(11) UNSIGNED NULL, `ver` TINYINT(1) NULL,`crear` TINYINT(1) NULL,`editar` TINYINT(1) NULL,`eliminar` TINYINT(1) NULL, `status` ENUM('A','I') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

-- 26 -- 01 -- 2021 

ALTER TABLE `menu` ADD `orden` INT NOT NULL AFTER `padre_id`; 

-- 31 - 01 -2021 

INSERT INTO `menu` (`id`, `title`, `type`, `icon`, `padre_id`, `orden`, `url`, `target`, `breadcrumbs`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_fecha_modifica`, `fecha_elimina`, `usuario_elimina_id`) VALUES (NULL, 'Contactos', 'group', 'feather icon-layers', NULL, '2', NULL, NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, 'Contactos', 'collapse', 'feather icon-book', '9', '1', NULL, NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, 'Clientes', 'item', 'feather icon-users', '10', '1', 'contactos/cliente-list', NULL, '1', 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL);

-- 01 - 02 - 2021

CREATE TABLE `marketingweb`.`contacto` ( `id` INT NOT NULL AUTO_INCREMENT , `identificacion` VARCHAR(50) NOT NULL, `nombres` VARCHAR(150) NOT NULL, `apellidos` VARCHAR(150) NOT NULL, `fecha_nacimiento` VARCHAR(50) NOT NULL, `celular` VARCHAR(10) NOT NULL, `status` ENUM('A','I') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

-- 03 - 02 - 2021
ALTER TABLE `contacto` ADD `correo` VARCHAR(150) NULL AFTER `celular`; 

-- -- 16 - 02 -2021

-- ALTER TABLE `permiso` DROP `ver`;
INSERT INTO `menu` (`id`, `title`, `type`, `icon`, `padre_id`, `orden`, `url`, `target`, `breadcrumbs`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_fecha_modifica`, `fecha_elimina`, `usuario_elimina_id`) VALUES (NULL, 'Parametrización', 'group', 'feather icon-layers', NULL, '4', NULL, NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, 'Configuración', 'collapse', 'feather icon-settings', NULL, '1', NULL, NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, 'Canales', 'item', '', '20', '1', NULL, NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, 'Parámetros', 'item', '', '20', '2', NULL, NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL)

-- 19 - 02 - 2021 

CREATE TABLE `canal` ( `id` INT NOT NULL AUTO_INCREMENT , `nombre` VARCHAR(50) NOT NULL, `status` ENUM('A','I','E') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

INSERT INTO `menu` (`id`, `title`, `type`, `icon`, `padre_id`, `orden`, `url`, `target`, `breadcrumbs`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_fecha_modifica`, `fecha_elimina`, `usuario_elimina_id`) VALUES (NULL, 'Intereses', 'item', NULL, '21', '3', '/parametrizacion/interes/list', NULL, NULL, 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL);

CREATE TABLE `interes` ( `id` INT NOT NULL AUTO_INCREMENT , `nombre` VARCHAR(50) NOT NULL, `status` ENUM('A','I','E') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `parametro` CHANGE `valor` `valor` VARCHAR(50) NOT NULL; 

-- 20 - 02 - 2021

CREATE TABLE `contacto_interes` ( `id` INT NOT NULL AUTO_INCREMENT , `contacto_id` INT(11) UNSIGNED NOT NULL,`interes_id` INT(11) UNSIGNED NOT NULL, `status` ENUM('A','I','E') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `contacto_interes` (`id`, `contacto_id`, `interes_id`, `status`, `fecha_ingresa`, `usuario_ingresa_id`, `fecha_modifica`, `usuario_modifica_id`, `fecha_elimina`, `usuario_elimina_id`) VALUES (NULL, '1', '1', 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL), (NULL, '1', '3', 'A', CURRENT_TIMESTAMP, '1', NULL, NULL, NULL, NULL);

-- 21 - 02 - 2021

CREATE TABLE `campania` ( `id` INT NOT NULL AUTO_INCREMENT , `tipo` ENUM('P','E','A') NOT NULL DEFAULT 'A', `nombre` VARCHAR(100) NOT NULL,  `mensaje` LONGTEXT NOT NULL, `url` VARCHAR(50) NOT NULL, `url_media` VARCHAR(50) NOT NULL, `status` ENUM('A','I','E') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `campania_interes` ( `id` INT NOT NULL AUTO_INCREMENT , `campania_id` INT(11) UNSIGNED NOT NULL,`interes_id` INT(11) UNSIGNED NOT NULL, `status` ENUM('A','I','E') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `campania_contacto` ( `id` INT NOT NULL AUTO_INCREMENT , `campania_id` INT(11) UNSIGNED NOT NULL,`contacto_id` INT(11) UNSIGNED NOT NULL, `status` ENUM('A','I','E') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `campania_canal` ( `id` INT NOT NULL AUTO_INCREMENT , `campania_id` INT(11) UNSIGNED NOT NULL,`canal_id` INT(11) UNSIGNED NOT NULL, `status` ENUM('A','I','E') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `seguimiento_campania` ( `id` INT NOT NULL AUTO_INCREMENT , `fecha_inicio_seguimiento` DATETIME NOT NULL,`fecha_fin_seguimiento` DATETIME NULL,`campania_id` INT(11) UNSIGNED NOT NULL, `mensajes_leidos` INT(11) NULL, `status` ENUM('A','I','E') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `seguimiento_campania_detalle` ( `id` INT NOT NULL AUTO_INCREMENT ,`seguimiento_campania_id` INT(11) UNSIGNED NOT NULL, `canal_id` INT(11) UNSIGNED NOT NULL,`estado_mensaje` VARCHAR(50) NOT NULL, `status` ENUM('A','I','E') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `evento_campania` ( `id` INT NOT NULL AUTO_INCREMENT , `fecha_inicio` DATETIME NOT NULL,`fecha_fin` DATETIME NULL,`campania_id` INT(11) UNSIGNED NOT NULL, `status` ENUM('A','I','E') NOT NULL DEFAULT 'A', `fecha_ingresa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `usuario_ingresa_id` INT NOT NULL , `fecha_modifica` DATETIME NULL , `usuario_modifica_id` INT NULL , `fecha_elimina` DATETIME NULL , `usuario_elimina_id` INT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `campania` CHANGE `tipo` `tipo` ENUM('P','E','A') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'A' COMMENT 'P: pendiente E: ejecutada A: archivada'; 