<?php
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
				$forum->FillByUserId($targetUser->Id);
			}
		}
	}

	if ($forum->IsEmpty()) {
		echo JsAlert("Журнал не найден.", 1);
		die;
	}

	$access = $forum->GetAccess($user->User->Id);
	if ($access != Forum::FULL_ACCESS && $access != Forum::READ_ADD_ACCESS && $access != Forum::FRIENDLY_ACCESS) {
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
		$q->Release();
	}

	// Search conditions
	$record = new ForumRecordBase();
	$condition = MakeSearchCriteria("DATE", ForumRecordBase::DATE, "SEARCH", $record->SearchTemplate);

	$q = $record->GetForumThreads($forum->Id, $access, $from, $amount, $condition, true);
	$result = "this.data=[";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$record->FillFromResult($q);
		$result .= ($i ? "," : "").$record->ToJs($search);
	}
	$result .= "];";
	$q->Release();

	if ($condition) {
		$forum->TotalCount = $record->GetForumThreadsCount($forum->Id, $access, $condition);
	}
	$result .= "this.Total=".$forum->TotalCount.";";
	$result .= "this.FORUM_ID=".$forum->Id.";";

	$alias = "";
	if ($forum->IsJournal()) {
		$settings = new JournalSettings();
		$settings->GetByForumId($forum->Id);
		if (!$settings->IsEmpty()) {
			$alias = $settings->Alias;
		}
	}
	$result .= "this.ALIAS=\"".$alias."\";";

	if ($forum->IsGallery()) {
		$result .= "this.GALLERY=\"".JsQuote($forum->Description)."\";";
	}

	echo $result;

?>