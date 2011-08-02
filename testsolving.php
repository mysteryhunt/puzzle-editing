<?php
	require_once "config.php";
	require_once "html.php";
	require_once "db-func.php";
	require_once "utils.php";
	
	// Redirect to the login page, if not logged in
	$uid = isLoggedIn();
			
	// Start HTML
	head("testsolving");
	
	if (isset($_SESSION['testError'])) {
		echo '<h2>' . $_SESSION['testError'] . '</h2>';
		unset($_SESSION['testError']);
	}
	if (isset($_SESSION['feedback'])) {
		echo '<p><strong>' . $_SESSION['feedback'] . '</strong></p><br />';
		unset($_SESSION['feedback']);
	}
?>	

	<form action="form-submit.php" method="post">
		<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
		Enter Puzzle ID to testsolve: <input type="text" name="pid" />
		<input type="submit" name="getTestId" value="Go" />
	</form>
	
	<br />
	
	<h3>In Queue</h3>
<?php

	if (getPuzzleToTest($uid) == FALSE) {
		echo '<strong>No Puzzles To Add</strong>';
	} else {
?>
			<form action="form-submit.php" method="post">
				<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
				<input type="submit" name="getTest" value="Get Puzzle" />
			</form>
<?php
	}		
	$testPuzzles = getActivePuzzlesInTestQueue($uid);
	displayQueue($uid, $testPuzzles, TRUE, FALSE, FALSE, FALSE, FALSE, TRUE, FALSE);
	
	echo '<br />';
	
	echo '<h3>Finished Testing</h3>';
	$donePuzzles = getActiveDoneTestingPuzzlesForUser($uid);
	displayQueue($uid, $donePuzzles, TRUE, FALSE, FALSE, FALSE, FALSE, TRUE, FALSE);
	
	echo '<br />';
	
	echo '<h3>Puzzles Not Currently In Testing</h3>';
	$inactivePuzzles = getInactiveTestPuzzlesForUser($uid);
	displayQueue($uid, $inactivePuzzles, TRUE, FALSE, FALSE, FALSE, FALSE, TRUE, FALSE);
	
	// End HTML
	foot();
	
	if (isset($_SESSION['testError']))
		unset($_SESSION['testError']);
?>
