<?php
        require_once "html.php";
        require_once "utils.php";
        require_once "db-func.php";
        require_once "config.php";
        require_once "utils-pic.php";

        // Start HTML
        head();
        echo '<h2>Account Registration</h2>';
        if (isset($_SESSION['uid'])) {
                echo '<div class="msg">You are logged in. Would you like to <a href="edit-account.php">edit your account information</a>?</div>';
        } else if (isset($_POST['checkEmail'])) {
                // If an email address is submitted, see if is on the team mailing list.
                $id = checkEmail($_POST['checkEmail'], $_POST['username']);
                if($id != FALSE) {
                        // If it is, allow the user to register.
                        registerForm($id);
                } else {
                        // Otherwise, try again.
                        echo '<div class="errormsg">';
                        echo 'I\'m sorry. That email address is not authorized to register.<br />';
                        echo 'Or there was some other failure in initial authorization check.<br />';
                        echo 'Please try again, or contact the Server Administrators.<br />';
                        echo '</div>';
                        checkEmailForm();
                }
        } else if(isset($_POST['register'])) {
                $r = register();

                if ($r === TRUE) {
                        echo '<div class="okmsg">Registration Successful.</div>';
                        echo '<a href="index.php">Log In</a>';
                } else {
                        echo "<div class='errormsg'>$r</div>";
                        registerForm($_POST['id']);
                }
        } else {
                checkEmailForm();
        }

        // End HTML
        foot();

//------------------------------------------------------------------------
        function checkEmailForm()
        {
?>
                <p><strong>Please enter your email address <?php if (!TRUST_REMOTE_USER) { ?> and desired username<?php } ?>.</strong></p>
                <form method="post" action="register.php" class="boxedform">
                        <table><tr><td>E-mail address:</td><td><input type="text" name="checkEmail" /></td></tr>
                        <?php if (TRUST_REMOTE_USER) { ?><tr><td>Username:</td><td><?PHP echo $_SERVER['HTTP_REMOTE_USER']; ?></td></tr>
                        <input type="hidden" name="username" value="<?PHP echo $_SERVER['HTTP_REMOTE_USER']; ?>"/> <?php } ?>
                        <?php if (!TRUST_REMOTE_USER) { ?><tr><td>Desired username:</td><td><input type="text" name="username" /></td></tr><?php } ?>
                        <tr><td colspan="2"><input type="submit" value="Submit" /></td></tr>
                        </table>
                </form>
<?php
        }

        function registerForm($id)
        {

                if (alreadyRegistered($id)) {
                        echo '<strong>You have already registered. You may edit your information, but must use the same password.</strong>';
                        $data = getPerson($id);
                }

                if (isset($_POST['username']))
                        $data['username'] = $_POST['username'];
                if (isset($_POST['checkEmail']))
                        $data['email'] = $_POST['checkEmail'];
                if (isset($_POST['email']))
                        $data['email'] = $_POST['email'];
                if (isset($_POST['fullname']))
                        $data['fullname'] = $_POST['fullname'];
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
                                } else if (f.pass1.value == "") {
                                        alert("You must enter a password.");
                                        return false;
                                } else if (f.pass2.value == "") {
                                        alert("You must re-enter a password.");
                                        return false;
                                } else if (f.pass1.value != f.pass2.value) {
                                        alert("Passwords do not match.");
                                        return false;
                                } else if (f.pass1.value.length < 6) {
                                        alert("Password must be at least 6 characters.");
                                        return false;
                                }

                                return true;
                        }
                //]]>
                </script>

                <form enctype="multipart/form-data" method="post" action="<?php echo SELF; ?>" onsubmit="return validate(this)">
                        <table>
                                <tr>
                                        <td>Email Address* (Will not change address on mailing lists)</td>
                                        <td><input type="text" name="email" value="<?php echo $data['email']; ?>"  /></td>
                                </tr>
                                <tr>
                                        <td>Username*</td>
					<?php if (TRUST_REMOTE_USER) { ?>
					<td><?php echo $_SERVER[HTTP_REMOTE_USER]; ?></td>
					<input type="hidden" name="username" value="<?php echo $_SERVER[HTTP_REMOTE_USER]; ?>"/>
					<?php } ?>

					<?php if (!TRUST_REMOTE_USER) { ?>
                                        <td><input type="text" name="username" value="<?php echo $data['username'] ?>"/></td>
					<?php } ?>
                                </tr>
                                <tr>
                                        <td>Full Name*</td>
                                        <td><input type="text" name="fullname" value="<?php echo $data['fullname'] ?>"/></td>
                                </tr>
				<?php if (!TRUST_REMOTE_USER) { ?>
                                <tr>
                                        <td>Password*</td>
                                        <td><input type="password" name="pass1" value=""/></td>
                                </tr>
                                <tr>
                                        <td>Password, Again*</td>
                                        <td><input type="password" name="pass2" /></td>
                                </tr>
				<?php } ?>
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
                                               mysql_real_escape_string($id), mysql_real_escape_string($user_key_id));
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
                        <input type="hidden" name="id" value="<?php echo($id); ?>" />
                        <input type="submit" name="register" value="Register" />
                </form>
<?php
        }

//------------------------------------------------------------------------
        //this function doesn't actually check for email authorization
        //you'll need to change it somehow if you want it to do that.
        function checkEmail($email, $username)
        {
                if ($username == ""){
                        return false;
                }
                $sql = sprintf("SELECT * FROM user_info WHERE email='%s'",
                               mysql_real_escape_string($email));
                $result = query_db($sql);

                if (mysql_num_rows($result) == 1) {
                        $r = mysql_fetch_assoc($result);
                        return $r['uid'];
                } else if (mysql_num_rows($result) == 0) {
                        //have it return false here if you plan to pre-populate user_info
                        //with known valid team email addressees
                        $sql = sprintf("INSERT INTO user_info (username, email) VALUES ('%s', '%s')",
                                       mysql_real_escape_string($username),
                                       mysql_real_escape_string($email));
                        $result = query_db($sql);

                        $sql = "SELECT LAST_INSERT_ID()";
                        $result = query_db($sql);
                        $r = mysql_fetch_row($result);
                        return $r[0];
                }
        }

        function register()
        {
                $data = $_POST;
                $picture = $_FILES['picture'];
                $id = $data['id'];

                if ($data['email'] == "")
                        return "Email may not be empty";
                if ($data['username'] == "")
                        return "Username may not be empty";
                if ($data['fullname'] == "")
                        return "Full name may not be empty";
		if (!TRUST_REMOTE_USER) {
                if ($data['pass1'] == "" || $data['pass2'] == "")
                        return "Passwords may not be empty";
                if ($data['pass1'] != $data['pass2'])
                        return "Passwords do not match";
                if (strlen($data['pass1']) < 6)
                        return "Password must be at least 6 characters";
		}
                if ($data['id'] == "")
                        return "Error: missing id";

                if (alreadyRegistered($id)) {
			if (!TRUST_REMOTE_USER) {
                        if (!checkPassword($data['username'], $data['pass1'])) {
                                return 'Incorrect Password. Please try again.';
                        }
			}
                        if ($picture['name'] != '') {
                                $pic = pictureHandling($id, $picture);
                        } else {
                                $pic = getPic($id);
                        }
                } else {
                        if ($picture['name'] != '') {
			        $pic = pictureHandling($id, $picture);
			}
                }

                $purifier = new HTMLPurifier();
                $id = $purifier->purify($id);
                $username = $purifier->purify($data['username']);
                $fullname = $purifier->purify($data['fullname']);
                $pic = $purifier->purify($pic);

                mysql_query('START TRANSACTION');
                $failed = 0;

		if (TRUST_REMOTE_USER) {
			$sql = sprintf("UPDATE user_info SET username = '%s',
                               		fullname='%s', picture='%s' WHERE uid='%s'",
			       		mysql_real_escape_string($username),
			       		mysql_real_escape_string($fullname), mysql_real_escape_string($pic), mysql_real_escape_string($id));
		}
		if (!TRUST_REMOTE_USER) {
                $sql = sprintf("UPDATE user_info SET username = '%s', password=AES_ENCRYPT('%s', '%s%s'),
                               fullname='%s', picture='%s' WHERE uid='%s'",
                               mysql_real_escape_string($username), mysql_real_escape_string($data['pass1']),
                               mysql_real_escape_string($username), mysql_real_escape_string($data['pass1']),
                               mysql_real_escape_string($fullname), mysql_real_escape_string($pic), mysql_real_escape_string($id));
 		}

                $result = mysql_query($sql);
                if ($result == FALSE)
                        $failed = 1;

                $sql = sprintf("DELETE from user_info_values WHERE person_id = '%s'", mysql_real_escape_string($id));
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
                        $value = $data[$shortname];
                        $value = $purifier->purify($value);

                        if ($data[$shortname] != "") {
                                $sql = sprintf("INSERT INTO user_info_values VALUES ('%s', '%s', '%s')",
                                               mysql_real_escape_string($id),
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
