<?
    require "menu_base.php";

	if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();
	}

?>

<h4>������ ������ ����:</h4>
<div id="TYPE" class="Radios">
	<input type="radio" name="BOT_TYPE" id="TYPE_YTKA" value="ytka" checked> <label for="TYPE_YTKA"><b>YTKA</b> &mdash; ������� ������������</label><br />
	<input type="radio" name="BOT_TYPE" id="TYPE_VICTORINA" value="victorina"> <label for="TYPE_VICTORINA"><b>BUKTOPUHA</b> &mdash; �������/������</label><br />
	<input type="radio" name="BOT_TYPE" id="TYPE_LINGVIST" value="lingvist"> <label for="TYPE_LINGVIST"><b>Lingvist</b> &mdash; �������� �� ����������� ������</label><br /></div>


<table class="Wide" cellpadding="4">
	<tr>
		<td width="50%">
			<h4>������������ <span id="SELECTED_HOLDER" name="SELECTED_HOLDER"></span></h4>
			<input type="text" name="FIND_USER" id="FIND_USER" class="Wide" />
			<input type="hidden" name="BOT_USER_ID" id="BOT_USER_ID" />
			<div class="Options">
				<ul id="FoundUsers"></ul>
			</div>
			<p class="Note">������� �������� ����� ��� �������</p></td>
		<td width="50%">
			<h4>�������</h4>
			<select name="ROOM" id="ROOM" class="Wide"><option /></select></td></tr></table>
