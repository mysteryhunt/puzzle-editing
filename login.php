<?php
	require_once "config.php";
	require_once "utils.php";
	require_once "html.php";
	
	if (isset($_SESSION['uid'])) {
		head();
		echo '<h3> You are logged in. Would you like to <a href="logout.php">log out</a>?</h3>';
	} else if (isset($_POST['username']) && isset($_POST['pass'])) {
		login($_POST['username'], $_POST['pass']);

		// If login was successful, user was redirected to index.php
		head();
		echo "<h3> Incorrect Username or Password</h3>";
		loginForm();
	} else {
		head();
		loginForm();
	}
	
	// End the HTML
	foot();

//------------------------------------------------------------------------
	function loginForm()
	{
?>
		<h3> Need to <a href="register.php">register</a>?</h3>
		
		<form method="post" action="<?php echo SELF; ?>">
			<table>
				<tr>
					<td>Username</td>
					<td><input type="text" name="username" /></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type="password" name="pass" value="" /></td>
				</tr>
			</table>
			<input type="submit" value="Log In" />
		</form>

<?php
	}
	
//------------------------------------------------------------------------
	// Try to log in user
	// Redirects to main page if successful
	function login($username, $pass)
	{
		$sql = sprintf("SELECT uid FROM user_info WHERE 
						username='%s' AND 
						password=AES_ENCRYPT('%s', '%s%s')",
						mysql_real_escape_string($username),
						mysql_real_escape_string($pass),
						mysql_real_escape_string($username),
						mysql_real_escape_string($pass));
		$result = query_db($sql);
		if (mysql_num_rows($result) != 1) {
			// Username/password combination not in database
			return FALSE;
		}
		
		// Store uid in SESSION
		$r = mysql_fetch_assoc($result);
		$_SESSION['uid'] = $r['uid'];
		
		header("Location: " . URL . "/index.php");
	}
?>