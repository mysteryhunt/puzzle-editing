<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("admin");

// Check for admin bits
if (!isServerAdmin($uid)) {
    echo "<div class='errormsg'>You do not have permissions for this page.</div>";
    foot();
    exit(1);
}

if (isset($_POST['newmotd'])) {
    $result = postMotdForm($_POST['newmotd'], $_POST['username']);
    if ($result == NULL){
        echo "<div class='errormsg'>Error Posting New Message</div>";
    }
}
echo "<p><a href=\"";
echo PHPMYADMIN_URL;
echo "\">Go To phpMyAdmin</a> (manipulate MySQL database)</p><br>";
?>
    <p><a href="adminpassword.php">Admin Reset Password Interface</a></p>
    <p>Enter New Message of the Day (MOTD) For Team:<br>
    <form method="post" action="admin.php" />
        <table class="boxed">
            <td><textarea name="newmotd" style="resize:none; width:75ex" wrap="hard" cols=75 rows=10></textarea></td>
        </tr>
        <tr>
            <td><input type="submit" value="Submit"></td>
        </tr>
        </table>
        <input type="hidden" value='<?php echo getUserUsername($_SESSION['uid']); ?>' />
    </form>
    </p>
<?php
function postMotdForm($newmotd, $username)
{
    if ($newmotd == ''){
        echo "<div class='errormsg'>Empty MOTD is unacceptable.</div>";
        return (NULL);
    }
    $result = addNewMotd($newmotd, $username);
    echo "<div class='okmsg'>Added new MOTD successfully</div>";
    return ($result);
}
// End HTML
foot();
