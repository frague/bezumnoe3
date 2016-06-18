<?php
    header("Content-type: text/javascript");
 ?>

InitSmiles(<?php

    $smiles = array();
    $dir = "../img/smiles/";

    $d = @dir($dir) or die("");
    while (false !== ($entry = $d->read())) {
        if ($entry[0] == ".") {
            continue;
        }

        if (!is_dir($dir.$entry) && is_readable($dir.$entry)) {
            $smiles[] = $entry;
        }
    }
    $d->close();
    sort($smiles);

    echo "[\"".join("\", \"", $smiles)."\"]";

?>);

