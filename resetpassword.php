<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";
require_once "utils-password.php";
// Start HTML
head("resetpassword");

echo "<h2>Reset Password</h2>";

if (isset($_GET['token'])) {
    $success = resetPasswordByToken($_GET['token']);
    if ($success) {
        echo "<div class='okmsg'>Password reset. Please check your email for your new password (allow a few minutes for the cronjob delay)</div>";
    } else {
        echo "<div class='errormsg'>Invalid or expired token</div>";
    }
} elseif (isset($_POST['email'])) {
    $success = addAndSendToken($_POST['email']);
    if ($success) {
        echo "<div class='okmsg'>Please check your email for instructions on how to reset your password (allow a few minutes for the cronjob delay)</div>";
    } else {
        echo "<div class='errormsg'>No user with the given email was found!</div>";
    }
} else {
    echo '<form method="post" action="' . SELF . '">';
    echo 'Email Address <input type="text" name="email" value=""/>';
    echo "</form>";
}
// End HTML
foot();
