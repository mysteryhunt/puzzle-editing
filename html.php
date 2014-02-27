<?php
        require_once "config.php";
        require_once "utils.php";

        function echoNav($isselected, $href, $linktext, $condition) {
                if ($condition) {
                        $navclass = $isselected ? "selnav" : "nav";
                        echo "<li class='nav'><a class='$navclass' href='$href'>$linktext</a></li>\n";
                }
        }
        function echoNav1($selnav, $name, $linktext, $condition) { echoNav($selnav == $name, $name . ".php", $linktext, $condition); }
        function head($selnav = "") {
        $hunt=mktime(12,00,00,1,HUNT_DOM,HUNT_YEAR);
        $now = time();
        $timediff = abs($hunt-$now);
        $days = floor($timediff/(60 * 60 * 24));
        $hrs = floor($timediff/(60 * 60))-(24*$days);
        $mins = floor($timediff/(60))-(24*60*$days)-(60*$hrs);
	if ($now > $hunt) {
	    $days *= -1;
	    $hrs *= -1;
	    $mins *= -1;
        }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <link rel="icon" type="image/vnd.microsoft.icon" href="favicon.ico" />

        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="css/reset-min.css" />
        <link rel="stylesheet" type="text/css" href="css/base-min.css" />
        <link rel="stylesheet" type="text/css" href="css/fonts-min.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <?php if ($selnav == "people" || $selnav == "account") { ?> <link rel="stylesheet" type="text/css" href="css/profiles.css" /> <?php } ?>

	<title>MH2015 puzzletron authoring server
	<?php if (DEVMODE) { ?> 
		(test/dev instance) 
	<?php } else if (PRACMODE) { ?> 
		(practice instance) 
	<?php } else { ?> 
		(actual mystery hunt-writing instance) 
	<?php } ?> </title>
 
        <script type='text/javascript' src='jquery-1.4.2.js'></script>
        <script type='text/javascript' src='jquery.tablesorter.min.js'></script>
        <script type="text/javascript" src='js.js'></script>
</head>
<body>
<div id="container">
	<div id="header" style="margin-top:10px;">
          <div id="titletext" style="vertical-align:middle; margin-bottom:4px;">
                                <div style="text-align:left;width:auto;float:left;vertical-align:top;">
                     <h1>MH2015 puzzletron authoring server
        <?php if (DEVMODE) { ?>                 
		(test/dev instance)         
	<?php } else if (PRACMODE) { ?>
                (practice instance) 
        <?php } else { ?> 
                (actual mystery hunt-writing instance) 
        <?php } ?>
		     </h1>
        <?php if (isset($_SESSION['uid'])) { ?>
                <h2>Logged in: [<?php echo getUserUsername(isLoggedIn()); ?>]</h2>
        <?php } else { ?>
                <h2>Not logged in</h2>
        <?php } ?>
                </div>
                <div style="text-align:right;width:auto;float:right;vertical-align:top;">
                     <h3 style="margin-top:0;"> <span class="red"><?php echo $days ?></span> days, <span class="red"><?php echo $hrs ?></span> hours and <span class="red"><?php echo $mins ?></span> minutes left until hunt.</h3>
                                </div>
                <div style="clear:both;"></div>
                   </div>
                  <div id="navbar" style="float:left;width:100%;background-color:#efefef;">
                <ul class="nav" style="float:left;">
			<li class="nav"><a class="nav" target="_blank" href="
<?php echo WIKI_URL; ?> ">Wiki</a></li>
<?php
        echoNav($selnav == "home", "index.php", "Home", true);
        if (isset($_SESSION['uid'])) {
                $suid = $_SESSION['uid'];
                echoNav1($selnav, "people",         "People",              true);
                echoNav1($selnav, "account",        "Your Account",        true);
                echoNav1($selnav, "admin",          "Admin",               isServerAdmin($suid));
                echoNav1($selnav, "author",         "Author",              true);
                echoNav1($selnav, "roundcaptain",   "Round Captain",       isRoundCaptain($suid));
                echoNav1($selnav, "spoiled",        "Spoiled",             true);
                echoNav1($selnav, "editor",         "Editor",              isEditor($suid));
                echoNav1($selnav, "ffc",            "Final Fact Check",    true);
                echoNav1($selnav, "factcheck",      "Fact Check",          true);
                echoNav1($selnav, "testsolving",    "Testsolving",         true);
                echoNav1($selnav, "testadmin",      "Testing Admin",       isTestingAdmin($suid));
                echoNav1($selnav, "testsolveteams", "TS Team Assignments", (USING_TESTSOLVE_TEAMS) && isTestingAdmin($suid));
                echoNav1($selnav, "answers",        "Answers",             canChangeAnswers($suid));
                echoNav1($selnav, "allpuzzles",     "All Puzzles",         canSeeAllPuzzles($suid));
                echoNav1($selnav, "editorlist",     "Editor List",         isServerAdmin($suid));
		echoNav1($selnav, "mailinglists",   "Mailing Lists",	   MAILING_LISTS);
        }
?>
                </ul>
			<?php if(!TRUST_REMOTE_USER) { ?> <div style="float:right;"><?php if (isset($_SESSION['uid'])) { ?><a class="nav" href="logout.php">Logout</a><?php } ?> </div> <?php } ?>
                </div>
                        <div style="clear:both;"></div>
        </div>
        <div id="body">
<?php
        }

        function foot()
        {
?>
        </div>
        <div id="footer">
                <hr />
		<p>This is the website for the hunt writing team. For technical assistance, please contact the <a href="mailto:
<?php echo HELP_EMAIL; ?>
">Server Administrators</a>.  The original authors of this software are Kate Baker and Metaphysical Plant.  This software is available <a href="http://github.com/mysteryhunt/puzzle-editing/">on GitHub</a> under the Simplified BSD license.  The copyrights for the puzzles and comments contained herein are retained by the puzzle authors.</p>
        </div>
</div>
</body>
</html>

<?php
        }

        function printPerson($p)
        {
                $id = $p['uid'];
                $uname = $p['username'];
                $picture = $p['picture'];
                $fullname = $p['fullname'];
                $email = $p['email'];

                if (strncmp($uname, "test", 4) == 0) {
                        // Ignore test users.
                        return;
                }

                $pic = "<img src=\"nophoto.gif\" />";
                if ($picture != "") {
                        $picsrc = "uploads/pictures/thumbs/$id.jpg";
                        if (file_exists($picsrc))
                                $pic = "<img src=\"".$picsrc."\" />";
                }

                $jobNames = getUserJobsAsList($id);
                ?>
                <div class="<?php echo ($jobNames) ? "specprofilebox" : "profilebox"; ?>">
                        <div class="profileimg"><?php echo $pic ?></div>
                        <div class="profiletxt">
                                <span class="profilename"><?php echo "$fullname"; ?> (<?php echo "$uname"; ?>)</span>
                                <span class="profiletitle"><?php echo $jobNames; ?></span>
                                <span class="profilecontact"><a href="mailto:<?php echo $email ?>"><?php echo $email ?></a></span>
<?php
                        $sql = "SELECT * FROM user_info_key";
                        $result = get_rows($sql);
                        foreach ($result as $r) {
                                $shortname = $r['shortname'];
                                $longname = $r['longname'];
                                $user_key_id = $r['id'];
                                $sql = sprintf("SELECT value FROM user_info_values WHERE person_id = '%s' AND user_info_key_id = '%s'",
                                               mysql_real_escape_string($id), mysql_real_escape_string($user_key_id));
                                $res = get_rows($sql);
                                if ($res[0]['value'] != "") {
?>
                                        <span class="profilesect"><?php echo "<b>$longname</b>: " . $res[0]['value']; ?></span>
<?php
                                }
                        }
?>
                        </div>
                        <div class="profilefooter"></div>
                </div>
<?php
        }

        function displayQueue($uid, $puzzles, $showNotes, $showAnswerAndSummary, $showAuthorsAndEditors, $test, $showTesters, $hidedeadpuzzles, $filter = array())
        {
                if (!$puzzles) {
                        echo "<span class='emptylist'>No puzzles to list</span><br/>";
                        return;
                }
                $statuses = getPuzzleStatuses();

                $deadstatusid = getDeadStatusId();
                
?>
                <table class="tablesorter">
                <thead>
                        <tr>
                                <th class="puzzidea">ID</th>
                                <?php if (USING_CODENAMES) {echo '<th class="puzzidea">Codename</th>';} ?>
                                <th class="puzzidea">Title</th>
                                <th class="puzzidea">Puzzle Status</th>
                                <th class="puzzidea">Round</th>
                                <?php if ($showAnswerAndSummary) {echo '<th class="puzzidea">Summary</th>';} ?>
                                <?php if ($showNotes) {echo '<th class="puzzidea">Status Notes</th>';} ?>
				<?php if ($showNotes) {echo '<th class="puzzidea">Runtime Info</th>';} ?>
                                <?php if ($showAnswerAndSummary) {echo '<th class="puzzidea">Answer</th>';} ?>
                                <?php if (!$test) { echo '<th class="puzzidea">Last Commenter</th>';} ?>
                                <?php if (!$test) { echo '<th class="puzzidea">Last Comment</th>';}?>
                                <?php if ($showAuthorsAndEditors) {echo '<th class="puzzidea">Authors</th>';} ?>
                                <?php if ($showAuthorsAndEditors) {echo '<th class="puzzidea">Editors</th>';} ?>
                                <?php if ($showAuthorsAndEditors) {echo '<th class="puzzidea">Approvers</th>';} ?>
                                <?php if ($showAuthorsAndEditors) {echo '<th class="puzzidea">Approvals</th>';} ?>
                                <?php if ($showTesters) {echo '<th class="puzzidea">Testers</th>';} ?>
                                <?php if ($showTesters) {echo '<th class="puzzidea">Last Test Report</th>';} ?>
                                <?php if (($showTesters) && (USING_TESTSOLVE_REQUESTS)){echo '<th class="puzzidea">Testsolve requests</th>';} ?>
                        </tr>
                </thead>
                <tbody>
<?php


                foreach ($puzzles as $pid) {
                        $puzzleInfo = getPuzzleInfo($pid);

                        // This is totally the wrong way to do this. The right way involves
                        // writing SQL.
                        if ($filter) {
                                if ($filter[0] == "status" && $filter[1] != $puzzleInfo["pstatus"]) {
                                  continue;
                                }
                                if ($filter[0] == "author" && !isAuthorOnPuzzle($filter[1], $pid)) {
                                  continue;
                                }
                                if ($filter[0] == "editor" && !isEditorOnPuzzle($filter[1], $pid)) {
                                  continue;
                                }
                                if ($filter[0] != "status" && $hidedeadpuzzles && $puzzleInfo["pstatus"] == $deadstatusid) {
                                  continue;
                                }
                        }
                        else if ($hidedeadpuzzles && $puzzleInfo["pstatus"] == $deadstatusid) {
                                continue;
                        }

                        $title = $puzzleInfo["title"];
                        if ($title == NULL)
                                $title = '(untitled)';


                        $codename = getCodename($pid);
                        $lastComment = getLastCommentDate($pid);
                        $lastCommenter = getLastCommenter($pid);
                        $lastVisit = getLastVisit($uid, $pid);

                        if (($lastVisit == NULL || strtotime($lastVisit) < strtotime($lastComment)) || $test)
                                echo '<tr class="puzz-new">';
                        else
                                echo '<tr class="puzz">';

                        if ($test)
				echo "<td class='puzzidea'><a href='test.php?pid=$pid'>$pid</a></td>";
                        else
				echo "<td class='puzzidea'><a href='puzzle.php?pid=$pid'>$pid</a></td>";
?>
                                <?php if (USING_CODENAMES) {echo '<td class="puzzidea">' . $codename . '</th>';} ?>
                                <td class='puzzidea'><?php echo $title; ?></td>
                                <td class='puzzidea'><?php echo $statuses[$puzzleInfo["pstatus"]]; ?></td>
                                <td class='puzzidea'><?php echo getPuzzleRound($pid); ?></td>
                                <?php if ($showAnswerAndSummary) {echo "<td class='puzzideasummary'>" . $puzzleInfo["summary"] . "</td>";} ?>
                                <?php if ($showNotes) {echo "<td class='puzzidea'>" . $puzzleInfo["notes"] . "</td>";} ?>
				<?php if ($showNotes) {echo "<td class='puzzidea'>" . $puzzleInfo["runtime_info"] . "</td>";} ?>
                <?php 
                if ($showAnswerAndSummary) { 
                        if (getAnswersForPuzzleAsList($pid) != "") { 
                                echo "<td class='puzzideasecure'>"; 
                        } else  
                                echo "<td class='puzzidea'>"; 
                        echo getAnswersForPuzzleAsList($pid) . "</td>";
                } ?>
                                <?php if (!$test) {echo "<td class='puzzidea'>$lastCommenter</td>";} ?>
                                <?php if (!$test) {echo "<td class='puzzidea'>$lastComment</td>";} ?>
                                <?php if ($showAuthorsAndEditors) {echo "<td class='puzzidea'>" . getAuthorsAsList($pid) . "</td>";} ?>
                                <?php if ($showAuthorsAndEditors) {echo "<td class='puzzidea'>" . getEditorsAsList($pid) . "</td>";} ?>
                                <?php if ($showAuthorsAndEditors) {echo "<td class='puzzidea'>" . getApproversAsList($pid) . "</td>";} ?>
                                <?php if ($showAuthorsAndEditors) {echo "<td class='puzzidea'>" . countPuzzApprovals($pid) . "</td>";} ?>
                                <?php if ($showTesters) {echo "<td class='puzzidea'>" . getCurrentTestersAsList($pid) . "</td>";} ?>
                                <?php if ($showTesters) {echo "<td class='puzzidea'>" .  getLastTestReportDate($pid) . "</td>";} ?>
                                <?php if (($showTesters) && (USING_TESTSOLVE_REQUESTS)) {echo "<td class='puzzidea'>" .  getTestsolveRequestsForPuzzle($pid) . "</td>";} ?>
                        </tr>
<?php
                }
?>
                </tbody>
                </table>
<?php
        }

// Make groups of checkboxes
// Takes an associative array and the name of the form element
function makeOptionElements($toDisplay, $name)
{
        if (!$toDisplay) {
                echo '<em>(none)</em>';
                return;
        }

        $maxLength = 5;
        $maxCol = 10;

        // Figure out how many columns are necessary to maintain max length
        // Use maxCol to keep from having too many columns
        $numCol = min(ceil(count(array_keys($toDisplay))/$maxLength), $maxCol);

        $i = 1;
        echo '<table>';
        foreach ($toDisplay as $key => $value) {
                if ($key == NULL)
                        continue;

                // Start a new row, if necessary
                if (($i % $numCol) == 1)
                        echo '<tr>';

                // Add answer information
                echo '<td>';
                echo "<input type='checkbox' name='$name" . "[]' value='$key' />";
                echo '</td>';
                echo "<td>$value</td>";

                // End row, if number of columns reached
                if (($i % $numCol) == 0)
                        echo '</tr>';

                $i++;
        }

        // Close last row, if necessary
        if (($i % $numCol) != 1)
                echo '</tr>';
        echo '</table>';
}

function displayPuzzleStats($uid)
{
        $max_rows = 6;

        $totalNumberOfPuzzles = count(getAllLivePuzzles());
        $numberOfEditors = getNumberOfEditorsOnPuzzles();
        $moreThanThree = $totalNumberOfPuzzles - $numberOfEditors['0'] - $numberOfEditors['1'] - $numberOfEditors['2'] - $numberOfEditors['3'];

        $userNumbers = getNumberOfPuzzlesForUser($uid);

        $editor = $userNumbers['editor'];

        $tester = $userNumbers['currentTester'];
        if ($userNumbers['doneTester'] > 0)
                $tester .= ' (+' . $userNumbers['doneTester'] . ' done)';

?>
                <table>
                        <tr>
                        <td class="puzz-stats">
                                <table>
                                        <tr>
                                                <th class="puzz-stats" colspan="2"><?php echo $totalNumberOfPuzzles; ?> Total Live Puzzles/Ideas</th>
                                        </tr>
                                        <tr>
                                                <td class="puzz-stats">You Are Editor</td>
                                                <td class="puzz-stats"><?php echo $editor; ?></td>
                                        </tr>
                                        <tr>
                                                <td class="puzz-stats">You Are Author</td>
                                                <td class="puzz-stats"><?php echo $userNumbers['author']; ?></td>
                                        </tr>
                                        <tr>
                                                <td class="puzz-stats">You Are Spoiled</td>
                                                <td class="puzz-stats"><?php echo $userNumbers['spoiled']; ?></td>
                                        </tr>
                                        <tr>
                                                <td class="puzz-stats">You Are Tester</td>
                                                <td class="puzz-stats"><?php echo $tester; ?></td>
                                        </tr>
                                        <tr>
                                                <td class="puzz-stats">Available To Edit</td>
                                                <td class="puzz-stats"><?php echo $userNumbers['available']; ?></td>
                                        </tr>
                                </table>
                        </td>
                        <td class="ed-stats">
                                <table>
                                        <tr>
                                                <th class="ed-stats" colspan="2">Num of Editors</th>
                                        </tr>
                                        <tr>
                                                <td class="ed-stats">Zero</td>
                                                <td class="ed-stats"><?php echo $numberOfEditors['0']; ?></td>
                                        </tr>
                                        <tr>
                                                <td class="ed-stats">One</td>
                                                <td class="ed-stats"><?php echo $numberOfEditors['1']; ?></td>
                                        </tr>
                                        <tr>
                                                <td class="ed-stats">Two</td>
                                                <td class="ed-stats"><?php echo $numberOfEditors['2']; ?></td>
                                        </tr>
                                        <tr>
                                                <td class="ed-stats">Three</td>
                                                <td class="ed-stats"><?php echo $numberOfEditors['3']; ?></td>
                                        </tr>
                                        <tr>
                                                <td class="ed-stats">&gt;Three</td>
                                                <td class="ed-stats"><?php echo $moreThanThree; ?></td>
                                        </tr>
                                </table>
                        </td>
<?php

                $puzzleStatuses = getPuzzleStatuses();
                $pstatusCol = ceil(count($puzzleStatuses) / $max_rows) * 2;

                $statuses = NULL;
                $statusCounts = getPuzzleStatusCounts();
                foreach ($puzzleStatuses as $sid => $name) {
                        $count = (array_key_exists($sid, $statusCounts) ? $statusCounts[$sid] : 0);
                        $status = NULL;
                        $status['id'] = $sid;
                        $status['name'] = $name;
                        $status['count'] = $count;
                        $statuses[] = $status;
                }
?>
                        <td class="p-stats">
                                <table>
                                        <tr>
                                                <th class="p-stats" colspan="<?php echo $pstatusCol; ?>">Puzzle Status</th>
                                        </tr>
<?php
                for ($row = 0; $row < $max_rows; $row++) {
                        for ($col = 0; $col < ($pstatusCol / 2); $col++) {
                                $n = $row + ($col * $max_rows);

                                if ($col==0)
                                        echo '
                                                <tr>';

                                if ($n >= count($puzzleStatuses)) {
                                        echo '
                                                <td></td>';
                                        echo '
                                                <td></td>';
                                } else {
                                        $num = $statuses[$n];
                                        $name = $num['name'];
                                        $count = $num['count'];

                                        echo '
                                                <td class="p-stats">' . $name . '</td>';
                                        echo '
                                                <td class="p-stats">' . $count . '</td>';
                                }

                                if ($col == ($pstatusCol/2 - 1)) {
                                        echo '
                                                </tr>';
                                }
                        }
                }
?>
                                </table>
                        </td>

                        <td class="answer-stats">
                                <table>
                                        <tr>
                                                <th class="answer-stats" colspan="2"> Answer Status</th>
                                        </tr>
                                        <tr>
                                                <td class="answer-stats"> Total Answers </td>
                                                <td class="answer-stats"> <?php echo numAnswers(); ?> </td>
                                        </tr>
                                        <tr>    
                                                <td class="answer-stats"> Assigned </td>
                                                <td class="answer-stats"> <?php echo answersAssigned(); ?> </td>
                                        </tr>
                                        <tr>    
                                                <td class="answer-stats"> Unassigned </td>
                                                <td class="answer-stats"> <?php echo (numAnswers() - answersAssigned()); ?> </td>
                                        </tr>
                                </table>
                        </tr>
                </table>
<?php
        }
?>
