<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("editorlist");

        echo '<h1 style="margin-top: 0em; margin-bottom: 0em;">List of Editor Stats</h1>';

        echo '<table>';

        $counts = getEditorStats();

        arsort($counts, SORT_NUMERIC);
        foreach (array_keys($counts) as $editor) {
                echo "<tr><td class='ed-stats'>$editor</td><td class='ed-stats'>$counts[$editor]</td></tr>";
        }

        echo '</table>';

	echo '<iframe src="http://www.google.com/calendar/embed?src=stormynight.org_9eiihk4r23sgbvlurmf8rn9kp4%40group.calendar.google.com&ctz=America/Los_Angeles" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>';

        foot();
?>
