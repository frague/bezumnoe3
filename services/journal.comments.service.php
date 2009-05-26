<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty() || !$user_id) {
		exit;
	}

	$search = substr(UTF8toWin1251($_POST["SEARCH"]), 0, 1024);

	if ($user_id == $user->User->Id || $user->Status->Rights > $AdminRights) {
		if ($user_id == $user->User->Id) {
			$targetUser = $user->User;
		} else {
			$targetUser = new User($user_id);
			$targetUser->Retrieve();
		}
		if ($targetUser->IsEmpty()) {
			return;
		}
	} else {
		die;
	}

	$post_id = round($_POST["RECORD_ID"]);

	$record = new JournalRecord($post_id);
	$record->Retrieve();
	if ($record->IsEmpty()) {
		echo JsAlert("Пост не найден!", 1);
		die;
	}

	if ($record->UserId != $targetUser->Id) {
		echo JsAlert("Запрошен пост, не принадлежащий пользователю!", 1);
		die;
	}

	$journal = new Journal($record->ForumId);
	$journal->Retrieve();
	if ($journal->IsEmpty()) {
		echo JsAlert("Журнал не существует!", 1);
		die;
	}

	$isOwner = ($user->IsSuperAdmin() || $journal->LinkedId == $user->User->Id) ? 1 : 0;

	$comment = new JournalComment();
	if ($go == "delete" && $id) {
		$comment->GetById($id);
		if ($comment->IsEmpty()) {
			echo JsAlert("Комментарий не найден!", 1);
		} else if (round($comment->Index) != round($record->Index)) {
			echo JsAlert("Комментарий оставлен к другому сообщению!", 1);
		} else {
			$comment->GetByCondition(
				ForumRecord::INDEX." LIKE '".$comment->Index."%' AND
				".ForumRecord::FORUM_ID."=".$journal->Id,
				$comment->DeleteThreadExpression()
			);
			$comment->UpdateAnswersCount();
			echo JsAlert("Комментарий удалён.");
		};
	}

	$q = $comment->GetByIndex($journal->Id, $user, $record->Index, $from, $amount, $isOwner);

	$result = "this.data=[";
	$threadCount = $q->NumRows();
	if ($threadCount > 1) {
		$q->NextResult();
		for ($i = 0; $i < $threadCount - 1; $i++) {
			$q->NextResult();
			$comment->FillFromResult($q);
			$result .= ($i ? "," : "").$comment->ToJs($search);
		}
	}
	$result .= "];";

	// Comments total number
	$q = $record->GetByCondition("", $record->CountAnswersExpression());
	if ($q->NumRows()) {
		$q->NextResult();
		$Total = $q->Get("TOTAL") - $q->Get("DELETED") - 1;
		$result .= "this.Total=".$Total.";";
	}

	echo $result;
?>