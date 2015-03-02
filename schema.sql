-- MySQL dump 10.13  Distrib 5.5.34, for debian-linux-gnu (x86_64)
--
-- Host: series-of-tubes.wind-up-birds.org    Database: puzzletron
-- ------------------------------------------------------
-- Server version	5.5.31-0ubuntu0.12.04.2

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answer_attempts`
--

LOCK TABLES `answer_attempts` WRITE;
/*!40000 ALTER TABLE `answer_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `answer_attempts` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answers`
--

LOCK TABLES `answers` WRITE;
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `answer_round`
--

DROP TABLE IF EXISTS `answer_round`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `answer_round` (
  `aid` int(11) NOT NULL COMMENT 'Answer ID',
  `rid` int(11) NOT NULL COMMENT 'Round ID',
  PRIMARY KEY (`aid`,`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answer_round`
--

LOCK TABLES `answer_round` WRITE;
/*!40000 ALTER TABLE `answer_round` DISABLE KEYS */;
/*!40000 ALTER TABLE `answer_round` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `author_links`
--

DROP TABLE IF EXISTS `author_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `author_links` (
  `pid` int(11) NOT NULL COMMENT 'Puzzle ID',
  `uid` int(11) NOT NULL COMMENT 'Author User ID',
  PRIMARY KEY (`pid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `author_links`
--

LOCK TABLES `author_links` WRITE;
/*!40000 ALTER TABLE `author_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `author_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment_types`
--

DROP TABLE IF EXISTS `comment_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment_types`
--

LOCK TABLES `comment_types` WRITE;
/*!40000 ALTER TABLE `comment_types` DISABLE KEYS */;
INSERT INTO `comment_types` VALUES (3,'Author'),(4,'Discuss Editor'),(7,'Lurker'),(1,'Server'),(6,'TestingAdmin'),(2,'Testsolver'),(8,'Unknown'),(5,'EIC'),(9,'Approver'),(10,'Cohesion'),(11,'Director'),(12,'Factchecker');
/*!40000 ALTER TABLE `comment_types` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `former_tester_links`
--

DROP TABLE IF EXISTS `former_tester_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `former_tester_links` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`,`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `former_tester_links`
--

LOCK TABLES `former_tester_links` WRITE;
/*!40000 ALTER TABLE `former_tester_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `former_tester_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `editor_links`
--

DROP TABLE IF EXISTS `editor_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `editor_links` (
  `uid` int(11) NOT NULL COMMENT 'User ID of Editor',
  `pid` int(11) NOT NULL COMMENT 'Puzzle ID',
  PRIMARY KEY (`pid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `editor_links`
--

LOCK TABLES `editor_links` WRITE;
/*!40000 ALTER TABLE `editor_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `editor_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_outbox`
--

DROP TABLE IF EXISTS `email_outbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_outbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60919 DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_outbox`
--

LOCK TABLES `email_outbox` WRITE;
/*!40000 ALTER TABLE `email_outbox` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_outbox` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriber_links`
--

DROP TABLE IF EXISTS `subscriber_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriber_links` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriber_links`
--

LOCK TABLES `subscriber_links` WRITE;
/*!40000 ALTER TABLE `subscriber_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriber_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factchecker_links`
--

DROP TABLE IF EXISTS `factchecker_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `factchecker_links` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  KEY `uid` (`uid`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factchecker_links`
--

LOCK TABLES `factchecker_links` WRITE;
/*!40000 ALTER TABLE `factchecker_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `factchecker_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `uid` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitor_links`
--

DROP TABLE IF EXISTS `visitor_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitor_links` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`uid`,`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitor_links`
--

LOCK TABLES `visitor_links` WRITE;
/*!40000 ALTER TABLE `visitor_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `visitor_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flagger_links`
--

DROP TABLE IF EXISTS `flagger_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flagger_links` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `flag` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`uid`,`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flagger_links`
--

LOCK TABLES `flagger_links` WRITE;
/*!40000 ALTER TABLE `flagger_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `flagger_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `motd`
--

DROP TABLE IF EXISTS `motd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `motd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` text NOT NULL,
  `uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `motd`
--

LOCK TABLES `motd` WRITE;
/*!40000 ALTER TABLE `motd` DISABLE KEYS */;
/*!40000 ALTER TABLE `motd` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `seeAllPuzzles` tinyint(1) NOT NULL,
  `becomeEditor` tinyint(1) NOT NULL,
  `administerServer` tinyint(1) NOT NULL,
  `changeAnswers` tinyint(1) NOT NULL,
  `beTestAdmin` tinyint(1) NOT NULL,
  `beBlind` tinyint(1) NOT NULL,
  `beLurker` tinyint(1) NOT NULL,
  `changePuzzleStatus` tinyint(1) NOT NULL,
  `autoSubWhenEditing` tinyint(1) NOT NULL,
  `becomeRoundCaptain` tinyint(1) NOT NULL,
  `becomeApprover` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (2,'Puzzle Herder',1,0,0,0,0,0,1,0,0,0,0),(3,'Director',1,1,1,1,1,0,1,1,1,1,1),(4,'Discussion Editor',0,1,0,0,0,0,1,1,1,0,0),(6,'Chief Testing Admin',1,0,0,0,1,0,1,1,0,0,0),(7,'Producer',1,0,0,0,0,0,1,0,0,1,0),(8,'Server Admin',1,0,1,0,1,0,1,1,0,0,0),(9,'Editor in Chief',1,1,0,1,1,0,1,1,1,1,1),(10,'Server Maintainer',0,0,1,0,0,0,1,0,0,0,0),(13,'Testing Admin',0,0,0,0,1,0,1,0,0,0,0),(14,'Approval Editor',0,1,0,0,0,0,1,1,1,0,1),(15,'Cohesion Editor',1,1,0,1,0,0,1,1,1,0,1);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pstatus`
--

LOCK TABLES `pstatus` WRITE;
/*!40000 ALTER TABLE `pstatus` DISABLE KEYS */;
INSERT INTO `pstatus` VALUES (1,'Initial Idea',1,0,1,1,0,0,0,0),(2,'In Testing',3,1,1,0,1,0,0,0),(3,'Awaiting Final Test Approval',4,0,0,0,1,0,0,0),(4,'Needs Fact Check',5,0,0,0,0,0,0,0),(5,'Post Production',6,0,0,0,0,0,1,0),(6,'Awaiting Final Approval',7,0,0,0,0,0,1,1),(7,'Done',8,0,0,0,0,0,0,0),(8,'Dead',9,0,0,0,0,0,0,0),(10,'In Revision',10,0,1,1,0,0,0,0),(22,'Writing (Answer Assigned)',2,0,1,1,0,0,0,0);
/*!40000 ALTER TABLE `pstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approver_links`
--

DROP TABLE IF EXISTS `approver_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approver_links` (
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `approve` smallint(6) NOT NULL,
  PRIMARY KEY (`pid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approver_links`
--

LOCK TABLES `approver_links` WRITE;
/*!40000 ALTER TABLE `approver_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `approver_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `puzzles`
--

DROP TABLE IF EXISTS `puzzles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `puzzles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `summary` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `pstatus` int(11) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL DEFAULT '',
  `update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notes` varchar(512) NOT NULL DEFAULT '',
  `editor_notes` varchar(512) NOT NULL DEFAULT '',
  `wikipage` varchar(512) NOT NULL DEFAULT '',
  `credits` varchar(255) NOT NULL DEFAULT '',
  `runtime_info` varchar(255) NOT NULL DEFAULT '',
  `needed_editors` int(11) NOT NULL DEFAULT '2',
  `priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pstatus` (`pstatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `puzzles`
--

LOCK TABLES `puzzles` WRITE;
/*!40000 ALTER TABLE `puzzles` DISABLE KEYS */;
/*!40000 ALTER TABLE `puzzles` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `puzzle_tester_count`
--

LOCK TABLES `puzzle_tester_count` WRITE;
/*!40000 ALTER TABLE `puzzle_tester_count` DISABLE KEYS */;
/*!40000 ALTER TABLE `puzzle_tester_count` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rounds`
--

DROP TABLE IF EXISTS `rounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rounds` (
  `rid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Round ID',
  `name` varchar(32) NOT NULL COMMENT 'Name of Round',
  `display` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Display Round?',
  `answer` varchar(255) NOT NULL COMMENT 'Answer of Round Meta',
  `unlock_at` double DEFAULT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rounds`
--

LOCK TABLES `rounds` WRITE;
/*!40000 ALTER TABLE `rounds` DISABLE KEYS */;
/*!40000 ALTER TABLE `rounds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spoiled_user_links`
--

DROP TABLE IF EXISTS `spoiled_user_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spoiled_user_links` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  PRIMARY KEY (`pid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spoiled_user_links`
--

LOCK TABLES `spoiled_user_links` WRITE;
/*!40000 ALTER TABLE `spoiled_user_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `spoiled_user_links` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testAdminQueue`
--

LOCK TABLES `testAdminQueue` WRITE;
/*!40000 ALTER TABLE `testAdminQueue` DISABLE KEYS */;
/*!40000 ALTER TABLE `testAdminQueue` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_call`
--

LOCK TABLES `test_call` WRITE;
/*!40000 ALTER TABLE `test_call` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_call` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_queue`
--

LOCK TABLES `test_queue` WRITE;
/*!40000 ALTER TABLE `test_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testing_feedback`
--

DROP TABLE IF EXISTS `testing_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testing_feedback` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `done` tinyint(11) NOT NULL DEFAULT '0',
  `how_long` varchar(255) DEFAULT NULL,
  `tried` text,
  `liked` text,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `breakthrough` varchar(255) DEFAULT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `fun` int(11) NOT NULL DEFAULT '0',
  `difficulty` int(11) NOT NULL DEFAULT '0',
  `when_return` text,
  PRIMARY KEY (`uid`,`pid`,`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testing_feedback`
--

LOCK TABLES `testing_feedback` WRITE;
/*!40000 ALTER TABLE `testing_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `testing_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testsolve_requests`
--

DROP TABLE IF EXISTS `testsolve_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testsolve_requests` (
  `pid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `done` tinyint(1) DEFAULT '0',
  `notes` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testsolve_requests`
--

LOCK TABLES `testsolve_requests` WRITE;
/*!40000 ALTER TABLE `testsolve_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `testsolve_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testsolve_team`
--

DROP TABLE IF EXISTS `testsolve_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testsolve_team` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testsolve_team`
--

LOCK TABLES `testsolve_team` WRITE;
/*!40000 ALTER TABLE `testsolve_team` DISABLE KEYS */;
/*!40000 ALTER TABLE `testsolve_team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testsolve_team_queue`
--

DROP TABLE IF EXISTS `testsolve_team_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testsolve_team_queue` (
  `tid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  PRIMARY KEY (`pid`),
  KEY `tid` (`tid`,`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testsolve_team_queue`
--

LOCK TABLES `testsolve_team_queue` WRITE;
/*!40000 ALTER TABLE `testsolve_team_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `testsolve_team_queue` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploaded_files`
--

LOCK TABLES `uploaded_files` WRITE;
/*!40000 ALTER TABLE `uploaded_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploaded_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `uid` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` blob NOT NULL DEFAULT '' COMMENT 'AES_ENCRYPT(''password'',''usernamepassword'')',
  `email` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL DEFAULT '',
  `picture` varchar(255) DEFAULT NULL,
  `email_level` tinyint(1) DEFAULT '2',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_info_keys`
--

DROP TABLE IF EXISTS `user_info_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_info_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shortname` varchar(255) NOT NULL,
  `longname` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_info_keys`
--

LOCK TABLES `user_info_keys` WRITE;
/*!40000 ALTER TABLE `user_info_keys` DISABLE KEYS */;
INSERT INTO `user_info_keys` VALUES (3,'location','Location'),(4,'phone','Phone Number'),(6,'expertise','What are your areas of interest and/or expertise?'),(7,'favorite','What are your favorite puzzle types?');
/*!40000 ALTER TABLE `user_info_keys` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_info_values`
--

LOCK TABLES `user_info_values` WRITE;
/*!40000 ALTER TABLE `user_info_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_info_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_testsolve_team`
--

DROP TABLE IF EXISTS `user_testsolve_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_testsolve_team` (
  `uid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `uid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_testsolve_team`
--

LOCK TABLES `user_testsolve_team` WRITE;
/*!40000 ALTER TABLE `user_testsolve_team` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_testsolve_team` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-11-04  3:42:30

DROP TABLE IF EXISTS `approver_queue`;
CREATE TABLE `approver_queue` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;

DROP TABLE IF EXISTS `round_captain_queue`;
CREATE TABLE `round_captain_queue` (
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;

DROP TABLE IF EXISTS `codenames`;
CREATE TABLE `codenames` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;

DROP TABLE IF EXISTS `reset_password_tokens`;
CREATE TABLE `reset_password_tokens` (
  `uid` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;

DROP TABLE IF EXISTS `puzzle_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `puzzle_tags` (
  `pid` int(11) NOT NULL,
  `tid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `tag_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag_names` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;;
/*!40101 SET character_set_client = @saved_cs_client */;
