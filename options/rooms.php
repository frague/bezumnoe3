<?
    require "menu_base.php";

	if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();
	}

?>

<h4>���������� ������ �������:</h4>
<input type="checkbox" id="locked" name="locked"> <label for="locked">���������������</label>&nbsp;
<input type="checkbox" id="by_invitation" name="by_invitation"> <label for="by_invitation">� ��������</label>&nbsp;
<input type="checkbox" id="deleted" name="deleted" checked> <label for="deleted">�� ��������</label>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshRooms">�������� ������ ������/��������� ������</a>
</ul>

<table cellpadding="2" cellspacing="0" id="RoomsGrid" class="Grid"><tbody><tr><th style="width:100%">�������</th><th>��������</th></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="AddNewRoom(this)" class="Add" id="AddRoom">����� �������</a>
</ul>

