<?php

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty()) {
		die();	// TODO: Implement client functionality
	}

	$id = round($_POST[UserOpenId::USER_OPENID_ID]);
	$userOpenId = new UserOpenId();
	$p = new OpenIdProvider();

	switch ($go) {
		case "save":

			$userOpenId->FillFromHash($_POST);
			if (!round($userOpenId->OpenIdProviderId)) {
				JsAlert("Провайдер OpenID не указан.", 1);
				break;
			}

			$p->GetById(round($userOpenId->OpenIdProviderId));
			if ($p->IsEmpty()) {
				JsAlert("Некоректное указание провайдера OpenID.", 1);
				break;
			}

			$error = $userOpenId->SaveChecked();
			if ($error) {
				echo JsAlert($error, 1);
			} else {
				echo JsAlert("Изменения сохранены.");
				SaveLog("OpenID: <b>".$p->MakeUrl($userOpenId->Login)."</b>", $user->User->Id, $user->User->Login, AdminComment::SEVERITY_WARNING);
			}
			break;
		case "delete":
			if ($id) {
				$userOpenId->Id = $id;
				$userOpenId->Retrieve();
				if (!$userOpenId->IsEmpty()) {
					$userOpenId->Delete();
					echo JsAlert("OpenID удалён.");
				}
			}
			break;
	}

	echo "OpenIdProviders={";
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