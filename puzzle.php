<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        // Start HTML
        head();

        // Get puzzle id
        $pid = isValidPuzzleURL();

        // Is the user testing this puzzle?
        if (isTesterOnPuzzle($uid, $pid)) {
                echo "You are currently testing this puzzle.";
                foot();
                exit(0);
        }

        // Does the user have permission to see this page?
        if (!canViewPuzzle($uid, $pid)) {
                echo "You do not have permission to view this puzzle.";
                foot();
                exit(0);
        }

        // Record this user's visit (in two ways)
        $lastVisit = updateLastVisit($uid, $pid);
        addSpoiledUserQuietly($uid, $pid);

        // If author is a blind tester, turn page background red
        if (isAnyAuthorBlind($pid)) {
                echo '<style type="text/css">html {background-color: #B00000;}</style>';
        }

        // Hide puzzle info from testing admins, to prevent spoilage
        $hidePuzzleInfo = (isTestingAdmin($uid) && !isAuthorOnPuzzle($uid, $pid) && !isEditorOnPuzzle($uid, $pid));

        // If Testing Admin, hide answer, summary, and description
        if ($hidePuzzleInfo) {
                echo '<style type="text/css">.hideFromTest {display: none;}</style>';
        }

        // Get all information about the puzzle (from puzzle_idea table)
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
	echo '<p style="font-size:75%;"><a href="' . URL . "/puzzle.php?edit&pid=$pid" . '">';
        echo 'Edit Title, Summary and Description</a></p>';

        echo "</div>";

        if (ENABLE_WRITING_WIKI) {
                echo "To chat, please go to <a href='http://manicsages.org/writingwiki/index.php/Main_Page'>the writing wiki</a>. (The chat box here was found to be annoying and has been removed.)<br>\n";
        }

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

        // List credits
        echo "<div class='creditsInfo'>";
        displayCredits($uid, $pid);
        echo "</div>";

        // List wiki page
        echo "<div class='wikiInfo'>";
        displayWikiPage($uid, $pid);
        echo "</div>";

        // List testsolve requests
        echo "<div class='testsolveInfo'>";
        displayTestsolveRequests($uid, $pid);
        echo "</div>";

        // Show files
        echo "<div class='fileInfo'>";
        displayFiles($uid, $pid);
        echo "</div>";

        // Link to post-prod site
        echo "<br />";
        echo "<div class='postProd'>";
        displayPostProd($uid, $pid, isStatusInPostProd($puzzleInfo['pstatus']));
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
function displayPuzzleInfo($uid, $pid, $puzzleInfo)
{
        $title = nl2br($puzzleInfo['title']);
        if ($title == NULL)
                $title = '(untitled)';

        $summary = nl2br($puzzleInfo['summary']);
        if ($summary == NULL)
                $summary = '(no summary)';

        $description = nl2br($puzzleInfo['description']);
        if ($description == NULL)
                $description = '(no description)';

?>
        <table>
                <tr>
                        <td>
                                <h1 style="margin: 0em;"><?php echo "(puzzle $pid): $title"; ?></h1>
                        </td>
                </tr>
                <?php displayAnswers($uid, $pid); ?>
                <tr class='hideFromTest'>
                        <td style="background-color: #F0E8E0;">
                                <?php echo $summary; ?>
                        </td>
                </tr>
                <tr><td></td></tr>
                <tr class='hideFromTest'>
                        <td style="background-color: #F0E8E0;">
                                <?php echo $description; ?>
                        </td>
                </tr>
        </table>
<?php
}

function displayAnswers($uid, $pid)
{
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

function editTitleSummaryDescription($uid, $pid, $title, $summary, $description)
{
?>
        <form method="post" action="form-submit.php">
                <h1>Puzzle <?php echo $pid; ?></h1>
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <p style="padding-top: 0.5em;">Title (NO SPOILERS): <input type="text" name="title" maxlength="255" style="width:30em;" value="<?php echo $title; ?>" /></p>
                <p style="padding-top: 0.5em;">Summary (spoilers ok): <input type="text" name="summary" maxlength="255" style="width:40em;" value="<?php echo $summary; ?>" /></p>
                <p style="padding-top: 0.5em;">Description (spoilers ok):</p>
                <textarea style="width:50em; height: 25em;" name="description"><?php echo $description; ?></textarea>
                <p style="padding-top: 0.5em;">
                        <input type="submit" name="editTSD" value="Change" />
                        <input type="submit" name="cancelTSD" value="Cancel" />
                </p>
        </form>
<?php
}

function displayPeople($uid, $pid)
{
?>
        <table>
        <?php displayAuthors($uid, $pid); ?>
        <?php displaySpoiled($uid, $pid); ?>
        <?php displayRoundCaptain($uid, $pid); ?>
        <?php displayEditors($uid, $pid); ?>
        <?php displayFactcheckers($uid, $pid); ?>
        <?php if (canSeeTesters($uid, $pid)) {displayTesters($uid, $pid);} ?>
        <?php displayTestingAdmin($uid, $pid); ?>
        </table>
<?php
}

function displayAuthors($uid, $pid)
{
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
                                                        <?php echo displayRemoveAuthor($pid); ?>
                                                </td>
                                                <td>
                                                        <p><strong>Add Author(s):</strong></p>
                                                        <?php echo displayAddAuthor($pid); ?>
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

function displayRemoveAuthor($pid)
{
        $authors = getAuthorsForPuzzle($pid);
        if ($authors != NULL)
                makeOptionElements($authors, 'removeAuth');
}

function displayAddAuthor($pid)
{
        $authors = getAvailableAuthorsForPuzzle($pid);
        if ($authors != NULL)
                makeOptionElements($authors, 'addAuth');
}

function displaySpoiled($uid, $pid)
{
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
                                                        <?php echo displayRemoveSpoiledUsers($pid); ?>
                                                </td>
                                                <td>
                                                        <p><strong>Add Spoiled:</strong></p>
                                                        <?php echo displayAddSpoiledUsers($pid); ?>
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

function displayRemoveSpoiledUsers($pid)
{
        $spoiled = getSpoiledUsersForPuzzle($pid);
        if ($spoiled != NULL)
                makeOptionElements($spoiled, 'removeSpoiledUser');
}

function displayAddSpoiledUsers($pid)
{
        $spoiled = getAvailableSpoiledUsersForPuzzle($pid);
        if ($spoiled != NULL)
                makeOptionElements($spoiled, 'addSpoiledUser');
}

function displayRoundCaptain($uid, $pid)
{
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
                                                        <?php echo displayRemoveRoundCaptain($pid); ?>
                                                </td>
                                                <td>
                                                        <p><strong>Add Round Captain(s):</strong></p>
                                                        <?php echo displayAddRoundCaptain($pid); ?>
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

function displayRemoveRoundCaptain($pid)
{
        $capts = getRoundCaptainsForPuzzle($pid);
        if ($capts != NULL)
                makeOptionElements($capts, 'removeRoundCaptain');
}

function displayAddRoundCaptain($pid)
{
        $capts = getAvailableRoundCaptainsForPuzzle($pid);
        if ($capts != NULL)
                makeOptionElements($capts, 'addRoundCaptain');
}

function displayEditors($uid, $pid)
{
?>
                <tr>
                        <td class='peopleInfo'>
                                <strong>Editors:</strong> <?php echo getEditorsAsList($pid); ?>&nbsp;&nbsp;<?php if (!isAuthorOnPuzzle($uid, $pid)) { ?><a href="#" class="changeLink">[Change]</a>
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
                                                        <p><strong>Remove Editor(s):</strong></p>
                                                        <?php echo displayRemoveEditor($pid)?>
                                                </td>
                                                <td>
                                                        <p><strong>Add Editor(s):</strong></p>
                                                        <?php echo displayAddEditor($pid); ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td colspan="2">
                                                        <input type="submit" name="changeEditors" value="Change Editors" />
                                                </td>
                                        </tr>
                                        </form>
                                </table><?php } ?>
                        </td>
                </tr>
<?php
}

function displayRemoveEditor($pid)
{
        $editors = getEditorsForPuzzle($pid);
        if ($editors != NULL)
                makeOptionElements($editors, 'removeEditor');
}

function displayAddEditor($pid)
{
        $editors = getAvailableEditorsForPuzzle($pid);
        if ($editors != NULL)
                makeOptionElements($editors, 'addEditor');
}

function displayFactcheckers($uid, $pid)
{
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
                                                        <?php echo displayRemoveFactchecker($pid)?>
                                                </td>
                                                <td>
                                                        <p><strong>Add Factchecker(s):</strong></p>
                                                        <?php echo displayAddFactchecker($pid); ?>
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

function displayRemoveFactchecker($pid)
{
        $factcheckers = getFactcheckersForPuzzle($pid);
        if ($factcheckers != NULL)
                makeOptionElements($factcheckers, 'removeFactchecker');
}

function displayAddFactchecker($pid)
{
        $factcheckers = getAvailableFactcheckersForPuzzle($pid);
        if ($factcheckers != NULL)
                makeOptionElements($factcheckers, 'addFactchecker');
}

function displayTesters($uid, $pid)
{
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
                </tr>
<?php
}

function displayTestingAdmin($uid, $pid)
{
?>
                <tr>
                        <td class='peopleInfo'>
                                <strong>Testing Admin: </strong><?php echo getTestingAdminsForPuzzleAsList($pid); ?>
                        </td>
                </tr>
<?php
}

function displayStatus($uid, $pid)
{
        $status = getStatusNameForPuzzle($pid);

?>
        <table class="statusInfo">
                <tr>
                        <td class='statusInfo'>
                                <strong>Puzzle Status: </strong> <?php echo $status; ?>
                        </td>
                        <td class='statusInfo'>
                                <?php if (canChangeStatus($uid) && !isAuthorOnPuzzle($uid, $pid)) { ?><a href="#" class="changeLink">[Change]</a><?php } ?>
                        </td>
                </tr>
<?php
        if (canChangeStatus($uid) && !isAuthorOnPuzzle($uid, $pid))
        {
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
                                                        <input type="submit" name="changeStatus" value="Change Status" />
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

function displayTestsolveRequests($uid, $pid)
{
        $reqs = getTestsolveRequestsForPuzzle($pid);

?>
        <table class="testsolveInfo">
                <tr>
                        <td class='testsolveInfo'>
                                <strong>Active Testsolve Requests: </strong> <?php echo $reqs; ?>
                        </td>
                        <td class='testsolveInfo'>
                                <?php
                                if (canRequestTestsolve($uid, $pid))
                                {
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
        if (canRequestTestsolve($uid, $pid))
        {
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

function displayCredits($uid, $pid)
{
        $notes = getCredits($pid);

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
                                        <input type="text" name="credits" maxlength="255" style="width:40em;" value="<?php echo $notes; ?>"/>
                                        <input type="submit" name="changeCredits" value="Change" />
                                </form>
                        </td>
                </tr>
        </table>
<?php
}
function displayNotes($uid, $pid)
{
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
                                        <input type="text" name="notes" maxlength="255" style="width:40em;" value="<?php echo $notes; ?>"/>
                                        <input type="submit" name="changeNotes" value="Change" />
                                </form>
                        </td>
                </tr>
        </table>
<?php
}

function displayWikiPage($uid, $pid)
{
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
                                        <input type="text" name="wikiPage" maxlength="255" style="width:40em;" value="<?php echo $page; ?>"/>
                                        <input type="submit" name="changeWikiPage" value="Change" />
                                </form>
                        </td>
                </tr>
        </table>
<?php
}

function displayChangePuzzleStatus($pid)
{
        $statuses = getPuzzleStatuses();
        $current = getStatusForPuzzle($pid);

        foreach ($statuses as $sid => $name) {
                echo "<input type ='radio' name='status' value='$sid'";
                if ($sid == $current)
                        echo ' checked';
                echo " />&nbsp;&nbsp;$name<br />";
        }
}

function displayFiles($uid, $pid)
{
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

        if ($fileList == NULL) {
                $file['filename'] = '(none)';
                $file['date'] = NULL;
                $fileList[] = $file;
        }

        foreach ($fileList as $file) {
                $finfo = pathinfo($file['filename']);
                $filename = $finfo['basename'];
                $link = $file['filename'];
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

                if ($first)
                        $first = FALSE;
        }
}

function displayPostProd($uid, $pid, $pp)
{
  $rinfo = getRoundForPuzzle($pid);
  $urlprefix = POSTPROD_URLPREFIX;
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
  $url = $urlprefix;
  $url .= "/" . postprodCanonRound($roundname);
  $url .= "/beta_" . postprodCanon($title) . "/";
  if ($pp) {
?>
  <strong>Post-Production Beta Link: </strong>
  <a href="<?php echo $url ?>">View postprod (as pushed from puzzletron)</a>
  <br>
<?php
  }
  $url = $urlprefix;
  $url .= "/" . postprodCanonRound($roundname);
  $url .= "/" . postprodCanon($title) . "/";
?>
  <strong>Post-Production Final Link: </strong>
  <a href="<?php echo $url ?>">View postprod (version installed as final)</a>
  <br>
<?php
  if ($pp) {
?>
  <form action="form-submit.php" method="post">
    <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
    <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
    <input type="submit" name="postprod" value="Push this puzzle to post-production">
  </form>
<?php
  }
}

function displayComments($uid, $pid, $lastVisit)
{
        $comments = getComments($pid);
        if ($comments == NULL)
                return;

        foreach ($comments as $comment)
        {
                $id = $comment['id'];
                $timestamp = $comment['timestamp'];
                $type = $comment['name'];
                $user = $comment['uid'];

                if ($user == 0)
                        $name = 'Server';
                else
                        $name = getUserName($user);

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
                        echo 'Testsolver '.substr(md5(strval($pid).strval($user)),0,8);
                } else
                        echo $name;

                echo "<br />$timestamp<br />$type <small>(Comment #$id)</small>";
                echo "<td class='$type" . "Comment'>";
                echo nl2br($comment['comment']);
                echo '</td>';
                echo '</tr>';
        }
}

function addCommentForm($uid, $pid)
{
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

function emailSubButton($uid, $pid)
{
        if (isSubbedOnPuzzle($uid, $pid))
        {
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
?>
