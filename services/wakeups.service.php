<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	$wakeup = new Wakeup();

	// Dates condition
	$d = $_POST["DATE"];
	if ($d) {
		$t = ParseDate($d);
		if ($t !== false) {
			$condition = "t1.".Wakeup::DATE." LIKE '".DateFromTime($t, "Y-m-d")."%' ";
		}
	}

	// Search keywords
	$search = MakeKeywordSearch(trim(substr(UTF8toWin1251($_POST["SEARCH"]), 0, 1024)), $comment->SearchTemplate);
	if ($search) {
		$condition .= ($condition ? " AND " : "").$search;
	}

	if (!$condition) {
		$condition = "1=1";
	}

	if ($search) {
		$amount = 10;
		$wakeup->TotalCount = 10;
	}
	
	$q = $wakeup->GetForUser($user->User->Id, $from, $amount, $condition);
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