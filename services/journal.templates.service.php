<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	$template = new JournalTemplate();
	$template->FillByUserId($user->User->Id);

	$settings = new JournalSettings();
	$settings->GetByUserId($user->User->Id);

	switch ($go) {
		case "save":
			if ($template->IsEmpty()) {
				$template->UserId = $user->User->Id;
			}
			$template->Body = UTF8toWin1251($_POST[JournalTemplate::BODY]);
			$template->Message = UTF8toWin1251($_POST[JournalTemplate::MESSAGE]);
			$template->Css = UTF8toWin1251($_POST[JournalTemplate::CSS]);
			$template->Save();

			$settings->UserId = $user->User->Id;
			$settings->SkinTemplateId = round($_POST[JournalSettings::SKIN_TEMPLATE_ID]);
			$settings->Save();

			echo JsAlert("Настройки шаблона сохранены.");
			break;
	}	
	if ($template->IsEmpty()) {
		echo JsAlert("Пользовательский шаблон не существует - взят шаблон по умолчанию.");
		$skin = new JournalSkin();
		$skin->GetDefault();
		$template->GetById($skin->TemplateId);
	}
	echo "this.data=".$template->ToJs().";";
	echo "this.skinTemplateId=".round($settings->SkinTemplateId).";";

?>