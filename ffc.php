<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("ffc", "Final Fact Check");

if (!hasFactCheckerPermission($uid)) {
    print themeNotAFactChecker();
    foot();
    exit(1);
}

echo "<h2>(Final) Factchecking</h2>";
$puzzles = getPuzzlesForFactchecker($uid);
displayQueue($uid, $puzzles, "notes summary editornotes finallinks", FALSE);
?>
    <h2>Puzzles Needing Final Factchecker</h2>
    <p><strong class="impt">IMPORTANT:</strong> <b>Clicking a puzzle below will
    mark you as a factchecker. Please click judiciously and complete your factchecking duties!</b></p>
<?php
$puzzles = getAvailablePuzzlesToFFCForUser($uid);
displayQueue($uid, $puzzles, "notes summary editornotes finallinks", FALSE, array(), "&factcheck=1");

// End HTML
foot();

function themeNotAFactChecker() {
    $html = "<h3>Not a factchecker</h3>\n";
    $html .= "<p>You are not currently a factchecker.</p>\n";
    return $html;
}
