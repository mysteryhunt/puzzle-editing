<?php

	require_once "config.php";
	require_once "html.php";
	require_once "db-func.php";
	require_once "utils.php";

	// Redirect to the login page, if not logged in
	$uid = isLoggedIn();
	
	// Start HTML
	head("home");

	$hunt=mktime(12,17,00,1,14,2011);
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
<b>Tuesday, July 19:</b><br/>
Here's a link to the <a href="http://ihavetofashionpuzzles.com/wiki/index.php/Progress_Tracking">Progress Tracking</a> page on our wiki.<br>
<br>
<b>Wednesday, February 2:</b><br/>
Welcome to the hunt editing server!<br>
</div>
<h3 style="padding-bottom:0.5em;"><a href="updates.php">Past Updates</a></h3>
<?	// End HTML
	foot();
?>
