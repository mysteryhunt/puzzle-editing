<?php
        require_once "html.php";
        require_once "utils.php";
        require_once "db-func.php";
        require_once "config.php";

        // Start HTML
        head();

        // If an email address is submitted, see if is on the team mailing list.
        if (isset($_POST['checkEmail'])) {
                $id = checkEmail($_POST['checkEmail'], $_POST['username']);
                if($id != FALSE) {
                        // If it is, allow the user to register.
                        registerForm($id);
                } else {
                        // Otherwise, try again.
                        echo '<h3> I\'m sorry. That email address is not authorized to register. </h3>';
                        echo '<h3> Or there was some other failure in initial authorization check. </h3>';
                        echo '<h3> Please try again, or contact contact the Server Administrators</a>.';
                        checkEmailForm();
                }
        } else if(isset($_POST['register'])) {
                $r = register();

                if ($r === TRUE) {
                        echo '<h4> Registration Successful. </h4>';
			echo '<h4> <a href="index.php"> Log In </a> </h4>';
                } else {
                        echo $r;
                        registerForm($_POST['id']);
                }
        } else if(isset($_SESSION['uid'])) {
                registerForm($_SESSION['uid']);
        } else {
                checkEmailForm();
        }

        // End HTML
        foot();

//------------------------------------------------------------------------
        function checkEmailForm()
        {
?>
		<h2> Please enter your email address. <?php if (!TRUST_REMOTE_USER) { ?> and username <?php } ?> </h2>
                <form method="post" action="register.php">
                    E-mail address: <input type="text" name="checkEmail" /><br>
		    <?php if (TRUST_REMOTE_USER) { ?> username: <?PHP echo $_SERVER['HTTP_REMOTE_USER']; ?><br>
		    <input type="hidden" name="username" value="<?PHP echo $_SERVER['HTTP_REMOTE_USER']; ?>"/> <?php } ?>
		    <?php if (!TRUST_REMOTE_USER) { ?> Desired username: <input type="text" name="username" /><br> <?php } ?> 
                    <input type="submit" value="Submit" />
                </form>
<?php
        }

        function registerForm($id)
        {
                echo "<h2>Registration</h2>";

                if (alreadyRegistered($id)) {
                        echo '<h3>You have already registered. You may edit your information, but must use the same password.</h3>';
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

        function pictureHandling($id, $picture)
        {
                if ($picture == NULL)        // No file uploaded
                        return "";

                //echo 'valid picture <br />';
                if ($picture['size'] == 0) {
                        echo "Problem: uploaded file is zero length";
                        return "";
              }

              if (($picture['type'] != "image/jpeg") &&
                            ($picture['type'] != "image/jpg") &&
                            ($picture['type'] != "image/png") &&
                            ($picture['type'] != "image/gif")) {
                      echo "Problem: file is not a proper png, gif, jpg, or jpeg";
                      return "";
                    }

                    if (!is_uploaded_file($picture['tmp_name'])) {
                      echo "Problem: possible file upload attack";
                        return "";
            }

            $upfile = picName($id, $picture['name']);
            $thumb = thumbName($id);

            if (!move_uploaded_file($picture['tmp_name'], $upfile)) {
                      echo "Problem: Could not move picture into pictures directory";
                      return "";
            }

            makeThumb($upfile, $thumb);

            return $upfile;
        }

        function picName($id, $name)
        {
                return PICPATH . $id . "--" . $name;
        }

        function thumbName($id)
        {
                return PICPATH . "thumbs/$id.jpg";
        }

        function makeThumb($uploaded, $thumbName)
        {
                $maxW = 120;
                $maxH = 120;

                list($width, $height, $type) = getimagesize($uploaded);

                // If the image is too big, scale it down
                // From kvslaap on http://us2.php.net/manual/en/function.imagecopyresized.php
                $imgratio = ($width / $height);
                if ($imgratio > 1) {
                        $newW = $maxW;
                        $newH = ($maxW / $imgratio);
                } else {
                        $newH = $maxH;
                        $newW = ($maxH * $imgratio);
                }

                $thumb = imagecreatetruecolor($newW, $newH);

                if ($type == IMAGETYPE_JPEG) {
                        $source = imagecreatefromjpeg($uploaded);
                } else if ($type == IMAGETYPE_GIF) {
                        $source = imagecreatefromgif($uploaded);
                } else if ($type == IMAGETYPE_PNG) {
                        $source = imagecreatefrompng($uploaded);
                } else {
                        echo "Unrecognized file type.";
                        exit(1);
                }

                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newW, $newH, $width, $height);
                imagejpeg($thumb, $thumbName);
        }
?>
