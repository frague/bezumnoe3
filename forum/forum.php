<?php 

	$root = "../";
	require_once $root."server_references.php";
	require_once "forum.template.php";

	$forum_id = round($_GET[Forum::ID_PARAM]);
	$from = round($_GET["from"]);

	$forum = new Forum($forum_id);
	$forum->Retrieve();
	if ($forum->IsEmpty()) {
		$forum->FillByCondition("t1.".Forum::TYPE."='".Forum::TYPE_FORUM."'");
	}

	$yesterday = DateFromTime(time() - 60*60*24);	// Yesterday

	Head($forum->Title, "forum.css", "forum.js");
	require_once $root."references.php";

	$postAccess = IsPostingAllowed();
	
	$forum->DoPrint("forum.php");

	$forumAccess = GetForumAccess($forum);
	$threadsPerPage = 20;

	$record = new ForumRecord();
	$q = $record->GetForumThreads(
		$forum->Id,
		$user,
		$from * $threadsPerPage, 
		$threadsPerPage,
		$forumAccess);

	echo "<style>#buttonCite".($forum->IsHidden ? "" : ", #IsProtected")." {display:none;}</style>";
	echo "<div class='NewThread'>";
	echo "<a href='javascript:void(0)' id='replyLink' onclick='ForumReply(this,0,".$forum->Id.")'>Новая тема</a>";
	echo "</div>";
	
	echo "<ul class='Threads'>";

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();

		$record->FillFromResult($q);
		echo $record->ToPrint("thread.php", 0, $yesterday);
	}
	echo "</ul>";

	$threads = $record->GetForumThreadsCount($forum_id, $user);
	$pager = new Pager($threads, $threadsPerPage, $from);
	echo $pager;

	include $root."inc/ui_parts/post_form.php";

	Foot();
?>