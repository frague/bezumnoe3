<?php

    $root = "../";
    require_once $root."server_references.php";
    require_once $root."inc/helpers/like_buttons.helper.php";
    require_once "journal.template.php";

    $messagesPerPage = 20;
    $yesterday = DateFromTime(time() - 60*60*24);   // Yesterday

    $alias = substr(LookInRequest(JournalSettings::PARAMETER), 0, 20);
    $record_id = round(LookInRequest(JournalRecord::ID_PARAM));
    $from = round($_GET["from"]);

    // No record ID specified
    if ($record_id <= 0) {
        DieWith404();
    }

    $record = new JournalRecord($record_id);
    $record->Retrieve();

    // Record not found
    if ($record->IsEmpty()) {
        DieWith404();
    }

    $journal = new Journal($record->ForumId);
    $journal->Retrieve();
    if ($journal->IsEmpty()) {
        // Journal not found
        DieWith404();
    }

    $settings = new JournalSettings();
    $settings->GetByForumId($record->ForumId);

    if ($settings->IsEmpty() || ($alias && $settings->Alias != $alias)) {
        DieWith404();
    }

    $alias = $settings->Alias;

    // Checking if journal is protected and logged user has access to it
    $access = 1 - $journal->IsProtected;
    if ($someoneIsLogged) {
        $access = $journal->GetAccess($user->User->Id);
    }

    if ($access == Journal::NO_ACCESS) {
        ErrorPage("У вас нет доступа к журналу.", "Владелец журнала ограничил к нему доступ.");
        die;
    }

    JournalRating($journal->Id);

//  Etag removed to prevent authorized session caching
//  AddEtagHeader(strtotime($record->UpdateDate));

    $descr = MakeDescription($record);
    $meta_description = MetaContent($record->Title." - ".$descr);

    // Get message tags
    $tag = new Tag();
    $q = $tag->GetByRecordId($record->Id);
    $tags = array();
    $labels = $q->NumRows();
    for ($i = 0; $i < $labels; $i++) {
        $q->NextResult();
        $tag->FillFromResult($q);
        array_push($tags, $tag->Title);
    }

    $buttons = FillButtonObjects($record->Title, $descr, "", $record->GetImageUrl(), $journal->Title, $tags);

    $p = new Page("Комментарии к &laquo;".$record->Title."&raquo;", $meta_description, "Комментарии");
    $p->buttons = $buttons;
    $p->AddCss(array("forum.css", "jqueryui.css"));
    $p->PrintHeader();

    require_once $root."references.php";

    $postAccess = ($access >= Journal::FRIENDLY_ACCESS);

    $record->Content = RenderPostsLinks(ReplaceLinks($record->Content, $record->Id, $alias));
    echo $record->ToPrint($author);

    if (!$record->IsCommentable) {
        echo "<div class='ErrorHolder'><h2>Ошибка</h2>Комментарии к данному сообщению отключены.</div>";
    } else {
        echo GetButtonsMarkup($buttons);
    }

        ?>

    <style>
        h1 .char2 {
            margin-left: -2px;
        }
        h1 .char4 {
            margin-left: 1px;
        }
        h1 .char7 {
            margin-left: -1px;
        }
        h1 .char8 {
            margin-left: -2px;
        }
    </style>

    <h3>Вернуться</h3>
    <ul class="back_links random">
        <li> к сообщению <?php echo $record->ToLink(100, $alias) ?>
        <li> к журналу <?php echo $journal->GetLink($alias, 0, false) ?>
    </ul>

<?
    if (!$record->IsCommentable) {
        $p->PrintFooter();
        die();
    }
?>
    <h3 style='clear:both'>Комментарии:</h3><?php

    $answers = $record->AnswersCount - ($access == Journal::FULL_ACCESS ? 0 : $record->DeletedCount);

    $comment = new JournalComment();
    $q = $comment->GetByIndex(
        $record->ForumId,
        $access,
        $record->Index."_",
        $from * $messagesPerPage,
        $messagesPerPage);

    echo "<a name='c'></a>
<div class='NewThread'>
    <div>
        <a href='javascript:void(0)' class='replyLink' onclick='ForumReply(this,".$record->Id.",".$journal->Id.")'>Новый комментарий</a>
    </div>
</div>";

    echo "<ul class='thread'>";
    $comments = $q->NumRows();
    if ($comments > 0) {
        $min_level = 1000;
        $lines = array();
        for ($i = 0; $i < $comments; $i++) {
            $q->NextResult();

            $record = new JournalRecord();
            $record->FillFromResult($q);
            $avatar = $q->Get(Profile::AVATAR);
            $alias = $q->Get(JournalSettings::ALIAS);
            $lastMessageDate = $q->Get(JournalSettings::LAST_MESSAGE_DATE);

            if ($record->Level < $min_level) $min_level = $record->Level;
            array_push($lines, array("record" => $record, "avatar" => $avatar, "alias" => $alias, "lastMessage" => $lastMessageDate));
        }
        $q->Release();


        $level = 0;
        for ($i = 0; $i < sizeof($lines); $i++) {
            $l = $lines[$i];
            $record = $l["record"];
            $record->Level = $record->Level - $min_level;
            echo $record->ToExtendedString($level, $l["avatar"], ($l["lastMessageDate"] ? $l["alias"] : ""), $user, $yesterday);
            $level = $record->Level;
        }

        for ($i = 0; $i < $level + 1; $i++) {
            echo "</ul>";
        }

        $pager = new Pager($answers, $messagesPerPage, $from, $journal->BasePath().$alias."/post".round($record_id)."/comments/");
        echo $pager;
    } else {
        echo "</ul>";
    }

    include $root."inc/ui_parts/post_form.php";

    $p->PrintFooter();

?>
