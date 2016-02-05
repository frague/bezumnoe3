<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user_id || !$user || $user->IsEmpty()) {
		exit();	// TODO: Implement client functionality
	}

	$tabId = $_POST["tab_id"];

	if ($user_id == $user->User->Id || $user->IsAdmin()) {
		if ($user_id == $user->User->Id) {
			$targetUser = $user->User;
		} else {
			$targetUser = new User($user_id);
			$targetUser->Retrieve();
		}
		if ($targetUser->IsEmpty()) {
			return;
		}

		//------------------------------------------------

		$settings = new Settings();
		$settings->GetByUserId($user_id);
		if (!$settings->IsEmpty()) {
			switch ($go) {
				case "save":
					/* Saving profile */
					$settings->FillFromHash($_POST);
					$errors = $settings->Validate();
					if ($errors) {
						echo JsAlert("Настройки не были сохранёны по следующим причинам:<ul>".$errors."</ul>", 1);
					} else {
						$settings->Save();
						echo JsAlert("Настройки успешно сохранёны.");
					}
					break;
			}
			echo "obj.FillFrom(".$settings->ToJsProperties($targetUser).");";
		}
	}

?>
