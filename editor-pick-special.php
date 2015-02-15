<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("editor-pick-special", "Discussion Editor (find puzzles needing help)");

// Check for editor permissions
if (!isEditor($uid)) {
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
        or view your current <a href="editor.php">discussion editor queue</a>.
    </form>
<?php
if (ALLOW_EDITOR_PICK) {
    echo '<br/>';
    echo '<h3>Puzzles needing help</h3>';
    echo '<p>(Clicking on a puzzle below will no longer automatically add you as a discussion editor. You may choose to add yourself on the puzzle page. Please give comments to improve the puzzles you decide to edit.)</p>';
    $puzzles = getPuzzlesNeedingSpecialEditors();
    displayQueue($uid, $puzzles, "notes summary editornotes authorsandeditors", FALSE, array(), "");
?>
    <script type="text/javascript">
    $(document).ready(function() {
        // decreasing by needed editors, then increasing by ID
        // (I don't have any words to describe how fragile this is; be careful!)
        $(".tablesorter").trigger("sorton", [[[14,1],[0,0]]]);
    });
    </script>
<?php
}

echo '<br/>';

// End HTML
foot();
?>
