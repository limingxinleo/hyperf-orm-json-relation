# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.35-log)
# Database: hyperf
# Generation Time: 2021-11-29 10:13:16 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table json_main
# ------------------------------------------------------------

DROP TABLE IF EXISTS `json_main`;

CREATE TABLE `json_main` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `workers` json NOT NULL,
  `data` json NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `json_main` WRITE;
/*!40000 ALTER TABLE `json_main` DISABLE KEYS */;

INSERT INTO `json_main` (`id`, `workers`, `data`)
VALUES
	(1,'[1, 2, 3]','{}'),
	(2,'[2, 3]','{\"worker_ids\": [1, 2]}'),
	(3,'[3]','{\"worker_ids\": [2]}');

/*!40000 ALTER TABLE `json_main` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table json_worker
# ------------------------------------------------------------

DROP TABLE IF EXISTS `json_worker`;

CREATE TABLE `json_worker` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `json_worker` WRITE;
/*!40000 ALTER TABLE `json_worker` DISABLE KEYS */;

INSERT INTO `json_worker` (`id`, `name`)
VALUES
	(1,'worker1'),
	(2,'worker2'),
	(3,'worker3');

/*!40000 ALTER TABLE `json_worker` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
