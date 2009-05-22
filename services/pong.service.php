<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true, true);

	if (!$user || $user->IsEmpty() || IdIsNull($user->User->RoomId)) {
		echo "Quit();";
		exit;
	}

	if ($reason = AddressIsBanned(new Bans(1, 0, 0))) {
		echo "Forbidden('".JsQuote($reason)."');";
		exit;
	}

	$displayedName = $user->DisplayedName();

	$user->User->TouchSession();
	$user->User->Save();
	SetUserSessionCookie($user->User, true);

	if ($user->User->IsBanned()) {
		if ($user->User->CheckAmnesty()) {
			$user->User->Save();
		} else {
			$bannerName = $user->User->GetUserCurrentName($user->User->BannedBy);
			echo "Banned('".JsQuote($user->User->BanReason)."', '".JsQuote($bannerName)."', ".JsQuote($user->User->BannedBy).", '".JsQuote($user->User->BannedTill)."');";
		}
	}
	if ($user->User->KickMessages) {
		echo "Kicked('".JsQuote($user->User->KickMessages)."');";
		$user->User->GoOffline();
		exit;
	}


	$s = "";
	$prefix = "";
	$lastId = round($_POST["last_id"]);

	$showMessages = true;


	/*--------------- Rooms ---------------*/

	$room = new Room();
	$room->DeleteEmptyRooms();

	$room->GetById($user->User->RoomId);

	/* Moving to room */

	$roomId = round($_POST["room_id"]);
	if ($roomId) {
		$newRoom = new Room($roomId);
		$newRoom->Retrieve();
		if (!$newRoom->IsEmpty() && !$newRoom->IsDeleted) {
			$prefix = "ClearMessages();";
			$message = new SystemMessage($displayedName." переходит в комнату &laquo;".$newRoom->Title."&raquo;", $user->User->RoomId);
			$message->Save();
			if (!$newRoom->BeenVisited) {
				$newRoom->BeenVisited = 1;
				$newRoom->Save();
			}
			$user->User->RoomId = $roomId;
			$user->User->Save();

			$user->User->Retrieve();	// Renew room access
			$room = $newRoom;

			$lastId = "";
			$message = new SystemMessage("В комнату переходит ".$displayedName, $newRoom->Id);
			$message->Save();
		 }
	}
	
	/* Checking room access */

	if (!$room->IsEmpty()) {
		if ($room->IsInvitationRequired && !$user->User->RoomIsPermitted/* && !$user->IsSuperAdmin()*/) {
			$showMessages = false;
		}
	}


	/* Rooms data */

	$updateTopic = 0;
	$q = $room->GetAllAlive();

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$room = new Room();
		$room->FillFromResult($q);
		if ($_POST["r_".$room->Id] != $room->CheckSum()) {
			$s .= "rooms.Add(".$room->ToJs().");";
		}
		if ($room->Id==$user->User->RoomId) {
			$currentRoom = $room;
		}
		$_POST["r_".$room->Id] = "";
	}

	/* Removed rooms */

	foreach ($_POST as $k  => $v) {
		if (ereg("^r_", $k) && $v) {
			$s .= "rooms.Delete('".ereg_replace("^r_", "", $k)."');";
		}
	}

	/*--------------- /Rooms ---------------*/

	/*--------------- Users ---------------*/

	/* Users */

	$ignore = new Ignore();
	$q = $ignore->GetForOnlineUsers($user->User->Id);
	$iIgnore = array();
	$ignoreMe = array();

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$ignore->FillFromResult($q);
		if ($ignore->UserId == $user->User->Id) {
			$iIgnore[$ignore->IgnorantId] = true;
		} else {
			$ignoresMe[$ignore->UserId] = true;
		}
	}

	$user1 = new UserComplete();
	$q = $user1->GetByCondition("t1.".User::ROOM_ID." IS NOT NULL", $user->ReadWithIgnoreDataExpression($user->User->Id));
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$user1->FillFromResult($q);
		$id1 = $user1->User->Id;

		$user_key = "u_".$id1;
		if ($_POST[$user_key] != $user1->CheckSum()) {
			$jsUser = $user1->ToJs($user->Status);
			if ($id1 == $user->User->Id) {
				$s .= "me=".$jsUser.";";
				$jsUser = "me";
			}
			$jsUser = str_replace(UserComplete::IsIgnoredDefault, $iIgnore[$id1] ? 1 : 0, $jsUser);
			$jsUser = str_replace(UserComplete::IgnoresYouDefault, $ignoresMe[$id1] ? 1 : 0, $jsUser);
			$s .= "users.Add(".$jsUser.");";
		}
		$_POST[$user_key] = "-";
	}

	/* Quited users */

	foreach ($_POST as $k  => $v) {
		if (ereg("^u_", $k) && $v != "-") {
			$s .= "users.Delete('".ereg_replace("^u_", "", $k)."');";
		}
	}

	/* Expired sessions */

	$q = $user->User->GetExpiredUsers();
	$u = array();
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$roomId = $q->Get(User::ROOM_ID);
		$s .= "users.Delete('".$q->Get(User::USER_ID)."');";
		$u[$roomId] .= ($u[$roomId] ? ", " : "").$q->Get(User::LOGIN);
	}
	// Left user sessions, but remove room information
	$q = $db->Query("UPDATE ".User::table." t1 SET t1.".User::ROOM_ID."=NULL, t1.".User::SESSION_PONG."=NULL WHERE ".$user->User->ExpireCondition());
//	$q = $db->Query("UPDATE ".User::table." t1 SET t1.".User::ROOM_ID."=-1, t1.".User::SESSION."='', t1.".User::SESSION_PONG."=NULL WHERE ".$user->User->ExpireCondition());

	if (sizeof($u) > 0) {
		while (list($roomId,$users) = each($u)) {
			$message = new SystemMessage($users.(ereg(", ", $users) ? " покидают" : " покидает")." чат.", $roomId);
			$message->Save();
		}
	}

	/* List of users should be refreshed */

	if ($s) {
		$s .= "showRooms=1;";
	}

	/*--------------- /Users ---------------*/

	/*--------------- Wakeups ---------------*/

	$wakeup = new Wakeup();
	$newWakeups = $wakeup->GetUserUnreadWakeupsIds($user->User);
	$flag = true;
	while (list($id, $name) = each($newWakeups)) {
		$s .= "Wakeup(".$id.",'".JsQuote($name)."'".($flag ? ",1" : "").");";
		$flag = false;
	}
	/*--------------- /Wakeups ---------------*/

	/*--------------- Messages ---------------*/

	if ($showMessages) {
	
		/* Retrieving messages */
		$messages = "";
		$message = new Message();
		$basicCondition = "(
	(t1.".Message::ROOM_ID."=".SqlQuote($user->User->RoomId)." AND 
		(t1.".Message::TO_USER_ID." IS NULL OR  t1.".Message::TO_USER_ID."=".$user->User->Id.")) OR 
	(t1.".Message::ROOM_ID."=-1 AND 
		(t1.".Message::USER_ID."=".$user->User->Id." OR t1.".Message::TO_USER_ID."=".$user->User->Id.")
	)
) ";
		if (!$lastId || $lastId < 0) {
			$condition = "
ORDER BY t1.".Message::MESSAGE_ID." DESC LIMIT 20";
		} else {
			$condition = "
	AND t1.".Message::MESSAGE_ID.">".SqlQuote($lastId)." 
ORDER BY t1.".Message::MESSAGE_ID." DESC LIMIT 10";
		}
		$q = $message->GetByCondition($basicCondition.$condition, $message->ReadWithIgnoresExpression($user->User->Id));

		if ($q !== false) {
			for ($i = 0; $i < $q->NumRows(); $i++) {
				$q->NextResult();
				$message->FillFromResult($q);
				$toUser = "";
				$toUserId = "";
				if ($message->ToUserId > 0 && $message->ToUserId != $message->UserId) {
					if ($message->UserId == $user->User->Id) {
						$toUser = $message->ToUserName;
						$toUserId = $message->ToUserId;
					} else {
						$toUser = $message->UserName;
						$toUserId = $message->UserId;
					}
				}
				$text = $message->ToPrint($displayedName, $user->User->Id);
				$text = OuterLinks(MakeLinks($text, true));
				$messages = "AM('".JsQuote($text)."','".$message->Id."','".$message->UserId."','".JsQuote($message->UserName)."','".$toUserId."','".$toUser."');".$messages;
			}
		}
	}

	/*--------------- /Messages ---------------*/

	/* Final output */
	$s = $prefix.$s.$messages;
	if ($s) {
		echo $s;
	}

?>
