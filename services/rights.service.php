<?php

	$root = "../";
	require_once $root."server_references.php";

	$mr = 0;
	$tr = 0;

	$meTelegramId = $_POST["mtid"];
	$targetTelegramId = $_POST["ttid"];
	$digitsOnly = "/^\d+$/";

	if (preg_match($digitsOnly, $meTelegramId) && preg_match($digitsOnly, $targetTelegramId)) {
		$status = new Status();
		$status->GetByTelegramId($meTelegramId);
		$mr = $status->Rights;
		$status->GetByTelegramId($targetTelegramId);
		$tr = $status->Rights;
	}
	print json_encode(array("me" => $mr, "target" => $tr));

?>
