-- MySQL dump 10.13  Distrib 8.4.5, for macos14.7 (x86_64)
--
-- Host: localhost    Database: battle2_db
-- ------------------------------------------------------
-- Server version	8.4.5

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
-- Table structure for table `fleet_templates`
--

DROP TABLE IF EXISTS `fleet_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fleet_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vessel_id` int unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fleet_templates`
--

LOCK TABLES `fleet_templates` WRITE;
/*!40000 ALTER TABLE `fleet_templates` DISABLE KEYS */;
INSERT INTO `fleet_templates` VALUES (1,1,'2025-12-18 11:52:36','2025-12-18 11:52:36'),(2,2,'2025-12-18 11:52:36','2025-12-18 11:52:36'),(3,3,'2025-12-18 11:52:36','2025-12-18 11:52:36'),(4,4,'2025-12-18 11:52:36','2025-12-18 11:52:36'),(5,5,'2025-12-18 11:52:36','2025-12-18 11:52:36');
/*!40000 ALTER TABLE `fleet_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fleet_vessel_locations`
--

DROP TABLE IF EXISTS `fleet_vessel_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fleet_vessel_locations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `fleet_vessel_id` int unsigned NOT NULL,
  `move_id` int unsigned NOT NULL,
  `row` tinyint unsigned NOT NULL,
  `col` tinyint unsigned NOT NULL,
  `status` enum('normal','hit','destroyed') COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fleet_vessel_locations`
--

LOCK TABLES `fleet_vessel_locations` WRITE;
/*!40000 ALTER TABLE `fleet_vessel_locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `fleet_vessel_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fleet_vessels`
--

DROP TABLE IF EXISTS `fleet_vessels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fleet_vessels` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `fleet_id` int unsigned NOT NULL,
  `vessel_id` int unsigned NOT NULL,
  `status` enum('available','started','plotted','hit','destroyed') COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fleet_vessels`
--

LOCK TABLES `fleet_vessels` WRITE;
/*!40000 ALTER TABLE `fleet_vessels` DISABLE KEYS */;
/*!40000 ALTER TABLE `fleet_vessels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fleets`
--

DROP TABLE IF EXISTS `fleets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fleets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `game_id` int unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fleets`
--

LOCK TABLES `fleets` WRITE;
/*!40000 ALTER TABLE `fleets` DISABLE KEYS */;
/*!40000 ALTER TABLE `fleets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `games` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('edit','waiting','ready','engaged','completed','deleted','undeleted') COLLATE utf8mb3_unicode_ci NOT NULL,
  `player_one_id` int unsigned NOT NULL,
  `player_two_id` int unsigned DEFAULT NULL,
  `player_two_link_token` varchar(16) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `winner_id` int unsigned NOT NULL,
  `started_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `games_name_unique` (`name`),
  UNIQUE KEY `games_player_two_link_token_unique` (`player_two_link_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `games`
--

LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;
/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_texts`
--

DROP TABLE IF EXISTS `message_texts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_texts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `text` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` enum('specific','broadcast') COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('ready','sent') COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_texts`
--

LOCK TABLES `message_texts` WRITE;
/*!40000 ALTER TABLE `message_texts` DISABLE KEYS */;
INSERT INTO `message_texts` VALUES (1,'Invite owner','Hi %s, a game has been created for you by the system called \'%s\' against opponent \'%s\'. *system_admin','specific','ready','2025-12-18 11:52:36','2025-12-18 11:52:36'),(2,'Invite player','Hi %s, will you play \'%s\' with me? %s','specific','ready','2025-12-18 11:52:36','2025-12-18 11:52:36'),(3,'Accept invitation','Hi %s, I will love playing \'%s\' with you. %s','specific','ready','2025-12-18 11:52:36','2025-12-18 11:52:36'),(4,'Game ready','Hi %s and %s, I\'m happy to say that \'%s\' is ready to play. *system_admin','specific','ready','2025-12-18 11:52:36','2025-12-18 11:52:36'),(5,'Waiting','Hi %s, %s is waiting for you to finish plotting your fleet in the \'%s\' game. *system_admin','specific','ready','2025-12-18 11:52:36','2025-12-18 11:52:36'),(6,'Winner','Hi %s, you won the \'%s\' game.  Well done. %s','specific','ready','2025-12-18 11:52:36','2025-12-18 11:52:36'),(7,'Loser','Hi %s, sadly you lost the \'%s\' game.  Try again later. %s','specific','ready','2025-12-18 11:52:36','2025-12-18 11:52:36'),(8,'Player Two Error','Hi %s, sorry you cannot play \'%s\' against yourself. %s','specific','ready','2025-12-18 11:52:36','2025-12-18 11:52:36'),(9,'Welcome to Version 2','Hi %s, welcome to version two of my battleships game. *system_admin','broadcast','ready','2025-12-18 11:52:36','2025-12-18 11:52:36');
/*!40000 ALTER TABLE `message_texts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `message_text` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('open','read') COLLATE utf8mb3_unicode_ci NOT NULL,
  `sending_user_id` int unsigned NOT NULL,
  `receiving_user_id` int unsigned NOT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES ('2014_10_12_000000_create_users_table',1),('2014_10_12_100000_create_password_resets_table',1),('2025_09_07_000000_create_fleet_templates_table',1),('2025_09_07_000000_create_fleet_vessels_table',1),('2025_09_07_000000_create_fleets_table',1),('2025_09_07_000000_create_vessels_table',1),('2025_09_24_000000_create_games_table',1),('2025_09_24_000000_create_moves_table',1),('2025_10_10_000000_create_fleet_vessel_locations_table',1),('2025_10_11_000000_create_messages_table',1),('2025_11_01_000000_create_message_texts_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `moves`
--

DROP TABLE IF EXISTS `moves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `moves` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `game_id` int unsigned NOT NULL,
  `player_id` int unsigned NOT NULL,
  `row` int unsigned NOT NULL DEFAULT '0',
  `col` int unsigned NOT NULL DEFAULT '0',
  `hit_vessel` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `moves`
--

LOCK TABLES `moves` WRITE;
/*!40000 ALTER TABLE `moves` DISABLE KEYS */;
/*!40000 ALTER TABLE `moves` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_hint` varchar(60) COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_token` varchar(16) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `games_played` int unsigned NOT NULL DEFAULT '0',
  `vessels_destroyed` int unsigned NOT NULL DEFAULT '0',
  `points_scored` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_name_unique` (`name`),
  UNIQUE KEY `users_user_token_unique` (`user_token`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'System Admin','$2y$10$QXo0osbCKtl7KMKWwpZEz.DlZgCYVuVI/WxmUuVDdeImaU/3/8PY.','Conflict with double room number','k9ELpvlDxM5OrSEn',1,NULL,0,0,0,'2025-12-18 11:52:36','2025-12-18 11:52:36'),(2,'Brian','$2y$10$KeihH6Jyxjaq7ATtuoOMoOQ/ArlS7PCx3/KxPLzwB7uqgkn2X3mb6','Conflict with single room number','o4z7g2Ec8XuAYX4N',1,NULL,0,0,0,'2025-12-18 11:52:36','2025-12-18 11:52:36');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vessels`
--

DROP TABLE IF EXISTS `vessels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vessels` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` enum('aircraft-carrier','battleship','cruiser','submarine','destroyer') COLLATE utf8mb3_unicode_ci NOT NULL,
  `length` int unsigned NOT NULL,
  `points` int unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vessels`
--

LOCK TABLES `vessels` WRITE;
/*!40000 ALTER TABLE `vessels` DISABLE KEYS */;
INSERT INTO `vessels` VALUES (1,'aircraft-carrier',5,7,'2025-12-18 11:52:36','2025-12-18 11:52:36'),(2,'battleship',4,6,'2025-12-18 11:52:36','2025-12-18 11:52:36'),(3,'cruiser',3,5,'2025-12-18 11:52:36','2025-12-18 11:52:36'),(4,'submarine',3,5,'2025-12-18 11:52:36','2025-12-18 11:52:36'),(5,'destroyer',2,4,'2025-12-18 11:52:36','2025-12-18 11:52:36');
/*!40000 ALTER TABLE `vessels` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-18 11:52:40
