<?php

        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("home");

	$hunt=mktime(12,30,00,1,HUNT_DOM,HUNT_YEAR);
        $now = time();
        $tth=$hunt-$now;
        $days=floor($tth/(60 * 60 * 24));
        $hrs=floor($tth/(60 * 60))-(24*$days);
        $mins=floor($tth/(60))-(24*60*$days)-(60*$hrs);

        displayPuzzleStats();
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
<h3 style="padding-bottom:0.5em;"><a href="updates.php">Past Updates</a><br>
<a href="
<?php echo BUGTRACK_URL ?>">Submit Bugs/Feature Requests Here</a><br>
<?        // End HTML
        foot();
?>
