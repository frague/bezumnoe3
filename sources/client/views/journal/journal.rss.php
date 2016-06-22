<?php

    $root = "../";
    require_once $root."server_references.php";

    // Request values
    $alias = substr(LookInRequest(JournalSettings::PARAMETER), 0, 20);

    // Init variables
    $forumId = 0;
    $record = new JournalRecord();
    $settings = new JournalSettings();


    // Getting the journal
    if ($alias) {
        $settings->GetByAlias($alias);
        if ($settings->Alias != $alias || $settings->IsEmpty()) {
            DieWith404();
        }
    }

    // Getting the journal by settings
    $journal = new Journal($settings->ForumId);
    $journal->Retrieve();
    if (!$journal->IsFull()) {
        DieWith404();
    }
    $forumId = $journal->Id;



    header("Content-Type: text/xml");

    $showMessages = 30;


    $rss_channel = new rssGenerator_channel();
    $rss_channel->title = $journal->Title;
    $rss_channel->link = "http://www.bezumnoe.ru/journal/".$settings->Alias."/";
    $rss_channel->description = $journal->Description;
    $rss_channel->language = "en-us";
    $rss_channel->generator = "";
    $rss_channel->managingEditor = "bezumnoe@gmail.com";
    $rss_channel->webMaster = "bezumnoe@gmail.com";

    $message = new JournalRecord();
    $q = $message->GetJournalTopics(Journal::READ_ONLY_ACCESS, 0, $showMessages, $journal->Id);


//  function FormatMessageBody($message, $userUrlName, $isSingleMessage) {

    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();

        $message->FillFromResult($q);

        $item = new rssGenerator_item();
        $item->author = $message->Author;
        $item->title = MakeTagsPrintable($message->Title);
        $item->description = MakeTagsPrintable(nl2br(RenderPostsLinks(FormatMessageBody($message, $settings->Alias, false))));
        $item->link = "http://www.bezumnoe.ru/journal/".$settings->Alias."/post".$message->Id."/";
        $item->pubDate = date("r", ParseDate($message->Date));
        $rss_channel->items[] = $item;
    }
    $q->Release();

    $rss_feed = new rssGenerator_rss();
    $rss_feed->encoding = "utf-8";
    $rss_feed->version = "2.0";
    echo $rss_feed->createFeed($rss_channel);

?>
