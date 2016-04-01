<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Check for puzzle id
if (!isset($_GET['pid'])) {
    head("", "Error");
    echo "Puzzle ID not found. Please try again.";
    foot();
    exit(1);
}

$pid = $_GET['pid'];

// Start HTML
head("", "Puzzle $pid: Testsolving");

// Check permissions
if (!hasTestAdminPermission($uid)) {
    if (!isTesterOnPuzzle($uid, $pid) && !isFormerTesterOnPuzzle($uid, $pid)) {
        if (!canTestPuzzle($uid, $pid)) {
            echo "You do not have permission to test this puzzle.";
            foot();
            exit(1);
        } else {
            addPuzzleToTestQueue($uid, $pid);
        }
    }
}

$title = getTitle($pid);
if ($title == NULL) {
    $title = '(untitled)';
}
echo "<h2>Puzzle $pid &mdash; $title</h2>";
echo "<strong class='impt'>IMPORTANT:</strong> <b>Please leave feedback! We
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

function maybeDisplayWarning($uid, $pid) {
    if (isTesterOnPuzzle($uid, $pid)) {
        return;
    }
?>
        <div class="warning">
                <strong class='impt'>WARNING:</strong> You are not marked as a current testsolver.<br>
                Please use the puzzle version, and wiki page, that were current when you started solving, NOT the ones listed below (if they differ).<br/>
                If in doubt, email <?php echo HELP_EMAIL; ?>
        </div>
<?php
}

function displayWikiPage($pid) {
    echo '<div>';
    $page = getWikiPage($pid);
    if ($page == NULL) {
        echo "<span class='testempty'>No Testsolve Wiki Page</span>";
    } else {
        echo "<span class='testdesc'>Testsolve wiki page: <a href='$page'>$page</a></span>";
    }
    echo '</div>';
}

function displayDraft($pid) {
    echo '<div>';
    $draft = getMostRecentDraftForPuzzle($pid);

    if ($draft == NULL) {
        echo '<span class="testempty">No Draft</span>';
    } else {
        $finfo = pathinfo($draft['filename']);
        if (isset($finfo['extension'])) {
            $ext = $finfo['extension'];
        } else {
            $ext = 'folder';
        }
        if (strpos($draft['filename'], 'http') !== false || !USING_AWS) {
            $link = $draft['filename'];
        } elseif (strpos($draft['filename'], '_dir', strlen($draft['filename']) - 4) !== false) {
            $link = AWS_ENDPOINT . AWS_BUCKET . '/' . $draft['filename'] . '/index.html';
        } else {
            $link = AWS_ENDPOINT . AWS_BUCKET . '/' . $draft['filename'];
        }
?>
        <span class="testdata">
            Puzzle: <a href="<?php echo $link; ?>"><?php echo $finfo['basename']; ?></a>
            <br/>
            Uploaded on <?php echo $draft['date']; ?>
        </span>
<?php
    }
    echo '</div>';
}

function checkAnsForm($uid, $pid) {
?>
    <form method="post" action="form-submit.php">
        Check an answer:
        <?php if (!getAnswersForPuzzle($pid)) { echo "<strong>Answer not in Puzzletron, so this will always reject your answer.</strong>"; } ?>
        <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
        <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
        <input type="input" name="ans" />
        <input type="submit" name="checkAns" value="Check" />
    </form>
<?php
}

function displayPrevAns($uid, $pid) {
    $answers = getAnswerAttempts($uid, $pid);
    if (!$answers) {
        return;
    }
    $correct = getCorrectSolves($uid, $pid);
    if ($correct) {
        echo "<h3>Correct Answers: $correct</h3>";
    }
    echo '<h3>Attempted Answers:</h3>';
    echo '<ul>';

    foreach ($answers as $ans) {
        echo "<li>$ans</li>";
    }
    echo '</ul>';
}

function displayFeedbackForm($uid, $pid) {
?>
        <h3>Feedback Form</h3>
<?php
    if (ANON_TESTERS) {
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

        <form method="post" action="form-submit.php" class="boxedform">
        <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
        <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
        <p>
            Do you intend to return to this puzzle?<br/>
            <input type="radio" name="done" value="yes" /> Yes<br/>
            <input type="radio" name="done" value="notype" /> No, this isn't a puzzle type I like.<br/>
            <input type="radio" name="done" value="nostuck" /> No, I'm not sure what to do and don't feel like working on it anymore.<br/>
            <input type="radio" name="done" value="nofun" /> No, I think I know what to do but it isn't fun/I'm not making progress.<br/>
            <input type="radio" name="done" value="nospoiled" /> No, I was already spoiled on this puzzle<br/>
            <input type="radio" name="done" value="nodone" /> No, I've solved it.<br/>
            <input type="radio" name="done" value="no" /> No (please give reason in the comments)<br/>
            <br><small>(Selecting "No" marks you as finished
            in the database. This is important for
            our records.)</small>
        </p>
        <p>
            If so, when do you plan to return to it?
            <input type="text" name="when_return" />
        </p>
        <p>
            How long did you spend on this puzzle (since your last feedback, if any)?
            <input type="text" name="time" />
        </p>
        <p>
            Describe what you tried. <br />
            <textarea style="width:50em; height: 10em;" name="tried"></textarea>
        </p>
        <p>
            What did you like/dislike about this puzzle?<br />
            Is there anything you think should be changed with the puzzle?<br />
            Is there anything wrong with the technical details/formatting of the puzzle?<br />
            <textarea style="width:50em; height: 25em;" name="liked"></textarea>
        </p>
        <p>
            Were there any special skills required to solve this puzzle?<br />
            <textarea style="width:50em; height: 3em;" name="skills"></textarea>
        </p>
        <p>
            Describe a breakthrough point and what in the puzzle lead you to it:<br />
            <textarea style="width:50em; height: 5em;" name="breakthrough"></textarea>
        </p>
        <p>
            Rate the overall fun of this puzzle (where 1 is the least fun, and 5 is the most fun): <SELECT NAME="fun">
                <OPTION VALUE="0" SELECTED>-</OPTION>
                <OPTION>1</OPTION>
                <OPTION>2</OPTION>
                <OPTION>3</OPTION>
                <OPTION>4</OPTION>
                <OPTION>5</OPTION>
            </SELECT>
        </p>
        <p>
            Rate the overall difficulty of this puzzle (where 1 is the easiest, and 5 is the most difficult): <SELECT NAME="difficulty">
                <OPTION VALUE="0" SELECTED>-</OPTION>
                <OPTION>1</OPTION>
                <OPTION>2</OPTION>
                <OPTION>3</OPTION>
                <OPTION>4</OPTION>
                <OPTION>5</OPTION>
            </SELECT>
        <p>
            <input type="submit" name="feedback" value="Submit Feedback" class="okSubmit" />
        </p>
        </form>
<?php
}

function displayPrevFeedback($uid, $pid) {
    $prevFeedback = getPreviousFeedback($uid, $pid);
    if (!$prevFeedback) {
        return;
    }
    echo '<h3>Previous Feedback</h3>';
    echo '<table>';

    foreach ($prevFeedback as $pf) {
        if ($pf['done'] == 0) {
            $done = 'Yes';
        } elseif ($pf['done'] == 1) {
            $done = 'No';
        } elseif ($pf['done'] == 2) {
            $done = 'No, this isn\'t a puzzle type I like.';
        } elseif ($pf['done'] == 3) {
            $done = 'No, I\'m not sure what to do and don\'t feel like working on it anymore.';
        } elseif ($pf['done'] == 4) {
            $done = 'No, I think I know what to do but it isn\'t fun/I\'m not making progress.';
        } elseif ($pf['done'] == 5) {
            $done = 'No, I was already spoiled on this puzzle';
        } elseif ($pf['done'] == 6) {
            $done = 'No, I\'ve solved it.';
        }

        $feedback = createFeedbackComment($done, $pf['how_long'], $pf['tried'], $pf['liked'], $pf['skills'], $pf['breakthrough'], $pf['fun'], $pf['difficulty'], $pf['when_return']);
        $purifier = getHtmlPurifier();
        $cleanComment = $purifier->purify($feedback);

        echo '<tr class="feedback">';
        echo '<td class="feedback">' . $pf['time'] . '</td>';
        echo '<td class="feedback">' . nl2br2($cleanComment) . '</td>';
        echo '</tr>';
    }

    echo '</table>';
}
