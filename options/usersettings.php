<?
    require "menu_base.php";

?>

<h2>��������� ������������ <span id="LOGIN">%username%</span></h2>

<table width="100%" cellpadding="4">
	<tr>
		<td width="50%" colspan="2">
<h4>�����:</h4>
�������� ������:<br>
<input id="FONT_FACE" name="FONT_FACE" class="Wide" onchange="UpdateFontView()" /><br />

������ ������: <select id="FONT_SIZE" name="FONT_SIZE" onchange="UpdateFontView()"><option value="1">����� �����<option value="2">�����<option value="3">����������<option value="4">�������<option value="5">����� �������</select><br />

����:<br>
<input id="FONT_COLOR" name="FONT_COLOR" onchange="UpdateFontView()" /><br />

�����:<br>
<input type="checkbox" id="FONT_BOLD" name="FONT_BOLD" onclick="UpdateFontView()" /> <label for="FONT_BOLD"><b>������</b></label><br />
<input type="checkbox" id="FONT_ITALIC" name="FONT_ITALIC" onclick="UpdateFontView()" /> <label for="FONT_ITALIC"><i>������</i></label><br />
<input type="checkbox" id="FONT_UNDERLINED" name="FONT_UNDERLINED" onclick="UpdateFontView()" /> <label for="FONT_UNDERLINED" class="NoBorder"><u>������������</u></label><br />

<h4>��� ����� �������� ���:</h4>
<div id="fontExample">
	Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh nunc, ultricies eu, ultricies id, tristique vel, purus.
	<p>����� ��� ���� ������ ����������� �����, �� ����� �� ���.
</div>

		</td><td colspan="2">
<h4>���������� ��������� ����:</h4>
<input type="checkbox" id="IGNORE_FONTS" name="IGNORE_FONTS" /> <label for="IGNORE_FONTS">������� �� ���������</label><br />
<input type="checkbox" id="IGNORE_COLORS" name="IGNORE_COLORS" /> <label for="IGNORE_COLORS">����� ������</label><br />
<input type="checkbox" id="IGNORE_FONT_SIZE" name="IGNORE_FONT_SIZE" /> <label for="IGNORE_FONT_SIZE">������� ������ �������</label><br />
<input type="checkbox" id="IGNORE_FONT_STYLE" name="IGNORE_FONT_STYLE" /> <label for="IGNORE_FONT_STYLE">������������ �����</label><br />

<h4>������ ���������:</h4>
<input type="checkbox" id="RECEIVE_WAKEUPS" name="RECEIVE_WAKEUPS" /> <label for="RECEIVE_WAKEUPS">���������� ������ ��������� ��� ���������</label>

<h4>��������� ����� � ������:</h4>
��������� � ����� � ���:<br>
<input id="ENTER_MESSAGE" name="ENTER_MESSAGE" class="Wide" /><br />
<p class="Note">����������� �������� ����� <b>%name</b> � ��� �����, ���� ����� ���������� ��� �������</p>

��������� � ������ �� ����:<br />
<input id="QUIT_MESSAGE" name="QUIT_MESSAGE" class="Wide" /><br />
<p class="Note">����������� �������� ����� <b>%name</b> � ��� �����, ���� ����� ���������� ��� �������</p>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<h4>������������ "�������" ����:</h4></td></tr>
	<tr class="Radios" id="FRAMESET"><td width="25%"><img src="/img/frames/0.gif" /><input type="radio" id="SET0" name="FRAMESET1" value="0" /><label for="SET0">������ ������������� �����, ���� ����� ������</label></td><td width="25%"><img src="/img/frames/1.gif" /><input type="radio" id="SET1" name="FRAMESET1" value="1" /><label for="SET1">������ ������������� ������, ���� ����� ������</label></td><td width="25%"><img src="/img/frames/2.gif" /><input type="radio" id="SET2" name="FRAMESET1" value="2"><label for="SET2" />������ ������������� �����, ���� ����� �����</label></td><td width="25%"><img src="/img/frames/3.gif" /><input type="radio" id="SET3" name="FRAMESET1" value="3" /><label for="SET3">������ ������������� ������, ���� ����� �����</label></td></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" id="linkRefresh" class="Refresh">�������� ������ � �������</a>
</ul>