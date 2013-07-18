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

        if (isset($_POST['changeFactcheckers'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid'];

                if (isset($_POST['addFactchecker']))
                        $add = $_POST['addFactchecker'];
                else
                        $add = NULL;

                if (isset($_POST['removeFactchecker']))
                        $remove = $_POST['removeFactchecker'];
                else
                        $remove = NULL;

                changeFactcheckers($uid, $pid, $add, $remove);

                header("Location: " . URL . "/puzzle.php?pid=$pid");
                exit(0);
        }

        if (isset($_POST['changeAuthors'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid'];

                if (isset($_POST['addAuth'])) {
                        $add = $_POST['addAuth'];
                        subscribe ($uid, $pid);
                }
                else
                        $add = NULL;

                if (isset($_POST['removeAuth'])) {
                        $remove = $_POST['removeAuth'];
                        unsubscribe ($uid, $pid);
                }
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
                if ($removeUser[0] == $uid)
                {
                        head();
                        echo "Removed you as spoiled. Not redirecting to avoid re-spoiling you.\n";
                        exit(0);
                }

		header("Location: " . URL . "/puzzle.php?pid=$pid");
                exit(0);
        }

        if (isset($_POST['changeEditors'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid'];

                if (isset($_POST['addEditor'])) {
                        $add = $_POST['addEditor'];
			if (isAutoSubEditor($uid)) {
                       	   subscribe($uid, $pid);
			}
                }
                else
                        $add = NULL;

                if (isset($_POST['removeEditor'])) {
                        $remove = $_POST['removeEditor'];
			if (isAutoSubEditor($uid)) {
                           unsubscribe($uid, $pid);
			}
                }
                else
                        $remove = NULL;

                changeEditors($uid, $pid, $add, $remove);

                header("Location: " . URL . "/puzzle.php?pid=$pid");
                exit(0);
        }

        if (isset($_POST['changeRoundCaptain'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid'];

                if (isset($_POST['addRoundCaptain']))
                        $add = $_POST['addRoundCaptain'];
                else
                        $add = NULL;

                if (isset($_POST['removeRoundCaptain']))
                        $remove = $_POST['removeRoundCaptain'];
                else
                        $remove = NULL;

                changeRoundCaptains($uid, $pid, $add, $remove);

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

        if (isset($_POST['changeCredits'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid'];

                $credits = $_POST['credits'];

                changeCredits($uid, $pid, $credits);

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

        if (isset($_POST['changeWikiPage'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid'];

                $wikiPage = $_POST['wikiPage'];

                changeWikiPage($uid, $pid, $wikiPage);

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

        if (isset($_POST['requestTestsolve'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid'];
                $notes = $_POST['notes'];

                requestTestsolve($uid, $pid, $notes);

                header("Location: " . URL . "/puzzle.php?pid=" . $pid);
                exit(0);
        }

        if (isset($_POST['clearTestsolveRequests'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid'];

                clearTestsolveRequests($pid);

                header("Location: " . URL . "/puzzle.php?pid=" . $pid);
                exit(0);
        }

        if (isset($_POST['clearOneTestsolveRequest'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid'];

                clearOneTestsolveRequest($pid);

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

        if (isset($_POST['SelfAddFactchecker'])) {
          // User wishes to opt in to factchecking duty.
          $uid = $_POST['uid'];
          grantFactcheckPowers($uid);
                      header("Location: " . URL . "/factcheck");
                      exit(0);
        }

        if (isset($_POST['FactcheckPuzzle'])) {
                $uid = $_POST['uid'];
                $pid = $_POST['pid'];

                addFactcheckers($uid, $pid, array($uid));

                header("Location: " . URL . "/factcheck");
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

                if (isTestingAdmin($uid)) {
                        header("Location: " . URL . "/test.php?pid=" . $pid);
                        exit(0);
                }

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

                if ($pid && isEditorAvailable($uid, $pid)) {
                        addPuzzleToEditorQueue($uid, $pid);
                        //subscribe($uid, $pid);
                }
                else {
                        $_SESSION['failedToAdd'] = TRUE;
                        $_SESSION['failedToAddEdit'] = TRUE;
                }

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
                $breakthrough = $_POST['breakthrough'];
                $skills = $_POST['skills'];
                $fun = $_POST['fun'];
                $difficulty = $_POST['difficulty'];
                $when_return = $_POST['when_return'];
                insertFeedback($uid, $pid, $done, $time, $tried, $liked, $skills, $breakthrough, $fun, $difficulty, $when_return);
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

        if (isset($_POST['postprod'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid'];

                pushToPostProd($uid, $pid);

                header("Location: " . URL . "/puzzle.php?pid=$pid");
                exit(0);
        }

        if (isset($_POST['postprodAll'])) {
                $uid = $_POST['uid'];

                postprodAll($uid);

                header("Location: " . URL . "/postprod.php");
                exit(0);
        }

        if (isset($_POST['setPuzzApprove'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid'];
                $approve = $_POST['puzzApprove'];
                
                setPuzzApprove($uid, $pid, $approve);
                
                header("Location: "  . URL . "/puzzle.php?pid=$pid");
                exit(0);
        }

        if (isset($_POST['killPuzzle'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid']; 
                
                changeStatus($uid, $pid, getDeadStatusId());

                header("Location: "  . URL . "/puzzle.php?pid=$pid");
                exit(0);
        }

        if (isset($_POST['markunseen'])) {
                $pid = $_POST['pid'];
                $uid = $_POST['uid'];

                markUnseen($uid, $pid);
                
                header("Location: " . URL);
                exit(0);
        }

        if (isset($_POST['setUserTestTeam'])) {
                $tid = $_POST['tid'];
                $uid = $_POST['uid'];
                
                //echo "tid=$tid";
                //echo "<br>uid=$uid";

                setUserTestTeam($uid, $tid);
                
                header("Location: "  . URL . "/testsolveteams.php");
                exit(0);
        }

        if (isset($_POST['setPuzzleTestTeam'])) {
                $tid = $_POST['tid'];
                $pid = $_POST['pid'];
                $notfrompuzz = $_POST['notfrompuzzle'];

                //echo "tid=$tid";
                //echo "<br>uid=$uid";

                setPuzzleTestTeam($pid, $tid);

                if ($notfrompuzz == "YES") {
                        header("Location: " .  URL  . "/testsolveteams.php");  
                } else {
                        header("Location: "  . URL . "/puzzle.php?pid=$pid");
                }
                exit(0);
        }
//-------------------------------------


        head();
        echo 'An unknown error seems to have occurred. <br />';
	echo 'Please try again, or contact <a href="mailto:';
        echo HELP_EMAIL;
        echo '\">the Server Administrators</a> for help. <br />';

        foot();


?>
