<?php
	require_once "/var/www/docroot/htmlpurifier/library/HTMLPurifier.auto.php";

	ini_set('default_charset', 'UTF-8');
	ini_set('session.gc_maxlifetime','86400');
	
	session_start();

        define("DEVMODE", TRUE);
	define("URL", "http://wind-up-birds.org/editing-dev");

//        define("DEVMODE", FALSE);
//	define("URL", "http://wind-up-birds.org/editing");
	

	define("SELF", "$_SERVER[PHP_SELF]");
	define("PICPATH", "uploads/pictures/"); // Path for user pictures
	define("TRUST_REMOTE_USER", TRUE);  // Skip puzzletron authentication and trust REMOTE_USER
	define("ENABLE_WRITING_WIKI", FALSE);
	define("PHPMYADMIN_URL", "https://wind-up-birds.org/phpmyadmin");
	define("HELP_EMAIL", "wind-up-birds-systems@wind-up-birds.org");
	define("WIKI_URL", "https://wind-up-birds.org");
	define("BUGTRACK_URL", "https://docs.google.com/a/stormynight.org/spreadsheet/ccc?key=0AjP2PJ8PtbmadEYxOU5VUHhoLXoteU1NaEhPTDVjSGc&usp=sharing");
        define("HUNT_YEAR",2014);
        define("HUNT_DOM",17);
	
	date_default_timezone_set('America/New_York');
?>
