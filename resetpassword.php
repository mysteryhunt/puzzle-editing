<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";
        require_once "utils-password.php";
        // Start HTML
        head("resetpassword");

?>
        <h3>Reset Password</h3>
<?
        if (isset($_GET['token'])) {
                $success = resetPasswordByToken($_GET['token']);
                if ($success) {
                        echo "<div class='okmsg'>Password reset. Please check your email for your new password (allow a few minutes for the cronjob delay)</div>";
                } else {
                        echo "<div class='errormsg'>Invalid or expired token</div>";
                }
        } else if (isset($_POST['email'])) {
                $success = addAndSendToken($_POST['email']);
                if ($success) {
                        echo "<div class='okmsg'>Please check your email for instructions on how to reset your password (allow a few minutes for the cronjob delay)</div>";
                } else {
                        echo "<div class='errormsg'>No user with the given email was found!</div>";
                }
        } else {
?>

        <form method="post" action="<?php echo SELF; ?>">
        <table>
                <tr>
                        <td>Email Address</td>
                        <td><input type="text" name="email" value=""/></td>
                </tr>
        </table>
        </form>

<?php
        }
        // End HTML
        foot();
?>
