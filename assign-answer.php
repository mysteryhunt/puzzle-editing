<?php
	require_once "config.php";
	require_once "html.php";
	require_once "db-func.php";
	require_once "user-func.php";
	require_once "puzzle-func.php";

	// Redirect to the login page, if not logged in
	loggedIn();
	$uid = $_SESSION['uid'];
			
	// Start HTML
	head("");
	
	$pid = $_GET['pid'];
	
	$editor = FALSE;
	$author = FALSE;
	
	// Check for permissions
	if (hasQueue($uid)) {
		$editor = TRUE;
	} else if (checkAuthor($pid, $uid)) {
		$author = TRUE;
	} else {
		echo "You do not have permission to assign an answer to this puzzle.";
		foot();
		exit(1);
	}

	$pid = $_GET['pid'];
	
	echo "<h1>Assign Answer for <a href=\"puzzle?pid=$pid\">Puzzle $pid</a></h1>";
	
	if ($editor) {
		$minihunts = getMinihunts();
		foreach($minihunts as $minihunt) {
			displayMinihunt($minihunt, $uid, $pid);
			echo '<br />';
		}
	} 
	
	if ($author) {
		$answers = getAnswersForAuthor($uid);
		displayAuthorAnswers($answers, $uid, $pid);
	}

	// End HTML
	foot();
//------------------------------------------------------------------------
	function displayMinihunt($minihunt, $uid, $pid)
	{
		$rounds = getRounds($minihunt['mid']);
?>	
		<table>
			<tr>
				<td><?php echo "{$minihunt['name']}: {$minihunt['answer']}"; ?></td>
			</tr>
<?php 
		foreach($rounds as $round) {
			if ($round['display']) {
				echo '<tr><td>';
				displayRound($round, $uid, $pid);
				echo '</td></tr>';
			}
		}
?>
		</table>

<?php 
	}

	function displayRound($round, $uid, $pid)
	{
		$answers = getAnswers($round['rid']);
?>
		<table>
			<tr>
				<td colspan="2"><?php echo "{$round['name']}: {$round['answer']}"; ?></td>
			</tr>
<?php 
		foreach($answers as $answer) {
			displayAnswer($answer, $uid, $pid);
		}
?>
		</table>
<?php
	}
	
	function displayAnswer($answer, $uid, $pid)
	{
		$assigned = $answer['pid'];
		if (!$assigned) {
		
		
?>
				<tr>
				<form action="form-submit.php" method="post">
					<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
					<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
					<input type="hidden" name="aid" value="<?php echo $answer['aid']; ?>" />
					<td><?php echo $answer['answer'] ?></td>
					<td><input type="submit" name="assignAnswer" value="Assign Answer" /></td>
				</form>
				</tr>
<?php
		}
	}
	
	function displayAuthorAnswers($answers, $uid, $pid)
	{
		if ($answers == NULL) {
			echo "No available answers are visible to you. Please contact this puzzle's editors for help.";
		} else {
?>
		<table>
<?php
			foreach($answers as $answer) {
				$assigned = $answer['pid'];
				if (!$assigned) {
?>
				<tr>
				<form action="form-submit.php" method="post">
					<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
					<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
					<input type="hidden" name="aid" value="<?php echo $answer['aid']; ?>" />
					<td><?php echo $answer['answer'] ?></td>
					<td><input type="submit" name="assignAnswer" value="Assign Answer" /></td>
				</form>
				</tr>
<?php 
			
				}
			}
		}
?>
		</table>
<?php
	}
?>