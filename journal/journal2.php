<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "journal.template.php";

	// Request values
	$alias = substr(LookInRequest(JournalSettings::PARAMETER), 0, 20);
	$show_from = round(LookInRequest("from"));
	$record_id = round(LookInRequest(JournalRecord::ID_PARAM));
	$tag = trim(substr(LookInRequest(TAG::PARAMETER), 0, 100));

	// Init variables
	$forumId = 0;
	$altMonth = 0;
	$altYear = 0;
	$record = new JournalRecord();
	$settings = new JournalSettings();


	// Getting the journal
	if ($record_id > 0) {
		// ... by record
		$record->GetById($record_id);
		if ($record->IsEmpty()) {
			DieWith404();
		}
		$forumId = $record->ForumId;
		$settings->GetByForumId($record->ForumId);
	} elseif ($alias) {
		// ... or by the alias
		if ($settings->IsEmpty()) {
			$settings->GetByAlias($alias);
		}
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
	if ($someoneIsLogged) {
		$access = $journal->GetAccess($user->User->Id);
	}
	
	if ($access == Journal::NO_ACCESS) {
		Head("Ошибка", "forum.css", "forum.js");
		error("У вас нет доступа к журналу.");
		Foot();
		die;
	}

	JournalRating($journal->Id);


	// Form dates condition
	$datesCondition = "";
	$year = round(LookInRequest("year"));
	if ($year > 1990) {
		$datesCondition = sprintf("t1.".JournalRecord::DATE." LIKE '%04d", $year);
		$month = round(LookInRequest("month"));
		if ($month > 0 && $month <= 12) {
			$datesCondition = sprintf($datesCondition."-%02d", $month);
			$day = round(LookInRequest("day"));
			if ($day < 1 || $day > 31) {
				$day = 0;
			} else {
				$datesCondition = sprintf($datesCondition."-%02d", $day);
			}
		} else {
			$month = 0;
		}
		$datesCondition .= "%'";
	} else {
		$year = 0;
	}

	// Filtering by tag

	if ($tag) {
		$condition = "";
	} else {
		$condition = $datesCondition;
	}

	// Getting the template
	$template = GetTemplateOrDie($settings);
	$user_id = round($journal->LinkedId);
	

    $globalTemplate = "<!DOCTYPE html>
<html lang=\"ru\">
<head>
	<meta charset=\"windows-1251\" />
	<title>".$template->Title."</title>
	<link rel=\"icon\" href=\"/img/icons/favicon.ico\" type=\"image/x-icon\" />
	<link rel=\"shortcut icon\" href=\"/img/icons/favicon.ico\" type=\"image/x-icon\" />
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS: ".HtmlQuote($journal->Title)."\" href=\"/journal/##USERURLNAME##/rss/\" />
	##META##
	<link rel=\"stylesheet\" href=\"/journal/css/".$template->Id.".css\" />
".file_get_contents($root."inc/ui_parts/google_analythics.php")."
</head>

<body>
	##BODY##
	##CLOSINGTAGS##
	".file_get_contents($root."/inc/ui_parts/li.php")."
</body>
</html>";

	$bodyText = $template->Body2;
        
	$shownMessages = substr_count($bodyText, $messageChunk);
	$showFrom = round($show_from) * $shownMessages;
	$metaDescription = "";
	$usedTags = array();

	if (!$record->IsEmpty()) {
		// Displaying given record
		$showFrom = 0;
		$shownMessages = 1;
		DisplayRecord($record);
		$addTitle = " &mdash; ".$record->Title;

		$metaDescription = "<meta name=\"description\" content=\"Блог (журнал) на Безумное.РУ: '".MetaContent($journal->Title."' (".$journal->Description.") - ".$record->Title)."\" />
	<meta name=\"keywords\" content=\"блог, журнал, Саратов, ".MetaContent(join(", ", array_keys($usedTags)))."\" />";
	} else {
		// Show records by given criteria or from the beginning
		if ($tag) {
			$q = $record->GetJournalTopicsByTag($access, $showFrom, $shownMessages, $forumId, $tag);
		} else {
			$q = $record->GetJournalTopics($access, $showFrom, $shownMessages, $forumId, $condition);
		}
		$messagesFound = $q->NumRows();

		for ($i = 0; $i < $messagesFound; $i++) {
			$q->NextResult();

			$record->FillFromResult($q);
			DisplayRecord($record);
			if (!$month && !$altMonth) {
				$altMonth = date("n", strtotime($record->Date));
			}
			if (!$year && !$altYear) {
				$altYear = date("Y", strtotime($record->Date));
			}
		}
		$q->Release();

		$addTitle = "";
		$record->Clear();	// Needed to check if single record has been requested
		$metaDescription = "<meta name=\"description\" content=\"Журнал на Безумное.РУ: '".MetaContent($journal->Title."' (".$journal->Description).")\" />";
	}

	$bodyText = str_replace($messageChunk, "", $bodyText);

	// Get journal owner and their profile
	$person = new User($user_id);
	$person->Retrieve();
	$profile = new Profile();
	$profile->GetByUserId($user_id);

	// Substitute chunks with user data
	$bodyText = str_replace(
		"##AVATAR##", 
		$profile->Avatar ? "<img class='AvatarImg' src='/img/avatars/".$profile->Avatar."' alt='' />" : "", 
		$bodyText);

	// --- Rendering the Pager
	if ($tag) {
		// Getting number of records in case of tag condition
		$pagerRecords = $record->GetForumThreadsCountByTag($journal->Id, $access, $tag);
	} else if ($condition) {
		// Getting number of records in case of dates condition
		$pagerRecords = $record->GetForumThreadsCount($journal->Id, $access, $condition);
	} else {
		// .. else get total number of journal records
		$pagerRecords = $journal->TotalCount;
	}

	if (!$record->IsEmpty()) {
		$bodyText = str_replace("##PAGES##", "", $bodyText);
	} else {
		$bodyText = str_replace("##PAGES##", MakeJournalPager($settings->Alias, $pagerRecords, $shownMessages, $showFrom, false), $bodyText);
	}

	// --- Calendar
	$calendar = "";
	if ($record->IsEmpty()) {
		// Calendar will not be shown for single record
		// for caching purposes (up-to-date calendar)
		$m = $month;
		$y = $year;
		if ($datesCondition) {
			if ($month && $altMonth) {
				$m = $altMonth;
			}
		} else {
			$m = $altMonth;
			$y = $altYear;
		}

		$calendar = new Calendar($m, $y);
		$q = $db->Query($record->MonthDaysExpression($journal->Id, $calendar->Month, $calendar->Year));
		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();
			$day = $q->Get("DAY");
			$calendar->Days[$day] = MakeJournalLink($settings->Alias, $day, 0, $calendar->Year, $calendar->Month, $day);
		}
		// Previous + Next month links
		$m = ($month ? $month : $altMonth);
		$calendar->PrevMonth = MakeMonthLink($settings->Alias, $calendar->Year, $calendar->Month - 1);
		$calendar->NextMonth = MakeMonthLink($settings->Alias, $calendar->Year, $calendar->Month + 1);
	}
	$bodyText = str_replace("##CALENDAR##", $calendar, $bodyText);

	// --- List of Friends
	if (strpos($bodyText, "##FRIENDS##") !== false) {
		$friends = new JournalFriend();
		$q = $friends->GetByForumId($journal->Id);

		$friendsLink = "";
		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();

			$login = $q->Get(User::LOGIN);
			$alias = $q->Get(JournalSettings::ALIAS);

			$friendsLink .= ($friendsLink ? ", " : "").JournalSettings::MakeLink($alias, $login);
		}
		$friendsLink = "<span id=\"friends\">".$friendsLink."</span>";

		$bodyText = str_replace("##FRIENDS##", $friendsLink, $bodyText);
	}

	// --- Tags cloud
	if (strpos($bodyText, "##TAGSCLOUD##") !== false) {
		$tag = new Tag();
		$q = $tag->GetCloud($forumId);
		$cloud = "";
		$tags = array();
		$maxWeigth = 0;
		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();
			$t = new Tag();
			$t->FillFromResult($q);
			$tags[$i] = $t;
			$maxWeight = ($maxWeight > $t->Weight ? $maxWeight : $t->Weight);
		}
		$q->Release();

		for ($i = 0; $i < sizeof($tags); $i++) {
			$cloud.= ($i ? " " : "").$tags[$i]->ToCloud($maxWeight, $settings->Alias);
		}

		$bodyText = str_replace("##TAGSCLOUD##", $cloud, $bodyText);
	}

	$bodyText = str_replace("##FRIENDSLINK##", "/journal/".$settings->Alias."/friends/", $bodyText);
        
	$bodyText = InjectionProtection(OuterLinks(MakeLinks($bodyText)));

	// Write caching header
	if (!$record->IsEmpty()) {
		AddEtagHeader(strtotime($record->UpdateDate));
	}


	$bodyText = str_replace("##BODY##", $bodyText, $globalTemplate);

	$bodyText = str_replace("##CLOSINGTAGS##", RenderClosingTags(), $bodyText);

	$bodyText = str_replace("##MESSAGETITLE##", $addTitle, $bodyText);
	$bodyText = str_replace("##TITLE##", $journal->Title, $bodyText);
	$bodyText = str_replace("##DESCRIPTION##", $journal->Description, $bodyText);
	$bodyText = str_replace("##PERSON##", $person->Login, $bodyText);
	$bodyText = str_replace("##ENCPERSON##", $settings->Alias, $bodyText);
	$bodyText = str_replace("##USERURLNAME##", $settings->Alias, $bodyText);
	$bodyText = str_replace("##USERID##", $user_id, $bodyText);

	$bodyText = str_replace("##META##", $metaDescription, $bodyText);

	print $bodyText;

	include $root."/inc/li_spider_check.inc.php";
?>