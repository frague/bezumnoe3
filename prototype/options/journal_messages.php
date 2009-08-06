<?
    require "menu_base.php";

?>

<h2 id="TITLE"></h2>

<h4>Поиск:</h4>
<table class="Wide">
	<tr>
		<td width="100%">
			<input name="SEARCH" id="SEARCH" class="Wide" />
		</td><td>
			<input type="image" src="/3/img/search_button.gif" onclick="SwitchPage(this)" id="buttonSearch" />
		</td></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">Сбросить фильтр</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="MessagesGrid" class="Grid"><tbody><tr><th style="width:80%">Сообщения</th><th>Комментарии</th><th>Операции</th></tr></table>

