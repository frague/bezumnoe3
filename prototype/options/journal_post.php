<?
    require "menu_base.php";

?>
<h2>���������: <span id="TITLE1"></span></h2>

<h4>���� � �����:</h4>
<input id="DATE" name="DATE" maxlength="10">

<h4>���������:</h4>
<input id="TITLE" name="TITLE" class="Wide" maxlength="1024">

<h4>����� ���������:</h4>
<div id="ContentHolder"></div>

<table class="Wide">
	<tr>
		<td width="50%">

<h4>��� ���������:</h4>

<div id="TYPE" class="Radios">
	<input type="radio" name="TYPE1" id="TYPE0" value="0" checked /> <label for="TYPE0">��������� (������� ��� ����)</label><br />
	<input type="radio" name="TYPE1" id="TYPE1" value="1" /> <label for="TYPE1">��� ������ (�������)</label><br />
	<input type="radio" name="TYPE1" id="TYPE2" value="2" /> <label for="TYPE2" id="TYPE2LABEL">��������� (������� ������ ���)</label><br />
</div>

		</td><td>

<h4>����������� � ���������:</h4>
	<input type="checkbox" id="IS_COMMENTABLE" name="IS_COMMENTABLE" checked /> <label for="IS_COMMENTABLE">��������� �������������� ���������</label>

		</td></tr></table>

<br />
<br />
