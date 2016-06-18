<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}
	$ignorantId = round($_POST["user_id"]);

	$ignorant = new User();
	if ($ignorantId > 0 && $ignorant->UserDoesExist($ignorantId)) {
		$ignore = new Ignore();
		$ignore->UserId = $user->User->Id;
		$ignore->IgnorantId = $ignorantId;
		if (!$state) {
			if ($ignore->Save()) {
				echo $ignorantId;
			}
		} else {
			if ($ignore->Delete()) {
				echo -$ignorantId;
			}
		}
	}
?>