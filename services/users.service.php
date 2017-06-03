<?php 
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty()) {
		exit();	// TODO: Implement client functionality
	}

	$type = $_POST["type"];

	$result = "";
	$condition = "";
	$expression = "";
	$u = new User();

	$value = "";
	$limit = 0;
	$expression = $u->FindUserExpression();

	switch ($type) {
		case "BY_ROOM":
			$room_id = round($_POST["BY_ROOM"]);
			$condition = "t1.".User::ROOM_ID."=".$room_id;
			$eq = "t2.".Nickname::USER_ID."=t1.".User::USER_ID;
			$expression = str_replace($eq, $eq." AND t2.".Nickname::IS_SELECTED."=1", $expression);
			break;
		default:
			$value = SqlQuote(trim(substr(UTF8toWin1251($_POST["BY_NAME"]), 0, 20)));

			$condition = $value ? "(t1.".User::LOGIN." LIKE '%".$value."%' OR t2.".Nickname::TITLE." LIKE '%".$value."%')" : "1=1";
			$limit = 20;
			break;
	}

	$today = DateFromTime(time(), "Y-m-d");
	$yesterday = DateFromTime(time() - $RangeDay, "Y-m-d");
	$year = DateFromTime(time() - $RangeYear, "Y-m-d");

	$datesConditions = 0;
	$filters = array("FILTER_BANNED", "FILTER_EXPIRED", "FILTER_TODAY", "FILTER_YESTERDAY", "FILTER_REGDATE");
	for ($i = 0; $i < sizeof($filters); $i++) {
		$filter = $filters[$i];
		if (!$_POST[$filter]) {
			continue;
		}
		switch ($filter) {
			case "FILTER_REGDATE":
				$condition .= " AND t3.".Profile::REGISTERED." LIKE '".SqlQuote(trim(substr($_POST["REG_DATE"], 0, 10)))."%'";
				break;
			case "FILTER_BANNED":
				$condition .= " AND t1.".User::BANNED_BY." IS NOT NULL";
				break;
			case "FILTER_EXPIRED":
				$condition .= " AND t3.".Profile::LAST_VISIT." < '".$year."'";
				break;
			case "FILTER_TODAY":
				$condition .= ($datesCondition ? " OR " : " AND (")."t3.".Profile::LAST_VISIT." LIKE '".$today."%'";
				$datesCondition = 1;
				break;
			case "FILTER_YESTERDAY":
				$condition .= ($datesCondition ? " OR " : " AND (")."t3.".Profile::LAST_VISIT." LIKE '".$yesterday."%'";
				$datesCondition = 1;
				break;
		}
	}
	if ($datesCondition) {
		$condition .= ")";
	}

//	echo "/* $condition */";

	$q = $u->GetByCondition($condition." AND ".User::IS_DELETED."<>1", $expression.($limit ? " LIMIT ".($limit + 1) : ""));
	$rows = $q->NumRows();
	if ($limit) {
		$result .= "this.more=".($rows > $limit ? 1 : 0).";";
		if ($rows > $limit) {
			$rows = $limit;
		}
	}
	$result .= "this.data=[";

	for ($i = 0; $i < $rows; $i++) {
		$q->NextResult();

		$login = Mark($q->Get(User::LOGIN), $value);
		$nick = Mark($q->Get(Nickname::TITLE), $value);

		$result .= ($i > 0 ? "," : "")."new udto(".$q->Get(User::USER_ID).",'".JsQuote($login)."','".JsQuote($nick)."')";
	}
	$result .= "];";
	$q->Release();

	echo $result;

?>
