<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>
<table cellpadding="2" cellspacing="0" id="BannedAddresses" class="Grid"><tbody><tr><th style="width:200px">Адрес</th><th>Параметры</th><th width="1%">Действия</th></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshBannedAddresses">Обновить список</a>
</ul>

<h2 id="FORM_TITLE">Добавить запрет:</h2>
<input name="BAN_ID" id="BAN_ID" type="hidden" />
<table class="Wide">
	<tr>
		<td width="100px">
			<h4>Закрыть:</h4>
			<input type="checkbox" id="BAN_CHAT" name="BAN_CHAT" checked /> <label for="BAN_CHAT">Доступ в чат</label><br>
			<input type="checkbox" id="BAN_FORUM" name="BAN_FORUM" checked /> <label for="BAN_FORUM">Форум</label><br>
			<input type="checkbox" id="BAN_JOURNAL" name="BAN_JOURNAL" checked /> <label for="BAN_JOURNAL">Журналы</label><br>
		</td><td width="100px" class="Radios" id="TYPE">
			<h4>Тип адреса:</h4>
			<input type="radio" id="TYPE01" name="TYPE1" value="ip" checked /> <label for="TYPE01">IP-адрес</label><br>
			<input type="radio" id="TYPE02" name="TYPE1" value="host" /> <label for="TYPE02">Хост</label>
		</td><td>
			<h4>Адрес:</h4>
			<input name="CONTENT" id="CONTENT" class="Wide" />
			<p class="Note">При задании адреса можно использовать символ * для закрытия диапазона адресов.<br>
			Пример формата ввода IP-адреса: <b>127.*.0.*</b></p>
		</td></tr>
	<tr>
		<td colspan="2">
			<h4>На срок до:</h4>
			<input name="TILL" id="TILL" maxlength="10" />
		</td><td>
			<h4>Комментарий:</h4>
			<input name="COMMENT" id="COMMENT" class="Wide" />
			<p class="Note">Не забывайте указывать, какова причина закрытия того или иного адреса.</p>
		</td></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ResetBanForm(this)" class="Delete" id="ResetBannedAddresses">Очистить форму/отменить редактирование</a>
</ul>
