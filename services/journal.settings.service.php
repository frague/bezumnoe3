<?
	require_once "base.service.php";

	function FillForumData($journal) {
	  global $user;

		$journal->Title = trim(UTF8toWin1251($_POST[Journal::TITLE]));
		$journal->IsProtected = Boolean($_POST[Journal::IS_PROTECTED]);
		
		if ($user->IsAdmin()) {
			$journal->IsHidden = Boolean($_POST[Journal::IS_HIDDEN]);
		}

		if (!$journal->IsGallery()) {
			$journal->Description = trim(UTF8toWin1251($_POST[Journal::DESCRIPTION]));
		}
	}

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	if (!$forum_id && $go == "save") {	// New journal
	    $journal = new Journal();
	    $journal->GetByUserId($user->User->Id);
	    if (!$journal->IsEmpty()) {
			echo JsAlert("У вас уже есть журнал!", 1);
		 	exit;
	    }
	    
	    $error = "";

	    $alias = $_POST["REQUESTED_ALIAS"];
	    if (!$alias) {
			$error = "При создании журнала необходимо указать алиас!<br>";
	    }
	    if (!eregi("^[a-z0-9_.]+$", $alias)) {
			$error .= "Алиас должен состоять из символов латинского алфавита, цифр и знаков '.' и '_'!<br>";
	    }

		$settings = new JournalSettings();
		if ($alias) {
		    $settings->GetByAlias($alias);
		    if (!$settings->IsEmpty()) {
				$error .= "Журнал с алиасом &laquo;".$alias."&raquo;! уже существует!<br>";
	    	}
	    }

	    FillForumData($journal);

	    if ($error) {
			echo "this.data=".$settings->ToJs($journal->Title, $journal->Description).";";
			echo JsAlert($error, 1);
			exit;
	    }

	    $journal->LinkedId = $user->User->Id;
		$journal->Save();
		
		$settings->ForumId = $journal->Id;
	    $settings->Alias = $alias;

		$template = new JournalTemplate();
		$skin = new JournalSkin();
		$settings->SkinTemplateId = $skin->GetDefaultTemplateId();
		$settings->Save();

		$template = new JournalTemplate($settings->SkinTemplateId);
		$template->Retrieve();
		$template->ForumId = $journal->Id;
		$template->Id = -1;
		$template->Save();

		echo JsAlert("Создан новый журнал!", 1);	// TODO: Write log
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
		FillForumData($journal);

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

	echo "this.data=".$settings->ToJs($journal->Title, $journal->Description, $journal->IsProtected, $journal->IsHidden).";";
	echo "this.type='".$journal->Type."';";

?>