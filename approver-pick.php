<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("approver", "Approval Editor Overview");

if (!USING_APPROVERS) {
    echo "<div class='msg'>Puzzletron is not set up to use approvers</div>";
    foot();
    exit(1);
}

// Check for editor permissions
if (!isApprover($uid) && !isEditorChief($uid)) {
    echo "<div class='errormsg'>You do not have permission for this page.</div>";
    foot();
    exit(1);
}

if (array_key_exists('failedToAddEdit',$_SESSION) and $_SESSION['failedToAddEdit'] == TRUE){
    echo "<div class='errormsg'>Failed to add puzzle to your editing queue<br/>";
    echo "Perhaps you are an author, are testsolving it, or are already editing it?</div>";
    unset($_SESSION['failedToAddEdit']);
}

displayPuzzleStats($uid);

?>
<br/>
<form action="form-submit.php" method="post">
    <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
    Enter Puzzle ID to edit: <input type="text" name="pid" />
    <input type="submit" name="getPuzz" value="Get Puzzle" />
    or view your current <a href="approver.php">approval editor queue</a>.
</form>
<?php
if (ALLOW_EDITOR_PICK) {
    echo '<br/><h3>Needs Approval Editor(s)</h3>';
    $puzzles = getPuzzlesNeedingApprovers($uid);
    echo '<p><strong class="impt">IMPORTANT:</strong> <strong>Clicking a puzzle below will add you as an approval editor</strong> (unless you already have a role on the puzzle or can see all puzzles.)</p>';
    echo '<p><strong>Please click judiciously and give comments to improve the puzzles you decide to approve.</strong> (You can still remove yourself from being an approval editor later, however.)</p>';
    displayQueue($uid, $puzzles, "notes summary editornotes authorsandeditors", FALSE, array(), "&approve=1");
}

echo '<br>(Hiding dead puzzles)<br>';
// End HTML
foot();
?>
