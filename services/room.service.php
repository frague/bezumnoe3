<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty() || $user->Status->Rights < 11) {
		exit;
	}

	$title = trim(UTF8toWin1251(strip_tags($_POST["NEW_ROOM"])));
	$title = substr($title, 0, 50);

	$title = str_replace("	", " ", $title);
	$title = preg_replace("/\s+/", " ", $title);

	if (!$title) {
		echo "Введите название";
		die;
	}

	$room = new Room();
	$room->FillByTitle($title);
	if (!$room->IsEmpty()) {
		echo "Комната уже существует";
		die;
	}

	$room->Clear();
	$room->OwnerId = $user->User->Id;
	$room->Title = $title;
	$room->IsInvitationRequired = Boolean($_POST["IS_PRIVATE"]);
	if ($user->IsAdmin()) {
		$room->IsLocked = Boolean($_POST["IS_LOCKED"]);
	}
	$room->Save();

	if ($room->IsInvitationRequired) {
		$roomAccess = new RoomUser($user->User->Id, $room->Id);
		$roomAccess->Save();
	}
?>
