<?php
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	$commenterId = round($_POST["user_id"]);

	$forbid = new JournalForbiddenCommenter();

	$commenter = new User();
	if ($commenterId > 0 && $commenter->UserDoesExist($commenterId)) {
		$forbid->UserId = $user->User->Id;
		$forbid->CommenterId = $commenterId;
		if (!$state) {
			if ($forbid->Save()) {
				echo AddJsAlert("Пользователь добавлен.");
			} else {
				echo AddJsAlert("Пользователь уже в списке!", 1);
			}
		} else {
			if ($forbid->Delete()) {
				echo AddJsAlert("Пользователь удалён из списка.");
			} else {
				echo AddJsAlert("Пользователя нет в списке!", 1);
			}
		}
	}

	/* Show user forbidden commenters */
	echo "var userlist=[";
	$q = $forbid->GetByUserId($user->User->Id, true);
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$forbid->FillFromResult($q);
		echo ($i ? "," : "").$forbid->ToJs($q->Get(User::LOGIN));
	}
	echo "]";
	$q->Release();

?>
