<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty() || !$user_id) {
		exit;
	}

	$search = substr(UTF8toWin1251($_POST["SEARCH"]), 0, 1024);

	$post_id = round($_POST["RECORD_ID"]);

	$record = new JournalRecord($post_id);
	$record->Retrieve();
	if ($record->IsEmpty()) {
		echo JsAlert("Запись не найдена!", 1);
		die;
	}

	$forum = new ForumBase($record->ForumId);
	$forum->Retrieve();
	if ($forum->IsEmpty()) {
		echo JsAlert("Журнал (форум) не существует!", 1);
		die;
	}

	$access = $forum->GetAccess($user->User->Id);
	
	if ($access != Forum::READ_ADD_ACCESS && $access != Forum::FULL_ACCESS) {
		echo JsAlert("Нет доступа к комментариям!", 1);
		die;
	}

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
				".ForumRecord::FORUM_ID."=".$forum->Id,
				$comment->DeleteThreadExpression()
			);
			$comment->UpdateAnswersCount();
			echo JsAlert("Комментарий удалён.");
		};
	}

	$q = $comment->GetByIndex($forum->Id, $access, $record->Index, $from, $amount);

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
	$q->Release();

	// Comments total number
	$q = $record->GetByCondition("", $record->CountAnswersExpression());
	if ($q->NumRows()) {
		$q->NextResult();
		$Total = $q->Get("TOTAL") - $q->Get("DELETED") - 1;
		$result .= "this.Total=".$Total.";";
		$q->Release();
	}

	echo $result;
?>