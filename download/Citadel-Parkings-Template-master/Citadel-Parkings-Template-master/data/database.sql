-- MySQL dump 10.13  Distrib 5.5.15, for Win32 (x86)
--
-- Host: 127.0.0.1    Database: citadel
-- ------------------------------------------------------
-- Server version	5.5.16

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
-- Current Database: `citadel`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `citadel` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `citadel`;

--
-- Table structure for table `authors`
--

DROP TABLE IF EXISTS `authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataset_id` int(11) NOT NULL,
  `identifier` varchar(450) NOT NULL,
  `value` varchar(450) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_author_dataset` (`dataset_id`),
  CONSTRAINT `FK_author_dataset` FOREIGN KEY (`dataset_id`) REFERENCES `datasets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authors`
--

LOCK TABLES `authors` WRITE;
/*!40000 ALTER TABLE `authors` DISABLE KEYS */;
INSERT INTO `authors` VALUES (28,47,'http://www.ville-issy.fr/','City of Athens');
/*!40000 ALTER TABLE `authors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text,
  `default` tinyint(4) NOT NULL DEFAULT '0',
  `dataset_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_dataset` (`dataset_id`),
  CONSTRAINT `categories_dataset` FOREIGN KEY (`dataset_id`) REFERENCES `datasets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (137,'Parking',0,47);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datasets`
--

DROP TABLE IF EXISTS `datasets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datasets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(500) NOT NULL,
  `update` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `lang` varchar(5) NOT NULL,
  `updateFrequency` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `KEY` (`identifier`(255))
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datasets`
--

LOCK TABLES `datasets` WRITE;
/*!40000 ALTER TABLE `datasets` DISABLE KEYS */;
INSERT INTO `datasets` VALUES (47,'http://www.example.com/','0000-00-00 00:00:00','0000-00-00 00:00:00','fr-FR','');
/*!40000 ALTER TABLE `datasets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `licenses`
--

DROP TABLE IF EXISTS `licenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataset_id` int(11) NOT NULL,
  `href` varchar(450) NOT NULL,
  `term` varchar(450) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_dataset_licence` (`dataset_id`),
  CONSTRAINT `FK_dataset_licence` FOREIGN KEY (`dataset_id`) REFERENCES `datasets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `licenses`
--

LOCK TABLES `licenses` WRITE;
/*!40000 ALTER TABLE `licenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `licenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataset_id` int(11) NOT NULL,
  `href` varchar(450) NOT NULL,
  `term` varchar(450) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_dataset_link` (`dataset_id`),
  CONSTRAINT `FK_dataset_link` FOREIGN KEY (`dataset_id`) REFERENCES `datasets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `links`
--

LOCK TABLES `links` WRITE;
/*!40000 ALTER TABLE `links` DISABLE KEYS */;
/*!40000 ALTER TABLE `links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poilabels`
--

DROP TABLE IF EXISTS `poilabels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poilabels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poi_id` int(11) DEFAULT NULL,
  `term` varchar(500) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'string',
  `text` varchar(500) NOT NULL,
  `tplIdentifier` varchar(450) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_poiLabels_pois` (`poi_id`),
  CONSTRAINT `FK_poiLabels_pois` FOREIGN KEY (`poi_id`) REFERENCES `pois` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13690 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poilabels`
--

LOCK TABLES `poilabels` WRITE;
/*!40000 ALTER TABLE `poilabels` DISABLE KEYS */;
INSERT INTO `poilabels` VALUES (13450,4579,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13451,4579,'url','url','','#Citadel_website'),(13452,4579,'E-mail','email','','#Citadel_email'),(13453,4579,'parkingType','string','ground','#Citadel_parkType'),(13454,4579,'Open Hours','string','Monday-Saturday 0800-2000,  Sunday 0800-1800.','#Citadel_openHours'),(13455,4579,'Run by','string','NCP Manchester Ltd',''),(13456,4580,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13457,4580,'url','url','','#Citadel_website'),(13458,4580,'E-mail','email','','#Citadel_email'),(13459,4580,'parkingType','string','ground','#Citadel_parkType'),(13460,4580,'Open Hours','string','Monday-Sunday 0600-2300','#Citadel_openHours'),(13461,4580,'Run by','string','NCP Manchester Ltd',''),(13462,4581,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13463,4581,'url','url','','#Citadel_website'),(13464,4581,'E-mail','email','','#Citadel_email'),(13465,4581,'parkingType','string','ground','#Citadel_parkType'),(13466,4581,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13467,4581,'Run by','string','NCP Manchester Ltd',''),(13468,4582,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13469,4582,'url','url','','#Citadel_website'),(13470,4582,'E-mail','email','','#Citadel_email'),(13471,4582,'parkingType','string','ground','#Citadel_parkType'),(13472,4582,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13473,4582,'Run by','string','NCP Manchester Ltd',''),(13474,4583,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13475,4583,'url','url','','#Citadel_website'),(13476,4583,'E-mail','email','','#Citadel_email'),(13477,4583,'parkingType','string','ground','#Citadel_parkType'),(13478,4583,'Open Hours','string','Monday-Sunday 24 Hours,Saturday & Sunday = Credit Cards Only','#Citadel_openHours'),(13479,4583,'Run by','string','NCP Manchester Ltd',''),(13480,4584,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13481,4584,'url','url','','#Citadel_website'),(13482,4584,'E-mail','email','','#Citadel_email'),(13483,4584,'parkingType','string','ground','#Citadel_parkType'),(13484,4584,'Open Hours','string','Monday-Saturday 0800-2000,Sunday 0800-1800','#Citadel_openHours'),(13485,4584,'Run by','string','NCP Manchester Ltd',''),(13486,4585,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13487,4585,'url','url','','#Citadel_website'),(13488,4585,'E-mail','email','','#Citadel_email'),(13489,4585,'parkingType','string','ground','#Citadel_parkType'),(13490,4585,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13491,4585,'Run by','string','NCP Manchester Ltd',''),(13492,4586,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13493,4586,'url','url','','#Citadel_website'),(13494,4586,'E-mail','email','','#Citadel_email'),(13495,4586,'parkingType','string','ground','#Citadel_parkType'),(13496,4586,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13497,4586,'Run by','string','NCP Manchester Ltd',''),(13498,4587,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13499,4587,'url','url','','#Citadel_website'),(13500,4587,'E-mail','email','','#Citadel_email'),(13501,4587,'parkingType','string','ground','#Citadel_parkType'),(13502,4587,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13503,4587,'Run by','string','NCP Manchester Ltd',''),(13504,4588,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13505,4588,'url','url','','#Citadel_website'),(13506,4588,'E-mail','email','','#Citadel_email'),(13507,4588,'parkingType','string','ground','#Citadel_parkType'),(13508,4588,'Open Hours','string','Monday-Sunday 0700-2200','#Citadel_openHours'),(13509,4588,'Run by','string','NCP Manchester Ltd',''),(13510,4589,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13511,4589,'url','url','','#Citadel_website'),(13512,4589,'E-mail','email','','#Citadel_email'),(13513,4589,'parkingType','string','ground','#Citadel_parkType'),(13514,4589,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13515,4589,'Run by','string','NCP Manchester Ltd',''),(13516,4590,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13517,4590,'url','url','','#Citadel_website'),(13518,4590,'E-mail','email','','#Citadel_email'),(13519,4590,'parkingType','string','ground','#Citadel_parkType'),(13520,4590,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13521,4590,'Run by','string','NCP Manchester Ltd',''),(13522,4591,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13523,4591,'url','url','','#Citadel_website'),(13524,4591,'E-mail','email','','#Citadel_email'),(13525,4591,'parkingType','string','ground','#Citadel_parkType'),(13526,4591,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13527,4591,'Run by','string','NCP Manchester Ltd',''),(13528,4592,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13529,4592,'url','url','','#Citadel_website'),(13530,4592,'E-mail','email','','#Citadel_email'),(13531,4592,'parkingType','string','ground','#Citadel_parkType'),(13532,4592,'Open Hours','string','Monday-Saturday 0800-2000,  Sunday 0800-1800','#Citadel_openHours'),(13533,4592,'Run by','string','NCP Manchester Ltd',''),(13534,4593,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13535,4593,'url','url','','#Citadel_website'),(13536,4593,'E-mail','email','','#Citadel_email'),(13537,4593,'parkingType','string','ground','#Citadel_parkType'),(13538,4593,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13539,4593,'Run by','string','NCP Manchester Ltd',''),(13540,4594,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13541,4594,'url','url','','#Citadel_website'),(13542,4594,'E-mail','email','','#Citadel_email'),(13543,4594,'parkingType','string','ground','#Citadel_parkType'),(13544,4594,'Open Hours','string','Monday-Friday 0730-1930,Saturday 0800-1800,Sunday Closed.','#Citadel_openHours'),(13545,4594,'Run by','string','NCP Manchester Ltd',''),(13546,4595,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13547,4595,'url','url','','#Citadel_website'),(13548,4595,'E-mail','email','','#Citadel_email'),(13549,4595,'parkingType','string','ground','#Citadel_parkType'),(13550,4595,'Open Hours','string','Monday-Sunday 0700-0000','#Citadel_openHours'),(13551,4595,'Run by','string','NCP Manchester Ltd',''),(13552,4596,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13553,4596,'url','url','','#Citadel_website'),(13554,4596,'E-mail','email','','#Citadel_email'),(13555,4596,'parkingType','string','underground','#Citadel_parkType'),(13556,4596,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13557,4596,'Run by','string','NCP Manchester Ltd',''),(13558,4597,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13559,4597,'url','url','','#Citadel_website'),(13560,4597,'E-mail','email','','#Citadel_email'),(13561,4597,'parkingType','string','ground','#Citadel_parkType'),(13562,4597,'Open Hours','string','Monday-Sunday 0730-1830, If there is a show, site will stay open till late','#Citadel_openHours'),(13563,4597,'Run by','string','NCP Manchester Ltd',''),(13564,4598,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13565,4598,'url','url','','#Citadel_website'),(13566,4598,'E-mail','email','','#Citadel_email'),(13567,4598,'parkingType','string','ground','#Citadel_parkType'),(13568,4598,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13569,4598,'Run by','string','NCP Manchester Ltd',''),(13570,4599,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13571,4599,'url','url','','#Citadel_website'),(13572,4599,'E-mail','email','','#Citadel_email'),(13573,4599,'parkingType','string','ground','#Citadel_parkType'),(13574,4599,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13575,4599,'Run by','string','NCP Manchester Ltd',''),(13576,4600,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13577,4600,'url','url','','#Citadel_website'),(13578,4600,'E-mail','email','','#Citadel_email'),(13579,4600,'parkingType','string','ground','#Citadel_parkType'),(13580,4600,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13581,4600,'Run by','string','NCP Manchester Ltd',''),(13582,4601,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13583,4601,'url','url','','#Citadel_website'),(13584,4601,'E-mail','email','','#Citadel_email'),(13585,4601,'parkingType','string','underground','#Citadel_parkType'),(13586,4601,'Open Hours','string','Monday-Wednesday Saturday 0745-2030,Thursday-Friday 0745-2130,Sunday 0945-1800','#Citadel_openHours'),(13587,4601,'Run by','string','NCP Manchester Ltd',''),(13588,4602,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13589,4602,'url','url','','#Citadel_website'),(13590,4602,'E-mail','email','','#Citadel_email'),(13591,4602,'parkingType','string','ground','#Citadel_parkType'),(13592,4602,'Open Hours','string','Monday-Friday 0700-1900,  Saturday-Sunday Closed','#Citadel_openHours'),(13593,4602,'Run by','string','NCP Manchester Ltd',''),(13594,4603,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13595,4603,'url','url','','#Citadel_website'),(13596,4603,'E-mail','email','','#Citadel_email'),(13597,4603,'parkingType','string','ground','#Citadel_parkType'),(13598,4603,'Open Hours','string','Monday-Sunday 24 Hours,Saturday & Sunday = Credit Cards only','#Citadel_openHours'),(13599,4603,'Run by','string','NCP Manchester Ltd',''),(13600,4604,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13601,4604,'url','url','','#Citadel_website'),(13602,4604,'E-mail','email','','#Citadel_email'),(13603,4604,'parkingType','string','ground','#Citadel_parkType'),(13604,4604,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13605,4604,'Run by','string','NCP Manchester Ltd',''),(13606,4605,'Tel','tel','','#Citadel_telephone'),(13607,4605,'url','url','','#Citadel_website'),(13608,4605,'E-mail','email','','#Citadel_email'),(13609,4605,'parkingType','string','ground','#Citadel_parkType'),(13610,4605,'Open Hours','string','24hrs','#Citadel_openHours'),(13611,4605,'Run by','string','Manchester City Council',''),(13612,4606,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13613,4606,'url','url','','#Citadel_website'),(13614,4606,'E-mail','email','','#Citadel_email'),(13615,4606,'parkingType','string','ground','#Citadel_parkType'),(13616,4606,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13617,4606,'Run by','string','NCP Manchester Ltd',''),(13618,4607,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13619,4607,'url','url','','#Citadel_website'),(13620,4607,'E-mail','email','','#Citadel_email'),(13621,4607,'parkingType','string','ground','#Citadel_parkType'),(13622,4607,'Open Hours','string','Monday-Friday 0700-1600,Saturday-Sunday Closed','#Citadel_openHours'),(13623,4607,'Run by','string','NCP Manchester Ltd',''),(13624,4608,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13625,4608,'url','url','','#Citadel_website'),(13626,4608,'E-mail','email','','#Citadel_email'),(13627,4608,'parkingType','string','underground','#Citadel_parkType'),(13628,4608,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13629,4608,'Run by','string','NCP Manchester Ltd',''),(13630,4609,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13631,4609,'url','url','','#Citadel_website'),(13632,4609,'E-mail','email','','#Citadel_email'),(13633,4609,'parkingType','string','ground','#Citadel_parkType'),(13634,4609,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13635,4609,'Run by','string','NCP Manchester Ltd',''),(13636,4610,'Tel','tel','','#Citadel_telephone'),(13637,4610,'url','url','','#Citadel_website'),(13638,4610,'E-mail','email','','#Citadel_email'),(13639,4610,'parkingType','string','ground','#Citadel_parkType'),(13640,4610,'Open Hours','string','24hrs','#Citadel_openHours'),(13641,4610,'Run by','string','Manchester City Council',''),(13642,4611,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13643,4611,'url','url','','#Citadel_website'),(13644,4611,'E-mail','email','','#Citadel_email'),(13645,4611,'parkingType','string','ground','#Citadel_parkType'),(13646,4611,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13647,4611,'Run by','string','NCP Manchester Ltd',''),(13648,4612,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13649,4612,'url','url','','#Citadel_website'),(13650,4612,'E-mail','email','','#Citadel_email'),(13651,4612,'parkingType','string','ground','#Citadel_parkType'),(13652,4612,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13653,4612,'Run by','string','NCP Manchester Ltd',''),(13654,4613,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13655,4613,'url','url','','#Citadel_website'),(13656,4613,'E-mail','email','','#Citadel_email'),(13657,4613,'parkingType','string','ground','#Citadel_parkType'),(13658,4613,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13659,4613,'Run by','string','NCP Manchester Ltd',''),(13660,4614,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13661,4614,'url','url','','#Citadel_website'),(13662,4614,'E-mail','email','','#Citadel_email'),(13663,4614,'parkingType','string','ground','#Citadel_parkType'),(13664,4614,'Open Hours','string','Monday-Saturday 0800-2000,Sunday 0800-1800','#Citadel_openHours'),(13665,4614,'Run by','string','NCP Manchester Ltd',''),(13666,4615,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13667,4615,'url','url','','#Citadel_website'),(13668,4615,'E-mail','email','','#Citadel_email'),(13669,4615,'parkingType','string','ground','#Citadel_parkType'),(13670,4615,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13671,4615,'Run by','string','NCP Manchester Ltd',''),(13672,4616,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13673,4616,'url','url','','#Citadel_website'),(13674,4616,'E-mail','email','','#Citadel_email'),(13675,4616,'parkingType','string','ground','#Citadel_parkType'),(13676,4616,'Open Hours','string','Monday-Sunday 24 Hours','#Citadel_openHours'),(13677,4616,'Run by','string','NCP Manchester Ltd',''),(13678,4617,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13679,4617,'url','url','','#Citadel_website'),(13680,4617,'E-mail','email','','#Citadel_email'),(13681,4617,'parkingType','string','ground','#Citadel_parkType'),(13682,4617,'Open Hours','string','Monday-Saturday 0800-2000,Sunday 0800-1800','#Citadel_openHours'),(13683,4617,'Run by','string','NCP Manchester Ltd',''),(13684,4618,'Tel','tel','0845 050 7080','#Citadel_telephone'),(13685,4618,'url','url','','#Citadel_website'),(13686,4618,'E-mail','email','','#Citadel_email'),(13687,4618,'parkingType','string','ground','#Citadel_parkType'),(13688,4618,'Open Hours','string','Monday-Friday 0700-1600,Saturday-Sunday Closed','#Citadel_openHours'),(13689,4618,'Run by','string','NCP Manchester Ltd','');
/*!40000 ALTER TABLE `poilabels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pois`
--

DROP TABLE IF EXISTS `pois`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pois` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(45) NOT NULL,
  `title` varchar(450) NOT NULL,
  `description` text NOT NULL,
  `pointSrsName` varchar(450) DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `address` varchar(45) DEFAULT NULL,
  `postal` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `dataset_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pois_dataset` (`dataset_id`),
  CONSTRAINT `pois_dataset` FOREIGN KEY (`dataset_id`) REFERENCES `datasets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4619 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pois`
--

LOCK TABLES `pois` WRITE;
/*!40000 ALTER TABLE `pois` DISABLE KEYS */;
INSERT INTO `pois` VALUES (4579,'1','Abingdon Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4771,-2.23896,'Abingdon Street, Corner Of Major Street','','',47),(4580,'2','Aquatic Centre ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.469,-2.23502,'Aquatic Centre, Booth Street East','','',47),(4581,'3','Bloom Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4769,-2.23803,'Bloom Street, Fmr National Express Bus Statio','','',47),(4582,'4','Bridge Street ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4814,-2.25122,'Bridge Street Sp','','',47),(4583,'5','Bridgewater Hall ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4741,-2.25021,'Bridgewater Hall, Little Peter Street','','',47),(4584,'6','Chepstow Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4757,-2.24435,'Chepstow Street','','',47),(4585,'7','Chester Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4721,-2.24143,'Chester Street','','',47),(4586,'8','China Town ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4783,-2.23956,'China Town','','',47),(4587,'9','Chorlton Street ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4775,-2.23533,'Chorlton Street','','',47),(4588,'10','Church Street ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4838,-2.23498,'Church Street','','',47),(4589,'11','Gt Northern Warehouse 1','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4766,-2.24887,'Gt Northern Warehouse, Watson Street','','',47),(4590,'12','Gt Northern Warehouse 2','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4766,-2.24882,'Gt Northern Warehouse, Watson Street','','',47),(4591,'13','High Street Arndale Centre','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4838,-2.23787,'High Street, Nicholas Croft','','',47),(4592,'14','Hulme Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4721,-2.24394,'Hulme Street ','','',47),(4593,'15','King Street West ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4814,-2.24885,'King Street West ','','',47),(4594,'16','Major Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4788,-2.23618,'Major Street ','','',47),(4595,'17','Manchester Arena ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4898,-2.24379,'New Bridge Street','','',47),(4596,'18','Manchester Central - G-Mex Under ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4769,-2.24716,'Central (G-Mex) Underground, Lower Mosley Str','','',47),(4597,'19','Manchester Central - G-Mex Upper ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4769,-2.24716,'Central (G-Mex) Rt, Lower Mosley Street','','',47),(4598,'20','Manchester Printworks','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4852,-2.23962,'Manchester Transport Interchange, Site B, Shu','','',47),(4599,'21','Manchester Victoria Station','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4864,-2.24172,'Long Mill Gate Street','','',47),(4600,'22','Market Place ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4833,-2.24686,'Blackfriars Street','','',47),(4601,'23','Marks & Spencer ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4837,-2.24395,'Marks & Spencer Underground, Off Market Stree','','',47),(4602,'24','Medlock Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4715,-2.24572,'Medlock Street, Old Dunlop Street, (Opposite ','','',47),(4603,'25','Miller Street ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4877,-2.23939,'Miller Street, Dantzic Street','','',47),(4604,'26','Oxford Street ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4756,-2.24213,'Oxford Street','','',47),(4605,'27','Oxford Street - 2 bays','','http://www.opengis.net/def/crs/EPSG/0/4326',53.475,-2.24134,'Opposite the Palace Theatre','','',47),(4606,'28','Palace ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4756,-2.23973,'Palace, Whitworth Street','','',47),(4607,'29','Park Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4905,-2.24334,'Park Street','','',47),(4608,'30','Piccadilly Plaza Ug','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4796,-2.23824,'York Street','','',47),(4609,'31','Queen Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4795,-2.24819,'Queen Street Sp','','',47),(4610,'32','Queens Street/Lincoln Square - 1 bay','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4795,-2.2466,'Off Deansgate, and Brazennose Street','','',47),(4611,'33','Sackville Street ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4769,-2.23803,'Off Bloom Street','','',47),(4612,'34','Sheffield Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4771,-2.22708,'Sheffield Street','','',47),(4613,'35','Spinningfields ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4797,-2.25441,'New Quay Street','','',47),(4614,'36','Stone Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4761,-2.25393,'Stone Street Sp','','',47),(4615,'37','The Grand ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4797,-2.23409,'Chatham Street','','',47),(4616,'38','Tib Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4839,-2.23459,'Tib Street','','',47),(4617,'39','Travis Street ','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4772,-2.22536,'Travis Street','','',47),(4618,'40','Water Street','','http://www.opengis.net/def/crs/EPSG/0/4326',53.4741,-2.26242,'Water Street','','',47);
/*!40000 ALTER TABLE `pois` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pois_categories`
--

DROP TABLE IF EXISTS `pois_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pois_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `poi_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poi_categories_categories` (`category_id`),
  KEY `poi_cateogries_pois` (`poi_id`),
  CONSTRAINT `FK_pois_poi_cateogries` FOREIGN KEY (`poi_id`) REFERENCES `pois` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK_categories_poi_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4106 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pois_categories`
--

LOCK TABLES `pois_categories` WRITE;
/*!40000 ALTER TABLE `pois_categories` DISABLE KEYS */;
INSERT INTO `pois_categories` VALUES (4066,137,4579),(4067,137,4580),(4068,137,4581),(4069,137,4582),(4070,137,4583),(4071,137,4584),(4072,137,4585),(4073,137,4586),(4074,137,4587),(4075,137,4588),(4076,137,4589),(4077,137,4590),(4078,137,4591),(4079,137,4592),(4080,137,4593),(4081,137,4594),(4082,137,4595),(4083,137,4596),(4084,137,4597),(4085,137,4598),(4086,137,4599),(4087,137,4600),(4088,137,4601),(4089,137,4602),(4090,137,4603),(4091,137,4604),(4092,137,4605),(4093,137,4606),(4094,137,4607),(4095,137,4608),(4096,137,4609),(4097,137,4610),(4098,137,4611),(4099,137,4612),(4100,137,4613),(4101,137,4614),(4102,137,4615),(4103,137,4616),(4104,137,4617),(4105,137,4618);
/*!40000 ALTER TABLE `pois_categories` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-11-30 18:50:35
