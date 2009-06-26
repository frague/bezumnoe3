<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty() || !$forum_id) {
		exit;
	}

	$journal = new ForumBase($forum_id);
	$journal->Retrieve();

	if ($journal->IsEmpty()) {
		echo JsAlert("Журнал не найден!", 1);
		exit;
	}

	$access = $journal->GetAccess($user->User->Id);
	if ($access != Forum::FULL_ACCESS && !$user->IsSuperAdmin()) {
		echo JsAlert("Нет доступа к настройкам журнала!", 1);
		exit;
	}

	$settings = new JournalSettings();
	$settings->GetByForumId($journal->Id);

	if ($go == "save") {
		$journal->Title = trim(UTF8toWin1251($_POST[Journal::TITLE]));
		if (!$journal->IsGallery()) {
			$journal->Description = trim(UTF8toWin1251($_POST[Journal::DESCRIPTION]));
		}
		$journal->Save();
		echo JsAlert("Название и описание журнала сохранены.");

		if ($settings->IsEmpty()) {
			$settings->ForumId = $journal->Id;
		}

		if ($journal->IsJournal()) {
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
	}	

	echo "this.data=".$settings->ToJs($journal->Title, $journal->Description).";";
	echo "this.type='".$journal->Type."';";

?>