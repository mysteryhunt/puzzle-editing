<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";
        function generateRandomPassword() {
                $alphabet = "ABCDEFGHIJKLMNOPQRSTUWXYZ";
                $pass = "";
                $len = strlen($alphabet);
                for ($i = 0; $i < 20; $i++) {
                        // note: this is not cryptographically secure
                        $pass .= $alphabet[mt_rand(0, $len - 1)];
                }
                return $pass;
        }
        function resetPassword($email) {
                $user = get_row_null(sprintf("SELECT * FROM user_info WHERE email='%s'", mysql_real_escape_string($email)));
                if (!$user) { return FALSE; }
                $uid = $user["uid"];
                $username = $user["username"];
                $pass = generateRandomPassword();
                newPass($uid, $username, $pass, $pass);
                $subject = "Password Reset Notice";
                $message = "Your Puzzletron password has been reset:\n\nUsername: $username\nPassword: $pass";
                $link = URL;
                sendEmail($uid, $subject, $message, $link);
                return TRUE;
        }

        // Start HTML
        head("resetpassword");

?>
        <h3>Reset Password</h3>
<?
        if (isset($_POST['email'])) {
                $success = resetPassword($_POST['email']);
                if ($success) {
                        echo "<p><strong>Your password has been reset.</strong> Please check your email (allow a few minutes for the cronjob delay)</p>";
                } else {
                        echo "<p><strong>No user with the given email was found!</strong></p>";
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
