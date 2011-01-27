<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty()) {
		die();	// TODO: Implement client functionality
	}

	$id = round($_POST[UserOpenId::USER_OPENID_ID]);
	$userOpenId = new UserOpenId();

	switch ($go) {
		case "save":
			$userOpenId->FillFromHash($_POST);
			$error = $userOpenId->SaveChecked();
			if ($error) {
				echo JsAlert($error, 1);
			} else {
				echo JsAlert("Изменения сохранены.");
			}
			break;
		case "delete":
			if ($id) {
				$userOpenId->Id = $id;
				$userOpenId->Retrieve();
				if (!$userOpenId->IsEmpty()) {
					// Getting standard status with same level of rights
					//$userOpenId->Delete();
					echo JsAlert("OpenID удалён.");
				}
			}
			break;
	}

	echo "OpenIdProviders={";
	$p = new OpenIdProvider();
	$q = $p->GetAll();
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$p->FillFromResult($q);
		echo ($i ? "," : "").$p->Id.": ".$p->ToJs();
	}
	echo "};";

	echo "this.data=[";
	$q = $userOpenId->GetForUser($user_id);
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$userOpenId->FillFromResult($q);
		echo ($i ? "," : "").$userOpenId->ToJs();
	}
	echo "];";
	$q->Release();

?>