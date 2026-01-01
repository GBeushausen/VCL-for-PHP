# SQL Manager 2005 for MySQL 3.6.5.1
# ---------------------------------------
# Host     : localhost
# Port     : 3306
# Database : prueba_db


SET FOREIGN_KEY_CHECKS=0;

DROP DATABASE IF EXISTS `prueba_db`;

CREATE DATABASE `prueba_db`
    CHARACTER SET 'latin1'
    COLLATE 'latin1_swedish_ci';

USE `prueba_db`;

#
# Structure for the `tablaprueba` table :
#

DROP TABLE IF EXISTS `tablaprueba`;

CREATE TABLE `tablaprueba` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=latin1;

insert into tablaprueba VALUES (null,'name','address');
insert into tablaprueba VALUES (null,'name2','address2');

DROP TABLE IF EXISTS `tabladastos`;

CREATE TABLE `tabladastos` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=latin1;
