<?

	$root = "./";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	Head("����������� � ����", "register.css");
	require_once $root."references.php";




	$errors = array();

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

		$is_human = !trim(LookInRequest("HUMAN"));

		/* Validation */

		if (!$is_human || ($gender && !preg_match("/^[mf]$/", $gender))) {
			$errors[] = "������������� �������������� �����������.";

			// Commented to prevent log drammatical growth
			// SaveLog("������������� �������������� ����������� ������������ ".$login, -1, "", AdminComment::SEVERITY_ERROR);
		}

		if (!$login) {
			$errors[] = "�� ������ �����.";
		}
		if (!$password) {
			$errors[] = "�� ������ ������.";
		}
		if (!$confirmPassword) {
			$errors[] = "�� ������� ������������� ������.";
		}
		if (!$email) {
			$errors[] = "�� ������ e-mail.";
		}

		if (strlen($login) < 3 || strlen($login) > 20) {
			$errors[] = "����� ������ ���� �� ������ 3 � �� ������� 20 ��������.";
		}
		if ($password != $confirmPassword) {
			$errors[] = "������ �� ���������.";
		}

		if (!$accept) {
			$errors[] = "���������� ������������ � ��������� ����.";
		}

		if (!sizeof($errors)) {
			// Check if name is already taken

			$taken = "��� &laquo;".$login."&raquo; ��� ������.";
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
				$errors[] = "E-mail &laquo;".$email."&raquo; ��� ��� ����������� ��� �����������.";
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

			// Journal
/*			$journal = new Journal();
			$journal->LinkedId = $newborn->Id;
			$journal->Title = $newborn->Login;
			$journal->Description = "������������ ������";
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
			$journalSettings->Save();*/

			// Write registration Log
			LogRegistration($newborn->Id, $login);

?>

<h2>����������� ������ �������</h2>
����������� � ������������ � ���� &laquo;�������� ��������&raquo;.
<p>������ �� ������ ������������ �������� ����� � ������ ��� <a href="/">�����</a> � ���.

<?php
			Foot();
			die;
		}
	}
?>

<h2>����������� � ����</h2>
��� ����������� � ���� ���������� ������� �����, ������ ����������� ��������� �
������� ���� e-mail ����� ��� ������������� �����������.<br>
����� ������ ���������� ����� ����� ������� �� ���� ����� ����� � ���.<br>

����, ���������� ������� <span class="Mandatory"></span>, ����������� ��� ����������!

<?php
		
	 if (sizeof($errors)) {
?>

<div>� �������� ����������� ���������� ��������� ������:
<ul>
<?php

		for ($i = 0; $i < sizeof($errors); $i++) {
			echo "<li>".$errors[$i]."</li>";
		}

?>
</ul>
����������, ��������� ��������� ���������� � ��������� �����������.

<?php
		
	}

?>

<div id="Summary">
</div>

<form onsubmit="if (!PageValidators.AreValid()) return false;" target="" method="POST">

<table width="auto">
	<tr>
		<th class="Mandatory">�����:</th>
		<td>
			<input name="LOGIN" id="LOGIN" class="RegData" maxlength="20" value="<?php echo $login ?>">
			<input name="DO_SAVE" id="DO_SAVE" type="hidden" value="1">
			<p class="Tip">6 - 20 �������� ���������� ��� ���������� �������.
		</td>
	</tr>
	<tr>
		<th class="Mandatory">������:</th>
		<td>
			<input type="password" name="PASSWORD" id="PASSWORD" class="RegData">
		</td>
	</tr>
	<tr>
		<th class="Mandatory">������������� ������:</th>
		<td>
			<input type="password" name="PASSWORD_CONFIRM" id="PASSWORD_CONFIRM" class="RegData">
		</td>
	</tr>
	<tr>
		<th class="Mandatory">E-mail �����:</th>
		<td>
			<input name="E-MAIL" id="E-MAIL" class="RegData" value="<?php echo $email ?>">
			<p class="Tip">������� ���������� �����, �.�. �� ���� ����� ������� ���������� �� ��������� ��������.
		</td>
	</tr>
	<tr>
		<th>���:</th>
		<td>
			<input name="FULL_NAME" id="FULL_NAME" class="RegData" value="<?php echo $full_name ?>">
		</td>
	</tr>
	<tr>
		<th>���:</th>
		<td>
			<input type="radio" name="GENDER" id="GENDER_MALE" value="m" <?php echo ($gender == "m" ? "checked" : ""); ?>> <label for="GENDER_MALE">�������</label> 
			<input type="radio" name="GENDER" id="GENDER_FEMALE" value="f"<?php echo ($gender == "f" ? "checked" : ""); ?>> <label for="GENDER_FEMALE">�������</label> 
			<input type="radio" name="GENDER" id="GENDER_UNKNOWN" value="" <?php echo ($gender ? "" : "checked"); ?>> <label for="GENDER_UNKNOWN">������</label> 
			<input name="HUMAN" id="HUMAN" style="visibility:hidden" value="">
		</td>
	</tr>
	<tr>
		<th>�����:</th>
		<td>
			<input name="LOCATION" id="LOCATION" class="RegData" value="<?php echo $location ?>">
		</td>
	</tr>
	<tr>
		<td class="Mandatory"></td>
		<td>
			<input name="ACCEPT" id="ACCEPT" type="checkbox"> � ��������(�) <a href="rules.php">������� ����</a> � �������� �� ���������.
			<p class="Tip">������������ ������ ��������� � ���� ����� ������� ���������, ������ �� �������� ������������.
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="image" alt="������������������" src="/img/send_button_black.gif">
		</td>
	</tr>
</table>

</form>

<script>
	PageValidators.Add(new Validator($("LOGIN"), new RequiredField(), "���������� ������ �����.", 1));
	PageValidators.Add(new Validator($("LOGIN"), new LengthRange(3, 20), "����� ������ ���� �� ����� 6 � �� ����� 20 ��������.", 1));

	PageValidators.Add(new Validator($("PASSWORD"), new RequiredField(), "���������� ������ ������.", 1));

	PageValidators.Add(new Validator($("PASSWORD_CONFIRM"), new RequiredField(), "���������� ����������� ������.", 1));
	PageValidators.Add(new Validator($("PASSWORD_CONFIRM"), new EqualTo($("PASSWORD")), "������ �� ���������.", 1));

	PageValidators.Add(new Validator($("E-MAIL"), new RequiredField(), "�� ������ e-mail.", 1));
	PageValidators.Add(new Validator($("E-MAIL"), new MatchPattern(emailPattern), "�������� ������ e-mail ������.", 1));

	PageValidators.Add(new Validator($("ACCEPT"), new IsChecked(), "���������� ������������ � ��������� ����.", 1));

	PageValidators.Init($("Summary"), "�������� ������:");
</script>
<?php
	Foot();
?>