-- Seed data for dev or staging databases.

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
/* password is password_hash("abelpass", PASSWORD_DEFAULT) */
(2,'abel','$2y$10$7Pi5PD87P5bZyqwwP9O81e5tBZCk9tQNTn0OW3lc0L4tK7zC8gjRm','abel@example.com','Abel','',2),
/* password is password_hash("bakerpass", PASSWORD_DEFAULT) */
(3,'baker','$2y$10$S7gBUVnHOlo5M5CdhFsMr.Ws98Pv4NasxITmtcFkZJDnsUKxb5Kka','baker@example.com','Baker','',1),
/* password is password_hash("charliepass", PASSWORD_DEFAULT) */
(4,'charlie','$2y$10$buVVorYaf0XDr7ce0NHIzeVwyjWYjtsbuFkidqHBONF9swBILkeaC','charlie@example.com','Charlie','',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (2,10),(3,9),(3,10);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `puzzles` WRITE;
/*!40000 ALTER TABLE `puzzles` DISABLE KEYS */;
INSERT INTO `puzzles` VALUES
(1,'a puzzle about puzzle ideas','IDEA IDEA IDEA IDEA IDEA!\n\nIDEA IDEA IDEA IDEA!!!!\n\n!!!',5,'Puzzle Ideas','2015-02-17 13:29:59','NEEDS SOMEONE GOOD AT PUZZLES THAT ONLY HAVE THE WORD IDEA IN THEM','','TESTSOLVE_WIKIPuzzle+Ideas/Testsolve_1','','',2,1),
(2,'Another puzzle about ideas!','IDEA IDEA IDEA!!!!',22,'Another puzzle idea!','2015-02-17 10:14:12','','','','','',2,0),
(3,'IDEA!!!','IDEA IDEA IDEA!!!!!!!!!!',2,'A third idea!!!!','2015-02-17 10:17:35','','','TESTSOLVE_WIKIA+third+idea%21%21%21%21/Testsolve_1','','',2,0),
(4,'idea idea','This is an idea.',2,'Another idea thingy','2015-02-24 07:14:34','STATUS NOTE!!!!','THIS IS AN EDITOR NOTE!!!!','TESTSOLVE_WIKIAnother+idea+thingy/Testsolve_1','Me','HERE IS THE RUNTIME NOTE',2,0),
(5,'an idea bubble','It\'s an idea bubble.',22,'idea','2015-02-23 02:28:11','','','','','',2,0);
/*!40000 ALTER TABLE `puzzles` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `answers` WRITE;
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
INSERT INTO `answers` VALUES
(1,'IDEA',3,NULL),
(2,'IDEA 2',4,NULL),
(3,'IDEA',3,NULL),
(4,'IDEA 3',4,NULL),
(5,'IDEA!!!!',4,NULL),
(6,'IDEA!!!',3,NULL),
(7,'EMPTY IDEA',4,NULL),
(8,'DRAWING A BLANK',1,NULL),
(9,'BAGEL',2,NULL),
(10,'RING',NULL,NULL),
(11,'44',NULL,NULL),
(12,'4+4*4',NULL,NULL),
(13,'44@',NULL,NULL),
(14,'4&amp;4$4',NULL,NULL),
(15,'O',NULL,NULL),
(16,'T',NULL,NULL),
(17,'H',NULL,NULL),
(18,',\'][',5,NULL),
(19,'&lt;',NULL,NULL),
(20,'~',NULL,NULL),
(21,'NO ANSWERS ADDED YET',NULL,NULL);
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `rounds` WRITE;
INSERT INTO `rounds` VALUES
(1,'THE IDEA ROUND!',0,'IDEA WITH TWO ES',NULL),
(2,'THE NON IDEA ROUND!',0,'NOT AN IDEA',NULL),
(3,'Round Round',0,'CIRCLE',NULL),
(4,'The Fourth One',0,'4 IS THE BEST',NULL),
(5,'Round 5',0,'FIVE IS ENOUGH',NULL),
(6,'HRound',0,'8',NULL),
(7,'Another Round?',0,'?',NULL);
/*!40000 ALTER TABLE `rounds` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `answer_round` WRITE;
/*!40000 ALTER TABLE `answer_round` DISABLE KEYS */;
INSERT INTO `answer_round` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,2),(8,2),(9,3),(10,3),(11,4),(12,4),(13,4),(14,4),(15,5),(16,5),(17,5),(18,6),(19,6),(20,6),(21,7);
/*!40000 ALTER TABLE `answer_round` ENABLE KEYS */;
UNLOCK TABLES;


