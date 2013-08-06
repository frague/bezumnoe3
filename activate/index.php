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
			$error = "������������ �� ���������� ����� �� ������!";
		}
	} else {
		$error = "������ ������������ ���� ���������!";
	}
	
	if ($error) {
		ErrorPage("������ ��������� �������.", $error);
		die;
	} 


	$p = new Page("��������� ��������");
	$p->AddCss("register.css");
	$p->PrintHeader();
	
	echo "�����������, <b>".$user->Login."</b>, ��� ������� ������� �����������!";
	echo "<div class=\"Spacer\"></div>";

	SaveLog("��������� ������� <b>".$user->Login."</b>.", $user->Id, "", AdminComment::SEVERITY_WARNING);

	$p->PrintFooter();

?>