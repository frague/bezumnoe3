<?php

	/*
	
		Updates existing forum messages.
		Sets IDs to existing users.
	
	*/

	$root = "../";
	require_once $root."references.php";
	require_once "step_tracking.php";

	set_time_limit(120);


	echo "<h2>Forum author IDs update:</h2>";
	
	echo "<h3>Update <u>OLD</u> forum with user avatars data:</h3>";

	$q = $db->Query("SELECT 
	DISTINCT(t1.author),
	t2.".User::USER_ID."
FROM
	_forum AS t1
	JOIN ".User::table." AS t2 ON t2.".User::LOGIN."=t1.author
WHERE
	t1.email=1");

	echo "<ol>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();

		$author = $q->Get("author");
		$id = $q->Get(User::USER_ID);
//		echo "<li> ".$author." (id=".$id.")";
		$q1 = $db->Query("UPDATE _forum
SET 
	email=".$id."
WHERE 
	author='".SqlQuote($author)."' AND
	email=1");
	}
	echo "</ol>";


	echo "<h2>Forum counts update:</h2>";

	$forum = new ForumBase();
	$q = $forum->GetByCondition("1=1");

	$record = new ForumRecord();

	echo "<ol>";

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$forum->FillFromResult($q);

		$r = $record->GetForumThreads($forum->Id, Forum::FULL_ACCESS);
		$c = 0;
		for ($k = 0; $k < $r->NumRows(); $k++) {
			$r->NextResult();
			$record->FillFromResult($r);
			$record->UpdateAnswersCount();
			$c++;
		}
//		echo "<li> ".$forum->Title." contains ".$c." threads.";
		$db->Query($forum->UpdateThreadsCountExpression());
	}

	echo "</ol>";
	
	echo "Update completed!";
	Passed();

?>