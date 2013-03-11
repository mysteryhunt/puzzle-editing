<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("author");
?>
        <h3><a href="submit-new.php">Submit New Puzzle Idea</a></h3>
        <h3>&nbsp;</h3>
        <h3>Your Puzzles</h3>

<?php
        $puzzles = getPuzzlesForAuthor($uid);
        displayQueue($uid, $puzzles, TRUE, TRUE, TRUE, FALSE, FALSE);

        // End the HTML
        foot();
?>
