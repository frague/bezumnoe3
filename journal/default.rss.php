<?
    require "../inc/site.inc.php";
    require "../inc/template.inc.php";
    require "../inc/journal.inc.php";

    require_once "../inc/classes/sql.class.php";
    require "../inc/classes/rss_generator.class.php";

    require "../inc/helpers/string.helper.php";
    require "../inc/helpers/journal.helper.php";

    require "../inc/classes/journal/message.journal.class.php";


	header('Content-Type: text/xml');
	
	$showMessages = 30;

    function DeTag($text) {
    	$text = str_replace("&", "&amp;", $text);
    	$text = str_replace("<", "&lt;", $text);
    	$text = str_replace(">", "&gt;", $text);
    	return $text;
    }

	$rss_channel = new rssGenerator_channel();
	$rss_channel->title = 'Журналы на Безумное.Ру';
	$rss_channel->link = 'http://www.bezumnoe.ru/journal';
	$rss_channel->description = 'Сообщения в журналах пользователей';
	$rss_channel->language = 'en-us';
	$rss_channel->generator = '';
	$rss_channel->managingEditor = 'zayets@bezumnoe.ru';
	$rss_channel->webMaster = 'zayets@bezumnoe.ru';

	$db = new SQL($db_name, $host, $user, $pass);
     
	$message = new JournalMessage();
	$q = $message->GetByCondition("kind='pu' ORDER BY moment DESC LIMIT ".$showMessages, $db);

	for ($i = 0; $i < $q->NumRows(); $i++) {
       	$q->NextResult();

		$message->FillFromResult($q);
		$message->GetComments($db);

      	$authorUrlName = PrepareUrlName($message->Author);

		$item = new rssGenerator_item();
		$item->author = $message->Author;
		$item->title = DeTag($message->Title);
		$item->description = DeTag(nl2br(FormatMessageBody($message, $authorUrlName)));
		$item->link = 'http://www.bezumnoe.ru/journal/'.$authorUrlName.'/post'.$message->Id.'.html';
		$item->pubDate = date("r", $message->Date);
		$rss_channel->items[] = $item;
	}

	$rss_feed = new rssGenerator_rss();
	$rss_feed->encoding = 'windows-1251';
	$rss_feed->version = '2.0';
	echo $rss_feed->createFeed($rss_channel);

?>