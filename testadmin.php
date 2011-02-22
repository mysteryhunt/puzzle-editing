<?php

	require_once "config.php";
	require_once "html.php";
	require_once "db-func.php";
	require_once "utils.php";

	// Redirect to the login page, if not logged in
	$uid = isLoggedIn();
		
	// Start HTML
	head("testadmin");
		
	// Check for permissions
	if (!isTestingAdmin($uid) && !isLurker($uid)) {
		echo "Sorry, you're not a testing admin.";
		foot();
		exit(1);
	}
	
	echo "<h1>Test Queue</h1>";
	
	$inTesting = count(getPuzzlesInTesting());
	$numNeedAdmin = count(getPuzzlesNeedTestAdmin());
	
	echo "<h3>There are currently $inTesting puzzles in testing</h3>";
	echo "<h3>$numNeedAdmin puzzles need a testing admin</h3>";
	echo "<br />";
	
	if (isTestingAdmin($uid)) {
	
		if (getPuzzleForTestAdminQueue($uid) == FALSE) {
			echo '<strong>No Puzzles To Add</strong>';
		} else {
?>
				<form action="form-submit.php" method="post">
					<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
					<input type="submit" name="getTestAdminPuzz" value="Get Puzzle" />
				</form>
<?php
		}
		
		displayTestQueue($uid);
	}
	
	echo "<h1>Testing Summary</h1>";	
	displayTestingSummary();
	
	// End HTML
	foot();
	
//------------------------------------------------------------------------
function displayTestQueue($uid)
{
	$puzzles = getInTestAdminQueue($uid);
	
	$puzzles = sortByLastCommentDate($puzzles);

	if ($puzzles == NULL) {
		echo '<h3>No Puzzles Currently In Queue</h3>';
	} else {
		displayQueue($uid, $puzzles, TRUE, TRUE, TRUE, FALSE, FALSE, FALSE, FALSE, FALSE, TRUE);
	}
}
	
function displayTestingSummary()
{
	$sql = sprintf("SELECT uid, pid from test_queue");
	$result = get_rows_null($sql);
	
	if ($result == NULL)
		return;

	echo '<style type="text/css">';
	echo '.testingtable, .testingtable a { color: #999999; }';
	echo '.testingtable .name, .testingtable .current, .testingtable .past { color: #000000; font-weight: bold; }';
	
	$currqueue = NULL;
	foreach($result as $r) {
		$currclass = NULL;
		$uid = $r['uid'];
		$pid = $r['pid'];
		
		if (isset($currqueue[$uid]))
			$currqueue[$uid] .= "$pid ";
		else
			$currqueue[$uid] = "$pid ";
		$currclass = ".a$uid-$pid";
		echo ".testingtable $currclass, .testingtable $currclass a {color: #000000; }\n";
	}
	echo "</style>\n";

	$sql = sprintf("SELECT user_info.uid, user_info.username, comments.id, type, timestamp, pid FROM comments
			LEFT JOIN user_info on comments.uid = user_info.uid WHERE comments.type = 5 
			ORDER BY user_info.username, comments.pid");
	$result = query_db($sql);
	$r = mysql_fetch_assoc($result);
	
	$arr = NULL;
	while ($r) {
		$uid = $r['uid'];
		$pid = $r['pid'];
		$id = $r['id'];
		$timestamp = $r['timestamp'];
		$name = getUserName($uid);
	      
		$arr[$uid] = "<tr><td class='name'>$name</td><td>";
		$arr[$uid] .= '<span class="current">Current queue: ' . print_r($currqueue[$uid], true) . '</span><br />';
		$arr[$uid] .= '<span class="past">Past comments: </span><br />';
		$arr[$uid] .= "<span class='a$uid-$pid'>";
		$puzzlink = URL . "/puzzle?pid=$pid";
		$arr[$uid] .= "<a href='$puzzlink'>$pid</a>: ";
		$arr[$uid] .= "<a href='$puzzlink#comm$id'>$timestamp</a> &nbsp; ";

		$r = mysql_fetch_assoc($result);
		while ($r) {
			if ($uid != $r['uid']) {
				$arr[$uid] .= "</td></tr>";
				break;
		    }
		    if ($pid != $r['pid']) {
				$pid = $r['pid'];
			    $arr[$uid] .= "</span><br />\n" . "<span class=\"a" . $r['uid'] . "-" . $pid . "\">";
			    $puzzlink = "https://ihtfp.us/editing/puzzle?pid=" . $pid;
			    $arr[$uid] .= "<a href=\"" . $puzzlink . "\">" . $pid . "</a>: ";
		    }
		    $arr[$uid] .= "<a href=\"" . $puzzlink . "#comm" . $r['id'] . "\">" . $r['timestamp'] . "</a> &nbsp; ";
		    $r = mysql_fetch_assoc($result);
	      }
	}

	echo "<table class=\"testingtable\">\n";
	foreach ($arr as $key => $value) {
		echo $value . "\n";
	}
	echo "</table>\n\n";
}
?>
