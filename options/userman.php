<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>
<h2>�������� �������������:</h2>

<table width="100%" cellpadding="4">
	<tr>
		<td width="200px">

<h4>����� �� ������ ��� ��������:</h4>
<input name="BY_NAME" id="BY_NAME" class="Wide" />

<h4>����� �� ��������:</h4>
<select name="BY_ROOM" id="BY_ROOM" class="Wide"><option></select>

<h4>���������� ������:</h4>
<input type="checkbox" name="FILTER_BANNED" id="FILTER_BANNED" /> <label for="FILTER_BANNED">����������</label><br />
<input type="checkbox" name="FILTER_EXPIRED" id="FILTER_EXPIRED" /> <label for="FILTER_EXPIRED">������������� ����� ����</label><br />
<input type="checkbox" name="FILTER_TODAY" id="FILTER_TODAY" /> <label for="FILTER_TODAY">���������� �������</label><br />
<input type="checkbox" name="FILTER_YESTERDAY" id="FILTER_YESTERDAY" /> <label for="FILTER_YESTERDAY">���������� �����</label><br />
<input type="checkbox" name="FILTER_REGDATE" id="FILTER_REGDATE" /> <label for="FILTER_REGDATE">�� ���� �����������</label> 
<input type="text" name="REG_DATE" id="REG_DATE" />

<div id="ExtendedCriteria">
</div>
		
		</td><td>
<h4>���������� ������:</h4>

<table cellpadding="2" cellspacing="0" id="UsersContainer" class="Grid"><tbody><tr><th class="Wide">������������</th><th>&nbsp;</th></tr></table>

		</td>
	</tr>
</table>
