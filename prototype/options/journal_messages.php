<?
    require "menu_base.php";

?>

<h2>Сообщения в журнале <span id="LOGIN">%username%</span></h2>

<span id="TARGET">
	<h4>Доступные разделы:</h4>
	<select id="FORUM_ID" name="FORUM_ID" class="Wide" onchange="this.obj.Request()"></select>
	<input type="checkbox" id="SHOW_JOURNALS" name="SHOW_JOURNALS" checked onchange="this.obj.BindForums(1)"> <label for="SHOW_JOURNALS">Журналы</label> 
	<input type="checkbox" id="SHOW_FORUMS" name="SHOW_FORUMS" onchange="this.obj.BindForums(1)"> <label for="SHOW_FORUMS">Форумы</label> 
	<input type="checkbox" id="SHOW_GALLERIES" name="SHOW_GALLERIES" onchange="this.obj.BindForums(1)"> <label for="SHOW_GALLERIES">Галереи</label> 
</span>

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

