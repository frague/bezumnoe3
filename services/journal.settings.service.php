<?
	require_once "base.service.php";

	function FillForumData($journal) {
		$journal->Title = trim(UTF8toWin1251($_POST[Journal::TITLE]));
		$journal->IsProtected = $_POST[Journal::IS_PROTECTED] ? 1 : 0;
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
			echo JsAlert("� ��� ��� ���� ������!", 1);
		 	exit;
	    }
	    
	    $error = "";

	    $alias = $_POST["REQUESTED_ALIAS"];
	    if (!$alias) {
			$error = "��� �������� ������� ���������� ������� �����!<br>";
	    }
	    if (!eregi("^[a-z0-9_.]+$", $alias)) {
			$error .= "����� ������ �������� �� �������� ���������� ��������, ���� � ������ '.' � '_'!<br>";
	    }

		$settings = new JournalSettings();
		if ($alias) {
		    $settings->GetByAlias($alias);
		    if (!$settings->IsEmpty()) {
				$error .= "������ � ������� &laquo;".$alias."&raquo;! ��� ����������!<br>";
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

		echo JsAlert("������ ����� ������!", 1);	// TODO: Write log
	 	exit;
	}

	$journal = new ForumBase($forum_id);
	$journal->Retrieve();

	if ($journal->IsEmpty()) {
		echo JsAlert("������ �� ������!", 1);
		exit;
	}

	$access = $journal->GetAccess($user->User->Id);
	if ($access != Forum::FULL_ACCESS && !$user->IsSuperAdmin()) {
		echo JsAlert("��� ������� � ���������� �������!", 1);
		exit;
	}

	$settings = new JournalSettings();
	$settings->GetByForumId($journal->Id);

	if ($go == "save") {
		FillForumData($journal);
/*		$journal->Title = trim(UTF8toWin1251($_POST[Journal::TITLE]));
		if (!$journal->IsGallery()) {
			$journal->Description = trim(UTF8toWin1251($_POST[Journal::DESCRIPTION]));
		}*/
		$journal->Save();
		echo JsAlert("�������� � �������� ������� ���������.");

		if ($settings->IsEmpty()) {
			$settings->ForumId = $journal->Id;
		}

		if ($journal->IsJournal()) {
			$hasErrors = false;
			$alias = trim(UTF8toWin1251($_POST[JournalSettings::REQUESTED_ALIAS]));

			if (strlen($alias) > 20) {
				echo JsAlert("����� ������� 20 ��������.", 1);
				$hasErrors = true;
			}

			if (!ereg("^[a-zA-Z0-9_]*$", $alias)) {
				echo JsAlert("����� ����� �������� ������ �� �������� ���������� ��������, ���� � ����� \"_\".", 1);
				$hasErrors = true;
			}

			if (!$hasErrors) {
				$settings->RequestedAlias = $alias;
				$settings->Save();
				echo JsAlert("��������� ������� ���������.");
			}
		}
	}	

	echo "this.data=".$settings->ToJs($journal->Title, $journal->Description, $journal->IsProtected).";";
	echo "this.type='".$journal->Type."';";

?>