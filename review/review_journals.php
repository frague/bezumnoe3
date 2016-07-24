<?php
    
    $root = "../";
    require_once $root."server_references.php";
    require_once $root."references.php";

?>
<style>
.Disabled a {
    text-decoration:line-through;
}

a.action {
    font-weight: 700;
    color:red;
    font-family: tahoma;
}
</style>

<?php

    $j = new Journal();
    $u = new User();

    $q = $j->GetByCondition(
        "1=1",
        $j->RatingExpression());

    echo "<ul>";
    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();
        $j->FillFromResult($q);
        $u->FillFromResult($q);
        $alias = $q->Get(JournalSettings::ALIAS);
        $own_markup_allowed = $q->Get(JournalSettings::OWN_MARKUP_ALLOWED);
        $skin_template_id = $q->Get(JournalSettings::SKIN_TEMPLATE_ID);

        if (!$j->IsHidden && !$skin_template_id) {
            echo "<li class=\"".($own_markup_allowed ? "" : "disabled")."\"> <a class=\"action\" target=\"action\" href=\"review_action.php?jid=".$j->Id."\">x</a>&nbsp;".JournalSettings::MakeLink($alias, "&laquo;".$j->Title."&raquo;", "", "journal");
        }
    }
    echo "</ul>";
    $q->Release();



?>