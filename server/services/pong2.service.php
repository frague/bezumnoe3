<?
    require_once "base.service.php";

    header("Content-type: application/json");

    $user = GetAuthorizedUser(true, true);

    $sessionCheck = LookInRequest(User::SESSION_CHECK);

    $result = array();

    function respond($result) {
        print json_encode($result);
        exit;
    }

    if (!$user || $user->IsEmpty()) {
        if ($sessionCheck) {
            $result["quit"] = true;
            respond($result);
        } else {
            $result["force_ping"] = true;
            respond($result);
        }
    } else if (IdIsNull($user->User->RoomId)) {
        $result["quit"] = true;
        respond($result);
    }

    if ($reason = AddressIsBanned(new Bans(1, 0, 0))) {
        $result["forbidden"] = $reason;
        respond($result);
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
            $bannerName = $user->User->GetUserCurrentName($user->User->BannedBy);
            $result["banned"] = array(
                "reason" => $user->User->BanReason,
                "banner_name" => $bannerName,
                "banner" => $user->User->BannedBy,
                "till" => $user->User->BannedTill
            );
        }
    }
    if ($user->User->KickMessages) {
        $result["kicked"] = $user->User->KickMessages;
//        echo "Kicked('".JsQuote($user->User->KickMessages)."');";
        $user->User->GoOffline();
        respond($result);
    }

    $s = "";
    $lastId = round($_POST["last_id"]);

    $showMessages = true;

    /*--------------- Rooms ---------------*/

    $room = new Room();
    $room->DeleteEmptyRooms();

    $room->GetById($user->User->RoomId);
    $result["rooms"] = array("add" => array(), "delete" => array());

    /* Moving to room */

    $roomId = round($_POST["room_id"]);
    if ($roomId) {
        $newRoom = new Room($roomId);
        $newRoom->Retrieve();
        if (!$newRoom->IsEmpty() && !$newRoom->IsDeleted) {
            $result["clear_messages"] = true;
            $message = new SystemMessage(Clickable($displayedName)." переходит в комнату &laquo;".$newRoom->Title."&raquo;", $user->User->RoomId);
            $message->Save();

            if (!$newRoom->BeenVisited) {
                $newRoom->BeenVisited = 1;
                $newRoom->Save();
            }
            $user->User->RoomId = $roomId;
            $user->User->Save();

            $user->User->Retrieve();    // Renew room access
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

    $updateTopic = 0;
    $q = $room->GetAllAlive();

    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();
        $room->FillFromResult($q);
        if ($_POST["r_".$room->Id] != $room->CheckSum()) {
            $result["rooms"]["add"][] = $room->ToJSON();
        }
        if ($room->Id==$user->User->RoomId) {
            $currentRoom = $room;
        }
        $_POST["r_".$room->Id] = "";
    }
    $q->Release();

    /* Removed rooms */

    foreach ($_POST as $k  => $v) {
        if (ereg("^r_", $k) && $v) {
//            $s .= "rooms.Delete('".ereg_replace("^r_", "", $k)."');";
            $result["rooms"]["delete"][] = ereg_replace("^r_", "", $k);
        }
    }

    /*--------------- /Rooms ---------------*/

    /*--------------- Users ---------------*/

    /* Users */

    $ignore = new Ignore();
    $q = $ignore->GetForOnlineUsers($user->User->Id);
    $iIgnore = array();
    $ignoreMe = array();
    $result["users"] = array();

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
            $ChangedUsers[] = $id1;
        }
        $_POST[$user_key] = "-";
    }
    $q->Release();

    // If chnged users found - request full set of info for them
    if (sizeof($ChangedUsers) > 0) {
        $result["refresh"] = true;

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
            $jsUser = $user1->ToJSON($user->Status);
            if ($id1 == $user->User->Id) {
                $result["users"]["me"] = $jsUser;
            }
            $jsUser = str_replace(UserComplete::IsIgnoredDefault, $ii, $jsUser);
            $jsUser = str_replace(UserComplete::IgnoresYouDefault, $im, $jsUser);
            $result["users"]["add"][] = $jsUser;
        }
        $q->Release();
    }


    /* Quited users */

    foreach ($_POST as $k => $v) {
        if (ereg("^u_", $k) && $v != "-") {
            $result["users"]["delete"][] = ereg_replace("^u_", "", $k);
            $result["refresh"] = true;
        }
    }

    /*--------------- /Users ---------------*/

    /*--------------- Wakeups ---------------*/

    $wakeup = new Wakeup();
    $newWakeups = $wakeup->GetUserUnreadWakeupsIds($user->User);
    $flag = true;
    $result["wakeups"] = array();

    while (list($id, $name) = each($newWakeups)) {
//        $s .= "Wakeup(".$id.",'".JsQuote($name)."'".($flag ? ",1" : "").");";
        $result["wakeups"][] = array(
            "id" => $id,
            "name" => $name,
            "flag" => $flag ? 1 : 0
        );
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
                $result["messages"][] = $message->ToJSON();
//                $text = OuterLinks(MakeLinks($text, true));
//                $messages = "AM('".JsQuote($text)."','".$message->Id."','".$message->UserId."','".JsQuote($message->UserName)."','".$toUserId."','".JsQuote($toUser)."');".$messages;
            }
            $q->Release();
        }
    }

    /*--------------- /Messages ---------------*/

    /* Final output */
//    $s = $s.$messages;
//    if ($s) {
//        echo $s;
//    }

    print json_encode($result);

    // Destructing objects
    destroy($user);
    destroy($room);
    destroy($user1);
    destroy($wakeup);
    destroy($message);
    destroy($ignore);
    destroy($message);
?>
