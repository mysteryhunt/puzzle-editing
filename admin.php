<?php
	require_once "config.php";
	require_once "html.php";
	require_once "db-func.php";
	require_once "utils.php";

	// Redirect to the login page, if not logged in
	$uid = isLoggedIn();
	
	// Start HTML
	head("admin");
	
	// Check for admin bits
	if (!isServerAdmin($uid)) {
		echo "<h3> You do not have permissions for this page. </h3>";
		foot();
		exit(1);
	}
?>
	<p><a href="https://wind-up-birds.org/phpmyadmin/">phpMyAdmin</a></p>
<?php
	
	
	// End HTML
	foot();
?>
