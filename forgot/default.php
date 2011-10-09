<?

	$root = "../";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	$no_jquery = 1;
	Head("��������� �������� ������", "register.css", "stub.js");
	require_once $root."references.php";




	$errors = array();

	$doSave = LookInRequest("DO_SAVE");
	if ($doSave) {
		$login = trim(LookInRequest("LOGIN"));
		$email = trim(LookInRequest("E-MAIL"));

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
		if (!preg_match("/^[a-zA-Z�-��-�0-9_\.\=\-\ \[\]\{\}\*\+\@\#\%\&\(\)\?\~\:\;]+$/", $login)) {
			$errors[] = "����� �������� ������������ �������.";
		}
		if (preg_match("/[a-zA-Z]/", $login) && preg_match("/[�-��-�]/", $login)) {
			$errors[] = "����������� �������� � ����� �������� � ���������� ���������.";
		}

		if (strlen($login) < 3 || strlen($login) > 20) {
			$errors[] = "����� ������ ���� �� ������ 3 � �� ������� 20 ��������.";
		}
		if (!$email) {
			$errors[] = "�� ������ e-mail.";
		}

		if (!sizeof($errors)) {
			// Check if name exists

			$taken = "��� &laquo;".$login."&raquo; ��� ������.";
			$loginQuoted = SqlQuote($login);

			$u = new User();
			$u->FillByCondition(User::LOGIN."='".$loginQuoted."' LIMIT 1");
			if ($u->IsEmpty()) {
				$errors[] = "������������ � ������� &laquo;".$login."&raquo; � ���� �� ���������������.";
			} else {
				// Check if e-mails are equal
				$p = new Profile();
				$p->GetByUserId($u->Id);
				if ($p->IsEmpty() || $p->Email != $email) {
					$errors[] = "������� ������ email �����.";
				} else {
					if ($doSave) {
					// User
					$u->SaveLoginGuid(true);

					// Write registration Log
					LogRegistration($newborn->Id, $login);

					SendMail($email, "�������������� ������ � ���� \"�������� �������� � ����������� �����\"", "������������.

��, ��� ���-�� � ��������� ������ e-mail ������, ��������� ��������� ������ � ���� \"�������� �������� � ����������� �����\" (http://www.bezumnoe.ru) ��� ����� \"".$login."\" ��� ��������� ������.
��� ����������� � ���� �������� �� ������ http://www.bezumnoe.ru/?f=".$u->LoginGuid.". ����� ����� � ��� �� �������� ������� ������.

���� �� �� �������� ��� ������ �� ������ - ������ �������������� ��� ���������.

 � ���������,
 ������������� ����.");
				}
			}
		}
	}

	if (!sizeof($errors)) {
?>

<h2>���������� ����������</h2>
���������� �� �������������� ������ ���� ���������� �� ��������� email �����.
�� �������� ������� ������ ����� ����� � ���!

<br /><br /><br /><br /><br /><br /><br /><br />

<?php
			Foot();
			die;
		}
	}
?>

���� �� ������ ���� ������ � ����, ������� � ����� ����� ��� ����� � email, ��������� ��� ����������� � 
�������� ���������� ���������� � ������.<br>

<p>����, ���������� ������� <span class="Mandatory"></span>, ����������� ��� ����������!

<div id="Summary" class="ErrorHolder">
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
		<th class="Mandatory">E-mail �����:</th>
		<td>
			<input name="E-MAIL" id="E-MAIL" class="RegData" value="<?php echo $email ?>">
			<p class="Tip">������� ���������� �����, �.�. �� ���� ����� ������� ���������� �� ��������� ��������.
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="image" alt="�������� ������" src="/img/t/send.gif" width="80" height="67" style="margin-top:10px" />
		</td>
	</tr>
</table>

</form>

<script>
	PageValidators.Add(new Validator($("LOGIN"), new RequiredField(), "���������� ������ �����.", 1));
	PageValidators.Add(new Validator($("LOGIN"), new LengthRange(3, 20), "����� ������ ���� �� ����� 6 � �� ����� 20 ��������.", 1));

	PageValidators.Add(new Validator($("E-MAIL"), new RequiredField(), "�� ������ e-mail.", 1));
	PageValidators.Add(new Validator($("E-MAIL"), new MatchPattern(emailPattern), "�������� ������ e-mail ������.", 1));

	PageValidators.Init($("Summary"), "�������� ������:");
<?php
		
	if (sizeof($errors)) {
 		echo "PageValidators.ShowSummary([\"".join("\", \"", $errors)."\"]);";
	}
?>
</script>
<?php

	Foot();

?>