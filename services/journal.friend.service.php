<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	$journal = new Journal();
	$journal->GetByUserId($user->User->Id);
	if ($journal->IsEmpty()) {
		echo AddJsAlert("� ������������ ��� �������!", 1);
		exit;
	}

	$friendId = round($_POST["user_id"]);

	$journalFriend = new JournalFriend();

	$friend = new User();
	if ($friendId > 0 && $friend->UserDoesExist($friendId)) {

		$journalFriend->UserId = $friendId;
		$journalFriend->ForumId = $journal->Id;

		if (!$state) {
			if ($journalFriend->Save()) {
				echo AddJsAlert("������������� ������ ��������.");
			} else {
				echo AddJsAlert("������ ��� � ������ ������!", 1);
			}
		} else {
			if ($journalFriend->Delete()) {
				echo AddJsAlert("������������� ������ �����.");
			} else {
				echo AddJsAlert("������a ��� � ������ ������!", 1);
			}
		}
	}

	/* Show user friendly journals */
	echo "var userlist=[";
	$q = $journalFriend->GetByJournalId($journal->Id);
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$journalFriend->FillFromResult($q);
		echo ($i ? "," : "").$journalFriend->ToJs($q->Get(User::LOGIN));
	}
	echo "]";
	$q->Release();

?>