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
	<p><a href="https://puzzle2011.com/phpmyadmin/">phpMyAdmin</a></p>
	<p><a href="https://docs.google.com/Doc?docid=0AYPUrdpGRvdTZGNtcDlrZGNfMzBmYnRyc25rbQ&hl=en">Server Feature Requests (Google Doc)</a></p>
	<p><a href="https://spreadsheets.google.com/ccc?key=0Ai0NigM42FA2dFAyNHhMZWpLVjFEcnMxWlRDclQ0MWc&hl=en#gid=0">Task List (Google Spreadsheet)</a></p>
<?php
	
	
	// End HTML
	foot();
?>
