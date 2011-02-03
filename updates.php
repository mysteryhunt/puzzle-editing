<?php

	require_once "config.php";
	require_once "html.php";
	require_once "db-func.php";
	require_once "utils.php";

	// Redirect to the login page, if not logged in
	isLoggedIn();
	
	// Start HTML
	head("home");

	$hunt=mktime(12,17,00,1,17,2011);
	$now = time();
	$tth=$hunt-$now;
	$days=floor($tth/(60 * 60 * 24));
	$hrs=floor($tth/(60 * 60))-(24*$days);
	$mins=floor($tth/(60))-(24*60*$days)-(60*$hrs);

	echo "<h2>All Updates:</h2>\n";
		
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


<div class="team-updates">
<b>January 1, 2011:</b><br/>
Happy New Year!<br />
<br />
Welcome to 2011, which as we all know, is the year we're putting on a<br />
Mystery Hunt! &nbsp;We're down to 14 days, so it's time to buckle down for<br />
one last push through the finish line.<br />
<br />

We currently have 100 puzzles that we'd feel comfortable putting into<br />
hunt as is. &nbsp;This is up 4 from last week. &nbsp;We only need 4 more to have<br />
a complete hunt. &nbsp;But we'd really like a few more solves on top of<br />
that in order to put together the best hunt that we can. &nbsp;So let's<br />
make it a goal to get half a dozen more solves in the next week. &nbsp;If<br />
you only have time to solve one puzzle, that's still a huge part of<br />

the way to finishing hunt.<br />
<br />
So what can you do to make sure we're ready for hunt:<br />
1) Our top priority is testsolving high and medium priority puzzles<br />
and puzzles which are targeted at you.<br />
<br />
2) There's other stuff that needs to be finished (proof-reading,<br />
endgame, plot, wrapup, etc.) &nbsp;Contact Andrew to find out how you can<br />
be most useful.<br />

<br />
3) If you have any ideas for puzzles which can take any answers you<br />
can still propose them, but only if they're puzzles that can be<br />
written quickly.<br />
<br />
Please don't work on testing low priority puzzles right now, unless<br />
you have nothing better to do.<br />
<br />
As always, email the council (<a href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>) or our<br />

ombudsperson Oliver (<a href="mailto:okosut@gmail.com">okosut@gmail.com</a>) with questions, comments, or<br />
concerns.<br />
</div>

<div class="team-updates">
<b>December 25, 2010:</b><br/>
Hunt begins 3 weeks from today (for some definitions of &quot;today&quot;).</div><div><br></div><div>We currently have 63 puzzles completed, 38 in testing (21 of those in low priority), and 5 in revision (which is 2 more than 104, the number of puzzles we need in Hunt). All together, there are 96 puzzles that are in a state that they could go into Hunt right now, which is pretty awesome. Even better, 2 of our finished puzzle can take any answer and stand ready to be swapped in for unsuccessful puzzles.</div>
<div><br></div><div>What should you be doing?</div><div>(1) In terms of testsolving, work on high and medium priority puzzles (and any targeted at you).</div><div>(2) In terms of writing, if you have any ideas for floating puzzles (ie, puzzles that could take any answer), please feel free to propose them, keeping in mind the comment above that not all puzzles will end up being used.</div>
<div>(3) In terms of all the stuff there is to do that isn&#39;t testing and writing, ask Andrew how you can be most helpful.</div><div><br></div><div>Please don&#39;t work on testing low priority puzzles right now, unless you have nothing better to do. </div>

<div><br></div><div>As always, email the council (<a href="mailto:council@puzzle2011.com" target="_blank">council@puzzle2011.com</a>) or our friendly ombudsperson Oliver (<a href="mailto:okosut@gmail.com" target="_blank">okosut@gmail.com</a>) with questions/comments/concerns/<WBR>etc.</div>

</div>



<div class="team-updates">
<b>December 19, 2010:</b><br/>
We have just 25 days until Hunt.<br>
<br>
We currently have 62 puzzles completed, 38 in testing, and 5 in revision (which is 1 more than 104, the number of puzzles we need in Hunt.  Note that because of this, some puzzles will end up having to be cut depending on the outcome of testsolving).  All together, there are 95 puzzles that are in a state that they could go into Hunt right now, which is pretty awesome.<br>
<br>
So what should everyone be working on?<br>

(1) In terms of testsolving, work on high and medium priority puzzles (and any targeted at you).<br>
(2) In terms of writing, if you have any ideas for floating puzzles (ie, puzzles that could take any answer), please feel free to propose them, keeping in mind the comment above that not all puzzles will end up being used.<br>
(3) In terms of all the stuff there is to do that isn&#39;t testing and writing, be on the lookout for an email from Andrew.  He will be requesting volunteers for some important tasks that still need doing.  Please consider volunteering for one of them, because we really need all the help we can get right now.<br>
<br>
Unless you have absolutely nothing else Hunt-related to do&mdash;i.e., you can&#39;t help <strong>at all</strong> with any of (1), (2), or (3)&mdash;please do not work on &quot;low priority&quot; puzzles (we are basically considering those puzzles as &quot;done&quot; for now).<br>

<br>
As always, email the council (<a href="mailto:council@puzzle2011.com" target="_blank">council@puzzle2011.com</a>) or our ombudsperson Oliver (<a href="mailto:okosut@gmail.com" target="_blank">okosut@gmail.com</a>) with questions/comments/concerns/<WBR>etc.<br>
</div>

<div class="team-updates">
<b>December 11, 2010:</b><br/>
34 days to go! The good news is that this means we&#39;re nearing the end. The bad news is that this means we&#39;re nearing the end.<br>
<div><img src="images/pie-20101211.png" width=500/></div>
<br>As you can see for yourself in the &quot;Puzzle Stats&quot; tab on the server, or in the pie chart above, we&#39;ve got:<br> - 56 puzzles done<br> - 35 in testing<br> - 6 in revision<br> - 7 awaiting drafts<br>However,
this slightly undersells our progress, since for a lot of the puzzles
that aren&#39;t officially done, we have some version that we&#39;d willing to put into hunt. Aaron has been compiling this &quot;Good List&quot; of puzzles
that could go into hunt today, and it currently stands at 90(!) puzzles.
But, many of these we&#39;d like to test again, so we can use the best
possible version. So please keep testsolving! Especially the
High-Priority and Medium-Priority puzzles. And write those last few
puzzles!<br>

<br>As we get down to the wire, we&#39;ve got lots of other things to finish up too:<br> - Website art (go Allen go!!)<br> - Deployment server (go Alex go!!)<br> - Coins<br> - Puzzle post-production<br> - Copy editing<br>
 - Kickoff<br> - Team interactions<br> - Costumes<br> - Events details<br> - Wedding invitations<br> - Teams registration<br> - Achievements<br><br>If
you want to help out with any of these things, email the council and
we&#39;ll direct you to the right person. This is a lot to do, so let&#39;s get
to it and make hunt as awesome as it can be.<br>

<br>As always, questions, comments, or reports on the locations of squirrel infestations should go to the council at <a href="mailto:council@puzzle2011.com" target="_blank">council@puzzle2011.com</a>, or the ombudsperson, me, at <a href="mailto:okosut@gmail.com" target="_blank">okosut@gmail.com</a>.<br>
</div>

<div class="team-updates">
<b>December 6, 2010:</b><br/>
The Pleasant Weekend of Doom was a pretty substantial success.  Thanks to all of you who attended!  Over the course of the weekend, we fully solved 17 puzzles (including two of the three-answer variety), and got 1/3 answers on another.  Also,  5 puzzles were finalized, 10 went into testing, 2 puzzles were submitted, and 1 idea was accepted.  We also finished hashing out plot details, nailed down our needs for the events, and had a great test-run of one of them.  That&#39;s awesome progress!<br>
<img src="images/pie-20101206"/>
<br>Our current breakdown is as shown.  What this graph doesn&#39;t capture is how many puzzles today have a version which would probably be OK to put into hunt in a pinch (but are still in testing for more feedback, or because they&#39;ve been edited, etc.).  Counting those puzzles with the ones that are definitively ready, our number of puzzles is 85 (82% of hunt).<br>
<br>As is apparent from the graph, we still have a fair number of puzzles which need testable drafts... and we need those soon, so there&#39;s time to test and post-produce the puzzle!  As was mentioned last week, the editors may now begin de-attaching answers from puzzles which aren&#39;t showing good progress towards getting testable drafts.  If you have a puzzle that isn&#39;t written yet, and you haven&#39;t discussed with your editors what its situation is, please log in and do that now, or that puzzles&#39; spot is likely to get given away.  Even if you have discussed with them, keep in touch; the editors may impose a deadline after which they&#39;ll pull your answer.  As a corollary, if you need help writing a puzzle, or getting it done in a timely fashion, please say so on the puzzle... it&#39;s much better to get some help and get the puzzle in than to have it cut completely!<br>

<br>In other news, as you may be aware, we&#39;ve decided that we are going to keep our headquarters open running business as usual until Sunday afternoon, if the coin is found any time before the stated closing time of 3:17pm (obviously if the coin isn&#39;t yet found, HQ will be open longer than that).  Assuming we do close at 3:17pm, wrapup will be at 5:17pm, followed by a social reception in Lobby 13.<br>
<br>As always, you can contact the Council (<a href="mailto:council@puzzle2011.com" target="_blank">council@puzzle2011.com</a>) or Oliver the Ombudsperson (<a href="mailto:okosut@gmail.com" target="_blank">okosut@gmail.com</a>) with any questions, comments, or concerns.<br>
<br>Keep the puzzles (writing and testing) coming!
</div>

<div class="team-updates">
<b>November 27, 2010:</b><br/>
We are down to just 7 weeks left until Hunt!  We currently have 42 puzzles completed (+7 from last week), 33 puzzles in testing (-6 from last week), 10 in revision (+2 from last week), and 19 awaiting a draft (-3 from last week).  We also have 67 puzzles that have at least one solve (+8 from last week).  Thanks to all the hard work that everyone has put in this week!  Here's what that looks like in graphical form:<br>
<img src="images/pie-20101127.png"><br/>
<br>
We have a major Hunt event coming up next weekend (12/4 and 12/5)!  Both Cambridge and Berkeley will be solving all weekend long and the solving server will be on-line (it&#39;s like Mystery Hunt in December!), so please plan on devoting all or part of your weekend to solving.  Given the numbers above, this is a necessary push if we are going to have a well-tested, complete Hunt in January.<br>
<br>
It&#39;s also necessary that we have as many drafts of puzzles in testing as possible by December 4th, so that they can be testsolved on the 5th and 6th.  If you can&#39;t produce a draft by then, you need to inform the editors and tell them when you can.  If they don&#39;t get either a draft or an update by 12/4, the editors reserve the right to either have someone else write your puzzle or pull your answer and give it to another puzzle.  So, get your puzzles written!<br>

<br>
Finally, one testing note: please submit progress on puzzles you have worked on, even if it is incomplete.  This is very helpful for the testing admins, editors, and authors.<br>
<br>
As always, you can contact the Council (<a href="mailto:council@puzzle2011.com" target="_blank">council@puzzle2011.com</a>) or Oliver the Ombudsperson (<a href="mailto:okosut@gmail.com" target="_blank">okosut@gmail.com</a>) with any questions, comments, or concerns.<br>
</div>

<div class="team-updates">
<b>November 19, 2010:</b><br/>
We&#39;re less than two months from the Hunt now, and here&#39;s our statistics!<br>
<img src="images/pie-20101119.png"><br/>

<br>
Target: 104 puzzles<br>
Finalized: 35 puzzles<br>
In some phase of testing or other: 39 puzzles<br>
In revision, post-testing: 8 puzzles<br>
(having at least one full solve in a usable form: 59ish)<br>
Therefore, still awaiting a full draft: 22 puzzles<br>
<br>
We need to pick up the pace! As Kate mentioned in another e-mail, the first weekend in December is set aside for a big ol&#39; Hunt Work Weekend, with marathon meetings in Cambridge and Berkeley and the solving server up for remote people. Just like old times! In order to get the most out of that weekend&#39;s testsolving, we need to get as many puzzles as possible ready for testing by then. So: write (or revise) those remaining 22+8 puzzles! If you need assistance on the implementation of your puzzles, please let your editors know and we can try and find someone to help you out. If you haven&#39;t posted on your puzzle in a while, please give us an update so we know how far along you are. Cindy is ready to be dispatched to annoy puzzle authors who show little evidence of progress!<br>

<br>
New puzzle proposals are still welcome, of course! Especially proposals for puzzles that can be flexible about what answer they take. At present, nearly all our puzzle slots are full, but having some more puzzles available that we could move in if some of our existing ones never get testsolved (or written) would be desirable.<br>
<br>
Continue testsolving the puzzles on your testing queues! Please pay attention to testing priority; work on &quot;In Testing&quot; puzzles only if you&#39;re stuck on all your &quot;High Priority&quot; puzzles, and so on. Testing-admins are now taking a more active role in supervising the testsolving process, so don&#39;t be surprised if you get e-mails from them (Oliver, Cat, Cally, Jen Berk) poking you for feedback on the puzzles on your queue. Don&#39;t forget to post partial feedback even for puzzles you haven&#39;t finished solving yet! If you get stuck you can e-mail <a href="mailto:testing-admins@puzzle2011.com" target="_blank">testing-admins@puzzle2011.com</a> and they&#39;ll try and find another tester to pick up from where you left off and try to extract the answer.<br>

<br>
Blind testsolving is underway! Blindsolvers both East and West have entered Bowser&#39;s Castle at the end of World 1 and caught their first glimpse of the next rounds beyond it. Word is that they&#39;re excited!<br>
<br>
Planning for Hunt events is taking place, the details of puzzle release ar e being worked out, rooms are getting reserved, and all other manner of progress is taking place! As usual, if you have any concerns or suggestions, e-mail the council (<a href="mailto:council@puzzle2011.com" target="_blank">council@puzzle2011.com</a>) or Oliver in his capacity as ombudsperson (<a href="mailto:okosut@gmail.com" target="_blank">okosut@gmail.com</a>).<br>
<br>
Happy testsolving!<br>

</div>

<div class="team-updates">
<b>November 13, 2010:</b><br/>
We now interrupt your regularly scheduled program for this important Hunt update.<br>
<div style="border-collapse:collapse;font-family:arial,sans-serif;font-size:13px"><br><b>YELLOW ALERT!</b><br></div><div style="border-collapse:collapse;font-family:arial,sans-serif;font-size:13px"> With approximately 60 days left, we are crawling along steadily, but we&#39;re also edging very close to the danger zone.<span style="font-family:arial,sans-serif;font-size:13px;border-collapse:collapse">
The holidays are nearly upon us and time is running short, so now is
definitely the time to step up and start making it happen, folks.</span><br>
<br><b>STAT SNAPSHOT!</b><br>Of our 104 puzzles:<br>33 puzzles are done and ready for Hunt<br>38 puzzles are currently in testing<br>5 puzzles are in revision, after returning from testing<br><br>That leaves 28 puzzles (in various states) which still need to be written.<br>

<span style="font-family:arial,sans-serif;font-size:13px;border-collapse:collapse"><br></span></div><div style="border-collapse:collapse;font-family:arial,sans-serif;font-size:13px"><b>I CAN HAZ TESTSOLVERS? SRSLY?</b><br>
Our goal is to have every puzzle solved twice whenever possible.
Currently, there are 59-ish puzzles with at least one solve, but we
clearly need more solves. Don&#39;t let your testing queue turn into the
dead puzzle office! If you have puzzles languishing in your queue,
please contact the testing admins (<a href="mailto:testing-admins@puzzle2011.com" target="_blank">testing-admins@puzzle2011.com</a><WBR>) and let them know if you need help. <br>
<br>Also, expect an email soon regarding a dedicated testsolving weekend in December.<br><br><b><span style="font-family:arial,sans-serif;font-size:13px;border-collapse:collapse"><span style="font-family:arial,sans-serif;font-size:13px;border-collapse:collapse">PUZZLE-WRITING - CAN WE DO IT? YES, WE CAN!</span></span></b><br>
There are still almost 30 puzzles left to write. Some of them just need
some final touches, others need a lot of work, but every one of them is
precious, and if you need help, please just ask. If you know you need
assistance now, the sooner you let us know, the less likely we are to
collapse of exhaustion in late December. Also, please remember to keep
your editors up to date on your progress. A fully updated editor makes
everyone&#39;s lives easier.<br>
<br>Remember, blind solving is under way, so we really do need your puzzles as soon as possible!<br><br><b>I CAN SEE! IT&#39;S A MIRACLE!</b><br>Speaking
of blind solvers, they&#39;re still hard at work, and from all reports,
they like what they see so far. It&#39;s an East Coast/West Coast battle of
the Easter Bunnies versus the Western Toads, and both teams thoroughly
rock. Blind solvers rule!<br>

<br>Everybody else, we don&#39;t want to let them down for the rest of Hunt, so let&#39;s keep our noses to the grindstone!<br><b><br>LAST BUT NOT LEAST!</b><br>Plot
and events are chugging along merrily, and the deadline for MIT funding
is fast approaching, so if you have budget requests or need to be
reimbursed for something you bought for Hunt, send an email to Kate (<a href="mailto:akatebaker@gmail.com" target="_blank">akatebaker@gmail.com</a>) by Nov. 26th. <br>
</div>
</div>

<div class="team-updates">
<b>November 6, 2010</b><br/>
<div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">Hi there everybody, it&#39;s time for this week&#39;s Hunt Update.</div>
<div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px"><br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">Still on Yellow Alert.  Everything is moving forward, as you can see by checking out the numbers at the bottom of this email, but we need it to keep doing so.  Less than 10 weeks until hunt!</div>
<div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px"><br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">We are hoping to have about 120 more solves (so that we can solve all puzzles twice).  We did 12 in the last two weeks.  (Oh dear.)</div>

<div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px"><br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">For puzzle writing, we wrote 10 in the last two weeks, and have just over 30 more to write.  If you&#39;re an author on a puzzle, please do remember to keep your editors updated on your progress.  Updates make everyone happy!  Oh, and do ask for help if you need it.  Better to find you help now than to have your puzzle still unwritten in December!</div>
<div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px"><br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">Blind testing is of course continuing! Keep up the great work, folks!</div>
<div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px"><br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">Of course we have other things to do too:  Events and Plot continue to be discussed and are moving forward.</div>
<div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px"><br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">*******************</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">
<br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">Numbers were so much fun last time that we thought we&#39;d give you more:</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">
<br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">Puzzles done with testsolving: 31</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">
Puzzles that have been testsolved at least once: 56</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">Puzzles that will be in hunt: 104</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">

<br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px"><br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">Puzzles in testing: 36</div>
<div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">Puzzles in revision after coming back from testing: 5</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">
Puzzles that still need to be written: 30ish</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">Puzzles that still need at least one solve: 73</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">
Days remaining until hunt: 69</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px"><br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">
<br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">********************</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px"><br></div>
<div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">Since Oct 22:</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px"><br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">
12 correct answers have been called in</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">12 puzzle drafts completed</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">
10 puzzles have moved into Testsolving Complete</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">12 puzzles have moved into Post-Production</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">

<br></div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">********************</div><div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px"><br></div>
<div style="border-collapse:collapse;font-family:arial, sans-serif;font-size:13px">As always, contact Oliver the Ombudsman at <a href="mailto:okosut@gmail.com" style="color:rgb(0, 0, 204)" target="_blank">okosut@gmail.com</a> or the council at <a href="mailto:council@puzzle2011.com" style="color:rgb(0, 0, 204)" target="_blank">council@puzzle2011.com</a> if you have a question, concern, or need a cookie.</div>
</div>

<div class="team-updates">
<b>October 29, 2010:</b>
<br>You want numbers? We&#39;ve got numbers:<br><br>Puzzles totally done: 26<br>Puzzles that have been testsolved at least once: 52<br>Puzzles that will be in hunt: 104<br><br>We&#39;d like to point out that these three numbers form a geometric sequence.<br>

<br>Puzzles in testing: 36<br>
Puzzles in revision after coming back from testing: 5<br>Puzzles that still need to be written: 37<br>
Puzzles that still need at least one solve: 78<br>Puzzle drafts written in the last two weeks: 14<br>Puzzles declared totally done in the last two weeks: 7<br>Solves of puzzles by testers in the last two weeks: 18<br>Days remaining until hunt: 79<br>
<br>We&#39;ve
gotten a whole lot done already, but, as you can see, we still have a
whole lot left. And this doesn&#39;t even include things like writing and
rehearsing kickoff, preparing events,
and making website art. So, don&#39;t panic, but yellow alert is still very
much in force. We&#39;ve just got to keep working and make the good numbers
big and the bad numbers small.<br>

<br>NEW TESTSOLVING POLICY: If you&#39;re testing a puzzle, and you get
stuck on answer extraction and would like some help to finish it off,
email <a href="mailto:testing-admins@puzzle2011.com" target="_blank">testing-admins@puzzle2011.com</a>, and we&#39;ll relay your state to a
member of our crack team of answer extractors. You may also discover
that you are a member of our crack team of answer extractors, and may
therefore receive requests to extract answers from puzzles that have
been worked on by other people.<br><br>As you may have noticed, blind testing has started on the east coast, and is soon to begin on the west coast. I hope you like the hunt!<br><br>As always, if you have questions,
comments, ways we can make you happier in working on hunt, or good
techniques for preventing your boss from noticing that you&#39;re spending
all your time working on hunt instead of work, email the council at
<a href="mailto:council@puzzle2011.com" target="_blank">council@puzzle2011.com</a>, or the ombudsperson at <a href="mailto:okosut@gmail.com" target="_blank">okosut@gmail.com</a>.
(Seriously. We say this every week and people almost never email me
complaining about stuff. It&#39;s almost like you&#39;re all completely happy. I
don&#39;t believe it!)
</div>

<div class="team-updates">
<b>October 22, 2010:</b><br/>
We're still on Yellow alert, we're down to only 87 days until hunt.
As before the main priorities are writing accepted puzzles and
testsolving. &nbsp;We are also hard at work at getting the graphics and the
hunt server up and running in order to start blind testsolving
sometime next week.<br />
<br />
If you have a puzzle with an answer assigned, please finish it!
Puzzles with 3 answers are especially important to get done ASAP.
Don't forget that you can subscribe to receive comments by email.
Cindy Keeler has recently accepted the position of &quot;harasser.&quot; This
position involves both harassing people who haven't finished their
puzzles and helping people find the resources they need to finish
writing.<br />
<br />
Solving puzzles is important too! &nbsp;We've recently changed our the
testsolving priority system. &nbsp;We have a new category called &quot;High
priority.&quot; &nbsp;The new system prioritizes getting puzzles ready for blind
testsolving and getting all puzzles solved at least once.<br/>
<br />
If you have any great new ideas for puzzles you should continue to
submit them. &nbsp;If a puzzle never gets written, or if testsolving shows
that the puzzle is unworkable, then we will need to replace puzzles
with new ideas. &nbsp;We're especially interested in puzzles that are
quickly adaptable to different answers.<br />
<br />
Events and plot discussions are ongoing. If you'd like to join in
email Tracy (<a href="mailto:tracyyonemoto@gmail.com">tracyyonemoto@gmail.com</a>) or Andrew (<a href="mailto:aethelred@gmail.com">aethelred@gmail.com</a>). Email the council (<a href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>) or our ombudsperson Oliver

(<a href="mailto:okosut@gmail.com">okosut@gmail.com</a>) with any questions or concerns, or if you have any
suggestions for stuff we can do to make it easier for you to work on<br />
Hunt.
</div>


<div class="team-updates">
<b>October 16, 2010:</b><br/>
Yellow alert status continues, but we are making progress: 19 puzzles
have been written in the last two weeks. Blind testsolving will also
begin this weekend.<br />
<br />
If you have a puzzle with an answer assigned, please finish it!
Puzzles with 3 answers are especially important to get done ASAP.

Don't forget that you can subscribe to receive comments by email.<br />
<br />
Solving puzzles is great too! We've got plenty of fun puzzles just
waiting for you, and the testsolving fairies are on vacation, so it's
up to us.<br />
<br />
Events and plot discussions are ongoing. If you'd like to join in,
email Tracy (<a href="mailto:tracyyonemoto@gmail.com">tracyyonemoto@gmail.com</a>) or Andrew (<a href="mailto:aethelred@gmail.com">aethelred@gmail.com</a>).<br />

<br />
As usual, email the council (<a href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>) or our<br />
outstanding ombudsperson Oliver (<a href="mailto:okosut@gmail.com">okosut@gmail.com</a>) with any questions<br />
or concerns, or if you have any suggestions for stuff we can do to<br />
make it easier for you to work on Hunt.<br />
</div>

<div class="team-updates">
<b>October 9, 2010:</b>
<br>
We are now at 100 days until Mystery Hunt 2011, and while we&#39;ve done a lot of good work in the last 265 days, there is still much more to be done.  Given our current status, we need to move about one puzzle a day from now until Hunt into the &quot;Testsolving Complete&quot; bin (this is in addition to doing things like writing kickoff and endgame, organizing logistics for events, setting up the running server, drawing a bunch of art, and so on).  Our rate for the past six weeks has been ~3 puzzles/week.  So, we are going to Yellow Alert!<br>
<br>
What does this mean?  It means that we need the team to collectively be putting in more hours of work than it currently is, by a substantial amount.  The situation is not yet dire, but if we each don&#39;t put in at least a couple more hours of work per week, it will be.<br>

<br>
So, what&#39;s the best use of your time?  At the moment, we have more puzzle ideas proposed than is necessary to fill the Hunt (which is great, by the way!  Thanks for submitting so many ideas!).  That means that (1) if you have a puzzle idea submitted with an answer assigned or reserved, you need to get it approved (if it isn&#39;t) and written ASAP or at least give the editors an ETA; otherwise, they may assign your answer to a different puzzle they think stands a better chance of being written, and (2) if you don&#39;t have any such puzzles, you absolutely must focus on testsolving.  Submitting new puzzle ideas is low priority at this time.<br>
<br>
Thanks for all of your hard work, and for picking up the pace.  One thing to keep in mind: we are the team that produced SPIES, which was one of the best-liked Hunts in the last decade.  We can totally do that again!<br>
<br>
As usual, email the council (<a href="mailto:council@puzzle2011.com" target="_blank">council@puzzle2011.com</a>) or our outstanding ombudsperson Oliver (<a href="mailto:okosut@gmail.com" target="_blank">okosut@gmail.com</a>) with any questions or concerns, or if you have any suggestions for stuff we can do to make it easier for you to work on Hunt.
</div>


<div class="team-updates">
<p><b>October 2, 2010:</b> It's October! How many puzzles do we have?</p>

<p>Answer: 16 puzzles have completed testsolving!</p>

<p>We also have 28 in various stages of testing, and 44 accepted ideas in various stages of writing.  *Please write your accepted ideas!*  Our top priority is getting puzzles written, so if you want help with writing yours, please please please email Andrew (<a href="mailto:aethelred@gmail.com">aethelred@gmail.com</a>) and he'll work with you to find a collaborator.  Our testers have been doing great things, and they need puzzles to attack.</p>

<p>We're also still finalizing the last meta, so if you're not blind, not spoiled on all the metas (being on the list but not reading it is fine), and have even an hour to look at it, please email <a href="mailto:testing-admins@puzzle2011.com">testing-admins@puzzle2011.com</a>.</p>

<p>We're very close to having our first set of designs, so blind testers should start seeing things in a couple weeks.  We're excited to share all the plot and structure stuff we've worked on!</p>

<p>The plot and events lists are planning away; more news on those fronts in future updates.  If you're not blind and want to join those lists, email Andrew (<a href="mailto:aethelred@gmail.com">aethelred@gmail.com</a>) and Tracy (<a href="tracyyonemoto@gmail.com">tracyyonemoto@gmail.com</a>) respectively.</p>

<p>And if you have any concerns, please contact the council (<a href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>) or our friendly ombudsperson Oliver (<a href="mailto:okosut@gmail.com">okosut@gmail.com</a>) - we can't improve things if we don't know they're broken.</p>
</div>

<div class="team-updates">
<p><b>September 19, 2010:</b> Hi Team,</p>

<p>Here we go!  Our current puzzle status: 146 puzzle ideas submitted, 83 accepted, 44 with submitted drafts, and 16 done with testsolving.</p>

<p>Our top priority right now is WRITING PUZZLES.  If you are someone who has an idea accepted, but you haven't yet submitted a draft, please make that your top (Hunt) priority.  Likewise, if you have submitted an idea but it has not been accepted, please check in with the editors on the server and refine it.</p>

<p>In other news, we still have one meta that needs one solve.  If you have some time to look at it, please let testing-admins@puzzle2011.com know.  Regular testsolving is moving along; 32 people currently have puzzles in their testing queues.  Puzzles with status "In Testing" are more important to work on than puzzles with status "Low Priority Testing" (as you might have imagined), so please concentrate on solving the former.  In particular, we would like to get the puzzles currently "In Testing" fully solved by October 15.</p>

<p>Additionally, our plot and events email lists (plot@puzzle2011.com and events@puzzle2011.com) are making good progress.  If you are interested in being on one or the other, please email nsnyder@gmail.com and tracyyonemoto@gmail.com respectively.</p>

<p>As always, you can direct any questions or concerns to the council (council@puzzle2011.com) or our outstanding  ombudsperson Oliver (okosut@gmail.com).</p>

<p>Happy Puzzling!</p>

</div>

<div class="team-updates">
<b>September 10, 2010:</b>

<p><b>Puzzles:</b> We currently have:<br>
 - 10 puzzles completely testsolved, <br>
 - 24 puzzles in testing, <br>
 - 4 puzzles in revision due to testing feedback<br>
 - 3 puzzles with drafts written but not yet in testing<br>
 - 28 accepted puzzle ideas with answers assigned but not yet written<br>
 - 11 accepted ideas without answers assigned<br>
 - 40 puzzle ideas pending</p>

<p>I want to make special mention of those 28 puzzles with answers assigned but not yet written. If you're an author on one of those puzzles, write it!<br><br>If you've submitted an idea, be sure to check the server to
see if editors have commented on it. If you've got a puzzle in
testing, wait for the thrilling moment when tester feedback appears,
telling tales of the epic struggle against your puzzle, until at last
the moment came when it gave way to reveal the untold beauty hidden
within. And, of course, we still need lots more puzzle ideas, so keep submitting them!</p>

<p><b>Testsolving:</b> We've been testsolving puzzles for
less than 2 weeks now, and we already have 10 puzzles completely
testsolved! This is great, but we still have a lot more puzzles left to
test, so keep working on puzzles and posting feedback. If you have any
questions or comments about testsolving, email the testing admins at <a href="mailto:testing-admins@puzzle2011.com" target="_blank">testing-admins@puzzle2011.com</a>.</p>

<p><b>Plot and Events: </b>If you're sighted, you can still join the plot list (email <a href="mailto:aethelred@gmail.com" target="_blank">aethelred@gmail.com</a>) or the events list (email <a href="mailto:tracyyonemoto@gmail.com" target="_blank">tracyyonemoto@gmail.com</a>).</p>

<p>As always, if you have questions, comments, or a good story
involving a porcupine and an unexpectedly tall janitor, send them to the
council at <a href="mailto:council@puzzle2011.com" target="_blank">council@puzzle2011.com</a>, or the ombudsperson, at <a href="mailto:okosut@gmail.com" target="_blank">okosut@gmail.com</a>.</p>
</div>

<div class="team-updates">
<b>September 3, 2010:</b>
<p><b>STOP THE PRESSES! TESTSOLVING HAS STARTED!</b><br/>
That's right, we have officially launched testsolving! To begin testsolving, please visit the server at <a href="https://puzzle2011.com/writing/login.php">https://puzzle2011.com/writing/login.php</a> and register if you haven't already done so or log in to start adding puzzles to your testing queue, and please remember to request specific puzzles by number to avoid accidental spoilage. If you've forgotten any of your login information, you can contact one of the server admins at <a href=mailto:server-admin@puzzle2011.com">server-admin@puzzle2011.com</a> for help. For other questions, please reference the more detailed testsolving email sent out earlier this week or contact the testing admins at <a href="mailto:testing-admins@puzzle2011.com">testing-admins@puzzle2011.com</a> with any questions or comments.</p>


<p><b>CRITICS AGREE: PUZZLES ARE TASTY AND NUTRITIOUS, BUT WE WANT MORE!</b><br/>
As of time of writing, we have 34 puzzles in various stages of testing, and 2 finished! There are 41 accepted ideas still in need of a completed draft, and 34 more puzzles in discussion. And with well over 5000 human-submitted comments on the server, there's been plenty of discussion happening!</p>

<p>If you've submitted an idea that hasn't been accepted yet, please check back on the server and respond to editor feedback. If you have an idea that's been accepted, write the puzzle! We want puzzles yourheck, we need your puzzles, but we won't be able to use your puzzles unless you write them. Also and equally important! If you have a puzzle that's in testsolving, please check back on the server frequently - the editors may request changes based on tester feedback.</p>

<p><b>CLASSIFIEDS</b><br>
If you like pina coladas and taking walks in the rain, then you'll be glad to hear there's been lots of work getting done behind the scenes. Metapuzzles are mostly finished, and sighted people are hard at work hammering out the plot and events details. I'd tell you more, but we all know that blind solvers love a little mystery before the first date.</p>
</div>

<div class="team-updates">
<p><b>August 27, 2010:</b> <i>Puzzle testsolving is starting TOMORROW.</i> Look for an email with more details.</p>

<p><i>Puzzles:</i> We currently have 34 puzzles with complete drafts ready for testing, 40 more accepted puzzles waiting for drafts to be written, and 32 more pending puzzles in discussion. If you've submitted an idea and it hasn't been accepted yet, be sure to check back on the server and respond to feedback from the editors. If you've had an idea accepted and an answer assigned, write it!</p>

<p>As mentioned previously, we recently finished deciding which rounds are in the hunt. We have also nearly finished writing and testing metapuzzles. Sighted people are also working on plot and events. All of which we'll be able to start telling you about, probably in the next month or two.</p>

<p>As always, if you have questions, comments, suggestions, or you know a good way to hold a plate of food and a drink at the same time, email the council at <a href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>, or the ombudsperson, at <a href="mailto:okosut@gmail.com">okosut@gmail.com</a>.</p>
</div>

<div class="team-updates">
<p><b>August 20, 2010:</b> As mentioned last week, we've selected all the rounds that are going to be in the Hunt. There are still a couple of metas that are in need of one more testsolve before we want to call them finalized, though, so if you're not a blindsolver and would like to finish a meta off, e-mail <a href="mailto:testing-admins@puzzle2011.com">testing-admins@puzzle2011.com</a> to request one.</p>

<p>Puzzle writing is proceeding; there are some great puzzle ideas on the server! If one or more of those puzzle ideas is yours, <i>please</i> check in on the discussion frequently (or click on "subscribe to comments" so you'll be updated automatically); there are 30 puzzle proposals marked "pending" that we'd love to be able move to "accepted", but can't until we get more discussion from the authors. Likewise, if you've got an accepted puzzle proposal with an answer assigned, the sooner you can produce a full draft of the puzzle the better; if you're having trouble finishing, let the editors know so we can help you out.</p>

<p>Speaking of which... roughly 30% of the (non-meta) puzzles in the Hunt are written and ready for testsolving! You'll be able to see these for yourself pretty soon; the testsolving server is projected to be up and running within a week. In the meantime, fill out your bio on the server so the testing-admins know where your skills are most useful.</p>

That 30% of the Hunt that's written includes almost all of the first segment of the Hunt that teams will see. These have been given a high priority so we'll be able to launch blind testsolving as soon as possible. So, blindsolvers, we haven't forgotten about you&mdash;and when you're not blindsolving, you can participate in regular testsolving also!</p>
</div>

<div class="team-updates">
<p><b>August 13, 2010:</b> We have exciting news, we've finished choosing all the rounds for the hunt!  Furthermore nearly all the metas are done.  This is a cause for
celebration.</p>

<p>We can't tell you all the awesome stuff about our hunt's structure,
but one thing we do need to tell you is that for part of the hunt we
will need 9 puzzles that can yield 3 specific answers each.  So if you
have any ideas that you think could give 3 answers in an interesting
way, we'd love to hear about them.</p>

<p>Even though the metas are nearly done, there's a lot of other stuff
that still needs to be done.</p>

<p><i>Writing puzzles:</i>
We need to buckle down on puzzle writing.  Basically we need to finish
at least a puzzle every day for the next few months to get back on
track.   Visit <a href="https://puzzle2011.com/writing/submit-new.php">https://puzzle2011.com/writing/submit-new.php</a> early and
often.</p>

<p><i>Testsolving:</i>
The testsolving interface is almost ready, but for now it would be
very helpful if you filled out your bio on the server
(<a href="https://puzzle2011.com/writing/register.php">https://puzzle2011.com/writing/register.php</a>) so that you can get
matched up with puzzles to test that you will actually like.</p>

<p>If you have any questions or concerns, please feel free to email the
council (<a href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>) or our outstanding ombudsperson
Oliver (<a href="mailto:okosut@gmail.com">okosut@gmail.com</a>).</p>
</div>

<div class="team-updates">

<p><b>August 7, 2010:</b> Here's your friendly neighborhood Hunt
update for the week:</p>

<p><i>Metas!</i> The meta-writing people are _really_ close to
wrapping up, but we still need some more meta testing help.  If you'd
like to testsolve a meta, email <a
href="mailto:testing-admins@puzzle2011.com">testing-admins@puzzle2011.com</a>
and they will hook you up.</p>

<p><i>Puzzles!</i> We currently have 103 puzzle ideas submitted, 62
accepted, and 24 with submitted drafts (which includes 1 complete
round!).</p>

<p><i>Testsolving!</i> The testsolving interface is almost ready, but
for now it would be very helpful if you filled out your bio on the
server (<a
href="https://puzzle2011.com/writing/register.php">https://puzzle2011.com/writing/register.php</a>)
so that you can get matched up with puzzles to test that need your
particular skills.</p>

<p><i>Writing!</i> If you have an answer assigned to a puzzle, please
get a draft up ASAP.  If you have a puzzle idea or draft posted,
please make sure that you respond to comments made by the editors (and
take advantage of the handy new feature that let's the puzzle server
email you when a new comment has been made -- just click the
"Subscribe to Comments" button at the bottom of the puzzle's status
page).  Also, keep on submitting new puzzle ideas at <a
href="https://puzzle2011.com/writing/submit-new.php">https://puzzle2011.com/writing/submit-new.php</a>.</p>

<p>If you have any questions or concerns, please feel free to email
the council (<a
href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>) or
our outstanding ombudsperson Oliver (<a
href="mailto:okosut@gmail.com">okosut@gmail.com</a>).</p>

</div>

<div class="team-updates">

<p><b>July 30, 2010:</b> Friendly fellow Plants &mdash;</p>

<p>We currently have:</p>
<ul>
<li>100 puzzles submitted</li>
<li>61 puzzles accepted</li>
<li>20 puzzles with drafts</li>
</ul>

<p>Much to my dismay, there don't seem to be any magical Mystery Hunt
writing elves. And so:</p>

<ul>

<li>Please fill out your bio on the server (<a
href="https://puzzle2011.com/writing/register.php">https://puzzle2011.com/writing/register.php</a>). More
complete information will help the editors and testing-admins in
connecting you with puzzles.</li>

<li>Test a meta (sighted solvers only, please). They're fun! Email <a
href="mailto:testing-admins">testing-admins@puzzle2011.com</a> for a
puzzle. (If you don't want to solve alone, they're happy to find you a
friend.) We could really use some help here.</li>

<li>Write a draft of your puzzle, once you have an answer assigned.</li>

<li>Submit your new puzzle ideas
(<a href="https://puzzle2011.com/writing/submit-new.php">https://puzzle2011.com/writing/submit-new.php</a>).</li>

</ul>

<p>But there's exciting news!:</p>

<ul>
<li>An entire round is written.</li>
<li>Plot discussions are underway.</li>
<li>The server's testsolving interface is in debugging.</li>
</ul>

<p>As always, if you have concerns/questions/a desire to do more hunt
stuff/general life thoughts, tell the council (<a
href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>). And
don't forget Oliver (<a
href="mailto:okosut@gmail.com">okosut@gmail.com</a>), our friendly
ombudsperson.</p>

</div>

<div class="team-updates">

<p><b>July 24, 2010:</b> We have 98 puzzle ideas submitted, 61
accepted, and 18 full drafts written.  We're also working on designing
the first round so we can give it to our blind testers.</p>

<p>This weekend please do at least the first of these that you're able
to do:</p>

<p>1. Complete your incomplete bio on the server by going to <a
href="https://puzzle2011.com/writing/register.php">https://puzzle2011.com/writing/register.php</a>.</p>

<p>2. Test a meta by emailing <a
href="mailto:testing-admins@puzzle2011.com">testing-admin@puzzle2011.com</a>
(only sighted team members).</p>

<p>3. Write a draft of your answer-assigned puzzle and upload it to the
puzzle's discussion - especially if your puzzle is in the first
rounds.</p>

<p>4. Submit a new puzzle idea by going to <a
href="https://puzzle2011.com/writing/submit-new.php">https://puzzle2011.com/writing/submit-new.php</a>.</p>

<p>We really need new meta testers from the sighted team members (trying
to finalize revisions to things that most of our meta testers are
already spoiled on).  Please ask for a meta to test even if you're
normally not a meta solver - they're a lot easier when you've slept
recently!</p>

<p>If you have questions/concerns/wishes to volunteer for things,
please send 'em to the council (<a
href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>) or
the ombudsperson (<a
href="mailto:okosut@gmail.com">okosut@gmail.com</a>).</p>

</div>

<div class="team-updates">

<p><b>July 16, 2010:</b> We're almost halfway from the last hunt to the next hunt! Eek!</p>

<p>We're working on all kinds of cool stuff. Our theme, it's awesome. Our metas, they're awesome. Our idea for kickoff, it's awesome. We're closing in on finishing the first group of puzzles that teams will get.</p>

<p>We've got 97 puzzle ideas submitted, 59 accepted, and 15 full drafts written. According to our records, this is more puzzles written than had been written by S.P.I.E.S. on July 15, 2005. Keep submitting those puzzle ideas! And write those puzzles! Meta testing is still in full force. Email <a href="mailto:testing-admins@puzzle2011.com">testing-admins@puzzle2011.com</a> if you're sighted and want a meta to test. Individual puzzle testing will start in the next two or three weeks. I know I'm yearning to see all the supercool puzzles that people have been writing, and I hope you are too.</p>

<p>The plot list has been launched, and discussion of ploty things are happening there. Email <a href="mailto:aethelred@gmail.com">aethelred@gmail.com</a> if you're sighted and want to join.</p>

<p>As always, if you have questions, comments, concerns, or ideas for superluminal communication, send them to the council at <a href="mailto:council@puzzle2011.com">council@puzzle2011.com</a> or the ombudsperson at <a href="mailto:okosut@gmail.com">okosut@gmail.com</a>.</p>

</div>

<div class="team-updates">

<p><b>July 10, 2010:</b> Here's your team update for this week! Hope you had a good Fourth of July last weekend.</p>

<p>The current puzzle writing statistics are as follows: 92 ideas submitted, 57 accepted, 44 with answers assigned, 13 with full drafts written. Keep submitting new puzzle ideas!</p>

<p>About half the Hunt's worth of metas have been fully finalized; there are other metas that have been fully or partially testsolved but that the meta writers are waiting for more information before choosing which to pin into the Hunt.</p>

<p>All of the puzzle answers in the first few sets that teams will see have been assigned to puzzles, and it's currently a high priority to get those puzzles written in order to be able to begin blind testsolving of the beginning of the Hunt.</p>

<p>An e-mail list has been launched for working out the details of the Hunt's theme, plot, and structural organization. If you're sighted and want to joine, email Andrew at <a href="mailto:aethelred@gmail.com">aethelred@gmail.com</a>.</p>

<p>Questions? Comments? Concerns? Email the council at <a href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>, or Oliver, the ombudsperson, at <a href="mailto:okosut@gmail.com">okosut@gmail.com</a>.</p>

</div>

<div class="team-updates">

<p><b>June 26, 2010:</b> It's time for your newest Mystery Hunt update!</p>

<p>At the moment, the team has two main priorities: (1) testsolve
metas and (2) write puzzles for which answers have been assigned.  If
you have metas to testsolve in your queue, please give solving them a
try (and if you don't but have some time, email <a
href="mailto:testing-admins@puzzle2011.com">testing-admins@puzzle2011.com</a>
to get some metas to testsolve).  If you have puzzle proposals which
have been assigned answers, please write them ASAP.</p>

<p>In terms of our current puzzle-writing progress: we currently have
83 regular puzzle ideas submitted, with 51 accepted, 22 with answers
assigned, and 9 puzzles fully drafted.  Please be sure to check the
writing server frequently if you have submitted any ideas to respond
to the editors' feedback, and please continue to submit new ideas
(remember, our target is ~120 puzzles!).  Also, if you haven't
submitted a profile to the server, please do that soon so that the
editors can use them to help match people with the right
skills/interests to puzzles who need them.</p>

<p>The plot discussion email list has just been formed, but it's not
too late to join.  Email Andrew (<a
href="mailto:aethelred@gmail.com">aethelred@gmail.com</a>) if you are
interested in joining.</p>

<p>Questions? Comments? Concerns? Email the counsel at <a
href="mailto:council@puzzle2011.com">council@puzzle2011.com</a> or
Oliver the Ombudsperson at <a
href="mailto:okosut@gmail.com">okosut@gmail.com</a>.</p>

</div>

<div class="team-updates">

<p><b>June 20, 2010:</b> Here's your team update for this weekend!
Sorry it's late.</p>

<p><i>Metas:</i> We'd still like more volunteers to test metas!  If
you're sighted and either not on the metas list or ignoring all metas
list emails, please contact <a
href="mailto:testing-admins@puzzle2011.com">testing-admins@puzzle2011.com</a>
to get a couple of metas to work on. Many thanks to those who've been
solving so far, and please keep up the good work.</p>

<p><i>Puzzles:</i> At this moment, the number of puzzle ideas
submitted is 82, including 47 accepted, including 8 with full
drafts. All of the answers in the first set of puzzles solvers will
see have been assigned to puzzles. There are only four remaining
answers not assigned to puzzles, and each of those four has been
either requested by an author but not yet assigned, or tentatively
offered to a puzzle whose authors and editors are considering the
feasibility of using it. More answers will become available as metas
get finalized.</p>

<p><i>Server:</i> If you haven't yet registered on the server, please
do so at <a
href="https://puzzle2011.com/writing/register.php">https://puzzle2011.com/writing/register.php</a>.
If you have, please make sure your bio and interests are filled out -
this is really useful for the puzzle editors trying to find coauthors.
Your entry doesn't need to be long, just take a minute to introduce
yourself to team members who might not know you.</p>

<p>As always, if you have comments, questions, concerns, or sunburn
remedies, send them to the council at <a
href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>, or
the new ombudsperson at <a
href="mailto:okosut@gmail.com">okosut@gmail.com</a>.</p>

</div>

<div class="team-updates">

<p><b>June 11, 2010:</b> Your update for this week:</p>

<p><i>Puzzles:</i> We have 76 puzzle ideas submitted, 40 accepted, 16 with answers assigned, and 5 complete drafts written. Puzzle testsolving will start soon, so we'll all get to see the certain-to-be awesome puzzles that have been written. In the mean time, keep submitting ideas, check the server to get feedback on your ideas from the editors, and write puzzles if you have an answer assigned.</p>

<p><i>Lists:</i> Puzzle brainstorming lists are up and running. More people always help for good discussions, so email the council (<a href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>) if you want to be added.</p>

<p><i>Server:</i> If you haven't yet registered on the server, you can still do so at <a href="https://puzzle2011.com/writing/register.php">https://puzzle2011.com/writing/register.php</a>. If you have, please make sure your bio and interests are filled out. It doesn't need to be long, just take a minute to introduce yourself to team members who might not know you.</p>

<p>As always, if you have comments, questions, concerns, or good meatloaf recipes, send them to the council at <a href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>, or the new ombudsperson, me, at <a href="mailto:okosut@gmail.com">okosut@gmail.com</a>.</p>

</div>

<div class="team-updates">

<p><b>June 5, 2010:</b> Hey look it's your weekly Mystery Hunt
update!</p>

<p>Metapuzzle writing and testsolving are moving along! There are
several metas in various stages of the testing process, and soon we'll
have enough information to start deciding which of the current crop we
want to put into the Hunt.</p>

<p>The number of puzzle ideas submitted is up to 65, with about 32
accepted, including a few that have had complete drafts written up. If
you've had an answer assigned to your puzzle idea, it's now time to
start writing up a complete draft of your puzzle to get it into
testsolvable form.</p>

<p>If you've submitted an idea that hasn't been accepted yet, don't
forget to check your idea's discussion thread for comments from the
editors to see how you can get it up to snuff. If there hasn't been a
lot of discussion on your puzzle lately and you're not sure what the
editors are looking for from you, go ahead and post and ask what's up;
that'll bring your puzzle to the top of the editors' queue.</p>

<p>And of course, continue submitting new puzzle ideas! And in
general, if you have suggestions, questions, concerns, or anything
else, please email the council at <a
href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>.</p>

</div>

<div class="team-updates">

<p><b>May 28, 2010:</b> Here's your weekly Mystery Hunt status update!</p>

<ol>

<li>Meta writing and testsolving are proceeding apace; several new
metas have been recently sent into the testsolving queue and a few
more will almost certainly be sent by the end of of this weekend. Meta
testsolving remains our highest priority; the more metas get
successfully testsolved, the more answers we can have available to
write puzzles around.</li>

<li>Speaking of which! Of the 58 puzzle ideas that have been
submitted, 31 have been accepted; moreover, thirteen have had answers
assigned to them. If you've had a puzzle submission accepted and an
answer assigned, the next step is to go ahead and write up a full
working draft of your puzzle.</li>

<li>Keep submitting new puzzle ideas to the server! The 31 ideas
accepted so far are great, but can obviously make up at most a quarter
of the Hunt. In case you're looking for inspiration or assistance with
a puzzle, the puzzle brainstorming e-mail lists launched this week and
have seen some interesting puzzle ideas and discussion
already. There's plenty of room on them; if you'd like to join one but
haven't yet, contact <a href="mailto:raf@mit.edu">Roger</a>.</li>

<li>Please register your account on the writing server if you haven't
don't so yet. And even if you have, make sure you've filled out the
miscellaneous bio and interests info; this will make it easier to the
best potential co-authors or testsolvers for particular puzzle
ideas.</li>

</ol>

<p>As always, if you have ideas, suggestions, questions, concerns, or
anything else, please email the council at <a
href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>, or
our ombudsperson Mira at <a
href="mailto:miracb@gmail.com">miracb@gmail.com</a>.</p>

</div>

<div class="team-updates">

<p><b>May 21, 2010:</b> Here's this week's team update:</p>

<p>Meta writing and testsolving are proceeding! The first couple of metas have successfully gotten through testsolving, and this means that a few answers are going to be unlocked to be assigned to puzzles in the near future. If you've got a puzzle idea submission that's been accepted, you might want to check in on it soon to see if the editors are ready to assign an answer to it.</p>

<p>Keep submitting your new puzzle ideas, and checking on your submissions for editor comments! Puzzle brainstorming lists will be launched soon, but there's still time to sign up for them by e-mailing Roger (<a href="mailto:raf@mit.edu">raf@mit.edu</a>).</p>

<p>If you haven't registered your account on the writing server yet, please do so; once some puzzles have answers assigned, testsolving will begin pretty soon, and we'll need everyone to have a server account in order to testsolve.</p>

<p>As always, if you have ideas, suggestions, questions, concerns, or anything else, please email the council at <a href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>, or our ombudsperson Mira at <a href="mailto:miracb@gmail.com">miracb@gmail.com</a>.</p>

</div>

<div class="team-updates">

<p><b>May 14, 2010:</b> Not much is new since last week's
update. There are 52 puzzle ideas in the queue, including 25 that have
been accepted.</p>

<p>1. The highest priority remains writing and test solving metas. If
you have been sent a meta, please work on it and send your results and
feedback to Jennifer (<a
href="mailto:jcberk@gmail.com">jcberk@gmail.com</a>). If you have time
to test solve some metas, please also let her know.</p>

<p>2. As we mentioned last time, we're going to set up puzzle-idea
brainstorming lists. If you would like to be on one, please email
Roger (<a href="mailto:raf@mit.edu">raf@mit.edu</a>) and let him know
if there are other team members you would like to be in the same group
as. We will be getting these lists set up shortly, so if you want to
be on one, please let Roger know very soon.</p>

<p>As always, if you have ideas, suggestions, questions, concerns, or
anything else, please email the council at <a
href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>, or
our ombudsperson Mira at <a
href="miracb@gmail.com">miracb@gmail.com</a>.</p>

</div>

<div class="team-updates">

<p><b>May 7, 2010:</b> Here's the latest hunt status update. The
council met last night to discuss the status of the hunt and our
priorities, and we're going to aim to send an update out every week
going forward.</p>

<p>1. Our highest priority is writing and test solving metas. Several
metas are in test solving right now; hopefully we will soon have one
or more rounds finalized. If you're on the meta-writing list, please
work on writing metas. If you have been sent metas to test solve,
please work on them and update Jennifer (<a
href="mailto:jcberk@gmail.com">jcberk@gmail.com</a>) with your solving
status. If you have time to test solve metas, please email Jennifer
and let her know.</p>

<p>2. If you have not signed up for an account on the puzzle server,
please do so now at <a
href="https://puzzle2011.com/writing/register.php">https://puzzle2011.com/writing/register.php</a>. If
you have signed up for an account, please log in and make sure you've
filled out all your bio information, including profile photo. This can
also be done at <a
href="https://puzzle2011.com/writing/register.php">https://puzzle2011.com/writing/register.php</a>.</p>

<p>3. The puzzle server is live (at <a
href="https://puzzle2011.com/writing/">https://puzzle2011.com/writing/</a>)
and accepting puzzle ideas. So far 50 puzzle ideas have been submitted
to the server; 22 have been approved. There has been a lot of
discussion between the authors and editors before an idea is accepted,
so if you have submitted an idea and haven't checked its status
lately, please log in and check, since the editors probably have
questions for you. If there's been little recent discussion on one of
your puzzle ideas, the editors may be waiting for a response from you,
or may just have lost track of your idea; posting a comment will bring
it to the top of their stack.</p>

<p>4. We're going to try something new this year and set up a few
puzzle-brainstorming email groups. The idea is that if someone has
part of a puzzle idea or has an idea for a topic for a puzzle, without
really knowing how it should work, they can email the group for
feedback and discussion from a broader audience. We want these groups
to be big enough for useful brainstorming and discussion but small
enough to leave plenty of unspoiled test solvers, so we're aiming for
15-20 people per list. Hopefully these lists will be useful in turning
half-formed ideas into great puzzles.</p>

<p>If you would like to be on one of these brainstorming lists, please
email Roger (<a href="mailto:raf@mit.edu">raf@mit.edu</a>) and let him
know if there are other team members you would like to be in the same
group as. Otherwise we'll do random assignment.</p>

<p>As always, if you have ideas, suggestions, questions, concerns, or
anything else, please email the council at <a
href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>, or
our ombudsperson Mira at <a
href="mailto:miracb@gmail.com">miracb@gmail.com</a>.</p>

</div>

<div class="team-updates">

<p><b>April 24, 2010:</b> Meta-writing continues.  Several metas have
begun testing, and there are more ideas in the pipe.</p>

<p>As Aaron announced earlier this week, the puzzle server is now open
for idea submissions.  Getting metas written is still our top
priority, so editors may not yet be devoting their full attention to
puzzle submissions.</p>

<p>A few people have asked about how to deal with their partial puzzle
ideas, or puzzle ideas they don't really want to implement themselves.
Roger will be sending out an email shortly explaining the structure
for dealing with such things.</p>

<p>We've been in touch with Beginners' Luck, and though they're not
done updating various archives yet, we're going to have people added
to lockers and mailing lists and such shortly.</p>

<p>As always, if you have suggestions for things our team should
remember to plan for, please email the council list (<a
href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>) and
suggest and/or volunteer. (Current membership is Aaron, Allen, Andrew,
Jen Berk, Jenn Braun, Joel, Joia, Kate, Noah, Oliver, Roger Ford,
Tracy.)  If you have concerns about how things are going, please let
the board (<a
href="mailto:board@puzzle2011.com">board@puzzle2011.com</a>) or our
ombudsperson Mira (<a href="miracb@gmail.com">miracb@gmail.com</a>)
know.</p>

</div>

<div class="team-updates">

<p><b>April 2, 2010:</b> We've picked a theme and are now writing
metas. Meta testing will be beginning shortly.</p>

<p>We got the running server from Beginners Luck. We don't yet have
the locker and puzzle@mit.edu - Beginners Luck has continued to make
archive updates - but hopefully will have them soon.</p>

<p>We're planning to start puzzle idea submissions in the next couple
of weeks. Please be thinking of puzzle ideas, even though the server
isn't ready yet. We hoping for 30-40 puzzle ideas suggested per month
for the next 6-8 months.</p>

<p>The writing server is close to ready for this stage, thanks mostly
to Allen and Kate. If you haven't registered on the server, please do
so at <a
href="https://puzzle2011.com/writing/register.php">https://puzzle2011.com/writing/register.php</a>.</p>

<p>We've created a "council" to assist the board with administrative
tasks, which eight people have volunteered to be on. If you have
suggestions for things our team should remember to plan for, please
email the council list (<a
href="mailto:council@puzzle2011.com">council@puzzle2011.com</a>) and
suggest and/or volunteer. (Current membership is Aaron, Allen, Andrew,
Jen Berk, Jenn Braun, Joel, Joia, Kate, Noah, Oliver, Roger Ford,
Tracy.)</p>

</div>

<div class="team-updates">

<p><b>March 4, 2010:</b> We picked a theme.</p>

</div>

<?	// End HTML
	foot();
?>