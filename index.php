<?php

        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("home");

        displayPuzzleStats($uid);

        echo '<br><h3><a href="stats.php">Testsolver stats</a></h3>';

        echo "<h2>Latest Update:</h2>\n";

        // Display index page
        echo "<div class='team-updates'>";

        // Fetch current MOTD from database
        $motd = getCurMotd();
        if ($motd != NULL) {
            $motddate = $motd[1];
            $motdmsg = $motd[2];

            if($motd[3] != NULL) {
                printf ("<b>Message from %s (%s) at</b>", getUserName($motd[3]), getUserUserName($motd[3]));
            }
            printf ("<b> %s UTC:</b><br/>",$motddate);
            echo $motdmsg;
            echo "<br/>";
        }
?>
</div>
<a href="updates.php">Past Updates</a><br />
<a href="<?php echo BUGTRACK_URL ?>">Submit Bugs/Feature Requests Here</a>
<?        // End HTML
        foot();
?>
