<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty() || !$user->IsSuperAdmin()) {
		die();	// TODO: Implement client functionality
	}

	$id = round($_POST[Room::ROOM_ID]);
	$room = new Room();

	switch ($go) {
		case "save":
			$room->FillFromHash($_POST);
			$error = $room->SaveChecked();
			if ($error) {
				echo JsAlert($error, 1);
			} else {
				echo JsAlert("Изменения сохранены.");
			}
			break;
		case "delete":
			if ($id) {
				$room->Id = $id;
				$room->Retrieve();
				if (!$room->IsEmpty()) {
					if (!$room->IsDeleted) {
						// Closing all user sessions for that room
						$u = new User();
						$u->GetByCondition(User::ROOM_ID."=".$id, $u->CloseSessionExpression());
						$room->Delete();
						echo JsAlert("Комната удалена.");
					} else {
						echo JsAlert("Комната уже удалена.");
					}
				}
			}
			break;
	}

	echo "this.data=[";
	$q = $room->GetByCondition("", $room->ListRoomsExpression());

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$room->FillFromResult($q);
		echo ($i ? "," : "").$room->ToDTO();
	}
	echo "];";
	$q->Release();

?>