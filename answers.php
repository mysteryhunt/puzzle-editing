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

	$seeAll = isLurker($uid);
	
	$byTitle = isset($_GET['title']);
	$byStatus = isset($_GET['status']);
	$byDraft = isset($_GET['draft']);
	$everything = isset($_GET['everything']);
	
	displayAnswers($uid, $byTitle, $byStatus, $byDraft, $everything);
	
	// End HTML
	foot();

//------------------------------------------------------------------------

	function displayAnswers($uid, $byTitle, $byStatus, $byDraft, $everything)
	{
		$rounds = getRounds();
?>	
		<table>
<?php 
		foreach($rounds as $round) {
			if ($round['display']) {
				echo '<tr><td>';
				displayRound($round, $uid, $byTitle, $byStatus, $byDraft, $everything);
				echo '</td></tr>';
			}
		}
?>
		</table>

<?php 
	}

	function displayRound($round, $uid, $byTitle, $byStatus, $byDraft, $everything)
	{
		$answers = getAnswersForRound($round['rid']);
?>
		<table>
			<tr>
				<td colspan="2"><?php echo "{$round['name']}: {$round['answer']}"; ?></td>
			</tr>
<?php 
		foreach($answers as $answer) {
			displayAnswer($answer, $uid, $byTitle,$byStatus,$byDraft,$everything);
		}
?>
		</table>
<?php
	}
	
	function displayAnswer($answer, $uid, $byTitle, $byStatus, $byDraft, $everything)
	{
		$pid = $answer['pid'];
		if (isLurker($uid) || isEditorOnPuzzle($uid, $pid)) {
?>
			<tr>
				<td><?php echo $answer['answer'] ?></td>
				<td><?php if ($everything && $pid) { echo "<a href=\"puzzle?pid=$pid\">".$pid."</a></td><td>".getTitle($pid)."</td><td>".getStatusNameForPuzzle($pid)."</td><td>" . URL . getMostRecentDraftNameForPuzzle($pid);
} else {
				 echo ($pid ? "<a href=\"puzzle?pid=$pid\">". ($byTitle ? getTitle($pid) : "Puzzle $pid"). "</a>". ($byStatus ? " ".getStatusNameForPuzzle($pid) : ' ') . ($byDraft ? " " .getMostRecentDraftNameForPuzzle($pid) : ' ') : 'unassigned');}
				?></td>
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
