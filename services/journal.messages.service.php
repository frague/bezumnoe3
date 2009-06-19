<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

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
	if ($journal->IsEmpty()) {
		exit;
	}

	$record = new JournalRecord();
	$search = MakeKeywordSearch(trim(substr(UTF8toWin1251($_POST["SEARCH"]), 0, 1024)), $record->SearchTemplate);
	if ($search) {
		$amount = 10;
		$journal->TotalCount = 10;
	}

	$access = Forum::NO_ACCESS;
	if ($user->IsSuperAdmin()) {
		$access = Forum::FULL_ACCESS;
	} else {
		$access = $journal->GetAccess($user->User->Id);
	}
	
	$q = $record->GetJournalTopics($access, $from, $amount, $journal->Id, $search);

	$result = "this.data = [";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$record->FillFromResult($q);
		$result .= ($i ? "," : "").$record->ToJs($search);
	}
	$result .= "];";

	$result .= "this.Total=".$journal->TotalCount.";";

	echo $result;

?>