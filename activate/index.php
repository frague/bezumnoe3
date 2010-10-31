<?php

	$root = "../";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	Head("Активация аккаунта", "register.css", "", "", "", "register.gif");
	require_once $root."references.php";


?><img src="/img/spacer.gif" border="0" width="1" height="400" style="float:right" /><?php

	$guid = trim(LookInRequest("g"));


	if ($guid && strlen($guid) == 10) {
		$user = new User();
		$user->FillByCondition("t1.".User::GUID."='_".SqlQuote($guid)."'");
		if (!$user->IsEmpty()) {
			$user->Guid = substr($user->Guid, 1);
			$user->Save();
		
			SaveLog("Активация профиля <b>".$user->Login."</b>.", $user->Id, "", AdminComment::SEVERITY_WARNING);

			echo "Поздравляем, <b>".$user->Login."</b>, ваш аккаунт успешно активирован!";
		} else {
			$error = "Указан некорректный ключ активации!";
		}
	} else {
		$error = "Указан некорректный ключ активации!";
	}
	
	if ($error) {
		echo "<div id='Summary'>Ошибка активации профиля: <li>".$error."</div>";
	}


	Foot();

?>