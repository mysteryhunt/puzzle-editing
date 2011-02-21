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
			echo '<h3> Please try again, or contact contact the <a href="mailto:server-admin@puzzle2011.com">Server Administrators</a>.';
			checkEmailForm();
		}
	} else if(isset($_POST['register'])) {
		$r = register($_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['username'], $_POST['pass1'],
					$_POST['pass2'], $_POST['street'], $_POST['city'], $_POST['state'], $_POST['postal'], 
					$_POST['country'], $_POST['phone'], $_FILES['picture'], $_POST['id'], $_POST['interest'], $_POST['favorite'], 
					$_POST['occupation'], $_POST['bio']);

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
		<h2> Please enter your email address and username</h2>
		<form method="post" action="register.php">
		    E-mail address: <input type="text" name="checkEmail" /><br>
		    Desired username: <input type="text" name="username" /><br>
		    <input type="submit" value="Submit" />
		</form>
<?php
	}

	function registerForm($id)
	{
		echo "<h2>Registration</h2>";

		if (isset($_POST['username']))
			$data['username'] = $_POST['username'];
		if (isset($_POST['checkEmail']))
			$data['email'] = $_POST['checkEmail'];

		if (alreadyRegistered($id)) {
			echo '<h3>You have already registered. You may edit your information, but must use the same password.</h3>';
			$data = getPerson($id);
		}
?>
		<p> All information (other than your password) will be visible to all members of the team. </p>

		<script type="text/javascript">
		//<![CDATA[
			function validate(f) {
				if (f.fname.value == "") {
					alert("You must enter a first name.");
					return false;
				} else if (f.lname.value == "") {
					alert("You must enter a last name.");
					return false;
				} else if (f.email.value == "") {
					alert("You must enter an email address.");
					return false;
				} else if (f.username.value == "") {
					alert("You must enter a username.");
					return false;
				} else if (f.pass1.value == "") {
					alert("You must enter a password.");
					return false;
				} else if (f.pass2.value == "") {
					alert("You must re-enter a password.");
					return false;
				} else if (f.pass1.value != f.pass2.value) {
					alert("Passwords do not match.");
					return false;
				} else if (f.pass1.length < 7) {
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
					<td>First Name*</td>
					<td><input type="text" name="fname" value="<?php echo $data['first']; ?>" /></td>
				</tr>
				<tr>
					<td>Last Name*</td>
					<td><input type="text" name="lname" value="<?php echo $data['last']; ?>" /></td>
				</tr>
				<tr>
					<td>Email Address* (Will not change address on mailing lists)</td>
					<td><input type="text" name="email" value="<?php echo $data['email']; ?>"  /></td>
				</tr>
				<tr>
					<td>Username*</td>
					<td><input type="text" name="username" value="<?php echo $data['username'] ?>"/></td>
				</tr>
				<tr>
					<td>Password*</td>
					<td><input type="password" name="pass1" value=""/></td>
				</tr>
				<tr>
					<td>Password, Again*</td>
					<td><input type="password" name="pass2" /></td>
				</tr>
				<tr>
					<td>Street Address</td>
					<td><input type="text" name="street"  value="<?php echo $data['street']; ?>" /></td>
				</tr>
				<tr>
					<td>City</td>
					<td><input type="text" name="city"  value="<?php echo $data['city']; ?>" /></td>
				</tr>
				<tr>
					<td>State</td>
					<td><input type="text" name="state"  value="<?php echo $data['state']; ?>" /></td>
				</tr>
				<tr>
					<td>Postal Code</td>
					<td><input type="text" name="postal"  value="<?php echo $data['zip']; ?>" /></td>
				</tr>
				<tr>
					<td>Country</td>
					<td><input type="text" name="country"  value="<?php echo $data['country']; ?>" /></td>
				</tr>
				<tr>
					<td>Phone Number</td>
					<td><input type="text" name="phone"  value="<?php echo $data['phone']; ?>" /></td>
				</tr>
				<tr>
					<td>Occupation/Employer</td>
					<td><input type="text" name="occupation" value="<?php echo $data['occupation']; ?>" /></td>
				</tr>
				<tr>
					<td>Upload a picture of yourself (jpg, png, gif)</td>
					<td>
						<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
						<input type="file" name="picture" />
					</td>
				</tr>
				<tr>
					<td>What are your areas of interest and/or expertise?</td>
					<td><textarea class="register" name="interest"><?php echo $data['expertise']; ?></textarea></td>
				</tr>
				<tr>
					<td>What are your favorite puzzle types?</td>
					<td><textarea class="register" name="favorite"><?php echo $data['favorite']; ?></textarea></td>
				</tr>
				<tr>
					<td>Short Bio</td>
					<td><textarea class="register" name="bio"><?php echo $data['bio']; ?></textarea></td>
				</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo($id); ?>" />
			<input type="submit" name="register" value="Register" />
		</form>
<?php
	}
	
//------------------------------------------------------------------------
	function checkEmail($email, $username)
	{
		$sql = sprintf("SELECT * FROM user_info WHERE email='%s'",
			       mysql_real_escape_string($email));
		$result = query_db($sql);

		if (mysql_num_rows($result) == 1) {
			$r = mysql_fetch_assoc($result);
			return $r['uid'];
		} else if (mysql_num_rows($result) == 0) {
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
	
	function register($fname, $lname, $email, $username, $pass1, $pass2, $street, $city, $state, 
						$postal, $country, $phone, $picture, $id, $interest, $favorite, $occupation, $bio)
	{
		if ($fname == "") 
			return "First name may not be empty";
		if ($lname == "")
			return "Last name may not be empty";
		if ($email == "")
			return "Email may not be empty";
		if ($username == "")
			return "Username may not be empty";
		if ($pass1 == "" || $pass2 == "")
			return "Passwords may not be empty";
		if ($pass1 != $pass2)
			return "Passwords do not match";
		if (strlen($pass1) < 6)
			return "Password must be at least 6 characters";
		if ($id == "")
			return "Error: missing id";
		
		if (alreadyRegistered($id)) {
			if (!checkPassword($username, $pass1)) {
				return 'Incorrect Password. Please try again.';
			}
			if ($picture['name'] != '') {
				$pic = pictureHandling($id, $picture);
			} else {
				$pic = getPic($id);
			}
		} else {
			$pic = pictureHandling($id, $picture);
		}
		
		$purifier = new HTMLPurifier();
		$username = $purifier->purify($username);
		$email = $purifier->purify($email);
		$fname = $purifier->purify($fname);
		$lname = $purifier->purify($lname);
		$street = $purifier->purify($street);
		$city = $purifier->purify($city);
		$state = $purifier->purify($state);
		$country = $purifier->purify($country);
		$postal = $purifier->purify($postal);
		$pic = $purifier->purify($pic);
		$interest = $purifier->purify($interest);
		$favorite = $purifier->purify($favorite);
		$occupation = $purifier->purify($occupation);
		$bio = $purifier->purify($bio);

		$sql = sprintf("UPDATE user_info SET username = '%s', password=AES_ENCRYPT('%s', '%s%s'),
						email='%s', first='%s', last='%s', street='%s', city='%s', state='%s',
						country='%s', zip='%s', phone='%s', picture='%s', expertise='%s', favorite='%s',
						occupation='%s', bio='%s' WHERE uid='%s'",
						mysql_real_escape_string($username),
						mysql_real_escape_string($pass1),
						mysql_real_escape_string($username),
						mysql_real_escape_string($pass1),
						mysql_real_escape_string($email),
						mysql_real_escape_string($fname),
						mysql_real_escape_string($lname),
						mysql_real_escape_string($street),
						mysql_real_escape_string($city),
						mysql_real_escape_string($state),
						mysql_real_escape_string($country),
						mysql_real_escape_string($postal),
						mysql_real_escape_string($phone),
						mysql_real_escape_string($pic),
						mysql_real_escape_string($interest),
						mysql_real_escape_string($favorite),
						mysql_real_escape_string($occupation),
						mysql_real_escape_string($bio),
						mysql_real_escape_string($id));
		
		mysql_query('START TRANSACTION');
		
		$result = mysql_query($sql);
		
		if ($result == FALSE) {
			mysql_query('ROLLBACK');
			return "Registration Failed";
		} else {
			mysql_query('COMMIT');
			return TRUE;
		}
	}
	
	function pictureHandling($id, $picture)
	{
		if ($picture == NULL)	// No file uploaded
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
