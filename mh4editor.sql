-- --------------------------------------------------------
-- Host:                         mh4editor.app
-- Versión del servidor:         5.6.24-0ubuntu2 - (Ubuntu)
-- SO del servidor:              debian-linux-gnu
-- HeidiSQL Versión:             9.2.0.4947
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Volcando estructura de base de datos para mh4editor
CREATE DATABASE IF NOT EXISTS `didix` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `didix`;


-- Volcando estructura para tabla mh4editor.abilities
CREATE TABLE IF NOT EXISTS `abilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `description_en` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla mh4editor.ability_activated
CREATE TABLE IF NOT EXISTS `ability_activated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `description_en` varchar(255) DEFAULT NULL,
  `points` tinyint(4) NOT NULL DEFAULT '0',
  `ability_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `ability_id` (`ability_id`),
  CONSTRAINT `FK_ability_activated_abilities` FOREIGN KEY (`ability_id`) REFERENCES `abilities` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla mh4editor.blacklist_ip
CREATE TABLE IF NOT EXISTS `blacklist_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL,
  `banned` tinyint(4) NOT NULL,
  `quota_finish` tinyint(4) NOT NULL COMMENT 'indica si se ha agotado la cuota diaria de compra para esa ip',
  `banned_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla mh4editor.clients
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `last_api_access` datetime DEFAULT NULL,
  `app_username` varchar(255) NOT NULL,
  `app_serial` varchar(255) NOT NULL,
  `client_upload_save_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  UNIQUE KEY `app_serial` (`app_serial`),
  UNIQUE KEY `client_upload_save_path` (`client_upload_save_path`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Clientes que tienen permiso para conectarse a la API del servidor';

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla mh4editor.crontask
CREATE TABLE IF NOT EXISTS `crontask` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET latin1 NOT NULL,
  `commands` text COLLATE utf8_spanish_ci NOT NULL COMMENT 'comandos a ejecutar',
  `interval` int(11) NOT NULL,
  `lastrun` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla mh4editor.items
CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `name_en` text COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `sub_type` text COLLATE utf8_unicode_ci NOT NULL,
  `rarity` int(11) NOT NULL,
  `carry_capacity` int(11) NOT NULL,
  `buy` int(11) NOT NULL,
  `sell` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description_en` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `web_zenis_buy` int(11) NOT NULL,
  `web_zenis_sell` int(11) NOT NULL,
  `web_caravan_points_buy` int(11) NOT NULL,
  `web_caravan_points_sell` int(11) NOT NULL,
  `icon` text COLLATE utf8_unicode_ci NOT NULL,
  `canonicalName` text COLLATE utf8_unicode_ci NOT NULL,
  `box_capacity` tinyint(3) unsigned NOT NULL DEFAULT '99',
  `times_bought` int(11) NOT NULL DEFAULT '0',
  `locked` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'indica si el item puede ser editado o no',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla mh4editor.items_bought
CREATE TABLE IF NOT EXISTS `items_bought` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `purchase_date` datetime DEFAULT NULL,
  `units` int(11) NOT NULL,
  `money_wasted` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla mh4editor.item_type
CREATE TABLE IF NOT EXISTS `item_type` (
  `id` int(11) NOT NULL,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla mh4editor.talismans
CREATE TABLE IF NOT EXISTS `talismans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `rarity` tinyint(4) NOT NULL,
  `icon` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla mh4editor.talismans_generated
CREATE TABLE IF NOT EXISTS `talismans_generated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `talisman_id` int(11) NOT NULL,
  `ability1_id` int(11) NOT NULL,
  `ab1_amount` int(11) NOT NULL,
  `ability2_id` int(11) NOT NULL,
  `ab2_amount` int(11) NOT NULL,
  `slots` int(11) NOT NULL,
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `talisman_id` (`talisman_id`),
  KEY `ability1_id` (`ability1_id`),
  KEY `ability2_id` (`ability2_id`),
  CONSTRAINT `FK_talismans_generated_abilities` FOREIGN KEY (`ability1_id`) REFERENCES `abilities` (`id`),
  CONSTRAINT `FK_talismans_generated_abilities_2` FOREIGN KEY (`ability2_id`) REFERENCES `abilities` (`id`),
  CONSTRAINT `FK_talismans_generated_talismans` FOREIGN KEY (`talisman_id`) REFERENCES `talismans` (`id`),
  CONSTRAINT `FK_talismans_generated_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.


-- Volcando estructura para tabla mh4editor.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mh4save_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `role` int(11) NOT NULL DEFAULT '0',
  `is_banned` tinyint(1) NOT NULL DEFAULT '0',
  `locale` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `register_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  `last_download_request` datetime DEFAULT NULL,
  `last_ua` text COLLATE utf8_unicode_ci NOT NULL,
  `last_ip` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `items_quota` int(11) NOT NULL DEFAULT '500',
  `max_items_quota` int(11) NOT NULL DEFAULT '500',
  `talismans_quota` int(11) NOT NULL DEFAULT '10',
  `max_talismans_quota` int(11) NOT NULL DEFAULT '10',
  `hunter_name` text COLLATE utf8_unicode_ci,
  `hunter_hr` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E9F85E0677` (`username`),
  UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`),
  UNIQUE KEY `UNIQ_1483A5E9D49AD672` (`mh4save_path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- La exportación de datos fue deseleccionada.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
