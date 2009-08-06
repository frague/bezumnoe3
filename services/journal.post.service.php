<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	$post_id = round($_POST["RECORD_ID"]);

	if (!$user || $user->IsEmpty() || !$user_id) {
		exit;
	}

	$forum = new ForumBase();
	$record = new ForumRecordBase();
	if ($post_id) {
		$record->GetById($post_id);
		if (!$record->IsEmpty()) {
			$forum->GetById($record->ForumId);
		} else {
			echo JsAlert("���� �� ������!", 1);
			die;
		}
	} elseif ($forum_id) {
		$forum->GetById($forum_id);
	} else {
		$forum->GetByUserId($user->User->Id);
	}

	if ($forum->IsEmpty()) {
		echo JsAlert("������ �� ������!", 1);
		die;
	}

	$access = $forum->GetAccess($user->User->Id);
	if ($access != Forum::FULL_ACCESS && $access != Forum::READ_ADD_ACCESS) {
		echo JsAlert("� ��� ��� ������� � ���������� �������!", 1);
		die;
	}
	
	if (!$post_id && $go != "save") {
		exit;
	}

	switch ($go) {
		case "save":
			if (!$user->IsSuperAdmin() && $post_id && $access != Forum::FULL_ACCESS && $record->UserId != $user->User->Id) {
				echo JsAlert("�� ������ ������������� ������ ����������� ���������!", 1);
				die;
			}

			$record->FillFromHash($_POST);

			$record->ForumId = $forum->Id;
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
			if (!$user->IsSuperAdmin() && $access != Forum::FULL_ACCESS && $record->UserId != $user->User->Id) { 
				echo JsAlert("������������ ���� ��� ��������!", 1);
				die;
			}
			echo JsAlert("��������� &laquo;".$record->Title."&raquo; �������.");
			$record->GetByCondition(
					ForumRecord::INDEX." LIKE '".substr($record->Index, 0, 4)."%' AND
					".ForumRecord::FORUM_ID."=".$forum->Id,
					$record->DeleteThreadExpression()
				);
		default:
			echo "this.data=".$record->ToFullJs();
			break;
	}

?>