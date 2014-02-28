<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";
        function generateRandomString($alphabet, $retlen) {
                $pass = "";
                $len = strlen($alphabet);
                for ($i = 0; $i < $retlen; $i++) {
                        // note: this is not cryptographically secure
                        $pass .= $alphabet[mt_rand(0, $len - 1)];
                }
                return $pass;
        }
        function addAndSendToken($email) {
                $user = get_row_null(sprintf("SELECT * FROM user_info WHERE email='%s'", mysql_real_escape_string($email)));
                if (!$user) { return FALSE; }
                $uid = $user["uid"];
                $token = generateRandomString("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz", 20);
                $escaped_token = mysql_real_escape_string($token);
                $sql = sprintf("INSERT INTO reset_password_tokens (uid, token) VALUES ('%s', '%s') ON DUPLICATE KEY UPDATE token='%s'", mysql_real_escape_string($uid), $escaped_token, $escaped_token);
                query_db($sql);

                $username = $user["username"];
                $subject = "Password Reset Link";
                $message = "This is a password reset link for Puzzletron user $username. To reset your password, click the link. If you didn't request for your password to be reset, you may ignore this email.";
                $link = URL . "/resetpassword.php?token=$token";
                sendEmail($uid, $subject, $message, $link);

                return TRUE;
        }
        function resetPassword($token) {
                $row = get_row_null(sprintf("SELECT * FROM reset_password_tokens LEFT JOIN user_info ON reset_password_tokens.uid = user_info.uid WHERE reset_password_tokens.token='%s';", mysql_real_escape_string($token)));
                if (!$row) return FALSE;

                // check for token expiry
                $now = time();
                $tokentime = strtotime($row["timestamp"]);
                if ($now - $tokentime > 24 * 60 * 60) return FALSE;

                $uid = $row["uid"];
                $username = $row["username"];
                $pass = generateRandomString("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 20);
                newPass($uid, $username, $pass, $pass);

                $subject = "Password Reset Notice";
                $message = "Your Puzzletron password has been reset:\n\nUsername: $username\nPassword: $pass\n\nYou should change your password right away.";
                $link = URL;
                sendEmail($uid, $subject, $message, $link);

                query_db(sprintf("DELETE FROM reset_password_tokens WHERE token='%s';", mysql_real_escape_string($token)));
                return TRUE;
        }

        // Start HTML
        head("resetpassword");

?>
        <h3>Reset Password</h3>
<?
        if (isset($_GET['token'])) {
                $success = resetPassword($_GET['token']);
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
