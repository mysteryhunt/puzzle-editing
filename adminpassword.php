<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";
require_once "utils-password.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("adminpassword");

// Check for admin bits
if (!hasServerAdminPermission($uid)) {
    echo "<div class='errormsg'>You do not have permissions for this page.</div>";
    foot();
    exit(1);
}
if (isset($_POST['username'])) {
    $success = adminResetPasswordByUsername($_POST['username'], $uid);
    if ($success) {
        echo "<div class='okmsg'>Password reset. Please check your email for the user's new password</div>";
    } else {
        echo "<div class='errormsg'>No user with the given username was found!</div>";
    }
} else {
?>
    <h2>Admin Reset Password</h2>
    <form method="post" action="adminpassword.php" class="boxedform" />
    <p>
    Enter the username of the user to reset the password for: <input type="text" name="username" value=""/>
    </p>
    </form>
<?php
}
foot();
