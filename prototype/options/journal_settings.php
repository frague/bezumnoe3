<?
    require "menu_base.php";

?>



<table class="Wide">
	<tr>
		<td width="50%">

<h4>�����:</h4>
�������: <span id="ALIAS" class="Bold">�� �����</span>
<p class="Note">�������������� �������� ������ ������� (��������� �����, �����, ���� "_"),
������������ ��� ���������� ������. ��������, http://www.bezumnoe.ru/journal/your_alias</p>

		</td><td>

<h4>����� ������:</h4>
<p class="Note">���� �� ������ �������� ����� ������ �������, ������� �������� � ��� ���� � ��������� ���������.
����� �������� ��������������� ����� ������� ������� � ����� ������.</p>
<input id="REQUESTED_ALIAS" name="REQUESTED_ALIAS" class="Wide" maxlength="20" />

		</td></tr>

	<tr>
		<td>

<h4>������������� �������:</h4>
<p class="Note">��������� �������� �� ����� ������ ����� ����������� � ����� "����������". ������������ ������������ ����� ������ ������������� ���� ��������� "������ ��� ������".</p>

<ul class="Links" id="friendlyBlogs"></ul>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="LoadFriendlyJournals(this)" id="linkRefresh" class="Refresh">�������� ������</a>
</ul>

		
		</td><td>

<h4>������ �� ��������������� ������ �������:</h4>
<p class="Note">��������� ������������ �� ������ ��������� ����������� � ���������� ������ �������.</p>

<ul class="Links"  id="forbiddenCommenters"></ul>
		
<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="LoadForbiddenCommenters(this)" id="linkRefresh" class="Refresh">�������� ������</a>
</ul>
		</td></tr></table>

<br />
<br />
