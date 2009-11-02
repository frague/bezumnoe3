<?php

	$room = new Room();
	$q = $room->GetAllAlive($room->GetTitleExpression());
	$rooms = array();
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$rooms[$q->Get(Room::ROOM_ID)] = $q->Get(Room::TITLE);
	}
	
	$expiredSession = DateFromTime(mktime()-$SessionLifetime);

	$u = new User();
	$q = $u->GetByCondition("1", $u->GetOnlineUsersExpression());
	$lastRoom = "     ";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$u->FillFromResult($q);
		if ($u->SessionPong > $expiredSession) {
			$roomId = $q->Get(User::ROOM_ID);
			$room = $rooms[$roomId];
			if ($room != $lastRoom) {
				echo ($lastRoom ? "</ul>" : "")."<ul><b>".($room ? $room.": " : "Авторизация вне чата")."</b>";
				$lastRoom = $room;
			}
			echo "<li> ".$q->Get(User::LOGIN);
		}
//		echo "<li> ".$u->SessionPong." vs ".$expiredSession;
	}
	echo "<ul>";

?>