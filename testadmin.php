<?php // vim:set ts=4 sw=4 sts=4 et:
require_once "config.php";
require_once "html.php";
require_once "db-func.php";
require_once "utils.php";

// Redirect to the login page, if not logged in
$uid = isLoggedIn();

// Start HTML
head("testadmin", "Test Admin");

// Check for permissions
if (!hasTestAdminPermission($uid)) {
    echo "<div class='errormsg'>Sorry, you're not a testing admin.</div>";
    foot();
    exit(1);
}

echo "<h2>Test Queue</h2>";

?>
    <form action="form-submit.php" method="post">
        <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
        Enter Puzzle ID to testadmin: <input type="text" name="pid" />
        <input type="submit" name="TestAdminPuzzle" value="Go" />
    </form>

    <br />
<?php
$inTesting = count(getPuzzlesInTesting());
$numNeedAdmin = count(getPuzzlesNeedTestAdmin());

echo "There are currently <strong>$inTesting puzzles</strong> in testing<br/>";
echo "<strong>$numNeedAdmin puzzles</strong> need a testing admin</strong>";
echo "<br /><br />";

if (hasTestAdminPermission($uid)) {

    if (getPuzzleForTestAdminQueue($uid) == FALSE) {
        echo '<div class="emptylist">No Puzzles To Add</div><br/>';
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

echo "<h2>Puzzles needing testadmin</h2>";
$testPuzzles = getPuzzlesNeedTestAdmin();

// Should we show "testers" identities? No,
// but we've gotten lazy about using the testadmin system, which means
// I need to see testers for puzzles that have no testadmin.
displayQueue($uid, $testPuzzles, "notes summary testers", FALSE);

echo "<h2>Testing Feed</h2>";
echo "<table>";
echo "<div class='comments'>";
displayTestingFeed();
echo "</div>";
echo "</table>";

echo "<h2>Testing Summary</h2>";
displayTestingSummary();

// End HTML
foot();

//------------------------------------------------------------------------
function displayTestQueue($uid) {
    $puzzles = getInTestAdminQueue($uid);

    $puzzles = sortByLastCommentDate($puzzles);

    if (!$puzzles) {
        echo '<h3>No Puzzles Currently In Queue</h3>';
    } else {
        displayQueue($uid, $puzzles, "notes answer summary testers currentpuzzletestercount", FALSE);
    }
}

function displayTestingSummary() {
    $sql = sprintf("SELECT uid, pid from tester_links");
    $result = get_rows($sql);

    if (!$result) {
        echo "<span class='emptylist'>No puzzles in test queue</span>";
        return;
    }

    echo '<style type="text/css">';
    echo '.testingtable, .testingtable a { color: #999999; }';
    echo '.testingtable .name, .testingtable .current, .testingtable .past { color: #000000; font-weight: bold; }';

    $currqueue = array();
    foreach ($result as $r) {
        $currclass = NULL;
        $uid = $r['uid'];
        $pid = $r['pid'];

        if (isset($currqueue[$uid])) {
            $currqueue[$uid] .= "$pid ";
        } else {
            $currqueue[$uid] = "$pid ";
        }
        $currclass = ".a$uid-$pid";
        echo ".testingtable $currclass, .testingtable $currclass a {color: #000000; }\n";
    }
    echo "</style>\n";

    $sql = sprintf("SELECT users.uid, users.username, comments.id, type, timestamp, pid FROM comments
        LEFT JOIN users on comments.uid = users.uid WHERE comments.type = 5
        ORDER BY users.username, comments.pid");
    $result = query_db($sql);
    $r = mysql_fetch_assoc($result);

    $arr = array();
    while ($r) {
        $uid = $r['uid'];
        $pid = $r['pid'];
        $id = $r['id'];
        $timestamp = $r['timestamp'];
        $name = getUserName($uid);

        $arr[$uid] = "<tr><td class='name'>$name</td><td>";
        if (!isset($currqueue[$uid])) {
            $currqueue[$uid] = "";
        }
        $arr[$uid] .= '<span class="current">Current queue: ' . print_r($currqueue[$uid], true) . '</span><br />';
        $arr[$uid] .= '<span class="past">Past comments: </span><br />';
        $arr[$uid] .= "<span class='a$uid-$pid'>";
        $puzzlink = URL . "/puzzle.php?pid=$pid";
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
                $puzzlink = URL . "/puzzle.php?pid=" . $pid;
                $arr[$uid] .= "<a href=\"" . $puzzlink . "\">" . $pid . "</a>: ";
            }
            $arr[$uid] .= "<a href=\"" . $puzzlink . "#comm" . $r['id'] . "\">" . $r['timestamp'] . "</a> &nbsp; ";
            $r = mysql_fetch_assoc($result);
        }
    }
    if (!$arr) {
        echo "<div class='emptylist'>No comments</div>";
    }
    echo "<table class=\"testingtable\">\n";
    foreach ($arr as $key => $value) {
        echo $value . "\n";
    }
    echo "</table>\n\n";
}
