<?php

        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        isLoggedIn();

        // Start HTML
        head("home");

	$hunt=mktime(12,30,00,1,HUNT_DOM,HUNT_YEAR);
        $now = time();
        $tth=$hunt-$now;
        $days=floor($tth/(60 * 60 * 24));
        $hrs=floor($tth/(60 * 60))-(24*$days);
        $mins=floor($tth/(60))-(24*60*$days)-(60*$hrs);

        echo "<h2>All Updates:</h2>\n";

        // Display index page
        // Put messages to the team here (separate for blind and non-blind solvers?)
	// todo:  fetch updates from the database
?>
<div class="team-updates">
Updates functionality is under construction
</div>

<?        // End HTML
        foot();
?>
