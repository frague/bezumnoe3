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
	}
	$directionCondition = "";
	if ($isIncoming) {
		$directionCondition = "t1.".Wakeup::TO_USER_ID."=".$user->User->Id;
	}
	if ($isOutgoing) {
		$directionCondition .= ($directionCondition ? " OR " : "")."t1.".Wakeup::FROM_USER_ID."=".$user->User->Id;
	}
	if ($directionCondition) {
		$condition = ($condition ? $condition." AND " : "")."(".$directionCondition.")";
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
	$q->Release();

	echo "this.Total=".$wakeup->GetResultsCount($condition).";";
?>
