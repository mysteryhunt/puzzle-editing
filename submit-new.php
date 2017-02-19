<?php // vim:set ts=4 sw=4 sts=4 et:
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

    $purifier = getHtmlPurifier();
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

    $sql = sprintf("INSERT INTO puzzles (title, summary, description, needed_editors) VALUES ('%s', '%s', '%s', '%s')",
        mysql_real_escape_string($cleanTitle),
        mysql_real_escape_string($cleanSummary),
        mysql_real_escape_string($cleanDescription),
        mysql_real_escape_string(MIN_EDITORS));
    query_db($sql);

    $sql = "SELECT LAST_INSERT_ID()";
    $id = get_element($sql);

    $sql = sprintf("INSERT INTO author_links (pid, uid) VALUES ('%s', '%s')",
        mysql_real_escape_string($id),
        mysql_real_escape_string($uid));
    query_db($sql);

    addSpoiledUserQuietly($uid, $id);

    foreach ($coauthors as $author) {
        if ($author != $uid) {
            $sql = sprintf("INSERT INTO author_links (pid, uid) VALUES ('%s', '%s')",
                mysql_real_escape_string($id),
                mysql_real_escape_string($author));
            query_db($sql);
            addSpoiledUserQuietly($author, $id);
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

function newIdeaForm($uid, $summary = '', $description = '') {
?>
    <h2>Puzzle Idea Submission</h2>

    <p><strong>Because running the Mystery Hunt and publishing the Hunt archive
    effectively constitute redistributions of your copyrighted content, we need
    to make sure we have the rights to do so. Authors will generally retain
    creative control over their puzzles.</strong></p>

    <p><strong>By submitting any puzzle-related content to Puzzletron, you
    agree to grant <?php print LICENSEE_NAME ?> a perpetual, irrevocable,
    non-exclusive, worldwide license to publish, modify, adapt, or relicense the
    Content in any form.</strong></p>

    <form method="post" action="submit-new.php">
        <p> Puzzle Title (NO SPOILERS):</p>
        <input type='text' name='title' maxlength='255' class="longin" />
        <p style='padding-top:1em;'>Puzzle Summary:

        <p>The summary should be a non-spoilery
        description of the puzzle in a few words. We
        suggest a description of what the solver will
        see upon first opening the puzzle or a few
        general keywords describing the puzzle
        type. Examples:</p>
        <ul>
        <li><a
        href="http://web.mit.edu/puzzle/www/2014/puzzle/blast_from_the_past/">BLAST
        from the Past</a>: "DNA sequences"</li>

        <li><a
        href="http://web.mit.edu/puzzle/www/2014/puzzle/stalk_us_maybe/">Stalk
        Us Maybe</a>: "Profiles of team members"</li>

        </ul>

        <p>If you're not quite sure what your puzzle
        will look like, giving general keywords is
        also okay. Possible examples: "square dancing
        puzzle", "NPL flats", "puzzle where you wander
        around MIT"</p>

        <p>It will <strong>occasionally</strong> be
        appropriate to put spoilers in this field, if
        your puzzle is about something significantly
        different than it first appears. Possible
        examples:</p>

        <ul>
        <li><a
        href="http://web.mit.edu/puzzle/www/2014/puzzle/i_came_across_a_japanese_rose_garden/">I
        Came Across a Japanese Rose Garden</a>: A
        summary like "Drawings cluing nail polish
        colors" could be appropriate although this is
        a minor spoiler.
        <li><a
        href="http://web.mit.edu/puzzle/www/2007/puzzles/war_dances/">War
        Dances</a>: "Videos of the author performing
        dances from World of Warcraft" would be
        appropriate.
        </ul>

        <p>If you're uncertain, <strong>err on the
        side of leaving spoilers out of the
        summary</strong>.</p>

        <input type="text" name="summary" maxlength="255" class="longin" value="<?php echo $summary; ?>" />
        <p style='padding-top:1em;'>Puzzle description:</p>

        <p>Please put as much detail about how your
        puzzle works as you have. Often examples of
        how puzzle mechanisms would work is
        helpful. You can use basic HTML in this
        field. Spoilers are fine.</p>

        <textarea style="width:50em; height: 25em;" name="description"><?php echo $description; ?></textarea>
        <p style='padding-top:1em;'>Select coauthors:</p>
<?php
$authors = getAvailableAuthorsForPuzzle(FALSE);
if ($authors != NULL) {
    makeOptionElements($authors, 'coauthor');
}
?>
        <p style='padding-top:1em;'>
        <input type="submit" name="newIdea" value="Submit Idea" class="okSubmit" />
        </p>
    </form>
<?php
}
