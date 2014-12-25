<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("answerlist", "Answer List");

if (isset($_GET['rid'])) {
    displayAnswersClassifiedByRound($uid, $_GET['rid']);
} else {
    displayAnswers($uid);
}

// End HTML
foot();

//------------------------------------------------------------------------

function displayAnswers($uid)
{
    $answers = getAvailableAnswers();
?>
    <table class="boxed">
    <tr><th><b>Available Answers</b></th></tr>
        <?php foreach ($answers as $answer) { ?>
            <tr><td><?php echo $answer ?></td></tr>
        <?php } ?>
    </table>
<?php
}
function displayAnswersClassifiedByRound($uid, $rid)
{
    $answers = getAvailableAnswersForRound($rid);
?>
    <table class="boxed">
    <tr><th><b>Available Answers For Round <?php echo $rid; ?></b></th></tr>
        <?php foreach ($answers as $answer) { ?>
             <tr><td><?php echo $answer ?></td></tr>
        <?php } ?>
    </table>
    <table class="boxed">
    <tr><th><b>Available Answers Not For Round <?php echo $rid; ?></b></th></tr>
        <?php
        $answers = getAvailableAnswersNotForRound($rid);
        foreach ($answers as $answer) { ?>
            <tr><td><?php echo $answer ?></td></tr>
        <?php } ?>
    </table>
<?php } ?>
