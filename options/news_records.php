<?
    require "menu_base.php";

	if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();
	}

?>
<table class="Wide">
	<tr>
		<td class="Nowrap">
			<h4>����:</h4>
			<input name="SEARCH_DATE" id="SEARCH_DATE" /></td>
		<td class="Wide">
			<h4>����� �� �������� ������:</h4>
			<input name="SEARCH" id="SEARCH" class="Wide" /></td></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="buttonSearch">��������� ������</a>
	<li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">�������� ������</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="NewsRecordsGrid" class="Grid"><tbody><tr><th style="width:100%">���������</th><th>��������</th></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="AddNewsRecord(this)" class="Add" id="AddNewsRecord">����� ���������</a>
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshNewsRecords">�������� ������</a>
</ul>
