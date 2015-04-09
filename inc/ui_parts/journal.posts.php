<?php

    $record = new JournalRecord();
    $q = $record->GetMixedJournalsTopics($user->User->Id, 0, 20, "", False);

    echo "<ul class='new_posts random'>";
    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();
        $record->FillFromResult($q);
        $alias = $q->Get(JournalSettings::ALIAS);

        echo "\n<li>".$record->ToLink(0, $alias).", ".JournalSettings::MakeLink($alias, $record->Author);
    }
    echo "</ul>";
    $q->Release();

?>
