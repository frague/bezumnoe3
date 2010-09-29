<?
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
			SaveLog("������� �������� ������������.", $targetUser->Id, $user->User->Login, AdminComment::SEVERITY_ERROR);

			echo "co.AlertType=true;co.Show(\"\", \"��� ������� � �������\", \"� ������� ��� ������� � �������� ������ ��������������� � ���������� ����.\");";
			return;
		}
	} else {
		SaveLog("������� �������� ������������ �� ���������������.", $targetUser->Id, $user->User->Login, AdminComment::SEVERITY_ERROR);
		echo "co.AlertType=true;co.Show(\"\", \"��� �������\", \"������������ ���� ��� �������� ������������.\");";
		return;
	}



	
	$targetUser->Delete();
	SaveLog("������������ �����.", $targetUser->Id, $user->User->Login, AdminComment::SEVERITY_ERROR);
	
	echo "co.AlertType=true;co.Show(\"\", \"������������ �����\", \"��� ������ � ������������ ".$targetUser->User->Login." ���� ������� �� �������.\");";

?>
