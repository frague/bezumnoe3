<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>
<p class="Note">��� ���������������� ��������� � ������� ������������: ���/������ ����, ����� ������� � ������ ������������ �����������.</p>

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
			<h4>���������:</h4>
			<input type="checkbox" id="SEVERITY_NORMAL" name="SEVERITY_NORMAL" checked> <label for="SEVERITY_NORMAL">����������</label>&nbsp;
			<input type="checkbox" id="SEVERITY_WARNING" name="SEVERITY_WARNING" checked> <label for="SEVERITY_WARNING">��������������</label>&nbsp;
			<input type="checkbox" id="SEVERITY_ERROR" name="SEVERITY_ERROR" checked> <label for="SEVERITY_ERROR">������</label>
			</td></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshAdminComments">�������� ������/��������� ������</a>
	<li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">�������� ������</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="AdminCommentsGrid" class="Grid"><tbody><tr><th style="width:120px">����/�����</th><th>�����������</th></tr></table>

<h4>�������� �����������:</h4>
<table class="Wide">
	<tr>
		<td width="100%">
			<input name="ADMIN_COMMENT" id="ADMIN_COMMENT" class="Wide" />
		</td><td>
			<input type="image" src="/3/img/icons/add_to.gif" id="AddComment" onclick="AddComment(this)" />
			</td></tr></table>
