<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty() || !$forum_id) {
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
		$journal->Title = trim(UTF8toWin1251($_POST[Journal::TITLE]));
		if (!$journal->IsGallery()) {
			$journal->Description = trim(UTF8toWin1251($_POST[Journal::DESCRIPTION]));
		}
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

	echo "this.data=".$settings->ToJs($journal->Title, $journal->Description).";";
	echo "this.type='".$journal->Type."';";

?>