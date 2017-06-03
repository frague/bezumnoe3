<?php
    global $someoneIsLogged;

    $userId = $someoneIsLogged ? $user->User->Id : -1;
    $comment = new GalleryComment();
    $q = $comment->GetMixedJournalsComments($userId, 0, $shownComments ? $shownComments : 5);

    $lastIndex = "";
    $condition = "";
    $sorted = array();

    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();

        $comment = new GalleryComment();
        $comment->FillFromResult($q);

        $r = substr($comment->Index, 0, 4)."_".$comment->ForumId;

        if (!isset($sorted[$r])) {
            $sorted[$r] = array();
        }
        array_push($sorted[$r], $comment);

        if ($lastIndex != $r) {
            $lastIndex = $r;
            $condition .= ($condition ? " OR " : "")."(t1.".GalleryPhoto::INDEX."='".substr($comment->Index, 0, 4)."' AND t1.".GalleryPhoto::FORUM_ID."=".$comment->ForumId.")";
        }
    }
    $q->Release();

    if ($condition) {
        $condition = "(".$condition.")";
    } else {
        $condition = "1=1";
    }

    $topic = new GalleryPhoto();
    $q = $topic->GetMixedJournalsRecords($userId, 0, 20, $condition);
    $topics = array();
    $paths = array();

    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();

        $top = new GalleryPhoto();
        $top->FillFromResult($q);

        $index = $top->Index."_".$top->ForumId;
        $topics[$index] = $top;

        $descr = $q->Get(Gallery::DESCRIPTION);
        $paths[$top->ForumId] = $descr;
    }
    $q->Release();

    echo "<ul class='new_comments'>";
    while (list($record, $comments) = each($sorted)) {
        $rec = $topics[$record];

        if ($rec && !$rec->IsEmpty()) {
        echo "<li>";
            echo $rec->ToPreview($paths[$rec->ForumId]);

            echo "<ul class='random'>";
            while (list($k, $comment) = each($comments)) {
                echo "\n<li> ".$comment->ToLink(100, $rec->Id).", ".$comment->Author;
            }
            echo "</ul></li>";
        }
    }
    echo "</ul>";

?>
