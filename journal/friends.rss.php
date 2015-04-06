<?php
    
    $root = "../";
    require_once $root."server_references.php";
    require_once "journal.template.php";

    // Request values
    $alias = substr(LookInRequest(JournalSettings::PARAMETER), 0, 20);

    // Init variables
    $record = new JournalRecord();
    $settings = new JournalSettings();
    $showMessages = 30;

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

    // Journal owner
    $user_id = round($journal->LinkedId);
    $person = new User($user_id);
    $person->Retrieve();
    $profile = new Profile();
    $profile->GetByUserId($user_id);


    header("Content-Type: text/xml");
    
    $showMessages = 30;

    $rss_channel = new rssGenerator_channel();
    $rss_channel->title = "Френдлента ".$person->Login;
    $rss_channel->link = "http://www.bezumnoe.ru/journal/".$alias."/friends";
    $rss_channel->description = "Сообщения во френдленте";
    $rss_channel->language = "en-us";
    $rss_channel->generator = "";
    $rss_channel->managingEditor = "bezumnoe@gamil.com";
    $rss_channel->webMaster = "bezumnoe@gamil.com";

    $q = $record->GetFriendlyTopics($journal->Id, 0, $showMessages);

    $message = new JournalRecord();

    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();

        $message->FillFromResult($q);
        $alias = $q->Get(JournalSettings::ALIAS);

        $item = new rssGenerator_item();
        $item->author = $message->Author;
        $item->title = MakeTagsPrintable($message->Author.": \"".$message->Title."\"");
        $item->description = MakeTagsPrintable(RenderPostsLinks(nl2br(FormatMessageBody($message, $alias, false))));
        $item->link = "http://www.bezumnoe.ru/journal/".$alias."/post".$message->Id."/";
        $item->pubDate = date("r", ParseDate($message->Date));
        $rss_channel->items[] = $item;
    }
    $q->Release();

    $rss_feed = new rssGenerator_rss();
    $rss_feed->encoding = "windows-1251";
    $rss_feed->version = "2.0";
    echo $rss_feed->createFeed($rss_channel);

?>
