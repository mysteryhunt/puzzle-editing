<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("testsolveteams");

        // Check for permissions
        if (!isTestingAdmin($uid)) {
                echo "Sorry, you're not a testing admin.";
                foot();
                exit(1);
        }

        echo "<h1>Testsolve Team People Assignments</h1>";
        
        echo "<table>";
        echo "<tr><th>Person</th><th>TestSolve Team</th></tr>\n"; 
        $people = getPeopleTeamsList();
        foreach ($people as $uid => $testteam) {
                $name = getUserName($uid);
                $teamid = getUserTestTeamID($uid);
                
                echo "<tr><td>$name</td>\n";
                echo "<td><form method='post' action='form-submit.php'>\n";
                echo "<input type='hidden' name='uid' value='$uid'>\n";
                echo "<SELECT NAME='tid'>\n";
                if ($teamid == NULL) echo "<option value=''>";
                $testteams = getTestTeams();
                foreach ($testteams as $t) {
                        $tid = $t['tid'];
                        $teamname = $t['name'];
                        echo "<option value='$tid' ";
                        if ($tid == $teamid) { echo "SELECTED "; }
                        echo ">$teamname\n"; 
                }

                echo "</select><input type='submit' value='set' name='setUserTestTeam'></form></td><tr>\n"; 
                
        }
        
        echo "</table>";

        echo "<h1>Testsolve Team Puzzle Assignments</h1>";
        echo "<table>";
        echo "<tr><th>Puzzle ID</th><th>TestSolve Team</th></tr>\n";
        $puzzles = getPuzzleTeamsList();
        foreach ($puzzles as $pid  => $testteam) {
                echo "<tr><td>$pid</td>\n";
                echo "<td><form method='post' action='form-submit.php'>\n";
                echo "<input type='hidden' name='pid' value='$pid'>\n";
                echo "<input type='hidden' name='notfrompuzzle' value='YES'>";
                echo "<SELECT NAME='tid'>\n";
                if ($testteam == NULL) echo "<option value=''>";
                $testteams = getTestTeams();
                foreach ($testteams as $t) {
                        $tid = $t['tid'];
                        $teamname = $t['name'];
                        echo "<option value='$tid' ";
                        if ($tid == $testteam) { echo "SELECTED "; }
                        echo ">$teamname\n";
                }

                echo "</select><input type='submit' value='set' name='setPuzzleTestTeam'></form></td><tr>\n";
        }
        echo "</table>";
        


        foot();
        
        function getPeopleTeamsList(){
                $people = getPeople();
                foreach ($people as $p) {
                        $uid = $p['uid'];
                        $testteam = getUserSolveTeam($uid);
                        
                        $teamassignments["$uid"] = $testteam;
                }
        asort($teamassignments);
        return($teamassignments);
 
        }

        function getPuzzleTeamsList(){
                $puzzles = getPuzzlesInTesting();
                foreach ($puzzles as $p) {
                        $pid = $p;
                        $testteam = getPuzzleTestTeam($pid);
                        
                        $teamassignments["$pid"] = $testteam;

                }
        asort($teamassignments);
        return($teamassignments);
        }
?>        
