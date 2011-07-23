<?php
	require_once "config.php";
	require_once "html.php";
	require_once "db-func.php";
	require_once "utils.php";

	// Redirect to the login page, if not logged in
	$uid = isLoggedIn();
			
	// Start HTML
	head("editor");
		
	// Check for editor permissions
	if (!isEditor($uid)) {
		echo "You do not have permission for this page.";
		foot();
		exit(1);
	}
	
	displayPuzzleStats($uid);
	
	if (getNewPuzzleForEditor($uid) == FALSE) {
		echo '<strong>No Puzzles To Add</strong>';
	} else {
		if (isEditor($uid)) {
?>
			<form action="form-submit.php" method="post">
				<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
				<input type="submit" name="getPuzz" value="Get Puzzle" />
			</form>
<?php
		}
	}
		
	$puzzles = getPuzzlesInEditorQueue($uid);
	displayQueue($uid, $puzzles, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, FALSE, FALSE);

	// End HTML
	foot();
	
?>
