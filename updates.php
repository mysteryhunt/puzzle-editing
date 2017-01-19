<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
isLoggedIn();

// Start HTML
head("home");

echo "<h2>All Updates:</h2>\n";

// Display index page
// Put messages to the team here (separate for blind and non-blind solvers?)

echo "<div class='team-updates'>";

// Fetch array of MOTDs from database
$motds = getAllMotd();
if (!$motds) {
    echo "<strong>No updates to list</strong>";
}
foreach ($motds as $motd) {
    $motddate = $motd[1];
    $motdmsg = $motd[2];

    if ($motd[3] != NULL) {
        printf("<b>Message from %s (%s) at</b>", getUserName($motd[3]), getUserUserName($motd[3]));
    }
    printf("<b> %s UTC:</b><br/>", $motddate);
    echo $motdmsg;
    echo "<br/>";
}
?>
</div>

<?        // End HTML
foot();
