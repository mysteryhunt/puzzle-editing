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
			    <td colspan="4"><b><?php echo "{$round['name']}: {$round['answer']}"; ?></b></td>
		    </tr>
<?php 
		    foreach($answers as $answer) {
		        $pid = $answer['pid'];
?>
		        <tr>
			        <td><?php echo $answer['answer'] ?></td>
			        <td><?php echo ($pid ? "<a href=\"puzzle.php?pid=$pid\">".$pid."</a>" : "unassigned") ?></td>
			        <td><?php echo ($pid ? getTitle($pid) : "") ?></td>
			        <td><?php echo ($pid ? getStatusNameForPuzzle($pid) : "") ?></td>
		        </tr>
<?php
		    }
		}
?>
		</table>

<?php 
	}
?>

