<?php

    function get_sequence() {
        $result = "";
        for ($i = 0; $i < rand(2, 5); $i++) {
            $result .= rand(0, 9);
        }
        return $result;
    }
    
    function encrypt($num) {
        $result = get_sequence();
        foreach (str_split($num) as $c) {
            $result .= chr(65 + 1 * $c) . get_sequence();
        }
        return $result;
    }

    function decrypt($text) {
        $result = "";
        $text = preg_replace("/[^A-Z]/", "", $text);
        foreach (str_split($text) as $c) {
            $result .= ord($c) - 65;
        }
        return $result;
    }
    
    $root = "../";
    require_once $root."server_references.php";
    require $root."inc/ui_parts/templates.php";
    
    $p = new Page("Регистрация в чате", "", "", true);
    $p->AddCss("register.css");
    $p->PrintHeader();
    
    require_once $root."references.php";

    $login = "";
    $email = "";
    $full_name = "";
    $gender = "";
    $location = "";


    $operations = array("+", "-", "*");

    $errors = array();
#   $errors[] = "Регистрация временно закрыта";

    $doSave = LookInRequest("DO_SAVE");
    if ($doSave) {
        $login = trim(LookInRequest("LOGIN"));
        $password = LookInRequest("PASSWORD");
        $confirmPassword = LookInRequest("PASSWORD_CONFIRM");
        $email = trim(LookInRequest("E-MAIL"));
        $accept = trim(LookInRequest("ACCEPT"));
        $full_name = trim(LookInRequest("FULL_NAME"));
        $gender = trim(LookInRequest("GENDER"));
        $location = trim(LookInRequest("LOCATION"));
        $answer = trim(LookInRequest("ANSWER"));

        $is_human = !trim(LookInRequest("HUMAN"));
        $session = LookInRequest("SESSION");

        /* Validation */
        if (!$is_human || ($gender && !preg_match("/^[mf]$/", $gender))) {
            $errors[] = "Заблокирована автоматическая регистрация.";

            // Commented to prevent log drammatical growth
            // SaveLog("Заблокирована автоматическая регистрация пользователя ".$login, -1, "", AdminComment::SEVERITY_ERROR);
        }

        if (!$login) {
            $errors[] = "Не указан логин.";
        }
        if (!preg_match("/^[a-zA-Zа-яА-Я0-9_\.\=\-\ \[\]\{\}\*\+\@\#\%\&\(\)\?\~\:\;]+$/", $login)) {
            $errors[] = "Логин содержит недопустимые символы.";
        }
        if (preg_match("/[a-zA-Z]/", $login) && preg_match("/[а-яА-Я]/", $login)) {
            $errors[] = "Недопустимо смешение в имени русского и латинского алфавитов.";
        }
        if (!$password) {
            $errors[] = "Не указан пароль.";
        }
        if (!$confirmPassword) {
            $errors[] = "Не указано подтверждение пароля.";
        }
        if (!$email) {
            $errors[] = "Не указан e-mail.";
        }

        if (strlen($login) < 3 || strlen($login) > 20) {
            $errors[] = "Логин должен быть не короче 3 и не длиннее 20 символов.";
        }
        if ($password != $confirmPassword) {
            $errors[] = "Пароли не совпадают.";
        }

        if ($answer != decrypt($session)) {
            $errors[] = "Неверный ответ на математическую задачу.";
        }

        if (!$accept) {
            $errors[] = "Необходимо ознакомиться с правилами чата.";
        }

        if (!sizeof($errors)) {
            // Check if name is already taken

            $taken = "Имя &laquo;".$login."&raquo; уже занято.";
            $loginQuoted = SqlQuote($login);

            $q = $db->Query("SELECT 1 FROM ".User::table." WHERE ".User::LOGIN."='".$loginQuoted."' LIMIT 1");
            if ($q->NumRows()) {
                $errors[] = $taken;
            } else {
                $q = $db->Query("SELECT 1 FROM ".Nickname::table." WHERE ".Nickname::TITLE."='".$loginQuoted."' LIMIT 1");
                if ($q->NumRows()) {
                    $errors[] = $taken;
                } else {
                    $q = $db->Query("SELECT 1 FROM ".JournalSettings::table." WHERE ".JournalSettings::ALIAS."='".$loginQuoted."' OR ".JournalSettings::REQUESTED_ALIAS."='".$loginQuoted."' LIMIT 1");
                    if ($q->NumRows()) {
                        $errors[] = $taken;
                    }
                }
            }

            // Check if e-mail exists
            $q = $db->Query("SELECT 1 FROM ".Profile::table." WHERE ".Profile::EMAIL."='".SqlQuote($email)."' LIMIT 1");
            if ($q->NumRows()) {
                $errors[] = "E-mail &laquo;".$email."&raquo; уже был использован при регистрации.";
            }
        }
    }

    // Math task
    $a = rand(10, 100);
    $b = rand(10, 100);
    if ($b > $a) {
        $c = $b;
        $b = $a;
        $a = $c;
    }
    $operation = $operations[rand(0, 2)];
    $equation = $a." ".$operation." ".$b;
    $equation_result = encrypt(eval("return ".$equation.";"));

    // Success
    if (!sizeof($errors)) {
        if ($doSave) {
            // User
            $status = new Status();
            $status->GetNewbie();


            $guid = MakeGuid(10);
            $newborn = new User();
            $newborn->Login = $login;
            $newborn->Password = $password;
            if (!$status->IsEmpty()) {
                $newborn->StatusId = $status->Id;
            }
            $newborn->Guid = "_".$guid;
            $newborn->Save();

            // Profile
            $profile = new Profile();
            $profile->UserId = $newborn->Id;
            $profile->Email = $email;
            $profile->Name = $full_name;
            $profile->Gender = $gender;
            $profile->City = $location;
            $profile->Save();

            // Settings
            $settings = new Settings();
            $settings->UserId = $newborn->Id;
            $settings->Save();

            // Schedule oldbie status
            $task = new StatusScheduledTask($newborn->Id, DateFromTime(MakeTime(1 + date("Y"), date("m"), date("d"))));
            $task->Save();

            // Write registration Log
            LogRegistration($newborn->Id, $login);

            SendMail($email, "Регистрация в чате \"Безумное ЧАепиТие у Мартовского Зайца\"", "Здравствуйте.

Вы, или кто-то с указанием вашего e-mail адреса, прошли регистрацию в чате \"Безумное ЧАепиТие у Мартовского Зайца\" (http://www.bezumnoe.ru) под ником \"".$login."\".
Для активации вашего аккаунта пройдите по ссылке http://www.bezumnoe.ru/activate/?g=".$guid.".

Если же вы получили это письмо по ошибке - просто проигнорируйте это сообщение и запись в нашей базе данных будет удалена автоматически в недельный срок.

 С уважением,
 администрация чата.");

?>

<h2>Регистрация прошла успешно</h2>
Поздравляем с регистрацией в чате &laquo;Безумное ЧАепиТие&raquo;.
<p>На указанный вами при регистрации адрес были высланы инструкции по активации вашего аккаунта.
<p>Желаем приятного общения!

<br /><br /><br /><br /><br /><br /><br /><br />

<?php
            $p->PrintFooter();
            die;
        }
    }
?>

<h2>Условия регистрации</h2>
Для регистрации в чате необходимо выбрать логин, пароль достаточной сложности и
указать свой e-mail адрес для подтверждения регистрации.<br>
Более полную информацию можно будет указать из меню после входа в чат.<br>

<p>Поля, отмеченные значком <span class="Mandatory"></span>, обязательны для заполнения!

<div id="Summary" class="ErrorHolder"></div>

<form onsubmit="if (!PageValidators.AreValid()) return false;" target="" method="POST">

    <table>
        <tr>
            <th class="Mandatory">Логин</th>
            <td>
                <input name="LOGIN" id="LOGIN" type="text" maxlength="20" value="<?php echo $login ?>" placeholder="6 - 20 символов кириллицей или латинскими буквами">
                <input name="DO_SAVE" id="DO_SAVE" type="hidden" value="1">
            </td>
        </tr>
        <tr>
            <th class="Mandatory">Пароль</th>
            <td>
                <input type="password" name="PASSWORD" id="PASSWORD">
            </td>
        </tr>
        <tr>
            <th class="Mandatory">Подтверждение пароля</th>
            <td>
                <input type="password" name="PASSWORD_CONFIRM" id="PASSWORD_CONFIRM">
            </td>
        </tr>
        <tr>
            <th class="Mandatory">E-mail адрес</th>
            <td>
                <input name="E-MAIL" id="E-MAIL" type="text" value="<?php echo $email ?>" placeholder="Актуальный адрес для отправки инструкций по активации">
            </td>
        </tr>
        <tr>
            <th>Имя</th>
            <td>
                <input name="FULL_NAME" id="FULL_NAME" type="text" value="<?php echo $full_name ?>">
            </td>
        </tr>
        <tr>
            <th>Пол</th>
            <td>
                <input type="radio" name="GENDER" id="GENDER_MALE" value="m" <?php echo ($gender == "m" ? "checked" : ""); ?>> <label for="GENDER_MALE">мужской</label> 
                <input type="radio" name="GENDER" id="GENDER_FEMALE" value="f"<?php echo ($gender == "f" ? "checked" : ""); ?>> <label for="GENDER_FEMALE">женский</label> 
                <input type="radio" name="GENDER" id="GENDER_UNKNOWN" value="" <?php echo ($gender ? "" : "checked"); ?>> <label for="GENDER_UNKNOWN">другое</label> 
                <input name="HUMAN" id="HUMAN" style="visibility:hidden" value="">
                <input type="hidden" name="SESSION" id="SESSION" value="<?php echo $equation_result ?>" />
            </td>
        </tr>
        <tr>
            <th>Откуда вы</th>
            <td>
                <input name="LOCATION" id="LOCATION" type="text" value="<?php echo $location ?>">
            </td>
        </tr>
        <tr>
            <th>
                <br />
                <p><?php echo $equation ?> = 
            </th>
            <td>
                Я не бот и вообще против авторегистрации. Готов доказать, решив простое уравнение:
                <p><input name="ANSWER" id="ANSWER" size="4" width="20px" type="text" />
            </td>
        </tr>
        <tr>
            <td class="Mandatory"></td>
            <td>
                <input name="ACCEPT" id="ACCEPT" type="checkbox"> Я прочитал(а) <a href="/rules">правила чата</a> и обязуюсь их соблюдать.
                <p class="Tip">Несоблюдение правил поведения в чате может повлечь наказание, вплоть до удаления пользователя.
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="image" alt="Зарегистрироваться" src="/img/t/send.gif" width="80" height="67" style="margin-top:10px" />
            </td>
        </tr>
    </table>
</form>

<script>
    PageValidators.Add(new Validator("LOGIN", new RequiredField(), "Необходимо задать логин.", 1));
    PageValidators.Add(new Validator("LOGIN", new LengthRange(3, 20), "Логин должен быть не менее 6 и не более 20 символов.", 1));

    PageValidators.Add(new Validator("PASSWORD", new RequiredField(), "Необходимо задать пароль.", 1));

    PageValidators.Add(new Validator("PASSWORD_CONFIRM", new RequiredField(), "Необходимо подтвердить пароль.", 1));
    PageValidators.Add(new Validator("PASSWORD_CONFIRM", new EqualTo($("#PASSWORD")[0]), "Пароли не идентичны.", 1));

    PageValidators.Add(new Validator("E-MAIL", new RequiredField(), "Не указан e-mail.", 1));
    PageValidators.Add(new Validator("E-MAIL", new MatchPattern(emailPattern), "Неверный формат e-mail адреса.", 1));

    PageValidators.Add(new Validator("ACCEPT", new IsChecked(), "Необходимо ознакомиться с правилами чата.", 1));

    PageValidators.Init("Summary", "Выявлены ошибки:");
<?php
        
    if (sizeof($errors)) {
        echo "PageValidators.ShowSummary([\"".join("\", \"", $errors)."\"]);";
    }
?>
</script>
<?php

    $p->PrintFooter();

?>