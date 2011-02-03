<?php

	require_once "config.php";
	require_once "html.php";
	require_once "db-func.php";
	require_once "utils.php";

	// Redirect to the login page, if not logged in
	$uid = isLoggedIn();
	
	// Start HTML
	head("home");

	$hunt=mktime(12,17,00,1,14,2011);
	$now = time();
	$tth=$hunt-$now;
	$days=floor($tth/(60 * 60 * 24));
	$hrs=floor($tth/(60 * 60))-(24*$days);
	$mins=floor($tth/(60))-(24*60*$days)-(60*$hrs);

	echo "<h2>Latest Updates:</h2>\n";
		
	// Display index page
	// Put messages to the team here (separate for blind and non-blind solvers?)
?>
<div class="team-updates">
<b>Friday, January 7:</b><br/>
<br>One week left! There&#39;s still lots to do. Here&#39;s how you can help:<br><br><b>Testsolving</b>:
The Good Number (# of puzzle that could go into hunt now) is currently
104, which is exactly the number of puzzles that will be in hunt. So
yay! We have a hunt! We do want to get a few more puzzles solved, so
that we have some back-ups, and can use the best possible versions. An
email will go out by tomorrow morning with a list of puzzles that we&#39;d still
like to have solved and priority among them. If you&#39;re not in the Boston
area, testsolving is the best way for you to be useful.<br>
<br><b>If you are in the Boston area</b>: Please come to Andrew and Kate&#39;s house as much as you can (1 Glenbrook Ln, Arlington. Accessible via the 77 bus). Work will start there tomorrow morning and run all day everyday until hunt. Show up any time and there will be things for you to do. In addition, here is a schedule for some specific events in the last week:<br>

<br>Saturday 2:30pm EST: load testing (organized by Cindy)<br>Saturday 4pm: Test run of Insult Swordfighting Event (at Kate and Andrew&#39;s)<br>Thursday around 5pm: Load stuff into our HQ (if you have a car and can help move stuff, let us know)<br>
Thursday 7pm: Final all-hands meeting. At our HQ (34-3xx), with pizza.<br>Friday 11am: Start getting ready for kickoff<br>Friday 12:17pm: Kickoff!<br><br><b>Things we still need to finish:</b><br>- Copy editing (email Andrew if you have time to do this)<br>
- Final blind testing<br>- Kickoff music (if you can play the violin, email Cat)<br>- Finish planning kickoff<br>- Finish planning endgame<br>- Make Tetrahedronforces<br>- Put together white powders for Powder Monkey<br>

- Put together stuff for Redundant Obsolescence<br>
- Art (go Allen go!)<br>- Production server (go Alex go!)<b><br></b><br>This will be the last regularly scheduled update. I hope you&#39;ve enjoyed them. From all of us in the council, thanks for the monumental amount of work that you&#39;ve put in over the last year. Let&#39;s bring this thing in for a landing and put on a hunt for the ages.<br>
</div>


<h3 style="padding-bottom:0.5em;"><a href="updates.php">Past Updates</a></h3>

<?	// End HTML
	foot();
?>
