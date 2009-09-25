<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>
<h2>Менеджер пользователей:</h2>

<table width="100%" cellpadding="4">
	<tr>
		<td width="200px">

<h4>Поиск по логину или никнейму:</h4>
<input name="BY_NAME" id="BY_NAME" onkeypress="GetUsers(this)" class="Wide" />

<h4>Поиск по комнатам:</h4>
<select name="BY_ROOM" id="BY_ROOM" onchange="GetUsers(this)" class="Wide"><option></select>

<h4>Показывать только:</h4>
<input type="checkbox" name="FILTER_BANNED" id="FILTER_BANNED" onchange="this.Request()" /> <label for="FILTER_BANNED">забаненных</label><br />
<input type="checkbox" name="FILTER_EXPIRED" id="FILTER_EXPIRED" onchange="this.Request()" /> <label for="FILTER_EXPIRED">отсутствующих более года</label><br />
<input type="checkbox" name="FILTER_TODAY" id="FILTER_TODAY" onchange="this.Request()" /> <label for="FILTER_TODAY">заходивших сегодня</label><br />
<input type="checkbox" name="FILTER_YESTERDAY" id="FILTER_YESTERDAY" onchange="this.Request()" /> <label for="FILTER_YESTERDAY">заходивших вчера</label><br />

<div id="ExtendedCriteria">
</div>
		
		</td><td>
<h4>Результаты поиска:</h4>

<table cellpadding="2" cellspacing="0" id="UsersContainer" class="Grid"><tbody><tr><th style="width:80%">Пользователь</th><!--th>Операции</th--></tr></table>

		</td>
	</tr>
</table>
