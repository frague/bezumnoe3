<?php

	/*
	
		Updates existing forum messages.
		Sets IDs to existing users.
	
	*/

	$root = "../";
	require_once $root."references.php";
	require_once "step_tracking.php";

	set_time_limit(120);


	echo "<h2>ƒобавление ID пользователей в записи форума:</h2>";
	
	echo "<h3>ƒобавление в старых форумах ссылок на аватары пользователей:</h3>";

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


	echo "<h3>ќбновление количества записей в форумах:</h3>";

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
		echo "<li> “ем в форуме ".$forum->Title.": ".$c;
		$db->Query($forum->UpdateThreadsCountExpression());
	}

	echo "</ol>";
	
	Passed();

?>