<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

       // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head("", "Testsolving Statistics");




function computeComments()
{
	$a = array();

        $comments = getTestCommentsAll();
        if (!$comments)
                return;


        foreach ($comments as $comment)
        {
                $id = $comment['id'];
                $pid = $comment['pid'];
                $timestamp = $comment['timestamp'];
                $type = $comment['name'];
                $user = $comment['uid'];

                if ($user == 0)
                        $name = 'Server';
                else
                       $name = getUserName($user);




		if (!(isset($a[$name])))
 		{
		        $a[$name] = array ('name' => $name, 'total' => 0, 'correct' => 0, 'incorrect' => 0, 'cordif' => 0, 'responses' => 0, 'words' => 0);
		}




		$ct = $comment['comment'];



		if ($ct == 'Added testsolver')
                {
                       $a[$name]['total'] += 1;
                }
		elseif (strpos($ct, 'Correct answer attempt') === 0)
		{
			$a[$name]['correct'] += 1;
		}
		elseif (strpos($ct, 'Incorrect answer attempt') === 0)
		{
			$a[$name]['incorrect'] += 1;
		}
		else   # feedback response
		{
		    $a[$name]['responses'] += 1;

		       $offset = 0;
			if (strpos($ct, 'What was the breakthrough') !== FALSE)
			  $offset = 164;
			else
			  $offset = 92;


		       	$a[$name]['words'] += str_word_count($ct) - $offset;		
		}






        }
	










        echo '
        <script type="text/javascript">
        $(document).ready(function() {
            // call the tablesorter plugin
            $("#solverstats").tablesorter({
                sortList: [[3,1],[0,0]]
            });
        });
        </script>
        ';


        echo ' <table id="solverstats" class="tablesorter">
                 <thead>
                        <tr>
                                <th class="puzzidea">Solver</th>
                                <th class="puzzidea">Puzzles Viewed</th>
                                <th class="puzzidea">Proportion Solved</th>
                                <th class="puzzidea">Correct Guesses</th>
                                <th class="puzzidea">Incorrect Guesses</th>
                                <th class="puzzidea">Proportion of Guesses Correct</th>
                                <th class="puzzidea">Feedback Responses</th>
                                <th class="puzzidea">Total Feedback Word Count</th>
                                <th class="puzzidea">Average Feedback Word Count</th>
                       </tr>
            </thead>
           <tbody>';
   foreach ($a as $person)
   {
      echo '<tr class="puzz">';
      echo '<td class="puzzidea">' . $person['name'] . '</td>';
      echo '<td class="puzzidea">' . $person['total'] . '</td>';
      if ($person['total']  == 0) {
          # Prevent division by zero.
          $person['total'] = 1;
      }
      echo '<td class="puzzidea">' . sprintf('%0.3f',($person['correct']/$person['total'])) . '</td>';
      echo '<td class="puzzidea">' . $person['correct'] . '</td>';
      echo '<td class="puzzidea">' . $person['incorrect'] . '</td>';
      if ($person['correct'] + $person['incorrect'] == 0) {
          # Prevent division by zero.
          $person['incorrect'] = 1;
      }
      echo '<td class="puzzidea">' . sprintf('%0.3f',($person['correct']/($person['correct']+$person['incorrect']))) . '</td>';
      echo '<td class="puzzidea">' . $person['responses'] . '</td>';
      echo '<td class="puzzidea">' . $person['words'] . '</td>';
      if ($person['responses'] == 0) {
          # Prevent division by zero.
          $person['responses'] = 1;
      }
      echo '<td class="puzzidea">' . sprintf('%0.2f',($person['words']/$person['responses'])) . '</td>';
      echo '</tr>';
   }


   echo '</tbody></table>';



}

function getTestCommentsAll()
{
        $sql = sprintf("SELECT comments.id, comments.uid, comments.comment, comments.type,
                                        comments.timestamp, comments.pid, comment_type.name FROM
                                        comments LEFT JOIN comment_type ON comments.type=comment_type.id
                                        WHERE comment_type.name='Testsolver' ORDER BY comments.id ASC",
                                        mysql_real_escape_string($pid));
        return get_rows($sql);
}

  computeComments();
?>
