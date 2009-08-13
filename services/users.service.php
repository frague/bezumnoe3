<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty()) {
		exit();	// TODO: Implement client functionality
	}

	$type = $_POST["type"];
	$value = SqlQuote(trim(substr(UTF8toWin1251($_POST["value"]), 0, 20)));

	$condition = "";
	$expression = "";
	$u = new User();

	if (!$value) {
		exit;
	}

	$limit = 0;
	$expression = $u->FindUserExpression();

	switch ($type) {
		case "BY_NAME":
			$condition = "t1.".User::LOGIN." LIKE '%".$value."%' OR t2.".Nickname::TITLE." LIKE '%".$value."%'";
			$limit = 20;
			break;
		case "BY_ROOM":
			$room_id = round($value);
			$condition = "t1.".User::ROOM_ID."=".$room_id;
			$eq = "t2.".Nickname::USER_ID."=t1.".User::USER_ID;
			$expression = str_replace($eq, $eq." AND t2.".Nickname::IS_SELECTED."=1", $expression);
			break;
		default:
			exit;
	}
	
	$q = $u->GetByCondition($condition, $expression.($limit ? " LIMIT ".($limit + 1) : ""));
	$rows = $q->NumRows();
	$result = "";
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

	echo $result;

?>