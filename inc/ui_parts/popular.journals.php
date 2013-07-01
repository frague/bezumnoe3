<?php

	$j = new Journal();
	$u = new User();

	$q = $j->GetByCondition(
		"1=1",
		$j->RatingExpression(" ORDER BY ".ForumBase::RATING."-".ForumBase::LAST_RATING." DESC LIMIT 20"));

	echo "<h4>Популярные за сутки</h4>";
	echo "<ul>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$j->FillFromResult($q);
		$u->FillFromResult($q);
		$alias = $q->Get(JournalSettings::ALIAS);

		if (!$j->IsHidden) {
			echo MakeListItem($i < 10 ? "Leading" : "").$j->GetRatingDelta()." &laquo;<b>".JournalSettings::MakeLink($alias, $j->Title)."</b>&raquo;, ".$u->ToInfoLink();
		}
	}
	echo "</ul>";
	$q->Release();

?>