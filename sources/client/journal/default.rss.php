<?php

    $root = "../";
    require_once $root."server_references.php";

    $message = new JournalRecord();

    header("Content-Type: text/xml");

    $showMessages = 30;

    $rss_channel = new rssGenerator_channel();
    $rss_channel->title = "Журналы на Безумное.Ру";
    $rss_channel->link = "http://www.bezumnoe.ru/journal/";
    $rss_channel->description = "Сообщения в журналах пользователей";
    $rss_channel->language = "en-us";
    $rss_channel->generator = "";
    $rss_channel->managingEditor = "bezumnoe@gamil.com";
    $rss_channel->webMaster = "bezumnoe@gamil.com";

    $q = $message->GetMixedJournalsTopics(-1, 0, $showMessages);

    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();

        $message->FillFromResult($q);
        $alias = $q->Get(JournalSettings::ALIAS);

        $item = new rssGenerator_item();
        $item->author = $message->Author;
        $item->title = MakeTagsPrintable($message->Author.": \"".$message->Title."\"");
        $item->description = MakeTagsPrintable(nl2br(FormatMessageBody($message, $alias, false)));
        $item->link = "http://www.bezumnoe.ru/journal/".$alias."/post".$message->Id."/";
        $item->pubDate = date("r", ParseDate($message->Date));
        $rss_channel->items[] = $item;
    }
    $q->Release();

    $rss_feed = new rssGenerator_rss();
    $rss_feed->encoding = "utf8";
    $rss_feed->version = "2.0";
    echo $rss_feed->createFeed($rss_channel);

?>
