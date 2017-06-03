<?php 
	$root = "../";
	require_once $root."server_references.php";

	// print "/* ".$_SERVER["HTTP_X_FORWARDED_FOR"]." */";

	$userId = (int) $_POST["user_id"];

	$username = str_replace("<", "&lt;", $_POST["username"]);
	$username = str_replace(">", "&gt;", $username);

	if ($userId && $username) {
		$link = new TelegramId($userId);
		$link->TelegramUsername = $username;
		$link->Delete();
		$link->Save();

		print json_encode(array(uuid => $link->Uuid));
	}

?>
