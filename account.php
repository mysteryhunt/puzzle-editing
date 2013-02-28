<?php
	require_once "config.php";
	require_once "html.php";
	require_once "db-func.php";
	require_once "utils.php";

	// Redirect to the login page, if not logged in
	$uid = isLoggedIn();
	
	// Start HTML
	head("account");
	
	$passwd = '';
	if (isset($_POST['changepwd'])) {
		$passwd = change_password($_POST['uid'], $_POST['oldpass'],
								$_POST['pass1'], $_POST['pass2']);
	}

	if ($passwd == 'changed') {
		echo '<strong>Password Changed!</strong>';
	} else if ($passwd == 'wrong') {
		echo '<strong>Incorrect Old Password</strong>';
	} else if ($passwd == 'invalid') {
		echo '<strong>Invalid New Password</strong>';
	} else if ($passwd == 'short') {
		echo '<strong>Password Must Be At Least 6 Characters</strong>';
	} else if ($passwd == 'error') {
		echo '<strong>An Error Occurred While Changing Password</strong>';
	}
	
	printPerson(getPerson($uid));
?>
	<p><a href="<?php echo URL ?>/register.php">Edit your information</a></p>
	
	<br />
	
	<script type="text/javascript">
		//<![CDATA[
			function validate(f) {
				if (f.oldpass.value == "") {
					alert("You must enter your password.");
					return false;
				} else if (f.pass1.value == "") {
					alert("You must enter a new password.");
					return false;
				} else if (f.pass2.value == "") {
					alert("You must re-enter your new password.");
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
<!--
	<form method="post" action="<?php echo SELF; ?>" onsubmit="return validate(this)">
	<table>
		<tr>
			<td>Current Password</td>
			<td><input type="password" name="oldpass" value=""/></td>
		</tr>
		<tr>
			<td>New Password</td>
			<td><input type="password" name="pass1" value=""/></td>
		</tr>
		<tr>
			<td>New Password, Again</td>
			<td><input type="password" name="pass2" value=""/></td>
		</tr>
		<tr>
			<td><input type="hidden" name="uid" value="<?php echo($uid); ?>" /></td>
			<td><input type="submit" name="changepwd" value="Change Password" /></td>
		</tr>
	</table>
	</form>
-->
<?php	
	// End HTML
	foot();
?>
