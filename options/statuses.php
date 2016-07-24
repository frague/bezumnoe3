<?php
    require "menu_base.php";

    if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
        exit();
    }

?>

<table cellpadding="2" cellspacing="0" id="StatusesGrid" class="Grid"><tbody><tr><th>Права</th><th style="width:100%" colspan="2">Цвет/название</th><th>Операции</th></tr></table>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="AddStatus(this)" class="Add" id="AddStatus">Новый статус</a>
    <li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshStatuses">Обновить список статусов</a>
</ul>