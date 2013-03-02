<?php
	require_once "/var/www/docroot/htmlpurifier/library/HTMLPurifier.auto.php";

	ini_set('default_charset', 'UTF-8');
	ini_set('session.gc_maxlifetime','86400');
	
	session_start();

	$dev = preg_match("/\/(.*)\/writing.*/", $_SERVER["SCRIPT_NAME"], $matches);
	if ($dev) {
           define("DEVMODE", TRUE);
	   define("URL", "http://wind-up-birds.org/" . $matches[1] . "/editing-dev");
	} else {
           define("DEVMODE", FALSE);
	   define("URL", "http://wind-up-birds.org/editing");
	}

	define("SELF", "$_SERVER[PHP_SELF]");
	define("PICPATH", "uploads/pictures/"); // Path for user pictures
	
	date_default_timezone_set('America/New_York');
?>
