<?
    require "menu_base.php";

	if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();
	}

?>

<h4>������ ������ ����:</h4>
<input type="radio" name="TYPE" id="TYPE_YTKA" checked> <label for="TYPE_YTKA"><b>YTKA</b> &mdash; ������� ������������</label><br />
<input type="radio" name="TYPE" id="TYPE_VICTORINA"> <label for="TYPE_VICTORINA"><b>BUKTOPUHA</b> &mdash; �������/������</label><br />
<input type="radio" name="TYPE" id="TYPE_LINGVIST"> <label for="TYPE_LINGVIST"><b>Lingvist</b> &mdash; �������� �� ����������� ������</label><br />

<h4>������������</h4>
<input type="text" name="USER" id="USER" class="Wide" /><br />
<p class="Note">������� �������� ����� ��� �������</p>

<h4>�������</h4>
<select name="ROOM" id="ROOM" class="Wide"><option></select>

