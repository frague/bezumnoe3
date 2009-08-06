<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty() || !$user_id) {
		exit;
	}

	if ($forum_id) {
		$forum = new ForumBase($forum_id);
		$forum->Retrieve();
	} else {
		$forum = new Journal();
		if ($user_id) {
			$targetUser = new User($user_id);
			$targetUser->Retrieve();
			if (!$targetUser->IsEmpty()) {
				$forum->GetByUserId($targetUser->Id);
			}
		}
	}

	if ($forum->IsEmpty()) {
		echo JsAlert("Журнал не найден.", 1);
		die;
	}

	$access = $forum->GetAccess($user->User->Id);
	if ($access != Forum::FULL_ACCESS && $access != Forum::READ_ADD_ACCESS) {
		echo JsAlert("У вас нет доступа к указанному журналу!", 1);
		die;
	}

	if (!$forum_id && $user_id == $user->User->Id) {
		/* Reading allowed forums */
		$fu = new ForumUser();
		$q = $fu->GetUserForums($user->User->Id);
		echo "this.forums=[";
		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();
			echo ($i ? "," : "")."new fldto(".
$q->Get(ForumUser::FORUM_ID).",\"".
$q->Get(ForumUser::ACCESS)."\",\"".
JsQuote($q->Get(Forum::TITLE))."\",\"".
substr($q->Get(Forum::TYPE), 0, 1)."\",\"".
JsQuote($q->Get(User::LOGIN))."\")";
		}
		echo "];";
	}

	$record = new ForumRecordBase();
	$search = MakeKeywordSearch(trim(substr(UTF8toWin1251($_POST["SEARCH"]), 0, 1024)), $record->SearchTemplate);
	if ($search) {
		$amount = 10;
		$forum->TotalCount = 10;
	}

	$q = $record->GetForumThreads($forum->Id, $access, $from, $amount);
	$result = "this.data=[";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$record->FillFromResult($q);
		$result .= ($i ? "," : "").$record->ToJs($search);
	}
	$result .= "];";

	$result .= "this.Total=".$forum->TotalCount.";";
	$result .= "this.FORUM_ID=".$forum->Id.";";

	echo $result;

?>