<?
    require "menu_base.php";

?>
<h2 id="TITLE1"></h2>

<table class="Wide">
	<tr>
		<td colspan="2">
<h4>��������:</h4>
<input id="TITLE" name="TITLE" class="Wide" /><br />
		
<div id="gallery">
	<h4>������� ��������:</h4>
	<textarea id="DESCRIPTION" name="DESCRIPTION" rows="5" class="Wide"></textarea>
</div>

<input type="checkbox" id="IS_PROTECTED" name="IS_PROTECTED" /> <label for="IS_PROTECTED">������ ������� �� ���������</label><br />
<p class="Note">��� ��������� ������ ������������ �� "������ ������" ������ ������ ������ � �������</p>

		</td></tr>
	<tr id="forum">
		<td width="50%">

<h4>�����:</h4>
�������: <span id="ALIAS" class="Bold">�� �����</span>
<p class="Note">�������������� �������� ������� (��������� �����, �����, ���� "_"),
������������ ��� ���������� ������. ��������, http://www.bezumnoe.ru/journal/your_alias</p>

		</td><td>

<h4>����� ������:</h4>
<p class="Note">���� �� ������ �������� ����� �������, ������� �������� � ��� ���� � ��������� ���������.
����� �������� ��������������� ����� ������� ������� � ����� ������.</p>
<input id="REQUESTED_ALIAS" name="REQUESTED_ALIAS" class="Wide" maxlength="20" />

		</td></tr>
</table>

<br />
<br />
