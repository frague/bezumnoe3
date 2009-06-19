<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "journal.template.php";

	$alias = substr(LookInRequest(JournalSettings::PARAMETER), 0, 20);
	$show_from = round(LookInRequest("from"));
	$record_id = round(LookInRequest(JournalRecord::ID_PARAM));

	$settings = new JournalSettings();
	$record = new JournalRecord();

	$forumId = 0;

	if ($record_id > 0) {
		$record->GetById($record_id);
		if ($record->IsEmpty()) {
			DieWith404();
		}
		$forumId = $record->ForumId;
		$settings->GetByUserId($record->UserId);
	}
	
	if ($alias) {
		if ($settings->IsEmpty()) {
			$settings->GetByAlias($alias);
		} elseif ($settings->Alias != $alias) {
			DieWith404();
		}
	}

	if ($settings->IsEmpty()) {
		DieWith404();
	}

	if (!$forumId) {
		$journal = new Journal();
		$journal->GetByUserId($settings->UserId);
		if (!$journal->IsFull()) {
			DieWith404();
		}
		$forumId = $journal->Id;
	}

	// Checking if journal is protected and logged user has access to it
	$access = 1 - $journal->IsProtected;
	if ($someoneIsLogged) {
		$access = $journal->GetAccess($user->User->Id);
	}
	
	if ($access == Journal::NO_ACCESS) {
		error("У вас нет доступа к журналу.");
		Foot();
		die;
	}
	

	/* -------------- Getting Journal Template -------------- */

	$template = new JournalTemplate();
	if ($settings->SkinTemplateId > 0) {
		$template->GetById($settings->SkinTemplateId);
	} else {
		$template->FillByUserId($settings->UserId);

		if ($template->IsEmpty()) {
			// Getting default template
			$skin = new JournalSkin();
			$template->GetById($skin->GetDefaultTemplateId());
		} elseif (!$template->IsComplete()) {
			// Merging empty template fields from Default
			$skin = new JournalSkin();
			$defaultTemplate = new JournalTemplate();
			$defaultTemplate->GetById($skin->GetDefaultTemplateId());

			if (!$defaultTemplate->IsEmpty()) {
				$template->MergeWith($defaultTemplate);
				if ($template->Id != $defaultTemplate->Id) {
					$template->Save();
				}
			}
		}
	}
	
	if (!$template) {
		DieWith404();
	}

	
	/* -------------- Create markup -------------- */

	$user_id = round($settings->UserId);
	
	$datesCondition = "";
	
	/* ----------------- Messages ---------------- */
        
    $bodyText = $template->Body;
	
	$last_date = "";
	$messageChunk = "##MESSAGE##";
        
	$message = new JournalRecord();
	$shownMessages = substr_count($bodyText, $messageChunk);
	$showFrom = round($show_from) * $shownMessages;

	// ----- Select parameters
		// TODO: Dates condition
	
	// -----

	if ($record_id > 0) {
		$showFrom = 0;
		$shownMessages = 1;
		DisplayRecord($record);
		$addTitle =  $record->Title;
	} else {
		$q = $message->GetJournalTopics($access, $showFrom, $shownMessages, $forumId);
		$messagesFound = $q->NumRows();

		for ($i = 0; $i < $messagesFound; $i++) {
			$q->NextResult();

			$message->FillFromResult($q);
			DisplayRecord($message);
		}
		$addTitle = "";
	}

	$bodyText = str_replace($messageChunk, "", $bodyText);

	//------------------ Styles ------------------
	//		$bodyText = "<script src='/js.new/go.js'></script>\n".$bodyText;

	$person = new User($user_id);
	$person->Retrieve();
	$profile = new Profile();
	$profile->GetByUserId($user_id);

	$bodyText = str_replace("##PERSON##", $person->Login, $bodyText);
	$bodyText = str_replace("##ENCPERSON##", $settings->Alias, $bodyText);
	$bodyText = str_replace(
		"##AVATAR##", 
		$profile->Avatar ? "<img class='AvatarImg' src='/images/avatars/".$profile->Avatar."' alt='' />" : "", 
		$bodyText);

	$bodyText = str_replace("##MESSAGETITLE##", $addTitle, $bodyText);

//------------------ Pages -----------------
	if ($dateExists) {
		$q = $db->Query("SELECT COUNT(*) AS records FROM ".$journal." WHERE login='".$person."'".$viewTypes.$datesCondition." LIMIT 1", $db);
		$q->NextResult();
		$pagerRecords = $q->Get("records");
	} else {
		$pagerRecords = $totalRecords;
	}
	$bodyText = str_replace("##PAGES##", MakeJournalPager($userUrlName, $pagerRecords, $shownMessages, $showFrom, false), $bodyText);


//------------------ Calendar -----------------
	$bodyText = str_replace("##CALENDAR##", $calendar->Out, $bodyText);


//------------------ Friends ------------------
	$friends = new JournalFriend();
	$q = $friends->GetByJournalId($forumId);

	$friendsLink = "";

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();

		$xLogin = $q->Get(User::LOGIN);
		$xAlias = $q->Get(JournalSettings::ALIAS);
		$friendsLink .= ($friendsLink ? ", " : "").JournalSettings::MakeLink($xAlias, $xLogin);
	}
	$friendsLink = "<span id=friends>".$friendsLink."</span>";

	$bodyText = str_replace("##FRIENDS##", $friendsLink, $bodyText);
//	$bodyText = str_replace("##FRIENDSLINK##", "/journal/".$userUrlName."/friends/", $bodyText);

	$bodyText = str_replace("##USERURLNAME##", $settings->Alias, $bodyText);
        
	$bodyText = InjectionProtection(OuterLinks(MakeLinks($bodyText)));

	$bodyText = str_replace(
		"##STYLES##", 
		"<link rel=stylesheet type=text/css href='/journal/css/".$settings->Alias.".css'>", 
		$bodyText);

	echo $bodyText;

	// ----------------- Close opened tags -----------------
	$doClose = "";
	while (list($k, $v) = each($tagsStack)) {
		if (round($v) > 0 && $k != "img" && $k != "br" && $k != "meta" && $k != "hr") {
			$doClose = str_repeat("</$k>", $v).$doClose;
		}
	}
	echo $doClose;
	// ----------------- Close opened tags -----------------


	function DisplayRecord($message) {
	  global $template, $settings, $bodyText, $messageChunk, $record_id;

		$messageText = FormatMessage($message, $template->Message, $settings->Alias, $record_id > 0);

		$position = strpos($bodyText, $messageChunk);
		if ($position === 0) {
			break;
		} else {
			$bodyText = substr_replace($bodyText, $messageText, $position, strlen($messageChunk));
		}
	}

?>
