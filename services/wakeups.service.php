<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	$wakeup = new Wakeup();

	$condition = MakeSearchCriteria("DATE", Wakeup::DATE, "SEARCH", $wakeup->SearchTemplate);
	
	/* Incoming/Outgoing filter */

	$isIncoming = $_POST["IS_INCOMING"];
	$isOutgoing = $_POST["IS_OUTGOING"];
	if (!$isIncoming && !$isOutgoing) {
		echo "this.data=[];this.Total=0;";
		die;
	} else if ($isIncoming && !$isOutgoing) {
		$condition .= ($condition ? " AND " : "")."t1.".Wakeup::TO_USER_ID."=".$user->User->Id;
	} else {
		$condition .= ($condition ? " AND " : "")."t1.".Wakeup::FROM_USER_ID."=".$user->User->Id;
	}

	/* --- */
	
	$q = $wakeup->GetForUser($user->User->Id, $from, $amount, $condition);
	$total = $q->NumRows();
	
	echo "this.data=[";
	for ($i = 0; $i < $total; $i++) {
		$q->NextResult();
		$wakeup->FillFromResult($q);
		echo ($i ? "," : "").$wakeup->ToJs($user->User->Id);
	}
	echo "];";
	echo "this.Total=".$wakeup->GetResultsCount($condition).";";
?>