-- MySQL dump 10.13  Distrib 8.0.33, for Linux (x86_64)
--
-- Host: localhost    Database: shoe_shop_db
-- ------------------------------------------------------
-- Server version	8.0.33-0ubuntu0.22.04.2

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
-- Current Database: `shoe_shop_db`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `shoe_shop_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `shoe_shop_db`;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `brand` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('sneakers','boots','sandals','formal','athletic') COLLATE utf8mb4_unicode_ci NOT NULL,
  `size_range` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT '0',
  `price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `idx_search` (`name`,`description`,`brand`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` VALUES 
(1,'Air Max 90','Classic Nike sneakers with visible air unit','Nike','sneakers','US 6-13','White/Black',45,120.00,80.00,'air-max-90.jpg','2023-06-01 08:00:00','2023-06-15 14:30:00'),
(2,'Classic Leather','Timeless casual shoes','Reebok','sneakers','US 7-12','White',28,85.00,55.00,'classic-leather.jpg','2023-06-02 09:15:00','2023-06-14 16:45:00'),
(3,'Chelsea Boots','Premium leather ankle boots','Clarks','boots','US 8-11','Brown',18,150.00,100.00,'chelsea-boots.jpg','2023-05-15 10:30:00','2023-06-10 11:20:00'),
(4,'Running Pro','High-performance running shoes','Adidas','athletic','US 5-12','Black/Red',32,110.00,75.00,'running-pro.jpg','2023-06-05 11:45:00','2023-06-12 09:10:00'),
(5,'Formal Oxford','Elegant dress shoes for formal occasions','Cole Haan','formal','US 7-13','Black',15,175.00,120.00,'formal-oxford.jpg','2023-05-20 14:20:00','2023-06-08 10:15:00'),
(6,'Beach Sandals','Comfortable waterproof sandals','Teva','sandals','US 5-11','Blue',22,45.00,30.00,'beach-sandals.jpg','2023-06-10 13:10:00','2023-06-13 15:30:00'),
(7,'Basketball Elite','High-top basketball shoes','Nike','athletic','US 8-13','White/Black',12,140.00,95.00,'basketball-elite.jpg','2023-05-25 16:40:00','2023-06-09 14:25:00'),
(8,'Hiking Boots','Durable boots for outdoor adventures','Merrell','boots','US 7-12','Brown/Gray',8,160.00,110.00,'hiking-boots.jpg','2023-06-08 10:50:00','2023-06-14 17:40:00'),
(9,'Slip-On Loafers','Comfortable casual loafers','Skechers','sneakers','US 6-11','Black',25,65.00,45.00,'slip-on-loafers.jpg','2023-06-12 09:30:00','2023-06-15 10:20:00'),
(10,'Dress Sandals','Elegant women\'s sandals','Naturalizer','sandals','US 5-10','Beige',14,95.00,65.00,'dress-sandals.jpg','2023-06-03 14:15:00','2023-06-11 13:50:00');
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `inventory_id` int DEFAULT NULL,
  `quantity_sold` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) GENERATED ALWAYS AS ((`quantity_sold` * `unit_price`)) STORED,
  `discount` decimal(10,2) DEFAULT '0.00',
  `payment_method` enum('cash','card','mobile_money') DEFAULT 'cash',
  `customer_info` json DEFAULT NULL,
  `sale_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `inventory_id` (`inventory_id`),
  KEY `idx_sale_time` (`sale_time`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` (`id`, `user_id`, `inventory_id`, `quantity_sold`, `unit_price`, `discount`, `payment_method`, `customer_info`, `sale_time`) VALUES 
(1,1,1,2,120.00,0.00,'card','{\"name\": \"John Smith\", \"contact\": \"john.smith@example.com\"}','2023-06-10 09:15:00'),
(2,2,2,1,85.00,5.00,'cash','{\"name\": \"Sarah Johnson\", \"contact\": \"555-123-4567\"}','2023-06-10 10:30:00'),
(3,1,3,1,150.00,10.00,'mobile_money','{\"name\": \"Michael Brown\", \"contact\": \"michael.b@example.com\"}','2023-06-10 11:45:00'),
(4,2,4,1,110.00,0.00,'card','{\"name\": \"Emily Davis\", \"contact\": \"555-987-6543\"}','2023-06-10 13:20:00'),
(5,1,1,1,120.00,0.00,'cash','{\"name\": \"Robert Wilson\", \"contact\": \"robert.w@example.com\"}','2023-06-11 10:15:00'),
(6,2,5,1,175.00,15.00,'card','{\"name\": \"Jennifer Lee\", \"contact\": \"jen.lee@example.com\"}','2023-06-11 11:30:00'),
(7,1,6,2,45.00,0.00,'cash','{\"name\": \"David Miller\", \"contact\": \"555-456-7890\"}','2023-06-11 14:45:00'),
(8,2,7,1,140.00,0.00,'mobile_money','{\"name\": \"Lisa Taylor\", \"contact\": \"lisa.t@example.com\"}','2023-06-12 09:30:00'),
(9,1,8,1,160.00,20.00,'card','{\"name\": \"James Anderson\", \"contact\": \"555-789-0123\"}','2023-06-12 11:15:00'),
(10,2,9,1,65.00,0.00,'cash','{\"name\": \"Patricia White\", \"contact\": \"patricia.w@example.com\"}','2023-06-12 14:00:00'),
(11,1,10,1,95.00,0.00,'card','{\"name\": \"Daniel Martinez\", \"contact\": \"555-234-5678\"}','2023-06-13 10:45:00'),
(12,2,1,1,120.00,0.00,'cash','{\"name\": \"Nancy Garcia\", \"contact\": \"nancy.g@example.com\"}','2023-06-13 12:30:00'),
(13,1,2,1,85.00,5.00,'mobile_money','{\"name\": \"Kevin Robinson\", \"contact\": \"555-345-6789\"}','2023-06-13 15:15:00'),
(14,2,3,1,150.00,0.00,'card','{\"name\": \"Karen Clark\", \"contact\": \"karen.c@example.com\"}','2023-06-14 09:45:00'),
(15,1,4,1,110.00,0.00,'cash','{\"name\": \"Thomas Rodriguez\", \"contact\": \"555-456-7890\"}','2023-06-14 11:30:00'),
(16,2,5,1,175.00,10.00,'card','{\"name\": \"Donna Hernandez\", \"contact\": \"donna.h@example.com\"}','2023-06-14 14:15:00'),
(17,1,6,1,45.00,0.00,'mobile_money','{\"name\": \"Paul Young\", \"contact\": \"paul.y@example.com\"}','2023-06-15 10:00:00'),
(18,2,7,1,140.00,0.00,'cash','{\"name\": \"Michelle King\", \"contact\": \"555-567-8901\"}','2023-06-15 12:45:00'),
(19,1,8,1,160.00,15.00,'card','{\"name\": \"Christopher Wright\", \"contact\": \"chris.w@example.com\"}','2023-06-15 14:30:00'),
(20,2,9,1,65.00,0.00,'mobile_money','{\"name\": \"Amanda Scott\", \"contact\": \"amanda.s@example.com\"}','2023-06-15 16:15:00');
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('owner','attendant') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'attendant',
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES 
(1,'admin','$2y$10$N9qo8uLOickgx2ZMRZoMy.MrYV7ZR3XW7xP7t7e5J5J5v5J5v5J5v','owner','System Administrator','admin@shoeshop.com','2023-06-15 16:30:00','2023-05-01 00:00:00','2023-06-15 16:30:00'),
(2,'john.doe','$2y$10$N9qo8uLOickgx2ZMRZoMy.MrYV7ZR3XW7xP7t7e5J5J5v5J5v5J5v','attendant','John Doe','john.doe@shoeshop.com','2023-06-15 15:45:00','2023-05-05 09:15:00','2023-06-15 15:45:00'),
(3,'jane.smith','$2y$10$N9qo8uLOickgx2ZMRZoMy.MrYV7ZR3XW7xP7t7e5J5J5v5J5v5J5v','attendant','Jane Smith','jane.smith@shoeshop.com','2023-06-14 17:20:00','2023-05-10 14:30:00','2023-06-14 17:20:00'),
(4,'mike.johnson','$2y$10$N9qo8uLOickgx2ZMRZoMy.MrYV7ZR3XW7xP7t7e5J5J5v5J5v5J5v','attendant','Mike Johnson','mike.j@shoeshop.com','2023-06-13 16:10:00','2023-05-15 11:45:00','2023-06-13 16:10:00');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `inventory_view`
--

DROP TABLE IF EXISTS `inventory_view`;
/*!50001 DROP VIEW IF EXISTS `inventory_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `inventory_view` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `brand`,
 1 AS `type`,
 1 AS `quantity`,
 1 AS `price`,
 1 AS `status`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `sales_summary`
--

DROP TABLE IF EXISTS `sales_summary`;
/*!50001 DROP VIEW IF EXISTS `sales_summary`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `sales_summary` AS SELECT 
 1 AS `sale_date`,
 1 AS `total_sales`,
 1 AS `total_revenue`,
 1 AS `total_items_sold`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `inventory_view`
--

/*!50001 DROP VIEW IF EXISTS `inventory_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `inventory_view` AS select `i`.`id` AS `id`,`i`.`name` AS `name`,`i`.`brand` AS `brand`,`i`.`type` AS `type`,`i`.`quantity` AS `quantity`,`i`.`price` AS `price`,(case when (`i`.`quantity` = 0) then 'Out of Stock' when (`i`.`quantity` < 10) then 'Low Stock' else 'In Stock' end) AS `status` from `inventory` `i` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `sales_summary`
--

/*!50001 DROP VIEW IF EXISTS `sales_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `sales_summary` AS select cast(`s`.`sale_time` as date) AS `sale_date`,count(0) AS `total_sales`,sum(`s`.`total_amount`) AS `total_revenue`,sum(`s`.`quantity_sold`) AS `total_items_sold` from `sales` `s` group by cast(`s`.`sale_time` as date) order by cast(`s`.`sale_time` as date) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Dumping events for database 'shoe_shop_db'
--

--
-- Dumping routines for database 'shoe_shop_db'
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-06-15 18:45:02