<?
    require "menu_base.php";

    if ($user->IsEmpty() && !$user->IsSuperAdmin()) {
        exit();
    }

?>
<table class="Wide">
    <tr>
        <td colspan="2">
            <h4>Комната:</h4>
            <select name="ROOM_ID" id="ROOM_ID" class="Wide" onchange="ReRequestData(this)" /></td></tr>
    <tr>
        <td class="Nowrap">
            <h4>Дата:</h4>
            <input name="DATE" id="DATE" /></td>
        <td class="Wide">
            <h4>Поиск по ключевым словам:</h4>
            <input name="SEARCH" id="SEARCH" class="Wide" /></td></tr></table>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshMessagesLog">Обновить лог/применить фильтр</a>
    <li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">Сбросить фильтр</a>
</ul>

<table cellpadding="2" cellspacing="0" id="MessagesLogGrid" class="Grid"><tbody><tr><th width="40px">Время</th><th width="100px">Автор</th><th>Сообщение</th></tr></table>
<div id="Pager"></div>

