<?php
    
    $root = "../";
    require_once $root."server_references.php";
    require $root."inc/ui_parts/templates.php";
    require $root."inc/base_template.php";

    $p = new Page("&laquo;׸���� ������&raquo;");
    $p->AddCss("banned.css");
    $p->PrintHeader();

    require_once $root."references.php";

?>

�� ���� �������� ������� ��� ���������� ����, ���������� ��������� � ���� ����.

<h2>����������:</h2>
<?php include $root."inc/ui_parts/banned.php"; ?>

<?php

    $p->PrintFooter();
?>