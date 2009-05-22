<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	$post_id = round($_POST["RECORD_ID"]);

	if (!$post_id && $go != "save") {
		exit;
	}

	if (!$user || $user->IsEmpty() || !$user_id) {
		exit;
	}

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
		exit;
	}

	$journal = new Journal();
	$journal->GetByUserId($targetUser->Id);
	if (!$journal->IsFull()) {
		echo JsAlert("������ ������������ �� ������!", 1);
		die;
	}


	$record = new JournalRecord();

	switch ($go) {
		case "save":
			if ($post_id) {
				$record->GetById($post_id);
			}

			$record->FillFromHash($_POST);
			if ($record->UserId != $targetUser->Id) {
				echo JsAlert("�������� �������� ������������!", 1);
				die;
			}

			$record->ForumId = $journal->Id;
			if ($record->IsEmpty()) {
				$record->Author = $targetUser->Login;
				$record->SaveAsTopic();
				echo JsAlert("��������� ���������.");
			} else {
				$record->Save();
				echo JsAlert("��������� ���������.");
			}
			echo "this.data=".$record->ToFullJs();

			break;
		case "delete":
		default:
			$record->GetById($post_id);

			if ($record->IsEmpty()) {
				echo JsAlert("���� �� ������!", 1);
				die;
			}
			if ($record->UserId != $targetUser->Id) { 
				echo JsAlert("�������� ����, �� ������������� ������������!", 1);
				die;
			}
			if ($go == "delete") {
				echo JsAlert("��������� &laquo;".$record->Title."&raquo; �������.");
				$record->GetByCondition(
						ForumRecord::INDEX." LIKE '".substr($record->Index, 0, 4)."%' AND
						".ForumRecord::FORUM_ID."=".$journal->Id,
						$record->DeleteThreadExpression()
					);
			} else {
				echo "this.data=".$record->ToFullJs();
			}
			break;
	}

?>