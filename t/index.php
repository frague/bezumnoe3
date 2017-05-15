<?php

    $root = "../";
    require_once $root."server_references.php";
    require $root."inc/ui_parts/templates.php";
    require $root."inc/base_template.php";

    $pg = new Page("Привязка к telegram");

    require_once $root."references.php";

    $user = GetAuthorizedUser(true);
    $uuid = trim(LookInRequest("uuid"));
    $error = "";

    if ($user->IsEmpty()) {
        $error = "Пользователь не авторизован!";
    }

    if ($uuid) {
        $telegram = new TelegramId();
        $telegram->FillByCondition(TelegramId::UUID."='".SqlQuote($uuid)."'");
        if (!$telegram->TelegramUserId) {
            $error = "Запись не найдена!";
        }
    } else {
        $error = "Не указан код для связи аккаунтов!";
    }

    $profile = new Profile();
    $profile->GetByUserId($user->Id);

    if ($profile->IsEmpty()) {
        $error = "Не удалось получить профиль пользователя!";
    }

    if ($error) {
        ErrorPage("Ошибка привязки telegram аккаунта.", $error);
        die;
    }

    $profile->TelegramId = $telegram->TelegramUserId;
    $profile->Save();

    $pg->PrintHeader();
?>

<h1>Поздравляем, <?php print $telegram->TelegramUsername ?>!</h1>
<p>Ваш аккаунт успешно привязан к аккаунту <?php print $user->User->Login ?>!
<div class="Spacer"></div>

<?php

    SaveLog("Привязка к telegram <b>".$telegram->TelegramUsername."</b> (".$telegram->TelegramUserId.").", $user->Id, "", AdminComment::SEVERITY_WARNING);
    
    $telegram->Delete();

    $pg->PrintFooter();
?>
