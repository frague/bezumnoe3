<?php
  require_once "base.service.php";

  $user = GetAuthorizedUser(true, true);
  $sessionCheck = LookInRequest(User::SESSION_CHECK);

  $result = array();
  $quitResponse = json_encode(array("quit" => TRUE));

  if (!$user || $user->IsEmpty()) {
    if ($sessionCheck) {
      print $quitResponse;
    } else {
      print json_encode(array("force_ping" => TRUE));
    }
    exit;
  }

  if (IdIsNull($user->User->RoomId)) {
    print $quitResponse;
    exit;
  }

  if ($reason = AddressIsBanned(new Bans(1, 0, 0))) {
    print json_encode(array("forbidden" => $reason));
    exit;
  }

  $displayedName = $user->DisplayedName();
  $user->User->TouchSession();
  $user->User->Save();

  SetUserSessionCookie($user->User, true);
  $user->UpdateChecksum();

  if ($user->User->IsBanned()) {
    if ($user->User->CheckAmnesty()) {
      $user->User->Save();
    } else {
      $result['banned_reason'] = $user->User->BanReason;
      $result['banner'] = $user->User->GetUserCurrentName($user->User->BannedBy);
      $result['banned_till'] = $user->User->BannedTill;
    }
  }

  if ($user->User->KickMessages) {
    $result['kicked'] = $user->User->KickMessages;
    print json_encode($result);
    $user->User->GoOffline();
    exit;
  }

  $lastId = round(LookInRequest("last_id"));
  $showMessages = true;

  /*--------------- Rooms ---------------*/

  $room = new Room();
  $room->DeleteEmptyRooms();

  $room->GetById($user->User->RoomId);

  /* Moving to room */

  $roomId = round(LookInRequest("room_id"));
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

      $user->User->Retrieve();  // Renew room access
      $room = $newRoom;

      $lastId = "";
      $message = new SystemMessage("В комнату переходит ".Clickable($displayedName), $newRoom->Id);
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

  $result["rooms"] = array();
  $result["rooms_delete"] = array();

  $updateTopic = 0;
  $q = $room->GetAllAlive();

  for ($i = 0; $i < $q->NumRows(); $i++) {
    $q->NextResult();
    $room->FillFromResult($q);
    $roomChecksum = LookInRequest("r_".$room->Id);
    if ($roomChecksum != $room->CheckSum()) {
      $result["rooms"][] = $room->ToJSON();
    }
    if ($room->Id == $user->User->RoomId) {
      $currentRoom = $room;
    }
    $_POST["r_".$room->Id] = "";
  }
  $q->Release();

  /* Removed rooms */

  foreach ($_POST as $k  => $v) {
    if (preg_match("/^r_/", $k) && $v) {
      $result['rooms_delete'][] = mb_ereg_replace("^r_", "", $k);
    }
  }

  /* Users */

  $ignore = new Ignore();
  $q = $ignore->GetForOnlineUsers($user->User->Id);
  $iIgnore = array();
  $ignoresMe = array();

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


  $onlineUser = new UserComplete();
  $ChangedUsers = array();

  // Getting users data (only USER_IDs & CHECK_SUMs)
  $q = $onlineUser->GetByCondition(
    "t1.".User::ROOM_ID." IS NOT NULL",
    $user->ReadChecksumsWithIgnoreDataExpression($user->User->Id)
  );

  // Creating the list of users to be requested
  for ($i = 0; $i < $q->NumRows(); $i++) {
    $q->NextResult();
    $onlineUser->User->FillFromResult($q);

    $id1 = $onlineUser->User->Id;

    $user_key = "u_".$id1;
    $userChecksum = LookInRequest($user_key);
    if ($userChecksum != $onlineUser->User->CheckSum
      .getValue($iIgnore, $id1, 0)
      .getValue($ignoresMe, $id1, 0)
    ) {
      $ChangedUsers[] = $id1;
    }
    $_POST[$user_key] = "-";
  }
  $q->Release();

  $result["users"] = array();
  // If chnged users found - request full set of info for them
  if (sizeof($ChangedUsers) > 0) {
    $s .= "/* add */";
    // Requesting only found users
    $q = $onlineUser->GetByCondition(
      "t1.".User::USER_ID."=".implode(" OR t1.".User::USER_ID."=", $ChangedUsers),
      $user->ReadWithIgnoreDataExpression($user->User->Id)
    );

    // Create js users representations
    for ($i = 0; $i < $q->NumRows(); $i++) {
      $q->NextResult();
      $onlineUser->FillFromResult($q);

      $id1 = $onlineUser->User->Id;
      $ii = getValue($iIgnore, $id1, 0);
      $im = getValue($ignoresMe, $id1, 0);

      $user_key = "u_".$id1;
      $result["users"][] = $onlineUser->ToJSON($user->Status->Rights);
    }
    $q->Release();
  }


  $result["users_delete"] = array();
  /* Quited users */

  foreach ($_POST as $k  => $v) {
    if (preg_match("/^u_/", $k) && $v != "-") {
      // $s .= "users.Delete('".mb_ereg_replace("^u_", "", $k)."');";
      $result["users_delete"][] = mb_ereg_replace("^u_", "", $k);
    }
  }

  /*--------------- Wakeups ---------------*/

  $result["wakeups"] = array();
  $wakeup = new Wakeup();
  $newWakeups = $wakeup->GetUserUnreadWakeupsIds($user->User);
  // $flag = true;
  while (list($id, $name) = each($newWakeups)) {
    // $s .= "Wakeup(".$id.",'".JsQuote($name)."'".($flag ? ",1" : "").");";
    $result["wakeups"][] = array("id" => $id, "name" => $name);
    // $flag = false;
  }

  /*--------------- /Wakeups ---------------*/

  /*--------------- Messages ---------------*/

  if ($showMessages) {

    $result["messages"] = array();

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
        // if ($lastDate && $messageDate != $lastDate) array_unshift($messages, "AM('<p class=\"NewDay\"><span>' + (new Date(".$messageDate.")).ToPrintableString() + '</span></p>', 0, 0, '')");
        // $lastDate = $messageDate;

        // array_unshift($messages, "AM('".JsQuote($text)."','".$message->Id."','".$message->UserId."','".JsQuote($message->UserName)."','".$toUserId."','".JsQuote($toUser)."')");
        $result["messages"][] = $message->ToJSON();
      }
      $q->Release();
    }
  }

  /*--------------- /Messages ---------------*/

  /* Final output */
  // $s = $prefix.$s.join(";", $messages);
  // if ($s) {
  //   // echo $s;
  // }

  print json_encode($result, JSON_UNESCAPED_UNICODE);

  // Destructing objects
  destroy($user);
  destroy($room);
  destroy($onlineUser);
  destroy($wakeup);
  destroy($message);
  destroy($ignore);
  destroy($messages);

//  echo "/* Mem: ".number_format(memory_get_usage())." */";
?>
