<?php 

    $settings = new JournalSettings();
    $q = $settings->GetByCondition(
        "t5.".JournalTemplate::UPDATED." IS NOT NULL ORDER BY ".JournalTemplate::UPDATED." DESC LIMIT 20",
        $settings->GetUpdatedTemplatesExpression()
    );

    echo "<h2>Обновления дизайна</h2>";
    echo "<ul class='template_updates random'>";
    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();

        $settings->Alias = $q->Get(JournalSettings::ALIAS);
        $login = $q->Get(User::LOGIN);
        $updated = $q->Get(JournalTemplate::UPDATED);

        echo "\n<li>".$settings->ToLink($login).", <span>".PrintableShortDate($updated)."</span>";
    }
    echo "</ul>";
    $q->Release();

?>