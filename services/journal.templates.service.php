<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	$journal = new Journal($forum_id);
	$journal->Retrieve();

	if ($journal->IsEmpty()) {
		echo "this.is_journal=0;";
		if ($go) {
			echo JsAlert("��������� �������� ����������� ������ ��� ��������.", 1);
		} else {
			echo JsAlert("������ �� ������.", 1);
		}
		die;
	}

	$access = $journal->GetAccess($user->User->Id);
	if ($access != Forum::FULL_ACCESS && !$user->IsSuperAdmin()) {
		echo "this.is_journal=0;";
		echo JsAlert("��� ������� � ���������� �������!", 1);
		exit;
	}
	
	$template = new JournalTemplate();
	$template->FillByForumId($journal->Id);

	$settings = new JournalSettings();
	$settings->GetByForumId($journal->Id);

	switch ($go) {
		case "save":
			if ($template->IsEmpty()) {
				$template->ForumId = $journal->Id;
			}
			$template->Body = UTF8toWin1251($_POST[JournalTemplate::BODY]);
			$template->Message = UTF8toWin1251($_POST[JournalTemplate::MESSAGE]);
			$template->Css = UTF8toWin1251($_POST[JournalTemplate::CSS]);
			$template->Save();

			$settings->UserId = $user->User->Id;
			$settings->SkinTemplateId = round($_POST[JournalSettings::SKIN_TEMPLATE_ID]);
			$settings->Save();

			echo JsAlert("��������� ������� ���������.");
			break;
	}	
	if ($template->IsEmpty()) {
		echo JsAlert("���������������� ������ �� ���������� - ���� ������ �� ���������.");
		$skin = new JournalSkin();
		$skin->GetDefault();
		$template->GetById($skin->TemplateId);
	}
	echo "this.data=".$template->ToJs().";";
	echo "this.skinTemplateId=".round($settings->SkinTemplateId).";";
	echo "this.is_journal=1;";

?>