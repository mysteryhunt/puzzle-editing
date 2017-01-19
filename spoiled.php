<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("spoiled", "Spoiled Puzzle List");
?>
    <h2>Puzzles you're spoiled on</h2>
    <p>(Hiding dead puzzles)</p>
<?php
$puzzles = getSpoiledPuzzles($uid);
displayQueue($uid, $puzzles, "notes answer summary authorsandeditors", FALSE);

// End the HTML
foot();
