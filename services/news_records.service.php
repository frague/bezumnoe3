<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty() || !$user->IsAdmin()) {
		exit();	// TODO: Implement client functionality
	}

	$record = new NewsRecord();
	switch ($go) {
		case "save":
			$record->FillFromHash($_POST);
			$record->AuthorId = $user->User->Id;
			$record->Save();
			echo JsAlert("Запись сохранена.");
			break;
		case "delete":
			$record->FillFromHash($_POST);
			if (!$record->IsEmpty()) {
				$record->Retrieve();
				if (!$record->IsEmpty()) {
					$record->Delete();
					echo JsAlert("Запись удалена.");
				}
			}
			break;
	}


	$condition = "";

/*	// Dates condition
	$d = $_POST["DATE"];
	if ($d) {
		$t = ParseDate($d);
		if ($t !== false) {
			$condition = "t1.".AdminComment::DATE." LIKE '".DateFromTime($t, "Y-m-d")."%' ";
		}
	}

	// Search keywords
	$search = MakeKeywordSearch(trim(substr(UTF8toWin1251($_POST["SEARCH"]), 0, 1024)), $comment->SearchTemplate);
	if ($search) {
		$condition .= ($condition ? " AND " : "").$search;
	}

	if (!$condition) {
		$condition = "1";
	}*/

	echo "this.data=[";
	if ($user_id && $user_id < 0) {
		$q = $record->GetByOwnerId($user_id, $from, $amount, $condition);

		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();
			$record->FillFromResult($q);
			echo ($i ? "," : "").$record->ToJs();
		}
	}
	echo "];";
	echo "this.Total=".$record->GetRecordsCount($user_id, $condition).";";

?>