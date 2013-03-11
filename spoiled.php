<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("spoiled");
?>
        <h3>&nbsp;</h3>
        <h3>Puzzles you're spoiled on</h3>

<?php
        $puzzles = getSpoiledPuzzles($uid);
        displayQueue($uid, $puzzles, TRUE, TRUE, TRUE, FALSE, FALSE);

        // End the HTML
        foot();
?>
