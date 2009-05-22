<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		echo "-��������� �� ����������: ������ �����������!";
		exit;
	}

	$text = trim(substr(UTF8toWin1251($_POST["message"]), 0, 1024));
	$message_id = round($_POST["reply_to"]);
	
	if ($message_id) {
		if ($text) {
			$wakeup = new Wakeup();
			$wakeup->GetById($message_id);
			if ($wakeup->IsEmpty()) {
				echo "-�������� ��������� �� �������!";
			} else {
				$wakeup = new Wakeup($text, $user->User->Id, $wakeup->FromUserId);
				$wakeup->Save();
				echo "+��������� ������� ����������.";
			}
		} else {
			echo "-��������� ������!";
		}
	} else {
		echo "-��������� �� ����������: ������������ ������!";
	}

?>