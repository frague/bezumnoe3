<?php

	$room = new Room();
	$q = $room->GetAllAlive($room->GetTitleExpression());
	$rooms = array();
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$rooms[$q->Get(Room::ROOM_ID)] = $q->Get(Room::TITLE);
	}
	
	$expiredSession = DateFromTime(mktime()-$SessionLifetime);

	$user = new User();
	$q = $user->GetByCondition("1", $user->GetOnlineUsersExpression());
	$lastRoom = "     ";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$user->FillFromResult($q);
		if ($user->SessionPong > $expiredSession) {
			$roomId = $q->Get(User::ROOM_ID);
			$room = $rooms[$roomId];
			if ($room != $lastRoom) {
				echo ($lastRoom ? "</ul>" : "")."<ul><b>".($room ? $room : "Авторизация вне чата")."</b>";
				$lastRoom = $room;
			}
			echo "<li> ".$q->Get(User::LOGIN);
		}
//		echo "<li> ".$user->SessionPong." vs ".$expiredSession;
	}
	echo "<ul>";

?>