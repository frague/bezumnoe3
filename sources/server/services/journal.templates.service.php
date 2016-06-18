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
	
	$settings = new JournalSettings();
	$settings->GetByForumId($journal->Id);

	$template = new JournalTemplate();
	if ($settings->OwnMarkupAllowed) {
		$template->FillByForumId($journal->Id);
	}

	switch ($go) {
		case "save":
			if ($template->IsEmpty()) {
				$template->ForumId = $journal->Id;
			}
			
			if ($settings->OwnMarkupAllowed) {
				$template->Title = UTF8toWin1251($_POST[JournalTemplate::TITLE]);
				$template->Body = UTF8toWin1251($_POST[JournalTemplate::BODY]);
				$template->Message = UTF8toWin1251($_POST[JournalTemplate::MESSAGE]);
				$template->Css = UTF8toWin1251($_POST[JournalTemplate::CSS]);
				$template->Save();
			}

			$settings->UserId = $user->User->Id;
			$settings->SkinTemplateId = round($_POST[JournalSettings::SKIN_TEMPLATE_ID]);
			$settings->Save();

			echo JsAlert("��������� ������� ���������.");
			break;
	}	
	// Getting default skin
	$skin = new JournalSkin();
	$defaultTemplateId = $skin->GetDefaultTemplateId();

	if ($template->IsEmpty() && $settings->OwnMarkupAllowed) {
		echo JsAlert("���������������� ������ �� ���������� - ���� ������ �� ���������.");
		$template->GetById($defaultTemplateId);
	}
	echo "this.data=".$template->ToJs(round($settings->SkinTemplateId)).";";
	echo "this.skinTemplateId=".round($settings->SkinTemplateId).";";
	echo "this.ownMarkupAllowed=".round($settings->OwnMarkupAllowed).";";
	echo "this.defaultTemplateId=".round($defaultTemplateId).";";
	echo "this.is_journal=1;";

?>