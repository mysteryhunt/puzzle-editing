<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("roundcaptain");

// Check for editor permissions
if (!isRoundCaptain($uid)) {
    echo "<div class='errormsg'>You do not have permissions for this page.</div>";
    foot();
    exit(1);
}

displayPuzzleStats($uid);

$puzzles = getPuzzlesInRoundCaptainQueue($uid);
displayQueue($uid, $puzzles, "notes answer summary authorsandeditors", FALSE);

// End HTML
foot();

?>
