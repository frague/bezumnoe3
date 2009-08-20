<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();	// TODO: Implement client functionality
	}

	$u = new UserComplete($user_id);
	$u->Retrieve();

	if (!$user->IsSuperAdmin()) {	// Coz I can! (Потому, что я - ведро)
		if ($u->User->IsEmpty()) {
			echo JsAlert("Пользователь не найден!", 1);
			die;
		}

		if ($u->Status->Rights <= $user->StatusRights) { 
			echo JsAlert("Недостаточно прав для просмотра!", 1);
			die;
		}
	}

	$comment = new AdminComment();
	if ($go == "save") {
		$comment->UserId = $user_id;
		$comment->Content = substr(strip_tags(trim(UTF8toWin1251($_POST["ADMIN_COMMENT"]))), 0, 1024);
		$comment->AdminLogin = $user->User->Login;
		$comment->Save();
		echo JsAlert("Комментарий добавлен.");
	}


	$condition = MakeSearchCriteria("DATE", AdminComment::DATE, "SEARCH", $comment->SearchTemplate);

	/* Filter events by type */
	function AddTypeCondition($key, $value, $condition) {
		if ($_POST[$key]) {
			return ($condition ? $condition." OR " : "")."t1.".AdminComment::SEVERITY."='".$value."'";
		}
		return $condition;
	}

	$typeFilter = AddTypeCondition("SEVERITY_NORMAL", AdminComment::SEVERITY_NORMAL, "");
	$typeFilter = AddTypeCondition("SEVERITY_WARNING", AdminComment::SEVERITY_WARNING, $typeFilter);
	$typeFilter = AddTypeCondition("SEVERITY_ERROR", AdminComment::SEVERITY_ERROR, $typeFilter);

	if (!$typeFilter) {
		echo "this.data=[];this.Total=0;";
		die;
	} else {
		$condition = ($condition ? $condition." AND " : "")."(".$typeFilter.")";
	}

	/* --- */
	

	echo "this.data=[";
	if ($user_id) {
		$q = $comment->GetByUserId($user_id, $from, $amount, $condition);
	} else if ($user->IsSuperAdmin()) {
		$q = $comment->GetRange($from, $amount, $condition, $comment->ReadWithNameExpression());
	}
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$comment->FillFromResult($q);
		$login = $q->Get(User::LOGIN);
		echo ($i ? "," : "").$comment->ToJs($login);
	}
	echo "];";
	echo "this.Total=".$comment->GetUserCommentsCount($user_id, $condition).";";

?>