<?php
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty() || !$user_id) {
		exit;
	}

	/* Reading allowed forums */

	$currentForum = "";

	$fu = new ForumUser();
	$q = $fu->GetUserForums($user->User->Id);
	echo "this.data=[";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$forumId = $q->Get(ForumUser::FORUM_ID);
		$type = substr($q->Get(Forum::TYPE), 0, 1);

		if (!$currentForum && $type == "j") {
			$currentForum = $forumId;
		}

		echo ($i ? "," : "")."new fldto(".
$forumId.",\"".
$q->Get(ForumUser::ACCESS)."\",\"".
JsQuote($q->Get(Forum::TITLE))."\",\"".
$type."\",\"".
JsQuote($q->Get(User::LOGIN))."\")";
	}
	echo "];";
	$q->Release();
	echo "this.Forum.FORUM_ID='".$currentForum."';";

?>
