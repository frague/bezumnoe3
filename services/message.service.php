<?

	$root = "../";
	require_once $root."server_references.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty() || $user->User->IsBanned()) {
		exit;
	}

	function GetUserStatus($id) {
	  global $db, $targetUser;

	  	$targetUser = new User($id);
	  	$targetUser->Retrieve();
	  	if ($targetUser->IsEmpty()) {
	  		return false;
	  	}
	  	$status = new Status($targetUser->StatusId);
	  	$status->Retrieve();
	  	if (!$status->IsEmpty()) {
	  		return $status;
	  	}
	  	return false;
	}

	
	$command = "";
	function CommandsProcessor($c) {
	  global $command;
	    $command = $c;
	}

	$room = new Room($user->User->RoomId);
	$room->Retrieve();
	if (!$room->IsEmpty()) {
		if ($room->IsInvitationRequired && !$user->IsAdmin() && !$user->User->RoomIsPermitted) {
		    echo "MessageForbiddenAlert();";
			exit;
		}
	}

	$message = str_replace("<", "&lt;", $_POST["message"]);
	$message = str_replace(">", "&gt;", $message);
	$message = substr($message, 0, 1024);
	$recepients = ereg_replace("[^0-9,\-]", "", $_POST["recepients"]);
	if ($recepients == "-1") {
		$recepients = "";
	}
	$recs = split(",", substr($recepients, 0, 1024));
	$type = ereg_replace("[^a-z]", "", substr(strtolower($_POST["type"]), 0, 50));

	if ($message || $type == "away" || $type == "quit" || $type == "kick" || $type == "ban" || strpos($type, "topic") !== 0) {
		$message = UTF8toWin1251($message);

		if ($recepients) {
			for ($i = 0; $i < sizeof($recs); $i++) {
				switch ($type) {
					case "wakeup":
						$msg = new Wakeup($message, $user->User->Id, $recs[$i]);
						$msg->Save();
						break;
					case "kick":
						if ($user->IsAdmin() || $user->Status->Rights == $KeeperRights) {
							$targetStatus = GetUserStatus($recs[$i]);
							if (!$targetStatus) {
								break;
							}

							$n = $targetUser->GetUserCurrentName();
							if ($user->Status->Rights >= $targetStatus->Rights) {
								$targetUser->Kick($message, $user->DisplayedName());
								$msg = new SystemMessage($user->DisplayedName()." выгон€ет из чата ".$n.($message ? " &laquo;".$message."&raquo;" : "."), $user->User->RoomId);
							} else {
								$msg = new PrivateSystemMessage("Ќевозможно выгнать пользовател€ <b>".$n."</b>!", $user->User->RoomId, $user->User->Id);
						   	}
							$msg->Save();
						} else {
						
						}
						break;
					case "ban":
						if ($user->IsAdmin()) {
							$targetStatus = GetUserStatus($recs[$i]);
							if (!$targetStatus) {
								break;
							}

							$n = $targetUser->GetUserCurrentName();
							if ($user->Status->Rights >= $targetStatus->Rights) {
								$targetUser->Ban($message, "", $user->User->Id);
								$targetUser->Save();
								$msg = new SystemMessage($user->DisplayedName()." банит пользовател€ ".$n.($message ? " &laquo;".$message."&raquo;" : "."), $user->User->RoomId);

								// Write log
								LogBan($targetUser->Id, $targetUser->BanReason, $user->User->Login, $targetUser->BannedTill);

								// Scheduler
								$task = new ScheduledTask();
								$task->DeleteUserUnbans($targetUser->Id);
							} else {
								$msg = new PrivateSystemMessage("Ќевозможно забанить пользовател€ <b>".$n."</b>!", $user->User->RoomId, $user->User->Id);
							}
							$msg->Save();
						}
						break;
					default:
						$msg = new PrivateMessage($message, $user->User, $recs[$i]);
						$msg->Save();
				}
			}
		} else {
			$roomExists = ($room && !$room->IsEmpty());
			switch ($type) {
				case "me":
					$msg = new MeMessage($message, $user->User);
					break;
				case "locktopic":
				case "unlocktopic":
					if ($roomExists) {
						if ($user->IsAdmin()) {
							$room->TopicLock = ($type == "locktopic" ? 1 : 0);
							$msg = new SystemMessage($user->DisplayedName()." ".($room->TopicLock ? "блокирует" : "разблокирует")." тему.", $user->User->RoomId);
						} else {
							$msg = new PrivateSystemMessage("»зменение темы невозможно!!", $user->User->RoomId, $user->User->Id);
							break;
						}
					}
				case "topic":
					if ($roomExists) {
						if ($user->Status->Rights >= $TopicRights && (!$room->TopicLock || $user->IsAdmin())) {
							if ($message || $type == "topic") {
								$room->Topic = $message;
								$room->TopicAuthorId = $user->User->Id;
								$msg = new SystemMessage($user->DisplayedName()." мен€ет тему на &laquo;".$message."&raquo;.", $user->User->RoomId);
							}
							$room->Save();
						} else {
							$msg = new PrivateSystemMessage("»зменение темы невозможно!", $user->User->RoomId, $user->User->Id);
						}
					}
					break;
				case "away":
					$user->User->AwayTime = NowDateTime();
					$user->User->AwayMessage = $message;
					$user->User->Save();
					$msg = new SystemMessage($user->DisplayedName()." отлучаетс€ из чата. ".($message ? " &laquo;".$message."&raquo;" : ""), $user->User->RoomId);
					break;
				case "quit":
					$text = "";
					if (!$message) {
						$text = $user->Settings->QuitMessage;
					}
					if (!$text) {
						$text = "%name выходит из чата.".($message ? " &laquo;".$message."&raquo;" : "");
					}
					$msg = new SystemMessage(str_replace("%name", $user->DisplayedName(), $text), $user->User->RoomId);
					$user->User->GoOffline();
					$user->User->Save();
					break;
				default:
					$msg = new Message($message, $user->User);
			}
			if ($msg) {
				if ($type != "away" && $user->User->AwayTime) {
					$backMsg = new SystemMessage($user->DisplayedName()." возвращаетс€".($user->User->AwayMessage ? ", закончив &laquo;".$user->User->AwayMessage."&raquo;" : " в чат").", отсутствовав с ".PrintableTime($user->User->AwayTime), $user->User->RoomId);
					$backMsg->Save();
					$user->User->AwayMessage = "";
					$user->User->AwayTime = "";
					$user->User->Save();
				}
				$msg->Save();
			}
		}
	}

?>
