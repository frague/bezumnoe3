<?
    require "menu_base.php";

?>

<h2>Wakeup-сообщения</h2>

<h4>Поиск:</h4>
<input name="SEARCH" id="SEARCH" class="Wide" /><br />
<h4>Дата:</h4>
<input name="DATE" id="DATE" /><br />

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="SwitchPage(this)" class="Refresh" id="buttonSearch">Применить фильтр</a>
	<li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">Сбросить фильтр</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="WakeupsGrid" class="Grid"><tbody><tr><th style="width:80%">Сообщения</th></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" id="linkRefresh" class="Refresh">Обновить данные с сервера</a>
</ul>