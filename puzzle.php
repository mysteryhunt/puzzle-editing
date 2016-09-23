<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Get puzzle id
$pid = isValidPuzzleURL();

// Start HTML
$title = "Puzzle $pid";
if (USING_CODENAMES) {
    $title .= ": " . getCodename($pid);
}
head("", $title);

// Is the user testing this puzzle?
if (isTesterOnPuzzle($uid, $pid)) {
    echo "You are currently testing this puzzle.";
    foot();
    exit(0);
}

// Does the user have permission to see this page?
if (!canViewPuzzle($uid, $pid)) {
    echo "<div class='errormsg'>You do not have permission to view this puzzle.</div>";
    foot();
    exit(0);
}

// Record this user's visit (in two ways)
$lastVisit = updateLastVisit($uid, $pid);
if (!isEditorChief($uid) && !isCohesion($uid)) {
    addSpoiledUserQuietly($uid, $pid);
    if ($_GET['discuss'] && isEditorAvailable($uid, $pid) && !isEditorChief($uid)) {
        changeEditors($uid, $pid, array($uid), array());
    } elseif ($_GET['approve'] && isApproverAvailable($uid, $pid) && !isEditorChief($uid)) {
        changeApprovers($uid, $pid, array($uid), array());
    } elseif ($_GET['factcheck'] && isFactcheckerAvailable($uid, $pid) && !isEditorChief($uid)) {
        changeFactcheckers($uid, $pid, array($uid), array());
    }
}

// If author is a blind tester, turn page background red
if (isAnyAuthorBlind($pid)) {
    echo '<style type="text/css">html {background-color: #B00000;}</style>';
}

// Hide puzzle info from testing admins, to prevent spoilage
$hidePuzzleInfo = ((hasServerAdminPermission($uid) || hasTestAdminPermission($uid)) && !isAuthorOnPuzzle($uid, $pid) && !isEditorOnPuzzle($uid, $pid));

// If Testing Admin, hide answer, summary, and description
if ($hidePuzzleInfo) {
    echo '<style type="text/css">.hideFromTest {display: none;}</style>';
}

// Get all information about the puzzle (from puzzles table)
$puzzleInfo = getPuzzleInfo($pid);

// Edit the summary and description?
if (isset($_GET['edit'])) {
    editTitleSummaryDescription($uid, $pid,
        $puzzleInfo['title'], $puzzleInfo['summary'], $puzzleInfo['description']);
    foot();
    exit(0);
}

echo "<div class='puzzleInfo'>";

// Display puzzle number, title, answer, summary, description.
displayPuzzleInfo($uid, $pid, $puzzleInfo);

// Allow testing admins to display the hidden puzzle info
if ($hidePuzzleInfo) {
    echo '<a href="#" id="showTestLink">Show Answer, Summary, and Description</a>';
    echo '<a href="#" id="hideTestLink" class="hideFromTest">Hide Answer, Summary, and Description</a>';
}

// Allow author and lurkers to edit summary and description
echo '<p class="small"><a href="' . URL . "/puzzle.php?edit&pid=$pid" . '">';
echo 'Edit Title, Summary and Description</a></p>';

echo "</div>";

// List various people working on the puzzle
echo "<div class='peopleInfo'>";
displayPeople($uid, $pid);
echo "</div>";

// List puzzle status
echo "<div class='statusInfo'>";
displayStatus($uid, $pid);
echo "</div>";

// List puzzle notes
echo "<div class='notesInfo'>";
displayNotes($uid, $pid);
echo "</div>";

// List puzzle editor notes
echo "<div class='notesInfo'>";
displayEditorNotes($uid, $pid);
echo "</div>";

// List puzzle runtime info
echo "<div class='notesInfo'>";
displayRuntime($uid, $pid);
echo "</div>";

// List credits
if (USING_CREDITS) {
    echo "<div class='creditsInfo'>";
    displayCredits($uid, $pid);
    echo "</div>";
}

// List wiki page
echo "<div class='wikiInfo'>";
displayWikiPage($uid, $pid);
echo "</div>";

// List testsolve requests
if (USING_TESTSOLVE_REQUESTS) {
    echo "<div class='testsolveInfo'>";
    displayTestsolveRequests($uid, $pid);
    echo "</div>";
}

// TestsolveTeam Stuff
if (USING_TESTSOLVE_TEAMS) {
    echo "<form method='post' action='form-submit.php'>";
    echo "<div class='testsolveInfo'>";
    echo "<strong>TestSolve Team:</strong>\n";
    echo "<input type='hidden' name='pid' value='$pid'>\n";
    echo "<SELECT NAME='tid'>\n";
    $teamid = getPuzzleTestTeam($pid);
    if ($teamid == NULL) {
        echo "<option value=''>";
    }
    $testteams = getTestTeams();
    foreach ($testteams as $t) {
        $tid = $t['tid'];
        $teamname = $t['name'];
        echo "<option value='$tid' ";
        if ($tid == $teamid) {
            echo "SELECTED ";
        }
        echo ">$teamname\n";
    }

    echo "</select><input type='submit' value='set' name='setPuzzleTestTeam'></form>\n";
    echo "</div>";
}

// Show files
echo "<div class='fileInfo'>";
displayFiles($uid, $pid);
echo "</div>";

// Link to post-prod site
echo "<br />";
echo "<div class='postProd'>";
displayPostProd($uid, $pid, isStatusInPostProd($puzzleInfo['pstatus']));
echo "</div>";

echo "<br />";
echo "<div class='puzzApproval'>";
displayPuzzApproval($uid, $pid);
echo "</div>";

echo "<br />";
echo "<div class='priority'>";
displayPuzzPriority($uid, $pid);
echo "</div>";

echo "<br />";
echo "<div class='markasunseen'>";
displayMarkAsUnseen($uid, $pid);
echo "</div>";

echo "<br />";
echo "<div class='killpuzzle'>";
displayKillPuzzle($uid, $pid);
echo "</div>";

// Display & add comments
echo "<div class='comments'>";
echo "<table>";
displayComments($uid, $pid, $lastVisit);
addCommentForm($uid, $pid);
emailSubButton($uid, $pid);
echo "</table>";
echo "</div>";

// End HTML
foot();

//------------------------------------------------------
function displayPuzzleInfo($uid, $pid, $puzzleInfo) {
    $title = nl2br2($puzzleInfo['title']);
    if ($title == NULL) {
        $title = '(untitled)';
    }
    $summary = nl2br2($puzzleInfo['summary']);
    if ($summary == NULL) {
        $summary = '(no summary)';
    }
    $description = nl2br2($puzzleInfo['description']);
    if ($description == NULL) {
        $description = '(no description)';
    }
    $codename = getCodename($pid);
    $puzzleround = getPuzzleRound($pid);
?>
    <h2><?php echo "$codename (puzzle #$pid): $title";?></h2>
    <p><strong><?php echo "Round: $puzzleround"; ?></strong></p>
    <p><table><?php displayAnswers($uid, $pid); ?></table></p>
    <div class='hideFromTest puzzledesc'>
        <?php echo $summary; ?>
    </div>
    <div class='hideFromTest puzzledesc'>
        <?php echo $description; ?>
    </div>
<?php
}

function displayAnswers($uid, $pid) {
    $currentAnswers =  getAnswersForPuzzle($pid);
    $availableAnswers = getAvailableAnswers();
?>
    <tr class='hideFromTest'>
        <td>
            <strong>Answers: <?php echo getAnswersForPuzzleAsList($pid); ?></strong>
        <?php
            if (canChangeAnswers($uid) && ($currentAnswers != NULL || $availableAnswers != NULL)) {
        ?>
                &nbsp;&nbsp;<a href="#" class="changeLink">[Change]</a>
        </td>
    </tr>
    <tr>
        <td>
            <table>
                <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <tr>
                <?php
                    if ($currentAnswers != NULL) {
                ?>
                        <td>
                        <p><strong>Remove Answer(s):</strong></p>
                        <?php makeOptionElements($currentAnswers, 'removeAns'); ?>
                        </td>
                <?php
                    }
                    if ($availableAnswers != NULL) {
                ?>
                        <td>
                        <p><strong>Add Answer(s):</strong></p>
                        <?php makeOptionElements($availableAnswers, 'addAns'); ?>
                        </td>
                <?php
                    }
                ?>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="changeAnswers" value="Change Answers" />
                    </td>
                </tr>
                </form>
            </table>
        <?php
            }
        ?>
        </td>
    </tr>
<?php
}

function editTitleSummaryDescription($uid, $pid, $title, $summary, $description) {
?>
    <form method="post" action="form-submit.php">
        <h2>Puzzle <?php echo $pid; ?></h2>
        <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
        <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
        <p style="padding-top: 0.5em;">Title (NO SPOILERS): <input type="text" name="title" maxlength="255" class="longin" value="<?php echo htmlspecialchars($title); ?>" /></p>
        <p style="padding-top: 0.5em;">Summary (MINIMAL SPOILERS): <input type="text" name="summary" maxlength="255" class="longin" value="<?php echo htmlspecialchars($summary); ?>" /></p>
        <p style="padding-top: 0.5em;">Description (spoilers ok):</p>
        <textarea style="width:50em; height: 25em;" name="description"><?php echo htmlspecialchars($description); ?></textarea>
        <p style="padding-top: 0.5em;">
            <input type="submit" name="editTSD" class="okSubmit" value="Change" />
            <input type="submit" name="cancelTSD" value="Cancel" />
        </p>
    </form>
<?php
}

function displayPeople($uid, $pid) {
?>
    <table>
        <?php displayAuthors($uid, $pid); ?>
        <?php displaySpoiled($uid, $pid); ?>
        <?php if (USING_ROUND_CAPTAINS) {displayRoundCaptain($uid, $pid);} ?>
        <?php displayEditors($uid, $pid); ?>
        <?php if (USING_APPROVERS) {displayApprovers($uid, $pid);} ?>
        <?php displayFactcheckers($uid, $pid); ?>
        <?php displayTags($uid, $pid); ?>
        <?php if (canSeeTesters($uid, $pid)) {displayTesters($uid, $pid);} ?>
        <?php if (USING_PER_PUZZLE_TESTER_LIMITS) {displayTesterLimit($uid, $pid);} ?>
        <?php displayTestingAdmin($uid, $pid); ?>
    </table>
<?php
}

function displayAuthors($uid, $pid) {
?>
    <tr>
        <td class='peopleInfo'>
            <strong>Authors:</strong> <?php echo getAuthorsAsList($pid); ?>&nbsp;&nbsp;<a href="#" class="changeLink">[Change]</a>
        </td>
    </tr>
    <tr>
        <td>
            <table>
                <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <tr>
                    <td>
                        <p><strong>Remove Author(s):</strong></p>
                        <?php echo displayRemoveAuthor($pid, $uid); ?>
                    </td>
                    <td>
                        <p><strong>Add Author(s):</strong></p>
                        <?php echo displayAddAuthor($pid, $uid); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="changeAuthors" value="Change Authors" />
                    </td>
                </tr>
                </form>
            </table>
        </td>
    </tr>
<?php
}

function displayRemoveAuthor($pid, $uid) {
    $authors = getAuthorsForPuzzle($pid);
    makeOptionElements($authors, 'removeAuth', $uid);
}

function displayAddAuthor($pid, $uid) {
    $authors = getAvailableAuthorsForPuzzle($pid);
    makeOptionElements($authors, 'addAuth', $uid);
}

function displaySpoiled($uid, $pid) {
?>
    <tr>
        <td class='peopleInfo'>
            <strong>Spoiled:</strong> <?php echo getSpoiledAsList($pid); ?>&nbsp;&nbsp;<a href="#" class="changeLink">[Change]</a>
        </td>
    </tr>
    <tr>
        <td>
            <table>
            <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <tr>
                    <td>
                        <p><strong>Remove Spoiled:</strong></p>
                        <?php echo displayRemoveSpoiledUsers($pid, $uid); ?>
                    </td>
                    <td>
                        <p><strong>Add Spoiled:</strong></p>
                        <?php echo displayAddSpoiledUsers($pid, $uid); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="changeSpoiled" value="Change Spoiled" />
                    </td>
                </tr>
            </form>
            </table>
        </td>
    </tr>
<?php
}

function displayRemoveSpoiledUsers($pid, $uid) {
    $spoiled = getSpoiledUsersForPuzzle($pid);
    makeOptionElements($spoiled, 'removeSpoiledUser', $uid);
}

function displayAddSpoiledUsers($pid, $uid) {
    $spoiled = getAvailableSpoiledUsersForPuzzle($pid);
    makeOptionElements($spoiled, 'addSpoiledUser', $uid);
}

function displayRoundCaptain($uid, $pid) {
?>
    <tr>
        <td class='peopleInfo'>
            <strong>Round Captain:</strong> <?php echo getRoundCaptainsAsList($pid); ?>&nbsp;&nbsp;<a href="#" class="changeLink">[Change]</a>
        </td>
    </tr>
    <tr>
        <td>
            <table>
            <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <tr>
                    <td>
                        <p><strong>Remove Round Captain(s):</strong></p>
                        <?php echo displayRemoveRoundCaptain($pid, $uid); ?>
                    </td>
                    <td>
                        <p><strong>Add Round Captain(s):</strong></p>
                        <?php echo displayAddRoundCaptain($pid, $uid); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="changeRoundCaptain" value="Select Round Captain" />
                    </td>
                </tr>
            </form>
            </table>
        </td>
    </tr>
<?php
}

function displayRemoveRoundCaptain($pid, $uid) {
    $capts = getRoundCaptainsForPuzzle($pid);
    makeOptionElements($capts, 'removeRoundCaptain', $uid);
}

function displayAddRoundCaptain($pid, $uid) {
    $capts = getAvailableRoundCaptainsForPuzzle($pid);
    makeOptionElements($capts, 'addRoundCaptain', $uid);
}

function displayEditors($uid, $pid) {
?>
    <tr>
        <td class='peopleInfo'>
            <strong>Discussion Editors:</strong> <?php $est = getEditorStatus($pid); echo $est[0]; ?>&nbsp;&nbsp;<a href="#" class="changeLink">[Change]</a>
            <!-- There was an if statement around this entire [change] block before, but non-authors should be able to add themselves as discussion editors, and authors should be able to change the number of discussion editors needed, so that's, like, everybody... -->
        </td>
    </tr>
    <tr>
        <td>
            <?php if (canChangeEditorsNeeded($uid, $pid)) { ?>
            <div>
            <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                Change # of needed editors: <input type="text" name="needed_editors" value="<?php echo getNeededEditors($pid); ?>" class="shortin" /> <input type="submit" name="changeNeededEditors" value="Go" />
            </form>
            </div>
            <?php } ?>
            <table>
                <form method="post" action="form-submit.php">
                    <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                    <tr>
                        <td>
                            <p><strong>Remove Discussion Editor(s):</strong></p>
                            <?php echo displayRemoveEditor($pid, $uid)?>
                        </td>
                        <td>
                            <p><strong>Add Discussion Editor(s):</strong></p>
                            <?php echo displayAddEditor($pid, $uid); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" name="changeEditors" value="Change Editors" />
                        </td>
                    </tr>
                </form>
            </table>
        </td>
    </tr>
<?php
}

function displayRemoveEditor($pid, $uid) {
    $editors = getEditorsForPuzzle($pid);
    makeOptionElements($editors, 'removeEditor', $uid);
}

function displayAddEditor($pid, $uid) {
    $editors = getAvailableEditorsForPuzzle($pid);
    makeOptionElements($editors, 'addEditor', $uid);
}

function displayApprovers($uid, $pid) {
?>
    <tr>
        <td class='peopleInfo'>
            <strong>Approval Editors:</strong> <?php echo getApproversAsList($pid); ?>&nbsp;&nbsp;
    <?php if (!isAuthorOnPuzzle($uid, $pid) || isEditorChief($uid) || hasServerAdminPermission($uid)) { ?><a href="#" class="changeLink">[Change]</a>
        </td>
    </tr>
    <tr>
        <td>
            <table>
            <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <tr>
                    <td>
                        <p><strong>Remove Approval Editor(s):</strong></p>
                        <?php echo displayRemoveApprover($pid, $uid)?>
                    </td>
                    <td>
                        <p><strong>Add Approval Editor(s):</strong></p>
                        <?php echo displayAddApprover($pid, $uid); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="changeApprovers" value="Change Approvers" />
                    </td>
                </tr>
            </form>
            </table>
    <?php } ?>
        </td>
    </tr>
<?php
}

function displayRemoveApprover($pid, $uid) {
    $approvers = getApproversForPuzzle($pid);
    makeOptionElements($approvers, 'removeApprover', $uid);
}

function displayAddApprover($pid, $uid) {
    $approvers = getAvailableApproversForPuzzle($pid);
    makeOptionElements($approvers, 'addApprover', $uid);
}

function displayFactcheckers($uid, $pid) {
?>
    <tr>
        <td class='peopleInfo'>
            <strong>Factcheckers:</strong> <?php echo getFactcheckersAsList($pid); ?>&nbsp;&nbsp;<a href="#" class="changeLink">[Change]</a>
        </td>
    </tr>
    <tr>
        <td>
            <table>
            <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <tr>
                    <td>
                        <p><strong>Remove Factchecker(s):</strong></p>
                        <?php echo displayRemoveFactchecker($pid, $uid)?>
                    </td>
                    <td>
                        <p><strong>Add Factchecker(s):</strong></p>
                        <?php echo displayAddFactchecker($pid, $uid); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="changeFactcheckers" value="Change Factcheckers" />
                    </td>
                </tr>
            </form>
            </table>
        </td>
    </tr>
<?php
}

function displayRemoveFactchecker($pid, $uid) {
    $factcheckers = getFactcheckersForPuzzle($pid);
    makeOptionElements($factcheckers, 'removeFactchecker', $uid);
}

function displayAddFactchecker($pid, $uid) {
    $factcheckers = getAvailableFactcheckersForPuzzle($pid);
    makeOptionElements($factcheckers, 'addFactchecker', $uid);
}

function displayTags($uid, $pid) {
?>
    <tr>
        <td class='peopleInfo'>
            <strong>Tags:</strong> <?php echo getTagsAsList($pid); ?>&nbsp;&nbsp;
    <?php if (isCohesion($uid) || isEditorChief($uid) || hasServerAdminPermission($uid)) { ?><a href="#" class="changeLink">[Change]</a>
        </td>
    </tr>
    <tr>
        <td>
            <table>
            <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <tr>
                    <td>
                        <p><strong>Remove Tags:</strong></p>
                        <?php echo displayRemoveTags($pid)?>
                    </td>
                    <td>
                        <p><strong>Add Tags:</strong></p>
                        <?php echo displayAddTags($pid); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="changeTags" value="Change Tags" />
                    </td>
                </tr>
            </form>
            </table>
    <?php } ?>
        </td>
    </tr>
<?php
}

function displayRemoveTags($pid) {
    $tags = getTagsForPuzzle($pid);
    makeOptionElements($tags, 'removeTag');
}

function displayAddTags($pid) {
    $tags = getAvailableTagsForPuzzle($pid);
    makeOptionElements($tags, 'addTag');
}

function displayTesters($uid, $pid) {
?>
    <tr>
        <td class='peopleInfo'>
            <strong>Current Testers:</strong> <?php echo getCurrentTestersAsList($pid); ?>
        </td>
    </tr>
    <tr>
        <td class='peopleInfo'>
            <strong>Finished Testers:</strong> <?php echo getFinishedTestersAsList($pid); ?>
        </td>
    </tr>
    <tr>
        <td class='peopleInfo'>
            <strong>Number of testers during this testsolving cycle:</strong> <?php echo getCurrentPuzzleTesterCount($pid); ?>
        </td>
    </tr>
    <tr>
        <td class='peopleInfo'>
            <strong>Fun Ratings:</strong> Median <?php echo getMedianFeedback($pid, 'fun'); ?>, Mode <?php echo getModeFeedback($pid, 'fun'); ?>
        </td>
    </tr>
    <tr>
        <td class='peopleInfo'>
            <strong>Difficulty Ratings:</strong> Median <?php echo getMedianFeedback($pid, 'difficulty'); ?>, Mode <?php echo getModeFeedback($pid, 'difficulty'); ?>
        </td>
    </tr>
<?php
}

function displayTesterLimit($uid, $pid) {
?>
    <tr>
        <td class='peopleInfo'>
            <strong>Tester Limit:</strong> <?php echo getTesterLimit($pid); ?>&nbsp;
            <?php if (canChangeTesterLimit($uid, $pid)) { ?>
            <a href="#" class="changeLink">[Change]</a>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php if (canChangeTesterLimit($uid, $pid)) { ?>
            <div>
            <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                Change maximum number of testers: <input type="text" name="tester_limit" value="<?php echo getTesterLimit($pid); ?>" class="shortin" /> <input type="submit" name="changeTesterLimit" value="Go" />
            </form>
            </div>
            <?php } ?>
        </td>
    </tr>
<?php
}

function displayTestingAdmin($uid, $pid) {
?>
    <tr>
        <td class='peopleInfo'>
            <strong>Testing Admin: </strong><?php echo getTestingAdminsForPuzzleAsList($pid); ?>
        </td>
    </tr>
<?php
}

function displayStatus($uid, $pid) {
    $status = getStatusNameForPuzzle($pid);

?>
    <table class="statusInfo">
        <tr>
            <td class='statusInfo'>
                <strong>Puzzle Status: </strong> <?php echo $status; ?>
            </td>
            <td class='statusInfo'>
                <?php if (canChangeStatus($uid)) { ?><a href="#" class="changeLink">[Change]</a><?php } ?>
            </td>
        </tr>
    <?php
    if (canChangeStatus($uid)) {
    ?>
        <tr>
            <td colspan='3'>
                <table>
                <form method="post" action="form-submit.php">
                        <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                        <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                    <tr>
                        <td>
                            <p><strong>Change Puzzle Status:</strong></p>
                            <?php echo displayChangePuzzleStatus($pid); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" name="changePuzzleStatus" value="Change Status" />
                        </td>
                    </tr>
                </form>
                </table>
            </td>
        </tr>
    <?php
    }
    ?>
    </table>
<?php
}

function displayTestsolveRequests($uid, $pid) {
    $reqs = getTestsolveRequestsForPuzzle($pid);

?>
    <table class="testsolveInfo">
        <tr>
            <td class='testsolveInfo'>
                <strong>Active Testsolve Requests: </strong> <?php echo $reqs; ?>
            </td>
            <td class='testsolveInfo'>
<?php
    if (canRequestTestsolve($uid, $pid)) {
        ?><a href="#" class="changeLink">[Request]</a><?php
    } else {
        ?><i class="smallText">[Put in testing first]</i><?php
    } ?>
                <a href="#" onClick="document.getElementById('clearOneTestsolveRequest').submit(); return 0;"
                    class="smallText">[Clear one]</a>
                <a href="#" onClick="document.getElementById('clearTestsolveRequests').submit(); return 0;"
                    class="smallText">[Clear all]</a>
                <form id="clearOneTestsolveRequest" method="post" action="form-submit.php">
                    <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                    <input type="hidden" name="clearOneTestsolveRequest" />
                </form>
                <form id="clearTestsolveRequests" method="post" action="form-submit.php">
                    <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                    <input type="hidden" name="clearTestsolveRequests" />
                </form>
            </td>
        </tr>
<?php
    if (canRequestTestsolve($uid, $pid)) {
?>
        <tr>
            <td colspan='3'>
            <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <p><strong>Request Testsolve:</strong></p>
                <textarea name="notes">[Enter any notes here]</textarea>
                <input type="submit" name="requestTestsolve" value="Request Testsolve">
            </form>
            </td>
        </tr>
<?php
    }
?>
    </table>
<?php
}

function displayCredits($uid, $pid) {
    $notes = htmlspecialchars(getCredits($pid));

?>
    <table class="creditsInfo">
        <tr>
            <td class='creditsInfo'>
                <strong>Credits: </strong> <?php echo $notes; ?>
            </td>
            <td class='creditsInfo'>
                <a href="#" class="changeLink">[Change]</a>
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <form method="post" action="form-submit.php">
                    <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                    <input type="text" name="credits" maxlength="255" class="longin" value="<?php echo $notes; ?>"/>
                    <input type="submit" name="changeCredits" value="Change" />
                </form>
            </td>
        </tr>
    </table>
<?php
}
function displayNotes($uid, $pid) {
    $notes = getNotes($pid);

?>
    <table class="statusInfo">
        <tr>
            <td class='statusInfo'>
                <strong>Status Notes: </strong> <?php echo $notes; ?>
            </td>
            <td class='statusInfo'>
                <a href="#" class="changeLink">[Change]</a>
            </td>
        </tr>
        <tr>
            <td colspan='2'>
            <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <input type="text" name="notes" maxlength="255" class="longin" value="<?php echo $notes; ?>"/>
                <input type="submit" name="changeNotes" value="Change" />
            </form>
            </td>
        </tr>
    </table>
<?php
}
function displayEditorNotes($uid, $pid) {
    $notes = getEditorNotes($pid);

?>
    <table class="statusInfo">
        <tr>
            <td class='statusInfo'>
                <strong>Editor Notes: </strong> <?php echo $notes; ?>
            </td>
            <td class='statusInfo'>
                <a href="#" class="changeLink">[Change]</a>
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <form method="post" action="form-submit.php">
                    <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                    <input type="text" name="notes" maxlength="255" class="longin" value="<?php echo $notes; ?>"/>
                    <input type="submit" name="changeEditorNotes" value="Change" />
                </form>
            </td>
        </tr>
    </table>
<?php
}
function displayRuntime($uid, $pid) {
    $notes = getRuntime($pid);

?>
    <table class="statusInfo">
        <tr>
            <td class='statusInfo'>
                <strong>Runtime Notes: </strong> <?php echo $notes; ?>
            </td>
            <td class='statusInfo'>
                <a href="#" class="changeLink">[Change]</a>
            </td>
        </tr>
        <tr>
            <td colspan='2'>
            <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <input type="text" name="notes" maxlength="255" class="longin" value="<?php echo $notes; ?>"/>
                <input type="submit" name="changeRuntime" value="Change" />
            </form>
            </td>
        </tr>
    </table>
<?php
}

function displayWikiPage($uid, $pid) {
    $page = getWikiPage($pid);

?>
    <table class="wikiInfo">
        <tr>
            <td class='wikiInfo'>
                <strong>Testsolve Work Page: </strong> <a href="<?php echo $page; ?>"><?php echo $page; ?></a>
            </td>
            <td class='wikiInfo'>
                <a href="#" class="changeLink">[Change]</a>
            </td>
        </tr>
        <tr>
            <td colspan='2'>
            <form method="post" action="form-submit.php">
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <input type="text" name="wikiPage" maxlength="255" class="longin" value="<?php echo $page; ?>"/>
                <input type="submit" name="changeWikiPage" value="Change" />
            </form>
            </td>
        </tr>
    </table>
<?php
}

function displayChangePuzzleStatus($pid) {
    $statuses = getPuzzleStatuses();
    $current = getStatusForPuzzle($pid);

    foreach ($statuses as $sid => $name) {
        echo "<input type ='radio' name='status' value='$sid'";
        if ($sid == $current) {
            echo ' checked';
        }
        echo " />&nbsp;&nbsp;$name<br />";
    }
}

function displayFiles($uid, $pid) {
?>
    <table>
        <?php displayFileList($uid, $pid, 'draft'); ?>
        <?php displayFileList($uid, $pid, 'solution'); ?>
        <?php displayFileList($uid, $pid, 'misc'); ?>
        <?php displayFileList($uid, $pid, 'postprod'); ?>
    </table>
    <p style="padding-top: .5em;"><a href='#' id='toggleFiles'>Show Older Files</a></p>
<?php
}

function displayFileList ($uid, $pid, $type) {
    $fileList = getFileListForPuzzle($pid, $type);
    $first = TRUE;

    if (!$fileList) {
        $file['filename'] = '(none)';
        $file['date'] = NULL;
        $fileList[] = $file;
    }

    foreach ($fileList as $file) {
        $finfo = pathinfo($file['filename']);
        $filename = $finfo['basename'];
        if (strpos($file['filename'], 'http') !== false || !USING_AWS) {
            $link = $file['filename'];
        } elseif (strpos($file['filename'], '_dir', strlen($file['filename']) - 4) !== false) {
            $link = AWS_ENDPOINT . AWS_BUCKET . '/' . $file['filename'] . '/index.html';
        } else {
            $link = AWS_ENDPOINT . AWS_BUCKET . '/' . $file['filename'];
        }

        $date = $file['date'];

        if ($first) {
            $class = 'fileInfoLatest';
?>
            <tr>
                <td class='<?php echo $class; ?>'>
                <?php echo "<strong>Latest $type:</strong>"; ?>
                </td>
<?php
        } else {
            $class = 'fileInfoOld';
?>
            <tr>
                <td class='<?php echo $class; ?>'>
                <?php echo "Older $type:"; ?>
                </td>
<?php
        }
?>

<?php
        if ($file['filename'] == '(none)') {
?>
                <td class='<?php echo $class; ?>' colspan='2'>
                    (none)
                </td>
<?php
        } else {
?>
                <td class='<?php echo $class; ?>'>
                    <?php echo "<a href='$link'/>$filename</a>"; ?>
                </td>
                <td class='<?php echo $class; ?>'>
                    <?php echo "$date"; ?>
                </td>
<?php
        }

        if ($first && !($type == 'draft' && !canAcceptDrafts($pid))) {
?>
                <td class='<?php echo $class; ?>'>
                    <a href="#" id="<?php echo "upload$type" . "Link"; ?>">[Upload New]</a>
                </td>
            </tr>
            <tr id='<?php echo"upload$type"; ?>'>
            <form enctype="multipart/form-data" method="post" action="form-submit.php">
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <input type="hidden" name="filetype" value="<?php echo $type; ?>" />
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />

                <td class='<?php echo $class; ?>' colspan='3'>
                    <input type="file" name="fileupload" />
                    <input type="submit" name="uploadFile" value="Upload" />
                </td>
            </form>
            </tr>
<?php
            if (isset($_SESSION['upload_error'])) {
                echo '<span class="error">' . $_SESSION['upload_error'] . '</span>';
                unset($_SESSION['upload_error']);
            }
        } else {
?>
            </tr>
<?php
        }

        if ($first) {
            $first = FALSE;
        }
    }
}

function displayKillPuzzle($uid, $pid) {
    if ((isAuthorOnPuzzle($uid, $pid)) && !(isPuzzleDead($pid))) {
?>
    <table style="border:1px solid black;">
        <tr>
        <td><strong>Kill This Puzzle:</strong></td>
        <td>
        <form method="post" action="form-submit.php">
            <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
            <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
            <input type="submit" name="killPuzzle" value="Really Kill" />
        </form>
        </td>
        </tr>
    </table>
<?php
    }
    return;
}

function displayMarkAsUnseen($uid, $pid) {
?>
    <form method="post" action="form-submit.php">
        <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
        <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
        <strong>Highlight until next time you post:</strong>
        <input type="checkbox" name="flag" value="yes" <?php if (getFlag($uid, $pid)) echo "checked"; ?> />
        <input type="submit" name="setflag" value="Update" />
or simply
        <input type="submit" name="markunseen" value="Mark unread for now" />
    </form>
<?php
    return;
}

function displayPostProd($uid, $pid, $pp) {
    $rinfo = getRoundForPuzzle($pid);
    $urlprefix = POSTPROD_URLPREFIX;
    $beta_urlprefix = POSTPROD_BETA_URLPREFIX;
    $roundname = $rinfo['name'];
    $title = getTitle($pid);
    /*
    $showmeta = FALSE;
    if ($roundname == 'Metas') {
        $m = array();
        $regexp = '/^\s*\(([CS])-META\)\s*(.*)$/';
        if (preg_match($regexp, $title, $m)) {
            $roundname = $m[2];
            $title = ($m[1]=='C') ? "Investigator's Report" : "Meta";
            if ($m[1]=='S') { $showmeta = TRUE; }
        }
    }
    */
    $fileList = getFileListForPuzzle($pid, 'postprod');
    $file = $fileList[0];
    $url = $beta_urlprefix;
    //  $url .= "/" . postprodCanonRound($roundname);
    $url .= "puzzle/" . postprodCanon($title) . "/";
    if ($pp) {
?>
        <strong>Final Fact Check Beta Link: </strong>
        <a href="<?php echo $url ?>">View postprod (as pushed from puzzletron)</a>.  <--  Use this to perform final fact check!  Username/password is jarthur/random17.  If it gives a 404, push the button below (which takes a minute).
        <br>
<?php
    if ($pp) {
?>
        <form action="form-submit.php" method="post">
            <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
            <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
            <input type="submit" name="postprod" value="Push this puzzle to post-production">
        </form><br/>
<?php
    }
    $url = $urlprefix;
    // $url .= "/" . postprodCanonRound($roundname);
    $url .= "/" . postprodCanon($title) . "/";
?>
    <strong>Actually Final Link: </strong>
    <a href="<?php echo $url ?>">View postprod (version installed as final, doesn't work yet)</a>
    <br>
<?php
    }
}

function displayComments($uid, $pid, $lastVisit) {
    $comments = getComments($pid);
    if (!$comments) {
        return;
    }

    foreach ($comments as $comment) {
        $id = $comment['id'];
        $timestamp = $comment['timestamp'];
        $type = $comment['name'];
        $user = $comment['uid'];

        if ($user == 0) {
            $name = 'Server';
        } else {
            $name = getUserName($user);
        }
        if ($lastVisit == NULL || strtotime($lastVisit) < strtotime($timestamp)) {
            echo "<tr class='comment-new' id='comm$id'>";
        } else {
            echo "<tr class='comment' id='comm$id'>";
        }

        echo "<td class='$type" . "Comment'>";

        if ($type == 'Testsolver') {
            if (canSeeTesters($uid, $pid)) {
                echo $name . '<br />';
            }
            echo 'Testsolver '.substr(md5(strval($pid).strval($user)), 0, 8);
        } else {
            echo $name;
        }
        echo "<br />$timestamp<br />$type <small>(Comment #$id)</small>";
        echo "<td class='$type" . "Comment'>";

// TODO: Is this really the best we can do? Markdown, anyone?
        $pcomment = preg_replace('#(\A|[^=\]\'"a-zA-Z0-9])(http[s]?://(.+?)/[^()<>\s]*)#i', '\\1<a href="\\2">\\2</a>', ($comment['comment']));
        if (USING_AWS) {
            $pcomment = str_replace("=\"uploads/", "=\"" . AWS_ENDPOINT . AWS_BUCKET . "/uploads/", $pcomment);
        }
        echo nl2br2($pcomment);
        echo '</td>';
        echo '</tr>';
    }
}

function addCommentForm($uid, $pid) {
    if (canComment($uid, $pid)) {
?>
        <form action="form-submit.php" method="post">
            <tr class="comment">
                <td colspan="2">
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                    <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                    <textarea style="width:50em; height: 20em; margin-bottom: 5px;" name="comment" class="hi"></textarea><br />
                    <input type="submit" name="addcomment" value="Add Comment" class="okSubmit" />
                </td>
            </tr>
        </form>
<?php
    }
}

function emailSubButton($uid, $pid) {
    if (isSubbedOnPuzzle($uid, $pid)) {
?>
        <form action="form-submit.php" method="post">
            <tr class="comment">
                <td>
                    <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                    <input type="submit" name="emailUnsub" value="Unsubscribe from Comments" />
                </td>
            </tr>
        </form>
<?php
    } else {
?>
        <form action="form-submit.php" method="post">
            <tr class="comment">
                <td>
                    <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                    <input type="submit" name="emailSub" value="Subscribe to Comments" />
                </td>
            </tr>
        </form>
<?php
    }
}

function displayPuzzApproval($uid, $pid) {
    $approvals = getPuzzApprovals($pid);
?>
        <b>Editor approval (required to change puzzle status):</b> <br/>
        <table>
<?php
    //only display approval form itself if you are an editor/approver on this puzzle
    if ((isEditorOnPuzzle($uid, $pid) && !USING_APPROVERS) ||
        (isApproverOnPuzzle($uid, $pid) && USING_APPROVERS)) {
?>
            <tr>
            <form action="form-submit.php" method="post">
                <td>
                    <input type="radio" name="puzzApprove" value="1" checked />Approve
                </td>
                <td>
                    <input type="radio" name="puzzApprove" value="0" />Revise
                </td>
                <td>
                    <input type="hidden" name="uid" value='<?php echo $uid; ?>' />
                    <input type="hidden" name="pid" value='<?php echo $pid; ?>' />
                    <input type="submit" name="setPuzzApprove" value="Submit" />
                </td>
            </form>
            </tr>
<?php
        }

    //everyone gets to see approval table
    if ($approvals == NULL) {
        echo "<tr><td colspan=3>No existing editor feedback at this stage yet.</td></tr><br>";
    } elseif (!isEditorOnPuzzle($uid, $pid)) {
        echo "<tr><td>Approve</td><td>Revise</td></td>";
    }
    foreach ($approvals as $fullname => $approve) {
        echo "<tr><td align=center>";

        //check if approved
        if ($approve == 1) {
            echo "<b>X</b>";
        }
        echo "</td><td align=center>";

        //check if notapproved
        if ($approve == 0) {
            echo "<b>X</b>";
        }

        printf("</td><td>%s</td></tr>", $fullname);
    }
?>
        </table>
<?php
}

function displayPuzzPriority($uid, $pid) {
    $priority = getPriority($pid);
    //only display approval form itself if you are an editor/approver on this puzzle
    if (canChangeStatus($uid)) {
?>
        <b>Testsolving Priority:</b> <br/>
        <table>
            <tr>
                <form action="form-submit.php" method="post">
                <td>
                    <input type="radio" name="puzzPriority" value="1" <?php if ($priority == 1) echo "checked" ?> />Very high
                    <input type="radio" name="puzzPriority" value="2" <?php if ($priority == 2) echo "checked" ?> />High
                    <input type="radio" name="puzzPriority" value="3" <?php if ($priority == 3) echo "checked" ?> />Normal
                    <input type="radio" name="puzzPriority" value="4" <?php if ($priority == 4) echo "checked" ?> />Low
                    <input type="radio" name="puzzPriority" value="5" <?php if ($priority == 5) echo "checked" ?> />Very low
                </td>
                <td></td>
                <td>
                    <input type="hidden" name="uid" value='<?php echo $uid; ?>' />
                    <input type="hidden" name="pid" value='<?php echo $pid; ?>' />
                    <input type="submit" name="setPuzzPriority" value="Submit" />
                </td>
                </form>
            </tr>
        </table>
<?php
    }
}
