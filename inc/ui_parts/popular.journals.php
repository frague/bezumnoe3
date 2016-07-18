<?php

    $j = new Journal();
    $u = new User();

    $q = $j->GetByCondition(
        "1=1",
        $j->RatingExpression(" ORDER BY ".ForumBase::RATING."-".ForumBase::LAST_RATING." DESC LIMIT 20"));

    echo "<h2>Популярные за сутки</h2>";
    echo "<ul class='popular'>";
    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();
        $j->FillFromResult($q);
        $u->FillFromResult($q);
        $alias = $q->Get(JournalSettings::ALIAS);

        if (!$j->IsHidden) {
            echo "\n<li> <span>".$j->GetRatingDelta()."</span> ".JournalSettings::MakeLink($alias, $j->Title).", ".$u->ToInfoLink();
        }
    }
    echo "</ul>";
    $q->Release();

?>