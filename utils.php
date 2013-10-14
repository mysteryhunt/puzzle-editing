<?php
        require_once "config.php";
        require_once "db-func.php";

// Check that the user is logged in.
// If so, update the session (to provent timing out) and return the uid;
// If not, redirect to login page.
function isLoggedIn()
{
        if (isset($_SESSION['uid']) && ($_SESSION['SITEURL'] == URL)) {
                $_SESSION['time'] = time();
                return $_SESSION['uid'];
        } else {
                unset($_SESSION['uid']);
                $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
                header("Location: " . URL . "/login.php");
                exit(0);
        }
}

function validUserId($uid)
{
        $sql = sprintf("SELECT * FROM user_info WHERE uid='%s'", mysql_real_escape_string($uid));
        return has_result($sql);
}

function isValidPuzzleFilter()
{
        if (isset($_GET['filterkey']) && isset($_GET['filtervalue'])) {
                $key = $_GET['filterkey'];
                if ($key != "status" && $key != "author" && $key != "editor") {
                        echo "Invalid sort key. What did you even do?";
                        foot();
                        exit(1);
                }
                $val = $_GET['filtervalue'];
                if ($key == "status" && !validPuzzleStatus($val)) {
                        echo "Invalid puzzle status ID.";
                        foot();
                        exit(1);
                }
                if (($key == "author" || $key == "editor") && !validUserId($val)) {
                        echo "Invalid user ID.";
                        foot();
                        exit(1);
                }
                return array($key, $val);
        }
        return array();
}

// Check that a valid puzzle is given in the URL.
function isValidPuzzleURL()
{
        if (!isset($_GET['pid'])) {
                echo "Puzzle ID not found. Please try again.";
                foot();
                exit(1);
        }

        $pid = $_GET['pid'];

        $sql = sprintf("SELECT * FROM puzzle_idea WHERE id='%s'",
                        mysql_real_escape_string($pid));
        if (!has_result($sql)) {
                echo "Puzzle ID not valid. Please try again.";
                foot();
                exit(1);
        }

        return $pid;
}


// convert puzzle names to canonical form used on post-prod site
function postprodCanon($s)
{
  $s = strtolower(trim($s));
  $s = preg_replace('/[\']([st])\b/', '$1', $s);
  $s = preg_replace('/[^a-z0-9]+/', '_', $s);
  return trim($s, "_");
}

function postprodCanonRound($s)
{
  $roundslugmap = array(
    'Get Smart' => 'get_smart',
    'Sneakers' => 'sneakers',
    'Rubik' => 'rubik',
    'Feynman' => 'feynman',
    "Ocean's 11" => 'oceans_11',
    'Indiana Jones' => 'indiana',
    'Movable answers' => 'movable',
    'Opening' => 'enigmavalley');
  $s = $roundslugmap[$s];
  return $s;
}

function postprodAll($uid)
{
    @ini_set('zlib.output_compression',0);
    @ini_set('implicit_flush',1);
    @ob_end_clean();
    set_time_limit(0);
    header( 'Content-type: text/plain; charset=utf-8' );
    print "Postprodding all...\n\n";
    ob_flush(); flush();
    $allofem = getPuzzlesInPostprodAndLater();
    foreach ($allofem as $puz) {
        print "$puz ... ";
        ob_flush(); flush();
        $out = pushToPostProdHelper($uid, $puz);
        if (!$out) {
            print "OK\n";
        } else {
            print "FAILED\n\n$out\n\n";
        }
        ob_flush(); flush();
    }
    exit(1);
}

function pushToPostProd($uid, $pid)
{
  $out = pushToPostProdHelper($uid, $pid);
  if ($out) {
    utilsError($out);
  }
}

function pushToPostProdHelper($uid, $pid) {
  $rinfo = getRoundForPuzzle($pid);
  #$runscript = "/usr/bin/env | grep ^CATTLEPROD";
  #$runscript = "/srv/veil/venv/bin/cattleprod 2>&1";
  $runscript = "/nfs/sages/deploy/mh2013/present/bin/cattleprod 2>&1";
  $roundname = $rinfo['name'];
  $roundslug = postprodCanonRound($roundname);
  $title = getTitle($pid);
  $titleslug = postprodCanon($title);
  $fileList = getFileListForPuzzle($pid, 'postprod');
  $file = $fileList[0];
  $filename = $file['filename'];
  if (empty($filename)) {
    return "Nothing in the postproduction slot of this puzzle: Nothing to push!";
  }
  $fileprefix = "/srv/puzzle-editing/";
  putenv("CATTLEPROD_PUZZLE_SLUG=beta_" . $titleslug); 
  putenv("CATTLEPROD_ROUND_SLUG=" . $roundslug);
  putenv("CATTLEPROD_TITLE=" . $title);
  putenv("CATTLEPROD_MEDIA=" . $fileprefix . $filename);
  putenv("CATTLEPROD_ASSET_PATH=/nfs/enigma/mh2013/chazelle/assets");
  $output = shell_exec($runscript);
  if ($output) {
    return "Push failed: $output";
  }
}

function isStatusInPostProd($sid)
{
        $sql = sprintf("SELECT postprod FROM pstatus WHERE id='%s'", mysql_real_escape_string($sid));
        return get_element($sql) == 1;
}

function isEditor($uid)
{
        return hasPriv($uid, 'addToEditingQueue');
}

function isAutoSubEditor($uid)
{
	return hasPriv($uid, 'autoSubEditor');
}

function isRoundCaptain($uid)
{
        return hasPriv($uid, 'addToRoundCaptainQueue');
}

function isTestingAdmin($uid)
{
        return hasPriv($uid, 'seeTesters');
}

function isLurker($uid)
{
        return hasPriv($uid, 'isLurker');
}

function isFactChecker($uid)
{
        //return hasPriv($uid, 'factcheck');
        return TRUE;
}

function isBlind($uid)
{
        return hasPriv($uid, 'isBlind');
}

function isServerAdmin($uid)
{
        return hasPriv($uid, 'changeServer');
}

function canChangeStatus($uid)
{
        return hasPriv($uid, 'changeStatus');
}

function canRequestTestsolve($uid, $pid)
{
        return isPuzzleInTesting($pid) &&
                canViewPuzzle($uid, $pid);
        // Should maybe be stricter than 'can view', but 'is editor on puzzle'
        // is too strict because it excludes the testsolve admins / chief
        // editors / chief producers / etc.
}

function hasPriv($uid, $priv)
{
        $sql = sprintf("SELECT name FROM jobs LEFT JOIN priv ON jobs.jid = priv.jid
                                        WHERE uid='%s' AND %s='1'",
                                        mysql_real_escape_string($uid), mysql_real_escape_string($priv));
        return has_result($sql);
}


function isAuthorOnPuzzle($uid, $pid)
{
        $sql = sprintf("SELECT * FROM authors WHERE uid='%s' AND pid='%s'",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        return has_result($sql);
}

function isRoundCaptainOnPuzzle($uid, $pid)
{
        $sql = sprintf("SELECT * FROM round_captain_queue WHERE uid='%s' AND pid='%s'",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        return has_result($sql);
}

function isEditorOnPuzzle($uid, $pid)
{
        $sql = sprintf("SELECT * FROM editor_queue WHERE uid='%s' AND pid='%s'",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        return has_result($sql);
}

function isTesterOnPuzzle($uid, $pid)
{
        $sql = sprintf("SELECT * FROM test_queue WHERE uid='%s' AND pid='%s'",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        return has_result($sql);
}

function isFactcheckerOnPuzzle($uid, $pid)
{
  $sql = sprintf("SELECT * FROM factcheck_queue WHERE uid='%s' AND pid='%s'",
      mysql_real_escape_string($uid), mysql_real_escape_string($pid));
  return has_result($sql);
}

function isFormerTesterOnPuzzle($uid, $pid)
{
        $sql = sprintf("SELECT * FROM doneTesting WHERE uid='%s' AND pid='%s'",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        return has_result($sql);
}

function isSpoiledOnPuzzle($uid, $pid)
{
        $sql = sprintf("SELECT * FROM spoiled WHERE uid='%s' AND pid='%s'",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        return has_result($sql);
}

function isTestingAdminOnPuzzle($uid, $pid)
{
        $sql = sprintf("SELECT * FROM testAdminQueue WHERE uid='%s' AND pid='%s'",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        return has_result($sql);
}



function updateLastVisit($uid, $pid)
{
        // Get previous visit time
        $lastVisit = getLastVisit($uid, $pid);

        // Store this visit in last_visit table
        $sql = sprintf("INSERT INTO last_visit (pid, uid, date) VALUES ('%s', '%s', NOW())
                                        ON DUPLICATE KEY UPDATE date=NOW()", mysql_real_escape_string($pid),
                                        mysql_real_escape_string($uid));
        query_db($sql);

        return $lastVisit;
}


function getLastVisit($uid, $pid)
{
        $sql = sprintf("SELECT date FROM last_visit WHERE pid='%s' AND uid='%s'",
                        mysql_real_escape_string($pid), mysql_real_escape_string($uid));
        return get_element_null($sql);
}

function getUserUsername($uid)
{
        $sql = sprintf("SELECT username FROM user_info WHERE uid='%s'", mysql_real_escape_string($uid));
        return get_element($sql);
}

function getUserName($uid)
{
        $sql = sprintf("SELECT fullname FROM user_info WHERE uid='%s'", mysql_real_escape_string($uid));
        return get_element($sql);
}

function getEmail($uid)
{
        $sql = sprintf("SELECT email FROM user_info WHERE uid='%s'",
                                        mysql_real_escape_string($uid));
        return get_element($sql);
}


// Get associative array of users' uid and name
function getUsersForPuzzle($table, $pid)
{
        // This is only called from the below functions, where $table is a hardcoded string
        $sql = sprintf("SELECT user_info.uid, user_info.fullname FROM user_info INNER JOIN %s ON user_info.uid=%s.uid WHERE %s.pid='%s'",
                                        $table, $table, $table, mysql_real_escape_string($pid));
        return get_assoc_array($sql, "uid", "fullname");
}

function getAuthorsForPuzzle($pid)
{
        return getUsersForPuzzle("authors", $pid);
}

function getRoundCaptainsForPuzzle($pid)
{
        return getUsersForPuzzle("round_captain_queue", $pid);
}

function getEditorsForPuzzle($pid)
{
        return getUsersForPuzzle("editor_queue", $pid);
}

function getSpoiledUsersForPuzzle($pid)
{
        return getUsersForPuzzle("spoiled", $pid);
}

function getFactcheckersForPuzzle($pid)
{
        return getUsersForPuzzle("factcheck_queue", $pid);
}


function getTestAdminsToNotify($pid)
{
        $table = 'testAdminQueue';
        $sql = sprintf("SELECT user_info.uid FROM user_info INNER JOIN %s ON user_info.uid=%s.uid WHERE %s.pid='%s'",
                                        $table, $table, $table, mysql_real_escape_string($pid));
        $testadmins_for_puzzle = get_elements_null($sql);

        $sql = "select user_info.uid from user_info, jobs where user_info.uid=jobs.uid and (jobs.jid=6 or jobs.jid=13);";
        $all_testadmins = get_elements_null($sql);

        // If a puzzle has testadmins, they will be auto-subscribed to
        // comments. Prevent them from getting notified twice.
        return ($testadmins_for_puzzle == NULL ? $all_testadmins : array());
}

// Get comma-separated list of users' names
function getUserNamesAsList($table, $pid)
{
        // This is only called from the below functions, where $table is a hardcoded string
        $sql = sprintf("SELECT user_info.fullname FROM user_info INNER JOIN %s ON user_info.uid=%s.uid WHERE %s.pid='%s'",
                                        $table, $table, $table, mysql_real_escape_string($pid));
        $users = get_elements_null($sql);

        return ($users == NULL ? "(none)" : implode(", ", $users));
}

function getAuthorsAsList($pid)
{
        return getUserNamesAsList("authors", $pid);
}

function getRoundCaptainsAsList($pid)
{
        return getUserNamesAsList("round_captain_queue", $pid);
}

function getEditorsAsList($pid)
{
        return getUserNamesAsList("editor_queue", $pid);
}

function getTestingAdminsForPuzzleAsList($pid)
{
        return getUserNamesAsList("testAdminQueue", $pid);
}

function getCurrentTestersAsList($pid)
{
        return getUserNamesAsList("test_queue", $pid);
}

function getSpoiledAsList($pid)
{
        return getUserNamesAsList("spoiled", $pid);
}

function getFactcheckersAsList($pid)
{
  return getUserNamesAsList("factcheck_queue", $pid);
}

function getFinishedTestersAsList($pid)
{
        return getUserNamesAsList("doneTesting", $pid);
}


// Get comma-separated list of users' names, with email addresses
function getUserNamesAndEmailsAsList($users)
{
        if ($users == NULL)
                return '(none)';

        $list = '';
        foreach ($users as $uid) {
                if ($list != '')
                        $list .= ', ';

                $name = getUserName($uid);
                $email = getEmail($uid);

                $list .= "<a href='mailto:$email'>$name</a>";
        }

        return $list;
}

function getUserJobsAsList($uid)
{
        $sql = sprintf("SELECT priv.name FROM jobs, priv WHERE jobs.uid='%s' AND jobs.jid=priv.jid",
                                        mysql_real_escape_string($uid));
        $result = get_elements_null($sql);

        if ($result == NULL)
                return '';

        else
                return implode(', ', $result);
}



function isAnyAuthorBlind($pid)
{
        $authors = getAuthorsForPuzzle($pid);

        if ($authors == NULL)
                return FALSE;

        foreach ($authors as $author) {
                if (isBlind($author))
                        return TRUE;
        }

        return FALSE;
}





function getPuzzleInfo($pid)
{
        $sql = sprintf("SELECT * FROM puzzle_idea WHERE id='%s'", mysql_escape_string($pid));
        return get_row($sql);
}

function getTitle($pid)
{
        $sql = sprintf("SELECT title FROM puzzle_idea WHERE id='%s'", mysql_real_escape_string($pid));
        return get_element($sql);
}

function getCredits($pid)
{
        $sql = sprintf("SELECT credits FROM puzzle_idea WHERE id='%s'", mysql_real_escape_string($pid));
        return get_element($sql);
}

function getNotes($pid)
{
        $sql = sprintf("SELECT notes FROM puzzle_idea WHERE id='%s'", mysql_real_escape_string($pid));
        return get_element($sql);
}

function getCurMotd()
{
        $sql = sprintf("SELECT * FROM motd ORDER BY time DESC LIMIT 1");
        return get_row_null($sql);
}

function getAllMotd()
{
        $sql = sprintf("SELECT * FROM motd ORDER BY time DESC");
        return get_rows_null($sql);
}

// Update the title, summary, and description of the puzzle (from form on puzzle page)
function changeTitleSummaryDescription($uid, $pid, $title, $summary, $description)
{
        // Check that user can view the puzzle
        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify this puzzle.");

        // Get the old title, summary, and description
        $puzzleInfo = getPuzzleInfo($pid);
        $oldTitle = $puzzleInfo["title"];
        $oldSummary = $puzzleInfo["summary"];
        $oldDescription = $puzzleInfo["description"];

        $purifier = new HTMLPurifier();
        mysql_query('START TRANSACTION');

        // If title has changed, update it
        $cleanTitle = $purifier->purify($title);
        $cleanTitle = htmlspecialchars($cleanTitle);
        if ($oldTitle !== $cleanTitle) {
                updateTitle($uid, $pid, $oldTitle, $cleanTitle);
        }

        // If summary has changed, update it
        $cleanSummary = $purifier->purify($summary);
        $cleanSummary = htmlspecialchars($cleanSummary);
        if ($oldSummary !== $cleanSummary) {
                updateSummary($uid, $pid, $oldSummary, $cleanSummary);
        }

        // If description has changed, update it
        $cleanDescription = $purifier->purify($description);
        if ($oldDescription !== $cleanDescription) {
                updateDescription($uid, $pid, $oldDescription, $cleanDescription);
        }

        // Assuming all went well, commit the changes to the database
        mysql_query('COMMIT');
}

function updateTitle($uid = 0, $pid, $oldTitle, $cleanTitle)
{
        $sql = sprintf("UPDATE puzzle_idea SET title='%s' WHERE id='%s'",
                        mysql_real_escape_string($cleanTitle), mysql_real_escape_string($pid));
                        query_db($sql);

        $comment = "Changed title from \"$oldTitle\" to \"$cleanTitle\"";

        addComment($uid, $pid, $comment, TRUE);
}


function updateSummary($uid = 0, $pid, $oldSummary, $cleanSummary)
{
        $sql = sprintf("UPDATE puzzle_idea SET summary='%s' WHERE id='%s'",
                        mysql_real_escape_string($cleanSummary), mysql_real_escape_string($pid));
        query_db($sql);

        $comment = "<p><strong>Changed summary from:</strong></p>\"$oldSummary\"";

        addComment($uid, $pid, $comment, TRUE);
}

function updateDescription($uid = 0, $pid, $oldDescription, $cleanDescription)
{
        $sql = sprintf("UPDATE puzzle_idea SET description='%s' WHERE id='%s'",
                        mysql_real_escape_string($cleanDescription), mysql_real_escape_string($pid));
        query_db($sql);

        $id = time();

        $comment = "<p><strong>Changed description</strong></p>";
        $comment .= "<p><a class='description' href='#'>[View Old Description]</a></p>";
        $comment .= "<p>$oldDescription</p>";

        addComment($uid, $pid, $comment, TRUE);
}

function updateCredits($uid = 0, $pid, $oldCredits, $cleanCredits)
{
        $sql = sprintf("UPDATE puzzle_idea SET credits='%s' WHERE id='%s'",
                        mysql_real_escape_string($cleanCredits), mysql_real_escape_string($pid));
        query_db($sql);

        $comment = "Changed credits from \"$oldCredits\" to \"$cleanCredits\"";

        addComment($uid, $pid, $comment, TRUE);
}

function updateNotes($uid = 0, $pid, $oldNotes, $cleanNotes)
{
        $sql = sprintf("UPDATE puzzle_idea SET notes='%s' WHERE id='%s'",
                        mysql_real_escape_string($cleanNotes), mysql_real_escape_string($pid));
        query_db($sql);

        $comment = "Changed status notes from \"$oldNotes\" to \"$cleanNotes\"";

        addComment($uid, $pid, $comment, TRUE);
}

function updateWikiPage($uid = 0, $pid, $oldWikiPage, $cleanWikiPage)
{
        $sql = sprintf("UPDATE puzzle_idea SET wikipage ='%s' WHERE id='%s'",
                        mysql_real_escape_string($cleanWikiPage), mysql_real_escape_string($pid));
        query_db($sql);

        $comment = "Changed testsolve wiki page from \"$oldWikiPage\" to \"$cleanWikiPage\"";

        addComment($uid, $pid, $comment, TRUE);
}

// Get the current answers (including answer id) for a puzzle
// Return an assoc array of type [aid] => [answer]
function getAnswersForPuzzle($pid)
{
        $sql = sprintf("SELECT aid, answer FROM answers WHERE pid='%s'",
                        mysql_real_escape_string($pid));
        return get_assoc_array($sql, "aid", "answer");
}

// Get the current answers for a puzzle as a comma separated list
function getAnswersForPuzzleAsList($pid)
{
        $sql = sprintf("SELECT answer FROM answers WHERE pid='%s'",
                        mysql_real_escape_string($pid));
        $answers = get_elements_null($sql);

        if ($answers == NULL)
                return '';
        else
                return implode(', ', $answers);
}

// Get available answers
// Return an assoc array of type [aid] => [answer]
function getAvailableAnswers()
{
        $answers = get_assoc_array("SELECT aid, answer FROM answers WHERE pid IS NULL", "aid", "answer");
        if ($answers != NULL)
                natcasesort($answers);
        return $answers;
}

// Get count of total answers
function numAnswers()
{
        $sql = sprintf("SELECT count(*) FROM answers");
        return get_element($sql);
}

// Get count of answers that have been assigned
function answersAssigned()
{
        $sql = sprintf("SELECT count(*) FROM answers WHERE pid IS NOT NULL");
        return get_element($sql);
}

// Add and remove puzzle answers
function changeAnswers($uid, $pid, $add, $remove)
{
        mysql_query('START TRANSACTION');
        addAnswers($uid, $pid, $add);
        removeAnswers($uid, $pid, $remove);
        mysql_query('COMMIT');
}

function addAnswers($uid, $pid, $add)
{
        if ($add == NULL)
                return;

        if (!canChangeAnswers($uid))
                utilsError("You do not have permission to add answers.");

        foreach ($add as $ans) {
                // Check that this answer is available for assignment
                if (!isAnswerAvailable($ans)) {
                        utilsError(getAnswerWord($ans) . ' is not available.');
                }

                // Add answer to puzzle
                $sql = sprintf("UPDATE answers SET pid='%s' WHERE aid='%s'",
                                mysql_real_escape_string($pid), mysql_real_escape_string($ans));
                query_db($sql);
        }

        $comment = "Assigned answer";
        if (count($add) > 1)
                $comment .= "s";

        addComment($uid, $pid, $comment, TRUE);
}

function removeAnswerKill($uid, $pid, $ans)
{
        //echo "called: removeAnswerKill with ansid= $ans<br>";

        // Check that this answer is assigned to this puzzle
        if (!isAnswerOnPuzzle($pid, $ans))
                        utilsError(getAnswerWord($ans) . " is not assigned to puzzle $pid");

        // Remove answer from puzzle
        $sql = sprintf("UPDATE answers SET pid=NULL WHERE aid='%s'",
                                mysql_real_escape_string($ans));
        query_db($sql);

        $comment = "Unassigned answer";

        addComment($uid, $pid, $comment, TRUE);
}
        
function removeAnswers($uid, $pid, $remove)
{
        if ($remove == NULL)
                return;

        if (!isAuthorOnPuzzle($uid, $pid) && !canChangeAnswers($uid))
                utilsError("You do not have permission to remove answers.");

        foreach ($remove as $ans) {
                // Check that this answer is assigned to this puzzle
                if (!isAnswerOnPuzzle($pid, $ans))
                        utilsError(getAnswerWord($ans) . " is not assigned to puzzle $pid");

                // Remove answer from puzzle
                $sql = sprintf("UPDATE answers SET pid=NULL WHERE aid='%s'",
                                mysql_real_escape_string($ans));
                query_db($sql);
        }

        $comment = "Unassigned answer";
        if (count($remove) > 1)
                $comment .= "s";

        addComment($uid, $pid, $comment, TRUE);
}

function isAnswerAvailable($aid)
{
        $sql = sprintf("SELECT * FROM answers WHERE aid='%s' AND pid IS NULL",
                        mysql_real_escape_string($aid));

        return has_result($sql);
}

function isAnswerOnPuzzle($pid, $aid)
{
        $sql = sprintf("SELECT * FROM answers WHERE aid='%s' AND pid='%s'",
                        mysql_real_escape_string($aid), mysql_real_escape_string($pid));

        return has_result($sql);
}

function getAnswerWord($aid)
{
        $sql = sprintf("SELECT answer FROM answers WHERE aid='%s'",
                        mysql_real_escape_string($aid));
        $ans = get_element_null($sql);

        if ($ans == NULL)
                utilsError("$aid is not a valid answer id");

        return $ans;
}


function addComment($uid, $pid, $comment, $server = FALSE, $testing = FALSE)
{
        $purifier = new HTMLPurifier();
        $cleanComment = $purifier->purify($comment);

        if ($server == TRUE) {
                $typeName = "Server";
        } else if ($testing == TRUE) {
                $typeName = "Testsolver";
        } else if (isAuthorOnPuzzle($uid, $pid)) {
                $typeName = "Author";
        } else if (isEditorOnPuzzle($uid, $pid)) {
                $typeName = "Editor";
        } else if (isTesterOnPuzzle($uid, $pid)) {
                $typeName = "Testsolver";
        } else if (isTestingAdminOnPuzzle($uid, $pid)) {
                $typeName = "TestingAdmin";
        } else if (isLurker($uid)) {
                $typeName = "Lurker";
        } else {
                $typeName = "Unknown";
        }

        $sql = sprintf("SELECT id FROM comment_type WHERE name='%s'", mysql_real_escape_string($typeName));
        $type = get_element($sql);

        $sql = sprintf("INSERT INTO comments (uid, comment, type, pid) VALUES ('%s', '%s', '%s', '%s')",
                        mysql_real_escape_string($uid), mysql_real_escape_string($cleanComment),
                        mysql_real_escape_string($type), mysql_real_escape_string($pid));
        query_db($sql);

        if ($typeName == "Testsolver")
                emailComment($uid, $pid, $cleanComment, TRUE);
        else
                emailComment($uid, $pid, $cleanComment);
}

function createAnswer($answer, $round)
{
        $sql = sprintf("INSERT INTO answers (answer) VALUES ('%s')", mysql_real_escape_string($answer));
        query_db($sql);
        $sql = "SELECT LAST_INSERT_ID()";
        $result = query_db($sql);
        $resultrow = mysql_fetch_row($result);
        $aid = $resultrow[0];
        $sql = sprintf("INSERT INTO answers_rounds (aid, rid) VALUES ('%s', '%s')", $aid, $round);
        $result = query_db($sql);
        return ($result);
}

function createRound($round, $roundanswer)
{
        $sql = sprintf("INSERT INTO rounds (name, answer) VALUES ('%s', '%s')", 
                mysql_real_escape_string($round), mysql_real_escape_string($roundanswer));
        $result = query_db($sql);
        return ($result);
} 

function addNewMotd($message)
{
        $sql = sprintf("INSERT INTO motd (message, uid) VALUES ('%s', '%s')",
                mysql_real_escape_string($message),$_SESSION['uid']);
        $result = query_db($sql);
        return ($result);
}
function requestTestsolve($uid, $pid, $notes)
{
        $sql = sprintf("INSERT INTO testsolve_requests (pid, uid, notes) VALUES ('%s', '%s', '%s')",
                       mysql_real_escape_string($pid),
                       mysql_real_escape_string($uid),
                       mysql_real_escape_string($notes));
        query_db($sql);
        addComment($uid, $pid, "Requested a testsolve (Notes: $notes)", TRUE);
}

function clearOneTestsolveRequest($pid)
{
        // Clears just the oldest testsolve request.
        //
        // This is just a touch horrifying. In particular, the 'select * from'
        // is a workaround for a mysql limitation.
        // benoc note: come on, don't be horrified.  that's what the LIMIT 1 is for.
        $sql = sprintf("update testsolve_requests set done=1 where pid='%d' and done=0 and
                        timestamp=(select * from (select timestamp from testsolve_requests where
                        pid='%d' and done=0 order by timestamp limit 1) as a)",
                       mysql_real_escape_string($pid),
                       mysql_real_escape_string($pid));
        query_db($sql);
}

function clearTestsolveRequests($pid)
{
        $sql = sprintf("UPDATE testsolve_requests SET done=1 where pid='%d'",
                       mysql_real_escape_string($pid));
        query_db($sql);
}

function emailComment($uid, $pid, $cleanComment, $isTestsolveComment = FALSE)
{
        if ($isTestsolveComment && ANON_TESTERS)
                $name = "Anonymous Testsolver";
        else
                $name = getUserName($uid);

        $message = "$name commented on puzzle $pid:\n";
        $message .= "$cleanComment";
        $title = getTitle($pid);
        $subject = "Comment on $title (puzzle $pid)";
        $link = URL . "/puzzle.php?pid=$pid";

        $users = getSubbed($pid);
        $admins = array();
        if ($isTestsolveComment) {
                $admins = getTestAdminsToNotify($pid);
        }
        if ($users == NULL)
                $users = array();
        if ($admins == NULL)
                $admins = array();

        foreach ($users as $user)
        {
                if ($user != $uid) {
                        sendEmail($user, $subject, $message, $link);
                }
        }
        foreach ($admins as $user)
        {
                // NB: If a puzzle has no testadmin assigned, all testing
                // directors get mail. If one of them is also an
                // author/editor/etc., they will get mail twice. This is
                // arguably not great, but we'll live with it.
                if ($user != $uid) {
                        sendEmail($user, "[Testsolve] $subject", $message, $link);
                }
        }
}

// Get uids of all users subscribed to receive email comments about this puzzle
function getSubbed($pid)
{
        $sql = sprintf("SELECT uid FROM email_sub WHERE pid='%s'",
                        mysql_real_escape_string($pid));
        return get_elements_null($sql);
}

function sendEmail($uid, $subject, $message, $link)
{
        $address = getEmail($uid);
        $msg = $message . "\n\n" . $link;

        $sql = sprintf("INSERT INTO email_outbox (address, subject, message)
                                           VALUES('%s', '%s', '%s')",
                                           mysql_real_escape_string($address),
                                           mysql_real_escape_string($subject),
                                           mysql_real_escape_string($msg));
        query_db($sql);
}

function realSendAllEmail()
{
        mysql_query("START TRANSACTION");
        $sql = ("SELECT * from email_outbox");
        $mails = get_rows($sql);
        $sql = ("DELETE from email_outbox");
        query_db($sql);
        mysql_query("COMMIT");

        if (is_null($mails))
        {
                return;
        }
        foreach ($mails as $mail)
        {
                $address = $mail[1];
                $subject = $mail[2];
                $msg = $mail[3];
                $headers = 'From: ' . PTRON_FROM_EMAIL . "\r\n";

                //subject line conditional on what instance of ptron this is
                if (DEVMODE)
                {
                        $subject = "PUZZLETRON-DEV: " . $subject;  
                }
                else if (PRACMODE)
                {
                        $subject = "PUZZLETRON-PRACTICE: " . $subject;
                }
                else
                {
                        $subject = "PUZZLETRON: " . $subject;
                }


                //ok, we want devmode to send emails also for testing subscriptions and stuff
                mail($address, $subject, $msg, $headers);

                //replace above line with this conditional to disable email in dev mode
                //if (!DEVMODE)
                //{
                //        mail($address, $subject, $msg, $headers);
                //} else {
                //        print "$address - $subject<BR>\n";
                //}
        }
}


// Get a list of users who are not authors or editors on a puzzle
// Return an assoc array of [uid] => [name]
function getAvailableAuthorsForPuzzle($pid)
{
        // Get all users
        $sql = 'SELECT uid FROM user_info';
        $users = get_elements($sql);

        $authors = NULL;
        foreach ($users as $uid) {
                if ($pid == FALSE || isAuthorAvailable($uid, $pid)) {
                        $authors[$uid] = getUserName($uid);
                }
        }

        // Sort by name
        natcasesort($authors);
        return $authors;
}

function getAvailableFactcheckersForPuzzle($pid)
{
        // Get all users
        $sql = 'SELECT uid FROM user_info';
        $users = get_elements($sql);
        if ($users == NULL) {
                $users = array();
        }

        $fcs = NULL;
        foreach ($users as $uid) {
                if ($pid == FALSE || isFactcheckerAvailable($uid, $pid)) {
                        $fcs[$uid] = getUserName($uid);
                }
        }

        // Sort by name
        natcasesort($fcs);
        return $fcs;
}

function getAvailableSpoiledUsersForPuzzle($pid)
{
        // Get all users
        $sql = 'SELECT uid FROM user_info';
        $users = get_elements($sql);

        $spoiled = NULL;
        foreach ($users as $uid) {
                if (!isSpoiledOnPuzzle($uid, $pid)) {
                        $spoiled[$uid] = getUserName($uid);
                }
        }

        // Sort by name
        natcasesort($spoiled);
        return $spoiled;
}


function isAuthorAvailable($uid, $pid)
{
        return (!isAuthorOnPuzzle($uid, $pid) && !isEditorOnPuzzle($uid, $pid) && !isTesterOnPuzzle($uid, $pid));
}

function isFactcheckerAvailable($uid, $pid)
{
  return (!isAuthorOnPuzzle($uid, $pid) && !isFactcheckerOnPuzzle($uid, $pid));
}

function defaultWikiPageForPuzzle($pid)
{
        return "http://manicsages.org/writingwiki/index.php?title=puzzle_$pid/Testsolve_1";
}

function getCurrentTestersAsEmailList($pid)
{
        $testers = getCurrentTestersForPuzzle($pid);
        if ($testers == NULL) {
                $testers = array();
        } else {
                $testers = array_keys($testers);
        }

        return getUserNamesAndEmailsAsList($testers);
}

function getCurrentTestersForPuzzle($pid)
{
        $sql = sprintf("SELECT uid FROM test_queue WHERE pid='%s'", mysql_real_escape_string($pid));
        $result = get_elements_null($sql);

        $testers = NULL;
        if ($result != NULL) {
                foreach ($result as $uid) {
                        $testers[$uid] = getUserName($uid);
                }
        }

        return $testers;
}

function getAvailableTestersForPuzzle($pid)
{
        // Get all users
        $sql = 'SELECT uid FROM user_info';
        $users = get_elements($sql);

        $testers = NULL;
        foreach ($users as $uid) {
                if (isTesterAvailable($uid, $pid)) {
                        $testers[$uid] = getUserName($uid);
                }
        }

        // Sort by name
        natcasesort($testers);
        return $testers;
}

function isTesterAvailable($uid, $pid)
{
        return (!isAuthorOnPuzzle($uid, $pid) && !isEditorOnPuzzle($uid, $pid));
}

function changeSpoiled($uid, $pid, $removeUser, $addUser)
{
        mysql_query('START TRANSACTION');
        removeSpoiledUser($uid, $pid, $removeUser);
        addSpoiledUser($uid, $pid, $addUser);
        mysql_query('COMMIT');
}

function removeSpoiledUser($uid, $pid, $removeUser)
{
        if ($removeUser == NULL)
                return;

        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify this puzzle.");

        $name = getUserName($uid);

        $comment = 'Removed ';
        foreach ($removeUser as $user) {
                if (!isSpoiledOnPuzzle($user, $pid))
                        utilsError(getUserName($user) . " is not spoiled on puzzle $pid.");

                $sql = sprintf("DELETE FROM spoiled WHERE uid='%s' AND pid='%s'",
                                mysql_real_escape_string($user), mysql_real_escape_string($pid));
                query_db($sql);


                // Add to comment
                if ($comment != 'Removed ')
                        $comment .= ', ';
                $comment .= getUserName($user);

                $title = getTitle($pid);
                $subject = "Spoiled on $title (puzzle $pid)";
                $message = "$name removed you as spoiled on $title (puzzle $pid).";
                $link = URL;
                sendEmail($user, $subject, $message, $link);
        }

        $comment .= ' as spoiled';

        addComment($uid, $pid, $comment, TRUE);
}

// Get editors who are not authors or editors on a puzzle
// Return assoc of [uid] => [name]
function getAvailableEditorsForPuzzle($pid)
{
        // Get all users
        $sql = 'SELECT uid FROM user_info';
        $users = get_elements_null($sql);

        $editors = NULL;
        if ($users != NULL) {
                foreach ($users as $uid) {
                        if (isEditorAvailable($uid, $pid)) {
                                $editors[$uid] = getUserName($uid);
                        }
                }
        }

        // Sort by name
        natcasesort($editors);
        return $editors;
}

function getAvailableRoundCaptainsForPuzzle($pid)
{
        // Get all users
        $sql = 'SELECT uid FROM user_info';
        $users = get_elements_null($sql);

        $capts = NULL;
        if ($users != NULL) {
                foreach ($users as $uid) {
                        if (isRoundCaptainAvailable($uid, $pid)) {
                                $capts[$uid] = getUserName($uid);
                        }
                }
        }

        // Sort by name
        natcasesort($capts);
        return $capts;
}

function addSpoiledUser($uid, $pid, $addUser)
{
        if ($addUser == NULL)
                return;

        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify this puzzle.");

        $name = getUserName($uid);

        $comment = 'Added ';
        foreach ($addUser as $user) {
                // Check that this author is available for this puzzle
                if (isSpoiledOnPuzzle($user, $pid)) {
                        utilsError(getUserName($user) . " is not spoilable on puzzle $pid.");
                }

                $sql = sprintf("INSERT INTO spoiled (pid, uid) VALUE ('%s', '%s')",
                                mysql_real_escape_string($pid), mysql_real_escape_string($user));
                query_db($sql);

                // Add to comment
                if ($comment != 'Added ')
                        $comment .= ', ';
                $comment .= getUserName($user);

                // Email new author
                $title = getTitle($pid);
                $subject = "Spoiled on $title (puzzle $pid)";
                $message = "$name added you as spoiled on $title (puzzle $pid).";
                $link = URL;
                sendEmail($user, $subject, $message, $link);
        }

        $comment .= ' as spoiled';

        addComment($uid, $pid, $comment, TRUE);
}

function addSpoiledUserQuietly($uid, $pid)
{
        if (!isSpoiledOnPuzzle($uid, $pid)) {
                $sql = sprintf("INSERT INTO spoiled (pid, uid) VALUE ('%s', '%s')",
                                mysql_real_escape_string($pid), mysql_real_escape_string($uid));
                query_db($sql);
        }
}

function isRoundCaptainAvailable($uid, $pid)
{
        return (isRoundCaptain($uid) && !isRoundCaptainOnPuzzle($uid, $pid));
}

function isEditorAvailable($uid, $pid)
{
        return (isEditor($uid) &&
                        !isAuthorOnPuzzle($uid, $pid) &&
                        !isEditorOnPuzzle($uid, $pid) &&
                        !isTesterOnPuzzle($uid, $pid));
}


// Add and remove puzzle authors
function changeAuthors($uid, $pid, $add, $remove)
{
        mysql_query('START TRANSACTION');
        addAuthors($uid, $pid, $add);
        removeAuthors($uid, $pid, $remove);
        mysql_query('COMMIT');
}

// Add and remove round captains
function changeRoundCaptains($uid, $pid, $add, $remove)
{
        mysql_query('START TRANSACTION');
        addRoundCaptains($uid, $pid, $add);
        removeRoundCaptains($uid, $pid, $remove);
        mysql_query('COMMIT');
}

// Add and remove puzzle editors
function changeEditors($uid, $pid, $add, $remove)
{
        mysql_query('START TRANSACTION');
        addEditors($uid, $pid, $add);
        removeEditors($uid, $pid, $remove);
        mysql_query('COMMIT');
}

function changeFactcheckers($uid, $pid, $add, $remove)
{
        mysql_query('START TRANSACTION');
        addFactcheckers($uid, $pid, $add);
        removeFactcheckers($uid, $pid, $remove);
        mysql_query('COMMIT');
}

function addFactcheckers($uid, $pid, $add)
{
        if ($add == NULL)
                return;

        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify this puzzle.");

        $name = getUserName($uid);

        $comment = 'Added ';
        foreach ($add as $fc) {
                // Check that this factchecker is available for this puzzle
                if (!isFactcheckerAvailable($fc, $pid)) {
                        utilsError(getUserName($fc) . ' is not available.');
                }

                // Add factchecker to puzzle
                $sql = sprintf("INSERT INTO factcheck_queue (pid, uid) VALUE ('%s', '%s')",
                                mysql_real_escape_string($pid), mysql_real_escape_string($fc));
                query_db($sql);

                // Add to comment
                if ($comment != 'Added ')
                        $comment .= ', ';
                $comment .= getUserName($fc);

                // Email new factchecker
                $title = getTitle($pid);
                $subject = "Factchecker on $title (puzzle $pid)";
                $message = "$name added you as a factchecker on $title (puzzle $pid).";
                $link = URL . "/puzzle?pid=$pid";
                sendEmail($fc, $subject, $message, $link);

                // Subscribe factcheckers to comments on their puzzles
                // subscribe($fc, $pid);
        }

        $comment .= ' as factchecker';
        if (count($add) > 1)
                $comment .= "s";

        addComment($uid, $pid, $comment, TRUE);
}

function removeFactcheckers($uid, $pid, $remove)
{
        if ($remove == NULL)
                return;

        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify puzzle $pid.");

        $name = getUserName($uid);

        $comment = 'Removed ';
        foreach ($remove as $fc) {
                // Check that this factchecker is assigned to this puzzle
                if (!isFactcheckerOnPuzzle($fc, $pid))
                        utilsError(getUserName($fc) . " is not a factchecker on to puzzle $pid");

                // Remove factchecker from puzzle
                $sql = sprintf("DELETE FROM factcheck_queue WHERE uid='%s' AND pid='%s'",
                                mysql_real_escape_string($fc), mysql_real_escape_string($pid));
                query_db($sql);

                // Add to comment
                if ($comment != 'Removed ')
                        $comment .= ', ';
                $comment .= getUserName($fc);

                // Email old factchecker
                $title = getTitle($pid);
                $subject = "Factchecker on $title (puzzle $pid)";
                $message = "$name removed you as a factchecker on $title (puzzle $pid).";
                $link = URL . "/factcheck";
                sendEmail($fc, $subject, $message, $link);
        }

        $comment .= ' as factchecker';
        if (count($remove) > 1)
                $comment .= "s";

        addComment($uid, $pid, $comment, TRUE);
}

function addAuthors($uid, $pid, $add)
{
        if ($add == NULL)
                return;

        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify this puzzle.");

        $name = getUserName($uid);

        $comment = 'Added ';
        foreach ($add as $auth) {
                // Check that this author is available for this puzzle
                if (!isAuthorAvailable($auth, $pid)) {
                        utilsError(getUserName($auth) . ' is not available.');
                }

                // Add answer to puzzle
                $sql = sprintf("INSERT INTO authors (pid, uid) VALUE ('%s', '%s')",
                                mysql_real_escape_string($pid), mysql_real_escape_string($auth));
                query_db($sql);

                // Add to comment
                if ($comment != 'Added ')
                        $comment .= ', ';
                $comment .= getUserName($auth);

                // Email new author
                $title = getTitle($pid);
                $subject = "Author on $title (puzzle $pid)";
                $message = "$name added you as an author on $title (puzzle $pid).";
                $link = URL . "/puzzle.php?pid=$pid";
                sendEmail($auth, $subject, $message, $link);

                // Subscribe authors to comments on their puzzles
                subscribe($auth, $pid);
        }

        $comment .= ' as author';
        if (count($add) > 1)
                $comment .= "s";

        addComment($uid, $pid, $comment, TRUE);
}

function removeAuthors($uid, $pid, $remove)
{
        if ($remove == NULL)
                return;

        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify puzzle $pid.");

        $name = getUserName($uid);

        $comment = 'Removed ';
        foreach ($remove as $auth) {
                // Check that this author is assigned to this puzzle
                if (!isAuthorOnPuzzle($auth, $pid))
                        utilsError(getUserName($auth) . " is not an author on to puzzle $pid");

                // Remove author from puzzle
                $sql = sprintf("DELETE FROM authors WHERE uid='%s' AND pid='%s'",
                                mysql_real_escape_string($auth), mysql_real_escape_string($pid));
                query_db($sql);

                // Add to comment
                if ($comment != 'Removed ')
                        $comment .= ', ';
                $comment .= getUserName($auth);

                // Email old author
                $title = getTitle($pid);
                $subject = "Author on $title (puzzle $pid)";
                $message = "$name removed you as an author on $title (puzzle $pid).";
                $link = URL . "/author.php";
                sendEmail($auth, $subject, $message, $link);
        }

        $comment .= ' as author';
        if (count($remove) > 1)
                $comment .= "s";

        addComment($uid, $pid, $comment, TRUE);
}

function addRoundCaptains($uid, $pid, $add)
{
        if ($add == NULL)
                return;

        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify puzzle $pid.");

        $name = getUserName($uid);

        $comment = 'Added ';
        foreach ($add as $roundcaptain) {
                // Check that this round captain is available for this puzzle
                if (!isRoundCaptainAvailable($roundcaptain, $pid)) {
                        utilsError(getUserName($roundcaptain) . ' is not available.');
                }

                // Add round captain to puzzle
                $sql = sprintf("INSERT INTO round_captain_queue (uid, pid) VALUES ('%s', '%s')",
                                mysql_real_escape_string($roundcaptain), mysql_real_escape_string($pid));
                query_db($sql);

                // Add to comment
                if ($comment != 'Added ')
                        $comment .= ', ';
                $comment .= getUserName($roundcaptain);

                // Email new round captain
                $title = getTitle($pid);
                $subject = "Round Captain on $title (puzzle $pid)";
                $message = "$name added you as a round captain to $title (puzzle $pid).";
                $link = URL . "/puzzle?pid=$pid";
                sendEmail($roundcaptain, $subject, $message, $link);

                // Subscribe round captains to comments on their puzzles
                // subscribe($roundcaptain, $pid);
        }

        $comment .= ' as round captain';
        if (count($add) > 1)
                $comment .= "s";

        addComment($uid, $pid, $comment, TRUE);
}

function removeRoundCaptains($uid, $pid, $remove)
{
        if ($remove == NULL)
                return;

        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify puzzle $pid.");

        $name = getUserName($uid);

        $comment = 'Removed ';
        foreach ($remove as $roundcaptain) {
                // Check that this round captain is assigned to this puzzle
                if (!isRoundCaptainOnPuzzle($roundcaptain, $pid))
                        utilsError(getUserName($roundcaptain) . " is not a round captain on puzzle $pid");

                // Remove round captain from puzzle
                $sql = sprintf("DELETE FROM round_captain_queue WHERE uid='%s' AND pid='%s'",
                                mysql_real_escape_string($roundcaptain), mysql_real_escape_string($pid));
                query_db($sql);

                // Add to comment
                if ($comment != 'Removed ')
                        $comment .= ', ';
                $comment .= getUserName($roundcaptain);

                // Email old round captain
                $title = getTitle($pid);
                $subject = "Round Captain on $title (puzzle $pid)";
                $message = "$name removed you as a round captain on $title (puzzle $pid).";
                $link = URL . "/roundcaptain";
                sendEmail($roundcaptain, $subject, $message, $link);
        }

        $comment .= ' as round captain';
        if (count($remove) > 1)
                $comment .= "s";

        addComment($uid, $pid, $comment, TRUE);
}

function addEditors($uid, $pid, $add)
{
        if ($add == NULL)
                return;

        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify puzzle $pid.");

        $name = getUserName($uid);

        $comment = 'Added ';
        foreach ($add as $editor) {
                // Check that this editor is available for this puzzle
                if (!isEditorAvailable($editor, $pid)) {
                        utilsError(getUserName($editor) . ' is not available.');
                }

                // Add editor to puzzle
                $sql = sprintf("INSERT INTO editor_queue (uid, pid) VALUES ('%s', '%s')",
                                mysql_real_escape_string($editor), mysql_real_escape_string($pid));
                query_db($sql);

                // Add to comment
                if ($comment != 'Added ')
                        $comment .= ', ';
                $comment .= getUserName($editor);

                // Email new editor
                $title = getTitle($pid);
                $subject = "Editor on $title (puzzle $pid)";
                $message = "$name added you as an editor to $title (puzzle $pid).";
                $link = URL . "/puzzle.php?pid=$pid";
                sendEmail($editor, $subject, $message, $link);

                // Subscribe editors to comments on their puzzles
		if (isAutoSubEditor($editor)){
                   subscribe($editor, $pid);
		}
        }

        $comment .= ' as editor';
        if (count($add) > 1)
                $comment .= "s";

        addComment($uid, $pid, $comment, TRUE);
}

function removeEditorKill($uid, $pid, $editor)
{
        //echo "called removeEditorKill with editorid = $editor<br>";

        if (!isEditorOnPuzzle($editor, $pid))
                utilsError(getUserName($editor) . " is not an editor on puzzle $pid");

        $name = getUserName($uid);


        $comment = 'Removed ';
        // Remove editor from puzzle
        $sql = sprintf("DELETE FROM editor_queue WHERE uid='%s' AND pid='%s'",
                        mysql_real_escape_string($editor), mysql_real_escape_string($pid));
        query_db($sql);

        // Add to comment
        if ($comment != 'Removed ')
                $comment .= ', ';
        $comment .= getUserName($editor);

        // Email old editor
        $title = getTitle($pid);
        $subject = "Editor on $title (puzzle $pid)";
        $message = "$name removed you as an editor on $title (puzzle $pid) by killing the puzzle.";
        $link = URL . "/editor.php";
        sendEmail($editor, $subject, $message, $link);

        $comment .= ' as editor';
        if (count($remove) > 1)
                $comment .= "s";

        addComment($uid, $pid, $comment, TRUE);
        
}
        

function removeEditors($uid, $pid, $remove)
{
        if ($remove == NULL)
                return;

        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify puzzle $pid.");

        $name = getUserName($uid);


        $comment = 'Removed ';
        foreach ($remove as $editor) {
                // Check that this editor is assigned to this puzzle
                if (!isEditorOnPuzzle($editor, $pid))
                        utilsError(getUserName($editor) . " is not an editor on puzzle $pid");

                // Remove editor from puzzle
                $sql = sprintf("DELETE FROM editor_queue WHERE uid='%s' AND pid='%s'",
                                mysql_real_escape_string($editor), mysql_real_escape_string($pid));
                query_db($sql);

                // Add to comment
                if ($comment != 'Removed ')
                        $comment .= ', ';
                $comment .= getUserName($editor);

                // Email old editor
                $title = getTitle($pid);
                $subject = "Editor on $title (puzzle $pid)";
                $message = "$name removed you as an editor on $title (puzzle $pid).";
                $link = URL . "/editor.php";
                sendEmail($editor, $subject, $message, $link);
        }

        $comment .= ' as editor';
        if (count($remove) > 1)
                $comment .= "s";

        addComment($uid, $pid, $comment, TRUE);
}

function canFactCheckPuzzle($uid, $pid)
{
  return isPuzzleInFactChecking($pid) && isFactChecker($uid);
}

function canViewPuzzle($uid, $pid)
{
        return isLurker($uid) || isPuzzleInFinalFactChecking($pid) ||
          isAuthorOnPuzzle($uid, $pid) || isEditorOnPuzzle($uid, $pid) ||
          isTestingAdminOnPuzzle($uid, $pid) || canFactCheckPuzzle($uid, $pid) ||
          isSpoiledOnPuzzle($uid, $pid) || isPuzzleInPostprod($pid);
}

function canChangeAnswers($uid)
{
        return hasPriv($uid, 'canEditAll');
}

function canSeeAllPuzzles($uid)
{
        return hasPriv($uid, 'seeAllPuzzles');
}

function canSeeTesters($uid, $pid)
{
        return !ANON_TESTERS || isTestingAdminOnPuzzle($uid, $pid) || !isAuthorOnPuzzle($uid, $pid);
}

function canTestPuzzle($uid, $pid, $display = FALSE)
{
        if (isAuthorOnPuzzle($uid, $pid)) {
                if ($display)
                        $_SESSION['testError'] = "You are an author on puzzle $pid. Could not add to test queue.";
                return FALSE;
        }

        if (isEditorOnPuzzle($uid, $pid)) {
                if ($display)
                        $_SESSION['testError'] = "You are an editor on puzzle $pid. Could not add to test queue.";
                return FALSE;
        }

        if (isSpoiledOnPuzzle($uid, $pid)) {
                if ($display)
                        $_SESSION['testError'] = "You are spoiled on puzzle $pid. Could not add to test queue.";
                return FALSE;
        }

        if (isTestingAdminOnPuzzle($uid, $pid)) {
                if ($display)
                        $_SESSION['testError'] = "You are a testing admin on puzzle $pid. Could not add to test queue.";
                return FALSE;
        }

        if (!isPuzzleInTesting($pid)) {
                if ($display)
                        $_SESSION['testError'] = "Puzzle $pid is not currently in testing. Could not add to test queue";
                return FALSE;
        }

        return TRUE;
}

function getStatusNameForPuzzle($pid)
{
        $sql = sprintf("SELECT pstatus.name FROM pstatus, puzzle_idea
                                        WHERE puzzle_idea.id='%s' AND puzzle_idea.pstatus=pstatus.id",
                        mysql_real_escape_string($pid));
        return get_element($sql);
}

function getTestsolveRequestsForPuzzle($pid)
{
        $sql = sprintf("SELECT count(*) FROM testsolve_requests where pid='%s' AND done=0",
                       mysql_real_escape_string($pid));
        return get_element($sql);
}

function getPuzzApprovals($pid)
{
        $sql = sprintf("SELECT fullname, approve from user_info, puzzle_approve where pid='%s' AND puzzle_approve.uid = user_info.uid", mysql_real_escape_string($pid));
        return get_assoc_array($sql, "fullname", "approve");
}

function countPuzzApprovals($pid)
{
        $sql = sprintf("SELECT count(*) from puzzle_approve where pid='%s' AND approve='1'", 
                mysql_real_escape_string($pid));
        return get_element($sql);
}

function flushPuzzApprovals($pid)
{
        //this function should get called after any puzzle state-change
        $sql = sprintf("DELETE from puzzle_approve WHERE pid = %s", mysql_real_escape_string($pid));
        $result = query_db($sql);
        return $result;
}

function setPuzzApprove($uid, $pid, $approve)
{
        $sql = sprintf("INSERT INTO puzzle_approve (uid, pid, approve) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE approve = %s",
                mysql_real_escape_string($uid), mysql_real_escape_string($pid),
                mysql_real_escape_string($approve), mysql_real_escape_string($approve));;

        $result = query_db($sql);
        $name = getUserName($uid);
        $title = getTitle($pid);
        
        //add comment noting this approval or disapproval

        if ($approve == 1){
                $comment = sprintf("%s approves puzzle %s to advance to next puzzle status", $name, $title);
        } else {
                $comment = sprintf("%s does not approve puzzle %s to advance to next puzzle status", $name, $title);
        }
        addComment ($uid, $pid, $comment, TRUE);
        
}

function getAllEditors()
{
        return get_assoc_array("select user_info.uid, fullname from user_info, jobs, priv where user_info.uid = jobs.uid and jobs.jid = priv.jid and priv.addToEditingQueue = 1 group by uid", "uid", "fullname");
}

function getAllAuthors()
{
        return get_assoc_array("select user_info.uid, fullname from user_info, authors where user_info.uid = authors.uid group by uid", "uid", "fullname");
}

function getEditorStats()
{
        // This one is a bit messy.
        return get_assoc_array("select fullname, count(pid) as pcount from
                                        (select user_info.uid, fullname from user_info, jobs, priv
                                        where user_info.uid = jobs.uid and jobs.jid = priv.jid and priv.addToEditingQueue = 1 group by uid)
                                as editors left join editor_queue on editors.uid = editor_queue.uid group by editors.uid", "fullname", "pcount");
}

function getPuzzleStatuses()
{
        return get_assoc_array("SELECT id, name FROM pstatus ORDER BY ord ASC", "id", "name");
}

function getPuzzleStatusCounts()
{
        return get_assoc_array("SELECT pstatus, COUNT(*) AS pcount FROM puzzle_idea GROUP BY pstatus", "pstatus", "pcount");
}

function getStatusForPuzzle($pid)
{
        $sql = sprintf("SELECT pstatus FROM puzzle_idea WHERE id='%s'", mysql_real_escape_string($pid));
        return get_element($sql);
}

function changeStatus($uid, $pid, $status)
{
        //make permission exception if we're killing -- for kill puzzle button
        if (!(canViewPuzzle($uid, $pid) && ((canChangeStatus($uid))||($status == getDeadStatusId()))))
                utilsError("You do not have permission to modify the status of this puzzle.");

        $sql = sprintf("SELECT pstatus.inTesting FROM puzzle_idea LEFT JOIN pstatus ON puzzle_idea.pstatus =
                       pstatus.id WHERE puzzle_idea.id='%s'", mysql_real_escape_string($pid));
        query_db($sql);

        $inTesting_before = get_element($sql);

        echo "<br>inTesting_before is $inTesting_before<br>";

        mysql_query('START TRANSACTION');

        $old = getStatusForPuzzle($pid);
        if ($old == $status) {
                mysql_query('COMMIT');
                return;
        }


        $sql = sprintf("UPDATE puzzle_idea SET pstatus='%s' WHERE id='%s'",
                        mysql_real_escape_string($status), mysql_real_escape_string($pid));
        query_db($sql);

        $oldName = getPuzzleStatusName($old);
        $newName = getPuzzleStatusName($status);
        $comment = "Puzzle status changed from $oldName to $newName. <br />";
        addComment($uid, $pid, $comment, TRUE);

        if (isStatusInTesting($old))
                emailTesters($pid, $status);

        mysql_query('COMMIT');

        $sql = sprintf("SELECT pstatus.inTesting FROM puzzle_idea LEFT JOIN pstatus ON puzzle_idea.pstatus =
                       pstatus.id WHERE puzzle_idea.id='%s'", mysql_real_escape_string($pid));
        query_db($sql);
        $inTesting_after = get_element($sql);

        echo "<br>inTesting_after is $inTesting_after<br>";

        if ($inTesting_before == "1" && $inTesting_after == "0") {
                echo "<br>inTesting changed from yes to no<br>";
                // For every user that was testing this puzzle, mark the puzzle as doneTesting
                $sql = sprintf("SELECT uid FROM test_queue WHERE pid = '%s'", mysql_real_escape_string($pid));
                query_db($sql);
                $users = get_elements_null($sql);
                if ($users) {
                           foreach ($users as $user) {
                                echo "<br>Setting puzzle $pid done for user $user<br>";
                                doneTestingPuzzle($user, $pid);
                        }
                }
                // Now, reset the number-of-testers count for the puzzle.
                resetPuzzleTesterCount($pid);
        }

        if ($inTesting_before == "0" && $inTesting_after == "1") {
                // Status changed into testing; file an automatic testsolve
                // request.
                if (getTestsolveRequestsForPuzzle($pid) == 0) {
                        requestTestsolve($uid, $pid, "Automatic testsolve request.");
                }
        }

        // Self-service testsolving is 25.
        if ($status == 25 && (getWikiPage($pid) == "" || getWikiPage($pid) == NULL)) {
                $newpage = defaultWikiPageForPuzzle($pid);
                updateWikiPage($uid, $pid, "", $newpage);
        }

        $fcs = getFactcheckersForPuzzle($pid);
        if (isStatusInFactchecking($status) && !empty($fcs)) {
                // Let existing factcheckers know that we've gone back into
                // factchecking.
                emailFactcheckers($pid);
        }
        
        // reset approval status on puzz status change
        flushPuzzApprovals($pid);

        if ($status == getDeadStatusId()) {
                //return answer words to pool if we're killing a puzzle
                foreach (getAnswersForPuzzle($pid) as $akey => $answer){
                  //echo "removing answer id $akey<br>";
                  removeAnswerKill($uid, $pid, $akey);
                }
                
                //remove editors from puzzle if we're killing it
                foreach (getEditorsForPuzzle($pid) as $ekey => $editor){
                  //echo "removing editor id $ekey<br>";
                  removeEditorKill($uid, $pid, $ekey);
                }

        //utilsError("Debug Breakpoint");
        }
               

}

function isStatusInTesting($sid)
{
        $sql = sprintf("SELECT inTesting FROM pstatus WHERE id='%s'", mysql_real_escape_string($sid));
        return get_element($sql);
}

function isStatusInFactchecking($sid)
{
        $sql = sprintf("SELECT needsFactcheck FROM pstatus WHERE id='%s'", mysql_real_escape_string($sid));
        return get_element($sql);
}

function changeCredits($uid, $pid, $credits)
{
        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify this puzzle.");

        $purifier = new HTMLPurifier();
        mysql_query('START TRANSACTION');

        $oldCredits = getCredits($pid);
        $cleanCredits = $purifier->purify($credits);
        $cleanCredits = htmlspecialchars($cleanCredits);
        updateCredits($uid, $pid, $oldCredits, $cleanCredits);

        mysql_query('COMMIT');
}

function changeNotes($uid, $pid, $notes)
{
        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify this puzzle.");

        $purifier = new HTMLPurifier();
        mysql_query('START TRANSACTION');

        $oldNotes = getNotes($pid);
        $cleanNotes = $purifier->purify($notes);
        $cleanNotes = htmlspecialchars($cleanNotes);
        updateNotes($uid, $pid, $oldNotes, $cleanNotes);

        mysql_query('COMMIT');
}

function changeWikiPage($uid, $pid, $wikiPage)
{
        if (!canViewPuzzle($uid, $pid))
                utilsError("You do not have permission to modify this puzzle.");

        $purifier = new HTMLPurifier();
        mysql_query('START TRANSACTION');

        $oldWikiPage = getWikiPage($pid);
        $cleanWikiPage = $purifier->purify($wikiPage);
        $cleanWikiPage = htmlspecialchars($cleanWikiPage);
        updateWikiPage($uid, $pid, $oldWikiPage, $cleanWikiPage);

        mysql_query('COMMIT');
}

function emailTesters($pid, $status)
{
        $title = getTitle($pid);
        $subject = "Puzzle $pid Status Change";

        if (!isStatusInTesting($status)) {
                $message = "$title (puzzle $pid) was removed from testing";
        } else {
                $statusName = getPuzzleStatusName($status);
                $message = "$title (puzzle $pid)'s status was changed to $statusName.";
        }

        $link = URL . "/testsolving.php";

        $testers = getCurrentTestersForPuzzle($pid);
        foreach ($testers as $uid => $name) {
                sendEmail($uid, $subject, $message, $link);
        }
}

function emailFactcheckers($pid)
{
        $title = getTitle($pid);
        $subject = "Puzzle $pid needs factchecking attention";

        $message = "$title (puzzle $pid), on which you are a factchecker, was put back into factchecking. Please comment on it letting us know whether, and when, you can take another look at it. Thanks!";

        $link = URL . "/puzzle.php?pid=$pid";

        $fcs = getFactcheckersForPuzzle($pid);
        foreach ($fcs as $uid => $name) {
                sendEmail($uid, $subject, $message, $link);
        }
}

function validPuzzleStatus($id)
{
        $sql = sprintf("SELECT * FROM pstatus WHERE id='%s'", mysql_real_escape_string($id));
        return has_result($sql);
}

function getPuzzleStatusName($id)
{
        $sql = sprintf("SELECT name FROM pstatus WHERE id='%s'", mysql_real_escape_string($id));
        return get_element($sql);
}

function getFileListForPuzzle($pid, $type)
{
        $sql = sprintf("SELECT * FROM uploaded_files WHERE pid='%s' AND type='%s' ORDER BY date DESC, filename DESC",
                        mysql_real_escape_string($pid), mysql_real_escape_string($type));
        return get_rows_null($sql);
}


function uploadFiles($uid, $pid, $type, $file) {
        if (!canViewPuzzle($uid, $pid)) {
                utilsError("You do not have permission to modify this puzzle.");
        }

        if ($type == 'draft' && !canAcceptDrafts($pid)) {
                utilsError("This puzzle has been finalized. No new drafts can be uploaded.");
        }

        $extension = "";

        $target_path = "uploads/puzzle_files/" . uniqid();
        $filename_parts = explode(".", $file['name']);
        if (count($filename_parts) > 1) {
                $target_path = $target_path . "." . end($filename_parts);
                $extension = end($filename_parts);
        }

        if ($extension == "zip") {
                $filetype = "dir";
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                        $new_path = $target_path . "_" . $filetype;
                        echo "target_path is $target_path<br>";
                        echo "new_path is $new_path<br>";
                        $res = exec("/usr/bin/unzip $target_path -d $new_path");

                        $sql = sprintf("INSERT INTO uploaded_files (filename, pid, uid, cid, type) VALUES ('%s', '%s', '%s', '%s', '%s')",
                                mysql_real_escape_string($new_path), mysql_real_escape_string($pid),
                                mysql_real_escape_string($uid), mysql_real_escape_string(-1), mysql_real_escape_string($type));
                        query_db($sql);
                        $sql = sprintf("INSERT INTO uploaded_files (filename, pid, uid, cid, type) VALUES ('%s', '%s', '%s', '%s', '%s')",
                                mysql_real_escape_string($target_path), mysql_real_escape_string($pid),
                                mysql_real_escape_string($uid), mysql_real_escape_string(-1), mysql_real_escape_string($type));
                        query_db($sql);

                        addComment($uid, $pid, "A new <a href=\"$new_path\">$type</a> has been uploaded.",TRUE);
                } else {
                        $_SESSION['upload_error'] = "There was an error uploading the file, please try again. (Note: file max size may be limited)";
                }
        }

        else {
                $upload_error = "";
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                        $sql = sprintf("INSERT INTO uploaded_files (filename, pid, uid, cid, type) VALUES ('%s', '%s', '%s', '%s', '%s')",
                                mysql_real_escape_string($target_path), mysql_real_escape_string($pid),
                                mysql_real_escape_string($uid), mysql_real_escape_string(-1), mysql_real_escape_string($type));
                        query_db($sql);

                        addComment($uid, $pid, "A new <a href=\"$target_path\">$type</a> has been uploaded.",TRUE);
                } else {
                        $_SESSION['upload_error'] = "There was an error uploading the file, please try again. (Note: file max size may be limited) " . serialize($file);
                }
        }

        if ($type == "postprod") {
                pushToPostProd($uid, $pid);      
        }
}

function getComments($pid)
{
        $sql = sprintf("SELECT comments.id, comments.uid, comments.comment, comments.type,
                                        comments.timestamp, comments.pid, comment_type.name FROM
                                        comments LEFT JOIN comment_type ON comments.type=comment_type.id
                                        WHERE comments.pid='%s' ORDER BY comments.id ASC",
                                        mysql_real_escape_string($pid));
        return get_rows_null($sql);
}

function getTestFeedComments()
{
        $sql = "SELECT comments.id, comments.uid, comments.comment, comments.type,
                                        comments.timestamp, comments.pid, comment_type.name FROM
                                        comments LEFT JOIN comment_type ON comments.type=comment_type.id
                                        WHERE (comments.comment LIKE '%In Testing%' OR comments.comment LIKE '%answer attempt%') AND (comment_type.name = 'Testsolver' OR comment_type.name = 'Server') ORDER BY comments.timestamp DESC LIMIT 50";
        return get_rows_null($sql);
}

function isSubbedOnPuzzle($uid, $pid)
{
        $sql = sprintf("SELECT * FROM email_sub WHERE uid='%s' AND pid='%s'",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        return has_result($sql);
}

function subscribe($uid, $pid)
{
        $sql = sprintf("REPLACE INTO email_sub (uid, pid) VALUES ('%s', '%s')",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        query_db($sql);
}

function unsubscribe($uid, $pid)
{
        $sql = sprintf("DELETE FROM email_sub WHERE uid='%s' AND pid='%s'",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        query_db($sql);
}


function getPeople()
{
        $sql = 'SELECT * FROM user_info ORDER BY fullname';
        return get_rows($sql);
}


function getPerson($uid)
{
        $sql = sprintf("SELECT * FROM user_info WHERE uid='%s'", mysql_real_escape_string($uid));
        return get_row($sql);
}

function change_password($uid, $oldpass, $pass1, $pass2)
{
        $sql = sprintf("SELECT username FROM user_info WHERE uid='%s'", mysql_real_escape_string($uid));
        $username = get_element($sql);

        if ($username == NULL)
                return 'error';

        if (checkPassword($username, $oldpass) == TRUE) {
                $err = newPass($uid, $username, $pass1, $pass2);
                return $err;
        } else
                return 'wrong';
}

function checkPassword($username, $password)
{
        $sql = sprintf("SELECT uid FROM user_info WHERE
                                        username='%s' AND
                                        password=AES_ENCRYPT('%s', '%s%s')",
                                        mysql_real_escape_string($username),
                                        mysql_real_escape_string($password),
                                        mysql_real_escape_string($username),
                                        mysql_real_escape_string($password));
        return has_result($sql);
}

function newPass($uid, $username, $pass1, $pass2)
{
        if ($pass1 == "" || $pass2 == "")
                return 'invalid';
        if ($pass1 != $pass2)
                return 'invalid';
        if (strlen($pass1) < 6)
                return 'short';
        $sql = sprintf("UPDATE user_info SET password=AES_ENCRYPT('%s', '%s%s')
                                        WHERE uid='%s'",
                                        mysql_real_escape_string($pass1),
                                        mysql_real_escape_string($username),
                                        mysql_real_escape_string($pass1),
                                        mysql_real_escape_string($uid));
        mysql_query($sql);

        if (mysql_error())
                return 'error';

        return 'changed';
}

function getPuzzlesForAuthor($uid)
{
        $sql = sprintf("SELECT pid FROM authors WHERE uid='%s'", mysql_real_escape_string($uid));
        $puzzles = get_elements_null($sql);

        return sortByLastCommentDate($puzzles);
}

function getPuzzlesForFactchecker($uid)
{
        $sql = sprintf("SELECT pid FROM factcheck_queue WHERE uid='%s'", mysql_real_escape_string($uid));
        $puzzles = get_elements_null($sql);

        return sortByLastCommentDate($puzzles);
}

function getSpoiledPuzzles($uid)
{
        $sql = sprintf("SELECT pid FROM spoiled WHERE uid='%s'", mysql_real_escape_string($uid));
        $puzzles = get_elements_null($sql);

        return sortByLastCommentDate($puzzles);
}

function getLastCommenter($pid)
{
        $sql = sprintf("SELECT fullname FROM user_info, comments WHERE pid='%s' and user_info.uid=comments.uid order by timestamp desc limit 1", mysql_real_escape_string($pid));
        return get_element_null($sql);
}

function getLastCommentDate($pid)
{
        $sql = sprintf("SELECT MAX(timestamp) FROM comments WHERE pid='%s'", mysql_real_escape_string($pid));
        return get_element_null($sql);
}

function getLastTestReportDate($pid)
{
        $sql = sprintf("SELECT MAX(time) FROM testing_feedback WHERE pid='%s'", mysql_real_escape_string($pid));
        return get_element_null($sql);
}

function getNumEditors($pid)
{
        $sql = sprintf("SELECT puzzle_idea.id, COUNT(editor_queue.uid) FROM puzzle_idea
                                        LEFT JOIN editor_queue ON puzzle_idea.id=editor_queue.pid
                                        WHERE puzzle_idea.id='%s'", mysql_real_escape_string($pid));
        $result = get_row($sql);

        return $result['COUNT(editor_queue.uid)'];

}

function getNumTesters($pid)
{
        $sql = sprintf("SELECT puzzle_idea.id, COUNT(test_queue.uid) FROM puzzle_idea
                                        LEFT JOIN test_queue ON puzzle_idea.id=test_queue.pid
                                        WHERE puzzle_idea.id='%s'", mysql_real_escape_string($pid));
        $result = get_row($sql);

        return $result['COUNT(test_queue.uid)'];

}

function getPuzzlesInPostprod()
{
        $puzzles = get_elements_null("select puzzle_idea.id from puzzle_idea, pstatus where puzzle_idea.pstatus=pstatus.id and pstatus.name like '%Post-Production%'");
        return sortByLastCommentDate($puzzles);
}

function getPuzzlesInPostprodAndLater()
{
        $puzzles = get_elements_null("select puzzle_idea.id from puzzle_idea, pstatus where puzzle_idea.pstatus=pstatus.id and pstatus.postprod='1'");
        return sortByLastCommentDate($puzzles);
}

function getPuzzlesInEditorQueue($uid)
{
        $sql = sprintf("SELECT pid FROM editor_queue WHERE uid='%s'",
                        mysql_real_escape_string($uid));
        $puzzles = get_elements_null($sql);

        return sortByLastCommentDate($puzzles);
}

function getPuzzlesInRoundCaptainQueue($uid)
{
        $sql = sprintf("SELECT pid FROM round_captain_queue WHERE uid='%s'",
                        mysql_real_escape_string($uid));
        $puzzles = get_elements_null($sql);

        return sortByLastCommentDate($puzzles);
}

function getPuzzlesInTestQueue($uid)
{
        $sql = sprintf("SELECT pid FROM test_queue WHERE uid='%s'",
                        mysql_real_escape_string($uid));
        $puzzles = get_elements_null($sql);

        return $puzzles;
}

// This is not actually used currently. -- gwillen
function getPuzzlesNeedingTesters()
{
        $sql = "SELECT puzzle_idea.id FROM puzzle_idea LEFT JOIN test_queue ON test_queue.pid
                = puzzle_idea.id WHERE pstatus = 4 AND uid IS NULL";
        $puzzles = get_elements_null($sql);

        return $puzzles;
}

function getActivePuzzlesInTestQueue($uid)
{
        $puzzles = getPuzzlesInTestQueue($uid);

        if ($puzzles == NULL)
                return NULL;

        $active = NULL;
        foreach ($puzzles as $pid) {
                if (isPuzzleInTesting($pid)) {
                        $active[] = $pid;
                }
        }

        return $active;
}

function getInactiveTestPuzzlesForUser($uid)
{
        $inQueue = getPuzzlesInTestQueue($uid);
        $oldPuzzles = getDoneTestingPuzzlesForUser($uid);

        $puzzles = array_merge((array)$inQueue, (array)$oldPuzzles);

        if ($puzzles == NULL)
                return NULL;

        $inactive = NULL;
        foreach ($puzzles as $pid) {
                if (!isPuzzleInTesting($pid)) {
                        $inactive[] = $pid;
                }
        }

        if ($inactive != NULL)
                sort($inactive);

        return $inactive;
}

function getDoneTestingPuzzlesForUser($uid)
{
        $sql = sprintf("SELECT pid FROM doneTesting WHERE uid='%s'", mysql_real_escape_string($uid));
        $result = get_elements_null($sql);
    if ($result == NULL)
      return array();
    else
      return $result;
}

function getActiveDoneTestingPuzzlesForUser($uid)
{
        $puzzles = getDoneTestingPuzzlesForUser($uid);

        $active = NULL;
        foreach ($puzzles as $pid) {
                if (isPuzzleInTesting($pid)) {
                        $active[] = $pid;
                }
        }

        return $active;
}

function sortByLastCommentDate($puzzles)
{
        if ($puzzles == NULL)
                return NULL;

        $sorted = NULL;
        foreach ($puzzles as $pid) {
                $sorted[$pid] = getLastCommentDate($pid);
        }

        arsort($sorted);

        return array_keys($sorted);
}

function sortByNumEditors($puzzles)
{
        if ($puzzles == NULL)
                return NULL;

        $sorted = NULL;
        foreach ($puzzles as $pid) {
                $sorted[$pid] = getNumEditors($pid);
        }

        arsort($sorted);

        return array_keys($sorted);
}

function getNewPuzzleForEditor($uid)
{
        $sql = "SELECT puzzle_idea.id FROM puzzle_idea LEFT JOIN pstatus ON puzzle_idea.pstatus=pstatus.id WHERE pstatus.addToEditorQueue='1'";
        $puzzles = get_elements_null($sql);
        $puzzles = sortByNumEditors($puzzles);

        $foundPuzzle = FALSE;

        while (!$foundPuzzle && count($puzzles) > 0) {
                $p = array_pop($puzzles);        // Pop first puzzle from array
                if (isEditorAvailable($uid, $p)) {
                        $foundPuzzle = $p;
                }
        }

        return $foundPuzzle;
}

function addPuzzleToEditorQueue($uid, $pid)
{
        mysql_query('START TRANSACTION');
        $sql = sprintf("INSERT INTO editor_queue (uid, pid) VALUES ('%s', '%s')",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        query_db($sql);

        $comment = "Added to " . getUserName($uid) . "'s queue";
        addComment($uid, $pid,$comment,TRUE);
        mysql_query('COMMIT');

        // Subscribe editors to comments on the puzzles they edit
        // subscribe($uid, $pid);
}

function addPuzzleToTestQueue($uid, $pid)
{
        if (!canTestPuzzle($uid, $pid, TRUE)) {
                if (!isset($_SESSION['testError']))
                        $_SESSION['testError'] = "Could not add Puzzle $pid to your queue";
                return;
        }

        if (isTesterOnPuzzle($uid, $pid) || isFormerTesterOnPuzzle($uid, $pid)) {
                $_SESSION['testError'] = "Already a tester on puzzle $pid.";
                return;
        }

        mysql_query('START TRANSACTION');
        $sql = sprintf("INSERT INTO test_queue (uid, pid) VALUES ('%s', '%s')",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        query_db($sql);

        $comment = "Added testsolver";
        addComment($uid, $pid, $comment, FALSE, TRUE);
        mysql_query('COMMIT');

        // For keeping track of how many testers have this puzzle open.
        incrementPuzzleTesterCount($pid);
}

// Lower numbers are HIGHER priorities.
function getPuzzleTestPriority($pid)
{
        $status = getStatusForPuzzle($pid);
        $numTesters = getNumTesters($pid);

        // XXX: These statuses are bogus and don't exist in the new
        // world. If this function is to be used, this ought to be
        // fixed.
        /*
        if ($status == 4)
                $num = $numTesters * 2;
        else if ($status == 7)
                $num = $numTesters * 3;
        else
                $num = $numTesters;
        */
        $num = $numTesters;
        return $num;
        // Lower numbers are HIGHER priorities.
}

function getPuzzleToTest($uid)
{
        $puzzles = getAvailablePuzzlesToTestForUser($uid);
        if ($puzzles == NULL)
                return FALSE;

        $sort = NULL;
        foreach ($puzzles as $pid) {
                $num = getPuzzleTestPriority($pid);
                // Lower numbers are HIGHER priorities
                $sort[$pid] = $num;
        }

        asort($sort);
        return key($sort);
}

function canUseMoreTesters($pid)
{
        $testers_limit = 99;

        $sql = sprintf("SELECT tester_count FROM puzzle_tester_count WHERE pid='%s'", mysql_real_escape_string($pid));
        $tester_count = get_elements_null($sql);

        if (!$tester_count) {
                // No entry in the DB means 0 testers.
                return 1;
        }

        if ((int)$tester_count[0] >= $testers_limit) {
                // We already have enough testers on this puzzle.
                return NULL;
        }
        else {
                // We can use more testers.
                return 1;
        }
}

function getCurrentPuzzleTesterCount($pid)
{
        $sql = sprintf("SELECT tester_count FROM puzzle_tester_count WHERE pid='%s'", mysql_real_escape_string($pid));
        $tester_count = get_element_null($sql);
        if (!$tester_count) {
                return 0;
        }
        else {
                return $tester_count;
        }
}

function resetPuzzleTesterCount($pid)
{
        $sql = sprintf("UPDATE puzzle_tester_count SET tester_count = 0 WHERE pid='%s'", mysql_real_escape_string($pid));
        query_db($sql);
}

function incrementPuzzleTesterCount($pid)
{
        $sql = sprintf("INSERT INTO puzzle_tester_count VALUES ('%s', 1)
                   ON DUPLICATE KEY UPDATE tester_count = tester_count + 1",
                   mysql_real_escape_string($pid));

        query_db($sql);
}

function getAvailablePuzzlesToTestForUser($uid)
{
        $puzzles = getPuzzlesInTesting();
        if ($puzzles == NULL)
                return NULL;

        $available = NULL;
        echo "\n<br>\n";
        foreach ($puzzles as $pid) {
                if (canTestPuzzle($uid, $pid) &&
                    !isInTargetedTestsolving($pid) &&
                    !isTesterOnPuzzle($uid, $pid) &&
                    !isFormerTesterOnPuzzle($uid, $pid) &&
                    canUseMoreTesters($pid)) {
                        $available[] = $pid;
                }
        }

        return $available;
}

function isInTargetedTestsolving($pid)
{
        $sql = sprintf("SELECT pstatus FROM puzzle_idea WHERE id='%s'", mysql_real_escape_string($pid));
        $status = get_element($sql);

        return ($status == 4 || $status == 12 || $status == 18);
}

function isPuzzleInAddToTestAdminQueue($pid)
{
        $sql = sprintf("SELECT * FROM puzzle_idea LEFT JOIN pstatus ON puzzle_idea.pstatus = pstatus.id
                        WHERE puzzle_idea.id='%s' AND pstatus.addToTestAdminQueue='1'", mysql_real_escape_string($pid));
        return has_result($sql);
}

function isPuzzleInTesting($pid)
{
        $sql = sprintf("SELECT * FROM puzzle_idea LEFT JOIN pstatus ON puzzle_idea.pstatus = pstatus.id
                        WHERE puzzle_idea.id='%s' AND pstatus.inTesting='1'", mysql_real_escape_string($pid));
        return has_result($sql);
}

function getPuzzlesInTesting()
{
        $sql = "SELECT puzzle_idea.id FROM puzzle_idea LEFT JOIN pstatus ON puzzle_idea.pstatus = pstatus.id
                        WHERE pstatus.inTesting = '1'";
        return get_elements_null($sql);
}

function getWikiPage($pid) {
        $sql = sprintf("SELECT wikipage FROM puzzle_idea WHERE id='%s'",
                       mysql_real_escape_string($pid));
        return get_element_null($sql);
}

function getMostRecentDraftForPuzzle($pid) {
        $sql = sprintf("SELECT filename, date FROM uploaded_files WHERE pid='%s' AND TYPE='draft'
                        ORDER BY date DESC, filename DESC LIMIT 0, 1", mysql_real_escape_string($pid));
        $result = mysql_query($sql);

        if (mysql_num_rows($result) == 0)
                return FALSE;
        return mysql_fetch_assoc($result);
}

function getMostRecentDraftNameForPuzzle($pid) {
        $file = getMostRecentDraftForPuzzle($pid);

        if ($file == FALSE)
                return '';

        else
                return $file['filename'];
}

function getAllPuzzles() { 
        $sql = "SELECT id FROM puzzle_idea";
        $puzzles = get_elements_null($sql);
        return sortByLastCommentDate($puzzles);
}
function getAllLivePuzzles() { 
        $deadpuzzleid = getDeadStatusId(); 
        $sql = sprintf("SELECT id FROM puzzle_idea where pstatus != %d", $deadpuzzleid);
        $puzzles = get_elements_null($sql);
        return sortByLastCommentDate($puzzles);
}

function isPuzzleInFactChecking($pid)
{
        $sql = sprintf("SELECT * FROM puzzle_idea LEFT JOIN pstatus ON puzzle_idea.pstatus = pstatus.id
                        WHERE puzzle_idea.id='%s' AND pstatus.needsFactcheck='1'", mysql_real_escape_string($pid));
        return has_result($sql);
}

function isPuzzleInFinalFactChecking($pid)
{
        $sql = sprintf("SELECT * FROM puzzle_idea LEFT JOIN pstatus ON puzzle_idea.pstatus = pstatus.id
                        WHERE puzzle_idea.id='%s' AND pstatus.finalFactcheck='1'", mysql_real_escape_string($pid));
        return has_result($sql);
}

function isPuzzleInPostprod($pid)
{
        $sql = sprintf("SELECT * FROM puzzle_idea LEFT JOIN pstatus ON puzzle_idea.pstatus = pstatus.id
                        WHERE puzzle_idea.id='%s' AND pstatus.postprod='1'", mysql_real_escape_string($pid));
        return has_result($sql);
}

function getUnclaimedPuzzlesInFactChecking() {
        $sql = "SELECT puzzle_idea.id FROM pstatus, puzzle_idea LEFT JOIN factcheck_queue ON puzzle_idea.id=factcheck_queue.pid WHERE puzzle_idea.pstatus=pstatus.id AND pstatus.needsFactcheck='1' AND factcheck_queue.uid IS NULL";
        $puzzles = get_elements_null($sql);

        return sortByLastCommentDate($puzzles);
}

function getClaimedPuzzlesInFactChecking() {
        $sql = "SELECT puzzle_idea.id FROM puzzle_idea, pstatus, factcheck_queue WHERE puzzle_idea.pstatus=pstatus.id AND pstatus.needsFactcheck='1' AND factcheck_queue.pid=puzzle_idea.id";
        $puzzles = get_elements_null($sql);

        return sortByLastCommentDate($puzzles);
}

function getPuzzlesInFinalFactChecking() {
        $sql = "SELECT puzzle_idea.id FROM puzzle_idea INNER JOIN pstatus ON puzzle_idea.pstatus=pstatus.id WHERE pstatus.finalFactcheck='1'";
        $puzzles = get_elements_null($sql);

        return sortByLastCommentDate($puzzles);
}

function getAnswerAttempts($uid, $pid)
{
        $sql = sprintf("SELECT answer FROM answer_attempts WHERE pid='%s' AND uid='%s'",
                        mysql_real_escape_string($pid), mysql_real_escape_string($uid));
        return get_elements_null($sql);
}

function getCorrectSolves($uid, $pid)
{
        $attempts = getAnswerAttempts($uid, $pid);
        if ($attempts == NULL)
                return NULL;

        $correct = NULL;
        foreach ($attempts as $attempt) {
                if (checkAnswer($pid, $attempt)) {
                        $correct[] = $_SESSION['answer'];
                        unset($_SESSION['answer']);
                }
        }

        if ($correct == NULL)
                return NULL;

        $correct = array_unique($correct);
        return implode(', ', $correct);
}

function getPreviousFeedback($uid, $pid)
{
        $sql = sprintf("SELECT * FROM testing_feedback WHERE pid='%s' AND uid='%s'",
                        mysql_real_escape_string($pid), mysql_real_escape_string($uid));
        return get_rows_null($sql);
}

function hasAnswer($pid)
{
        $sql = sprintf("SELECT answer FROM answers WHERE pid='%s'",
                        mysql_real_escape_string($pid));
        return has_result($sql);
}

function makeAnswerAttempt($uid, $pid, $answer)
{
        if (!isTesterOnPuzzle($uid, $pid) && !isFormerTesterOnPuzzle($uid, $pid))
                return;

        $check = checkAnswer($pid, $answer);
        if ($check === FALSE) {
                $comment = "Incorrect answer attempt: $answer";
                $_SESSION['answer'] = "<h2>$answer is incorrect</h2>";
        } else {
                $comment = "Correct answer attempt: $answer";
                $_SESSION['answer'] = "<h2>$check is correct</h2>";
        }

        mysql_query('START TRANSACTION');
        $sql = sprintf("INSERT INTO answer_attempts (pid, uid, answer) VALUES ('%s', '%s', '%s')",
                        mysql_real_escape_string($pid), mysql_real_escape_string($uid), mysql_real_escape_string($answer));
        query_db($sql);

        addComment($uid, $pid, $comment, FALSE, TRUE);
        mysql_query('COMMIT');
}

function checkAnswer($pid, $attempt)
{
        $actual = getAnswersForPuzzle($pid);

        if ($actual == NULL)
                return FALSE;

        foreach ($actual as $a) {
                $answers = explode(',', $a);
                foreach ($answers as $ans) {
                        if (strcasecmp(preg_replace("/[^a-zA-Z0-9]/","",$attempt), preg_replace("/[^a-zA-Z0-9]/","",$ans)) == 0) {
                                $_SESSION['answer'] = $ans;
                                return $ans;
                        }
                }
        }

        return FALSE;
}

function insertFeedback($uid, $pid, $done, $time, $tried, $liked, $skills, $breakthrough, $fun, $difficulty, $when_return)
{
        mysql_query('START TRANSACTION');

        $comment = createFeedbackComment($done, $time, $tried, $liked, $skills, $breakthrough, $fun, $difficulty, $when_return);
        addComment($uid, $pid, $comment, FALSE, TRUE);

        if (strcmp($done, 'no') == 0) {
                $done = 1;
                doneTestingPuzzle($uid, $pid);
        } else
                $done = 0;

        $sql = sprintf("INSERT INTO testing_feedback (uid, pid, done, how_long, tried, liked, skills, breakthrough, fun, difficulty, when_return)
                        VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %s, %s, '%s')",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid),
                        mysql_real_escape_string($done), mysql_real_escape_string($time),
                        mysql_real_escape_string($tried), mysql_real_escape_string($liked),
                        mysql_real_escape_string($skills), mysql_real_escape_string($breakthrough),
                        mysql_real_escape_string($fun), 
                        mysql_real_escape_string($difficulty), mysql_real_escape_string($when_return));
        query_db($sql);

        mysql_query('COMMIT');
}

function doneTestingPuzzle($uid, $pid)
{
        $sql = sprintf("DELETE FROM test_queue WHERE pid='%s' AND uid='%s'",
                        mysql_real_escape_string($pid), mysql_real_escape_string($uid));
        query_db($sql);

        $sql = sprintf("INSERT INTO doneTesting (uid, pid) VALUES ('%s', '%s')
                                        ON DUPLICATE KEY UPDATE time=NOW()",
                                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        query_db($sql);
}

function createFeedbackComment($done, $time, $tried, $liked, $skills, $breakthrough, $fun, $difficulty, $when_return)
{
        $comment = "
        <p><strong>Do you intend to return to this puzzle?</strong></p>
        <p>$done</p><br />
        <p><strong>If so, when do you plan to return to it?</strong></p>
        <p>$when_return</p><br />
        <p><strong>How long did you spend on this puzzle (since your last feedback, if any)?</strong></p>
        <p>$time</p><br />
        <p><strong>Describe what you tried.</p></strong>
        <p>$tried</p><br />
        <p><strong>What did you like/dislike about this puzzle? </br>
        Is there anything you think should be changed with the puzzle?</br>
        Is there anything wrong with the technical details/formatting of the puzzle?</strong><br /></p>
        <p>$liked</p><br />
        <p><strong>Were there any special skills required to solve this puzzle?</strong></p>
        <p>$skills</p><br />
        <p><strong>What was the breakthrough point for you in this puzzle?</strong></p>
        <p>$breakthrough</p><br />
        <p><strong>How fun was this puzzle?</p></strong></p>
        <p>$fun</p><br />
        <p><strong>How would you rate the difficulty of this puzzle?</p></strong></p>
        <p>$difficulty</p>";
        return $comment;
}

function getRounds()
{
        $sql = sprintf("SELECT * FROM rounds ORDER BY unlock_at");
        return get_rows_null($sql);
}

function getAnswersForRound($rid)
{
        $sql = sprintf("SELECT * FROM answers_rounds JOIN answers ON answers.aid=answers_rounds.aid WHERE answers_rounds.rid='%s'", mysql_real_escape_string($rid));
        return get_rows_null($sql);
}

function getRoundForPuzzle($pid)
{
        $sql = sprintf("SELECT rounds.* FROM rounds, answers_rounds, answers WHERE answers.pid='%s' and answers_rounds.aid = answers.aid and rounds.rid = answers_rounds.rid;", mysql_real_escape_string($pid));
        return get_rows_null($sql);
}

function getNumberOfEditorsOnPuzzles()
{
        $deadstatusid = getDeadStatusId();
        $sql = sprintf('SELECT COUNT(editor_queue.uid) FROM puzzle_idea
                        LEFT JOIN editor_queue ON puzzle_idea.id=editor_queue.pid WHERE puzzle_idea.pstatus != %d
                        GROUP BY id', $deadstatusid);
        $numbers = get_elements($sql);

        $count = array_count_values($numbers);

        if (!isset($count['0']))
                $count['0'] = 0;
        if (!isset($count['1']))
                $count['1'] = 0;
        if (!isset($count['2']))
                $count['2'] = 0;
        if (!isset($count['3']))
                $count['3'] = 0;

        return $count;
}

function getNumberOfPuzzlesForUser($uid)
{
        $numbers['author'] = 0;
        $numbers['editor'] = 0;
        $numbers['spoiled'] = 0;
        $numbers['currentTester'] = 0;
        $numbers['doneTester'] = 0;
        $numbers['available'] = 0;

        $puzzles = getAllLivePuzzles();

        foreach ($puzzles as $pid) {
                if (isAuthorOnPuzzle($uid, $pid)) {
                        $numbers['author']++;
                }
                if (isEditorOnPuzzle($uid, $pid)) {
                        $numbers['editor']++;
                }
                if (isSpoiledOnPuzzle($uid, $pid)) {
                        $numbers['spoiled']++;
                }
                if (isTesterOnPuzzle($uid, $pid)) {
                        $numbers['currentTester']++;
                }
                if (isFormerTesterOnPuzzle($uid, $pid)) {
                        $numbers['doneTester']++;
                }
                if (isEditorAvailable($uid, $pid)) {
                        $numbers['available']++;
                }
        }

        return $numbers;
}

function alreadyRegistered($uid)
{
        $person = getPerson($uid);

        return (strlen($person['password']) > 0);
}

function getPic($uid)
{
        $sql = sprintf("SELECT picture FROM user_info WHERE uid='%s'", mysql_real_escape_string($uid));
        return get_element($sql);
}

function getPuzzlesNeedTestAdmin()
{
        $sql = "SELECT puzzle_idea.id FROM (puzzle_idea LEFT JOIN testAdminQueue ON puzzle_idea.id=testAdminQueue.pid)
                        JOIN pstatus ON puzzle_idea.pstatus=pstatus.id WHERE testAdminQueue.uid IS NULL AND pstatus.addToTestAdminQueue=1";
        return get_elements_null($sql);
}

function getPuzzleForTestAdminQueue($uid)
{
        $puzzles = getPuzzlesNeedTestAdmin();
        if ($puzzles == NULL)
                return FALSE;

        foreach ($puzzles as $pid) {
                if (canTestAdminPuzzle($uid, $pid))
                        return $pid;
        }

        return FALSE;
}

function canTestAdminPuzzle($uid, $pid)
{
        return (!isEditorOnPuzzle($uid, $pid)
                && !isTesterOnPuzzle($uid, $pid)
                && isPuzzleInAddToTestAdminQueue($pid));
}

function addToFactcheckQueue($uid, $pid)
{
        if (!canFactCheckPuzzle($uid, $pid))
                return FALSE;

        $sql = sprintf("INSERT INTO factcheck_queue (uid, pid) VALUES ('%s', '%s')",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        query_db($sql);
        // Subscribe factcheckers to comments on their puzzles
        // subscribe($uid, $pid);
}

function addToTestAdminQueue($uid, $pid)
{
        if (!canTestAdminPuzzle($uid, $pid))
                return FALSE;

        $sql = sprintf("INSERT INTO testAdminQueue (uid, pid) VALUES ('%s', '%s')",
                        mysql_real_escape_string($uid), mysql_real_escape_string($pid));
        query_db($sql);
        // Subscribe testadmins to comments on their puzzles
        // subscribe($uid, $pid);
}

function getInTestAdminQueue($uid)
{
        $sql = sprintf("SELECT pid FROM testAdminQueue WHERE uid='%s'", mysql_real_escape_string($uid));
        return get_elements_null($sql);
}

function canAcceptDrafts($pid)
{
        $sql = sprintf("SELECT puzzle_idea.id FROM puzzle_idea LEFT JOIN pstatus ON puzzle_idea.pstatus = pstatus.id
                        WHERE pstatus.acceptDrafts = '1' AND puzzle_idea.id='%s'", mysql_real_escape_string($pid));
        return has_result($sql);
}

function grantFactcheckPowers($uid)
{
  $sql = sprintf("INSERT INTO jobs (uid, jid) VALUES (%d, (select jid from priv where name = 'Fact Checker'))", $uid);
  query_db($sql);
}

function computeTestsolverScores()
{
        $in_testing = get_elements_null("SELECT uid, pid FROM test_queue");
        $done_testing = get_elements_null("SELECT uid, pid FROM doneTesting");

}

function getPuzzleRound($pid)
{
  $sql = sprintf("SELECT aid FROM answers WHERE pid = %d LIMIT 1", $pid);
  $aid = get_element_null($sql); 
  if ($aid == NULL) { 
    return (""); 
  }
  $sql = sprintf("SELECT rounds.name FROM rounds,answers_rounds WHERE rounds.rid=answers_rounds.rid AND answers_rounds.aid=%d", $aid);
  $roundname=get_element($sql);

  return($roundname);
}

function isPuzzleDead($pid)
{
 return(getStatusForPuzzle($pid) == getDeadStatusId());
}

function getDeadStatusId()
{
  // terrible hack to figure out which status ID is "dead"
  // so we can omit them by default from queue
  $statuses = getPuzzleStatuses();
  $deadstatusid = NULL;
  foreach ($statuses as $sid => $sname) {
                         if (strtoupper($sname) == "DEAD") {
                                 $deadstatusid = $sid;
                         }
  }
  return ($deadstatusid);
}

function displayTestingFeed()
{
  $comments = getTestFeedComments();
  if ($comments == NULL)
    return;
 
  foreach ($comments as $comment){
    $id = $comment['id'];
    $pid = $comment['pid'];
    $timestamp = $comment['timestamp'];
    $type = $comment['name'];
 
    if ($lastVisit == NULL || strtotime($lastVisit) < strtotime($timestamp)) {
      echo "<tr class='comment-new' id='comm$id'>";
    } else {
      echo "<tr class='comment' id='comm$id'>";
    }

    echo "\n<td class='$type"."Comment'>";
    echo "Puzzle ID: <a href='puzzle.php?pid=" . $pid . "'>$pid" . "</a>";
    echo "<br />Puzzle: " . getTitle($pid);
    echo "<br />$timestamp<br />";
    echo "\n<td class='$type"."Comment'>";
    if ($type == 'Testsolver') {
        $splitcomment=explode(" ", $comment['comment'], 4);
        if ($splitcomment[0] == "Correct") echo "<b>";
        echo $splitcomment[0] ." Answer Attempt ";
        if ($splitcomment[0] == "Correct") echo "</b>";
    } else {
        echo $comment['comment'];
    }
    echo '</td>';
    echo '</tr>';
    }
}

function getUserSolveTeam($uid)
{

        $sql = sprintf('SELECT testsolve_team.name FROM testsolve_team, user_testsolve_team WHERE user_testsolve_team.uid=%s AND user_testsolve_team.tid = testsolve_team.tid',
                        mysql_real_escape_string($uid));

        return(get_element_null($sql));
        

}

function getUserTestTeamID($uid)
{
        $sql = sprintf('SELECT tid FROM user_testsolve_team WHERE uid=%s', mysql_real_escape_string($uid));
        return(get_element_null($sql));
}

function getTestTeams()
{
        $sql = "SELECT * FROM testsolve_team";
        return(get_rows_null($sql));
}

function getTestTeamName($tid)
{
        $sql = sprintf('SELECT name FROM testsolve_team WHERE tid=%s', mysql_real_escape_string($tid));
        return(get_element_null($sql));
}

function setUserTestTeam($uid, $tid)
{
        $sql = sprintf('INSERT INTO user_testsolve_team (uid, tid) VALUES(%s , %s) ON DUPLICATE KEY UPDATE tid=%s',
                        mysql_real_escape_string($uid), mysql_real_escape_string($tid), mysql_real_escape_string($tid));
        query_db($sql);
}                

function getTestTeamPuzzles($tid)
{
        $sql = sprintf("SELECT pstatus.id FROM pstatus WHERE pstatus.inTesting = '1'");
        $testingstatusid = get_element($sql);

        $sql = sprintf('SELECT pid FROM testsolve_team_queue,puzzle_idea WHERE tid=%s AND puzzle_idea.id = testsolve_team_queue.pid AND puzzle_idea.pstatus = %s', 
                mysql_real_escape_string($tid), mysql_real_escape_string($testingstatusid));
        return(get_elements_null($sql));
}

function getPuzzleTestTeam($pid)
{
        $sql = sprintf('SELECT tid FROM testsolve_team_queue WHERE pid=%s', mysql_real_escape_string($pid));
        return(get_element_null($sql));
}

function setPuzzleTestTeam($pid, $tid)
{
        $sql = sprintf('INSERT INTO testsolve_team_queue (pid, tid) VALUES(%s, %s) ON DUPLICATE KEY UPDATE tid=%s',
                        mysql_real_escape_string($pid), mysql_real_escape_string($tid), mysql_real_escape_string($tid));
        query_db($sql);
}

function markUnseen($uid, $pid)
{
        $sql = sprintf("INSERT INTO last_visit (pid, uid, date) VALUES ('%s', '%s', 'NULL')
                                        ON DUPLICATE KEY UPDATE date='NULL'", mysql_real_escape_string($pid),
                                        mysql_real_escape_string($uid));
        query_db($sql);
}

function utilsError($msg)
{
        mysql_query('ROLLBACK');
        echo "An error has occurred. Please try again. <br />";
        echo "<pre>$msg</pre>";
        foot();
        exit(1);
}
?>
