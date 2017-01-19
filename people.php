<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("people");

print themePeopleMenu($uid);

$people = getPeople();

if (!$people) {
    echo "<strong>No people to list!</strong>";
}

foreach ($people as $p) {
    printPerson($p);
}
// End HTML
foot();


function themePeopleMenu($uid) {
    $html = "<ul>\n";
    if (hasServerAdminPermission($uid)) {
        $html .= "<li><a href=\"user-roles.php\">Manage User Roles</a></li>\n";
    }
    $html .= "</ul>\n";
    return $html;
}
