<?php
    
    $root = "../";
    require_once $root."server_references.php";
    require $root."inc/ui_parts/templates.php";
    require $root."inc/base_template.php";

    $p = new Page("&laquo;Чёрный список&raquo;");
    $p->AddCss("banned.css");
    $p->PrintHeader();

    require_once $root."references.php";

?>

На этой странице собраны все нарушители чата, отбывающие наказание в виде бана.

<h2>Нарушители:</h2>
<?php include $root."inc/ui_parts/banned.php"; ?>

<?php

    $p->PrintFooter();
?>