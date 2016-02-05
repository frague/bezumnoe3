<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty()) {
		exit();	// TODO: Implement client functionality
	}

	$value = SqlQuote(trim(substr(UTF8toWin1251($_POST["value"]), 0, 20)));

	$condition = "";
	$expression = "";
	$u = new User();

	if (!$value) {
		exit;
	}

	$limit = 0;
	$expression = $u->FindUserWithJournalsExpression();
	$condition = "t1.".User::LOGIN." LIKE '%".$value."%' OR t2.".Nickname::TITLE." LIKE '%".$value."%'";
	$limit = 20;

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
		$title = Mark($q->Get(Forum::TITLE), $value);

		$result .= ($i > 0 ? "," : "")."new judto(".$q->Get(User::USER_ID).",'".JsQuote($login)."','".JsQuote($nick)."','".$q->Get(Forum::FORUM_ID)."','".JsQuote($title)."')";
	}
	$result .= "];";
	$q->Release();

	echo $result;

?>
