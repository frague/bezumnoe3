<?

	$root = "../";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	Head("Регистрация в чате", "register.css");
	require_once $root."references.php";




	$errors = array();

	$doSave = LookInRequest("DO_SAVE");
	if ($doSave) {
		$login = trim(LookInRequest("LOGIN"));
		$password = LookInRequest("PASSWORD");
		$confirmPassword = LookInRequest("PASSWORD_CONFIRM");
		$email = trim(LookInRequest("E-MAIL"));
		$accept = trim(LookInRequest("ACCEPT"));

		/* Validation */

		if (!$login) {
			$errors[] = "Не указан логин.";
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

	if (!sizeof($errors)) {
		if ($doSave) {
			// User
			$status = new Status();
			$status->GetNewbie();

			$newborn = new User();
			$newborn->Login = $login;
			$newborn->Password = $password;
			if (!$status->IsEmpty()) {
				$newborn->StatusId = $status->Id;
			}
			$newborn->Guid = MakeGuid(10);
			$newborn->Save();

			// Profile
			$profile = new Profile();
			$profile->UserId = $newborn->Id;
			$profile->Email = $email;
			$profile->Save();

			// Settings
			$settings = new Settings();
			$settings->UserId = $newborn->Id;
			$settings->Save();

			// Journal
			$journal = new Journal();
			$journal->LinkedId = $newborn->Id;
			$journal->Save();

			// Journal template
			$skin = new JournalSkin();
			$template = new JournalTemplate($skin->GetDefaultTemplateId());
			$template->Retrieve();
			$template->Id = -1;
			$template->UserId = $newborn->Id;
			$template->Save();
				
			// Journal settings
			$journalSettings = new JournalSettings();
			$journalSettings->UserId = $newborn->Id;
			$journalSettings->Alias = $newborn->Guid;
			$journalSettings->Save();

			// Write registration Log
			LogRegistration($newborn->Id, $login);

?>

<h2>Регистрация прошла успешно</h2>
Поздравляем с регистрацией в чате &laquo;Безумное ЧАепиТие&raquo;.
<p>Теперь вы можете использовать указанне логин и пароль для <a href="/3/prototype">входа</a> в чат.

<?php
			Foot();
			die;
		}
	}
?>

<h2>Регистрация в чате</h2>
Для регистрации в чате необходимо выбрать логин, пароль достаточной сложности и
указать свой e-mail адрес для подтверждения регистрации.<br>
Более полную информацию можно будет указать из меню после входа в чат.<br>

Все поля обязательны для заполнения!

<?php
		
	 if (sizeof($errors)) {
?>

<div>В процессе регистрации обнаружены следующие ошибки:
<ul>
<?php

		for ($i = 0; $i < sizeof($errors); $i++) {
			echo "<li>".$errors[$i]."</li>";
		}

?>
</ul>
Пожалуйста, исправьте указанные неточности и повторите регистрацию.

<?php
		
	}

?>

<div id="Summary">
</div>

<form onsubmit="if (!PageValidators.AreValid()) return false;" target="" method="POST">

<table>
	<tr>
		<th>Логин:</th>
		<td>
			<input name="LOGIN" id="LOGIN" class="RegData" maxlength="20" value="<?php echo $login ?>">
			<input name="DO_SAVE" id="DO_SAVE" type="hidden" value="1">
		</td>
	</tr>
	<tr>
		<th>Пароль:</th>
		<td>
			<input type="password" name="PASSWORD" id="PASSWORD" class="RegData">
		</td>
	</tr>
	<tr>
		<th>Подтверждение пароля:</th>
		<td>
			<input type="password" name="PASSWORD_CONFIRM" id="PASSWORD_CONFIRM" class="RegData">
		</td>
	</tr>
	<tr>
		<th>E-mail адрес:</th>
		<td>
			<input name="E-MAIL" id="E-MAIL" class="RegData" value="<?php echo $email ?>">
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input name="ACCEPT" id="ACCEPT" type="checkbox"> Я прочитал(а) <a href="rules.php">правила чата</a> и обязуюсь их соблюдать
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="image" alt="Зарегистрироваться" src="/3/img/send_button_black.gif">
		</td>
	</tr>
</table>

</form>

<script>
	PageValidators.Add(new Validator($("LOGIN"), new RequiredField(), "Необходимо задать логин.", 1));
	PageValidators.Add(new Validator($("LOGIN"), new LengthRange(3, 20), "Логин должен быть не менее 6 и не более 20 символов.", 1));

	PageValidators.Add(new Validator($("PASSWORD"), new RequiredField(), "Необходимо задать пароль.", 1));

	PageValidators.Add(new Validator($("PASSWORD_CONFIRM"), new RequiredField(), "Необходимо подтвердить пароль.", 1));
	PageValidators.Add(new Validator($("PASSWORD_CONFIRM"), new EqualTo($("PASSWORD")), "Пароли не идентичны.", 1));

	PageValidators.Add(new Validator($("E-MAIL"), new RequiredField(), "Не указан e-mail.", 1));
	PageValidators.Add(new Validator($("E-MAIL"), new MatchPattern(emailPattern), "Неверный формат e-mail адреса.", 1));

	PageValidators.Add(new Validator($("ACCEPT"), new IsChecked(), "Необходимо ознакомиться с правилами чата.", 1));

	PageValidators.Init($("Summary"), "Выявлены ошибки:");
</script>
<?php
	Foot();
?>