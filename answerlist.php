<?php
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("answerlist", "Answer List");

displayAnswers($uid);

// End HTML
foot();

//------------------------------------------------------------------------

function displayAnswers($uid)
{
  $answers = getAvailableAnswers();
?>
  <table class="boxed">
     <tr><th><b>Available Answers</b></th></tr>
<?php
  foreach($answers as $answer) {
?>
     <tr><td><?php echo $answer ?></td></tr>
<?php
  }
?>
  </table>
<?php
}
?>
