-- 05 - 03 - 2021
ALTER TABLE `contacto` CHANGE `apellidos` `apellidos` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL; 

ALTER TABLE `campania` CHANGE `descripcion` `descripcion` INT(150) NULL; 

-- 08 - 03 - 2021

ALTER TABLE `seguimiento_campania_detalle` CHANGE `message_id` `message_id` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL; 
ALTER TABLE `seguimiento_campania_detalle` CHANGE `estado_mensaje` `estado_mensaje` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL; 

-- 09 - 03 - 2021
ALTER DATABASE `marketingweb` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `campania` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci

ALTER TABLE campania CHANGE mensaje mensaje LONGTEXT
 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
 
REPAIR TABLE campania;
OPTIMIZE TABLE campania;