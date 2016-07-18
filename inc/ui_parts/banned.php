<?php

    $u = new User();
    $q = $u->GetByCondition(
        "t1.".User::BANNED_BY." IS NOT NULL AND t1.".User::IS_DELETED."=0  ORDER BY ".User::LOGIN,
        $u->BannedExpression()
    );

    echo "<ul class='random'>";
    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();
        $u->FillFromResult($q);
        $admin = $q->Get("ADMIN");
        echo "\n<li> ".$u->ToInfoLink()."<ul>".$u->BannedInfo($admin)."</ul>";
    }
    echo "</ul>";
    $q->Release();

?>