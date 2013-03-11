<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();

        header("Content-type: text/json");
        $puzzles = getPuzzlesInPostprodAndLater($uid);
        $exportdata = array();
        foreach ($puzzles as $pid) {
                # pid, status, title, slug, round name, round slug.
                $status = getStatusNameForPuzzle($pid);
                $title = getTitle($pid);
                $titleslug = postprodCanon($title);
                $rinfo = getRoundForPuzzle($pid);
                $answer = getAnswersForPuzzleAsList($pid);
                if ($rinfo) {
                  $roundname = $rinfo['name'];
                  $roundslug = postprodCanonRound($roundname);
                  $exportdata[] = array('url' => "/$roundslug/$titleslug/", 'pid' => $pid, 'status' => $status, 'title' => $title, 'titleslug' => $titleslug, 'round' => $roundname, 'roundslug' => $roundslug, 'answer' => $answer);
                }
        }
        print json_encode($exportdata) . "\n";
?>
