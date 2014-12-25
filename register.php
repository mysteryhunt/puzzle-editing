<?php // vim:set ts=4 sw=4 sts=4 et:
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
} else if (isset($_POST['register'])) {
    $errors = register();

    if ($errors) {
        echo "<div class='errormsg'>Registration failed.<ul>";
        foreach ($errors as $item => $msg) {
            echo "<li>$item: $msg</li>";
        }
        echo "</div>";
        registerForm();
    } else {
        echo '<div class="okmsg">Registration Successful.</div>';
        echo '<a href="index.php">Log In</a>';
    }
} else {
    registerForm();
}

// End HTML
foot();

function registerForm()
{
    $username = isset($_POST['username']) ? $_POST['username'] : "";
    $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : "";
    $email = isset($_POST['email']) ? $_POST['email'] : "";
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
        } else if (<?php echo TRUST_REMOTE_USER ? "true" : "false"; ?>) {
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
                <td>Email Address*</td>
                <td><input type="text" name="email" value="<?php echo $email; ?>"  /></td>
            </tr>
            <tr>
                <td>Username*</td>
                <?php if (TRUST_REMOTE_USER) { ?>
                    <td><?php echo $_SERVER[HTTP_REMOTE_USER]; ?></td>
                    <input type="hidden" name="username" value="<?php echo $_SERVER[HTTP_REMOTE_USER]; ?>"/>
                <?php } ?>

                <?php if (!TRUST_REMOTE_USER) { ?>
                    <td><input type="text" name="username" value="<?php echo $username; ?>"/></td>
                <?php } ?>
            </tr>
            <tr>
                <td>Full Name*</td>
                <td><input type="text" name="fullname" value="<?php echo $fullname; ?>"/></td>
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
$lastvalue = isset($_POST[$shortname]) ? $_POST[$shortname] : "";
?>
            <tr>
                <td><?php echo $longname; ?></td>
                <td><input type="text" name="<?php echo $shortname; ?>" value="<?php echo $lastvalue; ?>" /></td>
            </tr>
<?php
}
?>
        </table>
        <input type="submit" name="register" value="Register" />
    </form>
<?php
}

function register()
{
    $errors = array();
    $data = $_POST;
    $picture = $_FILES['picture'];
    $email = isset($data['email']) ? $data['email'] : "";
    $username = isset($data['username']) ? $data['username'] : "";
    $fullname = isset($data['fullname']) ? $data['fullname'] : "";
    $pass1 = isset($data['pass1']) ? $data['pass1'] : "";
    $pass2 = isset($data['pass2']) ? $data['pass2'] : "";

    if ($email === "") $errors['email'] = "Email may not be empty";
    if ($username === "") $errors['username'] = "Username may not be empty";
    if ($fullname === "") $errors['fullname'] = "Full name may not be empty";
    if (!TRUST_REMOTE_USER) {
        if ($pass1 === "") $errors['pass1'] = "Passwords may not be empty";
        if ($pass2 === "") $errors['pass2'] = "Passwords may not be empty";
        else if ($pass1 !== $pass2) $errors['pass2'] = "Passwords do not match";
        else if (strlen($pass1) < 6) $errors['pass1'] = "Password must be at least 6 characters";
    }
    $purifier = new HTMLPurifier();
    $username = $purifier->purify($username);
    $fullname = $purifier->purify($fullname);
    $email = $purifier->purify($email);

    $sql = sprintf("SELECT * FROM user_info WHERE username='%s'", mysql_real_escape_string($username));
    if (has_result($sql)) $errors['username'] = "Username already taken";
    $sql = sprintf("SELECT * FROM user_info WHERE email='%s'", mysql_real_escape_string($email));
    if (has_result($sql)) $errors['email'] = "There is already an account using that email";

    if ($errors) return $errors;

    if ($picture['name'] != '') $pic = pictureHandling($uid, $picture);
    $pic = $purifier->purify($pic);

    mysql_query('START TRANSACTION');

    if (TRUST_REMOTE_USER) {
        $sql = sprintf("INSERT INTO user_info (username, fullname, email) VALUES ('%s', '%s', '%s')",
            mysql_real_escape_string($username), mysql_real_escape_string($fullname), mysql_real_escape_string($email)
        );
    } else {
        $sql = sprintf("INSERT INTO user_info (username, password, fullname, email) VALUES ('%s', AES_ENCRYPT('%s', '%s%s'), '%s', '%s')",
            mysql_real_escape_string($username), mysql_real_escape_string($pass1),
            mysql_real_escape_string($username), mysql_real_escape_string($pass1),
            mysql_real_escape_string($fullname), mysql_real_escape_string($email)
        );
    }

    $result = mysql_query($sql);
    if ($result === FALSE) {
        mysql_query('ROLLBACK');
        return array("unknown" => "Registration error in adding user");
    }

    $uid = mysql_insert_id();

    $failed = FALSE;

    $sql = sprintf("UPDATE user_info SET picture='%s' WHERE uid='%s'", mysql_real_escape_string($pic), mysql_real_escape_string($uid));
    $result = mysql_query($sql);
    if ($result === FALSE) $failed = TRUE;

    $sql = sprintf("DELETE from user_info_values WHERE person_id = '%s'", mysql_real_escape_string($uid));
    $result = mysql_query($sql);
    if ($result === FALSE) $failed = TRUE;

    $sql = sprintf("SELECT id, shortname, longname FROM user_info_key");
    $result = get_rows($sql);

    foreach ($result as $r) {
        $shortname = $r['shortname'];
        $longname = $r['longname'];
        $user_key_id = $r['id'];

        if (isset($data[$shortname]) && $data[$shortname] !== "") {
            $value = $purifier->purify($data[$shortname]);
            $sql = sprintf("INSERT INTO user_info_values VALUES ('%s', '%s', '%s')",
                mysql_real_escape_string($uid),
                mysql_real_escape_string($user_key_id),
                mysql_real_escape_string($value));
            $res = mysql_query($sql);
            if ($res === FALSE) $failed = TRUE;
        }
    }

    if ($failed) {
        mysql_query('ROLLBACK');
        return array("unknown" => "Registration error in updating info");
    } else {
        mysql_query('COMMIT');
        return array();
    }
}
?>
