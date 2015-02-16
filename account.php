<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("account", "Account Overview");

$passwd = '';
if (isset($_POST['changepwd'])) {
    $passwd = change_password($uid, $_POST['oldpass'],
                              $_POST['pass1'], $_POST['pass2']);
}

if ($passwd == 'changed') {
    echo '<div class="okmsg">Password Changed!</div>';
} elseif ($passwd == 'wrong') {
    echo '<div class="errormsg">Incorrect Old Password</div>';
} elseif ($passwd == 'invalid') {
    echo '<div class="errormsg">Invalid New Password</div>';
} elseif ($passwd == 'short') {
    echo '<div class="errormsg">Password Must Be At Least 6 Characters</div>';
} elseif ($passwd == 'error') {
    echo '<div class="errormsg">An Error Occurred While Changing Password</div>';
}

printPerson(getPerson($uid));
?>
    <p class="pad-bottom"><a href="<?php echo URL ?>/edit-account.php">Edit your information</a></p>
    <script type="text/javascript">
    //<![CDATA[
    function validate(f) {
        if (f.oldpass.value == "") {
            alert("You must enter your password.");
            return false;
        } elseif (f.pass1.value == "") {
            alert("You must enter a new password.");
            return false;
        } elseif (f.pass2.value == "") {
            alert("You must re-enter your new password.");
            return false;
        } elseif (f.pass1.value != f.pass2.value) {
            alert("Passwords do not match.");
            return false;
        } elseif (f.pass1.length < 7) {
            alert("Password must be at least 6 characters.");
            return false;
        }

        return true;
    }
    //]]>
    </script>
    <form method="post" action="<?php echo SELF; ?>" onsubmit="return validate(this)" class="boxedform">
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
<?php
// End HTML
foot();
