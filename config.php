<?php
	require_once "/var/www/htmlpurifier-4.0.0/library/HTMLPurifier.auto.php";

	ini_set('default_charset', 'UTF-8');
	ini_set('session.gc_maxlifetime','86400');
	
	session_start();

	$dev = preg_match("/\/(.*)\/writing.*/", $_SERVER["SCRIPT_NAME"], $matches);
	if ($dev) {
           define("DEVMODE", TRUE);
	   define("URL", "http://ihavetofashionpuzzles.com/" . $matches[1] . "/writing");
	} else {
           define("DEVMODE", FALSE);
	   define("URL", "http://ihavetofashionpuzzles.com/editing");
	}

	define("SELF", "$_SERVER[PHP_SELF]");
	define("PICPATH", "uploads/pictures/"); // Path for user pictures
	
	date_default_timezone_set('America/New_York');
?>
