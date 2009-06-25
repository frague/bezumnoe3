<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	$journal = new Journal();
	$journal->GetByUserId($user->User->Id);

	$settings = new JournalSettings();
	$settings->GetByUserId($user->User->Id);

	if ($go == "save") {
		$journal->Title = trim(UTF8toWin1251($_POST[Journal::TITLE]));
		$journal->Description = trim(UTF8toWin1251($_POST[Journal::DESCRIPTION]));
		$journal->Save();
		echo JsAlert("Название и описание журнала сохранены.");

		if ($settings->IsEmpty()) {
			$settings->UserId = $user->User->Id;
		}

		$hasErrors = false;
		$alias = trim(UTF8toWin1251($_POST[JournalSettings::REQUESTED_ALIAS]));

		if (strlen($alias) > 20) {
			echo JsAlert("Алиас длиннее 20 символов.", 1);
			$hasErrors = true;
		}

		if (!ereg("^[a-zA-Z0-9_]*$", $alias)) {
			echo JsAlert("Алиас может состоять только из символов латинского алфавита, цифр и знака \"_\".", 1);
			$hasErrors = true;
		}

		if (!$hasErrors) {
			$settings->RequestedAlias = $alias;
			$settings->Save();
			echo JsAlert("Настройки журнала сохранены.");
		}
	}	

	echo "this.data=".$settings->ToJs($journal->Title, $journal->Description).";";

?>