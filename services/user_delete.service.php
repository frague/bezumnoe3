<?php
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	$user_id = round($_POST["user_id"]);
	
	if (!$user || $user->IsEmpty() || !$user_id) {
		exit;
	}

	$ownProfile = ($user_id == $user->User->Id);
	if ($ownProfile || $user->IsAdmin()) {
		if ($user_id == $user->User->Id) {
			// Self-deletion
			$targetUser = $user;
		} else {
			$targetUser = new UserComplete($user_id);
			$targetUser->Retrieve();

			if ($targetUser->Status->IsAdmin() && !$user->IsSuperAdmin()) {
				// Only Super admin can delete admins
				$targetUser->Clear();
			}
		}
		if ($targetUser->IsEmpty()) {
			SaveLog("Попытка удаления пользователя.", $targetUser->Id, $user->User->Login, AdminComment::SEVERITY_ERROR);

			echo "co.AlertType=true;co.Show(\"\", \"Нет доступа к профилю\", \"У админов нет доступа к профилям других администраторов и хранителей чата.\");";
			return;
		}
	} else {
		SaveLog("Попытка удаления пользователя не администратором.", $targetUser->Id, $user->User->Login, AdminComment::SEVERITY_ERROR);
		echo "co.AlertType=true;co.Show(\"\", \"Нет доступа\", \"Недостаточно прав для удаления пользователя.\");";
		return;
	}



	
	$targetUser->Delete();
	SaveLog("Пользователь удалён.", $targetUser->Id, $user->User->Login, AdminComment::SEVERITY_ERROR);
	
	echo "co.AlertType=true;co.Show(\"\", \"Пользователь удалён\", \"Все данные о пользователе ".$targetUser->User->Login." были удалены из системы.\");";

?>
