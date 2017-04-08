<?php // vim:set ts=4 sw=4 sts=4 et:
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

if (isset($_POST['editAccount'])) {
    $r = editAccount($uid);

    if ($r === TRUE) {
        echo '<div class="okmsg">Your account information has been successfully edited.</div>';
    } else {
        echo "<div class='errormsg'>$r</div>";
    }
}
$data = getPerson($uid);
?>
    <p>All information will be visible to all members of the team.</p>

    <script type="text/javascript">
    //<![CDATA[
    function validate(f) {
        if (f.fullname.value == "") {
            alert("You must enter a first name.");
            return false;
        } elseif (f.email.value == "") {
            alert("You must enter an email address.");
            return false;
        } elseif (f.username.value == "") {
            alert("You must enter a username.");
            return false;
        } elseif (TRUST_REMOTE_USER) {
            return true;
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
        <tr>
            <td>Update Email Preferences</td>
            <td>
                <input type="radio" name="email_pref" value="1" <?php if ($data['email_level'] == 1) echo "checked" ?>/>Human Comments on Puzzles<br/>
                <input type="radio" name="email_pref" value="2" <?php if ($data['email_level'] == 2) echo "checked" ?>/>All Puzzle Updates<br/>
            </td>
        </tr>
<?php
// Start by getting the list of rows of user_info_keys into arrays.
$sql = "SELECT id, shortname, longname FROM user_info_keys";
$result = get_rows($sql);
foreach ($result as $r) {
    $shortname = $r['shortname'];
    $longname = $r['longname'];
    $user_key_id = $r['id'];
    $sql = sprintf("SELECT value FROM user_info_values WHERE person_id = '%s' AND user_info_key_id = '%s'",
        mysql_real_escape_string($uid), mysql_real_escape_string($user_key_id));
    $res = get_rows($sql);
    $lastvalue = '';
    if (count($res) > 0) {
        $lastvalue = $res[0]['value'];
    }
    if (isset($_POST[$shortname])) {
        $lastvalue = $_POST[$shortname];
    }
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
function editAccount($uid) {
    $user = getPerson($uid);
    $picture = $_FILES['picture'];

    if ($_POST['email'] == "") {
        return "Email may not be empty";
    }
    if ($_POST['fullname'] == "") {
        return "Full name may not be empty";
    }
    if ($picture['name'] != '') {
        $pic = pictureHandling($uid, $picture);
    } else {
        $pic = getPic($uid);
    }
    $purifier = getHtmlPurifier();
    $fullname = $purifier->purify($_POST['fullname']);
    $pic = $purifier->purify($pic);
    $email_level = $purifier->purify($_POST['email_pref']);

    mysql_query('START TRANSACTION');
    $failed = 0;

    $sql = sprintf("UPDATE users SET fullname='%s', picture='%s', email_level='%s' WHERE uid='%s'",
        mysql_real_escape_string($fullname), mysql_real_escape_string($pic), mysql_real_escape_string(($email_level)), mysql_real_escape_string($uid));

    $result = mysql_query($sql);
    if ($result == FALSE) {
        $failed = 1;
    }

    $sql = sprintf("DELETE from user_info_values WHERE person_id = '%s'", mysql_real_escape_string($uid));
    $result = mysql_query($sql);
    if ($result == FALSE) {
        $failed = 1;
    }

    $sql = sprintf("SELECT id, shortname, longname FROM user_info_keys");
    $result = get_rows($sql);
    if (!$result) {
        $failed = 1;
    }

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
            if ($res == FALSE) {
                $failed = 1;
            }
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
