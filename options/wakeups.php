<?php
    require "menu_base.php";

?>

<h2>Wakeup-сообщения</h2>

<table class="Wide">
    <tr>
        <td class="Nowrap">
            <h4>Дата:</h4>
            <input name="DATE" id="DATE" /></td>
        <td class="Wide">
            <h4>Поиск по ключевым словам:</h4>
            <input name="SEARCH" id="SEARCH" class="Wide" /></td></tr>
    <tr>
        <td colspan="2">
            <h4>Тип сообщений:</h4>
            <input type="checkbox" id="IS_INCOMING" name="IS_INCOMING" checked> <label for="IS_INCOMING">входящие</label>&nbsp;
            <input type="checkbox" id="IS_OUTGOING" name="IS_OUTGOING" checked> <label for="IS_OUTGOING">исходящие</label>&nbsp;
            </td></tr></table>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="SwitchPage(this)" class="Refresh" id="buttonSearch">Применить фильтр</a>
    <li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">Сбросить фильтр</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="WakeupsGrid" class="Grid"><tbody><tr><th width="60">Время</th><th width="100">Пользователь</th><th>Сообщения</th></tr></table>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="ReRequestData(this)" id="linkRefresh" class="Refresh">Обновить данные с сервера</a>
</ul>