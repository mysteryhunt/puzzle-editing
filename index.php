<?php

	require_once "config.php";
	require_once "html.php";
	require_once "db-func.php";
	require_once "utils.php";

	// Redirect to the login page, if not logged in
	$uid = isLoggedIn();
	
	// Start HTML
	head("home");

	$hunt=mktime(12,30,00,1,17,2014);
	$now = time();
	$tth=$hunt-$now;
	$days=floor($tth/(60 * 60 * 24));
	$hrs=floor($tth/(60 * 60))-(24*$days);
	$mins=floor($tth/(60))-(24*60*$days)-(60*$hrs);

	echo "<h2>Latest Updates:</h2>\n";
		
	// Display index page
	// Put messages to the team here (separate for blind and non-blind solvers?)
?>
<div class="team-updates">
<b>Sunday, 2013-03-04:</b><br/>
MH2014 Puzzletron is still in pre-implementation/test mode<br>
Submit bugs/feature requests <a href="https://docs.google.com/a/stormynight.org/spreadsheet/ccc?key=0AjP2PJ8PtbmadEYxOU5VUHhoLXoteU1NaEhPTDVjSGc&usp=sharing">Here</a>(wind-up-birds.org google spreadsheet)<br>
</div>
<h3 style="padding-bottom:0.5em;"><a href="updates.php">Past Updates</a></h3>
<?	// End HTML
	foot();
?>
