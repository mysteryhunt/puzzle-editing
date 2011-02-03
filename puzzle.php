<?php
/*
 * To Do Before Pushing
 * 		create jobs table
 * 		turn back on sendEmail
 */
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
	if (!isAuthorOnPuzzle($uid, $pid) && !isEditorOnPuzzle($uid, $pid) && !isTestingAdminOnPuzzle($uid, $pid) && !isLurkerOnPuzzle($uid, $pid)) {
		echo "You do not have permission to view this puzzle.";
		foot();
		exit(0);
	}
	
	// Get & update user's last visit to this puzzle's page
	$lastVisit = updateLastVisit($uid, $pid);

	// If author is a blind tester, turn page background red
	if (isAnyAuthorBlind($pid)) {
	        echo '<style type="text/css">html {background-color: #B00000;}</style>';
	}
/*	
	// Link to first unread comment
	if (getLastCommentDate($pid) > $lastVisit)
		echo '<p style="padding-bottom: 1em;"><a href="#firstUnread">Go To First Unread Comment</a></p>';
*/	
	// Hide puzzle info from testing admins, to prevent spoilage
	$hidePuzzleInfo = (isTestingAdmin($uid) && !isAuthorOnPuzzle($uid, $pid) && !isEditorOnPuzzle($uid, $pid));
	
	// If Testing Admin, hide answer, summary, and description
	if ($hidePuzzleInfo) {
		echo '<style type="text/css">.hideFromTest {display: none;}</style>';
	}
	
	// Get all information about the puzzle (from puzzle_idea table)
	$puzzleInfo = getPuzzleInfo($pid);
		
	// Edit the summary and description? (Only authors and lurkers)
	if (canEditTSD($uid, $pid) && isset($_GET['edit'])) {
		editTitleSummaryDescription($uid, $pid,
				$puzzleInfo['title'], $puzzleInfo['summary'], $puzzleInfo['description']);
		foot();
		exit(0);
	}
	
	if (isLurkerOnPuzzle($uid, $pid) && isTesterOnPuzzle($uid, $pid)) {
		echo '<style type="text/css">.hideFromLurkerTester {display: none;}</style>';
		echo "<p id='showPageLine'>You are testsolving Puzzle $pid. <a href='#' id='showPage'>Show Page Anyway</a></p>";
	}
	
	echo "<div class='hideFromLurkerTester'>";
	echo "<div class='puzzleInfo'>";
	
	// Display puzzle number, title, answer, summary, description.
	displayPuzzleInfo($uid, $pid, $puzzleInfo);
	
	// Allow testing admins to display the hidden puzzle info
	if ($hidePuzzleInfo) {
		echo '<a href="#" id="showTestLink">Show Answer, Summary, and Description</a>';
		echo '<a href="#" id="hideTestLink" class="hideFromTest">Hide Answer, Summary, and Description</a>';
	}
	
	// Allow author and lurkers to edit summary and description
	if (canEditTSD($uid, $pid)) {
		echo '<p style="font-size:75%;"><a href="' . URL . "/puzzle?edit&pid=$pid" . '">';
		echo 'Edit Title, Summary and Description</a></p>';
	}

	echo "</div>";
	
	// List various people working on the puzzle
	echo "<div class='peopleInfo'>";
	displayPeople($uid, $pid);
	echo "</div>";
	
	// List puzzle status
	echo "<div class='statusInfo'>";
	displayStatus($uid, $pid);
	echo "</div>";
	
	// Show files
	echo "<div class='fileInfo'>";
	displayFiles($uid, $pid);
	echo "</div>";

	// Add post-prod link
	if (getStatusForPuzzle($pid) == 13) {
		echo "<br />";
		echo "<div class='postProd'>";
		displayPostProd($uid, $pid);
		echo "</div>";
	}
	
	
	// Display & add comments
	echo "<div class='comments'>";
	echo "<table>";
	displayComments($uid, $pid, $lastVisit);
	addCommentForm($uid, $pid);
	emailSubButton($uid, $pid);
	echo "</table>";
	echo "</div>";
	
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
				<h1 style="margin: 0em;"><?php echo "Puzzle $pid: $title"; ?></h1>
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
	$availableAnswers = getAvailableAnswersForUser($uid);
?>
		<tr class='hideFromTest'>
			<td>
				<strong>Answers: <?php echo getAnswersForPuzzleAsList($pid); ?></strong>
<?php 
	if (canChangeAnswers($uid, $pid) && ($currentAnswers != NULL || $availableAnswers != NULL)) { 
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
							<?php echo displayRemoveAns($pid); ?>
						</td>
<?php 
		}
		if ($availableAnswers != NULL) {
?>
						<td>
							<p><strong>Add Answer(s):</strong></p>
							<?php echo displayAddAns($uid, $pid); ?>
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

function displayRemoveAns($pid)
{
	$answers = getAnswersForPuzzle($pid);
	if ($answers != NULL)
		makeOptionElements($answers, 'removeAns');
}

function displayAddAns($uid, $pid)
{
	$answers = getAvailableAnswersForUser($uid);
	if ($answers != NULL)
		makeOptionElements($answers, 'addAns');
}

function editTitleSummaryDescription($uid, $pid, $title, $summary, $description)
{
?>
	<form method="post" action="form-submit.php">
		<h1>Puzzle <?php echo $pid; ?></h1>		
		<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
		<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
		<p style="padding-top: 0.5em;">Title: <input type="text" name="title" maxlength="255" style="width:30em;" value="<?php echo $title; ?>" /></p>
		<p style="padding-top: 0.5em;">Summary: <input type="text" name="summary" maxlength="255" style="width:40em;" value="<?php echo $summary; ?>" /></p>
		<p style="padding-top: 0.5em;">Description:</p>
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
	<?php displayEditors($uid, $pid); ?>
	<?php displayBlocked($uid, $pid); ?>
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
				<strong>Authors:</strong> <?php echo getAuthorsAsList($pid); ?>
				<?php if (canAddAuthor($uid, $pid) || canRemoveAuthor($uid, $pid))
					echo '&nbsp;&nbsp;<a href="#" class="changeLink">[Change]</a>';
				?>
			</td>
		</tr>
<?php
	if (canAddAuthor($uid, $pid) || canRemoveAuthor($uid, $pid)) {
?>
		<tr>
			<td>
				<table>
					<form method="post" action="form-submit.php">
					<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
					<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
					<tr>
<?php
		if (canRemoveAuthor($uid, $pid)) {
?>
						<td>
							<p><strong>Remove Author(s):</strong></p>
							<?php echo displayRemoveAuthor($pid); ?>
						</td>
<?php 
		}
		if (canAddAuthor($uid, $pid)) {
?>
						<td>
							<p><strong>Add Author(s):</strong></p>
							<?php echo displayAddAuthor($pid); ?>
						</td>
<?php
		} 
?>
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
				<strong>Spoiled:</strong> <?php echo getSpoiledAsList($pid); ?>
				<?php if (canAddSpoiled($uid, $pid) || canRemoveSpoiled($uid, $pid))
					echo '&nbsp;&nbsp;<a href="#" class="changeLink">[Change]</a>';
				?>
			</td>
		</tr>
<?php
	if (canAddSpoiled($uid, $pid) || canRemoveSpoiled($uid, $pid)) {
?>
		<tr>
			<td>
				<table>
					<form method="post" action="form-submit.php">
					<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
					<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
					<tr>					
<?php
		if (canRemoveSpoiled($uid, $pid)) {
?>
						<td>
							<p><strong>Remove Spoiled:</strong></p>
							<?php echo displayRemoveSpoiledUsers($pid); ?>
							<?php echo displayRemoveSpoiledLists($pid); ?>
						</td>
<?php 
		}
		if (canAddSpoiled($uid, $pid)) {
?>
						<td>
							<p><strong>Add Spoiled:</strong></p>
							<?php echo displayAddSpoiledUsers($pid); ?>
							<?php echo displayAddSpoiledLists($pid); ?>
						</td>
<?php
		} 
?>
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
}

function displayRemoveSpoiledUsers($pid)
{
	$spoiled = getSpoiledUsersForPuzzle($pid);
	if ($spoiled != NULL)
		makeOptionElements($spoiled, 'removeSpoiledUser');	
}

function displayRemoveSpoiledLists($pid)
{
	$spoiled = getSpoiledListsForPuzzle($pid);
	if ($spoiled != NULL)
		makeOptionElements($spoiled, 'removeSpoiledList');	
}

function displayAddSpoiledUsers($pid)
{
	$spoiled = getAvailableSpoiledUsersForPuzzle($pid);
	if ($spoiled != NULL)
		makeOptionElements($spoiled, 'addSpoiledUser');
}

function displayAddSpoiledLists($pid)
{
	$spoiled = getAvailableSpoiledListsForPuzzle($pid);
	if ($spoiled != NULL)
		makeOptionElements($spoiled, 'addSpoiledList');
}

function displayEditors($uid, $pid)
{
?>
		<tr>
			<td class='peopleInfo'>
				<strong>Editors:</strong> <?php echo getEditorsAsList($pid); ?>
				<?php if (canAddOncallEditor($uid, $pid) || canRemoveOncallEditor($uid, $pid) ||
							canAddEditor($uid, $pid) || canRemoveEditor($uid, $pid))
					echo '&nbsp;&nbsp;<a href="#" class="changeLink">[Change]</a>';
				?>
			</td>
		</tr>
<?php
	if (canAddOncallEditor($uid, $pid) || canRemoveOncallEditor($uid, $pid) ||
		canAddEditor($uid, $pid) || canRemoveEditor($uid, $pid)) {
?>
		<tr>
			<td>
				<table>
					<form method="post" action="form-submit.php">
					<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
					<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
					<tr>
<?php
		if (canRemoveOncallEditor($uid, $pid)) {
?>
						<td>
							<p><strong>Remove On-Call Editor(s):</strong></p>
							<?php echo displayRemoveOncallEditor($pid); ?>
						</td>
<?php 
		}
		if (canAddOncallEditor($uid, $pid)) {
?>
						<td>
							<p><strong>Add On-Call Editors(s):</strong></p>
							<?php echo displayAddOncallEditor($pid); ?>
						</td>
<?php 
		}
?>
					</tr>
					<tr>
<?php
		if (canRemoveEditor($uid, $pid)) {
?>
						<td>
							<p><strong>Remove Editor(s):</strong></p>
							<?php echo displayRemoveEditor($pid)?>
						</td>
<?php 
		}
		if (canAddEditor($uid, $pid)) {
?>
						<td>
							<p><strong>Add Editor(s):</strong></p>
							<?php echo displayAddEditor($pid); ?>
						</td>
<?php 
		}
?>
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
}

function displayRemoveOncallEditor($pid)
{
	$editors = getOncallEditorsForPuzzle($pid);
	if ($editors != NULL)
		makeOptionElements($editors, 'removeOncallEd');	
}

function displayAddOncallEditor($pid)
{
	$editors = getAvailableOncallEditorsForPuzzle($pid);
	if ($editors != NULL)
		makeOptionElements($editors, 'addOncallEd');	
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

function displayBlocked($uid, $pid)
{
?>
		<tr>
			<td class='peopleInfo'>
				<strong>Blocked:</strong> <?php echo getBlockedAsList($pid); ?>
				<?php if (canAddBlocked($uid, $pid) || canRemoveBlocked($uid, $pid))
					echo '&nbsp;&nbsp;<a href="#" class="changeLink">[Change]</a>';
				?>
			</td>
		</tr>
<?php
	if (canAddBlocked($uid, $pid) || canRemoveBlocked($uid, $pid)) {
?>
		<tr>
			<td>
				<table>
					<form method="post" action="form-submit.php">
					<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
					<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
					<tr>					
<?php
		if (canRemoveBlocked($uid, $pid)) {
?>
						<td>
							<p><strong>Remove Blocked:</strong></p>
							<?php echo displayRemoveBlocked($pid); ?>
						</td>
<?php 
		}
		if (canAddBlocked($uid, $pid)) {
?>
						<td>
							<p><strong>Add Blocked:</strong></p>
							<?php echo displayAddBlocked($pid); ?>
						</td>
<?php
		} 
?>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" name="changeBlocked" value="Change Blocked" />
						</td>
					</tr>
					</form>
				</table>
			</td>
		</tr>
<?php		
	}
}

function displayRemoveBlocked($pid)
{
	$blocked = getBlockedForPuzzle($pid);
	if ($blocked != NULL)
		makeOptionElements($blocked, 'removeBlocked');	
}

function displayAddBlocked($pid)
{
	$blocked = getAvailableBlockedForPuzzle($pid);
	if ($blocked != NULL)
		makeOptionElements($blocked, 'addBlocked');	
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
<?php 
	if (canChangeStatus($uid, $pid)) { 
?>
			<td class='statusInfo'>
				<a href="#" class="changeLink">[Change]</a>
			</td>
		</tr>
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
	} else {
?>
		</tr>
<?php 
	}
?>
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
				<?php echo "<strong>Lastest $type:</strong>"; ?>
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
			
		if ($first && canUploadFiles($uid, $pid) && !($type == 'draft' && !canAcceptDrafts($pid))) {
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

function displayPostProd($uid, $pid)
{
	$postProd = getPostProdForPuzzle($pid);
	
?>
	<form action="form-submit.php" method="post">
		<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
		<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
		<strong>Post-Production Link: </strong>
		<input type="text" name="postProd" value="<?php echo $postProd; ?>" style="width: 20em;" />
		<input type="submit" name="changePostProd" value="Change" />
	</form>
<?php
}

function displayComments($uid, $pid, $lastVisit)
{
	$comments = getComments($pid);
	if ($comments == NULL)
		return;
	
	$unread = FALSE;
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
			if ($unread == FALSE) {
				echo '<a name="firstUnread"></a>';
				$unread = TRUE;
			}
			echo "<tr class='comment-new' id='comm$id'>";
		} else {
			echo "<tr class='comment' id='comm$id'>";	
		}
			
		echo "<td class='$type" . "Comment'>";		
		
		if ($type == 'Testsolver') {
			if (canSeeTesters($uid, $pid)) {
				echo $name . '<br />';	
			}	
			echo 'Testsolver '.substr(md5(strval($pid).$comment['first'].$comment['last']),0,8);
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
