<?php
	require_once "config.php";
	require_once "html.php";
	require_once "db-func.php";
	require_once "utils.php";

	// Redirect to the login page, if not logged in
	$uid = isLoggedIn();
			
	// Start HTML
	head("answers");
		
	// Check for answers permissions
	if (!isEditor($uid) && !isLurker($uid)) {
		echo "You do not have permission for this page.";
		foot();
		exit(1);
	}

	displayAnswers($uid);
	
	// End HTML
	foot();

//------------------------------------------------------------------------

	function displayAnswers($uid)
	{
		$rounds = getRounds();
?>	
		<table>
<?php 
		foreach($rounds as $round) {
		    $answers = getAnswersForRound($round['rid']);
?>
		    <tr>
			    <td colspan="2"><b><?php echo "{$round['name']}: {$round['answer']}"; ?></b></td>
		    </tr>
<?php 
		    foreach($answers as $answer) {
			    displayAnswer($answer, $uid);
		    }
		}
?>
		</table>

<?php 
	}

	function displayAnswer($answer, $uid)
	{
		$pid = $answer['pid'];
		if ($pid && (isLurker($uid) || isEditorOnPuzzle($uid, $pid) || isAuthorOnPuzzle($uid, $pid))) {
?>
		<tr>
			<td><?php echo $answer['answer'] ?></td>
			<td><?php echo "<a href=\"puzzle?pid=$pid\">".$pid."</a>" ?></td>
			<td><?php echo getTitle($pid) ?></td>
			<td><?php echo getStatusNameForPuzzle($pid) ?></td>
		</tr>
<?php
		} else {
?>
		<tr>
			<td><?php echo $answer['answer'] ?></td>
			<td><?php echo ($pid ? 'assigned' : 'unassigned') ?></td>
		</tr>
<?php
		}
	}
?>
