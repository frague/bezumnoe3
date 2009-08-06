<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "journal.template.php";

	$messagesPerPage = 20;
	$yesterday = DateFromTime(time() - 60*60*24);	// Yesterday

	$alias = substr(LookInRequest(JournalSettings::PARAMETER), 0, 20);
	$record_id = round(LookInRequest(JournalRecord::ID_PARAM));
	$from = round($_GET["from"]);

	// No record ID specified
	if ($record_id <= 0) {
		DieWith404();
	}

	$record = new JournalRecord($record_id);
	$record->Retrieve();

	// Record not found
	if ($record->IsEmpty()) {
		DieWith404();
	}

	$journal = new Journal($record->ForumId);
	$journal->Retrieve();
	if ($journal->IsEmpty()) {
		// Journal not found
		DieWith404();
	}

	$settings = new JournalSettings();
	$settings->GetByForumId($record->ForumId);

	if ($settings->IsEmpty() || ($alias && $settings->Alias != $alias)) {
		DieWith404();
	}

	// Checking if journal is protected and logged user has access to it
	$access = 1 - $journal->IsProtected;
	if ($someoneIsLogged) {
		$access = $journal->GetAccess($user->User->Id);
	}
	
	if ($access == Journal::NO_ACCESS) {
		Head("Ошибка", "forum.css", "forum.js");
		error("У вас нет доступа к журналу.");
		Foot();
		die;
	}

	Head("Комментарии к &laquo;".$record->Title."&raquo;", "forum.css", "forum.js");
	require_once $root."references.php";

	$postAccess = ($access >= Journal::READ_ADD_ACCESS);

	echo $record->ToPrint($author);

	if (!$record->IsCommentable) {
		echo "<div class='Error'>Комментарии к данному сообщению отключены.</div>";
		Foot();
		die;
	}

	echo "<h3 style='clear:both'>Комментарии:</h3>";

	$answers = $record->AnswersCount - ($forumAccess ? 0 : $record->DeletedCount);

	$comment = new JournalComment();
	$q = $comment->GetByIndex(
		$record->ForumId, 
		$access,
		$record->Index, 
		$from * $messagesPerPage, 
		$messagesPerPage);

	echo "<div class='NewThread'>";
	echo "<a href='javascript:void(0)' id='replyLink' onclick='ForumReply(this,".$record->Id.",".$journal->Id.")'>Новый комментарий</a>";
	echo "</div>";

	echo "<ul class='Thread'>";
	if ($q->NumRows()) {
		$q->NextResult();

		for ($i = 1; $i < $q->NumRows(); $i++) {
			$q->NextResult();

			$record->FillFromResult($q);
		
			$avatar = $q->Get(Profile::AVATAR);
			$alias = $q->Get(JournalSettings::ALIAS);
			$lastMessageDate = $q->Get(JournalSettings::LAST_MESSAGE_DATE);

			echo "<a name='cm".$record->Id."'></a>";
			echo $record->ToExtendedString($level, $avatar, ($lastMessageDate ? $alias : ""), $user, $yesterday);
			$level = $record->Level;
		}
	
		for ($i = 0; $i < $level + 1; $i++) {
			echo "</ul>";
		}

		$pager = new Pager($answers, $messagesPerPage, $from);
		echo $pager;
	} else {
		echo "</ul>";
	}

	include $root."inc/ui_parts/post_form.php";

	Foot();

?>
