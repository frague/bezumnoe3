<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "gallery.template.php";

	$messagesPerPage = 20;

	$record_id = round(LookInRequest(GalleryPhoto::ID_PARAM));
	$from = round($_GET["from"]);

	if ($record_id <= 0) {
		DieWith404();
	}

	$record = new GalleryPhoto($record_id);
	$record->Retrieve();

	if ($record->IsEmpty()) {
		DieWith404();
	}

	$gallery = new Gallery($record->ForumId);
	$gallery->Retrieve();
	if ($gallery->IsEmpty()) {
		DieWith404();
	}

	$meta_description = MetaContent($record->Title);


	AddEtagHeader(strtotime($record->UpdateDate));
	Head($record->Title, "forum.css", "forum.js");
	require_once $root."references.php";

	$gallery->DoPrint(true);

	echo "<div id=\"Photo\">";
	echo "	".$record->GetPreviousLink()."<div class=\"Previous\" style=\"height:".$record->GetHeight($gallery->Description)."px;\"></div></a>";
	echo "	".$record->GetNextLink()."<div class=\"Next\" style=\"height:".$record->GetHeight($gallery->Description)."px;\"></div></a>";
	echo $record->ToPrint($gallery->Description, 0);
	echo "</div>";

	if ($record->IsCommentable) {
		echo "<h3>Комментарии:</h3>";

		$answers = $record->AnswersCount - $record->DeletedCount;

		$comment = new GalleryComment();
		$q = $comment->GetByIndex(
			$record->ForumId, 
			$user,
			$record->Index."_", 
			$from * $messagesPerPage, 
			$messagesPerPage,
			1);


		echo "<div class='NewThread'>";
		echo "<a href='javascript:void(0)' id='replyLink' onclick='ForumReply(this,".$record->Id.",".$gallery->Id.")'>Новый комментарий</a>";
		echo "</div>";

		echo "<ul class='Thread'>";
		$comments = $q->NumRows();
		if ($comments) {
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

			$pager = new Pager($answers, $messagesPerPage, $from);
			echo $pager;
		} else {
			echo "</ul>";
		}
	} else {
		echo "<div class='Error'>Комментарии к фотографии отключены.</div>";
	}

	include $root."inc/ui_parts/post_form.php";

	Foot();

?>
