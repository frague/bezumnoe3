<?php 

	$root = "../";
	require_once $root."server_references.php";
	require_once "forum.template.php";

	$yesterday = DateFromTime(time() - 60*60*24);	// Yesterday
	$thread_id = round($_GET[ForumRecord::ID_PARAM]);
	$from = round($_GET["from"]);

	$record = new ForumRecord($thread_id);
	$record->Retrieve();

	if ($record->IsEmpty()) {
		DieWith404();
	}

	$forum = new Forum($record->ForumId);
	$forum->Retrieve();
	if ($forum->IsEmpty()) {
		DieWith404();
	}

	$meta_description = MetaContent($record->Title." - ".$record->Content);

	Head($record->Title, array("forum.css", "jqueryui.css"), "forum.js", "", "", "Форумы");

	require_once $root."references.php";

	$access = 1 - $forum->IsProtected;
	if ($someoneIsLogged) {
		$access = $forum->GetAccess($user->User->Id);
	}

	$forum->DoPrint("/forum");

	$messagesPerPage = 20;
	$level = 0;

	$answers = $record->AnswersCount - ($forumAccess ? 0 : $record->DeletedCount);
	// $forumId, $access, $index, $from, $amount
	$q = $record->GetByIndex(
		$record->ForumId, 
		$access,
		$record->GetTopicIndex(), 
		$from * $messagesPerPage, 
		$messagesPerPage);

//	$result = ($forum->IsProtected ? "" : "<style>#IsProtected {display:none;}</style>");
	$result.= "<ul class='Thread'>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();

		$record->FillFromResult($q);
		
		$avatar = $q->Get(Profile::AVATAR);
		$alias = $q->Get(JournalSettings::ALIAS);
		$lastMessageDate = $q->Get(JournalSettings::LAST_MESSAGE_DATE);

		$result.= $record->ToExtendedString($level, $avatar, ($lastMessageDate ? $alias : ""), $user, $yesterday);
		$level = $record->Level;
	}
	$q->Release();
	
	for ($j = 0; $j < $level + 1; $j++) {
		$result.= "</ul>";
	}

	if (!$i) {
		$result.= "<div class='Error'>Сообщения не найдены!</div>";
	}

	$result.= new Pager($answers, $messagesPerPage, $from, $forum->BasePath().$thread_id."/");

	// Printing
// Etag removed to prevent authorized session caching
//	AddEtagHeader(strtotime($record->UpdateDate));
	echo $result;
	include $root."inc/ui_parts/post_form.php";

	include $root."/inc/ui_parts/li.php";
	
	Foot();
?>
