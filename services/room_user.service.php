<?php 	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}
	$requestor_id = round($_POST["user_id"]);

	if ($requestor_id) {
		$room = new Room($user->User->RoomId);
		$room->Retrieve();
		if (!$room->IsEmpty() && $room->IsInvitationRequired) {
			$roomUser = new RoomUser($requestor_id, $user->User->RoomId);
			if ($state) {
				if ($user->User->RoomIsPermitted) {
					$roomUser->Save();
				}
			} else if ($room->OwnerId == $user->User->Id) {
				$roomUser->Delete();
			}
		}
	}

?>
