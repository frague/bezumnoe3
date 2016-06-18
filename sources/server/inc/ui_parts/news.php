<?php

    function ShowNews($ownerId, $amount = 5) {
      global $db;

        $ownerId = round($ownerId);
        $record = new NewsRecord();

        $q = $record->GetByOwnerId($ownerId, 0, $amount, "t1.".NewsRecord::IS_HIDDEN."=0");

        echo "<ul>";
        for ($i = 0; $i < $q->NumRows(); $i++) {
            $q->NextResult();
            $record->FillFromResult($q);
            echo $record->ToPrint();
        }
        echo "</ul>";
        $q->Release();
    }

?>
