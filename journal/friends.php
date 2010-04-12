<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "journal.template.php";

	// Request values
	$alias = substr(LookInRequest(JournalSettings::PARAMETER), 0, 20);
	$show_from = round(LookInRequest("from"));
	$record_id = round(LookInRequest(JournalRecord::ID_PARAM));
//	$tag = trim(substr(LookInRequest(TAG::PARAMETER), 0, 100));

	// Init variables
	$forumId = 0;
	$record = new JournalRecord();
	$settings = new JournalSettings();

	if ($alias) {
		$settings->GetByAlias($alias);
	}

	// Record belongs to another person's journal
	if ($alias && $settings->Alias != $alias) {
		DieWith404();
	}

	// Setting for journal not found
	if ($settings->IsEmpty()) {
		DieWith404();
	}

	// Getting the journal by settings
	$journal = new Journal($settings->ForumId);
	$journal->Retrieve();
	if (!$journal->IsFull()) {
		DieWith404();
	}
	$forumId = $journal->Id;
	
	// Checking if journal is protected and logged user has access to it
	$access = 1 - $journal->IsProtected;
/*	if ($someoneIsLogged) {
		$access = $journal->GetAccess($user->User->Id);
	}*/
	
	if ($access == Journal::NO_ACCESS) {
		Head("Ошибка", "forum.css", "forum.js");
		error("У вас нет доступа к журналу.");
		Foot();
		die;
	}

	// Journal owner
	$user_id = round($journal->LinkedId);
	$person = new User($user_id);
	$person->Retrieve();
	$profile = new Profile();
	$profile->GetByUserId($user_id);

	// Getting the template
	$skin = new JournalSkin();
	$template = new JournalTemplate($skin->GetFriendlyTemplateId());
	$template->Retrieve();

    $bodyText = $template->Body;
        
	$shownMessages = substr_count($bodyText, $messageChunk);
	$showFrom = round($show_from) * $shownMessages;

//		$q = $record->GetFriendlyTopics($journal->Id, $showFrom, $shownMessages, $condition);
	$q = $record->GetFriendlyTopics($journal->Id, $showFrom, $shownMessages);
	$messagesFound = $q->NumRows();

	for ($i = 0; $i < $messagesFound; $i++) {
		$q->NextResult();

		$settings->Alias = $q->Get(JournalSettings::ALIAS);

		$record->FillFromResult($q);
		DisplayRecord($record);
	}
	$q->Release();

	$settings->Alias = $alias;

	$addTitle = "";
	$record->Clear();	// Needed to check if single record has been requested

	$bodyText = str_replace($messageChunk, "", $bodyText);

	// Get journal owner and their profile
/*	$person = new User($user_id);
	$person->Retrieve();
	$profile = new Profile();
	$profile->GetByUserId($user_id);*/

	// Substitute chunks with user data
	$bodyText = str_replace("##TITLE##", $journal->Title, $bodyText);
	$bodyText = str_replace("##DESCRIPTION##", $journal->Description, $bodyText);

	$bodyText = str_replace("##PERSON##", $person->Login, $bodyText);
	$bodyText = str_replace("##ENCPERSON##", $settings->Alias, $bodyText);
/*	$bodyText = str_replace(
		"##AVATAR##", 
		$profile->Avatar ? "<img class='AvatarImg' src='/img/avatars/".$profile->Avatar."' alt='' />" : "", 
		$bodyText);*/

	$bodyText = str_replace("##MESSAGETITLE##", $addTitle, $bodyText);

	// --- Rendering the Pager
	// Getting number of records in case of dates condition

	$pagerRecords = $record->GetFriendlyThreadsCount($journal->Id);

	if (!$record->IsEmpty()) {
		$bodyText = str_replace("##PAGES##", "", $bodyText);
	} else {
		$bodyText = str_replace("##PAGES##", MakeJournalPager($settings->Alias, $pagerRecords, $shownMessages, $showFrom, true), $bodyText);
	}

	$bodyText = str_replace("##USERURLNAME##", $settings->Alias, $bodyText);
	$bodyText = str_replace("##FRIENDSLINK##", "/journal/".$alias."/friends/", $bodyText);
        
	$bodyText = InjectionProtection(OuterLinks(MakeLinks($bodyText)));

	// Insert reference to styles to prevent alternative ones
	$bodyText = str_replace("##STYLES##", "<link rel='stylesheet' type='text/css' href='/journal/css/".$template->Id.".css'>", $bodyText);

	echo $bodyText;
	// Opening tags closure (to safely insert footer banner)
	echo RenderClosingTags();

	include $root."/inc/li_spider_check.inc.php";


?>