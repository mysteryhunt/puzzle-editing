<?php
        require_once "config.php";
        require_once "db-func.php";
        require_once "utils.php";
        function generateRandomString($bytelen) {
                return bin2hex(openssl_random_pseudo_bytes($bytelen));
        }
        function addAndSendToken($email) {
                $user = get_row_null(sprintf("SELECT * FROM user_info WHERE email='%s'", mysql_real_escape_string($email)));
                if (!$user) { return FALSE; }
                $uid = $user["uid"];
                $token = generateRandomString(16);
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
        function resetPassword($row, $toUid) {
                $uid = $row["uid"];
                $username = $row["username"];
                $email = $row["email"];
                $pass = generateRandomString(16);
                newPass($uid, $username, $pass, $pass);

                $subject = "Password Reset Notice";
                if ($toUid === NULL) {
                        // send to the user whose password is being reset
                        $message = "Your Puzzletron password has been reset:\n\nUsername: $username\nPassword: $pass\n\nYou should change your password right away.";
                        $toUid = $uid;
                } else {
                        // send to an admin, presumably
                        $message = "The Puzzletron password for this user has been reset:\n\nUsername: $username\nPassword: $pass\nEmail: $email";
                }
                $link = URL;
                sendEmail($toUid, $subject, $message, $link);
        }
        function resetPasswordByToken($token) {
                $row = get_row_null(sprintf("SELECT * FROM reset_password_tokens LEFT JOIN user_info ON reset_password_tokens.uid = user_info.uid WHERE reset_password_tokens.token='%s';", mysql_real_escape_string($token)));
                if (!$row) return FALSE;

                // check for token expiry
                $now = time();
                $tokentime = strtotime($row["timestamp"]);
                if ($now - $tokentime > 24 * 60 * 60) return FALSE;

                resetPassword($row, NULL);
                query_db(sprintf("DELETE FROM reset_password_tokens WHERE token='%s';", mysql_real_escape_string($token)));
                return TRUE;
        }
        function adminResetPasswordByUsername($username, $adminUid) {
                $row = get_row_null(sprintf("SELECT * FROM user_info WHERE username='%s';", mysql_real_escape_string($username)));
                if (!$row) return FALSE;
                resetPassword($row, $adminUid);
                return TRUE;
        }
?>
