-- MySQL dump 10.13  Distrib 8.3.0, for macos13.6 (x86_64)
--
-- Host: 127.0.0.1    Database: sublym_test
-- ------------------------------------------------------
-- Server version	8.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lang` varchar(50) DEFAULT NULL,
  `code` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (11,'English','EN'),(12,'Chinese','ZH'),(13,'Spanish','ES'),(14,'Arabic','AR'),(15,'Portuguese','PT'),(16,'Indonesian/Malay','ID'),(17,'French','FR'),(18,'Japanese','JA'),(19,'Russian','RU'),(20,'German','DE'),(21,'Italian','IT');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;

--
-- Table structure for table `Membres`
--

DROP TABLE IF EXISTS `Membres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Membres` (
  `idMembre` bigint unsigned NOT NULL AUTO_INCREMENT,
  `identifiant` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `dnaiss` int DEFAULT NULL,
  `nom` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `keyvalid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `siret` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `sexe` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `taille` int DEFAULT NULL,
  `corpulence` int DEFAULT NULL,
  `nationalite` int DEFAULT NULL,
  `active` int DEFAULT NULL,
  PRIMARY KEY (`idMembre`)
) ENGINE=InnoDB AUTO_INCREMENT=446 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Membres`
--

/*!40000 ALTER TABLE `Membres` DISABLE KEYS */;
/*!40000 ALTER TABLE `Membres` ENABLE KEYS */;

--
-- Table structure for table `navigation_links`
--

DROP TABLE IF EXISTS `navigation_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `navigation_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `link_url` varchar(2048) NOT NULL,
  `link_value` varchar(255) NOT NULL,
  `link_ident` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `link_ident` (`link_ident`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `navigation_links`
--

/*!40000 ALTER TABLE `navigation_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `navigation_links` ENABLE KEYS */;

--
-- Table structure for table `navigation_menus`
--

DROP TABLE IF EXISTS `navigation_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `navigation_menus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `navigation_menus`
--

/*!40000 ALTER TABLE `navigation_menus` DISABLE KEYS */;
/*!40000 ALTER TABLE `navigation_menus` ENABLE KEYS */;

--
-- Table structure for table `pageMenus`
--

DROP TABLE IF EXISTS `pageMenus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pageMenus` (
  `pageId` int NOT NULL,
  `menuId` int NOT NULL,
  PRIMARY KEY (`pageId`,`menuId`),
  KEY `menuId` (`menuId`),
  CONSTRAINT `pagemenus_ibfk_1` FOREIGN KEY (`pageId`) REFERENCES `Pages` (`id`),
  CONSTRAINT `pagemenus_ibfk_2` FOREIGN KEY (`menuId`) REFERENCES `navigation_menus` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pageMenus`
--

/*!40000 ALTER TABLE `pageMenus` DISABLE KEYS */;
/*!40000 ALTER TABLE `pageMenus` ENABLE KEYS */;

--
-- Table structure for table `Pages`
--

DROP TABLE IF EXISTS `Pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `status` varchar(50) NOT NULL,
  `follow` varchar(255) DEFAULT 'follow',
  `position` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Pages`
--

/*!40000 ALTER TABLE `Pages` DISABLE KEYS */;
INSERT INTO `Pages` VALUES (1,'accueil','Accueil','Description accueil','published','follow',NULL),(2,'page','Page','Description page de base','published','follow',NULL),(4,'landing','Landing page','page de vente','published','follow',NULL),(5,'base','Page de base','page de base','published','follow',NULL),(6,'sublym','Sublym','sublym test','published','follow',NULL),(7,'register','Inscription',NULL,'published','follow',NULL);
/*!40000 ALTER TABLE `Pages` ENABLE KEYS */;

--
-- Table structure for table `Sections`
--

DROP TABLE IF EXISTS `Sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pageId` int NOT NULL,
  `sectionName` varchar(255) NOT NULL,
  `status` varchar(255) DEFAULT 'draft',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Sections`
--

/*!40000 ALTER TABLE `Sections` DISABLE KEYS */;
/*!40000 ALTER TABLE `Sections` ENABLE KEYS */;

--
-- Table structure for table `siteLangs`
--

DROP TABLE IF EXISTS `siteLangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `siteLangs` (
  `langId` int DEFAULT NULL,
  `status` tinyint DEFAULT '0',
  KEY `langId` (`langId`),
  CONSTRAINT `sitelangs_ibfk_1` FOREIGN KEY (`langId`) REFERENCES `languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siteLangs`
--

/*!40000 ALTER TABLE `siteLangs` DISABLE KEYS */;
INSERT INTO `siteLangs` VALUES (11,1),(17,1);
/*!40000 ALTER TABLE `siteLangs` ENABLE KEYS */;

--
-- Dumping routines for database 'sublym_test'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-04-22 19:39:12
