# phptest
PHP Database test
Wanted something quick and simple to verify that all the components where in place to make a PHP site driven by mySQL/MariaDB database so I put together this test site. The code grabs some env variables and displayes them as well as displays a table from a database. Run the following query in your database to generate the test table to be shown
```
-- Dumping database structure for VMdb
DROP DATABASE IF EXISTS `VMdb`;
CREATE DATABASE IF NOT EXISTS `VMdb` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `VMdb`;

-- Dumping structure for table VMdb.tblModules
DROP TABLE IF EXISTS `tblModules`;
CREATE TABLE IF NOT EXISTS `tblModules` (
  `iModID` int(11) NOT NULL AUTO_INCREMENT,
  `vcModuleName` varchar(250) NOT NULL,
  PRIMARY KEY (`iModID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table VMdb.tblModules: ~9 rows (approximately)
DELETE FROM `tblModules`;
/*!40000 ALTER TABLE `tblModules` DISABLE KEYS */;
INSERT INTO `tblModules` (`iModID`, `vcModuleName`) VALUES
	(1, 'VM'),
	(2, 'WAS'),
	(3, 'CA-Windows Agent'),
	(4, 'CA-Linux Agent'),
	(5, 'CA-Mac Agent'),
	(6, 'CA-Solaris Agent'),
	(7, 'CA-AIX Agent'),
	(8, 'CA-BSD Agent'),
	(9, 'WAF'),
	(10, 'MD');
```
