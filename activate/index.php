<?php

	$root = "../";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	require_once $root."references.php";

	$guid = trim(LookInRequest("g"));


	if ($guid && strlen($guid) == 10) {
		$user = new User();
		$user->FillByCondition("t1.".User::GUID."='_".SqlQuote($guid)."'");
		if (!$user->IsEmpty()) {
			$user->Guid = substr($user->Guid, 1);
			$user->Save();
		} else {
			$error = "Пользователь по указанному ключу не найден!";
		}
	} else {
		$error = "Указан некорректный ключ активации!";
	}
	
	if ($error) {
		ErrorPage("Ошибка активации профиля.", $error);
		die;
	} 


	$p = new Page("Активация аккаунта");
	$p->AddCss("register.css");
	$p->PrintHeader();
	
	echo "Поздравляем, <b>".$user->Login."</b>, ваш аккаунт успешно активирован!";
	echo "<div class=\"Spacer\"></div>";

	SaveLog("Активация профиля <b>".$user->Login."</b>.", $user->Id, "", AdminComment::SEVERITY_WARNING);

	$p->PrintFooter();

?>