<?php

    echo "<h2>Комментарии в журналах</h2>";

    $userId = $someoneIsLogged ? $user->User->Id : -1;
    $comment = new JournalComment();
    $q = $comment->GetMixedJournalsComments($userId, 0, 50);

    $lastIndex = "";
    $condition = "";
    $sorted = array();

    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();

        $comment = new JournalComment();
        $comment->FillFromResult($q);

        $r = substr($comment->Index, 0, 4)."_".$comment->ForumId;

        if (!isset($sorted[$r])) {
            $sorted[$r] = array();
        }
        array_push($sorted[$r], $comment);

        if ($lastIndex != $r) {
            $lastIndex = $r;
            $condition .= ($condition ? " OR " : "")."(t1.".JournalRecord::INDEX."='".substr($comment->Index, 0, 4)."' AND t1.".JournalRecord::FORUM_ID."=".$comment->ForumId.")";
        }
    }
    $q->Release();

    if ($condition) {
        $condition = "(".$condition.")";
    } else {
        $condition = "1=1";
    }

    $topic = new JournalRecord();
    $q = $topic->GetMixedJournalsRecords($userId, 0, 50, $condition);
    $topics = array();
    $aliases = array();

    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();

        $topic = new JournalRecord();
        $topic->FillFromResult($q);
        if (!$topic->IsCommentable) continue;

        $index = $topic->Index."_".$topic->ForumId;
        $topics[$index] = $topic;
        $aliases[$index] = $q->Get(JournalSettings::ALIAS);
    }
    $q->Release();

    echo "<ul class='new_replies'>";
    while (list($record, $comments) = each($sorted)) {
        $rec = $topics[$record];
        $alias = $aliases[$record];

        if (!$rec || $rec->IsEmpty()) {
            continue;
        }

        echo "<li>".$rec->ToLink(255, $alias).", ".JournalSettings::MakeLink($alias, $rec->Author);

        echo "<ul class='random'>";
        while (list($k, $comment) = each($comments)) {
            echo "<li>".$comment->ToLink(100, $alias, $rec->Id).", ".$comment->Author;
        }
        echo "</ul></li>";
    }
    echo "</ul>";

?>
