<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "utils.php";

function echoNav($isselected, $href, $linktext, $condition) {
    if ($condition) {
        $navclass = $isselected ? "selnav" : "nav";
        echo "<li class='nav'><a class='$navclass' href='$href'>$linktext</a></li>\n";
    }
}

function echoNav1($selnav, $name, $linktext, $condition) {
    echoNav($selnav == $name, $name . ".php", $linktext, $condition);
}

function fullTitle() {
    return 'MH2015 puzzletron authoring server (' . (DEVMODE ? 'test/dev' : (PRACMODE ? 'practice' : 'actual mystery hunt-writing')) . ' instance)';
}

function head($selnav = "", $title = -1) {
    if ($title == -1) {$title = fullTitle();}
$hunt = mktime(12, 17, 00, 1, HUNT_DOM, HUNT_YEAR);
$now = time();
$timediff = abs($hunt-$now);
$days = (int)($timediff/(60 * 60 * 24));
$hrs = (int)($timediff/(60 * 60))-(24*$days);
$mins = (int)($timediff/(60))-(24*60*$days)-(60*$hrs);
$cdmsg = "";
if ($now > $hunt) {
    $cdmsg = "after hunt started!!!";
    $cdclass = "cunum";
} else {
    $cdmsg = "left until hunt.";
    $cdclass = "cdnum";
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
    <title><?php echo $title; ?></title>
    <script type='text/javascript' src='jquery-1.4.2.js'></script>
    <script type='text/javascript' src='jquery.tablesorter.min.js'></script>
    <script type="text/javascript" src='js.js'></script>
</head>
<body>
<div id="container">
    <div id="header">
        <div id="top">
            <div id="countdowndiv">
                <span id="countdown">
                <?php
                    if ($days !== 0) {
                        $daypl = $days === 1 ? "" : "s";
                        echo "<span class=\"$cdclass\">$days</span> day$daypl, ";
                    }
                    $hrpl =  $hrs  === 1 ? "" : "s";
                    echo "<span class=\"$cdclass\">$hrs</span> hour$hrpl and ";
                    $minpl = $mins === 1 ? "" : "s";
                    echo "<span class=\"$cdclass\">$mins</span> minute$minpl $cdmsg";
                ?>
                </span>
            </div>
            <div id="titlediv">
                <h1><?php echo fullTitle(); ?></h1>
            </div>
            <div id="logindiv">
<?php if (isset($_SESSION['uid'])) {
    echo 'Logged in as <strong>' . getUserUsername($_SESSION['uid']) . '</strong>';
    echo '<a href="account.php"' . ($selnav == "account" ? ' class="accsel"' : "") . '>Your Account</a>';
    if (MAILING_LISTS) { echo '<a href="mailinglists.php"' . ($selnav == "mailinglists" ? ' class="accsel"' : "") . '>Mailing Lists</a>'; }
    if (!TRUST_REMOTE_USER) { echo '<a href="logout.php">Logout</a>'; }
} else { ?>
                <span class="notloggedin">Not logged in</span> <a href="login.php">Login</a>
        <?php } ?>
            </div>
        </div>
        <div id="navbar">
            <ul class="nav">
                <li class="nav"><a class="nav wikinav" target="_blank" href="<?php echo WIKI_URL; ?> ">Wiki</a></li>
<?php
echoNav($selnav == "home", "index.php", "Home", true);

if (isset($_SESSION['uid'])) {
    $suid = $_SESSION['uid'];
    echoNav1($selnav, "people",         "People",              true);
    echoNav1($selnav, "admin",          "Admin",               isServerAdmin($suid));
    echoNav1($selnav, "author",         "Author",              true);
    echoNav1($selnav, "roundcaptain",   "Round Captain",       (USING_ROUND_CAPTAINS) && isRoundCaptain($suid));
    echoNav1($selnav, "spoiled",        "Spoiled",             true);
    echoNav1($selnav, "editor",         "Discussion Editor",   isEditor($suid));
    echoNav1($selnav, "approver",       "Approval Editor",     (USING_APPROVERS) && (isApprover($suid) || isEditorChief($suid)));
    echoNav1($selnav, "testsolving",    "Testsolving",         true);
//    echoNav1($selnav, "factcheck",      "Fact Check",          true);
    echoNav1($selnav, "ffc",            "Final Fact Check",    true);
    echoNav1($selnav, "editorlist",     "Editor List",         isEditorChief($suid) || isServerAdmin($suid));
    echoNav1($selnav, "testadmin",      "Testing Admin",       isTestingAdmin($suid));
    echoNav1($selnav, "testsolveteams", "TS Team Assignments", (USING_TESTSOLVE_TEAMS) && isTestingAdmin($suid));
    echoNav1($selnav, "answers",        "Answers",             canChangeAnswers($suid));
    echoNav1($selnav, "allpuzzles",     "All Puzzles",         canSeeAllPuzzles($suid));
    echoNav1($selnav, "editor-pick-special",     "Puzzles Needing Help",         true);
}
?>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div id="body">
<?php
}

function foot() {
?>
    </div>
    <div id="footer">
        <hr />
        <p>
        This is the website for the hunt writing team.

        For technical assistance, please contact the <a href="mailto:<?php echo HELP_EMAIL; ?>">Server Administrators</a>.<br/>

        This software is available <a href="http://github.com/mysteryhunt/puzzle-editing/">on GitHub</a> under the Simplified BSD license.<br/>
        The copyrights for the puzzles and comments contained herein are retained by the puzzle authors.</p>
    </div>
</div>
</body>
</html>

<?php
}

function printPerson($p) {
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
        if (USING_AWS) {
            $picsrc = "https://" . AWS_BUCKET . ".s3.amazonaws.com/uploads/pictures/thumbs/$id.jpg";
            $pic = "<img src=\"".$picsrc."\" />";
        } else {
            $picsrc = "uploads/pictures/thumbs/$id.jpg";
            if (file_exists($picsrc)) {
                $pic = "<img src=\"".$picsrc."\" />";
            }
        }
    }

    $jobNames = getUserJobsAsList($id);
    if (canSeeAllPuzzles($id)) {
        $profclass = "seeallprofilebox";
    } elseif (isApprover($id)) {
        $profclass = "approverprofilebox";
    } elseif ($jobNames) {
        $profclass = "specprofilebox";
    } else {
        $profclass = "profilebox";
    }
?>
    <div class="<?php echo $profclass; ?>">
        <div class="profileimg"><?php echo $pic ?></div>
        <div class="profiletxt">
            <span class="profilename"><?php echo "$fullname"; ?> (<?php echo "$uname"; ?>)</span>
            <span class="profiletitle"><?php echo $jobNames; ?></span>
            <span class="profilecontact"><a href="mailto:<?php echo $email ?>"><?php echo $email ?></a></span>
<?php
    $sql = "SELECT * FROM user_info_keys";
    $result = get_rows($sql);
    foreach ($result as $r) {
        $shortname = $r['shortname'];
        $longname = $r['longname'];
        $user_key_id = $r['id'];
        $sql = sprintf("SELECT value FROM user_info_values WHERE person_id = '%s' AND user_info_key_id = '%s'",
            mysql_real_escape_string($id), mysql_real_escape_string($user_key_id));
        $res = get_rows($sql);
        if (count($res) > 0 && $res[0]['value'] != "") {
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

function displayQueue($uid, $puzzles, $fields, $test, $filter = array(), $addLinkArgs = "", $hidedeadpuzzles = TRUE) {
    $fields = explode(" ", $fields);
    $showNotes = in_array("notes", $fields);
    $showAnswer = in_array("answer", $fields);
    $showSummary = in_array("summary", $fields);
    $showEditorNotes = in_array("editornotes", $fields);
    $showTags = in_array("tags", $fields);
    $showAuthorsAndEditors = in_array("authorsandeditors", $fields);
    $showNumTesters = in_array("numtesters", $fields);
    $showTesters = in_array("testers", $fields);
    $showFinalLinks = in_array("finallinks", $fields);
    if (!$puzzles) {
        echo "<span class='emptylist'>No puzzles to list</span><br/>";
        return;
    }
    $statuses = getPuzzleStatuses();

    $deadstatusid = getDeadStatusId();
    $flaggedPuzzles = getFlaggedPuzzles($uid);
?>
    <table class="tablesorter">
    <thead>
        <tr>
            <th class="puzzidea">ID</th>
            <?php if (USING_CODENAMES) {echo '<th class="puzzidea">Codename</th>';} ?>
            <th class="puzzidea">Title</th>
            <th class="puzzidea">Puzzle Status</th>
            <th class="puzzidea">Round</th>
            <?php if ($showSummary) {echo '<th class="puzzidea">Summary</th>';} ?>
            <?php if ($showEditorNotes) {echo '<th class="puzzidea">Editor Notes</th>';} ?>
            <?php if ($showTags) {echo '<th class="puzzidea">Tags</th>';} ?>
            <?php if ($showNotes) {echo '<th class="puzzidea">Status Notes</th>';} ?>
            <?php if ($showNotes) {echo '<th class="puzzidea">Runtime Info</th>';} ?>
            <?php if ($showNotes) {echo '<th class="puzzidea">Priority</th>';} ?>
            <?php if ($showAnswer) {echo '<th class="puzzidea">Answer</th>';} ?>
            <?php if (!$test) { echo '<th class="puzzidea">Last Commenter</th>';} ?>
            <?php if (!$test) { echo '<th class="puzzidea">Last Comment</th>';}?>
            <?php if ($showAuthorsAndEditors) {echo '<th class="puzzidea">Authors</th>';} ?>
            <?php if ($showAuthorsAndEditors) {echo '<th class="puzzidea">Discussion Editors</th>';} ?>
            <?php if ($showAuthorsAndEditors) {echo '<th class="puzzidea">D.Eds Needed</th>';} ?>
            <?php if ($showAuthorsAndEditors) {echo '<th class="puzzidea">Approval Editors</th>';} ?>
            <?php if ($showAuthorsAndEditors) {echo '<th class="puzzidea">Approvals</th>';} ?>
            <?php if ($showNumTesters) {echo '<th class="puzzidea"># Testers</th>';} ?>
            <?php if ($showTesters) {echo '<th class="puzzidea">Testers</th>';} ?>
            <?php if ($showTesters) {echo '<th class="puzzidea">Last Test Report</th>';} ?>
            <?php if (($showTesters) && (USING_TESTSOLVE_REQUESTS)) {echo '<th class="puzzidea">Testsolve requests</th>';} ?>
            <?php if ($showFinalLinks) {echo '<th class="puzzidea">Final Links</th>';} ?>
        </tr>
    </thead>
    <tbody>
<?php
    foreach ($puzzles as $pid) {
        $puzzleInfo = getPuzzleInfo($pid);
        $tags = getTagsAsList($pid);
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
            if ($filter[0] == "approver" && !isApproverOnPuzzle($filter[1], $pid)) {
                continue;
            }
            if ($filter[0] == "tag" && !isTagOnPuzzle($filter[1], $pid)) {
                continue;
            }
            if ($filter[0] != "status" && $hidedeadpuzzles && $puzzleInfo["pstatus"] == $deadstatusid) {
                continue;
            }
        }
        elseif ($hidedeadpuzzles && $puzzleInfo["pstatus"] == $deadstatusid) {
            continue;
        }

        $title = $puzzleInfo["title"];
        if ($title == NULL) {
            $title = '(untitled)';
        }
        $codename = getCodename($pid);
        $lastComment = getLastCommentDate($pid);
        $lastCommenter = getLastCommenter($pid);
        $lastVisit = getLastVisit($uid, $pid);
        $flagged = in_array($pid, $flaggedPuzzles);

        if (($lastVisit == NULL || strtotime($lastVisit) < strtotime($lastComment)) || $test) {
            echo '<tr class="puzz-new">';
        } elseif ($flagged) {
            echo '<tr class="puzz-flag">';
        } else {
            echo '<tr class="puzz">';
        }

        if ($test) {
            echo "<td class='puzzidea'><a href='test.php?pid=$pid$addLinkArgs'>$pid</a></td>";
        } else {
            echo "<td class='puzzidea'><a href='puzzle.php?pid=$pid$addLinkArgs'>$pid</a></td>";
        }
?>
        <?php if (USING_CODENAMES) {echo '<td class="puzzidea">' . $codename . '</th>';} ?>
        <td class='puzzidea'><?php echo $title; ?></td>
        <td class='puzzidea'><?php echo $statuses[$puzzleInfo["pstatus"]]; ?></td>
        <td class='puzzidea'><?php echo getPuzzleRound($pid); ?></td>
        <?php if ($showSummary) {echo "<td class='puzzideasecure'>" . $puzzleInfo["summary"] . "</td>";} ?>
        <?php if ($showEditorNotes) {echo "<td class='puzzideasecure'>" . $puzzleInfo["editor_notes"] . "</td>";} ?>
        <?php if ($showTags) {echo "<td class='puzzidea'>" . $tags . "</td>";} ?>
        <?php if ($showNotes) {echo "<td class='puzzidea'>" . $puzzleInfo["notes"] . "</td>";} ?>
        <?php if ($showNotes) {echo "<td class='puzzidea'>" . $puzzleInfo["runtime_info"] . "</td>";} ?>
        <?php if ($showNotes) {echo "<td class='puzzidea'>" . getPriorityWord($puzzleInfo["priority"]) . "</td>";} ?>
<?php
        if ($showAnswer) {
            if (getAnswersForPuzzleAsList($pid) != "") {
                echo "<td class='puzzideasecure'>";
            } else {
                echo "<td class='puzzidea'>";
            }
            echo getAnswersForPuzzleAsList($pid) . "</td>";
        } ?>
        <?php if (!$test) {echo "<td class='puzzidea'>$lastCommenter</td>";} ?>
        <?php if (!$test) {echo "<td class='puzzidea'>$lastComment</td>";} ?>
        <?php if ($showAuthorsAndEditors) {echo "<td class='puzzidea'>" . getAuthorsAsList($pid) . "</td>";} ?>
        <?php if ($showAuthorsAndEditors) {
            $est = getEditorStatus($pid);
            echo "<td class='puzzidea'>" . $est[0] . "</td>";
            echo "<td class='puzzidea'>" . $est[1] . "</td>";
        } ?>
        <?php if ($showAuthorsAndEditors) {echo "<td class='puzzidea'>" . getApproversAsList($pid) . "</td>";} ?>
        <?php if ($showAuthorsAndEditors) {echo "<td class='puzzidea'>" . countPuzzApprovals($pid) . "</td>";} ?>
        <?php if ($showNumTesters) {echo "<td class='puzzidea'>" . getNumTesters($pid) . "</td>";} ?>
        <?php if ($showTesters) {echo "<td class='puzzidea'>" . getCurrentTestersAsList($pid) . "</td>";} ?>
        <?php if ($showTesters) {echo "<td class='puzzidea'>" .  getLastTestReportDate($pid) . "</td>";} ?>
        <?php if (($showTesters) && (USING_TESTSOLVE_REQUESTS)) {echo "<td class='puzzidea'>" .  getTestsolveRequestsForPuzzle($pid) . "</td>";} ?>
        <?php if ($showFinalLinks) {echo "<td class='puzzidea'><a href='" .  getBetaLink($title) . "'>beta</a> <a href='". getFinalLink($title)."'.>final</a></td>";} ?>

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
function makeOptionElements($toDisplay, $name, $highlightKey = NULL) {
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
        if ($key == NULL) {
            continue;
        }
        // Start a new row, if necessary
        if (($i % $numCol) == 1) {
            echo '<tr>';
        }
        // Add answer information
        if ($key == $highlightKey) {
            echo "<td class='highlightkey'>";
        } else {
            echo '<td>';
        }
        echo "<label><input type='checkbox' name='$name" . "[]' value='$key' /> $value</label>";
        echo '</td>';

        // End row, if number of columns reached
        if (($i % $numCol) == 0) {
            echo '</tr>';
        }
        $i++;
    }

    // Close last row, if necessary
    if (($i % $numCol) != 1) {
        echo '</tr>';
    }
    echo '</table>';
}

function displayPuzzleStats($uid) {
    $max_rows = 6;

    $totalNumberOfPuzzles = countLivePuzzles();
    $numberOfEditors = getNumberOfEditorsOnPuzzles("discuss");
    $moreThanThree = $totalNumberOfPuzzles - $numberOfEditors['0'] - $numberOfEditors['1'] - $numberOfEditors['2'] - $numberOfEditors['3'];

    $numberOfApprovalEditors = getNumberOfEditorsOnPuzzles("approval");
    $moreThanThreeApproval = $totalNumberOfPuzzles - $numberOfApprovalEditors['0'] - $numberOfApprovalEditors['1'] - $numberOfApprovalEditors['2'] - $numberOfApprovalEditors['3'];

    $userNumbers = getNumberOfPuzzlesForUser($uid);

    $editor = $userNumbers['editor'];

    $tester = $userNumbers['currentTester'];
    if ($userNumbers['doneTester'] > 0) {
        $tester .= ' (+' . $userNumbers['doneTester'] . ' done)';
    }
?>
    <table><tr>
        <td class="puzz-stats">
            <table>
                <tr>
                    <th class="puzz-stats" colspan="2"><?php echo $totalNumberOfPuzzles; ?> Total Live Puzzles/Ideas</th>
                </tr>
                <tr>
                    <td class="puzz-stats">You Are Discuss Ed</td>
                    <td class="puzz-stats"><?php echo $editor; ?></td>
                </tr>
                <tr>
                    <td class="puzz-stats">You Are Approve Ed</td>
                    <td class="puzz-stats"><?php echo $userNumbers['approver']; ?></td>
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
                <!--<tr>
                        <td class="puzz-stats">Available To Edit</td>
                        <td class="puzz-stats"><?php echo $userNumbers['available']; ?></td>
                </tr>-->
            </table>
        </td>
        <td class="discussion-ed-stats">
            <table>
                <tr>
                    <th class="discussion-ed-stats" colspan="2">Discuss Eds</th>
                </tr>
                <tr>
                    <td class="discussion-ed-stats">Zero</td>
                    <td class="discussion-ed-stats"><?php echo $numberOfEditors['0']; ?></td>
                </tr>
                <tr>
                    <td class="discussion-ed-stats">One</td>
                    <td class="discussion-ed-stats"><?php echo $numberOfEditors['1']; ?></td>
                </tr>
                <tr>
                    <td class="discussion-ed-stats">Two</td>
                    <td class="discussion-ed-stats"><?php echo $numberOfEditors['2']; ?></td>
                </tr>
                <tr>
                    <td class="discussion-ed-stats">Three</td>
                    <td class="discussion-ed-stats"><?php echo $numberOfEditors['3']; ?></td>
                </tr>
                <tr>
                    <td class="discussion-ed-stats">&gt;Three</td>
                    <td class="discussion-ed-stats"><?php echo $moreThanThree; ?></td>
                </tr>
            </table>
        </td>
        <td class="approval-ed-stats">
            <table>
                <tr>
                    <th class="approval-ed-stats" colspan="2">Approval Eds</th>
                </tr>
                <tr>
                    <td class="approval-ed-stats">Zero</td>
                    <td class="approval-ed-stats"><?php echo $numberOfApprovalEditors['0']; ?></td>
                </tr>
                <tr>
                    <td class="approval-ed-stats">One</td>
                    <td class="approval-ed-stats"><?php echo $numberOfApprovalEditors['1']; ?></td>
                </tr>
                <tr>
                    <td class="approval-ed-stats">Two</td>
                    <td class="approval-ed-stats"><?php echo $numberOfApprovalEditors['2']; ?></td>
                </tr>
                <tr>
                    <td class="approval-ed-stats">Three</td>
                    <td class="approval-ed-stats"><?php echo $numberOfApprovalEditors['3']; ?></td>
                </tr>
                <tr>
                    <td class="approval-ed-stats">&gt;Three</td>
                    <td class="approval-ed-stats"><?php echo $moreThanThreeApproval; ?></td>
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

            if ($col==0) {
                echo '
                <tr>';
            }
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
        </td>
    </tr></table>
<?php
}
