<?
    require "menu_base.php";

?>

<h2>Wakeup-���������</h2>

<h4>�����:</h4>
<input name="SEARCH" id="SEARCH" class="Wide" /><br />
<h4>����:</h4>
<input name="DATE" id="DATE" /><br />

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="SwitchPage(this)" class="Refresh" id="buttonSearch">��������� ������</a>
	<li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">�������� ������</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="WakeupsGrid" class="Grid"><tbody><tr><th style="width:80%">���������</th></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" id="linkRefresh" class="Refresh">�������� ������ � �������</a>
</ul>