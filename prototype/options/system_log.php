<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>
<h4>����������� �� ����:</h4>
<input name="DATE" id="DATE" />

<h4>�����:</h4>
<input name="SEARCH" id="SEARCH" class="Wide" />

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshAdminComments">�������� ���/��������� ������</a>
	<li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">�������� ������</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="AdminCommentsGrid" class="Grid"><tbody><tr><th style="width:120px">����/�����</th><th>�����������</th></tr></table>

