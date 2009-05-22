<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		echo "-Сообщение не отправлено: ошибка авторизации!";
		exit;
	}

	$text = trim(substr(UTF8toWin1251($_POST["message"]), 0, 1024));
	$message_id = round($_POST["reply_to"]);
	
	if ($message_id) {
		if ($text) {
			$wakeup = new Wakeup();
			$wakeup->GetById($message_id);
			if ($wakeup->IsEmpty()) {
				echo "-Исходное сообщение не найдено!";
			} else {
				$wakeup = new Wakeup($text, $user->User->Id, $wakeup->FromUserId);
				$wakeup->Save();
				echo "+Сообщение успешно отправлено.";
			}
		} else {
			echo "-Сообщение пустое!";
		}
	} else {
		echo "-Сообщение не отправлено: недостаточно данных!";
	}

?>