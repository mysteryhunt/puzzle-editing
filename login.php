<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "utils.php";
require_once "html.php";

if (isset($_SESSION['uid'])) {
    head();
    //simply do a refresh of session time and siteID for sanity
    $_SESSION['time'] = time();
    $_SESSION['SITEURL'] = URL;

    if (!TRUST_REMOTE_USER) {
        echo '<div class="msg"> You are logged in. Would you like to <a href="logout.php">log out</a>?</div><br>';
    } else {
        echo '<h3> A new puzzletron session has been initialized for you via single-sign-on authentication <br>';
        echo '(most likely because your previous session expired or this is your first visit to puzzletron in a while)<br>';
        echo 'Would you like to <a href="logout.php">log out</a>?</h3><br>';
    }
    echo '<a href="index.php" class="goto">Go to puzzletron main/welcome page.</a>';
} elseif (TRUST_REMOTE_USER) {   //we are trusting apache remote_user header so use that
    login($_SERVER['HTTP_REMOTE_USER'], "nopass");
    // If login was successful, user was redirected to index.php
    head();
    echo "<div class='errormsg'>User not yet registered</div>";
    loginForm();
} elseif (isset($_POST['username'])) {  //try to login with username/password if this is login form
    login($_POST['username'], $_POST['pass']);

    // If login was successful, user was redirected to index.php
    head();
    echo "<div class='errormsg'>Incorrect Username or Password</div>";
} else {
    // otherwise display login form
    head();
    loginForm();
}

// End the HTML
foot();

//------------------------------------------------------------------------
function loginForm() {
?>
    <strong>You need to <a href="register.php">register for puzzletron</a> before you can use it.</strong>

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
    <p>If you've forgotten your password, you can
    <a href="resetpassword.php">reset your password</a>.</p>
<?php
}

//------------------------------------------------------------------------
// Try to log in user
// Redirects to main page if successful
function login($username, $pass) {
    if (!TRUST_REMOTE_USER) {
        $uid = checkPassword($username, $pass);
        if ($uid == FALSE) {
            return FALSE;
        }
    } else {
        $sql = sprintf("SELECT uid FROM users WHERE
            username='%s'",
            mysql_real_escape_string($username));
        $result = query_db($sql);
        if (mysql_num_rows($result) != 1) {
            return FALSE;
        }
        $r = mysql_fetch_assoc($result);
        $uid = $r['uid'];
    }

    // Store uid in SESSION
    $_SESSION['uid'] = $uid;
    $_SESSION['SITEURL'] = URL;

    if (isset($_SESSION['redirect_to'])) {
        header("Location: " . $_SESSION['redirect_to']);
    } else {
        if (TRUST_REMOTE_USER) {
            header("Location: " . URL . "/login.php");
        } else {
            header("Location: " . URL . "/index.php");
        }
    }
}
