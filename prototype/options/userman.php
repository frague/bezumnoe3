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
<input name="BY_NAME" id="BY_NAME" onkeypress="GetUsers(this)" class="Wide" />

<h4>����� �� ��������:</h4>
<select name="BY_ROOM" id="BY_ROOM" onchange="GetUsers(this)" class="Wide"><option></select>

		
		</td><td>
<h4>���������� ������:</h4>

<table cellpadding="2" cellspacing="0" id="UsersContainer" class="Grid"><tbody><tr><th style="width:80%">������������</th><!--th>��������</th--></tr></table>

		</td>
	</tr>
</table>
