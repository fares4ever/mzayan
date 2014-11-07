CREATE DATABASE  IF NOT EXISTS `mzayan` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `mzayan`;
-- MySQL dump 10.13  Distrib 5.6.19, for osx10.7 (i386)
--
-- Host: 127.0.0.1    Database: mzayan
-- ------------------------------------------------------
-- Server version	5.5.38

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
-- Table structure for table `mz_age`
--

DROP TABLE IF EXISTS `mz_age`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mz_age` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `aname` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mz_age`
--

LOCK TABLES `mz_age` WRITE;
/*!40000 ALTER TABLE `mz_age` DISABLE KEYS */;
INSERT INTO `mz_age` VALUES (1,'قعود'),(2,'بكرة'),(3,'مفرود'),(4,'مفرودة'),(5,'لقي'),(6,'لقية'),(7,'حق'),(8,'حقة'),(9,'جذع'),(10,'جذعة'),(11,'ثني'),(12,'ثنية'),(13,'رباع'),(14,'رباعية'),(15,'سديس'),(16,'سدسة'),(17,'هرش'),(18,'فاطر');
/*!40000 ALTER TABLE `mz_age` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mz_city`
--

DROP TABLE IF EXISTS `mz_city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mz_city` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mz_city`
--

LOCK TABLES `mz_city` WRITE;
/*!40000 ALTER TABLE `mz_city` DISABLE KEYS */;
INSERT INTO `mz_city` VALUES (1,'حفر الباطن');
/*!40000 ALTER TABLE `mz_city` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mz_color`
--

DROP TABLE IF EXISTS `mz_color`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mz_color` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mz_color`
--

LOCK TABLES `mz_color` WRITE;
/*!40000 ALTER TABLE `mz_color` DISABLE KEYS */;
INSERT INTO `mz_color` VALUES (1,'الوضح (المغاتير)'),(2,'الصفر'),(3,'الشعل'),(4,'المجاهيم'),(5,'الحمر'),(6,'الشقح');
/*!40000 ALTER TABLE `mz_color` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mz_following`
--

DROP TABLE IF EXISTS `mz_following`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mz_following` (
  `uid` int(11) NOT NULL,
  `f_uid` int(11) NOT NULL,
  KEY `uid_index` (`uid`),
  KEY `f_uid_index` (`f_uid`),
  CONSTRAINT `FollowedUser_FK` FOREIGN KEY (`f_uid`) REFERENCES `mz_users` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `follwingUser_FK` FOREIGN KEY (`uid`) REFERENCES `mz_users` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mz_following`
--

LOCK TABLES `mz_following` WRITE;
/*!40000 ALTER TABLE `mz_following` DISABLE KEYS */;
INSERT INTO `mz_following` VALUES (7,8),(7,8),(7,8),(5,7);
/*!40000 ALTER TABLE `mz_following` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mz_post`
--

DROP TABLE IF EXISTS `mz_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mz_post` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `ptitle` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `pimage` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `pdesc` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pmobile` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pcity` int(11) DEFAULT NULL,
  `pcolor` int(11) DEFAULT NULL,
  `psection` int(11) DEFAULT NULL,
  `page` int(11) DEFAULT NULL,
  `pdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `forsale` bit(1) NOT NULL DEFAULT b'0',
  `pviewcount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pid`),
  KEY `color_FK_idx` (`pcolor`),
  KEY `section_FK_idx` (`psection`),
  KEY `city_FK_idx` (`pcity`),
  KEY `age_FK_idx` (`page`),
  KEY `postuser_FK_idx` (`uid`),
  CONSTRAINT `age_FK` FOREIGN KEY (`page`) REFERENCES `mz_age` (`aid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `city_FK` FOREIGN KEY (`pcity`) REFERENCES `mz_city` (`cid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `color_FK` FOREIGN KEY (`pcolor`) REFERENCES `mz_color` (`cid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `postuser_FK` FOREIGN KEY (`uid`) REFERENCES `mz_users` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `section_FK` FOREIGN KEY (`psection`) REFERENCES `mz_section` (`sid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mz_post`
--

LOCK TABLES `mz_post` WRITE;
/*!40000 ALTER TABLE `mz_post` DISABLE KEYS */;
INSERT INTO `mz_post` VALUES (1,7,'Big 7ashi','big','Big 7ashi desc','555555555',NULL,1,1,1,'2014-11-05 14:16:46','\0',0),(2,8,'Small ','small','Small 7ashi Describtion','66666666',NULL,3,3,3,'2014-11-05 14:17:42','\0',0),(4,7,'الطيب','يب','الطيب غني عن التعريف','99999999',NULL,5,4,5,'2014-11-06 20:19:02','',0),(5,7,'from chrome','123',NULL,NULL,NULL,NULL,NULL,NULL,'2014-11-06 20:34:54','\0',0),(6,7,'from chrome2','12345','test 2222',NULL,NULL,NULL,NULL,NULL,'2014-11-06 20:35:31','\0',0),(7,7,'from chrome2','12345','test 2222','5656565656',NULL,NULL,NULL,NULL,'2014-11-06 20:35:54','\0',0),(9,7,'from chrome2','12345','test 2222','5656565656',NULL,1,NULL,NULL,'2014-11-06 20:36:24','\0',0),(10,7,'from chrome2','12345','test 2222','5656565656',NULL,1,NULL,1,'2014-11-06 20:36:36','\0',0),(11,7,'from chrome2','12345','test 2222','5656565656',NULL,1,1,1,'2014-11-06 20:36:46','\0',0),(12,7,'from chrome2','12345','test 2222','5656565656',NULL,1,1,1,'2014-11-06 20:38:19','',0),(13,7,'from chrome2','12345','test 2222','5656565656',1,1,1,1,'2014-11-06 20:39:11','',0);
/*!40000 ALTER TABLE `mz_post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mz_post_comment`
--

DROP TABLE IF EXISTS `mz_post_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mz_post_comment` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `comment` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `c_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `price` double DEFAULT NULL,
  PRIMARY KEY (`cid`),
  KEY `pid_index` (`pid`),
  KEY `commentUser_FK_idx` (`uid`),
  CONSTRAINT `commentUser_FK` FOREIGN KEY (`uid`) REFERENCES `mz_users` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `postid_fk` FOREIGN KEY (`pid`) REFERENCES `mz_post` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mz_post_comment`
--

LOCK TABLES `mz_post_comment` WRITE;
/*!40000 ALTER TABLE `mz_post_comment` DISABLE KEYS */;
INSERT INTO `mz_post_comment` VALUES (1,1,8,'funny chick','2014-11-05 14:20:28',5000),(2,1,9,'hot','2014-11-05 14:20:28',NULL),(3,1,7,'','2014-11-06 21:49:37',NULL),(4,1,7,'dsadsa','2014-11-06 21:54:08',0),(5,1,7,'dsadsa','2014-11-06 21:55:06',0),(6,1,7,'dsadsa','2014-11-06 21:55:51',NULL);
/*!40000 ALTER TABLE `mz_post_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mz_post_like`
--

DROP TABLE IF EXISTS `mz_post_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mz_post_like` (
  `lid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`lid`),
  KEY `likeUser_FK_idx` (`uid`),
  KEY `likePost_FK_idx` (`pid`),
  CONSTRAINT `likePost_FK` FOREIGN KEY (`pid`) REFERENCES `mz_post` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `likeUser_FK` FOREIGN KEY (`uid`) REFERENCES `mz_users` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mz_post_like`
--

LOCK TABLES `mz_post_like` WRITE;
/*!40000 ALTER TABLE `mz_post_like` DISABLE KEYS */;
INSERT INTO `mz_post_like` VALUES (3,1,8),(4,1,3),(9,2,7),(10,2,8),(17,1,5),(19,1,2);
/*!40000 ALTER TABLE `mz_post_like` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mz_section`
--

DROP TABLE IF EXISTS `mz_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mz_section` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `sname` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mz_section`
--

LOCK TABLES `mz_section` WRITE;
/*!40000 ALTER TABLE `mz_section` DISABLE KEYS */;
INSERT INTO `mz_section` VALUES (1,'???'),(2,'????'),(3,'???'),(4,'??????');
/*!40000 ALTER TABLE `mz_section` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mz_userInfo`
--

DROP TABLE IF EXISTS `mz_userInfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mz_userInfo` (
  `uid` int(11) NOT NULL,
  `displayname` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `ustatus` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uavatar` blob,
  `mobile` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `age` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `regist_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`),
  CONSTRAINT `user_FK` FOREIGN KEY (`uid`) REFERENCES `mz_users` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mz_userInfo`
--

LOCK TABLES `mz_userInfo` WRITE;
/*!40000 ALTER TABLE `mz_userInfo` DISABLE KEYS */;
INSERT INTO `mz_userInfo` VALUES (2,'الثاني','الفضية',NULL,'22222222','22','2014-11-07 16:42:20'),(3,'الثالث','الرونزية',NULL,'٣٣٣٣٣٣٣٣','33','2014-11-07 16:42:20'),(5,'الخامس','عشق الخامس',NULL,'٥٥٥٥٥٥٥٥',NULL,'2014-11-07 16:06:44'),(6,'the sixth','stranger',NULL,'6666666','66','2014-11-07 16:42:20'),(7,'fares','7',NULL,'777777777','27','2014-11-02 19:48:06'),(8,'الفارس ','الحب قطع قلوب البعارين',NULL,'٨٨٨٨٨٨٨٨٨','87','2014-11-07 16:05:19');
/*!40000 ALTER TABLE `mz_userInfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mz_users`
--

DROP TABLE IF EXISTS `mz_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mz_users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mz_users`
--

LOCK TABLES `mz_users` WRITE;
/*!40000 ALTER TABLE `mz_users` DISABLE KEYS */;
INSERT INTO `mz_users` VALUES (1,'test','test'),(2,'fares','faresPassword'),(3,'from rest client','fromrestclient'),(5,'from rest client1','fromrestclient'),(6,'from rest client2','fromrestclient'),(7,'from@client.com','fromrestclient'),(8,'from1@client.com','fromrestclient'),(9,'fares4ever@gmail.com','dasdasdas');
/*!40000 ALTER TABLE `mz_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_access_tokens`
--

LOCK TABLES `oauth_access_tokens` WRITE;
/*!40000 ALTER TABLE `oauth_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_authorization_codes`
--

DROP TABLE IF EXISTS `oauth_authorization_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`authorization_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_authorization_codes`
--

LOCK TABLES `oauth_authorization_codes` WRITE;
/*!40000 ALTER TABLE `oauth_authorization_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_authorization_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_clients` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) NOT NULL,
  `redirect_uri` varchar(2000) NOT NULL,
  `grant_types` varchar(80) DEFAULT NULL,
  `scope` varchar(100) DEFAULT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_clients`
--

LOCK TABLES `oauth_clients` WRITE;
/*!40000 ALTER TABLE `oauth_clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_jwt`
--

DROP TABLE IF EXISTS `oauth_jwt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_jwt`
--

LOCK TABLES `oauth_jwt` WRITE;
/*!40000 ALTER TABLE `oauth_jwt` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_jwt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_refresh_tokens`
--

LOCK TABLES `oauth_refresh_tokens` WRITE;
/*!40000 ALTER TABLE `oauth_refresh_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_refresh_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_scopes`
--

DROP TABLE IF EXISTS `oauth_scopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_scopes` (
  `scope` text,
  `is_default` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_scopes`
--

LOCK TABLES `oauth_scopes` WRITE;
/*!40000 ALTER TABLE `oauth_scopes` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_scopes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_users`
--

DROP TABLE IF EXISTS `oauth_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(2000) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_users`
--

LOCK TABLES `oauth_users` WRITE;
/*!40000 ALTER TABLE `oauth_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-11-07 22:16:10
