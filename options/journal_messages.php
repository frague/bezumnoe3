<?php
    require "menu_base.php";

?>

<table cellpadding="4" cellspacing="0" class="Wide">
    <tr>
        <td class="Nowrap">
            <h4>Дата:</h4>
            <input name="DATE" id="DATE" /></td>
        <td class="Wide">
            <h4>Поиск по ключевым словам:</h4>
            <input name="SEARCH" id="SEARCH" class="Wide" /></td></tr></table>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="SwitchPage(this)" class="Refresh" id="buttonSearch">Применить фильтр</a>
    <li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">Сбросить фильтр</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="MessagesGrid" class="Grid"><tbody><tr><th style="width:80%">Сообщения</th><th>Комментарии</th><th>Операции</th></tr></table>

