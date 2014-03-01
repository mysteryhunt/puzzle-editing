<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("editorlist");

        echo '<h2>List of Editor Stats</h2>';

        echo '<table>';

        $counts = getEditorStats();

        arsort($counts, SORT_NUMERIC);
        foreach (array_keys($counts) as $editor) {
                echo "<tr><td class='ed-stats'>$editor</td><td class='ed-stats'>$counts[$editor]</td></tr>";
        }

        echo '</table>';

        foot();
?>
