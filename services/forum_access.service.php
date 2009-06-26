<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	$targetUserId = round($_POST["TARGET_USER_ID"]);

	if ($forum_id) {
		$forum = new ForumBase($forum_id);
		$forum->Retrieve();
	} elseif ($user->User->Id != $targetUserId) {
		$forum = new Journal();
		$forum->GetByUserId($user->User->Id);
	}

	if ($forum->IsEmpty()) {
		exit;
	}

	if ($go) {
		$access = $forum->GetAccess($user->User->Id);
		if ($access != Forum::FULL_ACCESS && !$user->IsSuperAdmin()) {
			echo AddJsAlert("������ ��������!", 1);
			exit;
		}
		/* Access granted - handle operations */
		$accessType = round($_POST["ACCESS"]);

		$friend = new User($targetUserId);
		$friend->Retrieve();
		
		if (!$friend->IsEmpty()) {
			$forumUser = new ForumUser($friend->Id, $forum->Id);
			$forumUser->Access = $accessType;

			switch ($go) {
				case "add":
					if ($forumUser->Save()) {
						echo AddJsAlert("������������ ".$friend->Login." �������� � ������.");
					} else {
						echo AddJsAlert("������������ ".$friend->Login." ��� ������ � ������!", 1);
					}
					break;
				case "delete":
					if ($forumUser->Delete()) {
						echo AddJsAlert("������������ ".$friend->Login." ����� �� ������.");
					} else {
						echo AddJsAlert("������������ ".$friend->Login." �� ������ � ������!", 1);
					}
					break;
			}
		}
	}

	if ($forum->IsEmpty()) {
		echo AddJsAlert("��������� �����/������ �� ������!", 1);
		exit;
	}

	$forumUser = new ForumUser();


	/* Show user friendly journals */
	echo "this.data=[";
	$q = $forumUser->GetByForumId($forum->Id);
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$forumUser->FillFromResult($q);
		if ($forumUser->IsFull()) {
			echo ($i ? "," : "").$forumUser->ToJs();
		}
	}
	echo "];";

	$type = "�������";
	switch ($forum->Type) {
		case ForumBase::TYPE_FORUM:
			$type = "������";
			break;
		case ForumBase::TYPE_GALLERY:
			$type = "�����������";
			break;
	}

	$owner = "";
	if ($forum->LinkedId) {
		if ($forum->LinkedId == $user->User->Id) {
			$owner = " ������������ ".$user->User->Login;
		} else {
			$ownerUser = new User($forum->LinkedId);
			$ownerUser->Retrieve();
			if (!$ownerUser->IsEmpty()) {
				$owner = " ������������ ".$ownerUser->Login;
			}
		}
	}

	echo "this.TITLE='".$type.$owner.":<br>&laquo;".JsQuote($forum->Title)."&raquo;';";
	echo "this.DESCRIPTION='".JsQuote($forum->Description)."';";

?>