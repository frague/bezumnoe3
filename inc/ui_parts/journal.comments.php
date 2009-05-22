<?php

	echo "<h4>Комментарии в журналах:</h4>";

	$comment = new JournalComment();
	$q = $comment->GetJournalComments($user);

	$lastIndex = "";
	$condition = "";
	$sorted = array();

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();

		$comment = new JournalComment();
		$comment->FillFromResult($q);

		$r = substr($comment->Index, 0, 4)."_".$comment->ForumId;

		if (!$sorted[$r]) {
			$sorted[$r] = array();
		}
		array_push($sorted[$r], $comment);

		if ($lastIndex != $r) {
			$lastIndex = $r;
			$condition .= ($condition ? " OR " : "")."(t1.".JournalRecord::INDEX."='".substr($comment->Index, 0, 4)."' AND t1.".JournalRecord::FORUM_ID."=".$comment->ForumId.")";
		}
	}
	if ($condition) {
		$condition = "($condition)";
	} else {
		$condition = "1=1";
	}

	$topic = new JournalRecord();
	$q = $topic->GetJournalRecords($user, 0, 20, $condition);
	$topics = array();

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();

		$topic = new JournalRecord();
		$topic->FillFromResult($q);

		$topics[$topic->Index."_".$topic->ForumId] = $topic;
	}


	echo "<ul>";
	while (list($record, $comments) = each($sorted)) {
		$rec = $topics[$record];

		if ($rec && !$rec->IsEmpty()) {
			echo "&laquo;".$rec->ToLink(255)."&raquo;, ".$rec->Author;
		}

		echo "<ul class='Comments'>";
		while (list($k, $comment) = each($comments)) {
			echo "<li> &laquo;".$comment->ToLink(100, $rec->Id)."&raquo;, ".$comment->Author;
		}
		echo "</ul>";
	}
	echo "</ul>";

?>