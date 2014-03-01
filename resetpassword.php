<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";
        require_once "utils-password.php";
        // Start HTML
        head("resetpassword");

?>
        <h2>Reset Password</h2>
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
                Email Address <input type="text" name="email" value=""/>
        </form>

<?php
        }
        // End HTML
        foot();
?>
