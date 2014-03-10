<?php
        require_once "html.php";
        require_once "utils.php";
        require_once "db-func.php";
        require_once "config.php";
        require_once "utils-pic.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("edit-account");

        echo "<h2>Edit Account Information</h2>";

        if(isset($_POST['editAccount'])) {
                $r = editAccount($uid);

                if ($r === TRUE) {
                        echo '<div class="okmsg">Your account information has been successfully edited.</div>';
                } else {
                        echo "<div class='errormsg'>$r</div>";
                }
        }
        $data = getPerson($uid);
?>
        <p> All information (other than your password) will be visible to all members of the team. </p>

        <script type="text/javascript">
        //<![CDATA[
        function validate(f) {
                if (f.fullname.value == "") {
                        alert("You must enter a first name.");
                        return false;
                } else if (f.email.value == "") {
                        alert("You must enter an email address.");
                        return false;
                } else if (f.username.value == "") {
                        alert("You must enter a username.");
                        return false;
                } else if (TRUST_REMOTE_USER) {
                        return true;
                } else if (f.pass.value === "") {
                        alert("You must enter your password.");
                        return false;
                }
                return true;
        }
        //]]>
        </script>

        <form enctype="multipart/form-data" method="post" action="<?php echo SELF; ?>" onsubmit="return validate(this)">
                <table>
                        <?php if (!TRUST_REMOTE_USER) { ?>
                        <tr>
                                <td>Username</td>
                                <?php if (TRUST_REMOTE_USER) { ?>
                                <td><?php echo $_SERVER[HTTP_REMOTE_USER]; ?></td>
                                <input type="hidden" name="username" value="<?php echo $_SERVER[HTTP_REMOTE_USER]; ?>"/>
                                <?php } else { ?>
                                <td><strong><?php echo $data['username'] ?></strong></td>
                                <?php } ?>
                        </tr>
                        <tr>
                                <td><strong>Current password*</strong></td>
                                <td><input type="password" name="pass" value=""/></td>
                        </tr>
                        <?php } ?>
                        <tr>
                                <td>Email Address* (Will not change address on mailing lists)</td>
                                <td><input type="text" name="email" value="<?php echo $data['email']; ?>"  /></td>
                        </tr>
                        <tr>
                                <td>Full Name*</td>
                                <td><input type="text" name="fullname" value="<?php echo $data['fullname'] ?>"/></td>
                        </tr>
                        <tr>
                                <td>Upload a picture of yourself (jpg, png, gif)</td>
                                <td>
                                        <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
                                        <input type="file" name="picture" />
                                </td>
                        </tr>
<?php
                        // Start by getting the list of rows of user_info_keys into arrays.
                        $sql = "SELECT id, shortname, longname FROM user_info_key";
                        $result = get_rows($sql);
                        foreach ($result as $r) {
                                $shortname = $r['shortname'];
                                $longname = $r['longname'];
                                $user_key_id = $r['id'];
                                $sql = sprintf("SELECT value FROM user_info_values WHERE person_id = '%s' AND user_info_key_id = '%s'",
                                               mysql_real_escape_string($uid), mysql_real_escape_string($user_key_id));
                                $res = get_rows($sql);
				$lastvalue = $res[0]['value'];
				if (isset($_POST[$shortname]))
					$lastvalue = $_POST[$shortname];
?>
                                <tr>
                                        <td><?php echo $longname; ?></td>
                                        <td><input type="text" name="<?php echo $shortname; ?>" value="<?php echo $lastvalue; ?>" /></td>
                                </tr>
<?php
                        }
?>
                </table>
                <input type="submit" name="editAccount" value="Submit" />
        </form>
<?php
        function editAccount($uid)
        {
                $user = getPerson($uid);
                $picture = $_FILES['picture'];

                if (!TRUST_REMOTE_USER) {
                        if (!checkPassword($user['username'], $_POST['pass'])) {
                                return 'Incorrect Password. Please try again.';
                        }
                }
                if ($_POST['email'] == "")
                        return "Email may not be empty";
                if ($_POST['fullname'] == "")
                        return "Full name may not be empty";
                if ($picture['name'] != '') {
                        $pic = pictureHandling($uid, $picture);
                } else {
                        $pic = getPic($uid);
                }
                $purifier = new HTMLPurifier();
                $fullname = $purifier->purify($_POST['fullname']);
                $pic = $purifier->purify($pic);

                mysql_query('START TRANSACTION');
                $failed = 0;

                $sql = sprintf("UPDATE user_info SET fullname='%s', picture='%s' WHERE uid='%s'",
                                mysql_real_escape_string($fullname), mysql_real_escape_string($pic), mysql_real_escape_string($uid));

                $result = mysql_query($sql);
                if ($result == FALSE)
                        $failed = 1;

                $sql = sprintf("DELETE from user_info_values WHERE person_id = '%s'", mysql_real_escape_string($uid));
                $result = mysql_query($sql);
                if ($result == FALSE)
                        $failed = 1;

                $sql = sprintf("SELECT id, shortname, longname FROM user_info_key");
                $result = get_rows($sql);
                if (!$result)
                        $failed = 1;

                foreach ($result as $r) {
                        $shortname = $r['shortname'];
                        $longname = $r['longname'];
                        $user_key_id = $r['id'];
                        $value = $_POST[$shortname];
                        $value = $purifier->purify($value);

                        if ($_POST[$shortname] != "") {
                                $sql = sprintf("INSERT INTO user_info_values VALUES ('%s', '%s', '%s')",
                                               mysql_real_escape_string($uid),
                                               mysql_real_escape_string($user_key_id),
                                               mysql_real_escape_string($value));
                                $res = mysql_query($sql);
                                if ($res == FALSE)
                                        $failed = 1;
                        }
                }

                if ($failed == 1) {
                        mysql_query('ROLLBACK');
                        return "Registration Failed";
                } else {
                        mysql_query('COMMIT');
                        return TRUE;
                }
        }

?>