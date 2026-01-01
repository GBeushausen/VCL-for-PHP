# SQL Manager 2005 for MySQL 3.6.5.1
# ---------------------------------------
# Host     : localhost
# Port     : 3306
# Database : authentication


SET FOREIGN_KEY_CHECKS=0;

DROP DATABASE IF EXISTS `authentication`;

CREATE DATABASE `authentication`
    CHARACTER SET 'latin1'
    COLLATE 'latin1_swedish_ci';

#
# Structure for the `credentials` table : 
#

DROP TABLE IF EXISTS `credentials`;

CREATE TABLE `credentials` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `realm` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

#
# Data for the `credentials` table  (LIMIT 0,500)
#

INSERT INTO `credentials` (`id`, `username`, `password`, `realm`) VALUES 
  (2,'hexdump','pass','myrealm');

COMMIT;

