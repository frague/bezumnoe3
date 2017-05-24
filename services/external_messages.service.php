<?php

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

	preg_match_all("/^\/([a-z_]+)/", $message, $commands, PREG_PATTERN_ORDER);
	if (sizeof($commands)) {
		$command = $commands[1][0];
		if ($command) {
			$message = preg_replace("/^\/".$command."\s*/", "", $message);
		}
	}

	if ($message) {
		$msg = new TelegramMessage($user, $message, $room->Id, $command == 'me');
		if ($userId) {
			$profile = new Profile();
			$profile->FillByCondition(Profile::TELEGRAM_ID."=".$userId);

			if (!$profile->IsEmpty()) {
				$user = new User($profile->UserId);
				$user->Retrieve();
				$user->RoomId = $room->Id;
				if (!$user->SessionPong) {
					$user->CreateSession("telegram");
				}
				$user->Save();

				switch ($command) {
					case "me":
						$msg = new MeMessage($message, $user);
						break;
					default:
						$msg = new Message($message, $user);
				}
			}
		}
		$msg->Save();
		$msg->FromTelegram = True;
		TriggerBotsByMessage($msg);
	}

?>
