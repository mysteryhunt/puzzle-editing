-- Seed data for dev or staging databases.

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(2,'abel', AES_ENCRYPT('abelpass', 'abelabelpass'), 'abel@example.com','Abel','',2),
(3,'baker', AES_ENCRYPT('bakerpass', 'bakerbakerpass'), 'baker@example.com','Baker','',1),
(4,'charlie', AES_ENCRYPT('charliepass', 'charliecharliepass'),'charlie@example.com','Charlie','',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
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

