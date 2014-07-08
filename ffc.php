<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("ffc", "Final Fact Check");

        $puzzles = getPuzzlesInFinalFactChecking();
        displayQueue($uid, $puzzles, "notes", FALSE);


        // End HTML
        foot();
?>

