<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty() || !$user->IsAdmin()) {
		exit();	// TODO: Implement client functionality
	}

	$ban = new BannedAddress();

	switch ($go) {
		case "save":
			$ban->FillFromHash($_POST, $user->User->Login);
			$error = $ban->SaveChecked();
			if ($error) {
				echo JsAlert($error, 1);
			} else {
				echo JsAlert("Изменения сохранены.");

				// Write log
				LogAddressBan($user_id, $user->User->Login, $ban->Content, $ban->Bans->GetList(), $ban->Comment);
			}
			break;
		case "edit":
			if ($id) {
				$ban->Id = $id;
				$ban->Retrieve();
				if (!$ban->IsEmpty()) {
					echo "this.banData=".$ban->ToJsProperties($user).";";
				}
			}
			break;
		case "delete":
			if ($id) {
				$ban->Id = $id;
				$ban->Retrieve();

				// Write log
				LogAddressBanEnd($user_id, $user->User->Login, $ban->Content, $ban->Bans->GetList());

				$ban->Delete();
				echo JsAlert("Запрет снят.");
			}
			break;
	}

	echo "this.data=[";
	$q = $ban->GetByCondition("1 ORDER BY ".BannedAddress::BAN_ID." DESC");

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$ban->FillFromResult($q);
		echo ($i ? "," : "").$ban->ToJs();
	}
	echo "];";
	$q->Release();

?>
