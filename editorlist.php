<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("editorlist");


        echo '
        <script type="text/javascript">
        $(document).ready(function() {
            // call the tablesorter plugin
            $("#approvalstats").tablesorter({
                sortList: [[1,1],[3,1]]
            });
            $("#discussionstats").tablesorter({
                sortList: [[1,1],[3,1]]
            });
        });
        </script>
        ';

        function print_rows($rows) {
            foreach ($rows as $row) {
                echo "<tr>";
                    foreach (array_keys($row) as $key) {
                        echo "<td class='ed-stats'>".$row[$key] . "</td>";
                    }
                echo "</tr>";
            }
        }

        echo '<h2>List of Approval Editor Stats</h2>';
        echo '<table id="approvalstats" class="tablesorter">';
        echo '<thead><tr>';
        echo '<th>Editor</th><th>Number of puzzles</th><th>Comments (total)</th><th>Comments (in last week)</th>';
        echo '</tr></thead>';
        print_rows(getApprovalEditorStats());
        echo '</table><br><br>';

        echo '<h2>List of Discussion Editor Stats</h2>';
        echo '<table id="discussionstats" class="tablesorter">';
        echo '<thead><tr>';
        echo '<th>Editor</th><th>Number of puzzles</th><th>Comments (total)</th><th>Comments (in last week)</th>';
        echo '</tr></thead>';
        print_rows(getDiscussionEditorStats());
        echo '</table><br><br>';

        echo '<h2>List of Author Stats</h2>';
        echo '<table id="authorstats" class="tablesorter">';
        echo '<thead><tr>';
        echo '<th>Author</th><th>Number of puzzles</th><th>Comments (total)</th><th>Comments (in last week)</th>';
        echo '</tr></thead>';
        print_rows(getAuthorStats());
        echo '</table><br><br>';

        foot();
?>
