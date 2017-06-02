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

    if ($uuid) {
        $telegram = new TelegramId();
        $telegram->FillByCondition(TelegramId::UUID."='".SqlQuote($uuid)."'");
        if (!$telegram->TelegramUserId) {
            DieWith404();
        } elseif ($user->IsEmpty()) {
            $pg->PrintHeader();

            ?>

<p>Привязка telegram-аккаунта к аккаунту в чате позволяет:
<ol>
    <li>Отображать в чате ваши сообщения, отправленные в telegram от вашего аккаунта в чате с учётом настроек шрифта
    <li>Эмулировать ваше присутствие в чате при публикации сообщений в telegram
    <li>Из telegram давать боту команды для выполнения различных действий в чате (смена темы, кик, бан) в зависимости от уровня доступа (пока не реализовано)
</ol>
<p>Для продолжения необходимо авторизоваться в чате:</p>
<form method="POST" class="telegram-auth">
    <input type="hidden" name="AUTH" id="AUTH" value="1" />
    <label for="<?php echo LOGIN_KEY ?>">Логин</label>
    <input name="<?php echo LOGIN_KEY ?>" id="<?php echo LOGIN_KEY ?>" type="text" placeholder="Логин" />, 
    <label for="<?php echo PASSWORD_KEY ?>">пароль</label>
    <input name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" value="" size="10" type="password" placeholder="Пароль" />
    <button type="submit">Авторизоваться</button>
</form>
<div class="Spacer"></div>

        <?php

            $pg->PrintFooter();
            die;

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
