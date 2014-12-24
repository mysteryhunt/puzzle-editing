<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("answers", "Answers");

// Check for answers permissions
if (!canChangeAnswers($uid) && !isApprover($uid)) {
    echo "<div class='errormsg'>You do not have permission for this page.</div>";
    foot();
    exit(1);
}
if (isset($_POST['newAnswer'])) {
    $result = submitAnswersForm(strtoupper($_POST['newAnswer']),$_POST['round']);
    if ($result == FALSE) {
        echo '<div class="errormsg">Error in submitting new answer</div>';
    }
}
if (isset($_POST['newRound'])) {
    $result = submitNewRound($_POST['newRound'], strtoupper($_POST['roundAnswer']));
    if ($result == FALSE) {
        echo '<div class="errormsg">Error in submitting new round</div>';
    }
}
displayAnswers($uid);

// End HTML
foot();

//------------------------------------------------------------------------

function displayAnswers($uid)
{
    $rounds = getRounds();
    if (!$rounds) {
?>
<span class="emptylist">No rounds to list</span>
<?php
    }
    foreach($rounds as $round) {
        $answers = getAnswersForRound($round['rid']);
?>
        <table class="boxed">
        <tr><th colspan="6"><b><?php echo "{$round['name']}: {$round['answer']}"; ?></b></th></tr>
            <tr>
                <td><b>Answer</b></td>
                <td><b>ID</b></td>
                <td><b>Title</b></td>
                <td><b>Status</b></td>
                <td><b>Editor Notes</b></td>
                <td><b>Status Notes</b></td>
            </tr>
    <?php
        if (!$answers) {
    ?>
            <tr><td colspan="6"><span class="emptylist">No answers added yet</span></td></tr>
    <?php
        }
        foreach($answers as $answer) {
            $pid = $answer['pid'];
    ?>
            <tr><td><?php echo $answer['answer'] ?></td>
                <td><?php echo ($pid ? "<a href=\"puzzle.php?pid=$pid\">".$pid."</a>" : "unassigned") ?></td>
                <td><?php echo ($pid ? getTitle($pid) : "") ?></td>
                <td><?php echo ($pid ? getStatusNameForPuzzle($pid) : "") ?></td>
                <td><?php echo ($pid ? getEditorNotes($pid) : "") ?></td>
                <td><?php echo ($pid ? getNotes($pid) : "") ?></td>
            </tr>
    <?php
        }
    ?>

            <tr>
                <form method="post" action="answers.php" />
                <td><input type="text" name="newAnswer" />
                <input type="hidden" name="round" value='<?php echo $round['rid']; ?>'/></td>
                <td colspan="5"><input type="submit" value='Add Answer For Round <?php echo ($round['rid']); ?>' /></td></form>
            </tr>
        </table>
        <br />
<?php
    }
?>
    <table class="boxed">
        <tr><th colspan="3"><b>New Round</b></th></tr>
        <tr>
            <td>Round Name</td><td>Meta Answer Word</td><td></td>
        </tr>
        <tr>
            <form method="post" action="answers.php" />
            <td><input type="text" name="newRound" /></td>
            <td><input type="text" name="roundAnswer" /></td>
            <td><input type="submit" value="Add New Round" /></td></form>
        </tr>
    </table>
<?php
}

function submitAnswersForm($newAnswer, $round)
{
    if ($newAnswer == "") {
        echo("<div class='errormsg'>Blank Answer is unacceptable. Try again</div>\n");
        return FALSE;
    }

    createAnswer($newAnswer, $round);
    printf("<div class='okmsg'>Added new Answer: %s for Round %s</div>\n", htmlspecialchars($newAnswer), $round);
    return TRUE;
}

function submitNewRound($roundname,$roundanswer)
{
    if ($roundname == "") {
        printf("<div class='errormsg'>Blank Round Name is unacceptable. Try again</div>\n");
        return FALSE;
    }
    if ($roundanswer == "") {
        printf("<div class='errormsg'>Blank Round Answer is unacceptable. Try again</div>\n");
        return FALSE;
    }

    createRound ($roundname, $roundanswer);
    printf("<div class='okmsg'>Added new Round: %s with meta answer: %s</div>\n", htmlspecialchars($roundname), htmlspecialchars($roundanswer));
    return TRUE;
}
?>
