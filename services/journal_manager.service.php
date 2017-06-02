<?php
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty() || !$user_id) {
		exit;
	}



	/* Check if user has at least 1 journal */

	$fu = new ForumUser();
	$q = $fu->GetUserForums($user->User->Id, "t2.".Forum::TYPE."='".Forum::TYPE_JOURNAL."'");
	$hasJournal = ($q->NumRows() ? 1 : 0);

	$q->Release();

	if ($go == "create") {
		if ($hasJournal) {
			echo JsAlert("Ваш персональный журнал уже создан!", 1);
		} else {
			$journal = new Journal();
			$journal->Title = $user->User->Login;
			$journal->Description = "Персональный журнал";

			$journal->LinkedId = $user->User->Id;
			$journal->Save();

			$settings = new JournalSettings();
			$settings->ForumId = $journal->Id;
			$settings->Alias = MakeGuid(10);

			$template = new JournalTemplate();
			$skin = new JournalSkin();
			$settings->SkinTemplateId = $skin->GetDefaultTemplateId();
			$settings->Save();

			$template = new JournalTemplate($settings->SkinTemplateId);
			$template->Retrieve();
			$template->ForumId = $journal->Id;
			$template->Id = -1;
			$template->Save();

			echo JsAlert("Ваш персональный журнал успешно создан!");
			SaveLog("Создан персональный журнал.", $user->User->Id, $user->User->Login, AdminComment::SEVERITY_WARNING);

			$hasJournal = 1;
		}
	}

	echo "this.HasJournal=".$hasJournal.";";

	/* Filtering condition */

	$condition = "";
	$conds = array("SHOW_FORUMS" => Forum::TYPE_FORUM, "SHOW_GALLERIES" => Forum::TYPE_GALLERY, "SHOW_JOURNALS" => Forum::TYPE_JOURNAL);
	foreach ($conds as $key => $type) {
		if ($_POST[$key]) {
			$condition .= ($condition ? " OR " : "")."t2.".Forum::TYPE."='".$conds[$key]."'";
		}
	}

	if (!$condition) {
		$condition = "1=0";
	}

	/* Reading allowed forums */

	$q = $fu->GetUserForums($user->User->Id, $condition);
	echo "this.data=[";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();

		echo ($i ? "," : "")."new jjdto(".
$q->Get(ForumUser::FORUM_ID).",\"".
JsQuote($q->Get(Forum::TITLE))."\",\"".
substr($q->Get(Forum::TYPE), 0, 1)."\",".
$q->Get(ForumUser::ACCESS).")";
	}
	echo "];";
	$q->Release();


?>
