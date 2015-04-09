<?
    require "menu_base.php";

    if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
        exit();
    }

?>
<table class="Wide">
    <tr>
        <td class="Nowrap">
            <h4>Дата:</h4>
            <input name="SEARCH_DATE" id="SEARCH_DATE" /></td>
        <td class="Wide">
            <h4>Поиск по ключевым словам:</h4>
            <input name="SEARCH" id="SEARCH" class="Wide" /></td></tr></table>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="buttonSearch">Применить фильтр</a>
    <li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">Сбросить фильтр</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="NewsRecordsGrid" class="Grid"><tbody><tr><th style="width:100%">Сообщения</th><th>Операции</th></tr></table>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="AddNewsRecord(this)" class="Add" id="AddNewsRecord">Новое сообщение</a>
    <li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshNewsRecords">Обновить список</a>
</ul>
