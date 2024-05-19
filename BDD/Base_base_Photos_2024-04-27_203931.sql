-- MySQL dump 10.13  Distrib 8.3.0, for macos13.6 (x86_64)
--
-- Host: 127.0.0.1    Database: Base_base
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
-- Table structure for table `Photos`
--

DROP TABLE IF EXISTS `Photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Photos`
--

/*!40000 ALTER TABLE `Photos` DISABLE KEYS */;
INSERT INTO `Photos` VALUES (1,'http://localhost:9500//../UPLOADS/superbibi.jpeg','superbibi.jpeg',NULL),(2,'http://localhost:7000//../UPLOADS/caroline.jpeg','caroline.jpeg',NULL);
/*!40000 ALTER TABLE `Photos` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-04-27 20:39:35

CREATE TABLE OAIActionTypes (
    actionType VARCHAR(255) PRIMARY KEY,
    model VARCHAR(255)
);

INSERT INTO OAIActionTypes (actionType, model) 
VALUES 
('translation', 'gpt-3.5-turbo'),
('imgCreate', 'dall-e'),
('textGenerate_def', 'gpt-3.5-turbo'),
('textGenerate_hq', 'gpt-3.5-turbo');

CREATE TABLE OAIActions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    actionType VARCHAR(255),
    dateAction DATETIME,
    action TEXT,
    FOREIGN KEY (actionType) REFERENCES OAIActionTypes(actionType)
);


CREATE TABLE OAIActionData (
    id INT AUTO_INCREMENT PRIMARY KEY,
    actionId INT,
    data TEXT,
    FOREIGN KEY (actionId) REFERENCES OAIActions(id)
);


CREATE TABLE OAIconsos (
    dateaction DATETIME,
    actionId INT,
    conso INT,
    real INT,
    FOREIGN KEY (actionId) REFERENCES OAIActions(id)
);
DELIMITER $$
CREATE FUNCTION addOAIconso(dateaction DATETIME, actionId INT, conso INT, real INT)
RETURNS INT
BEGIN
    INSERT INTO OAIconsos (dateaction, actionId, conso, real) VALUES (dateaction, actionId, conso, real);
    RETURN LAST_INSERT_ID();
END$$
DELIMITER ;


CREATE TABLE OAIconsos (
    dateaction DATETIME,
    actionId INT,
    conso INT,
    `real` INT,
    FOREIGN KEY (actionId) REFERENCES OAIActions(id)
);



  INSERT INTO OAIActions (actionType, dateAction, `action`) VALUES ('translate', '2024-04-29 16:51:21', 'add site content')

   INSERT INTO OAIconsos (dateaction, actionId, conso, eval) VALUES ('2024-04-29 17:05:09', '14', '34', '') 

SELECT CONSTRAINT_NAME 
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'OAIconsos' AND TABLE_SCHEMA = 'Base_base';

ALTER TABLE OAIconsos DROP FOREIGN KEY oaiconsos_ibfk_1;


 SELECT COUNT(*) FROM translations WHERE `key` = 'dsds'
 
 INSERT INTO translations (lang, `key`, value) VALUES ('English', 'dsds', 'hurrah')

INSERT INTO pageContents (textKey, pageId, contType, abtest) VALUES ('accroche1', '', 'text', 'on') 

SELECT id FROM pages WHERE name = 'accueil'

SELECT pc.id, pc.textKey, pc.pageName, pc.contType, pc.abtest, t.`value` FROM pageContents pc LEFT JOIN translations t ON t.key = pc.textKey WHERE pc.pageName = 'accueil'
