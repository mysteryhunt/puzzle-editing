<?php
	require_once "config.php";	
	require_once "db-func.php";

// Check that the user is logged in.
// If so, update the session (to provent timing out) and return the uid;
// If not, redirect to login page.
function isLoggedIn()
{
	if (isset($_SESSION['uid'])) {
		$_SESSION['time'] = time();
		return $_SESSION['uid'];
	} else {
		header("Location: " . URL . "/login.php");
		exit(0);
	}
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
  return $s;
}


function isEditor($uid)
{
	return hasPriv($uid, 'addToEditingQueue');
}

function isTestingAdmin($uid)
{
	return hasPriv($uid, 'seeTesters');
}

function isLurker($uid)
{
	return hasPriv($uid, 'isLurker');
}

function isBlind($uid)
{
	return hasPriv($uid, 'isBlind');
}

function isServerAdmin($uid)
{
	return hasPriv($uid, 'changeServer');
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

function isFormerTesterOnPuzzle($uid, $pid)
{
	$sql = sprintf("SELECT * FROM doneTesting WHERE uid='%s' AND pid='%s'",
			mysql_real_escape_string($uid), mysql_real_escape_string($pid));
	return has_result($sql);
}

function isLurkerOnPuzzle($uid, $pid)
{
	return (isLurker($uid) && !isAuthorOnPuzzle($uid, $pid) && !isEditorOnPuzzle($uid, $pid) && !isTesterOnPuzzle($uid, $pid));
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

// Get user's name. Return username if no name in database.
function getUserName($uid)
{
	$sql = sprintf("SELECT value from user_info_values where user_info_key_id = " .
		       "(select id from user_info_key where shortname = 'fname') AND " .
		       "person_id = '%s'", mysql_real_escape_string($uid));
	$result = get_row_null($sql);
	$first = $result['value'];

	$sql = sprintf("SELECT value from user_info_values where user_info_key_id = " .
		       "(select id from user_info_key where shortname = 'lname') AND " .
		       "person_id = '%s'", mysql_real_escape_string($uid));
	$result = get_row_null($sql);
	$last = $result['value'];

	if ($first == '' || $last == '') {
		$sql = sprintf("SELECT username FROM user_info WHERE uid='%s'", mysql_real_escape_string($uid));
		$result = get_row($sql);
		return $result['username'];
	} else
		return "$first $last";
}

function getEmail($uid)
{
	$sql = sprintf("SELECT email FROM user_info WHERE uid='%s'",
					mysql_real_escape_string($uid));
	return get_element($sql);
}

// Get all authors of $pid
// Return assoc array of [uid] => [name]
function getAuthorsForPuzzle($pid)
{
	$sql = sprintf("SELECT uid FROM authors WHERE pid='%s'", mysql_real_escape_string($pid));
	return getUsersForPuzzle($pid, $sql);
}

// Get all editors of $pid
// Return assoc array of [uid] => [name]
function getEditorsForPuzzle($pid)
{
	$sql = sprintf("SELECT uid FROM editor_queue WHERE pid='%s'", mysql_real_escape_string($pid));
	return getUsersForPuzzle($pid, $sql);
}

// Get all users spoiled individually (not from a list) for $pid
// Return assoc array of [uid] => [name]
function getSpoiledUsersForPuzzle($pid)
{
	$sql = sprintf("SELECT uid FROM spoiled WHERE pid='%s'", mysql_real_escape_string($pid));
	return getUsersForPuzzle($pid, $sql);
}

// Get uid and name of users returned by a sql query
// Return assoc array of [uid] => [name]
function getUsersForPuzzle($pid, $sql)
{
	$result = get_elements_null($sql);
	
	$users = NULL;
	if ($result != NULL) {
		foreach ($result as $uid) {
			$users[$uid] = getUserName($uid);
		}
	}
	
	return $users;
}





// Get a comma-separated list of author names
function getAuthorsAsList($pid)
{
	$sql = sprintf("SELECT uid FROM authors WHERE pid='%s'",
			mysql_real_escape_string($pid));
	$authors = get_elements_null($sql);
	
	return getUserNamesAsList($authors);
}

// Get a comma-separated list of editor names
function getEditorsAsList($pid)
{	
	$sql = sprintf("SELECT uid FROM editor_queue WHERE pid='%s'",
			mysql_real_escape_string($pid));
	$editors = get_elements_null($sql);

	$names = getUserNamesAsList($editors);
	
	if ($names == '')
		$names = '(none)';	
		
	return $names;
}

// Get comma-separated list of users' names
function getUserNamesAsList($users)
{
	if ($users == NULL)
		return '(none)';
	
	$names = '';
	foreach ($users as $uid) {
		if ($names != '')
			$names .= ', ';
		$names .= getUserName($uid);
	}
	
	return $names;
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

function getNotes($pid)
{
	$sql = sprintf("SELECT notes FROM puzzle_idea WHERE id='%s'", mysql_real_escape_string($pid));
	return get_element($sql);
}


// Update the title, summary, and description of the puzzle (from form on puzzle page)
function changeTitleSummaryDescription($uid, $pid, $title, $summary, $description)
{
	// Check that user is author or lurker
	if (!canEditTSD($uid, $pid))
		utilsError("You do not have permission to edit the title, summary, or description of puzzle $pid");
	
	// Get the old title, summary, and description
	$puzzleInfo = getPuzzleInfo($pid);
	$oldTitle = $puzzleInfo["title"];
	$oldSummary = $puzzleInfo["summary"];
	$oldDescription = $puzzleInfo["description"];
		
	// Make sure this user has permission to modify the puzzle info
	if (isAuthorOnPuzzle($uid, $pid) || isLurker($uid)) {
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

function updateNotes($uid = 0, $pid, $oldNotes, $cleanNotes)
{		
	$sql = sprintf("UPDATE puzzle_idea SET notes='%s' WHERE id='%s'",
			mysql_real_escape_string($cleanNotes), mysql_real_escape_string($pid));
			query_db($sql);
		
	$comment = "Changed status notes from \"$oldNotes\" to \"$cleanNotes\"";

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
		return '(none)';
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

function removeAnswers($uid, $pid, $remove)
{
	if ($remove == NULL)
		return;
		
	if (!canChangeAnswers($uid))
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
		emailComment(FALSE, $pid, $cleanComment);
	else
		emailComment($uid, $pid, $cleanComment);
}

function emailComment($uid, $pid, $cleanComment)
{
	if ($uid == FALSE)
		$name = "Anonymous Testsolver";
	else
		$name = getUserName($uid);
	
	$message = "$name commented on puzzle $pid:\n";
	$message .= "$cleanComment";
	$subject = "Comment on Puzzle $pid";
	$link = URL . "/puzzle?pid=$pid";

	$users = getSubbed($pid);
	if ($users == NULL)
		return;
		
	foreach ($users as $user)
	{
		if ($user != $uid) {
			sendEmail($user, $subject, $message, $link);
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

        if (!DEVMODE)
		mail($address, $subject, $msg);
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


function getSpoiledAsList($pid)
{
	$sql = sprintf("SELECT uid FROM spoiled WHERE pid='%s'", mysql_real_escape_string($pid));
	$users = get_elements_null($sql);
	
	$spoiled = '';
	if ($users != NULL) {
		foreach ($users as $uid) {
			if ($spoiled != '')
				$spoiled .= ', ';
			$spoiled .= getUserName($uid);
		}
	}
	
	if ($spoiled == '')
		$spoiled = '(none)';
		
	return $spoiled;
}

function getCurrentTestersAsList($pid)
{
	$sql = sprintf("SELECT uid FROM test_queue WHERE pid='%s'",
			mysql_real_escape_string($pid));
	$users = get_elements_null($sql);
	
	if ($users == NULL)
		return '(none)';
		
	$testers = '';
	foreach ($users as $uid) {
		if ($testers != '')
			$testers .= ', ';
		$testers .= getUserName($uid);
	}
	
	return $testers;
}

function getCurrentTestersAsEmailList($pid)
{
	$testers = array_keys(getCurrentTestersForPuzzle($pid));
	
	return getUserNamesAndEmailsAsList($testers);
}

function getOncallTestersAsList($pid)
{
	$sql = sprintf("SELECT on_call FROM test_call WHERE pid='%s'",
			mysql_real_escape_string($pid));
	$users = get_elements_null($sql);
	
	if ($users == NULL)
		return '(none)';
		
	$testers = '';
	foreach ($users as $uid) {
		if ($testers != '')
			$testers .= ', ';
		$testers .= getUserName($uid);
	}
	
	return $testers;
}

function getFinishedTestersAsList($pid)
{
	$sql = sprintf("SELECT uid FROM doneTesting WHERE pid='%s'",
			mysql_real_escape_string($pid));
	$users = get_elements_null($sql);
	
	if ($users == NULL)
		return '(none)';
		
	$testers = '';
	foreach ($users as $uid) {
		if ($testers != '')
			$testers .= ', ';
		$testers .= getUserName($uid);
	}
	
	return $testers;
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
		
	if (!canRemoveSpoiled($uid, $pid))
		utilsError("You do not have permission to remove spoiled users from puzzle $pid.");
		
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
		
		$subject = "Spoiled on Puzzle $pid";
		$message = "$name removed you as spoiled on puzzle $pid.";
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

function addSpoiledUser($uid, $pid, $addUser)
{
	if ($addUser == NULL)
		return;
		
	if (!canAddSpoiled($uid, $pid))
		utilsError("You do not have permission to add spoiled to puzzle $pid");
		
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
		$subject = "Spoiled on Puzzle $pid";
		$message = "$name added you as spoiled on puzzle $pid.";
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
	// Check that the user has permission to change authors for this puzzle
	if (!isAuthorOnPuzzle($uid, $pid) && !isLurker($uid))
		return;
		
	mysql_query('START TRANSACTION');
	addAuthors($uid, $pid, $add);
	
	if (isLurker($uid))
		removeAuthors($uid, $pid, $remove);
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

function addAuthors($uid, $pid, $add)
{		
	if ($add == NULL)
		return;
		
	if (!canAddAuthor($uid, $pid))
		utilsError("You do not have permission to add authors to puzzle $pid");
		
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
		$subject = "Author on Puzzle $pid";
		$message = "$name added you as an author on puzzle $pid.";
		$link = URL . "/puzzle?pid=$pid";
		sendEmail($auth, $subject, $message, $link);
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
		
	if (!canRemoveAuthor($uid, $pid))
		utilsError("You do not have permission to remove authors on puzzle $pid.");
		
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
		$subject = "Author on Puzzle $pid";
		$message = "$name removed you as an author on puzzle $pid.";
		$link = URL . "/author";
		sendEmail($auth, $subject, $message, $link);
	}
	
	$comment .= ' as author';
	if (count($remove) > 1)
		$comment .= "s";
		
	addComment($uid, $pid, $comment, TRUE);
}

function addEditors($uid, $pid, $add)
{
	if ($add == NULL)
		return;
		
	if (!canAddEditor($uid, $pid))
		utilsError("You do not have permission to add an editor to puzzle $pid");
		
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
		$subject = "Editor on Puzzle $pid";
		$message = "$name added you as an editor to puzzle $pid.";
		$link = URL . "/puzzle?pid=$pid";
		sendEmail($editor, $subject, $message, $link);
	}
		
	$comment .= ' as editor';
	if (count($add) > 1)
		$comment .= "s";
		
	addComment($uid, $pid, $comment, TRUE);
}

function removeEditors($uid, $pid, $remove)
{	
	if ($remove == NULL)
		return;
		
	if (!canRemoveEditor($uid, $pid))
		utilsError("You do not have permission to remove editors on puzzle $pid.");
		
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
		$subject = "Editor on Puzzle $pid";
		$message = "$name removed you as an editor on puzzle $pid.";
		$link = URL . "/editor";
		sendEmail($editor, $subject, $message, $link);
	}
	
	$comment .= ' as editor';
	if (count($remove) > 1)
		$comment .= "s";

	addComment($uid, $pid, $comment, TRUE);
}

function canEditTSD($uid, $pid)
{
	return (isAuthorOnPuzzle($uid, $pid) || isLurker($uid)); 
}

function canChangeAnswers($uid)
{
	return hasPriv($uid, 'canEditAll');
}

function canAddAuthor($uid, $pid)
{
	return (isAuthorOnPuzzle($uid, $pid) || isLurker($uid));
}

function canRemoveAuthor($uid, $pid)
{
	return (isLurker($uid));
}

function canAddSpoiled($uid, $pid)
{
	return (isAuthorOnPuzzle($uid, $pid) || isEditorOnPuzzle($uid, $pid) || isLurker($uid));
}

function canRemoveSpoiled($uid, $pid)
{
	return (isLurker($uid));
}

function canAddEditor($uid, $pid)
{
	return (isEditorOnPuzzle($uid, $pid) || isLurker($uid));
}

function canRemoveEditor($uid, $pid)
{
	return (isEditorOnPuzzle($uid, $pid) || isLurker($uid));
}

function canSeeTesters($uid, $pid)
{
	return (isTestingAdminOnPuzzle($uid, $pid) || isLurkerOnPuzzle($uid, $pid));
}

function canChangeStatus($uid, $pid)
{
	return (isLurker($uid) || isEditorOnPuzzle($uid, $pid) || isTestingAdminOnPuzzle($uid, $pid));
}

function canChangeNotes($uid, $pid)
{
	return (isLurker($uid) || isAuthorOnPuzzle($uid, $pid) || isEditorOnPuzzle($uid, $pid) || isTestingAdminOnPuzzle($uid, $pid));
}

function canUploadFiles($uid, $pid)
{
	return (isAuthorOnPuzzle($uid, $pid) || isEditorOnPuzzle($uid, $pid) || isLurker($uid));
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

function getPuzzleStatuses()
{
    return get_assoc_array("SELECT id, name FROM pstatus ORDER BY ord ASC", "id", "name");
}

function getPuzzlesWithStatus($sid)
{
	$sql = sprintf("SELECT id FROM puzzle_idea WHERE pstatus='%s'", mysql_real_escape_string($sid));
	return get_elements_null($sql);
}

function getStatusForPuzzle($pid)
{
	$sql = sprintf("SELECT pstatus FROM puzzle_idea WHERE id='%s'", mysql_real_escape_string($pid));
	return get_element($sql);
}

function changeStatus($uid, $pid, $status)
{
	if (!canChangeStatus($uid, $pid))
		utilsError("You do not have permission to change statuses on puzzle $pid");

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
}

function isStatusInTesting($sid)
{
	$sql = sprintf("SELECT inTesting FROM pstatus WHERE id='%s'", mysql_real_escape_string($sid));
	return get_element($sql);
}

function changeNotes($uid, $pid, $notes)
{
	if (!canChangeNotes($uid, $pid))
		utilsError("You do not have permission to change notes on puzzle $pid");

	$purifier = new HTMLPurifier();
	mysql_query('START TRANSACTION');

	$oldNotes = getNotes($pid);
	$cleanNotes = $purifier->purify($notes);
	$cleanNotes = htmlspecialchars($cleanNotes);
	updateNotes($uid, $pid, $oldNotes, $cleanNotes);

	mysql_query('COMMIT');  
}

function emailTesters($pid, $status)
{
	$subject = "Puzzle $pid Status Change";
	
	if (!isStatusInTesting($status)) {
		$message = "Puzzle $pid was removed from testing";
	} else {
		$statusName = getPuzzleStatusName($status);
		$message = "Puzzle $pid's status was changed to $statusName.";
	}
	
	$link = URL . "/testsolving";
	
	$testers = getCurrentTestersForPuzzle($pid);
	foreach ($testers as $uid => $name) {
		sendEmail($uid, $subject, $message, $link);
	}
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
	if (!canUploadFiles($uid, $pid)) {
		utilsError("You do not have permission to upload files on this puzzle.");
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
			$_SESSION['upload_error'] = "There was an error uploading the file, please try again. (Note: file size is limited to 25MB)";
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
			$_SESSION['upload_error'] = "There was an error uploading the file, please try again. (Note: file size is limited to 25MB)";
		}
	}
}
	
function getComments($pid)
{
	$sql = sprintf("SELECT comments.id, comments.uid, comments.comment, comments.type, 
					comments.timestamp, comments.pid, comment_type.name FROM
					comments LEFT JOIN comment_type ON comments.type=comment_type.id
					WHERE comments.pid='%s' ORDER BY comments.timestamp ASC",
					mysql_real_escape_string($pid));
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
	$sql = sprintf("INSERT INTO email_sub (uid, pid) VALUES ('%s', '%s')",
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
	$sql = 'SELECT * FROM user_info ORDER BY username';
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

function getLastCommentDate($pid)
{
	$sql = sprintf("SELECT timestamp FROM comments WHERE pid='%s' ORDER BY timestamp DESC", mysql_real_escape_string($pid));
	$result = get_elements_null($sql);
	
	if ($result == NULL)
		return NULL;
	else
		return $result[0];
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

function getPuzzlesInEditorQueue($uid)
{
	$sql = sprintf("SELECT pid FROM editor_queue WHERE uid='%s'",
			mysql_real_escape_string($uid));
	$puzzles = get_elements_null($sql);
	
	return sortByLastCommentDate($puzzles);
}

function getPuzzlesInOncallTestQueue($uid)
{
	$sql = sprintf("SELECT pid FROM test_call WHERE on_call='%s'",
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
	return get_elements_null($sql);
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
		$p = array_pop($puzzles);	// Pop first puzzle from array
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
	mysql_query('COMMIT');

        // For keeping track of how many testers have this puzzle open.
        incrementPuzzleTesterCount($pid);
}

function getPuzzleToTest($uid)
{
	$puzzles = getAvailablePuzzlesToTestForUser($uid);
	if ($puzzles == NULL)
		return FALSE;
		
	$sort = NULL;
	foreach ($puzzles as $pid) {
		$status = getStatusForPuzzle($pid);
		$numTesters = getNumTesters($pid);
			
		if ($status == 4)
			$num = $numTesters * 2;
		else if ($status == 7)
			$num = $numTesters * 3;
		else
			$num = $numTesters;
				
		$sort[$pid] = $num;
	}
	
	asort($sort);
	return key($sort);
}

function canUseMoreTesters($pid)
{
	$testers_limit = 5;

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
	
	return ($status == 5);
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

function insertFeedback($uid, $pid, $done, $time, $tried, $liked, $when_return)
{
	mysql_query('START TRANSACTION');
	
	$comment = createFeedbackComment($done, $time, $tried, $liked, $when_return);
	addComment($uid, $pid, $comment, FALSE, TRUE);
	
	if (strcmp($done, 'no') == 0) {
		$done = 1;
		doneTestingPuzzle($uid, $pid);
	} else
		$done = 0;
		
	$sql = sprintf("INSERT INTO testing_feedback (uid, pid, done, how_long, tried, liked, when_return)
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
			mysql_real_escape_string($uid), mysql_real_escape_string($pid),
			mysql_real_escape_string($done), mysql_real_escape_string($time),
			mysql_real_escape_string($tried), mysql_real_escape_string($liked), mysql_real_escape_string($when_return));
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

function createFeedbackComment($done, $time, $tried, $liked, $when_return)
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
	<p><strong>What did you like/dislike about this puzzle? How hard do you think it is? Is there anything you think should be changed?</strong></p>
	<p>$liked</p>";
	return $comment;
}

function getRounds()
{
	$sql = sprintf("SELECT * FROM rounds");
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
	return get_row_null($sql);
}
	
function getNumberOfEditorsOnPuzzles()
{
	$sql = 'SELECT COUNT(editor_queue.uid) FROM puzzle_idea 
			LEFT JOIN editor_queue ON puzzle_idea.id=editor_queue.pid 
			GROUP BY id';
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

	$puzzles = getAllPuzzles();
	
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

function addToTestAdminQueue($uid, $pid)
{
	if (!canTestAdminPuzzle($uid, $pid)) 
		return FALSE;

	$sql = sprintf("INSERT INTO testAdminQueue (uid, pid) VALUES ('%s', '%s')",
			mysql_real_escape_string($uid), mysql_real_escape_string($pid));
	query_db($sql);
}

function getInTestAdminQueue($uid)
{
	$sql = sprintf("SELECT pid FROM testAdminQueue WHERE uid='%s'", mysql_real_escape_string($uid));
	return get_elements_null($sql);
}

function getTestingAdminsForPuzzleAsList($pid)
{
	$sql = sprintf("SELECT uid FROM testAdminQueue WHERE pid='%s'", mysql_real_escape_string($pid));
	$admins = get_elements_null($sql);
	
	return getUserNamesAsList($admins);
}

function canAcceptDrafts($pid)
{
	$sql = sprintf("SELECT puzzle_idea.id FROM puzzle_idea LEFT JOIN pstatus ON puzzle_idea.pstatus = pstatus.id
			WHERE pstatus.acceptDrafts = '1' AND puzzle_idea.id='%s'", mysql_real_escape_string($pid));
	return has_result($sql);
}

function utilsError($msg)
{
	mysql_query('ROLLBACK');
	echo "An error has occurred. Please try again. <br />";
	echo $msg;
	foot();
	exit(1);
}
?>
