<?php

	$root = "../";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	Head("��������� ��������", "register.css", "", "", "", "register.gif");
	require_once $root."references.php";


?><img src="/img/spacer.gif" border="0" width="1" height="400" style="float:right" /><?php

	$guid = trim(LookInRequest("g"));


	if ($guid && strlen($guid) == 10) {
		$user = new User();
		$user->FillByCondition("t1.".User::GUID."='_".SqlQuote($guid)."'");
		if (!$user->IsEmpty()) {
			$user->Guid = substr($user->Guid, 1);
			$user->Save();
		
			SaveLog("��������� ������� <b>".$user->Login."</b>.", $user->Id, "", AdminComment::SEVERITY_WARNING);

			echo "�����������, <b>".$user->Login."</b>, ��� ������� ������� �����������!";
		} else {
			$error = "������ ������������ ���� ���������!";
		}
	} else {
		$error = "������ ������������ ���� ���������!";
	}
	
	if ($error) {
		echo "<div id='Summary'>������ ��������� �������: <li>".$error."</div>";
	}


	Foot();

?>