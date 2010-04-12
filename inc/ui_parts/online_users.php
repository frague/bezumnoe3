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
	$lastRoom = "     ";
	$toPrint = "";
	$inside = 0;
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$u->FillFromResult($q);
		if ($u->SessionPong > $expiredSession) {
			$roomId = $q->Get(User::ROOM_ID);
			$room = $rooms[$roomId];
			if ($room != $lastRoom) {
				$toPrint .= ($lastRoom ? ($inside ? " <sup>(".$inside.")</sup>" : "").$people."</ul>" : "")."<ul><b>".($room ? $room : "Авторизация вне чата")."</b>";
				$lastRoom = $room;
				$people = "";
				$inside = 0;
			}
			$people .= MakeListItem()." ".$u->ToInfoLink();
			$inside++;
		}
	}
	$q->Release();

	$toPrint .= ($lastRoom ? ($inside ? " <sup>(".$inside.")</sup>" : "").$people."</ul>" : "");
	echo $toPrint;

?>