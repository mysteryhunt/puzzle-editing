<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("allpuzzles", "All Puzzles");
echo '<style type="text/css">.puzzideasummary {background-color: #000000;}</style>';

if (!canSeeAllPuzzles($uid) && !hasApproverPermission($uid)) {
    echo "<div class='errormsg'>You do not have permissions for this page.</div>";
    foot();
    exit(1);
}

$filt = isValidPuzzleFilter();

function selected($key, $value) {
    global $filt;

    if (count($filt) == 2 && $filt[0] == $key && $filt[1] == $value) {
        return "selected";
    }
    return "";
}

displayPuzzleStats($uid);
?>
    <div class="inlbox">
    <form method="get" action="allpuzzles.php" class="inlform">
        <input type="hidden" name="filterkey" value="status">
        <select name="filtervalue">
        <option value='-'>-</option>
        <?php
            $statuses = getPuzzleStatuses();
            foreach ($statuses as $sid => $sname) {
                $slct = selected('status', $sid);
                echo "<option value='$sid' $slct>$sname</option>";
            }
        ?>
        </select>
        <input type="submit" value="Filter status">
    </form>
    <form method="get" action="allpuzzles.php" class="inlform">
        <input type="hidden" name="filterkey" value="editor">
        <select name="filtervalue">
        <option value='-'>-</option>
        <?php
            $editors = getAllEditors();
            asort($editors);
            foreach ($editors as $uid => $fullname) {
                $slct = selected('editor', $uid);
                echo "<option value='$uid' $slct>$fullname</option>";
            }
        ?>
        </select>
        <input type="submit" value="Filter approver">
    </form>
<?php if (USING_APPROVERS) { ?>
    <form method="get" action="allpuzzles.php" class="inlform">
        <input type="hidden" name="filterkey" value="approver">
        <select name="filtervalue">
        <option value='-'>-</option>
        <?php
            $editors = getAllApprovalEditors();
            asort($editors);
            foreach ($editors as $uid => $fullname) {
                $slct = selected('approver', $uid);
                echo "<option value='$uid' $slct>$fullname</option>";
            }
        ?>
        </select>
        <input type="submit" value="Filter editor">
    </form>
<?php } ?>
    <form method="get" action="allpuzzles.php" class="inlform">
        <input type="hidden" name="filterkey" value="author">
        <select name="filtervalue">
        <option value='-'>-</option>
        <?php
        $authors = getAllAuthors();
        asort($authors);
        foreach ($authors as $uid => $fullname) {
            $slct = selected('author', $uid);
            echo "<option value='$uid' $slct>$fullname</option>";
        }
        ?>
        </select>
        <input type="submit" value="Filter author">
    </form>
    <form method="get" action="allpuzzles.php" class="inlform">
        <input type="hidden" name="filterkey" value="tag">
        <select name="filtervalue">
        <option value='-'>-</option>
        <?php
        $tags = getAllTags();
        asort($tags);
        foreach ($tags as $tid => $name) {
            $slct = selected('tag', $tid);
            echo "<option value='$tid' $slct>$name</option>";
        }
        ?>
        </select>
        <input type="submit" value="Filter tag">
    </form>
    </div>
<?php
$puzzles = getAllPuzzles();
$uid = isLoggedIn();
echo "(Hiding dead puzzles by default)<br><br>";
displayQueue($uid, $puzzles, "notes answer summary editornotes tags authorsandeditors currentpuzzletestercount", FALSE, $filt);

// End HTML
foot();
