<?php 

	$settings = new JournalSettings();
	$q = $settings->GetByCondition(
		"t5.".JournalTemplate::UPDATED." IS NOT NULL ORDER BY ".JournalTemplate::UPDATED." DESC LIMIT 20",
		$settings->GetUpdatedTemplatesExpression()
	);

	echo "<h3>Обновления дизайна</h3>";
	echo "<ul>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();

		$settings->Alias = $q->Get(JournalSettings::ALIAS);
		$login = $q->Get(User::LOGIN);
		$updated = $q->Get(JournalTemplate::UPDATED);

		echo "<li>".$settings->ToLink($login)." (".PrintableShortDate($updated).")";
	}
	echo "</ul>";
	$q->Release();

?>