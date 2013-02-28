<?php
	require_once "config.php";
	require_once "html.php";
	require_once "db-func.php";
	require_once "utils.php";

	// Redirect to the login page, if not logged in
	$uid = isLoggedIn();
			
	// Start HTML
	head("puzzlestats");
	
	if (isBlind($uid)) {
		echo '<h3>This page may contain spoilers</h3>';
		foot();
	}

	echo '<h1 style="margin-top: 0em; margin-bottom: 0em;">Puzzle Status Numbers</h1>';
	echo '<table>';
	
	$counts = getPuzzleStatusCounts();
	
	$completed = array(7);
	makeStatusTable($completed, $counts, 'Completed Puzzles', 'complete-stats');
	
	$written = array(4, 5, 6);
	makeStatusTable($written, $counts, 'Fact Check / Revision', 'writing-stats');
	
	$testing = array(2, 3, 4, 5);
	makeStatusTable($testing, $counts, 'Testing', 'testing-stats');
	
	$ideas = array(1);
	makeStatusTable($ideas, $counts, 'Development', 'pending-stats');
	
	$dead = array(8);
	makeStatusTable($dead, $counts, 'Dead / Admin', 'dead-stats');
	
	echo '</table>';
	
	foot();

function makeStatusTable($arr, $counts, $name, $class)
{
	$count = 0;

	foreach ($arr as $status) {
		if (array_key_exists($status, $counts)) {
			$count += $counts[$status];
		}
	}

	echo "<tr><th colspan='2' class='$class'>$name: $count</th></tr>";
	foreach ($arr as $status) {
		if (array_key_exists($status, $counts)) {
			echo "<tr><td class='$class'>" . $counts[$status] . "</td><td class='$class'>" . getPuzzleStatusName($status) . "</td></tr>";
		}
	}
	
	if ($count == 0)
		echo "<tr><td colspan='2' class='$class'>(no puzzles)</td></tr>";
}	
	
?>
