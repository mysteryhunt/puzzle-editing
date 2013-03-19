<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("answers");

        // Check for answers permissions
        if (!isEditor($uid) && !isLurker($uid)) {
                echo "You do not have permission for this page.";
                foot();
                exit(1);
        }
        if (isset($_POST['newAnswer'])) {
                $result = submitAnswersForm(strtoupper($_POST['newAnswer']),$_POST['round']);
                if ($result == FALSE) {
                        echo '<h3> Error in submitting new answer </h3>';
                }
        }
        if (isset($_POST['newRound'])) {
                $result = submitNewRound($_POST['newRound'], strtoupper($_POST['roundAnswer']));
                if ($result == FALSE) {
                        echo '<h3> Error in submitting new round </h3>';
                }
        } 
        displayAnswers($uid);

        // End HTML
        foot();

//------------------------------------------------------------------------

        function displayAnswers($uid)
        {
                $rounds = getRounds();
?>
<?php
                foreach($rounds as $round) {
                    $answers = getAnswersForRound($round['rid']);
?>
                <table style="border:3px solid black;">
                    <tr>
                            <th colspan="4"><b><?php echo "{$round['name']}: {$round['answer']}"; ?></b></th>
                    </tr>
<?php
                    foreach($answers as $answer) {
                        $pid = $answer['pid'];
?>
                        <tr>
                                <td><?php echo $answer['answer'] ?></td>
			        <td><?php echo ($pid ? "<a href=\"puzzle.php?pid=$pid\">".$pid."</a>" : "unassigned") ?></td>
                                <td><?php echo ($pid ? getTitle($pid) : "") ?></td>
                                <td><?php echo ($pid ? getStatusNameForPuzzle($pid) : "") ?></td>
                        </tr>
<?php
                    }
?>

                    <tr>
                                <form method="post" action="answers.php" />
                                <td><input type="text" name="newAnswer" />
                                <input type="hidden" name="round" value='<?php echo $round['rid']; ?>'/></td>
                                <td colspan=3><input type="submit" value='Add Answer For Round <?php echo ($round['rid']); ?>' /></td></form>
                    </tr>
               </table>
               <br />
<?php  
              }
?>
                <table style="border:3px solid black;">
                    <tr>
                                <th colspan="3"><b>New Round</b></th>
                    </tr>
                    <tr>
                                <td>Round Name</td><td>Meta Answer Word</td>
                    </tr>
                    <tr>
                                <form method="post" action="answers.php" />
                                <td><input type="text" name="newRound" /></td>
                                <td><input type="text" name="roundAnswer" /></td>
                                <td><input type="submit" value="Add New Round" /></td></form>
                    </tr>
                <table>

<?php
        }

        function submitAnswersForm($newAnswer, $round)
        {		
                if ($newAnswer == "") {
                        printf("Blank Answer is unacceptable. Try again \n");
                        return (FALSE);
                }
                
                createAnswer ($newAnswer, $round);
                printf ("Added new Answer: %s for Round %s \n", $newAnswer, $round);  
                return(TRUE);
        }

        function submitNewRound($roundname,$roundanswer)
        {
                if ($roundname == "") {
                        printf("Blank Round Name is unacceptable. Try again \n");
                        return (FALSE);
                }
                if ($roundanswer == "") {
                        printf("Blank Round Answer is unacceptable. Try again \n");
                        return (FALSE);
                }
        
                createRound ($roundname, $roundanswer);
                printf ("Added new Round: %s with meta answer: %s\n", $roundname, $roundanswer);
                return(TRUE);
        }
?>

