<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("allpuzzles");

        // Check for lurker permissions
        if (!canSeeAllPuzzles($uid)) {
                echo "You do not have permission for this page.";
                foot();
                exit(1);
        }

        $filt = isValidPuzzleFilter();

        displayPuzzleStats($uid);
?>

        <table><tr><td>
        <form method="get" action="allpuzzles.php">
        <input type="hidden" name="filterkey" value="status">
        <select name="filtervalue">
<?php
        $statuses = getPuzzleStatuses();
        foreach ($statuses as $sid => $sname) {
                echo "<option value='$sid'>$sname</option>";
        }
?>
        </select>
        <input type="submit" value="Filter status">
        </form>

        </td><td>&nbsp;&nbsp;&nbsp;</td><td>
        <form method="get" action="allpuzzles.php">
        <input type="hidden" name="filterkey" value="editor">
        <select name="filtervalue">
<?php
        $editors = getAllEditors();
        asort($editors);
        foreach ($editors as $uid => $fullname) {
                echo "<option value='$uid'>$fullname</option>";
        }
?>
        </select>
        <input type="submit" value="Filter editor">
        </form>

        </td><td>&nbsp;&nbsp;&nbsp;</td><td>
        <form method="get" action="allpuzzles.php">
        <input type="hidden" name="filterkey" value="author">
        <select name="filtervalue">
<?php
        $authors = getAllAuthors();
        asort($authors);
        foreach ($authors as $uid => $fullname) {
                echo "<option value='$uid'>$fullname</option>";
        }
?>
        </select>
        <input type="submit" value="Filter author">
        </form>
        </td></tr></table>
<?php

        $puzzles = getAllPuzzles();
        displayQueue($uid, $puzzles, TRUE, TRUE, TRUE, FALSE, FALSE, $filt);


        // End HTML
        foot();
?>

