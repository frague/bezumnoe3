<?php

	$root = "../";
	require_once $root."server_references.php";

	$mr = 0;
	$tr = 0;

	$meTelegramId = $_POST["mtid"];
	$targetTelegramId = $_POST["ttid"];
	$digitsOnly = "/^\d+$/";

	$result = array();
	if (preg_match($digitsOnly, $meTelegramId)) {
		$status = new Status();
		$status->GetByTelegramId($meTelegramId);
		$mr = $status->Rights;
		$result["me"] = $mr;

		if (preg_match($digitsOnly, $targetTelegramId)) {
			$status->GetByTelegramId($targetTelegramId);
			$tr = $status->Rights;
			$result["target"] = $tr;
		}
	}
	print json_encode($result);

?>
