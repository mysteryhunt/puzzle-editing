<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head();

        // Check for puzzle id
        if (!isset($_GET['pid'])) {
                echo "Puzzle ID not found. Please try again.";
                foot();
                exit(1);
        }

        $pid = $_GET['pid'];

        // Check permissions
        if (!isTesterOnPuzzle($uid, $pid) && !isFormerTesterOnPuzzle($uid, $pid)) {
                if (!canTestPuzzle($uid, $pid)) {
                        echo "You do not have permission to test this puzzle.";
                        foot();
                        exit(1);
                } else {
                        addPuzzleToTestQueue($uid, $pid);
                }
        }

        $title = getTitle($pid);
        if ($title == NULL)
                $title = '(untitled)';

        echo "<h1>Puzzle $pid -- $title</h1>";
        echo "<b><font color='red'>IMPORTANT:</font> Please leave feedback! We
        need it!</b><br><br> When you are done, PLEASE leave feedback indicating
        that you do not intend to return, <b>even if the rest is blank</b>. This
        removes you as a tester on this puzzle, so we can track who's still
        working.\n";
        echo "<br><br>\n";

        if (isset($_SESSION['feedback'])) {
                echo '<p><strong>' . $_SESSION['feedback'] . '</strong></p>';
                unset($_SESSION['feedback']);
        }

        maybeDisplayWarning($uid, $pid);
        displayWikiPage($pid);
        displayDraft($pid);
        echo '<br />';

        $otherTesters = getCurrentTestersAsEmailList($pid);
        echo "<p>Current Testsolvers: $otherTesters</p>";
        echo '<br />';

        checkAnsForm($uid, $pid);

        if (isset($_SESSION['answer'])) {
                echo $_SESSION['answer'];
                unset($_SESSION['answer']);
        }

        displayPrevAns($uid, $pid);

        echo '<br />';

        displayFeedbackForm($uid, $pid);

        echo '<br />';
        displayPrevFeedback($uid, $pid);

        // End HTML
        foot();

//------------------------------------------------------------------------

function maybeDisplayWarning($uid, $pid)
{
        if (isTesterOnPuzzle($uid, $pid)) {
                return;
        }
?>
        <table style="border-width: 0px; vertical-align:middle;">
                <tr>
                        <td style="vertical-align:middle;background-color: #FFA7A9;">
                                <B>WARNING:</B> You are not marked as a current
                                testsolver.<br> Please
                                use the puzzle version, and wiki page, that
                                were current when you started solving, NOT the
                                ones listed below (if they differ).<br>If in doubt, contact Glenn
                                Willen or Robin Baur.
                        </td>
                </tr>
        </table>
<?php
}


function displayWikiPage($pid)
{
        $page = getWikiPage($pid);
        if ($page == NULL) {
                echo '<h3>No Testsolve Wiki Page</h3>';
                return;
        }

?>
        <table style="border-width: 0px; vertical-align:middle;">
                <tr>
                        <td style="vertical-align:middle;background-color: #A9D7FF;">
                                Testsolve wiki page: <a href="<?php echo $page; ?>">
                                        <?php echo $page; ?>
                                </a>
                        </td>
                </tr>
        </table>
<?php
}

function displayDraft($pid)
{
        $draft = getMostRecentDraftForPuzzle($pid);

        if ($draft == NULL) {
                echo '<h3>No Draft</h3>';
                return;
        }

        $finfo = pathinfo($draft['filename']);
        if (isset($finfo['extension']))
                $ext = $finfo['extension'];
        else
                $ext = 'folder';

?>
        <table style="border-width: 0px; vertical-align:middle;">
                <tr>
                        <td style="vertical-align:middle;background-color: #FAD97D;">
                                Puzzle: <a href="<?php echo $draft['filename']; ?>">
                                        <?php echo $finfo['basename']; ?>
                                </a>
                        </td>
                        <td style="vertical-align:middle;background-color: #FAD97D;">
                                Uploaded on <?php echo $draft['date']; ?>
                        </td>
                </tr>
        </table>
<?php
}

function checkAnsForm($uid, $pid)
{
?>
        <form method="post" action="form-submit.php">
                Check an answer: (NOTE: If the title says "ANSWER NOT IN PUZZLETRON", this won't work and will always reject your answer.)
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="input" name="ans" />
                <input type="submit" name="checkAns" value="Check" />
        </form>
<?php
}

function displayPrevAns($uid, $pid)
{
        $answers = getAnswerAttempts($uid, $pid);
        if ($answers == NULL)
                return;

        $correct = getCorrectSolves($uid, $pid);
        if ($correct != NULL)
                echo "<h4>Correct Answers: $correct</h4>";

        echo '<table>';
        echo '<tr><td>Attempted Answers:</td></tr>';

        foreach($answers as $ans) {
                echo '<tr>';
                echo "<td class=\"test\">$ans</td>";
                echo '</tr>';
        }
        echo '</table>';
}

function displayFeedbackForm($uid, $pid)
{
?>
        <h2>Feedback Form</h2>
<?php
        if(ANON_TESTERS) {
?>
        <p>Your name will be visible to testing admins and the board,
        but not to other puzzle editors or authors.</p>
<?php
        } else {
?>
        <p>Your name will be attached to your feedback. If you wish to leave
        anonymous feedback, contact a testsolving director.
        </p>
<?php
        }
?>

        <form method="post" action="form-submit.php">
        <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
        <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
        <table>
                <tr>
                        <td>
                        Do you intend to return to this puzzle?
                        <input type="radio" name="done" value="yes" /> Yes
                        <input type="radio" name="done" value="no" /> No
                        <br><small>(Selecting "No" marks you as finished
                        in the database. This is important for
                        our records.)</small>
                        </td>
                </tr>
                <tr>
                        <td>
                        If so, when do you plan to return to it?
                        <input type="text" name="when_return" />
                        </td>
                </tr>
                <tr>
                        <td>
                        How long did you spend on this puzzle (since your last feedback, if any)?
                        <input type="text" name="time" />
                        </td>
                </tr>
                <tr>
                        <td>
                        Describe what you tried. <br />
                        <textarea style="width:50em; height: 25em;" name="tried"></textarea>
                        </td>
                </tr>
                <tr>
                        <td>
                        What did you like/dislike about this puzzle? How hard do you think it is? Is there anything you think should be changed?<br />
                        <textarea style="width:50em; height: 25em;" name="liked"></textarea>
                        </td>
                </tr>
                <tr>
                        <td>
                                <input type="submit" name="feedback" value="Submit Feedback" class="okSubmit" />
                        </td>
                </tr>
        </table>
        </form>
<?php
}

function displayPrevFeedback($uid, $pid)
{
        $prevFeedback = getPreviousFeedback($uid, $pid);
        if ($prevFeedback == NULL)
                return;

        echo '<h3>Previous Feedback</h3>';
        echo '<table>';

        foreach ($prevFeedback as $pf) {
                if ($pf['done'] == 1)
                        $done = 'no';
                else
                        $done = 'yes';

                $feedback = createFeedbackComment($done, $pf['how_long'], $pf['tried'], $pf['liked'], $pf['when_return']);
                $purifier = new HTMLPurifier();
                $cleanComment = $purifier->purify($feedback);

                echo '<tr class="feedback">';
                echo '<td class="feedback">' . $pf['time'] . '</td>';
                echo '<td class="feedback">' . nl2br($cleanComment) . '</td>';
                echo '</tr>';
        }

        echo '</table>';
}
?>
