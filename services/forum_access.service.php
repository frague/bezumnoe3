<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	$targetUserId = round($_POST["TARGET_USER_ID"]);
	$targetForumId = round($_POST["TARGET_FORUM_ID"]);

	if ($forum_id) {
		$forum = new ForumBase($forum_id);
		$forum->Retrieve();
	} else {
		echo AddJsAlert("������ �� ������.", 1);
		exit;
	}
	
	if ($forum->IsEmpty()) {
		echo AddJsAlert("��������� �����/������ �� ������!", 1);
		exit;
	}

	if ($forum->GetAccess($user->User->Id) != Forum::FULL_ACCESS) {
		echo AddJsAlert("��� �������!", 1);
		exit;
	}

	$journal = new Journal();
	if ($targetForumId) {
		$journal->GetById($targetForumId);
		if ($journal->IsEmpty()) {
			exit;
		}
	}

	if ($go) {
		if ($targetForumId) {
			// Friendly journals operations
			$friend = new JournalFriend($forum->Id, $journal->Id);
			switch ($go) {
				case "add":
					if ($friend->Save()) {
						echo AddJsAlert("������������� ������ &laquo;".$journal->Title."&raquo; ��������.");
					} else {
						echo AddJsAlert("������ &laquo;".$journal->Title."&raquo; ��� � ������ ������.", 1);
					}
					break;
				case "delete":
					if ($forumUser->Delete()) {
						echo AddJsAlert("������ &laquo;".$journal->Title."&raquo; ����� �� ������ ������.");
					} else {
						echo AddJsAlert("������ &laquo;".$journal->Title."&raquo; �� ������ � ������ ������.", 1);
					}
					break;
			}
		} else {
			// Forum users (B/W lists)
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
							echo AddJsAlert("������������ ".$friend->Login." ��� ������ � ���� �� �������<br>��� �������� ���������� ������!", 1);
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
	}

	$forumUser = new ForumUser();


	/* Show user black/white list users */
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

	/* Show user friendly journals */
	$friend = new JournalFriend();
	echo "this.friends=[";
	$q = $friend->GetByForumId($forum->Id);
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$friend->FillFromResult($q);
		if ($friend->IsFull()) {
			echo ($i ? "," : "").$friend->ToJs($q->Get(Forum::TITLE), $q->Get(User::LOGIN));
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

//	echo "this.TITLE='".$type.$owner.":<br>&laquo;".JsQuote($forum->Title)."&raquo;';";
//	echo "this.DESCRIPTION='".JsQuote($forum->Description)."';";
//	echo "this.type='".$forum->Type."';";

?>