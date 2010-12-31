<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty()) {
		exit();	// TODO: Implement client functionality
	}

	$u = new User();
	$expression = $u->FindUserExpression();
	$rows = 0;
	$value = SqlQuote(trim(substr(UTF8toWin1251($_POST["USER"]), 0, 20)));

	if ($value) {
		$condition = $value ? "(t1.".User::LOGIN." LIKE '%".$value."%' OR t2.".Nickname::TITLE." LIKE '%".$value."%')" : "1=1";
		$limit = 20;

		$q = $u->GetByCondition($condition." AND ".User::IS_DELETED."<>1", $expression.($limit ? " LIMIT ".($limit + 1) : ""));
		$rows = $q->NumRows();
		$result = "";
		if ($limit) {
			$result .= "this.more=".($rows > $limit ? 1 : 0).";";
			if ($rows > $limit) {
				$rows = $limit;
			}
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