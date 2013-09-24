<?php

	$record = new JournalRecord();
	$q = $record->GetMixedJournalsTopics($user->User->Id, 0, 20, "", False);

	echo "<ul>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$record->FillFromResult($q);
		$alias = $q->Get(JournalSettings::ALIAS);

		echo MakeListItem()." &laquo;<b>".$record->ToLink(0, $alias)."</b>&raquo;, ".JournalSettings::MakeLink($alias, $record->Author);
	}
	echo "</ul>";
	$q->Release();

?>