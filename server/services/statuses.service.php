<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty() || !$user->IsSuperAdmin()) {
		die();	// TODO: Implement client functionality
	}

	$id = round($_POST[Status::STATUS_ID]);
	$status = new Status();

	switch ($go) {
		case "save":
			$status->FillFromHash($_POST);
			$error = $status->SaveChecked();
			if ($error) {
				echo JsAlert($error, 1);
			} else {
				echo JsAlert("Изменения сохранены.");
			}
			break;
		case "delete":
			if ($id) {
				$status->Id = $id;
				$status->Retrieve();
				if (!$status->IsEmpty()) {
					// Getting standard status with same level of rights
					$altStatus = new Status();
					$altStatus->FillByCondition("t1.".Status::RIGHTS."=".$status->Rights." AND t1.".Status::IS_SPECIAL."=0");
					if (!$altStatus->IsEmpty()) {
						$altStatus->GetNewbie();
					}
					$q = $db->Query("UPDATE ".User::table." SET ".User::STATUS_ID."=".$altStatus->Id." WHERE ".User::STATUS_ID."=".$status->Id);

					$status->Delete();
					echo JsAlert("Статус удалён.");
				}
			}
			break;
	}

	echo "this.data=[";
	$q = $status->GetByCondition("t1.".Status::IS_SPECIAL."=1");

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$status->FillFromResult($q);
		echo ($i ? "," : "").$status->ToJs();
	}
	echo "];";
	$q->Release();

?>