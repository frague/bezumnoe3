<?

	$root = "../";
	require_once $root."server_references.php";

	$room = new Room();
    $q = $room->GetAllAlive();
    $q->NextResult();
    $room->FillFromResult($q);
    $q->Release();

	if ($room->IsEmpty()) {
		return;
	}

	// print "/* ".$_SERVER["HTTP_X_FORWARDED_FOR"]." */";

	$message = str_replace("<", "&lt;", $_POST["message"]);
	$message = str_replace(">", "&gt;", $message);
	$message = substr($message, 0, 1024);

	$user = str_replace("<", "&lt;", $_POST["user"]);
	$user = str_replace(">", "&gt;", $user);

	if ($message) {
		$msg = new TelegramMessage($user, $message, $room->Id);
		$msg->Save();
	}

?>
