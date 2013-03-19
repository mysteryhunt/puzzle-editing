<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("admin");

        // Check for admin bits
        if (!isServerAdmin($uid)) {
                echo "<h3> You do not have permissions for this page. </h3>";
                foot();
                exit(1);
        }

        if (isset($_POST['newmotd'])) {
                $result = postMotdForm($_POST['newmotd'],$_POST['username']);
                if ($result == NULL){
                        echo "<h3>Error Posting New Message</h3><br>";
                }
        } 
        echo "<p><a href=\"";
        echo PHPMYADMIN_URL;
        echo "\">Go To phpMyAdmin</a> (manipulate MySQL database)</p><br>";
?>
        <p>Enter New Message of the Day (MOTD) For Team:<br>
        <form method="post" action="admin.php" />
        <table style="border: 3px solid black;">
            <td><textarea name="newmotd" style="resize:none; width:75ex" wrap="hard" cols=75 rows=10></textarea></td>
        </tr>
        <tr>
            <td><input type="submit" value="Submit"></td>
        </tr>
        </table>
        <input type="hidden" value='<?php echo getUserUsername($_SESSION['uid']); ?>' /> 
        </form>
        </p>

<?php

        function postMotdForm($newmotd, $username)
        {
                if ($newmotd == ''){
                        echo "Empty MOTD is unacceptable.<br>";
                        return (NULL);
                }
	        $result = addNewMotd($newmotd, $username);
                echo "<h3>Added new MOTD successfully </h3><br>";
 	        return ($result);
        }
        // End HTML
        foot();
?>
