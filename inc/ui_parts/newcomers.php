<?php

    $u = new User();
    $q = $u->GetByCondition("1=1 ORDER BY ".Profile::REGISTERED." DESC LIMIT 10", $u->WithProfileExpression());

    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();
        $u->FillFromResult($q);
        echo "\n<li> ".$u->ToInfoLink();
    }
    $q->Release();

?>