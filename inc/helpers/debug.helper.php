<?php

    $timeStart = microtime(true);

    function MeasureAtThisPoint() {
       global $timeStart;

        $timeEnd = microtime(true);
        $s = $timeEnd - $timeStart;
        $timeStart = $timeEnd;

        return $s;
    }

    function JsPoint($label) {
        echo "/*    ".$label."  ".str_replace(".", ",", sprintf("%01.4f", MeasureAtThisPoint()))."  */\n";
    }

?>