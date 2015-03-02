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

