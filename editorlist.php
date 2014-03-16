<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("editorlist");

        $approval_counts = getApprovalEditorStats();
		$discussion_counts = getDiscussionEditorStats();

        echo '<h2>List of Approval Editor Stats</h2>';

        echo '<table>';		
        arsort($approval_counts, SORT_NUMERIC);
        foreach (array_keys($approval_counts) as $editor) {
                echo "<tr><td class='ed-stats'>$editor</td><td class='ed-stats'>$approval_counts[$editor]</td></tr>";
        }
        echo '</table><br><br>';


        echo '<h2>List of Discussion Editor Stats</h2>';

        echo '<table>';		
        arsort($discussion_counts, SORT_NUMERIC);
        foreach (array_keys($discussion_counts) as $editor) {
                echo "<tr><td class='ed-stats'>$editor</td><td class='ed-stats'>$discussion_counts[$editor]</td></tr>";
        }
        echo '</table>';

        foot();
?>
