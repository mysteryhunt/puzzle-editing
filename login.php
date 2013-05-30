<?php
        require_once "config.php";
        require_once "utils.php";
        require_once "html.php";

        if (isset($_SESSION['uid'])) {
                head();
                $_SESSION['time'] = time();
                if (!TRUST_REMOTE_USER) {
                        echo '<h3> You are logged in. Would you like to <a href="logout.php">log out</a>?</h3>';
                } else {
                        echo '<h3> A new puzzletron session has been initialized for you via single-sign-on authentication <br>';
                        echo '(most likely because your previous session expired or this is your first visit to puzzletron in a while)<br>';
                        echo 'Would you like to <a href="logout.php">log out</a>?</h3>';
                }
	} else if(TRUST_REMOTE_USER) {   //we are trusting apache remote_user header so use that
		login($_SERVER['HTTP_REMOTE_USER'], "nopass");
		// If login was successful, user was redirected to index.php
		head();
		echo "<h3> User not yet registered</h3>";
		loginForm();
	} else if (isset($_POST['username'])) {  //try to login with username/password if this is login form
                login($_POST['username'], $_POST['pass']);

                // If login was successful, user was redirected to index.php
                head();
                echo "<h3> Incorrect Username or Password</h3>";
	} else {		//otherwise display login form
                head();
                loginForm();
        }

        // End the HTML
        foot();

//------------------------------------------------------------------------
        function loginForm()
        {
?>
		<h3> You Need to <a href="register.php">register for puzzletron</a>(fill in some basic info) before you can use it.</h3>

                <form method="post" action="<?php echo SELF; ?>">
                        <table>
                                <tr>
					<td>Username:</td>
					<!-- prompt for username if we don't use REMOTE_USER -->
					<?php if (!TRUST_REMOTE_USER) { ?> <td><input type="text" name="username" /></td> <?php } ?> 
					<!-- if we trust REMOTE_USER use that instead -->
					<?php if (TRUST_REMOTE_USER) { ?> <td><?php echo $_SERVER['HTTP_REMOTE_USER']; ?></td> 
					<input type="hidden" name="username" value="<?php echo $_SERVER['HTTP_REMOTE_USER']; ?>"> <?php } ?>
                                </tr>
			<!-- prompt for password if we're not trusting remote_user -->
			<?php if (!TRUST_REMOTE_USER) { ?> <tr>
                                        <td>Password</td>
                                        <td><input type="password" name="pass" value="" /></td>
				</tr> <?php } ?>
                        </table>
		<?php if (!TRUST_REMOTE_USER) { ?> <input type="submit" value="Log In" /> <?php } ?>
                </form><br>
		<p>If you've forgotten your password, you can <a href="
		<?php echo HELP_EMAIL; ?>">e-mail <?php echo HELP_EMAIL ?> for a new one></a>.</p> 

<?php
        }

//------------------------------------------------------------------------
        // Try to log in user
        // Redirects to main page if successful
        function login($username, $pass)
        {
		if (!TRUST_REMOTE_USER){
                $sql = sprintf("SELECT uid FROM user_info WHERE
						username='%s'  
						AND password=AES_ENCRYPT('%s', '%s%s')",
                                                mysql_real_escape_string($username),
                                                mysql_real_escape_string($pass),
                                                mysql_real_escape_string($username),
                                                mysql_real_escape_string($pass));
		} else {
			$sql = sprintf("SELECT uid FROM user_info WHERE
						username='%s'",
						mysql_real_escape_string($username));
		}
                $result = query_db($sql);
                if (mysql_num_rows($result) != 1) {
                        // Username/password combination not in database
                        return FALSE;
                }

                // Store uid in SESSION
                $r = mysql_fetch_assoc($result);
                $_SESSION['uid'] = $r['uid'];

                if (TRUST_REMOTE_USER){
		        header("Location: " . URL . "/login.php");
                } else {
                        header("Location: " . URL . "/index.php");
                }
        }
?>
