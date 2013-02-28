<?php 
	require_once "config.php";
	require_once "html.php";
	require_once "utils.php";

	// Redirect to the login page, if not logged in
	$uid = isLoggedIn();

	if (isset($_POST['editTSD'])) {
		$pid = $_POST['pid'];
		$uid = $_POST['uid'];
		$title = $_POST['title'];
		$summary = $_POST['summary'];
		$description = $_POST['description'];
			
		changeTitleSummaryDescription($uid, $pid, $title, $summary, $description);
		
		header('Location: ' . URL . "/puzzle.php?pid=$pid");
		exit(0);
	}
	
	if (isset($_POST['cancelTSD'])) {
		$pid = $_POST['pid'];
		
		header('Location: ' . URL . "/puzzle.php?pid=$pid");
		exit(0);
	}
	
	if (isset($_POST['changeAnswers'])) {
		$pid = $_POST['pid'];
		$uid = $_POST['uid'];
		
		if (isset($_POST['addAns']))
			$add = $_POST['addAns'];
		else
			$add = NULL;
			
		if (isset($_POST['removeAns']))
			$remove = $_POST['removeAns'];
		else
			$remove = NULL;

		changeAnswers($uid, $pid, $add, $remove);

		header("Location: " . URL . "/puzzle.php?pid=$pid");
		exit(0);
	}	
	
	if (isset($_POST['changeAuthors'])) {
		$pid = $_POST['pid'];
		$uid = $_POST['uid'];
		
		if (isset($_POST['addAuth']))
			$add = $_POST['addAuth'];
		else
			$add = NULL;
			
		if (isset($_POST['removeAuth']))
			$remove = $_POST['removeAuth'];
		else
			$remove = NULL;
		
		changeAuthors($uid, $pid, $add, $remove);
		
		header("Location: " . URL . "/puzzle.php?pid=$pid");
		exit(0);
	}		
	
	if (isset($_POST['changeSpoiled'])) {
		$pid = $_POST['pid'];
		$uid = $_POST['uid'];
		
		if (isset($_POST['removeSpoiledUser']))
			$removeUser = $_POST['removeSpoiledUser'];
		else
			$removeUser = NULL;
			
		if (isset($_POST['addSpoiledUser']))
			$addUser = $_POST['addSpoiledUser'];
		else
			$addUser = NULL;
			
		changeSpoiled($uid, $pid, $removeUser, $addUser);
		
		header("Location: " . URL . "/puzzle.php?pid=$pid");
		exit(0);
	}
		
	if (isset($_POST['changeEditors'])) {
		$pid = $_POST['pid'];
		$uid = $_POST['uid'];
		
		if (isset($_POST['addEditor']))
			$add = $_POST['addEditor'];
		else
			$add = NULL;
			
		if (isset($_POST['removeEditor']))
			$remove = $_POST['removeEditor'];
		else
			$remove = NULL;
			
		changeEditors($uid, $pid, $add, $remove);
		
		header("Location: " . URL . "/puzzle.php?pid=$pid");
		exit(0);
	}
	
	if (isset($_POST['changeStatus'])) {
		$pid = $_POST['pid'];
		$uid = $_POST['uid'];
		
		$status = $_POST['status'];
		
		changeStatus($uid, $pid, $status);
		
		header("Location: " . URL . "/puzzle.php?pid=$pid");
		exit(0);
	}
	
	if (isset($_POST['changeNotes'])) {
		$pid = $_POST['pid'];
		$uid = $_POST['uid'];
		
		$notes = $_POST['notes'];
		
		changeNotes($uid, $pid, $notes);
		
		header("Location: " . URL . "/puzzle.php?pid=$pid");
		exit(0);
	}
	
	if (isset($_POST['uploadFile'])) {
		$pid = $_POST['pid'];
		$uid = $_POST['uid'];
		
		$type = $_POST['filetype'];
		$file = $_FILES['fileupload'];
			
		uploadFiles($uid, $pid, $type, $file);
		
		header("Location: " . URL . "/puzzle.php?pid=" . $pid);
		exit(0);
	}
	
	if (isset($_POST['addcomment'])) {
		$pid = $_POST['pid'];
		$uid = $_POST['uid'];
		
		$comment = $_POST['comment'];
		
		addComment($uid, $pid, $comment);
		
		header("Location: " . URL . "/puzzle.php?pid=" . $pid);
		exit(0);
	} 
	
	if (isset($_POST['emailSub'])) {
		$uid = $_POST['uid'];
		$pid = $_POST['pid'];
		subscribe($uid, $pid);
		
		header("Location: " . URL . "/puzzle.php?pid=" . $pid);
		exit(0);
	}
	
	if (isset($_POST['emailUnsub'])) {
		$uid = $_POST['uid'];
		$pid = $_POST['pid'];
		unsubscribe($uid, $pid);
		
		header("Location: " . URL . "/puzzle.php?pid=" . $pid);
		exit(0);
	}
	
	if (isset($_POST['getTest'])) {
	 	$uid = $_POST['uid'];
	 	
		$pid = getPuzzleToTest($uid);
		if ($pid)
			addPuzzleToTestQueue($uid, $pid);
		else
			$_SESSION['failedToAdd'] = TRUE;

		header("Location: " . URL . "/testsolving.php");
		exit(0);
	}

	if (isset($_POST['TestAdminPuzzle'])) {
		$uid = $_POST['uid'];
		$pid = $_POST['pid'];

		if (!addToTestAdminQueue($uid, $pid))
                        $_SESSION['failedToAdd'] = TRUE;

		header("Location: " . URL . "/testadmin.php");
		exit(0);
	}
	
	if (isset($_POST['getTestId'])) {
		$uid = $_POST['uid'];
		$pid = $_POST['pid'];
		
		if (canTestPuzzle($uid, $pid, TRUE) && !isTesterOnPuzzle($uid, $pid)) {
			addPuzzleToTestQueue($uid, $pid);
			header("Location: " . URL . "/test.php?pid=" . $pid);
		} else if (isTesterOnPuzzle($uid, $pid)) {
			header("Location: " . URL . "/test.php?pid=" . $pid);
		} else {
			if (!isset($_SESSION['testError']))
				$_SESSION['testError'] = "Could not add Puzzle $pid to your queue";
			header("Location: " . URL . "/testsolving.php");
		}
		exit(0);
	}
	
	if (isset($_POST['getPuzz'])) {
		$uid = $_POST['uid'];
		
		$pid = $_POST['pid'];
		if (!$pid)
			$pid = getNewPuzzleForEditor($uid);

		if ($pid && isEditorAvailable($uid, $pid))
			addPuzzleToEditorQueue($uid, $pid);
		else
			$_SESSION['failedToAdd'] = TRUE;

		header("Location: " . URL . "/editor.php");
		exit(0);
	}
	
	if (isset($_POST['checkAns'])) {
		$uid = $_POST['uid'];
		$pid = $_POST['pid'];
		$answer = $_POST['ans'];
		
		$_SESSION['answer'] = $answer;
		
		makeAnswerAttempt($uid, $pid, $answer);
		
		header("Location: " . URL . "/test.php?pid=$pid");
		exit(0);
	}
	
	if (isset($_POST['feedback'])) {
		$pid = $_POST['pid'];
		$uid = $_POST['uid'];
		$done = $_POST['done'];
		$time = $_POST['time'];
		$tried = $_POST['tried'];
		$liked = $_POST['liked'];
		$when_return = $_POST['when_return'];
		insertFeedback($uid, $pid, $done, $time, $tried, $liked, $when_return);
		$_SESSION['feedback'] = "Thank you for giving feedback on this puzzle!";
		
		if (strcmp($done, 'no') == 0) {
			header("Location: " . URL . "/testsolving.php");
		} else
			header("Location: " . URL . "/test.php?pid=$pid");
		exit(0);
	}
	
	if (isset($_POST['getTestAdminPuzz'])) {
		$uid = $_POST['uid'];
		
		$p = getPuzzleForTestAdminQueue($uid);
		if ($p != FALSE)
			addToTestAdminQueue($uid, $p);
		else
			$_SESSION['failedToAdd'] = TRUE;
			
		header("Location: " . URL . "/testadmin.php");
		exit(0);
	}
	
//-------------------------------------


	head();
	echo 'An unknown error seems to have occurred. <br />';
	echo 'Please try again, or contact <a href="mailto:wind-up-birds-systems@wind-up-birds.org">the Server Administrators</a> for help. <br />';
	echo 'Note: uploaded file size is limited to 25MB. <br />';
	foot();

	
?>
