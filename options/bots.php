<?
    require "menu_base.php";

	if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();
	}

?>

<h4>������ ������ ����:</h4>
<input type="radio" name="TYPE" id="TYPE_YTKA" value="1" checked /> <label for="TYPE_YTKA"><b>YTKA</b> &mdash; ������� ������������</label><br />
<input type="radio" name="TYPE" id="TYPE_VICTORINA" value="2" /> <label for="TYPE_VICTORINA"><b>BUKTOPUHA</b> &mdash; �������/������</label><br />
<input type="radio" name="TYPE" id="TYPE_LINGVIST" value="3" /> <label for="TYPE_LINGVIST"><b>Lingvist</b> &mdash; �������� �� ����������� ������</label><br />

<table width="100%" cellpadding="4">
	<tr>
		<td width="50%">
			<h4>������������</h4>
			<input type="text" name="USER" id="USER" class="Wide" />
			<input type="hidden" name="BOT_USER_ID" id="BOT_USER_ID" />
			<div class="Options">
				<ul id="FoundUsers"></ul>
			</div>
			<p class="Note">������� �������� ����� ��� �������</p></td>
		<td width="50%">
			<h4>�������</h4>
			<select name="ROOM" id="ROOM" class="Wide"><option /></select></td></tr></table>
