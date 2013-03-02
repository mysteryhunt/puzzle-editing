<?php
	require_once "/var/www/docroot/htmlpurifier/library/HTMLPurifier.auto.php";

	ini_set('default_charset', 'UTF-8');
	ini_set('session.gc_maxlifetime','86400');
	
	session_start();

        //define("DEVMODE", TRUE);
	//define("URL", "http://wind-up-birds.org/editing-dev");

        define("DEVMODE", FALSE);
	define("URL", "http://wind-up-birds.org/editing");
	

	define("SELF", "$_SERVER[PHP_SELF]");
	define("PICPATH", "uploads/pictures/"); // Path for user pictures
	define("TRUST_REMOTE_USER", TRUE);  // Skip puzzletron authentication and trust REMOTE_USER
	
	date_default_timezone_set('America/New_York');
?>
