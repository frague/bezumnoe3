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


	$condition = MakeSearchCriteria("SEARCH_DATE", NewsRecord::DATE, "SEARCH", $record->SearchTemplate);

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