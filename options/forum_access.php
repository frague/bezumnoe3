<?
    require "menu_base.php";

?>
<h2 id="TITLE"></h2>

<table class="Wide">
	<tr>
		<td colspan="2">
<h4>����� �������������:</h4>
<p class="Note">
	� ����������� ������ ������������ ������������ � �� �������.
	������� ����� �������� � ���� ���������� (������������� �������). 
	��� ������������� �� ������ ������ ����������� ������� ������� � ������ ������� (�������� ����������� ��������� ��� ��������).
</p>
<div>
	<input id="ADD_USER" name="ADD_USER" class="Wide" />
	<div id="FOUND_USERS" name="FOUND_USERS"></div>
</div>
		</td></tr>
	<tr id="FRIENDS">
		<td colspan="2">
<h4>������������� �������:</h4>
<p class="Note">������ �� �������� ����� ������ �������� �� �����-����� ���������� �������.</p>
<table cellpadding="2" cellspacing="0" id="FRIENDS_LIST" class="Grid Wide"><tbody><tr><th>��������/�������� �������������� �������</th><th width="1%">�������</th></tr></tbody></table>
		</td></tr>
	<tr>
		<td width="50%">
<h4>&laquo;����� ������&raquo;:</h4>
<p class="Note">������������ �� ����� ������ ����� ������������ ������ � ���������� �������: ����������� ���������� ��������� � �������� ������������.</p>
<table cellpadding="2" cellspacing="0" id="WHITE_LIST" class="Grid Wide"><tbody><tr><th>������������</th><th width="1%">�������</th></tr></tbody></table>
		</td><td>
<h4>&laquo;׸���� ������&raquo;:</h4>
<p class="Note">������������ �� ����� ������ �� ����� ����������� ���������� ���������/������������ � ��������� �������.</p>
<table cellpadding="2" cellspacing="0" id="BLACK_LIST" class="Grid Wide"><tbody><tr><th>������������</th><th width="1%">�������</th></tr></tbody></table>
		</td></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshForumAccess">�������� ������</a>
</ul>
