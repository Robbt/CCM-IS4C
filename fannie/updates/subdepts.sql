-- MySQL dump 10.11
--
-- Host: localhost    Database: is4c_op
-- ------------------------------------------------------
-- Server version	5.0.54-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `subdepts`
--

DROP TABLE IF EXISTS `subdepts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `subdepts` (
  `subdept_no` smallint(4) NOT NULL,
  `subdept_name` varchar(30) default NULL,
  `dept_ID` tinyint(2) default NULL,
  KEY `subdept_no` (`subdept_no`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `subdepts`
--

LOCK TABLES `subdepts` WRITE;
/*!40000 ALTER TABLE `subdepts` DISABLE KEYS */;
INSERT INTO `subdepts` VALUES (101,'Grocery',1),(201,'General Merchandise',2),(301,'Haba',3),(401,'Dairy Milks',4),(402,'Dairy Cheeses/Butters',4),(403,'Dairy Yogurts',4),(404,'Alt Milks',4),(405,'Alt Cheese/Butters',4),(406,'Alt Yogurts',4),(407,'Alt meats',4),(408,'Meats',4),(409,'Perishable Beverages',4),(410,'Eggs',4),(411,'Tofu/Tempeh',4),(412,'Raw Foods/Miso',4),(499,'Misc',4),(501,'Frozen',5),(601,'Deli',6),(701,'Herbs/Spices',7),(702,'Teas',7),(799,'Misc',7),(801,'Supplements',8),(802,'Tinctures',8),(899,'Misc',8),(901,'Coffee',9),(902,'Hot Coffee',9),(903,'Bluk Coffee',9),(904,'Pre-Packed Coffee',9),(999,'Misc',9),(1001,'Fresh Bread',10),(1101,'Produce',11),(1201,'Bulk Cheese',12),(1301,'Pet',13),(1401,'Bulk Foods',14),(1501,'Alcohol',15),(1601,'Simply Living',16),(1701,'Admin',17),(1801,'Special Orders',18);
/*!40000 ALTER TABLE `subdepts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-06-13 18:37:50
