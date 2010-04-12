<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "forum.template.php";

	Head("Форумы", "forum.css", "forum.js", "", "", "forums.gif");
	require_once $root."references.php";

	$yesterday = DateFromTime(time() - 60*60*24);	// Yesterday

	$forum = new Forum();
	if ($someoneIsLogged) {
		$q = $forum->GetByConditionWithUserAccess("1=1", $user->User->Id);
	} else {
		$q = $forum->GetByCondition("1=1");
	}
	echo "<ul class='Forums'>";

	$forumUser = new ForumUser();

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$forum->FillFromResult($q);

		$access = 1 - $forum->IsProtected;
		if ($someoneIsLogged) {
			$forumUser->FillFromResult($q);
			$access = $forum->LoggedUsersAccess($forumUser, $user->User->Id);
		}

		if ($access > Forum::NO_ACCESS) {
			echo "<li>";
			$forum->DoPrint("/forum", $yesterday);
		}
	}
	echo "</ul>";
	$q->Release();

	Foot();
?>