<?
    require "menu_base.php";

	if ($user->IsEmpty() && !$user->IsSuperAdmin()) {
		exit();
	}

?>
<table class="Wide">
	<tr>
		<td width="120px">
			<h4>Дата:</h4>
			<input name="DATE" id="DATE" /></td>
		<td>
			<h4>Комната:</h4>
			<select name="ROOM_ID" id="ROOM_ID" class="Wide" onchange="ReRequestData(this)" /></td>
			</td></tr>
	<tr>
		<td colspan="2">
			<h4>Поиск по ключевым словам:</h4>
			<input name="SEARCH" id="SEARCH" class="Wide" /></td></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshMessagesLog">Обновить лог/применить фильтр</a>
	<li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">Сбросить фильтр</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="MessagesLogGrid" class="Grid"><tbody><tr><th style="width:100px">Дата</th><th style="width:100px">Автор</th><th>Сообщение</th></tr></table>

