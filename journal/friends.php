<?php

    $root = "../";
    require_once $root."server_references.php";
    require_once "journal.template.php";

    // Request values
    $alias = substr(LookInRequest(JournalSettings::PARAMETER), 0, 20);
    $show_from = round(LookInRequest("from"));
    $record_id = round(LookInRequest(JournalRecord::ID_PARAM));
//  $tag = trim(substr(LookInRequest(TAG::PARAMETER), 0, 100));

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
/*  if ($someoneIsLogged) {
        $access = $journal->GetAccess($user->User->Id);
    }*/

    if ($access == Journal::NO_ACCESS) {
        ErrorPage("У вас нет доступа к журналу.", "Владелец журнала ограничил к нему доступ.");
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


    $globalTemplate = "<!DOCTYPE html>
<html lang=\"ru\">
<head>
    <meta charset=\"windows-1251\" />
    <title>".$template->Title."</title>
    <link rel=\"icon\" href=\"/img/icons/favicon.ico\" type=\"image/x-icon\" />
    <link rel=\"shortcut icon\" href=\"/img/icons/favicon.ico\" type=\"image/x-icon\" />
    <link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS: ".HtmlQuote($journal->Title)."\" href=\"/journal/##USERURLNAME##/friends/rss/\" />
    ##META##
    <link href=\"/journal/css/".$template->Id.".css\" />
".file_get_contents($root."inc/ui_parts/google_analythics.php")."
</head>

<body>
    ##BODY##
    ##CLOSINGTAGS##
    ".file_get_contents($root."/inc/ui_parts/li.php")."
</body>
</html>";



    $bodyText = $template->Body;

    $shownMessages = substr_count($bodyText, $messageChunk);
    $showFrom = round($show_from) * $shownMessages;

//      $q = $record->GetFriendlyTopics($journal->Id, $showFrom, $shownMessages, $condition);
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
    $record->Clear();   // Needed to check if single record has been requested

    $bodyText = str_replace($messageChunk, "", $bodyText);


    // --- Rendering the Pager
    // Getting number of records in case of dates condition

    $pagerRecords = $record->GetFriendlyThreadsCount($journal->Id);

    if (!$record->IsEmpty()) {
        $bodyText = str_replace("##PAGES##", "", $bodyText);
    } else {
        $bodyText = str_replace("##PAGES##", MakeJournalPager($settings->Alias, $pagerRecords, $shownMessages, $showFrom, true), $bodyText);
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

    $bodyText = RenderPostsLinks($bodyText);

    print $bodyText;

    include $root."/inc/li_spider_check.inc.php";


?>
