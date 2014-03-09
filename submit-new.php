<?php
        require_once "config.php";
        require_once "html.php";
        require_once "db-func.php";
        require_once "utils.php";

        // Redirect to the login page, if not logged in
        $uid = isLoggedIn();


        // Start HTML
        head();

        // Check if form was submitted
        if (isset($_POST) && isset($_POST['newIdea'])) {
                $title = $_POST['title'];
                $summary = $_POST['summary'];
                $description = $_POST['description'];
                if (isset($_POST['coauthor'])) {
                        $coauthors = $_POST['coauthor'];
                } else {
                        $coauthors = array();
                }

                $purifier = new HTMLPurifier();
                $cleanTitle = htmlspecialchars($title); // I don't think we need or want HTML in titles... do we?
                $cleanSummary = $purifier->purify($summary);
                $cleanDescription = $purifier->purify($description);

                if ($summary == '' || $description == '') {
                        echo "<div class='errormsg'>You must enter a summary and a description</div>";
                        newIdeaForm($uid, $summary, $description);
                        foot();
                        exit(1);
                }

                $time = time();

                mysql_query('START TRANSACTION');

                $sql = sprintf("INSERT INTO puzzle_idea (title, summary, description, needed_editors) VALUES ('%s', '%s', '%s', '%s')",
                                mysql_real_escape_string($cleanTitle),
                                mysql_real_escape_string($cleanSummary),
                                mysql_real_escape_string($cleanDescription),
                                mysql_real_escape_string(MIN_EDITORS));
                query_db($sql);

                $sql = sprintf("SELECT id FROM puzzle_idea WHERE summary='%s' AND description='%s'",
                                mysql_real_escape_string($cleanSummary), mysql_real_escape_string($cleanDescription));
                $id = get_element($sql);

                $sql = sprintf("INSERT INTO authors (pid, uid) VALUES ('%s', '%s')",
                                mysql_real_escape_string($id),
                                mysql_real_escape_string($uid));
                query_db($sql);

                foreach ($coauthors as $author) {
                        if ($author != $uid) {
                                $sql = sprintf("INSERT INTO authors (pid, uid) VALUES ('%s', '%s')",
                                                mysql_real_escape_string($id),
                                                mysql_real_escape_string($author));
                                query_db($sql);
                        }
                }

                mysql_query('COMMIT');

                // Subscribe authors and coauthors to comments on their own puzzles
                subscribe($uid, $id);
                foreach ($coauthors as $auth) {
                        if ($auth != $uid) {
                                subscribe($auth, $id);
                        }
                }

                echo "<div class='okmsg'>Idea Submitted</div>";
        } else {
                newIdeaForm($uid);
        }

        // End HTML
        foot();
//------------------------------------------------------------------------

        function newIdeaForm($uid, $summary = '', $description = '')
        {
?>
                <h2>Puzzle Idea Submission</h2>

                <p>So that we can later publish the hunt as a book or as a
                website, we need to obtain rights to all puzzles that the team
                writes.  When so released, they will probably be licensed under
                a copyleft license. Authors will generally retain creative
                control over their puzzles.</p>

                <p>By submitting any puzzle-related content to Puzzletron (the
                “Content”), you agree to grant Adam Rosenfield and Iolanthe
                Chronis the sole and perpetual right to publish, modify, adapt,
                or relicense the Content in any form.</p>

                <form method="post" action="submit-new.php">
                        <p> Puzzle Title (NO SPOILERS):</p>
                        <input type='text' name='title' maxlength='255' class="longin" />
                        <p style='padding-top:1em;'>Puzzle Summary (a description in a few words; MINIMAL SPOILERS): </p>
                        <input type="text" name="summary" maxlength="255" class="longin" value="<?php echo $summary; ?>" />
                        <p style='padding-top:1em;'>Puzzle description (basic HTML encouraged; spoilers ok):</p>
                        <textarea style="width:50em; height: 25em;" name="description"><?php echo $description; ?></textarea>
                        <p style='padding-top:1em;'>Select coauthors:</p>
<?php
                $authors = getAvailableAuthorsForPuzzle(FALSE);
                if ($authors != NULL)
                        makeOptionElements($authors, 'coauthor');
?>
                        <p style='padding-top:1em;'>
                        <input type="submit" name="newIdea" value="Submit Idea" class="okSubmit" />
                        </p>
                </form>
<?php
        }
?>
