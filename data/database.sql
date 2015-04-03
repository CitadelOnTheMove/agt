-- Generation Time: Apr 03, 2015 at 07:34 AM
-- Server version: 5.5.41-0ubuntu0.14.04.1
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `citadel_app_generator`
--

-- --------------------------------------------------------

--
-- Table structure for table `apps`
--

CREATE TABLE IF NOT EXISTS `apps` (
  `uid` varchar(60) NOT NULL,
  `name` varchar(50) NOT NULL,
  `userId` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `description` varchar(500) NOT NULL,
  `image` mediumtext,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `apps_available_colors`
--

CREATE TABLE IF NOT EXISTS `apps_available_colors` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `value` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `apps_available_colors`
--

INSERT INTO `apps_available_colors` (`id`, `name`, `value`) VALUES
(1, 'Red', '#F8545B'),
(2, 'Blue', '#4980AE');

-- --------------------------------------------------------

--
-- Table structure for table `apps_available_darker_colors`
--

CREATE TABLE IF NOT EXISTS `apps_available_darker_colors` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `value` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `apps_available_darker_colors`
--

INSERT INTO `apps_available_darker_colors` (`id`, `name`, `value`) VALUES
(1, 'Dark Red', '#F00A14'),
(2, 'Dark Blue', '#00467A');

-- --------------------------------------------------------

--
-- Table structure for table `apps_settings`
--

CREATE TABLE IF NOT EXISTS `apps_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_uid` varchar(60) NOT NULL,
  `apps_settings_definition_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `app_uid` (`app_uid`),
  KEY `apps_settings_definition_id` (`apps_settings_definition_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `apps_settings_definition`
--

INSERT INTO `apps_settings_definition` (`id`, `name`) VALUES
(1, 'City'),
(2, 'Dataset'),
(3, 'Color'),
(4, 'DarkColor'),
(5, 'Category');

-- --------------------------------------------------------

--
-- Table structure for table `apps_settings_definition`
--

CREATE TABLE IF NOT EXISTS `apps_settings_definition` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE IF NOT EXISTS `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataset_id` int(11) NOT NULL,
  `identifier` varchar(450) NOT NULL,
  `value` varchar(450) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_author_dataset` (`dataset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text,
  `default` tinyint(4) NOT NULL DEFAULT '0',
  `dataset_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_dataset` (`dataset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE IF NOT EXISTS `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;

-- --------------------------------------------------------

--
-- Table structure for table `city_datasets`
--

CREATE TABLE IF NOT EXISTS `city_datasets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city_id` int(11) DEFAULT NULL,
  `dataset_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_city_datasets_cities` (`city_id`),
  KEY `FK_city_datasets_dataset_types` (`dataset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `datasets`
--

CREATE TABLE IF NOT EXISTS `datasets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(500) NOT NULL,
  `type` varchar(150) NOT NULL,
  `update` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdBy` int(11) NOT NULL,
  `lang` varchar(5) DEFAULT NULL,
  `updateFrequency` varchar(45) DEFAULT NULL,
  `url` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `KEY` (`identifier`(255))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dataset_types`
--

CREATE TABLE IF NOT EXISTS `dataset_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `licenses`
--

CREATE TABLE IF NOT EXISTS `licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataset_id` int(11) NOT NULL,
  `href` varchar(450) NOT NULL,
  `term` varchar(450) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_dataset_licence` (`dataset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataset_id` int(11) NOT NULL,
  `href` varchar(450) NOT NULL,
  `term` varchar(450) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_dataset_link` (`dataset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `poilabels`
--

CREATE TABLE IF NOT EXISTS `poilabels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poi_id` int(11) DEFAULT NULL,
  `term` varchar(500) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'string',
  `text` varchar(500) NOT NULL,
  `tplIdentifier` varchar(450) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_poiLabels_pois` (`poi_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `pois`
--

CREATE TABLE IF NOT EXISTS `pois` (
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
  KEY `pois_dataset` (`dataset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pois_categories`
--

CREATE TABLE IF NOT EXISTS `pois_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `poi_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poi_categories_categories` (`category_id`),
  KEY `poi_cateogries_pois` (`poi_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(18) NOT NULL,
  `password` varchar(512) NOT NULL,
  `email` varchar(1024) NOT NULL,
  `email_code` varchar(100) NOT NULL,
  `time` int(11) NOT NULL,
  `confirmed` int(11) NOT NULL,
  `ip` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poiId` int(11) NOT NULL,
  `voteDate` datetime NOT NULL,
  `value` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `apps_settings`
--
ALTER TABLE `apps_settings`
  ADD CONSTRAINT `FK_apps_settings_apps` FOREIGN KEY (`app_uid`) REFERENCES `apps` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_apps_settings_apps_settings_definition` FOREIGN KEY (`apps_settings_definition_id`) REFERENCES `apps_settings_definition` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `authors`
--
ALTER TABLE `authors`
  ADD CONSTRAINT `FK_author_dataset` FOREIGN KEY (`dataset_id`) REFERENCES `datasets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_dataset` FOREIGN KEY (`dataset_id`) REFERENCES `datasets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `city_datasets`
--
ALTER TABLE `city_datasets`
  ADD CONSTRAINT `FK_city_datasets_cities` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_city_datasets_dataset_types` FOREIGN KEY (`dataset_id`) REFERENCES `datasets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `licenses`
--
ALTER TABLE `licenses`
  ADD CONSTRAINT `FK_dataset_licence` FOREIGN KEY (`dataset_id`) REFERENCES `datasets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `links`
--
ALTER TABLE `links`
  ADD CONSTRAINT `FK_dataset_link` FOREIGN KEY (`dataset_id`) REFERENCES `datasets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `poilabels`
--
ALTER TABLE `poilabels`
  ADD CONSTRAINT `FK_poiLabels_pois` FOREIGN KEY (`poi_id`) REFERENCES `pois` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `pois`
--
ALTER TABLE `pois`
  ADD CONSTRAINT `pois_dataset` FOREIGN KEY (`dataset_id`) REFERENCES `datasets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `pois_categories`
--
ALTER TABLE `pois_categories`
  ADD CONSTRAINT `FK_categories_poi_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_pois_poi_cateogries` FOREIGN KEY (`poi_id`) REFERENCES `pois` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
