<?php

    $room = new Room();
    $q = $room->GetAllAlive($room->GetTitleExpression());
    $rooms = array();
    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();
        $rooms[$q->Get(Room::ROOM_ID)] = $q->Get(Room::TITLE);
    }
    $q->Release();
    
    $expiredSession = DateFromTime(mktime()-$SessionLifetime);

    $u = new User();
    $q = $u->GetByCondition("1", $u->GetOnlineUsersExpression());

    $lastRoom = False;
    $peope = "";
    $inside = 0;
    $roomId = -1;

    function PrintRoom($lastRoom, $room, $inside, $people) {
        if ($room != $lastRoom && $lastRoom) {
            echo "<h6>".$lastRoom .($inside ? " <span>".$inside."</span>" : "")."</h6>";
            echo ($people ? "<ul class='random'>".$people."</ul>" : "");
        }
    }
    
    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();
        $u->FillFromResult($q);
        if ($u->SessionPong > $expiredSession) {
            $roomId = $q->Get(User::ROOM_ID);
            $room = $rooms[$roomId];
            if ($room != $lastRoom && $roomId > 0) {
                PrintRoom($lastRoom, $room, $inside, $people);
                $lastRoom = $room;
                $people = "";
                $inside = 0;
            }
            $people .= "<li> ".$u->ToInfoLink();
            $inside++;
        }
    }
    $q->Release();
    if ($roomId > 0) {
        PrintRoom($room, False, $inside, $people);
    } else {
        echo "никого нет";
    }

?>