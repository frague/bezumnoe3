<?
    require "menu_base.php";

	if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();
	}

?>

<table cellpadding="2" cellspacing="0" id="RoomsGrid" class="Grid"><tbody><tr><th style="width:100%">�������</th><th>��������</th></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="AddRoom(this)" class="Add" id="AddRoom">����� �������</a>
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshRooms">�������� ������ ������</a>
</ul>