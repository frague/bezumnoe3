<?
    require "menu_base.php";

	if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();
	}

?>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="NewsRecordsGrid" class="Grid"><tbody><tr><th style="width:100%">���������</th><th>��������</th></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="AddNewsRecord(this)" class="Add" id="AddNewsRecord">����� ���������</a>
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshNewsRecords">�������� ������</a>
</ul>
