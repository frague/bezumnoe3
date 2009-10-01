<?
	require_once "base.service.php";
	die;



	$user_id = round($_POST["user_id"]);
	
	if ($user_id) {
		// UserID specified
		$user = new UserComplete($user_id);
		$user->Retrieve();
	} else {
		// Get random user
		$user = new UserComplete();
		$q = $user->GetByCondition("1");
		$q->Seek(round(rand(0, $q->NumRows())));
		$q->NextResult();
		$user->FillFromResult($q);
	}

	if (!$user->IsEmpty()) {
		echo $user->ToJs();
	}

?>
