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

echo addEditFailureHtml();

displayPuzzleStats($uid);

?>
    <br/>
    <form action="form-submit.php" method="post">
        <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
        Enter Puzzle ID to edit: <input type="text" name="pid" />
        <input type="submit" name="getPuzz" value="Get Puzzle" />
        <?php if (ALLOW_EDITOR_PICK) {
            echo 'or view the <a href="approver-pick.php">list of puzzles that need approval editors</a>.';
        } ?>
    </form>
<?php

echo '<br/>';
echo '<h3>Approval Editor Queue</h3>';
$puzzles = getPuzzlesInApproverQueue($uid);
displayQueue($uid, $puzzles, "notes answer summary editornotes authorsandeditors", FALSE);
echo  '<br>(Hiding dead puzzles)<br>';
// End HTML
foot();
