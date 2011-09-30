<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once $root."inc/helpers/like_buttons.helper.php";
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

	$alias = $settings->Alias;

	// Checking if journal is protected and logged user has access to it
	$access = 1 - $journal->IsProtected;
	if ($someoneIsLogged) {
		$access = $journal->GetAccess($user->User->Id);
	}
	
	if ($access == Journal::NO_ACCESS) {
		ErrorPage("У вас нет доступа к журналу.", "Владелец журнала ограничил к нему доступ.");
		die;
	}

	JournalRating($journal->Id);

//	Etag removed to prevent authorized session caching
//	AddEtagHeader(strtotime($record->UpdateDate));

	$meta_description = MetaContent($record->Title." - ".$record->Content);

	$buttons = FillButtonObjects($journal->Title, $record->Title, "", $record->GetImageUrl());
	
	Head("Комментарии к &laquo;".$record->Title."&raquo;", "forum.css", "forum.js", "", false, "Комментарии", $buttons);
	require_once $root."references.php";

	$postAccess = ($access >= Journal::FRIENDLY_ACCESS);

	echo $record->ToPrint($author);

	if (!$record->IsCommentable) {
		echo Error("Комментарии к данному сообщению отключены.");
		Foot();
		die;
	}

	echo "<div class=\"Likes\">";
	echo GetButtonsMarkup($journal->Title, $record->Title, "", $record->GetImageUrl());
	echo "</div>\n";

	?>
	
	<ul style="margin-top:10px"> <strong>Вернуться</strong>
		<li> к сообщению &laquo;<?php echo $record->ToLink(100, $alias); ?>&raquo;<br />
		<li> к журналу &laquo;<?php echo $journal->GetLink($alias, 0, false); ?>&raquo;
	</ul>

	<h3 style='clear:both'>Комментарии:</h3><?php

	$answers = $record->AnswersCount - ($access == Journal::FULL_ACCESS ? 0 : $record->DeletedCount);

	$comment = new JournalComment();
	$q = $comment->GetByIndex(
		$record->ForumId, 
		$access,
		$record->Index."_", 
		$from * $messagesPerPage, 
		$messagesPerPage);

	echo "<a name='c'></a>";
	echo "<div class='NewThread'>";
	echo "<a href='javascript:void(0)' id='replyLink' onclick='ForumReply(this,".$record->Id.",".$journal->Id.")'>Новый комментарий</a>";
	echo "</div>";

	echo "<ul class='Thread'>";
	$comments = $q->NumRows();
	if ($comments > 0) {
		for ($i = 0; $i < $comments; $i++) {
			$q->NextResult();

			$record->FillFromResult($q);
		
			$avatar = $q->Get(Profile::AVATAR);
			$alias = $q->Get(JournalSettings::ALIAS);
			$lastMessageDate = $q->Get(JournalSettings::LAST_MESSAGE_DATE);

			//echo "<a name='cm".$record->Id."'></a>";
			echo $record->ToExtendedString($level, $avatar, ($lastMessageDate ? $alias : ""), $user, $yesterday);
			$level = $record->Level;
		}
		$q->Release();
	
		for ($i = 0; $i < $level + 1; $i++) {
			echo "</ul>";
		}

		$pager = new Pager($answers, $messagesPerPage, $from, $journal->BasePath().$alias."/post".round($record_id)."/comments/");
		echo $pager;
	} else {
		echo "</ul>";
	}

	include $root."inc/ui_parts/post_form.php";

	Foot();

?>
