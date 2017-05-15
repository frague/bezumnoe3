<?

	$root = "../";
	require_once $root."server_references.php";

	$room = new Room();
    $q = $room->GetAllAlive();
    $q->NextResult();
    $room->FillFromResult($q);
    $q->Release();

	if ($room->IsEmpty()) {
		return;
	}

	$message = str_replace("<", "&lt;", $_POST["message"]);
	$message = str_replace(">", "&gt;", $message);
	$message = substr($message, 0, 1024);

	$user = str_replace("<", "&lt;", $_POST["user"]);
	$user = str_replace(">", "&gt;", $user);

	$userId = (int) $_POST["user_id"];

	if ($message) {
		if ($userId) {
			$profile = new Profile();
			$profile->GetByCondition(Profile::TELEGRAM_ID."=".$userId);
			if (!$profile->IsEmpty()) {
				$user = new User();
				$user->Id = $profile->UserId;
				$user->RoomId = $room->Id;

				$msg = new Message($message, $user->User);
				$msg->Save();
				die;
			}
		}

		$msg = new TelegramMessage($user, $message, $room->Id);
		$msg->Save();
	}

?>
