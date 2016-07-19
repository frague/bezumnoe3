<?php
	require_once "base.service.php";

//	JsPoint("Auth user");

	$user = GetAuthorizedUser(true, true);

	$sessionCheck = LookInRequest(User::SESSION_CHECK);

	if (!$user || $user->IsEmpty()) {
		if ($sessionCheck) {
			echo "DebugLine('No authorized user for session');";
			echo "Quit();";
			exit;
		} else {
			echo "ForcePing(1);";
			exit;
		}
	} else if (IdIsNull($user->User->RoomId)) {
		echo "DebugLine('No room found for user');";
		echo "Quit();";
		exit;
	}

//	JsPoint("Get authorized user");

	if ($reason = AddressIsBanned(new Bans(1, 0, 0))) {
		echo "Forbidden('".JsQuote($reason)."');";
		exit;
	}

//	JsPoint("Check ban");

	$displayedName = $user->DisplayedName();

	$user->User->TouchSession();

//	JsPoint("Touch session");

	$user->User->Save();

//	JsPoint("User save");

	SetUserSessionCookie($user->User, true);

//	JsPoint("Set session cookie");

	$user->UpdateChecksum();

//	JsPoint("Update checksum");

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
			$message = new SystemMessage(Clickable($displayedName)." переходит в комнату &laquo;".$newRoom->Title."&raquo;", $user->User->RoomId);
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
			$message = new SystemMessage("В комнату переходит ".Clickable($displayedName), $newRoom->Id);
			$message->Save();
		 }
	}

//	JsPoint("Moving to room");

	/* Checking room access */

	if (!$room->IsEmpty()) {
		if ($room->IsInvitationRequired && !$user->User->RoomIsPermitted/* && !$user->IsSuperAdmin()*/) {
			$showMessages = false;
		}
	}

//	JsPoint("Room access");

	/* Rooms data */

	$updateTopic = 0;
	$q = $room->GetAllAlive();

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
//		$room = new Room();
		$room->FillFromResult($q);
		if ($_POST["r_".$room->Id] != $room->CheckSum()) {
			$s .= "rooms.Add(".$room->ToJs().");";
		}
		if ($room->Id==$user->User->RoomId) {
			$currentRoom = $room;
		}
		$_POST["r_".$room->Id] = "";
	}
	$q->Release();

//	JsPoint("Rooms data");

	/* Removed rooms */

	foreach ($_POST as $k  => $v) {
		if (preg_match("/^r_/", $k) && $v) {
			$s .= "rooms.Delete('".mb_ereg_replace("^r_", "", $k)."');";
		}
	}

//	JsPoint("Remove rooms");

	/*--------------- /Rooms ---------------*/

	/*--------------- Users ---------------*/

	/* Users */

	$ignore = new Ignore();
	$q = $ignore->GetForOnlineUsers($user->User->Id);
	$iIgnore = array();
	$ignoreMe = array();

	// Getting ignore information (who from online users ignores who)
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$ignore->FillFromResult($q);
		if ($ignore->UserId == $user->User->Id) {
			$iIgnore[$ignore->IgnorantId] = 1;
		} else {
			$ignoresMe[$ignore->UserId] = 1;
		}
	}
	$q->Release();

//	JsPoint("Ignores");


	$user1 = new UserComplete();
	$ChangedUsers = array();

	// Getting users data (only USER_IDs & CHECK_SUMs)
	$q = $user1->GetByCondition(
		"t1.".User::ROOM_ID." IS NOT NULL",
		$user->ReadChecksumsWithIgnoreDataExpression($user->User->Id)
	);

	// Creating the list of users to be requested
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$user1->User->FillFromResult($q);

		$id1 = $user1->User->Id;

		$user_key = "u_".$id1;
		if ($_POST[$user_key] != $user1->User->CheckSum.round($iIgnore[$id1]).round($ignoresMe[$id1])) {
//			print "/* ".$_POST[$user_key]." != ".$user1->User->CheckSum.round($iIgnore[$id1]).round($ignoresMe[$id1])." */";
			$ChangedUsers[] = $id1;
		}
		$_POST[$user_key] = "-";
	}
	$q->Release();

//	JsPoint("Users1");

	// If chnged users found - request full set of info for them
	if (sizeof($ChangedUsers) > 0) {
		$s .= "/* add */";
		// Requesting only found users
		$q = $user1->GetByCondition(
			"t1.".User::USER_ID."=".implode(" OR t1.".User::USER_ID."=", $ChangedUsers),
			$user->ReadWithIgnoreDataExpression($user->User->Id)
		);

		// Create js users representations
		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();
			$user1->FillFromResult($q);

			$id1 = $user1->User->Id;
			$ii = round($iIgnore[$id1]);
			$im = round($ignoresMe[$id1]);

			$user_key = "u_".$id1;
			$jsUser = $user1->ToJs($user->Status);
			if ($id1 == $user->User->Id) {
				$s .= "me=".$jsUser.";";
				$jsUser = "me";
			}
			$jsUser = str_replace(UserComplete::IsIgnoredDefault, $ii, $jsUser);
			$jsUser = str_replace(UserComplete::IgnoresYouDefault, $im, $jsUser);
			$s .= "users.Add(".$jsUser.");";
		}
		$q->Release();

	//	JsPoint("Users2");
	}











	/* Quited users */

	foreach ($_POST as $k  => $v) {
		if (preg_match("/^u_/", $k) && $v != "-") {
			$s .= "users.Delete('".mb_ereg_replace("^u_", "", $k)."');";
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

//	JsPoint("Wakeups");

	/*--------------- /Wakeups ---------------*/

	/*--------------- Messages ---------------*/

	if ($showMessages) {

		/* Retrieving messages */
		$messages = array();
		$message = new Message();
		$basicCondition = "(
	(t1.".Message::ROOM_ID."=".SqlQuote($user->User->RoomId)." AND
		((t1.".Message::TO_USER_ID." IS NULL OR  t1.".Message::TO_USER_ID."=".$user->User->Id.") OR
		(t1.".Message::USER_ID."=t1.".Message::TO_USER_ID."))
	) OR
	(t1.".Message::ROOM_ID."=-1 AND
		(t1.".Message::USER_ID."=".$user->User->Id." OR t1.".Message::TO_USER_ID."=".$user->User->Id." OR t1.".Message::USER_ID." IS NULL)
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

		$q = $message->GetByCondition(
			$basicCondition.$condition,
			$message->ReadWithIgnoresExpression($user->User->Id)
		);

		$lastDate = "";
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

				$messageDate = date("Y, n-1, j", strtotime($message->Date));
				if ($lastDate && $messageDate != $lastDate) array_unshift($messages, "AM('<p class=\"NewDay\"><span>' + (new Date(".$messageDate.")).ToPrintableString() + '</span></p>', 0, 0, '')");
				$lastDate = $messageDate;

				array_unshift($messages, "AM('".JsQuote($text)."','".$message->Id."','".$message->UserId."','".JsQuote($message->UserName)."','".$toUserId."','".JsQuote($toUser)."')");
			}
			$q->Release();
		}

	//	JsPoint("Messages");

	}

	/*--------------- /Messages ---------------*/

	/* Final output */
	$s = $prefix.$s.join(";", $messages);
	if ($s) {
		echo $s;
	}

	// Destructing objects
	destroy($user);
	destroy($room);
	destroy($user1);
	destroy($wakeup);
	destroy($message);
	destroy($ignore);
	destroy($messages);

//	echo "/* Mem: ".number_format(memory_get_usage())." */";
?>
