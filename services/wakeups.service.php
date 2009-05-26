<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	$wakeup = new Wakeup();

	$search = MakeKeywordSearch(trim(substr(UTF8toWin1251($_POST["SEARCH"]), 0, 1024)), $wakeup->SearchTemplate);
	if ($search) {
		$amount = 10;
		$wakeup->TotalCount = 10;
	}
	
	$q = $wakeup->GetForUser($user->User->Id, $from, $amount, $search);
	$total = $q->NumRows();
	
	echo "this.data=[";
	for ($i = 0; $i < $total; $i++) {
		$q->NextResult();
		$wakeup->FillFromResult($q);
		echo ($i ? "," : "").$wakeup->ToJs($user->User->Id);
	}
	echo "];";
	echo "this.Total=".$total.";";
?>