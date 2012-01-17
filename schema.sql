-- MySQL dump 10.13  Distrib 5.1.58, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ihtfp
-- ------------------------------------------------------
-- Server version	5.1.58-1ubuntu1

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
-- Table structure for table `answer_attempts`
--

DROP TABLE IF EXISTS `answer_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `answer_attempts` (
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `answer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `answers`
--

DROP TABLE IF EXISTS `answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `answers` (
  `aid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Answer ID',
  `answer` varchar(255) NOT NULL COMMENT 'Answer Text',
  `pid` int(11) DEFAULT NULL,
  `batch` int(11) DEFAULT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB AUTO_INCREMENT=10063 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `answers_rounds`
--

DROP TABLE IF EXISTS `answers_rounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `answers_rounds` (
  `aid` int(11) NOT NULL COMMENT 'Answer ID',
  `rid` int(11) NOT NULL COMMENT 'Round ID',
  PRIMARY KEY (`aid`,`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `authors`
--

DROP TABLE IF EXISTS `authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authors` (
  `pid` int(11) NOT NULL COMMENT 'Puzzle ID',
  `uid` int(11) NOT NULL COMMENT 'Author User ID',
  PRIMARY KEY (`pid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comment_type`
--

DROP TABLE IF EXISTS `comment_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Comment ID',
  `uid` int(11) NOT NULL COMMENT 'User ID',
  `comment` text NOT NULL COMMENT 'Comment',
  `type` int(11) NOT NULL COMMENT 'Comment Type',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `pid` int(11) NOT NULL COMMENT 'Puzzle ID',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`,`timestamp`)
) ENGINE=InnoDB AUTO_INCREMENT=37927 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `doneTesting`
--

DROP TABLE IF EXISTS `doneTesting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doneTesting` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`,`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `editor_queue`
--

DROP TABLE IF EXISTS `editor_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `editor_queue` (
  `uid` int(11) NOT NULL COMMENT 'User ID of Editor',
  `pid` int(11) NOT NULL COMMENT 'Puzzle ID',
  PRIMARY KEY (`pid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_sub`
--

DROP TABLE IF EXISTS `email_sub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_sub` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `uid` int(11) NOT NULL,
  `jid` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`jid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `last_visit`
--

DROP TABLE IF EXISTS `last_visit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `last_visit` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`uid`,`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ponies`
--

DROP TABLE IF EXISTS `ponies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ponies` (
  `aid` int(11) DEFAULT NULL,
  `name` varchar(28) DEFAULT NULL,
  KEY `pony_index` (`aid`),
  KEY `pony_name_index` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `priv`
--

DROP TABLE IF EXISTS `priv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `priv` (
  `jid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `seeAllPuzzles` tinyint(1) NOT NULL,
  `addToEditingQueue` tinyint(1) NOT NULL,
  `changeServer` tinyint(1) NOT NULL,
  `canEditAll` tinyint(1) NOT NULL,
  `seeTesters` tinyint(4) NOT NULL,
  `isBlind` tinyint(1) NOT NULL,
  `isLurker` tinyint(1) NOT NULL,
  `factcheck` tinyint(1) NOT NULL,
  `changeStatus` tinyint(1) NOT NULL,
  PRIMARY KEY (`jid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pstatus`
--

DROP TABLE IF EXISTS `pstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `ord` int(11) NOT NULL,
  `inTesting` tinyint(1) NOT NULL,
  `addToEditorQueue` tinyint(1) NOT NULL,
  `acceptDrafts` tinyint(1) NOT NULL,
  `addToTestAdminQueue` tinyint(1) NOT NULL,
  `needsFactcheck` tinyint(1) NOT NULL,
  `postprod` tinyint(1) NOT NULL,
  `finalFactcheck` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order` (`ord`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `puzzle_idea`
--

DROP TABLE IF EXISTS `puzzle_idea`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `puzzle_idea` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `summary` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `pstatus` int(11) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL DEFAULT '',
  `update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notes` varchar(512) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pstatus` (`pstatus`)
) ENGINE=InnoDB AUTO_INCREMENT=373 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `puzzle_tester_count`
--

DROP TABLE IF EXISTS `puzzle_tester_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `puzzle_tester_count` (
  `pid` int(11) NOT NULL,
  `tester_count` int(11) NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rounds`
--

DROP TABLE IF EXISTS `rounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rounds` (
  `rid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Round ID',
  `name` varchar(32) NOT NULL COMMENT 'Name of Round',
  `display` tinyint(1) NOT NULL COMMENT 'Display Round?',
  `answer` varchar(255) NOT NULL COMMENT 'Answer of Round Meta',
  `unlock_at` double DEFAULT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB AUTO_INCREMENT=4102 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spoiled`
--

DROP TABLE IF EXISTS `spoiled`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spoiled` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  PRIMARY KEY (`pid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testAdminQueue`
--

DROP TABLE IF EXISTS `testAdminQueue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testAdminQueue` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_call`
--

DROP TABLE IF EXISTS `test_call`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_call` (
  `pid` int(11) NOT NULL COMMENT 'Puzzle ID',
  `on_call` int(11) NOT NULL COMMENT 'User ID of Editor on Call',
  `put_by` int(11) NOT NULL COMMENT 'User ID who Put Editor on Call',
  PRIMARY KEY (`pid`,`on_call`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_queue`
--

DROP TABLE IF EXISTS `test_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_queue` (
  `uid` int(11) NOT NULL COMMENT 'User ID of Tester',
  `pid` int(11) NOT NULL COMMENT 'Puzzle ID',
  PRIMARY KEY (`uid`,`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testing_feedback`
--

DROP TABLE IF EXISTS `testing_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testing_feedback` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `how_long` varchar(255) DEFAULT NULL,
  `tried` text,
  `liked` text,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `when_return` text,
  PRIMARY KEY (`uid`,`pid`,`time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uploaded_files`
--

DROP TABLE IF EXISTS `uploaded_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploaded_files` (
  `filename` varchar(255) NOT NULL,
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('draft','solution','misc','postprod') NOT NULL,
  PRIMARY KEY (`filename`),
  KEY `pid` (`pid`,`type`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_info`
--

DROP TABLE IF EXISTS `user_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_info` (
  `uid` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` blob NOT NULL COMMENT 'AES_ENCRYPT(''password'',''usernamepassword'')',
  `email` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=243 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_info_key`
--

DROP TABLE IF EXISTS `user_info_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_info_key` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shortname` varchar(255) NOT NULL,
  `longname` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_info_values`
--

DROP TABLE IF EXISTS `user_info_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_info_values` (
  `person_id` int(11) NOT NULL,
  `user_info_key_id` int(11) NOT NULL,
  `value` varchar(511) DEFAULT NULL,
  KEY `person_id` (`person_id`),
  KEY `user_info_key_id` (`user_info_key_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-01-16 22:48:44


LOCK TABLES `comment_type` WRITE;
/*!40000 ALTER TABLE `comment_type` DISABLE KEYS */;
INSERT INTO `comment_type` VALUES (1,'Author'),(2,'Editor'),(4,'Lurker'),(8,'PostProdRobot'),(3,'Server'),(7,'TestingAdmin'),(5,'Testsolver'),(6,'Unknown');
/*!40000 ALTER TABLE `comment_type` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `priv` WRITE;
/*!40000 ALTER TABLE `priv` DISABLE KEYS */;
INSERT INTO `priv` VALUES (1,'Chief Producer',1,0,0,1,0,0,1,1,1),(2,'Puzzle Herder',1,0,0,0,0,0,1,0,0),(3,'Chief Puzzle Editor',1,1,0,1,0,0,1,1,1),(4,'Puzzle Editor',0,1,0,0,0,0,1,1,0),(6,'Chief Testing Admin',1,0,0,0,1,0,1,1,1),(7,'Producer',1,0,0,0,0,0,1,1,0),(8,'Server Admin',0,0,1,0,0,0,0,0,0),(12,'Fact Checker',0,0,0,0,0,0,1,1,0),(13,'Testing Admin',0,0,0,0,1,0,1,0,0);
/*!40000 ALTER TABLE `priv` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `user_info_key` WRITE;
/*!40000 ALTER TABLE `user_info_key` DISABLE KEYS */;
INSERT INTO `user_info_key` VALUES (3,'location','Location'),(4,'phone','Phone Number'),(5,'jabber','Jabber server nickname'),(6,'expertise','What are your areas of interest and/or expertise?'),(7,'favorite','What are your favorite puzzle types?'),(8,'bio','Short bio');
/*!40000 ALTER TABLE `user_info_key` ENABLE KEYS */;
UNLOCK TABLES;
