<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("approver");

        if (!USING_APPROVERS) {
                echo "<div class='msg'>Puzzletron is not set up to use approvers</div>";
                foot();
                exit(1);
        }

        // Check for editor permissions
        if (!isApprover($uid)) {
                echo "<div class='errormsg'>You do not have permission for this page.</div>";
                foot();
                exit(1);
        }

        if ($_SESSION['failedToAddEdit'] == TRUE){
                echo "<div class='errormsg'>Failed to add puzzle to your editing queue<br/>";
                echo "Perhaps you are an author, are testsolving it, or are already editing it?</div>";
                unset($_SESSION['failedToAddEdit']);
        }

        displayPuzzleStats($uid);

?>
        <form action="form-submit.php" method="post">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                Enter Puzzle ID to edit: <input type="text" name="pid" />
                <input type="submit" name="getPuzz" value="Get Puzzle" />
        </form>
        <br>(Hiding dead puzzles)<br>
<?php
        if (ALLOW_EDITOR_PICK) {
                echo '<br/><h3>Needs Approval Editor(s)</h3>';
                $puzzles = getPuzzlesNeedingApprovers();
                displayQueue($uid, $puzzles, TRUE, FALSE, TRUE, TRUE, FALSE, FALSE, TRUE, array());
        }

	echo '<br/>';
	echo '<h3>Approval Editor Queue</h3>';
	$puzzles = getPuzzlesInApproverQueue($uid);
	displayQueue($uid, $puzzles, TRUE, TRUE, TRUE, TRUE, FALSE, FALSE, TRUE, array());

        // End HTML
        foot();

?>
