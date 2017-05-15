<?php

    $root = "../";
    require_once $root."server_references.php";
    require $root."inc/ui_parts/templates.php";
    require $root."inc/base_template.php";

    $pg = new Page("Привязка к telegram");
    $pg->PrintHeader();

    require_once $root."references.php";

    $user = GetAuthorizedUser(true);
    $uuid = $_GET["uuid"];

    if ($uuid && !$user->IsEmpty()) {
        $telegram = new TelegramId();
        $telegram->FillByCondition(TelegramId::UUID."='".SqlQuote($uuid)."'");
        if ($telegram->TelegramUserId) {
            print "Запись не найдена!";
        } else {
            print $telegram;
        }
    }
?>


<?php

    $pg->PrintFooter();
?>
