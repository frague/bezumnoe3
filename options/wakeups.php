<?
    require "menu_base.php";

?>

<h2>Wakeup-���������</h2>

<table class="Wide">
	<tr>
		<td class="Nowrap">
			<h4>����:</h4>
			<input name="DATE" id="DATE" /></td>
		<td class="Wide">
			<h4>����� �� �������� ������:</h4>
			<input name="SEARCH" id="SEARCH" class="Wide" /></td></tr>
	<tr>
		<td colspan="2">
			<h4>��� ���������:</h4>
			<input type="checkbox" id="IS_INCOMING" name="IS_INCOMING" checked> <label for="IS_INCOMING">��������</label>&nbsp;
			<input type="checkbox" id="IS_OUTGOING" name="IS_OUTGOING" checked> <label for="IS_OUTGOING">���������</label>&nbsp;
			</td></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="SwitchPage(this)" class="Refresh" id="buttonSearch">��������� ������</a>
	<li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">�������� ������</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="WakeupsGrid" class="Grid"><tbody><tr><th width="60">�����</th><th width="100">������������</th><th>���������</th></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" id="linkRefresh" class="Refresh">�������� ������ � �������</a>
</ul>