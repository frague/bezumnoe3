<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>

<table cellpadding="2" cellspacing="0" id="OpenIdsGrid" class="Grid"><tbody><tr><th>���������</th><th style="width:100%">�����</th><th>��������</th></tr></tbody></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="AddOpenId(this)" class="Add" id="AddOpenId">����� OpenID</a>
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshOpenIds">�������� ������</a>
</ul>