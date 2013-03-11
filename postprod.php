<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("postprod");
?>
        <h3>Puzzles in Postprod</h3>

<?php
        $puzzles = getPuzzlesInPostprod($uid);
        displayQueue($uid, $puzzles, TRUE, FALSE, TRUE, FALSE, FALSE);
?>
        <hr>
        <br>
        <h2>Warning: Please don't press this button. If you were supposed to press this button, you would know.</h2>
        <form action="form-submit.php" method="post">
        <input type="hidden" name="uid" value="<?php echo $uid ?>">
        <input type="submit" name="postprodAll" value="Re-postprod ALL puzzles (THIS CANNOT BE UNDONE) [This will take a LONG TIME!]">
        </form>
        <br>

<?php
        // End the HTML
        foot();
?>
