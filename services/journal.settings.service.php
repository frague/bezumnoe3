<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	$settings = new JournalSettings();
	$settings->GetByUserId($user->User->Id);

	if ($go == "save") {
		if ($settings->IsEmpty()) {
			$settings->UserId = $user->User->Id;
		}

		$hasErrors = false;
		$alias = trim(UTF8toWin1251($_POST[JournalSettings::REQUESTED_ALIAS]));
		if (strlen($alias) > 20) {
			echo JsAlert("����� ������� 20 ��������.", 1);
			$hasErrors = true;
		}

		if (!ereg("^[a-zA-Z0-9_]+$", $alias)) {
			echo JsAlert("����� ����� �������� ������ �� �������� ���������� ��������, ���� � ����� \"_\".", 1);
			$hasErrors = true;
		}

		if (!$hasErrors) {
			$settings->RequestedAlias = $alias;
			$settings->Save();
			echo JsAlert("��������� ������� ���������.");
		}
	}	

	echo "this.data=".$settings->ToJs().";";

?>