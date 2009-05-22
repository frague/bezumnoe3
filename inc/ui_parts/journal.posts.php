<?php

	$record = new JournalRecord();
	$q = $record->GetJournalTopics($user, 0, 20);

	echo "<h4>Новые сообщения:</h4>";
	echo "<ul>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$record->FillFromResult($q);

		echo "<li> &laquo;".$record->ToLink()."&raquo;, ".$record->Author;
	}
	echo "</ul>";

?>