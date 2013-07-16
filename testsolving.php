<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("testsolving");

        if (isset($_SESSION['testError'])) {
                echo '<h2>' . $_SESSION['testError'] . '</h2>';
                unset($_SESSION['testError']);
        }
        if (isset($_SESSION['feedback'])) {
                echo '<p><strong>' . $_SESSION['feedback'] . '</strong></p><br />';
                unset($_SESSION['feedback']);
        }
?>

        <form action="form-submit.php" method="post">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                Enter Puzzle ID to testsolve: <input type="text" name="pid" />
                <input type="submit" name="getTestId" value="Go" />
        </form>

        <br />
<?php if (ALLOW_TESTSOLVE_PICK) { ?>
        <h2>Available for you to test (In order of priority):</small></h2>
        <b><font color="red">IMPORTANT:</font> Clicking a puzzle below will
        spoil you on it. Please click judiciously.</b>
        <br>
        <small>Tip: If you are joining an existing testsolve group, you can enter the puzzle number above, even if you don't see it here.</small>
        <br>
<?php

        $availPuzzles = getAvailablePuzzlesToTestForUser($uid);
        if ($availPuzzles != NULL)
        {
                // Sort descending, by MINUS priority, then reverse.
                // Trust me on this.
                //
                // (We want to sort by priority descending, then by puzzle ID
                // ascending. This is the easiest way to (sort of mostly)
                // accomplish that.  "Mostly" because PHP's asort is not
                // stable, so this method of using a secondary sort key doesn't
                // quite work.)
                foreach ($availPuzzles as $pid)
                {
                        $sort[$pid] = -getPuzzleTestPriority($pid);
                }
                asort($sort);
                $availPuzzles = array_keys($sort);
                $availPuzzles = array_reverse($availPuzzles);
        }
        displayQueue($uid, $availPuzzles, TRUE, FALSE, FALSE, TRUE, FALSE, TRUE, array());

?>
        <br/>
        <br/>
        <hr/>
        <br/>
<?php } 

        if (USE_TESTSOLVE_TEAMS) {
        
                $myteam = getUserTestTeamID($uid);
                if ($myteam == NULL) {
                        echo "<h2>You are not assigned to a testsolve team at this time. Talk to a testing admin. No puzzles listed.</h2>";
                } else {
                $myteamname = getTestTeamName($myteam);
                echo "<h2>Listing available puzzles to testsolve for team $myteamname </h2>";
                $teampuzzles = getTestTeamPuzzles($myteam);
                if ($teampuzzles == NULL) {
                echo "This testsolving team has no puzzles assigned at this time.";
                } else {
                displayQueue($uid, $teampuzzles, TRUE, FALSE, FALSE, TRUE, FALSE, TRUE, array());
                }
                }
        }


        ?>
        <br><h3>Currently Testing -- (if you're done, please submit a report, even an empty one):</h3>
<?php

  /* Commented out to disallow -- for now -- getting a random puzzle to test.
        if (getPuzzleToTest($uid) == FALSE) {
                echo '<strong>No Puzzles To Add</strong>';
        } else {
?>
                        <form action="form-submit.php" method="post">
                                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                                <input type="submit" name="getTest" value="Get Puzzle" />
                        </form>
<?php
        }
  */

        $testPuzzles = getActivePuzzlesInTestQueue($uid);
        displayQueue($uid, $testPuzzles, TRUE, FALSE, FALSE, TRUE, FALSE, TRUE, array());

        echo '<br />';
        echo '<br />';

        echo '<h3>Finished Testing</h3>';
        $donePuzzles = getActiveDoneTestingPuzzlesForUser($uid);
        displayQueue($uid, $donePuzzles, TRUE, FALSE, FALSE, TRUE, FALSE, TRUE, array());

        echo '<br />';
        echo '<br />';

        echo '<h3>Puzzles Not Currently In Testing</h3>';
        $inactivePuzzles = getInactiveTestPuzzlesForUser($uid);
        displayQueue($uid, $inactivePuzzles, FALSE, FALSE, FALSE, TRUE, FALSE, TRUE, array());

        // End HTML
        foot();

        if (isset($_SESSION['testError']))
                unset($_SESSION['testError']);
?>
