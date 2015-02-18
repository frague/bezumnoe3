<?php
    
    $root = "../";
    require_once $root."server_references.php";
    require $root."inc/ui_parts/templates.php";
    require $root."inc/base_template.php";

    $pg = new Page("�������");
    $pg->AddCss("rating.css");
    $pg->PrintHeader();

    require_once $root."references.php";

    function GetUsersRatings($condition) {
        $p = new Profile();
        $u = new User();

        $q = $p->GetByCondition(
            $condition,
            $p->RatingExpression());

        for ($i = 0; $i < $q->NumRows(); $i++) {
            $q->NextResult();
            $p->FillFromResult($q);
            $u->FillFromResult($q);

            $delta = $p->GetRatingDelta();
            echo "\n<li> <span><b>".$p->Rating."</b> <sup class=\"".($delta > 0 ? "Positive" : "Negative")."\">".$delta."</sup></span> ".$u->ToInfoLink();
        }
    }

?>

<p>������� - ���������� ������������ ������� ������������ ������������ �����. 
���� ��������� ������� �� ���� �� ����, ������� ����������� �� -10.
���������� �������� ������������ ��� � �����.</p>

<table class='rating'>
    <tr>
        <td>
            <ul>
                <h2>������ �� �����</h2>
                <?php GetUsersRatings("1=1 ORDER BY ".Profile::RATING."-".Profile::LAST_RATING." DESC LIMIT 40"); ?>
            </ul>
        </td>
        <td>
            <ul>
                <h2>��� 40</h2>
                <?php GetUsersRatings("1=1 ORDER BY ".Profile::RATING." DESC LIMIT 40"); ?>
            </ul>
        </td>
    </tr>
</table>

<?php

    $pg->PrintFooter();
?>