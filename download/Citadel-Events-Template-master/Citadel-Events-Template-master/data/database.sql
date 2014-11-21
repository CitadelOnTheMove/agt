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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authors`
--

LOCK TABLES `authors` WRITE;
/*!40000 ALTER TABLE `authors` DISABLE KEYS */;
INSERT INTO `authors` VALUES (11,30,'http://www.issy.fr','Issy');
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
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (105,'théâtre',0,30),(106,'animation',0,30),(107,'concert',0,30),(108,'projection',0,30),(109,'spectacle',0,30),(110,'conférence',0,30),(111,'rencontre',0,30);
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datasets`
--

LOCK TABLES `datasets` WRITE;
/*!40000 ALTER TABLE `datasets` DISABLE KEYS */;
INSERT INTO `datasets` VALUES (30,'http://www.issy.fr','0000-00-00 00:00:00','0000-00-00 00:00:00','fr-FR','');
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
) ENGINE=InnoDB AUTO_INCREMENT=9274 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poilabels`
--

LOCK TABLES `poilabels` WRITE;
/*!40000 ALTER TABLE `poilabels` DISABLE KEYS */;
INSERT INTO `poilabels` VALUES (9149,3567,'Tel','tel','01 40 93 44 50','#Citadel_telephone'),(9150,3567,'url','url','http://www.espace-icare.com','#Citadel_website'),(9151,3567,'Place','string','Espace Icare','#Citadel_eventPlace'),(9152,3567,'Transports','string','M12 Mairie d\'Issy','#Citadel_nearTransport'),(9153,3567,'Start date','date','1/10/2012','#Citadel_eventStart'),(9154,3567,'End date','date','6/10/2012','#Citadel_eventEnd'),(9155,3567,'Schedule','string','20h30',''),(9156,3567,'Rates','string','entrée libre',''),(9157,3567,'Sort','string','philosophie',''),(9158,3568,'Tel','tel','01 41 23 81 50','#Citadel_telephone'),(9159,3568,'Place','string','Ludothèque','#Citadel_eventPlace'),(9160,3568,'Transports','string','M12 Mairie d\'Issy','#Citadel_nearTransport'),(9161,3568,'Start date','date','6/10/2012','#Citadel_eventStart'),(9162,3568,'End date','date','6/10/2012','#Citadel_eventEnd'),(9163,3568,'Schedule','string','20h30',''),(9164,3568,'Sort','string','jeux de société',''),(9165,3569,'Tel','tel','01 41 46 17 20','#Citadel_telephone'),(9166,3569,'url','url','http://www.clavim.asso.fr','#Citadel_website'),(9167,3569,'Place','string','l\'Entrepont','#Citadel_eventPlace'),(9168,3569,'Transports','string','M12 Mairie d\'Issy','#Citadel_nearTransport'),(9169,3569,'Start date','date','11/10/2012','#Citadel_eventStart'),(9170,3569,'End date','date','11/10/2012','#Citadel_eventEnd'),(9171,3569,'Schedule','string','20h30',''),(9172,3569,'Rates','string','adhérent 6€ ; tout public 9€',''),(9173,3569,'Sort','string','country, folk',''),(9174,3570,'Tel','tel','01 46 38 21 05','#Citadel_telephone'),(9175,3570,'Place','string','La halle des Epinettes','#Citadel_eventPlace'),(9176,3570,'Transports','string','RER C Issy val de Seine','#Citadel_nearTransport'),(9177,3570,'Start date','date','12/10/2012','#Citadel_eventStart'),(9178,3570,'End date','date','12/10/2012','#Citadel_eventEnd'),(9179,3570,'Schedule','string','20h30',''),(9180,3570,'Rates','string','entrée libre',''),(9181,3570,'Sort','string','documentaire',''),(9182,3571,'Tel','tel','01 40 93 44 50','#Citadel_telephone'),(9183,3571,'url','url','http://www.espace-icare.com','#Citadel_website'),(9184,3571,'Place','string','Espace Icare','#Citadel_eventPlace'),(9185,3571,'Transports','string','M12 Mairie d\'Issy','#Citadel_nearTransport'),(9186,3571,'Start date','date','15/10/2012','#Citadel_eventStart'),(9187,3571,'End date','date','18/10/2012','#Citadel_eventEnd'),(9188,3571,'Schedule','string','20h30',''),(9189,3571,'Rates','string','entrée libre',''),(9190,3571,'Sort','string','danse',''),(9191,3572,'Tel','tel','01 40 93 44 50','#Citadel_telephone'),(9192,3572,'url','url','http://www.espace-icare.com','#Citadel_website'),(9193,3572,'Place','string','Espace Icare','#Citadel_eventPlace'),(9194,3572,'Transports','string','M12 Mairie d\'Issy','#Citadel_nearTransport'),(9195,3572,'Start date','date','20/10/2012','#Citadel_eventStart'),(9196,3572,'End date','date','20/10/2012','#Citadel_eventEnd'),(9197,3572,'Schedule','string','20h',''),(9198,3572,'Rates','string','entrée libre',''),(9199,3572,'Sort','string','film',''),(9200,3573,'Tel','tel','01 40 93 44 50','#Citadel_telephone'),(9201,3573,'url','url','http://www.espace-icare.com','#Citadel_website'),(9202,3573,'image','image','images/events1.jpg','#Citadel_image'),(9203,3573,'Place','string','Espace Icare','#Citadel_eventPlace'),(9204,3573,'Transports','string','M12 Mairie d\'Issy','#Citadel_nearTransport'),(9205,3573,'Start date','date','24/10/2012','#Citadel_eventStart'),(9206,3573,'End date','date','26/10/2012','#Citadel_eventEnd'),(9207,3573,'Schedule','string','20h',''),(9208,3573,'Rates','string','entrée libre',''),(9209,3574,'Tel','tel','01 40 68 22 22','#Citadel_telephone'),(9210,3574,'url','url','http://www.viparis.com/Viparis/salon-paris/site/Palais-Congres-Issy/fr/112','#Citadel_website'),(9211,3574,'Place','string','Palais des Congrès d\'Issy','#Citadel_eventPlace'),(9212,3574,'Transports','string','M12 Mairie d\'Issy','#Citadel_nearTransport'),(9213,3574,'Start date','date','25/10/2012','#Citadel_eventStart'),(9214,3574,'End date','date','25/10/2012','#Citadel_eventEnd'),(9215,3574,'Schedule','string','20h-23h',''),(9216,3574,'Sort','string','humour',''),(9217,3575,'Tel','tel','01 41 23 83 60','#Citadel_telephone'),(9218,3575,'url','url','http://www.issy.com/musee/','#Citadel_website'),(9219,3575,'Place','string','Musée de la Carte à jouer','#Citadel_eventPlace'),(9220,3575,'Transports','string','M12 Mairie d\'Issy','#Citadel_nearTransport'),(9221,3575,'Start date','date','25/10/2012','#Citadel_eventStart'),(9222,3575,'End date','date','25/10/2012','#Citadel_eventEnd'),(9223,3575,'Schedule','string','18h30',''),(9224,3575,'Rates','string','entrée libre',''),(9225,3575,'Sort','string','tables rondes',''),(9226,3576,'Tel','tel','01 58 88 30 00','#Citadel_telephone'),(9227,3576,'Place','string','Le cube','#Citadel_eventPlace'),(9228,3576,'Transports','string','RER C Issy val de Seine','#Citadel_nearTransport'),(9229,3576,'Start date','date','6/11/2012','#Citadel_eventStart'),(9230,3576,'End date','date','6/11/2012','#Citadel_eventEnd'),(9231,3576,'Schedule','string','19h30-20h30',''),(9232,3576,'Rates','string','entrée libre',''),(9233,3576,'Sort','string','émission interractive de télévision',''),(9234,3577,'Tel','tel','01 40 68 22 22','#Citadel_telephone'),(9235,3577,'url','url','http://www.viparis.com/Viparis/salon-paris/site/Palais-Congres-Issy/fr/112','#Citadel_website'),(9236,3577,'Place','string','Palais des Congrès d\'Issy','#Citadel_eventPlace'),(9237,3577,'Transports','string','M12 Mairie d\'Issy','#Citadel_nearTransport'),(9238,3577,'Start date','date','10/11/2012','#Citadel_eventStart'),(9239,3577,'End date','date','10/11/2012','#Citadel_eventEnd'),(9240,3577,'Schedule','string','20h-23h',''),(9241,3577,'Sort','string','chansons celtiques et anglo-saxonnes',''),(9242,3578,'Tel','tel','01 41 23 80 69','#Citadel_telephone'),(9243,3578,'Place','string','Médiathèque centre-ville','#Citadel_eventPlace'),(9244,3578,'Transports','string','M12 Mairie d\'Issy','#Citadel_nearTransport'),(9245,3578,'Start date','date','13/11/2012','#Citadel_eventStart'),(9246,3578,'End date','date','13/11/2012','#Citadel_eventEnd'),(9247,3578,'Schedule','string','20h30',''),(9248,3578,'Rates','string','entrée libre',''),(9249,3578,'Sort','string','politique et société',''),(9250,3579,'Tel','tel','01 41 23 80 69','#Citadel_telephone'),(9251,3579,'Place','string','Médiathèque centre-ville','#Citadel_eventPlace'),(9252,3579,'Transports','string','M12 Mairie d\'Issy','#Citadel_nearTransport'),(9253,3579,'Start date','date','22/11/2012','#Citadel_eventStart'),(9254,3579,'End date','date','22/11/2012','#Citadel_eventEnd'),(9255,3579,'Schedule','string','20h30',''),(9256,3579,'Rates','string','entrée libre',''),(9257,3579,'Sort','string','soirée littéraire',''),(9258,3580,'Tel','tel','01 46 38 21 05','#Citadel_telephone'),(9259,3580,'Place','string','La halle des Epinettes','#Citadel_eventPlace'),(9260,3580,'Transports','string','RER C Issy val de Seine','#Citadel_nearTransport'),(9261,3580,'Start date','date','16/11/2012','#Citadel_eventStart'),(9262,3580,'End date','date','16/11/2012','#Citadel_eventEnd'),(9263,3580,'Schedule','string','20h30',''),(9264,3580,'Rates','string','entrée libre',''),(9265,3580,'Sort','string','jam-sessions',''),(9266,3581,'Tel','tel','01 46 38 21 05','#Citadel_telephone'),(9267,3581,'Place','string','La halle des Epinettes','#Citadel_eventPlace'),(9268,3581,'Transports','string','RER C Issy val de Seine','#Citadel_nearTransport'),(9269,3581,'Start date','date','24/11/2012','#Citadel_eventStart'),(9270,3581,'End date','date','24/11/2012','#Citadel_eventEnd'),(9271,3581,'Schedule','string','20h',''),(9272,3581,'Rates','string','entrée libre',''),(9273,3581,'Sort','string','comédie','');
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
) ENGINE=InnoDB AUTO_INCREMENT=3582 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pois`
--

LOCK TABLES `pois` WRITE;
/*!40000 ALTER TABLE `pois` DISABLE KEYS */;
INSERT INTO `pois` VALUES (3567,'citadel_event_issy_1','Je me demande','','http://www.opengis.net/def/crs/EPSG/0/4326',48.8276,2.27699,'31, bd Gambetta','92130','Issy-Les-Moulineaux',30),(3568,'citadel_event_issy_2','Soirée Jeu','','http://www.opengis.net/def/crs/EPSG/0/4326',48.8223,2.27925,'18, rue de l\'Abbé Derry','92130','Issy-Les-Moulineaux',30),(3569,'citadel_event_issy_3','Eddy Ray Cooper','','http://www.opengis.net/def/crs/EPSG/0/4326',48.8245,2.27434,'24, chemin de l\'Accès à la Gare','92130','Issy-Les-Moulineaux',30),(3570,'citadel_event_issy_4','Chercher le courant','','http://www.opengis.net/def/crs/EPSG/0/4326',48.8181,2.26233,'45-47, rue de l\'Egalité','92130','Issy-Les-Moulineaux',30),(3571,'citadel_event_issy_5','Chaque chose est une pause','','http://www.opengis.net/def/crs/EPSG/0/4326',48.8276,2.27699,'31, bd Gambetta','92130','Issy-Les-Moulineaux',30),(3572,'citadel_event_issy_1','Billy Eliott','','http://www.opengis.net/def/crs/EPSG/0/4326',48.8276,2.27699,'31, bd Gambetta','92130','Issy-Les-Moulineaux',30),(3573,'citadel_event_issy_7','Cendrillon, la chatte des cendres','','http://www.opengis.net/def/crs/EPSG/0/4326',48.8276,2.27699,'31, bd Gambetta','92130','Issy-Les-Moulineaux',30),(3574,'citadel_event_issy_8','Dernières avant vegas','','http://www.opengis.net/def/crs/EPSG/0/4326',48.823,2.26951,'25, avenue Victor Cresson','92130','Issy-Les-Moulineaux',30),(3575,'citadel_event_issy_9','Les coulisses de la création','','http://www.opengis.net/def/crs/EPSG/0/4326',48.8236,2.27302,'16, rue Auguste Gervais','92130','Issy-Les-Moulineaux',30),(3576,'citadel_event_issy_10','Rendez-vous du futur : Patrick Viveret','','http://www.opengis.net/def/crs/EPSG/0/4326',48.819,2.25402,'20, cours Saint-Vincent','92130','Issy-Les-Moulineaux',30),(3577,'citadel_event_issy_11','Nolwenn Leroy','','http://www.opengis.net/def/crs/EPSG/0/4326',48.823,2.26951,'25, avenue Victor Cresson','92130','Issy-Les-Moulineaux',30),(3578,'citadel_event_issy_12','L\'idée républicaine dans le monde','','http://www.opengis.net/def/crs/EPSG/0/4326',48.8261,2.26994,'33, rue du gouverneur général Eboué','92130','Issy-Les-Moulineaux',30),(3579,'citadel_event_issy_13','Victor Segalen','','http://www.opengis.net/def/crs/EPSG/0/4326',48.8261,2.26994,'33, rue du gouverneur général Eboué','92130','Issy-Les-Moulineaux',30),(3580,'citadel_event_issy_14','Scène ouverte','','http://www.opengis.net/def/crs/EPSG/0/4326',48.8181,2.26233,'45-47, rue de l\'Egalité','92130','Issy-Les-Moulineaux',30),(3581,'citadel_event_issy_15','We want sex equality','','http://www.opengis.net/def/crs/EPSG/0/4326',48.8181,2.26233,'45-47, rue de l\'Egalité','92130','Issy-Les-Moulineaux',30);
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
) ENGINE=InnoDB AUTO_INCREMENT=3069 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pois_categories`
--

LOCK TABLES `pois_categories` WRITE;
/*!40000 ALTER TABLE `pois_categories` DISABLE KEYS */;
INSERT INTO `pois_categories` VALUES (3053,105,3567),(3054,106,3568),(3055,107,3569),(3056,108,3570),(3057,109,3571),(3058,108,3572),(3059,105,3573),(3060,109,3574),(3061,110,3575),(3062,111,3576),(3063,110,3576),(3064,107,3577),(3065,110,3578),(3066,110,3579),(3067,107,3580),(3068,108,3581);
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

-- Dump completed on 2012-11-30 17:51:27
