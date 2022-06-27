-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               8.0.29 - MySQL Community Server - GPL
-- Server OS:                    Linux
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for VMdb
CREATE DATABASE IF NOT EXISTS `VMdb` /*!40100 DEFAULT CHARACTER SET latin1 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `VMdb`;

-- Dumping structure for table VMdb.tblModules
CREATE TABLE IF NOT EXISTS `tblModules` (
  `iModID` int NOT NULL AUTO_INCREMENT,
  `vcModuleName` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`iModID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table VMdb.tblModules: ~10 rows (approximately)
DELETE FROM `tblModules`;
/*!40000 ALTER TABLE `tblModules` DISABLE KEYS */;
INSERT INTO `tblModules` (`iModID`, `vcModuleName`) VALUES
	(1, 'VM'),
	(2, 'WAS'),
	(3, 'WA-Windows Agent'),
	(4, 'LA-Linux Agent'),
	(5, 'MA-Mac Agent'),
	(6, 'SA-Solaris Agent'),
	(7, 'CA-AIX Agent'),
	(8, 'CA-BSD Agent'),
	(9, 'WAF'),
	(10, 'MD');
/*!40000 ALTER TABLE `tblModules` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
