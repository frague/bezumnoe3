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


	$condition = "";

	// Dates condition
	$d = $_POST["DATE"];
	if ($d) {
		$t = ParseDate($d);
		if ($t !== false) {
			$condition = "t1.".AdminComment::DATE." LIKE '".DateFromTime($t, "Y-m-d")."%' ";
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