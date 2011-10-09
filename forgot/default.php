<?

	$root = "../";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	$no_jquery = 1;
	Head("Изменение забытого пароля", "register.css", "stub.js");
	require_once $root."references.php";




	$errors = array();

	$doSave = LookInRequest("DO_SAVE");
	if ($doSave) {
		$login = trim(LookInRequest("LOGIN"));
		$email = trim(LookInRequest("E-MAIL"));

		$is_human = !trim(LookInRequest("HUMAN"));

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

		if (strlen($login) < 3 || strlen($login) > 20) {
			$errors[] = "Логин должен быть не короче 3 и не длиннее 20 символов.";
		}
		if (!$email) {
			$errors[] = "Не указан e-mail.";
		}

		if (!sizeof($errors)) {
			// Check if name exists

			$taken = "Имя &laquo;".$login."&raquo; уже занято.";
			$loginQuoted = SqlQuote($login);

			$u = new User();
			$u->FillByCondition(User::LOGIN."='".$loginQuoted."' LIMIT 1");
			if ($u->IsEmpty()) {
				$errors[] = "Пользователь с логином &laquo;".$login."&raquo; в чате не зарегистрирован.";
			} else {
				// Check if e-mails are equal
				$p = new Profile();
				$p->GetByUserId($u->Id);
				if ($p->IsEmpty() || $p->Email != $email) {
					$errors[] = "Неверно указан email адрес.";
				} else {
					if ($doSave) {
					// User
					$u->SaveLoginGuid(true);

					// Write registration Log
					LogRegistration($newborn->Id, $login);

					SendMail($email, "Восстановление пароля в чате \"Безумное ЧАепиТие у Мартовского Зайца\"", "Здравствуйте.

Вы, или кто-то с указанием вашего e-mail адреса, запросили временный доступ к чату \"Безумное ЧАепиТие у Мартовского Зайца\" (http://www.bezumnoe.ru) под ником \"".$login."\" для изменения пароля.
Для авторизации в чате пройдите по ссылке http://www.bezumnoe.ru/?f=".$u->LoginGuid.". После входа в чат не забудьте сменить пароль.

Если же вы получили это письмо по ошибке - просто проигнорируйте это сообщение.

 С уважением,
 администрация чата.");
				}
			}
		}
	}

	if (!sizeof($errors)) {
?>

<h2>Инструкции отправлены</h2>
Инструкции по восстановлению пароля были отправлены на указанный email адрес.
Не забудьте сменить пароль после входа в чат!

<br /><br /><br /><br /><br /><br /><br /><br />

<?php
			Foot();
			die;
		}
	}
?>

Если вы забыли свой пароль в чате, введите в форму внизу ваш логин и email, указанный при регистрации и 
ожидайте дальнейших инструкций в письме.<br>

<p>Поля, отмеченные значком <span class="Mandatory"></span>, обязательны для заполнения!

<div id="Summary" class="ErrorHolder">
</div>

<form onsubmit="if (!PageValidators.AreValid()) return false;" target="" method="POST">

<table width="auto">
	<tr>
		<th class="Mandatory">Логин:</th>
		<td>
			<input name="LOGIN" id="LOGIN" class="RegData" maxlength="20" value="<?php echo $login ?>">
			<input name="DO_SAVE" id="DO_SAVE" type="hidden" value="1">
			<p class="Tip">6 - 20 символов кириллицей или латинскими буквами.
		</td>
	</tr>
	<tr>
		<th class="Mandatory">E-mail адрес:</th>
		<td>
			<input name="E-MAIL" id="E-MAIL" class="RegData" value="<?php echo $email ?>">
			<p class="Tip">Укажите актуальный адрес, т.к. на него будут высланы инструкции по активации аккаунта.
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="image" alt="Сбросить пароль" src="/img/t/send.gif" width="80" height="67" style="margin-top:10px" />
		</td>
	</tr>
</table>

</form>

<script>
	PageValidators.Add(new Validator($("LOGIN"), new RequiredField(), "Необходимо задать логин.", 1));
	PageValidators.Add(new Validator($("LOGIN"), new LengthRange(3, 20), "Логин должен быть не менее 6 и не более 20 символов.", 1));

	PageValidators.Add(new Validator($("E-MAIL"), new RequiredField(), "Не указан e-mail.", 1));
	PageValidators.Add(new Validator($("E-MAIL"), new MatchPattern(emailPattern), "Неверный формат e-mail адреса.", 1));

	PageValidators.Init($("Summary"), "Выявлены ошибки:");
<?php
		
	if (sizeof($errors)) {
 		echo "PageValidators.ShowSummary([\"".join("\", \"", $errors)."\"]);";
	}
?>
</script>
<?php

	Foot();

?>